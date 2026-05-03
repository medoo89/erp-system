<?php

namespace App\Http\Controllers\Portal;

use App\Models\CandidateFinanceProfile;
use App\Models\SalarySlip;
use App\Models\SalaryTermsHistory;
use App\Support\RecruitmentCalendarEvents;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        $latestUpdates = $this->buildLatestUpdates($employment, $preEmployment, $recentSalarySlips, $recentFiles)->take(3);


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

        return view('portal.dashboard', array_merge($shared, [
            'currentIdentity' => $currentIdentity,
            'latestNotifications' => $latestNotifications->take(3),
            'latestTimeline' => $latestUpdates,
            'recentSalarySlips' => $recentSalarySlips,
            'pendingPaymentConfirmations' => $pendingPaymentConfirmations,
            'unreadNotificationsCount' => $portalAccount?->unreadNotifications()->count() ?? 0,
            'recentFiles' => $recentFiles,
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
        $updates = collect();

        foreach ($recentSalarySlips as $slip) {
            $updates->push([
                'title' => 'Salary Slip ' . sprintf('%02d/%04d', (int) ($slip->salary_month ?? 0), (int) ($slip->salary_year ?? 0)),
                'description' => 'Net Amount: ' . number_format((float) ($slip->net_amount ?? 0), 2) . ' ' . ($slip->currency ?: ''),
                'event_date' => $slip->updated_at ?: $slip->created_at,
                'badge_status' => (string) $slip->status,
            ]);
        }

        if ($employment?->currentRotation) {
            $rotation = $employment->currentRotation;
            $updates->push([
                'title' => 'Current Rotation Updated',
                'description' => trim(implode(' · ', array_filter([
                    $rotation->from_date ? 'From: ' . $rotation->from_date->format('Y-m-d') : null,
                    $rotation->to_date ? 'To: ' . $rotation->to_date->format('Y-m-d') : null,
                    $employment->rotation_status ? 'Status: ' . $employment->rotation_status : null,
                ]))),
                'event_date' => $rotation->updated_at ?: $rotation->created_at,
                'badge_status' => 'rotation',
            ]);
        }

        if ($employment?->travel_status || $employment?->mobilization_date || $employment?->demobilization_date) {
            $updates->push([
                'title' => 'Travel / Mobilization Update',
                'description' => trim(implode(' · ', array_filter([
                    $employment->travel_status ? 'Travel: ' . $employment->travel_status : null,
                    $employment->mobilization_date ? 'Mobilization: ' . $employment->mobilization_date->format('Y-m-d') : null,
                    $employment->demobilization_date ? 'Demobilization: ' . $employment->demobilization_date->format('Y-m-d') : null,
                ]))),
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'travel',
            ]);
        }

        if ($employment?->contract_status) {
            $updates->push([
                'title' => 'Contract Status Updated',
                'description' => 'Contract Status: ' . $employment->contract_status,
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'contract',
            ]);
        }

        if ($employment?->visa_status) {
            $updates->push([
                'title' => 'Visa Status Updated',
                'description' => trim(implode(' · ', array_filter([
                    'Visa Status: ' . $employment->visa_status,
                    $employment->visa_expiry_date ? 'Expiry: ' . $employment->visa_expiry_date->format('Y-m-d') : null,
                ]))),
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'visa',
            ]);
        }

        if ($employment?->medical_status) {
            $updates->push([
                'title' => 'Medical Status Updated',
                'description' => trim(implode(' · ', array_filter([
                    'Medical Status: ' . $employment->medical_status,
                    $employment->medical_expiry_date ? 'Expiry: ' . $employment->medical_expiry_date->format('Y-m-d') : null,
                ]))),
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'medical',
            ]);
        }

        foreach ($recentFiles as $file) {
            $updates->push([
                'title' => $file['title'],
                'description' => $file['description'] ?: 'File added',
                'event_date' => $file['date'],
                'badge_status' => $file['badge_status'] ?? 'file',
            ]);
        }

        return $updates
            ->sortByDesc(fn ($item) => optional($item['event_date'])->timestamp ?? 0)
            ->values();
    }

    protected function resolveCompensationSnapshot($employment, $preEmployment): array
    {
        $snapshot = [
            'salary_basis' => null,
            'daily_rate' => null,
            'monthly_salary' => null,
            'salary_currency' => null,
            'source_label' => null,
        ];

        if ($employment) {
            $employment->loadMissing(['currentFinanceProfile', 'preEmployment.currentFinanceProfile']);
        }

        $profile = $employment?->currentFinanceProfile;
        if ($profile instanceof CandidateFinanceProfile) {
            $snapshot = $this->snapshotFromFinanceProfile($profile, 'Employment Finance Profile');
            if ($this->snapshotHasValues($snapshot)) {
                return $snapshot;
            }
        }

        if ($employment?->id) {
            $history = SalaryTermsHistory::query()
                ->where('employment_id', $employment->id)
                ->latest('effective_from')
                ->latest('id')
                ->first();

            if ($history) {
                $snapshot = $this->snapshotFromSalaryHistory($history, 'Employment Salary History');
                if ($this->snapshotHasValues($snapshot)) {
                    return $snapshot;
                }
            }
        }

        $preProfile = $preEmployment?->currentFinanceProfile;
        if ($preProfile instanceof CandidateFinanceProfile) {
            $snapshot = $this->snapshotFromFinanceProfile($preProfile, 'Pre-Employment Finance Profile');
            if ($this->snapshotHasValues($snapshot)) {
                return $snapshot;
            }
        }

        if ($preEmployment?->id) {
            $history = SalaryTermsHistory::query()
                ->where('pre_employment_id', $preEmployment->id)
                ->latest('effective_from')
                ->latest('id')
                ->first();

            if ($history) {
                $snapshot = $this->snapshotFromSalaryHistory($history, 'Pre-Employment Salary History');
                if ($this->snapshotHasValues($snapshot)) {
                    return $snapshot;
                }
            }
        }

        if ($employment) {
            $snapshot = [
                'salary_basis' => $employment->salary_basis ?? null,
                'daily_rate' => $employment->daily_rate ?? null,
                'monthly_salary' => $employment->monthly_salary ?? null,
                'salary_currency' => $employment->salary_currency ?? null,
                'source_label' => 'Employment Record',
            ];

            if ($this->snapshotHasValues($snapshot)) {
                return $snapshot;
            }
        }

        return $snapshot;
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

        $employment->loadMissing(['rotations', 'files']);

        $events = collect();

        $push = function ($date, string $title, string $type, ?string $notes = null) use ($events) {
            if (! $date) {
                return;
            }

            try {
                $carbonDate = Carbon::parse($date)->startOfDay();
            } catch (\Throwable) {
                return;
            }

            $events->push([
                'title' => $title,
                'date' => $carbonDate,
                'type' => $type,
                'notes' => $notes,
                'color' => RecruitmentCalendarEvents::colorForType($type),
            ]);
        };

        $push($employment->mobilization_date, 'Mobilization Date', 'mobilization');
        $push($employment->demobilization_date, 'Demobilization Date', 'demobilization');
        $push($employment->visa_expiry_date, 'Visa Expiry', 'visa_expiry');
        $push($employment->medical_expiry_date, 'Medical Expiry', 'medical_expiry');
        $push($employment->contract_end_date, 'Contract End', 'contract_end');

        foreach (($employment->rotations ?? collect()) as $rotation) {
            $label = $rotation->rotation_label ?: ('Rotation #' . $rotation->id);

            $push($rotation->mobilization_date, 'Travel / Mobilization', 'ticket_travel', $label);
            $push($rotation->from_date, 'Rotation Start', 'rotation_start', $label);
            $push($rotation->to_date, 'Rotation End', 'rotation_end', $label);
            $push($rotation->demobilization_date, 'Demobilization', 'demobilization', $label);
        }

        foreach (($employment->files ?? collect()) as $file) {
            if (! (bool) ($file->is_current ?? true) || blank($file->expiry_date)) {
                continue;
            }

            $category = strtolower((string) ($file->category ?? 'file'));

            $type = match (true) {
                str_contains($category, 'visa') => 'visa_expiry',
                str_contains($category, 'medical') => 'medical_expiry',
                str_contains($category, 'contract') => 'contract_end',
                str_contains($category, 'passport') => 'passport_expiry',
                str_contains($category, 'certificate') => 'certificate_expiry',
                str_contains($category, 'desert') => 'desert_pass_expiry',
                str_contains($category, 'ticket') => 'ticket_expiry',
                default => 'file_expiry',
            };

            $push(
                $file->expiry_date,
                ($file->title ?: ucfirst(str_replace('_', ' ', $category))) . ' Expiry',
                $type,
                ucfirst(str_replace('_', ' ', $category))
            );
        }

        return $events->sortBy('date')->values();
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
}
