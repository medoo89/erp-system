<?php

namespace App\Http\Controllers\Portal;

use App\Services\AdminErpNotificationService;
use App\Models\Employment;
use App\Models\FinanceExpense;
use App\Models\FinanceExpenseTravelDetail;
use App\Models\PortalAccount;
use App\Models\PortalIdentity;
use App\Models\PortalTimelineEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortalReimbursementController extends PortalBaseController
{
    public function index(Request $request): View
    {
        $context = $this->resolvePortalContext($request);
        $employment = $context['employment'];
        $portalAccount = $context['portalAccount'];
        $currentIdentity = $context['currentIdentity'];

        abort_if(! $employment, 403, 'Employment profile is not linked to this portal account.');

        $claims = FinanceExpense::query()
            ->where(function ($query) use ($employment) {
                $query->where('employment_id', $employment->id);

                if ($employment->pre_employment_id) {
                    $query->orWhere('pre_employment_id', $employment->pre_employment_id);
                }
            })
            ->where('paid_by', FinanceExpense::PAID_BY_CANDIDATE)
            ->latest('expense_date')
            ->latest('id')
            ->get();

        return view('portal.reimbursements.index', array_merge($this->sharedPortalData($request), [
            'pageTitle' => 'Reimbursements',
            'employment' => $employment,
            'portalAccount' => $portalAccount,
            'currentIdentity' => $currentIdentity,
            'claims' => $claims,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $context = $this->resolvePortalContext($request);
        $employment = $context['employment'];

        abort_if(! $employment, 403, 'Employment profile is not linked to this portal account.');

        $validated = $request->validate([
            'expense_title' => ['required', 'string', 'max:255'],
            'expense_category' => ['required', 'string', 'max:80'],
            'expense_amount' => ['required', 'numeric', 'min:0.01'],
            'expense_currency' => ['required', 'string', 'max:10'],
            'expense_date' => ['required', 'date'],

            // Legacy names kept for compatibility.
            'departure_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date', 'after_or_equal:departure_date'],
            'check_in_date' => ['nullable', 'date'],
            'check_out_date' => ['nullable', 'date', 'after_or_equal:check_in_date'],
            'visa_issue_date' => ['nullable', 'date'],
            'visa_expiry_date' => ['nullable', 'date', 'after_or_equal:visa_issue_date'],
            'medical_date' => ['nullable', 'date'],

            // Final operational calendar dates.
            'operational_departure_date' => ['nullable', 'date'],
            'operational_return_date' => ['nullable', 'date', 'after_or_equal:operational_departure_date'],
            'operational_check_in_date' => ['nullable', 'date'],
            'operational_check_out_date' => ['nullable', 'date', 'after_or_equal:operational_check_in_date'],
            'operational_visa_issue_date' => ['nullable', 'date'],
            'operational_visa_expiry_date' => ['nullable', 'date', 'after_or_equal:operational_visa_issue_date'],
            'operational_medical_date' => ['nullable', 'date'],

            'expense_notes' => ['nullable', 'string', 'max:3000'],
            'receipt_file' => ['nullable', 'file', 'max:20480'],
        ]);

        $receiptPath = null;

        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');

            $safeName = Str::slug($employment->employee_name ?: 'employee');
            $safeCategory = Str::slug($validated['expense_category'] ?: 'expense');
            $extension = $file->getClientOriginalExtension() ?: 'file';

            $fileName = $safeName . '-' . $safeCategory . '-receipt-' . now()->format('YmdHis') . '.' . $extension;

            $receiptPath = $file->storeAs(
                'employee-reimbursements/' . $employment->id,
                $fileName,
                'public'
            );
        }

        $job = $employment->job;
        $project = $job?->project;
        $client = $project?->client;

        $category = $validated['expense_category'] ?: FinanceExpense::CATEGORY_OTHER;

        // SADA_FINAL_OPERATIONAL_DATE_SERVER_VALIDATION
        $categoryKey = strtolower((string) $category);

        $operationalDepartureDate = $validated['operational_departure_date']
            ?? $validated['departure_date']
            ?? null;

        $operationalReturnDate = $validated['operational_return_date']
            ?? $validated['return_date']
            ?? null;

        $operationalCheckInDate = $validated['operational_check_in_date']
            ?? $validated['check_in_date']
            ?? null;

        $operationalCheckOutDate = $validated['operational_check_out_date']
            ?? $validated['check_out_date']
            ?? null;

        $operationalVisaIssueDate = $validated['operational_visa_issue_date']
            ?? $validated['visa_issue_date']
            ?? null;

        $operationalVisaExpiryDate = $validated['operational_visa_expiry_date']
            ?? $validated['visa_expiry_date']
            ?? null;

        $operationalMedicalDate = $validated['operational_medical_date']
            ?? $validated['medical_date']
            ?? null;

        if (
            ($categoryKey === FinanceExpense::CATEGORY_TICKET || str_contains($categoryKey, 'ticket'))
            && blank($operationalDepartureDate)
        ) {
            return back()
                ->withErrors(['operational_departure_date' => 'Ticket Departure Date is required for ticket claims.'])
                ->withInput();
        }

        if (
            (
                $categoryKey === FinanceExpense::CATEGORY_HOTEL
                || $categoryKey === FinanceExpense::CATEGORY_ACCOMMODATION
                || str_contains($categoryKey, 'hotel')
                || str_contains($categoryKey, 'accommodation')
            )
            && (blank($operationalCheckInDate) || blank($operationalCheckOutDate))
        ) {
            return back()
                ->withErrors(['operational_check_in_date' => 'Hotel Check-in Date and Check-out Date are required for hotel claims.'])
                ->withInput();
        }

        if (
            ($categoryKey === FinanceExpense::CATEGORY_VISA || str_contains($categoryKey, 'visa'))
            && (blank($operationalVisaIssueDate) || blank($operationalVisaExpiryDate))
        ) {
            return back()
                ->withErrors(['operational_visa_issue_date' => 'Visa Issue / Submission Date and Visa Expiry Date are required for visa claims.'])
                ->withInput();
        }

        $currency = strtoupper((string) ($validated['expense_currency'] ?: ($employment->salary_currency ?: 'EUR')));
        $amount = (float) ($validated['expense_amount'] ?? 0);

        $incurredFrom = null;
        $incurredTo = null;

        if ($categoryKey === FinanceExpense::CATEGORY_TICKET || str_contains($categoryKey, 'ticket')) {
            $incurredFrom = $operationalDepartureDate;
            $incurredTo = $operationalReturnDate;
        } elseif (
            $categoryKey === FinanceExpense::CATEGORY_HOTEL
            || $categoryKey === FinanceExpense::CATEGORY_ACCOMMODATION
            || str_contains($categoryKey, 'hotel')
            || str_contains($categoryKey, 'accommodation')
        ) {
            $incurredFrom = $operationalCheckInDate;
            $incurredTo = $operationalCheckOutDate;
        } elseif ($categoryKey === FinanceExpense::CATEGORY_VISA || str_contains($categoryKey, 'visa')) {
            $incurredFrom = $operationalVisaIssueDate;
            $incurredTo = $operationalVisaExpiryDate;
        } elseif ($categoryKey === FinanceExpense::CATEGORY_MEDICAL || str_contains($categoryKey, 'medical')) {
            $incurredFrom = $operationalMedicalDate;
            $incurredTo = null;
        }

        $payload = [
            'employment_id' => $employment->id,
            'pre_employment_id' => $employment->pre_employment_id,
            'job_application_id' => $employment->preEmployment?->job_application_id,
            'job_id' => $employment->job_id,
            'client_id' => $client?->id,
            'project_id' => $project?->id,
            'candidate_finance_profile_id' => $employment->currentFinanceProfile?->id,
            'created_by' => null,
            'expense_scope' => FinanceExpense::SCOPE_EMPLOYMENT,
            'category' => $category,
            'expense_category' => $category,
            'title' => $validated['expense_title'],
            'description' => $validated['expense_notes'] ?? null,
            'amount' => $amount,
            'currency' => $currency,
            'expense_date' => $validated['expense_date'],
            'incurred_from' => $incurredFrom,
            'incurred_to' => $incurredTo,
            'paid_by' => FinanceExpense::PAID_BY_CANDIDATE,
            'reimbursement_status' => FinanceExpense::REIMBURSEMENT_PENDING,
            'reimbursement_required' => true,
            'reimbursement_amount' => $amount,
            'reimbursement_currency' => $currency,
            'reimbursement_notes' => $validated['expense_notes'] ?? null,
            'status' => FinanceExpense::STATUS_DRAFT,
            'is_company_expense' => false,
            'is_manual_expense' => false,
            'candidate_submitted' => true,
            'candidate_submitted_at' => now(),
            'attachment_path' => $receiptPath,
            'receipt_file_path' => $receiptPath,
            'notes' => trim('Submitted by employee from employee portal.' . "\n" . (string) ($validated['expense_notes'] ?? '')),
        ];

        $payload = $this->filterPayloadForTable('finance_expenses', $payload);

        $expense = FinanceExpense::query()->create($payload);

        $this->createTimelineEvent($employment, $expense);
        $this->notifyFinanceAboutSubmittedClaim($expense, 'employee_portal');

        return redirect()
            ->route('portal.reimbursements.index')
            ->with('success', 'Your reimbursement claim has been submitted successfully and is pending review.');
    }


    protected function notifyFinanceAboutSubmittedClaim(FinanceExpense $expense, string $source): void
    {
        try {
            app(AdminErpNotificationService::class)->notifyReimbursementClaimSubmitted($expense, $source);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function resolvePortalContext(Request $request): array
    {
        $portalAccount = $request->attributes->get('portalAccount');

        if (! $portalAccount && session()->has('portal_account_id')) {
            $portalAccount = PortalAccount::query()->find(session('portal_account_id'));
        }

        $currentIdentity = null;

        if ($portalAccount) {
            $currentIdentity = PortalIdentity::query()
                ->where('portal_account_id', $portalAccount->id)
                ->where('is_current', true)
                ->latest('id')
                ->first();

            if (! $currentIdentity) {
                $currentIdentity = PortalIdentity::query()
                    ->where('portal_account_id', $portalAccount->id)
                    ->latest('id')
                    ->first();
            }
        }

        $employment = null;

        if ($currentIdentity?->employment_id) {
            $employment = Employment::query()
                ->with(['job.project.client', 'preEmployment', 'currentFinanceProfile'])
                ->find($currentIdentity->employment_id);
        }

        if (! $employment && $portalAccount?->email) {
            $employment = Employment::query()
                ->with(['job.project.client', 'preEmployment', 'currentFinanceProfile'])
                ->where('employee_email', $portalAccount->email)
                ->latest('id')
                ->first();
        }

        return [
            'portalAccount' => $portalAccount,
            'currentIdentity' => $currentIdentity,
            'employment' => $employment,
        ];
    }

    protected function createTimelineEvent(Employment $employment, FinanceExpense $expense): void
    {
        if (! class_exists(PortalTimelineEvent::class) || ! Schema::hasTable('portal_timeline_events')) {
            return;
        }

        $amount = number_format((float) ($expense->reimbursement_amount ?: $expense->amount), 2) . ' ' . ($expense->reimbursement_currency ?: $expense->currency);
        $payload = [
            'employment_id' => $employment->id,
            'title' => 'Reimbursement Claim Submitted',
            'description' => ($expense->title ?: 'Employee reimbursement claim') . ' — ' . $amount,
            'status' => 'pending',
            'category' => 'reimbursement',
            'event_type' => 'reimbursement',
            'related_type' => FinanceExpense::class,
            'related_id' => $expense->id,
            'event_date' => now(),
            'occurred_at' => now(),
            'is_visible_to_employee' => true,
            'visible_to_employee' => true,
        ];

        $payload = $this->filterPayloadForTable('portal_timeline_events', $payload);

        if (! empty($payload)) {
            try {
                PortalTimelineEvent::query()->create($payload);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    protected function filterPayloadForTable(string $table, array $payload): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $columns = Schema::getColumnListing($table);

        return collect($payload)
            ->filter(fn ($value, $key) => in_array($key, $columns, true))
            ->all();
    }
}
