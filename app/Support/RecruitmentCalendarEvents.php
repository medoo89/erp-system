<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecruitmentCalendarEvents
{
    public static function make(): array
    {
        $events = [];

        $events = array_merge($events, static::manualCalendarEvents());
        $events = array_merge($events, static::jobExpiryEvents());
        $events = array_merge($events, static::employmentRotationEvents());
        $events = array_merge($events, static::employmentDirectDateEvents());
        $events = array_merge($events, static::employmentFileExpiryEvents());
        $events = array_merge($events, static::preEmploymentFileExpiryEvents());
        $events = array_merge($events, static::preEmploymentRequirementEvents());

        return collect($events)
            ->filter(fn (array $event) => filled($event['start'] ?? null) && filled($event['title'] ?? null))
            ->unique(fn (array $event) => ($event['source'] ?? 'event') . '|' . ($event['source_id'] ?? '') . '|' . ($event['type'] ?? '') . '|' . ($event['start'] ?? '') . '|' . ($event['title'] ?? ''))
            ->sortBy('start')
            ->values()
            ->all();
    }

    protected static function manualCalendarEvents(): array
    {
        if (! Schema::hasTable('calendar_events')) {
            return [];
        }

        $query = DB::table('calendar_events');

        if (Schema::hasColumn('calendar_events', 'is_active')) {
            $query->where(function ($q) {
                $q->where('is_active', true)
                    ->orWhereNull('is_active');
            });
        }

        return $query
            ->orderBy('event_date')
            ->get()
            ->map(function ($event) {
                $type = (string) ($event->event_type ?? 'task');

                return static::event(
                    title: (string) ($event->title ?? 'Calendar Event'),
                    start: $event->event_date ?? null,
                    type: $type,
                    color: static::colorForType($type),
                    source: (string) ($event->linked_type ?? 'manual'),
                    sourceId: $event->linked_id ?? ($event->id ?? null),
                    notes: $event->notes ?? null,
                    extra: [
                        'job_title' => $event->job_title ?? null,
                        'calendar_event_id' => $event->id ?? null,
                        'linked_type' => $event->linked_type ?? null,
                        'linked_id' => $event->linked_id ?? null,
                    ],
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function jobExpiryEvents(): array
    {
        if (! Schema::hasTable('jobs')) {
            return [];
        }

        $dateColumn = collect(['expiry_date', 'closing_date', 'deadline', 'application_deadline'])
            ->first(fn ($column) => Schema::hasColumn('jobs', $column));

        if (! $dateColumn) {
            return [];
        }

        return DB::table('jobs as j')
            ->whereNotNull("j.$dateColumn")
            ->get()
            ->map(function ($job) use ($dateColumn) {
                return static::event(
                    title: 'Job Expiry: ' . static::safeText($job->title ?? 'Job #' . $job->id),
                    start: $job->{$dateColumn} ?? null,
                    type: 'job_expiry',
                    color: '#f59e0b',
                    source: 'job',
                    sourceId: $job->id ?? null,
                    notes: 'Recruitment job opening expiry / deadline.',
                    extra: [
                        'job_title' => $job->title ?? null,
                    ],
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function employmentRotationEvents(): array
    {
        if (! Schema::hasTable('employment_rotations') || ! Schema::hasTable('employments')) {
            return [];
        }

        $rows = DB::table('employment_rotations as r')
            ->leftJoin('employments as e', 'e.id', '=', 'r.employment_id')
            ->select([
                'r.*',
                'e.employee_name',
                'e.employee_code',
                'e.position_title',
                'e.project_name',
                'e.client_name',
            ])
            ->get();

        $events = [];

        foreach ($rows as $rotation) {
            $employee = static::employeeLabel($rotation);
            $rotationLabel = static::safeText($rotation->rotation_label ?? ('Rotation #' . $rotation->id));
            $notes = trim(implode("\n", array_filter([
                'Employee: ' . $employee,
                'Rotation: ' . $rotationLabel,
                filled($rotation->rotation_pattern ?? null) ? 'Pattern: ' . $rotation->rotation_pattern : null,
                filled($rotation->status ?? null) ? 'Status: ' . $rotation->status : null,
                filled($rotation->travel_status ?? null) ? 'Travel Status: ' . $rotation->travel_status : null,
                filled($rotation->client_name ?? null) ? 'Client: ' . $rotation->client_name : null,
                filled($rotation->project_name ?? null) ? 'Project: ' . $rotation->project_name : null,
                $rotation->notes ?? null,
            ])));

            if (filled($rotation->mobilization_date ?? null)) {
                $events[] = static::event(
                    title: 'Travel / Mobilization: ' . $employee,
                    start: $rotation->mobilization_date,
                    type: 'mobilization',
                    color: '#2563eb',
                    source: 'employment_rotation',
                    sourceId: $rotation->id,
                    notes: $notes,
                    extra: static::employmentExtra($rotation),
                );
            }

            if (filled($rotation->from_date ?? null)) {
                $events[] = static::event(
                    title: 'Work Start: ' . $employee,
                    start: $rotation->from_date,
                    type: 'rotation_start',
                    color: '#10b981',
                    source: 'employment_rotation_start',
                    sourceId: $rotation->id,
                    notes: $notes,
                    extra: static::employmentExtra($rotation),
                );
            }

            if (filled($rotation->to_date ?? null)) {
                $events[] = static::event(
                    title: 'Work End: ' . $employee,
                    start: $rotation->to_date,
                    type: 'rotation_end',
                    color: '#059669',
                    source: 'employment_rotation_end',
                    sourceId: $rotation->id,
                    notes: $notes,
                    extra: static::employmentExtra($rotation),
                );
            }

            if (filled($rotation->demobilization_date ?? null)) {
                $events[] = static::event(
                    title: 'Demobilization / Return: ' . $employee,
                    start: $rotation->demobilization_date,
                    type: 'demobilization',
                    color: '#7c3aed',
                    source: 'employment_rotation',
                    sourceId: $rotation->id,
                    notes: $notes,
                    extra: static::employmentExtra($rotation),
                );
            }

            if (filled($rotation->ticket_file_path ?? null)) {
                $date = $rotation->mobilization_date ?: $rotation->from_date ?: $rotation->created_at ?? null;

                $events[] = static::event(
                    title: 'Ticket File: ' . $employee,
                    start: $date,
                    type: 'ticket',
                    color: '#8b5cf6',
                    source: 'employment_rotation_ticket',
                    sourceId: $rotation->id,
                    notes: trim($notes . "\nTicket file is available."),
                    extra: static::employmentExtra($rotation),
                );
            }

            if (filled($rotation->travel_request_file_path ?? null)) {
                $date = $rotation->mobilization_date ?: $rotation->from_date ?: $rotation->created_at ?? null;

                $events[] = static::event(
                    title: 'Travel Request: ' . $employee,
                    start: $date,
                    type: 'travel_request',
                    color: '#0ea5e9',
                    source: 'employment_rotation_travel_request',
                    sourceId: $rotation->id,
                    notes: trim($notes . "\nTravel request file is available."),
                    extra: static::employmentExtra($rotation),
                );
            }
        }

        return collect($events)->filter()->values()->all();
    }

    protected static function employmentDirectDateEvents(): array
    {
        if (! Schema::hasTable('employments')) {
            return [];
        }

        $events = [];

        $rows = DB::table('employments')->get();

        foreach ($rows as $employment) {
            $employee = static::employeeLabel($employment);

            $baseExtra = [
                'employee_name' => $employment->employee_name ?? null,
                'employee_code' => $employment->employee_code ?? null,
                'position_title' => $employment->position_title ?? null,
                'project_name' => $employment->project_name ?? null,
                'client_name' => $employment->client_name ?? null,
                'job_title' => $employment->position_title ?? null,
            ];

            if (filled($employment->visa_expiry_date ?? null)) {
                $events[] = static::event(
                    title: 'Visa Expiry: ' . $employee,
                    start: $employment->visa_expiry_date,
                    type: 'visa_expiry',
                    color: static::expiryColor($employment->visa_expiry_date),
                    source: 'employment_visa',
                    sourceId: $employment->id,
                    notes: static::expiryNotes('Visa', $employment->visa_expiry_date, $employment->visa_status ?? null, $employment),
                    extra: $baseExtra,
                );
            }

            if (filled($employment->medical_expiry_date ?? null)) {
                $events[] = static::event(
                    title: 'Medical Expiry: ' . $employee,
                    start: $employment->medical_expiry_date,
                    type: 'medical_expiry',
                    color: static::expiryColor($employment->medical_expiry_date),
                    source: 'employment_medical',
                    sourceId: $employment->id,
                    notes: static::expiryNotes('Medical', $employment->medical_expiry_date, $employment->medical_status ?? null, $employment),
                    extra: $baseExtra,
                );
            }

            if (filled($employment->contract_end_date ?? null) && ! (bool) ($employment->is_open_ended_contract ?? false)) {
                $events[] = static::event(
                    title: 'Contract End: ' . $employee,
                    start: $employment->contract_end_date,
                    type: 'contract_expiry',
                    color: static::expiryColor($employment->contract_end_date),
                    source: 'employment_contract',
                    sourceId: $employment->id,
                    notes: static::expiryNotes('Contract', $employment->contract_end_date, $employment->contract_status ?? null, $employment),
                    extra: $baseExtra,
                );
            }

            if (filled($employment->mobilization_date ?? null)) {
                $events[] = static::event(
                    title: 'Employment Mobilization: ' . $employee,
                    start: $employment->mobilization_date,
                    type: 'mobilization',
                    color: '#2563eb',
                    source: 'employment_mobilization',
                    sourceId: $employment->id,
                    notes: 'Employment-level mobilization date.',
                    extra: $baseExtra,
                );
            }

            if (filled($employment->demobilization_date ?? null)) {
                $events[] = static::event(
                    title: 'Employment Demobilization: ' . $employee,
                    start: $employment->demobilization_date,
                    type: 'demobilization',
                    color: '#7c3aed',
                    source: 'employment_demobilization',
                    sourceId: $employment->id,
                    notes: 'Employment-level demobilization date.',
                    extra: $baseExtra,
                );
            }
        }

        return collect($events)->filter()->values()->all();
    }

    protected static function employmentFileExpiryEvents(): array
    {
        if (! Schema::hasTable('employment_files')) {
            return [];
        }

        $query = DB::table('employment_files as f')
            ->leftJoin('employments as e', 'e.id', '=', 'f.employment_id')
            ->whereNotNull('f.expiry_date')
            ->select([
                'f.*',
                'e.employee_name',
                'e.employee_code',
                'e.position_title',
                'e.project_name',
                'e.client_name',
            ]);

        if (Schema::hasColumn('employment_files', 'is_active')) {
            $query->where(function ($q) {
                $q->where('f.is_active', true)->orWhereNull('f.is_active');
            });
        }

        return $query
            ->get()
            ->map(function ($file) {
                $employee = static::employeeLabel($file);
                $category = static::documentCategoryLabel($file->category ?? 'document');
                $title = filled($file->title ?? null) ? $file->title : $category;

                return static::event(
                    title: $category . ' Expiry: ' . $employee,
                    start: $file->expiry_date,
                    type: static::documentTypeKey($file->category ?? 'document'),
                    color: static::expiryColor($file->expiry_date, $file->category ?? null),
                    source: 'employment_file',
                    sourceId: $file->id,
                    notes: trim(implode("\n", array_filter([
                        'Employee: ' . $employee,
                        'Document: ' . $title,
                        'Category: ' . $category,
                        filled($file->document_status ?? null) ? 'Status: ' . $file->document_status : null,
                        filled($file->document_date ?? null) ? 'Document Date: ' . Carbon::parse($file->document_date)->toDateString() : null,
                        'Expiry Date: ' . Carbon::parse($file->expiry_date)->toDateString(),
                        filled($file->client_name ?? null) ? 'Client: ' . $file->client_name : null,
                        filled($file->project_name ?? null) ? 'Project: ' . $file->project_name : null,
                        $file->notes ?? null,
                    ]))),
                    extra: static::employmentExtra($file),
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function preEmploymentFileExpiryEvents(): array
    {
        if (! Schema::hasTable('pre_employment_files')) {
            return [];
        }

        $query = DB::table('pre_employment_files as f')
            ->leftJoin('pre_employments as p', 'p.id', '=', 'f.pre_employment_id')
            ->whereNotNull('f.expiry_date')
            ->select([
                'f.*',
                'p.candidate_name',
                'p.employee_code',
                'p.status as pre_employment_status',
            ]);

        if (Schema::hasColumn('pre_employment_files', 'is_active')) {
            $query->where(function ($q) {
                $q->where('f.is_active', true)->orWhereNull('f.is_active');
            });
        }

        return $query
            ->get()
            ->map(function ($file) {
                $candidate = static::safeText($file->candidate_name ?? ('Pre-Employment #' . $file->pre_employment_id));
                $category = static::documentCategoryLabel($file->category ?? 'document');
                $title = filled($file->title ?? null) ? $file->title : $category;

                return static::event(
                    title: 'Pre-Hire ' . $category . ' Expiry: ' . $candidate,
                    start: $file->expiry_date,
                    type: static::documentTypeKey($file->category ?? 'pre_employment_document'),
                    color: static::expiryColor($file->expiry_date, $file->category ?? null),
                    source: 'pre_employment_file',
                    sourceId: $file->id,
                    notes: trim(implode("\n", array_filter([
                        'Candidate: ' . $candidate,
                        filled($file->employee_code ?? null) ? 'Employee Code: ' . $file->employee_code : null,
                        'Document: ' . $title,
                        'Category: ' . $category,
                        filled($file->document_date ?? null) ? 'Document Date: ' . Carbon::parse($file->document_date)->toDateString() : null,
                        'Expiry Date: ' . Carbon::parse($file->expiry_date)->toDateString(),
                        filled($file->pre_employment_status ?? null) ? 'Pre-Employment Status: ' . $file->pre_employment_status : null,
                        $file->notes ?? null,
                    ]))),
                    extra: [
                        'employee_name' => $candidate,
                        'employee_code' => $file->employee_code ?? null,
                        'job_title' => 'Pre-Employment',
                    ],
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function preEmploymentRequirementEvents(): array
    {
        if (! Schema::hasTable('pre_employment_requirements')) {
            return [];
        }

        $rows = DB::table('pre_employment_requirements as r')
            ->leftJoin('pre_employments as p', 'p.id', '=', 'r.pre_employment_id')
            ->whereNotNull('r.deadline')
            ->select([
                'r.*',
                'p.candidate_name',
                'p.employee_code',
            ])
            ->get();

        return $rows
            ->map(function ($requirement) {
                $candidate = static::safeText($requirement->candidate_name ?? ('Pre-Employment #' . $requirement->pre_employment_id));

                return static::event(
                    title: 'Requirement Deadline: ' . static::safeText($requirement->title ?? 'Requirement') . ' — ' . $candidate,
                    start: $requirement->deadline,
                    type: 'requirement_deadline',
                    color: static::expiryColor($requirement->deadline),
                    source: 'pre_employment_requirement',
                    sourceId: $requirement->id,
                    notes: trim(implode("\n", array_filter([
                        'Candidate: ' . $candidate,
                        filled($requirement->requirement_type ?? null) ? 'Type: ' . $requirement->requirement_type : null,
                        filled($requirement->status ?? null) ? 'Status: ' . $requirement->status : null,
                        $requirement->hr_note ?? null,
                    ]))),
                    extra: [
                        'employee_name' => $candidate,
                        'employee_code' => $requirement->employee_code ?? null,
                        'job_title' => 'Pre-Employment Requirement',
                    ],
                );
            })
            ->filter()
            ->values()
            ->all();
    }

    protected static function event(
        string $title,
        mixed $start,
        string $type,
        string $color,
        ?string $source = null,
        mixed $sourceId = null,
        ?string $notes = null,
        ?string $end = null,
        array $extra = [],
    ): ?array {
        if (blank($start)) {
            return null;
        }

        try {
            $startDate = Carbon::parse($start)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }

        $event = [
            'title' => $title,
            'start' => $startDate,
            'type' => $type,
            'icon' => static::iconForType($type),
            'backgroundColor' => $color,
            'borderColor' => $color,
            'textColor' => '#ffffff',
            'source' => $source ?: $type,
            'source_id' => $sourceId,
            'notes' => $notes,
        ];

        if (filled($end)) {
            try {
                $event['end'] = Carbon::parse($end)->toDateString();
                $event['allDay'] = true;
            } catch (\Throwable $e) {
                // Keep event without end date.
            }
        }

        foreach ($extra as $key => $value) {
            if (filled($value)) {
                $event[$key] = $value;
            }
        }

        return $event;
    }

    public static function colorForType(string $type): string
    {
        return match ($type) {
            'job_expiry', 'expiry', 'requirement_deadline' => '#f59e0b',
            'visa', 'visa_expiry' => '#ef4444',
            'passport', 'passport_expiry' => '#dc2626',
            'desert_pass', 'desert_pass_expiry' => '#f97316',
            'medical', 'medical_expiry' => '#06b6d4',
            'contract', 'contract_expiry' => '#64748b',
            'rotation' => '#10b981',
            'rotation_start' => '#10b981',
            'rotation_end' => '#059669',
            'mobilization', 'travel_request' => '#2563eb',
            'demobilization' => '#7c3aed',
            'ticket' => '#8b5cf6',
            'task' => '#0ea5e9',
            'interview' => '#6366f1',
            default => '#14b8a6',
        };
    }

    protected static function expiryColor(mixed $date, ?string $category = null): string
    {
        try {
            $expiry = Carbon::parse($date)->startOfDay();
            $days = now()->startOfDay()->diffInDays($expiry, false);
        } catch (\Throwable $e) {
            return static::colorForType(static::documentTypeKey($category ?: 'expiry'));
        }

        if ($days < 0) {
            return '#991b1b';
        }

        if ($days <= 15) {
            return '#ef4444';
        }

        if ($days <= 45) {
            return '#f97316';
        }

        if ($days <= 90) {
            return '#f59e0b';
        }

        return static::colorForType(static::documentTypeKey($category ?: 'expiry'));
    }

    protected static function documentTypeKey(string $category): string
    {
        $category = strtolower(trim($category));

        return match ($category) {
            'passport' => 'passport_expiry',
            'visa' => 'visa_expiry',
            'desert_pass', 'desert pass', 'desert-pass' => 'desert_pass_expiry',
            'medical' => 'medical_expiry',
            'contract' => 'contract_expiry',
            'training' => 'training_expiry',
            'certificate', 'certification' => 'certificate_expiry',
            'ticket' => 'ticket',
            'travel_request' => 'travel_request',
            default => 'document_expiry',
        };
    }

    protected static function documentCategoryLabel(?string $category): string
    {
        $category = strtolower(trim((string) $category));

        return match ($category) {
            'passport' => 'Passport',
            'visa' => 'Visa',
            'desert_pass', 'desert pass', 'desert-pass' => 'Desert Pass',
            'medical' => 'Medical',
            'contract' => 'Contract',
            'training' => 'Training',
            'certificate', 'certification' => 'Certificate',
            'ticket' => 'Ticket',
            'travel_request' => 'Travel Request',
            'rotation_document' => 'Rotation Document',
            'internal_document' => 'Internal Document',
            default => filled($category) ? str($category)->replace(['_', '-'], ' ')->title()->toString() : 'Document',
        };
    }

    protected static function employeeLabel(object $record): string
    {
        $name = $record->employee_name
            ?? $record->candidate_name
            ?? null;

        $code = $record->employee_code ?? null;

        if (filled($name) && filled($code)) {
            return static::safeText($name) . ' [' . static::safeText($code) . ']';
        }

        if (filled($name)) {
            return static::safeText($name);
        }

        if (filled($code)) {
            return static::safeText($code);
        }

        return 'Employee #' . ($record->employment_id ?? $record->id ?? '-');
    }

    protected static function employmentExtra(object $record): array
    {
        return [
            'employee_name' => $record->employee_name ?? null,
            'employee_code' => $record->employee_code ?? null,
            'position_title' => $record->position_title ?? null,
            'project_name' => $record->project_name ?? null,
            'client_name' => $record->client_name ?? null,
            'job_title' => $record->position_title ?? null,
        ];
    }

    protected static function expiryNotes(string $label, mixed $date, ?string $status, object $employment): string
    {
        return trim(implode("\n", array_filter([
            'Employee: ' . static::employeeLabel($employment),
            $label . ' expiry date: ' . Carbon::parse($date)->toDateString(),
            filled($status) ? 'Status: ' . $status : null,
            filled($employment->client_name ?? null) ? 'Client: ' . $employment->client_name : null,
            filled($employment->project_name ?? null) ? 'Project: ' . $employment->project_name : null,
        ])));
    }

    protected static function safeText(mixed $value): string
    {
        return trim((string) ($value ?? ''));
    }
    public static function iconForType(?string $type): string
    {
        $type = strtolower((string) $type);

        return match (true) {
            str_contains($type, 'ticket'),
            str_contains($type, 'travel'),
            str_contains($type, 'mobilization'),
            str_contains($type, 'flight') => 'flight_takeoff',

            str_contains($type, 'return'),
            str_contains($type, 'demobilization') => 'flight_land',

            str_contains($type, 'hotel'),
            str_contains($type, 'accommodation') => 'hotel',

            str_contains($type, 'visa') => 'approval_delegation',

            str_contains($type, 'medical'),
            str_contains($type, 'health') => 'medical_services',

            str_contains($type, 'training'),
            str_contains($type, 'certificate') => 'workspace_premium',

            str_contains($type, 'document'),
            str_contains($type, 'file'),
            str_contains($type, 'expiry') => 'description',

            str_contains($type, 'rotation'),
            str_contains($type, 'work_start'),
            str_contains($type, 'work_end') => 'sync_alt',

            str_contains($type, 'job') => 'work',

            default => 'event',
        };
    }

}
