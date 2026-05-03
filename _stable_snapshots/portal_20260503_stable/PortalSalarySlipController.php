<?php

namespace App\Http\Controllers\Portal;

use App\Models\SalarySlip;
use App\Services\AdminErpNotificationService;
use Illuminate\Http\Request;

class PortalSalarySlipController extends PortalBaseController
{
    public function index(Request $request)
    {
        $shared = $this->sharedPortalData($request);
        $portalAccount = $shared['portalAccount'];
        $identity = $portalAccount?->currentIdentity;

        $status = trim((string) $request->query('status', ''));
        $month = trim((string) $request->query('month', ''));
        $year = trim((string) $request->query('year', ''));

        $query = SalarySlip::query()
            ->with(['employment', 'client', 'project', 'employmentRotation'])
            ->latest('salary_year')
            ->latest('salary_month')
            ->latest('id');

        if ($identity?->employment_id) {
            $query->where('employment_id', $identity->employment_id);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($month !== '' && is_numeric($month)) {
            $query->where('salary_month', (int) $month);
        }

        if ($year !== '' && is_numeric($year)) {
            $query->where('salary_year', (int) $year);
        }

        $salarySlips = $query->paginate(20)->withQueryString();

        $baseQuery = SalarySlip::query();

        if ($identity?->employment_id) {
            $baseQuery->where('employment_id', $identity->employment_id);
        } else {
            $baseQuery->whereRaw('1 = 0');
        }

        $yearOptions = $baseQuery->clone()
            ->select('salary_year')
            ->whereNotNull('salary_year')
            ->distinct()
            ->orderByDesc('salary_year')
            ->pluck('salary_year')
            ->filter()
            ->values();

        return view('portal.salary-slips.index', array_merge($shared, [
            'salarySlips' => $salarySlips,
            'statusFilter' => $status,
            'monthFilter' => $month,
            'yearFilter' => $year,
            'statusOptions' => SalarySlip::statusLabels(),
            'yearOptions' => $yearOptions,
            'monthOptions' => [
                1 => '01 - January',
                2 => '02 - February',
                3 => '03 - March',
                4 => '04 - April',
                5 => '05 - May',
                6 => '06 - June',
                7 => '07 - July',
                8 => '08 - August',
                9 => '09 - September',
                10 => '10 - October',
                11 => '11 - November',
                12 => '12 - December',
            ],
        ]));
    }

    public function show(Request $request, SalarySlip $salarySlip)
    {
        $shared = $this->sharedPortalData($request);

        $this->authorizePortalSlip($shared, $salarySlip);

        $salarySlip->load([
            'employment',
            'client',
            'project',
            'employmentRotation',
            'treasuryAccount',
            'attachments',
            'days',
        ]);

        return view('portal.salary-slips.show', array_merge($shared, [
            'salarySlip' => $salarySlip,
        ]));
    }

    public function confirmReceived(Request $request, SalarySlip $salarySlip)
    {
        $shared = $this->sharedPortalData($request);

        abort_unless($this->portalCanAccessSalarySlip($shared, $salarySlip), 403);

        $salarySlip->refresh();

        $payload = [
            'employee_confirmation_status' => 'received',
            'employee_confirmation_notes' => $request->input('employee_confirmation_notes'),
            'employee_confirmed_at' => now(),
            'employee_confirmation_ip' => $request->ip(),
            'employee_confirmation_user_agent' => substr((string) $request->userAgent(), 0, 500),
        ];

        $payload['status'] = SalarySlip::STATUS_PAID;

        if (! $salarySlip->paid_at) {
            $payload['paid_at'] = now();
        }

        $salarySlip->forceFill($payload)->save();

        $this->notifyAdminAboutEmployeeSalaryResponse(
            $salarySlip->fresh(['employment', 'client', 'project']),
            'received',
            $request->input('employee_confirmation_notes')
        );

        $this->closeSalarySlipPaymentNotification($salarySlip, $shared);

        return redirect()
            ->route('portal.salary-slips.show', $salarySlip)
            ->with('success', 'Salary payment receipt confirmed successfully.');
    }

    public function notReceived(Request $request, SalarySlip $salarySlip)
    {
        $shared = $this->sharedPortalData($request);

        abort_unless($this->portalCanAccessSalarySlip($shared, $salarySlip), 403);

        $salarySlip->refresh();

        if ($salarySlip->payment_method === SalarySlip::PAYMENT_METHOD_CASH) {
            return redirect()
                ->route('portal.salary-slips.show', $salarySlip)
                ->with('error', 'Not received action is not available for cash payments. Please contact finance if there is an issue.');
        }

        $existingNotes = trim((string) $salarySlip->notes);
        $employeeNote = trim((string) $request->input('employee_confirmation_notes'));

        $reportNote = 'Employee reported from portal that bank salary payment was not received.';
        if ($employeeNote !== '') {
            $reportNote .= "
Employee note: " . $employeeNote;
        }

        $salarySlip->forceFill([
            'status' => SalarySlip::STATUS_BANK_REJECTED,
            'employee_confirmation_status' => 'not_received',
            'employee_confirmation_notes' => $employeeNote !== '' ? $employeeNote : null,
            'employee_confirmed_at' => now(),
            'employee_confirmation_ip' => $request->ip(),
            'employee_confirmation_user_agent' => substr((string) $request->userAgent(), 0, 500),
            'paid_at' => null,
            'notes' => $existingNotes === '' ? $reportNote : $existingNotes . "

" . $reportNote,
        ])->save();

        $this->notifyAdminAboutEmployeeSalaryResponse(
            $salarySlip->fresh(['employment', 'client', 'project']),
            'not_received',
            $employeeNote !== '' ? $employeeNote : null
        );

        $this->closeSalarySlipPaymentNotification($salarySlip, $shared);

        return redirect()
            ->route('portal.salary-slips.show', $salarySlip)
            ->with('success', 'Your not received report has been submitted. The salary slip is now marked as Bank Rejected.');
    }




    protected function notifyAdminAboutEmployeeSalaryResponse(SalarySlip $salarySlip, string $response, ?string $employeeNote = null): void
    {
        try {
            app(AdminErpNotificationService::class)->notifySalarySlipEmployeeResponse(
                salarySlip: $salarySlip,
                response: $response,
                employeeNote: $employeeNote,
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function authorizePortalSlip(array $shared, SalarySlip $salarySlip): void
    {
        $portalAccount = $shared['portalAccount'] ?? null;
        $identity = $portalAccount?->currentIdentity;

        $salarySlip->loadMissing('employment');

        $identityEmploymentId = $identity?->employment_id;
        $slipEmploymentId = $salarySlip->employment_id;

        $portalEmail = strtolower(trim((string) ($portalAccount?->email ?? '')));
        $employeeEmail = strtolower(trim((string) ($salarySlip->employment?->employee_email ?? '')));

        $allowedByEmploymentId = filled($identityEmploymentId)
            && filled($slipEmploymentId)
            && (int) $identityEmploymentId === (int) $slipEmploymentId;

        $allowedByEmail = $portalEmail !== ''
            && $employeeEmail !== ''
            && $portalEmail === $employeeEmail;

        /*
         * Admin Preview Support:
         * When testing from the ERP/admin browser, Laravel's normal auth session may exist
         * while the portal identity does not match perfectly. Allow admin-authenticated preview.
         */
        $allowedByAdminPreview = auth()->check();

        abort_unless($allowedByEmploymentId || $allowedByEmail || $allowedByAdminPreview, 403);
    }




    protected function portalCanAccessSalarySlip(array $shared, SalarySlip $salarySlip): bool
    {
        $portalAccount = $shared['portalAccount'] ?? null;
        $currentIdentity = $shared['currentIdentity'] ?? null;
        $portalEmployment = $shared['portalEmployment'] ?? null;

        $salarySlip->loadMissing('employment');

        if ($currentIdentity?->employment_id && (int) $salarySlip->employment_id === (int) $currentIdentity->employment_id) {
            return true;
        }

        if ($portalEmployment?->id && (int) $salarySlip->employment_id === (int) $portalEmployment->id) {
            return true;
        }

        if ($portalAccount?->email && $salarySlip->employment?->employee_email) {
            return strtolower((string) $portalAccount->email) === strtolower((string) $salarySlip->employment->employee_email);
        }

        return false;
    }

    protected function closeSalarySlipPaymentNotification(SalarySlip $salarySlip, array $shared = []): void
    {
        try {
            $portalAccount = $shared['portalAccount'] ?? null;

            if (! $portalAccount) {
                $salarySlip->loadMissing('employment.portalUser');
                $portalAccount = $salarySlip->employment?->portalUser;
            }

            if (! $portalAccount) {
                return;
            }

            \App\Models\PortalNotification::query()
                ->where('portal_account_id', $portalAccount->id)
                ->where('related_type', 'salary_slip')
                ->where('related_id', $salarySlip->id)
                ->where('action_type', 'salary_payment_confirmation')
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        } catch (\Throwable $e) {
            //
        }
    }

}
