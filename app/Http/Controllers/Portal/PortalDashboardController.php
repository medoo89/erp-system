<?php

namespace App\Http\Controllers\Portal;

use App\Models\PreEmploymentPortalValue;
use App\Models\PreEmploymentPortalField;
use App\Models\CandidateFinanceProfile;
use App\Models\FinanceExpense;
use App\Models\SalarySlip;
use App\Models\SalaryTermsHistory;
use App\Support\RecruitmentCalendarEvents;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class PortalDashboardController extends PortalBaseController
{
    public function __invoke(Request $request)
    {
        $shared = $this->sharedPortalData($request);

        $portalAccount = $shared['portalAccount'];
        $currentIdentity = $shared['currentIdentity'];
        $employment = $shared['portalEmployment'];
        $preEmployment = $employment?->preEmployment;

        $selectedYear = (int) ($request->query('year') ?: now()->year);
        $selectedMonth = (int) ($request->query('month') ?: now()->month);

        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = (int) now()->month;
        }

        if ($selectedYear < 2000 || $selectedYear > 2100) {
            $selectedYear = (int) now()->year;
        }

        $calendarDate = Carbon::create($selectedYear, $selectedMonth, 1);

        $latestNotifications = $portalAccount?->notifications()
            ->latest()
            ->limit(5)
            ->get() ?? collect();

        $recentSalarySlips = collect();
        if ($currentIdentity?->employment_id) {
            $recentSalarySlips = SalarySlip::query()
                ->where('employment_id', $currentIdentity->employment_id)
                ->with(['client', 'project'])
                ->latest('salary_year')
                ->latest('salary_month')
                ->latest('id')
                ->limit(3)
                ->get();
        }

        $pendingPaymentConfirmations = collect();

        if ($currentIdentity?->employment_id) {
            $pendingPaymentConfirmations = SalarySlip::query()
                ->where('employment_id', $currentIdentity->employment_id)
                ->whereIn('status', [
                    SalarySlip::STATUS_SENT_TO_BANK,
                    SalarySlip::STATUS_PAID,
                ])
                ->where(function ($query) {
                    $query->whereNull('employee_confirmation_status')
                        ->orWhere('employee_confirmation_status', '')
                        ->orWhere('employee_confirmation_status', 'pending')
                        ->orWhere('employee_confirmation_status', 'not_received');
                })
                ->where(function ($query) {
                    $query->where('status', SalarySlip::STATUS_SENT_TO_BANK)
                    ->orWhere(function ($cash) {
                        $cash->where('status', SalarySlip::STATUS_PAID)
                            ->where('payment_method', SalarySlip::PAYMENT_METHOD_CASH);
                    });
                })
                ->with(['client', 'project'])
                ->latest('updated_at')
                ->limit(5)
                ->get();
        }

        $recentFiles = $this->buildPortalFiles($employment, $preEmployment)->take(3);
        $latestUpdates = $this->buildLatestUpdates($employment, $preEmployment, $recentSalarySlips, $recentFiles)->take(12);

        $dashboardReimbursementClaims = $this->buildDashboardReimbursementClaims($employment, $preEmployment)->take(5);


        $pendingPaymentConfirmations = collect();

        if ($currentIdentity?->employment_id) {
            $pendingPaymentConfirmations = SalarySlip::query()
                ->with(['client', 'project', 'employment'])
                ->where('employment_id', $currentIdentity->employment_id)
                ->whereIn('status', [
                    SalarySlip::STATUS_SENT_TO_BANK,
                    SalarySlip::STATUS_PAID,
                ])
                ->where(function ($query) {
                    $query->whereNull('employee_confirmation_status')
                        ->orWhere('employee_confirmation_status', '')
                        ->orWhere('employee_confirmation_status', 'pending');
                })
                ->where(function ($query) {
                    $query->where('status', SalarySlip::STATUS_SENT_TO_BANK)
                        ->orWhere(function ($cashQuery) {
                            $cashQuery->where('status', SalarySlip::STATUS_PAID)
                                ->where('payment_method', SalarySlip::PAYMENT_METHOD_CASH);
                        });
                })
                ->latest('updated_at')
                ->limit(5)
                ->get();
        }

        $nextEvents = $this->buildPortalCalendarEvents($employment)
            ->filter(fn ($event) => ($event['date'] ?? null) && $event['date']->gte(now()->startOfDay()))
            ->sortBy('date')
            ->values();

        $eventDates = $nextEvents
            ->groupBy(fn ($item) => $item['date']->format('Y-m-d'))
            ->map(fn ($items) => [
                'type' => $items->first()['type'] ?? 'event',
                'color' => $items->first()['color'] ?? '#2563eb',
                'count' => $items->count(),
                'items' => $items->map(fn ($item) => [
                    'title' => $item['title'] ?? 'Event',
                    'type' => $item['type'] ?? 'event',
                    'notes' => $item['notes'] ?? null,
                    'color' => $item['color'] ?? '#2563eb',
                    'date' => isset($item['date']) ? $item['date']->format('Y-m-d') : null,
                ])->values()->toArray(),
            ])
            ->toArray();

        $calendar = $this->buildCalendar($calendarDate, $eventDates);

        $prev = $calendarDate->copy()->subMonth();
        $next = $calendarDate->copy()->addMonth();

        if ($employment) {
            $employment->loadMissing('rotations');
        }

        $travelRotations = collect($employment?->rotations ?? []);

        $travelTicketsSummary = [
            'rotations_count' => $travelRotations->count(),
            'tickets_count' => $travelRotations->filter(fn ($rotation) => filled($rotation->ticket_file_path ?? null))->count(),
            'travel_requests_count' => $travelRotations->filter(fn ($rotation) => filled($rotation->travel_request_file_path ?? null))->count(),
            'latest_rotation' => $travelRotations
                ->sortByDesc(fn ($rotation) => optional($rotation->updated_at)->timestamp ?? optional($rotation->created_at)->timestamp ?? 0)
                ->first(),
        ];

        $compensationSnapshot = $this->resolveCompensationSnapshot($employment, $preEmployment);

        
        $pendingFileRequests = collect();

        if ($employment?->preEmployment) {
            $submittedFieldIds = PreEmploymentPortalValue::query()
                ->where('pre_employment_id', $employment->preEmployment->id)
                ->whereNotNull('value')
                ->where('value', '!=', '')
                ->pluck('portal_field_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $pendingFileRequests = PreEmploymentPortalField::query()
                ->where('pre_employment_id', $employment->preEmployment->id)
                ->where('field_type', 'file')
                ->where('is_active', true)
                ->where('visible_to_candidate', true)
                ->when(! empty($submittedFieldIds), fn ($query) => $query->whereNotIn('id', $submittedFieldIds))
                ->orderByDesc('id')
                ->get();
        }

return view('portal.dashboard', array_merge($shared, [
            'currentIdentity' => $currentIdentity,
            'latestNotifications' => $latestNotifications->take(3),
            'latestTimeline' => $latestUpdates,
            'recentSalarySlips' => $recentSalarySlips,
            'pendingPaymentConfirmations' => $pendingPaymentConfirmations,
            'unreadNotificationsCount' => $portalAccount?->unreadNotifications()->count() ?? 0,
            'pendingFileRequests' => $pendingFileRequests,
            'recentFiles' => $recentFiles,
            'dashboardReimbursementClaims' => $dashboardReimbursementClaims ?? collect(),
            'calendarMonthLabel' => $calendar['monthLabel'],
            'calendarWeeks' => $calendar['weeks'],
            'calendarToday' => now()->format('Y-m-d'),
            'calendarPrevUrl' => route('portal.dashboard', [
                'month' => $prev->month,
                'year' => $prev->year,
            ]),
            'calendarNextUrl' => route('portal.dashboard', [
                'month' => $next->month,
                'year' => $next->year,
            ]),
            'compensationSnapshot' => $compensationSnapshot,
            'travelTicketsSummary' => $travelTicketsSummary,
            'rotationSnapshot' => [
                'rotation_status' => $employment?->rotation_status,
                'travel_status' => $employment?->travel_status,
                'mobilization_date' => $employment?->mobilization_date,
                'demobilization_date' => $employment?->demobilization_date,
                'work_location' => $employment?->work_location,
            ],
            'nextEvents' => $nextEvents->take(10),
        ]));
    }

    protected function portalVisibleFileCategories(): array
    {
        return [
            'cv',
            'candidate_upload',
            'passport',
            'visa',
            'medical',
            'personal_photo',
            'certificate',
            'caf',
            'gl',
            'contract',
            'rotation_document',
            'internal_document',
        ];
    }

    protected function portalHiddenFileKeywords(): array
    {
        return [
            'expense',
            'expenses',
            'invoice',
            'receipt',
            'payment',
            'payment_proof',
            'bank',
            'treasury',
            'salary',
            'payslip',
            'payroll',
            'voucher',
            'cost',
            'finance',
            'financial',
        ];
    }

    protected function isPortalVisibleFile(?string $category, ?string $title = null, ?bool $isCurrent = true): bool
    {
        if ($isCurrent === false) {
            return false;
        }

        $category = strtolower(trim((string) $category));
        $title = strtolower(trim((string) $title));
        $combined = $category . ' ' . $title;

        foreach ($this->portalHiddenFileKeywords() as $keyword) {
            if (str_contains($combined, $keyword)) {
                return false;
            }
        }

        return in_array($category, $this->portalVisibleFileCategories(), true)
            || str_contains($combined, 'cv')
            || str_contains($combined, 'resume')
            || str_contains($combined, 'passport')
            || str_contains($combined, 'visa')
            || str_contains($combined, 'medical')
            || str_contains($combined, 'certificate')
            || str_contains($combined, 'contract')
            || str_contains($combined, 'rotation');
    }

    protected function buildPortalFiles($employment, $preEmployment): Collection
    {
        if ($employment) {
            $employment->loadMissing([
                'files',
                'documents',
                'preEmployment.files',
                'preEmployment.uploads',
                'preEmployment.jobApplication',
            ]);
        }

        $items = collect();

        foreach (($employment?->files ?? collect()) as $file) {
            if (! $this->isPortalVisibleFile($file->category ?? null, $file->title ?? null, (bool) ($file->is_current ?? true))) {
                continue;
            }

            $items->push([
                'type' => 'file',
                'title' => $file->title ?? $file->file_name ?? ('Employment File #' . $file->id),
                'description' => $file->category ?? 'Employment File',
                'date' => $file->created_at,
                'badge_status' => 'file',
                'file_path' => $file->file_path ?? $file->path ?? null,
            ]);
        }

        foreach (($employment?->documents ?? collect()) as $doc) {
            if (! $this->isPortalVisibleFile($doc->document_type ?? null, $doc->title ?? null, true)) {
                continue;
            }

            $items->push([
                'type' => 'file',
                'title' => $doc->document_name ?? $doc->title ?? ('Employment Document #' . $doc->id),
                'description' => $doc->document_type ?? 'Employment Document',
                'date' => $doc->created_at,
                'badge_status' => 'document',
                'file_path' => $doc->file_path ?? $doc->path ?? null,
            ]);
        }

        foreach (($preEmployment?->files ?? collect()) as $file) {
            if (! $this->isPortalVisibleFile($file->category ?? null, $file->title ?? null, (bool) ($file->is_current ?? true))) {
                continue;
            }

            $items->push([
                'type' => 'file',
                'title' => $file->title ?? $file->file_name ?? ('Pre-Employment File #' . $file->id),
                'description' => $file->category ?? 'Pre-Employment File',
                'date' => $file->created_at,
                'badge_status' => 'file',
                'file_path' => $file->file_path ?? $file->path ?? null,
            ]);
        }

        foreach (($preEmployment?->uploads ?? collect()) as $upload) {
            $items->push([
                'type' => 'file',
                'title' => $upload->label ?? $upload->field_label ?? $upload->original_name ?? ('Upload #' . $upload->id),
                'description' => $upload->field_key ?? 'candidate_upload',
                'date' => $upload->created_at,
                'badge_status' => 'file',
                'file_path' => $upload->file_path ?? $upload->path ?? $upload->stored_name ?? null,
            ]);
        }

        $jobApplication = $preEmployment?->jobApplication;
        if ($jobApplication) {
            $cvPath = $jobApplication->cv_file ?? $jobApplication->cv_path ?? $jobApplication->file_path ?? null;

            if (filled($cvPath)) {
                $items->push([
                    'type' => 'file',
                    'title' => ($jobApplication->full_name ?: 'Candidate') . ' CV',
                    'description' => 'cv',
                    'date' => $jobApplication->created_at,
                    'badge_status' => 'file',
                    'file_path' => $cvPath,
                ]);
            }
        }

        return $items
            ->filter(fn ($item) => filled($item['file_path']))
            ->unique(fn ($item) => ($item['title'] ?? '') . '|' . ($item['file_path'] ?? ''))
            ->sortByDesc(fn ($item) => optional($item['date'])->timestamp ?? 0)
            ->values();
    }

    protected function buildLatestUpdates($employment, $preEmployment, Collection $recentSalarySlips, Collection $recentFiles): Collection
    {
        $items = collect();

        /*
         * Salary slips visible to employee.
         */
        foreach ($recentSalarySlips as $slip) {
            $period = trim(sprintf('%02d / %04d', (int) ($slip->salary_month ?? 0), (int) ($slip->salary_year ?? 0)));

            $items->push([
                'type' => 'salary_slip',
                'title' => 'Salary Slip',
                'description' => 'Salary slip for ' . $period . ' is ' . str_replace('_', ' ', (string) ($slip->status ?: 'draft')) . '.',
                'badge_status' => $slip->status ?: 'draft',
                'event_date' => $slip->updated_at ?: $slip->created_at,
                'color' => '#2563eb',
            ]);
        }

        /*
         * Employee files / documents visible in portal.
         */
        foreach ($recentFiles as $file) {
            $items->push([
                'type' => 'file',
                'title' => $file['title'] ?? 'Portal File',
                'description' => 'A portal-visible file was added or updated.',
                'badge_status' => $file['description'] ?? 'file',
                'event_date' => $file['date'] ?? now(),
                'color' => '#0ea5e9',
            ]);
        }

        /*
         * Employment lifecycle updates.
         */
        if ($employment) {
            $employment->loadMissing(['rotations', 'files']);

            foreach ([
                ['field' => 'mobilization_date', 'title' => 'Mobilization', 'type' => 'mobilization', 'color' => '#0ea5e9'],
                ['field' => 'demobilization_date', 'title' => 'Demobilization', 'type' => 'demobilization', 'color' => '#8b5cf6'],
                ['field' => 'contract_start_date', 'title' => 'Contract Start', 'type' => 'contract', 'color' => '#16a34a'],
                ['field' => 'contract_end_date', 'title' => 'Contract End', 'type' => 'contract', 'color' => '#d97706'],
                ['field' => 'visa_expiry_date', 'title' => 'Visa Expiry', 'type' => 'visa', 'color' => '#2563eb'],
                ['field' => 'medical_expiry_date', 'title' => 'Medical Expiry', 'type' => 'medical', 'color' => '#16a34a'],
            ] as $event) {
                $date = $employment->{$event['field']} ?? null;

                if ($date) {
                    $items->push([
                        'type' => $event['type'],
                        'title' => $event['title'],
                        'description' => $event['title'] . ' date recorded for this employment profile.',
                        'badge_status' => $event['type'],
                        'event_date' => $date,
                        'color' => $event['color'],
                    ]);
                }
            }

            foreach (($employment->rotations ?? collect()) as $rotation) {
                foreach ([
                    ['field' => 'from_date', 'title' => 'Rotation Start', 'type' => 'rotation_start', 'color' => '#16a34a'],
                    ['field' => 'to_date', 'title' => 'Rotation End', 'type' => 'rotation_end', 'color' => '#16a34a'],
                    ['field' => 'mobilization_date', 'title' => 'Rotation Mobilization', 'type' => 'mobilization', 'color' => '#0ea5e9'],
                    ['field' => 'demobilization_date', 'title' => 'Rotation Demobilization', 'type' => 'demobilization', 'color' => '#8b5cf6'],
                    ['field' => 'travel_request_date', 'title' => 'Travel Request', 'type' => 'travel_request', 'color' => '#d97706'],
                ] as $event) {
                    $date = $rotation->{$event['field']} ?? null;

                    if ($date) {
                        $items->push([
                            'type' => $event['type'],
                            'title' => $event['title'],
                            'description' => 'Rotation / travel update recorded.',
                            'badge_status' => $event['type'],
                            'event_date' => $rotation->updated_at ?: $rotation->created_at,
                            'sort_date' => $rotation->updated_at ?: $rotation->created_at,
                            'color' => $event['color'],
                        ]);
                    }
                }

                if (filled($rotation->ticket_file_path ?? null)) {
                    $items->push([
                        'type' => 'ticket',
                        'title' => 'Ticket Uploaded',
                        'description' => 'A travel ticket was uploaded for your rotation.',
                        'badge_status' => 'ticket',
                        'event_date' => $rotation->updated_at ?: $rotation->created_at,
                        'sort_date' => $rotation->updated_at ?: $rotation->created_at,
                        'color' => '#d97706',
                    ]);
                }

                if (filled($rotation->travel_request_file_path ?? null)) {
                    $items->push([
                        'type' => 'travel_request',
                        'title' => 'Travel Request Uploaded',
                        'description' => 'A travel request document was uploaded.',
                        'badge_status' => 'travel_request',
                        'event_date' => $rotation->updated_at ?: $rotation->created_at,
                        'sort_date' => $rotation->updated_at ?: $rotation->created_at,
                        'color' => '#d97706',
                    ]);
                }
            }
        }

        /*
         * Reimbursement claims only, not all expenses.
         */
        $expenseQuery = FinanceExpense::query()
            ->where(function ($query) use ($employment, $preEmployment) {
                if ($employment?->id) {
                    $query->orWhere('employment_id', $employment->id);
                }

                if ($preEmployment?->id) {
                    $query->orWhere('pre_employment_id', $preEmployment->id);
                }
            })
            ->where(function ($query) {
                $query->where('reimbursement_required', true)
                    ->orWhere('paid_by', FinanceExpense::PAID_BY_CANDIDATE);
            })
            ->where(function ($query) {
                $query->whereNull('reimbursement_status')
                    ->orWhere('reimbursement_status', '!=', FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE);
            })
            ->latest('updated_at')
            ->limit(20)
            ->get();

        foreach ($expenseQuery as $expense) {
            $items->push([
                'type' => 'reimbursement',
                'title' => 'Reimbursement Claim: ' . ($expense->title ?: 'Expense Claim'),
                'description' => trim(
                    number_format((float) ($expense->reimbursement_amount ?: $expense->amount ?: 0), 2)
                    . ' '
                    . ($expense->reimbursement_currency ?: $expense->currency ?: '')
                    . ' · '
                    . str_replace('_', ' ', (string) ($expense->reimbursement_status ?: 'pending'))
                ),
                'badge_status' => $expense->reimbursement_status ?: 'pending',
                'event_date' => $expense->updated_at ?: $expense->created_at ?: $expense->expense_date,
                'sort_date' => $expense->updated_at ?: $expense->created_at ?: $expense->expense_date,
                'color' => '#f59e0b',
            ]);
        }

        return $items
            ->filter(fn ($item) => ! empty($item['event_date']))
            ->sortByDesc(fn ($item) => optional($item['sort_date'] ?? $item['event_date'])->timestamp ?? 0)
            ->values();
    }

    protected function snapshotFromFinanceProfile(CandidateFinanceProfile $profile, string $sourceLabel): array
    {
        return [
            'salary_basis' => $profile->salary_basis ?: null,
            'daily_rate' => $profile->daily_rate,
            'monthly_salary' => $profile->monthly_salary,
            'salary_currency' => $profile->resolvedPayoutCurrency(),
            'source_label' => $sourceLabel,
        ];
    }

    protected function snapshotFromSalaryHistory(SalaryTermsHistory $history, string $sourceLabel): array
    {
        return [
            'salary_basis' => $history->salary_basis ?: null,
            'daily_rate' => $history->daily_rate,
            'monthly_salary' => $history->monthly_salary,
            'salary_currency' => $history->currency ?: null,
            'source_label' => $sourceLabel,
        ];
    }

    protected function snapshotHasValues(array $snapshot): bool
    {
        return filled($snapshot['salary_basis'] ?? null)
            || filled($snapshot['salary_currency'] ?? null)
            || ! is_null($snapshot['daily_rate'] ?? null)
            || ! is_null($snapshot['monthly_salary'] ?? null);
    }


    protected function buildPortalCalendarEvents($employment): Collection
    {
        if (! $employment) {
            return collect();
        }

        $employmentId = (int) $employment->id;
        $preEmploymentId = (int) ($employment->pre_employment_id ?? 0);

        $employeeName = strtolower(trim((string) ($employment->employee_name ?? '')));
        $employeeCode = strtolower(trim((string) ($employment->employment_code ?? $employment->employee_code ?? '')));
        $jobTitle = strtolower(trim((string) ($employment->job?->title ?? $employment->position_title ?? '')));

        $events = collect(RecruitmentCalendarEvents::make());

        return $events
            ->filter(function (array $event) use ($employmentId, $preEmploymentId, $employeeName, $employeeCode, $jobTitle) {
                $linkedType = (string) ($event['linked_type'] ?? '');
                $linkedId = (int) ($event['linked_id'] ?? 0);
                $source = (string) ($event['source'] ?? '');

                /*
                 * Finance expense / reimbursement operational calendar events.
                 * Show only if the linked finance expense belongs to this employment/pre-employment.
                 */
                if ($linkedType === \App\Models\FinanceExpense::class || $source === \App\Models\FinanceExpense::class) {
                    if ($linkedId <= 0 || ! \Illuminate\Support\Facades\Schema::hasTable('finance_expenses')) {
                        return false;
                    }

                    return \Illuminate\Support\Facades\DB::table('finance_expenses')
                        ->where('id', $linkedId)
                        ->where(function ($q) use ($employmentId, $preEmploymentId) {
                            $q->where('employment_id', $employmentId);

                            if ($preEmploymentId > 0) {
                                $q->orWhere('pre_employment_id', $preEmploymentId);
                            }
                        })
                        ->exists();
                }

                /*
                 * Employment rotation / travel / document events generated by the global calendar.
                 * The global events already contain employee name/code in title/notes in most cases.
                 * Portal must show only events that mention this employee/code/job.
                 */
                $blob = strtolower(trim(implode(' ', array_filter([
                    $event['title'] ?? '',
                    $event['notes'] ?? '',
                    $event['job_title'] ?? '',
                    $event['source'] ?? '',
                    $event['type'] ?? '',
                ]))));

                if ($employeeCode !== '' && str_contains($blob, $employeeCode)) {
                    return true;
                }

                if ($employeeName !== '' && str_contains($blob, $employeeName)) {
                    return true;
                }

                if ($jobTitle !== '' && str_contains($blob, $jobTitle) && str_contains($blob, 'employee')) {
                    return true;
                }

                return false;
            })
            ->map(function (array $event) {
                $type = (string) ($event['type'] ?? $event['event_type'] ?? 'event');

                $start = $event['start'] ?? $event['date'] ?? null;
                $date = null;

                if ($start instanceof \Carbon\CarbonInterface) {
                    $date = $start->copy()->startOfDay();
                } elseif (filled($start)) {
                    try {
                        $date = \Carbon\Carbon::parse($start)->startOfDay();
                    } catch (\Throwable $e) {
                        $date = null;
                    }
                }

                $color = $event['color']
                    ?? $event['backgroundColor']
                    ?? RecruitmentCalendarEvents::colorForType($type);

                $event['type'] = $type;
                $event['date'] = $date;
                $event['start'] = $date?->toDateString() ?? ($event['start'] ?? null);
                $event['color'] = $color;
                $event['backgroundColor'] = $event['backgroundColor'] ?? $color;
                $event['borderColor'] = $event['borderColor'] ?? $color;
                $event['icon'] = $event['icon'] ?? RecruitmentCalendarEvents::iconForType($type);

                return $event;
            })
            ->filter(fn (array $event) => ! empty($event['date']))
            ->sortBy('date')
            ->values();
    }

    protected function buildCalendar(Carbon $date, array $eventDates = []): array
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $gridStart = $startOfMonth->copy()->startOfWeek(Carbon::SATURDAY);
        $gridEnd = $endOfMonth->copy()->endOfWeek(Carbon::FRIDAY);

        $cursor = $gridStart->copy();
        $weeks = [];

        while ($cursor <= $gridEnd) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $key = $cursor->format('Y-m-d');
                $week[] = [
                    'date' => $key,
                    'day' => $cursor->day,
                    'isCurrentMonth' => $cursor->month === $date->month,
                    'isToday' => $cursor->isToday(),
                    'hasEvent' => array_key_exists($key, $eventDates),
                    'eventType' => $eventDates[$key] ?? null,
                ];

                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return [
            'monthLabel' => $date->format('F Y'),
            'weeks' => $weeks,
        ];
    }

    protected function buildDashboardReimbursementClaims($employment, $preEmployment): \Illuminate\Support\Collection
    {
        if (! class_exists(FinanceExpense::class)) {
            return collect();
        }

        try {
            return FinanceExpense::query()
                ->where(function ($query) use ($employment, $preEmployment) {
                    $hasFilter = false;

                    if ($employment?->id) {
                        $query->orWhere('employment_id', $employment->id);
                        $hasFilter = true;
                    }

                    if ($preEmployment?->id) {
                        $query->orWhere('pre_employment_id', $preEmployment->id);
                        $hasFilter = true;
                    }

                    if (! $hasFilter) {
                        $query->whereRaw('1 = 0');
                    }
                })
                ->where(function ($query) {
                    $query->where('reimbursement_required', true)
                        ->orWhere('paid_by', FinanceExpense::PAID_BY_CANDIDATE);
                })
                ->where(function ($query) {
                    $query->whereNull('reimbursement_status')
                        ->orWhere('reimbursement_status', '!=', FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE);
                })
                ->latest('updated_at')
                ->latest('id')
                ->get()
                ->map(function ($claim) {
                    $status = $claim->reimbursement_status ?: FinanceExpense::REIMBURSEMENT_PENDING;
                    $amount = $claim->reimbursement_amount ?: $claim->amount;
                    $currency = $claim->reimbursement_currency ?: $claim->currency;

                    return [
                        'id' => $claim->id,
                        'title' => $claim->title ?: 'Reimbursement Claim',
                        'category' => $claim->category ?: $claim->expense_category ?: 'claim',
                        'status' => $status,
                        'amount' => $amount,
                        'currency' => $currency ?: 'EUR',
                        'date' => $claim->expense_date ?: $claim->created_at,
                        'updated_at' => $claim->updated_at ?: $claim->created_at,
                        'description' => $claim->description ?: $claim->notes,
                    ];
                })
                ->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }



    protected function resolveCompensationSnapshot($employment, $preEmployment): array
    {
        $profile = null;
        $sourceLabel = null;

        try {
            if ($employment) {
                $employment->loadMissing('currentFinanceProfile');

                if ($employment->currentFinanceProfile) {
                    $profile = $employment->currentFinanceProfile;
                    $sourceLabel = 'Employment Finance Profile';
                }
            }

            if (! $profile && $preEmployment) {
                $preEmployment->loadMissing('currentFinanceProfile');

                if ($preEmployment->currentFinanceProfile) {
                    $profile = $preEmployment->currentFinanceProfile;
                    $sourceLabel = 'Pre-Employment Finance Profile';
                }
            }
        } catch (\Throwable $e) {
            $profile = null;
        }

        $basis = $profile?->salary_basis ?: CandidateFinanceProfile::BASIS_DAILY_RATE;

        return [
            'source_label' => $sourceLabel,
            'salary_basis' => $basis,
            'daily_rate' => $profile?->daily_rate,
            'monthly_salary' => $profile?->monthly_salary,
            'salary_currency' => $profile?->payout_currency ?: $profile?->agreed_salary_currency,
            'client_billing_basis' => $profile?->client_billing_basis,
            'client_billing_rate' => $profile?->client_billing_rate,
            'client_billing_currency' => $profile?->client_billing_currency,
            'finance_notes' => $profile?->finance_notes,
        ];
    }


}
