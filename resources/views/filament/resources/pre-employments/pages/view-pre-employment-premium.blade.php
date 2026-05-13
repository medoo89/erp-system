<x-filament-panels::page>
@php

    $record->loadMissing([
        'job.project.client',
        'jobApplication.values.field',
        'assignedHrUser',
        'files',
        'portalFields',
        'portalValues.field',
        'currentFinanceProfile',
        'financeExpenses',
    ]);

    $candidateName = $record->candidate_name
        ?? $record->jobApplication?->full_name
        ?? 'Candidate';

    $candidateEmail = $record->candidate_email
        ?? $record->jobApplication?->email
        ?? '-';

    $candidatePhone = $record->candidate_phone
        ?? $record->jobApplication?->phone
        ?? '-';

    $position = $record->job?->title
        ?? $record->jobApplication?->job?->title
        ?? '-';

    $project = $record->job?->project?->name
        ?? $record->jobApplication?->job?->project?->name
        ?? '-';

    $client = $record->job?->project?->client?->name
        ?? $record->jobApplication?->job?->project?->client?->name
        ?? '-';

    $employeeCode = $record->employee_code ?: '-';

    $statusLabel = filled($record->status)
        ? \Illuminate\Support\Str::headline($record->status)
        : '-';

    $portalStatusLabel = filled($record->portal_status)
        ? \Illuminate\Support\Str::headline($record->portal_status)
        : 'Not Sent';

    $createdAt = optional($record->created_at)->format('Y-m-d H:i') ?: '-';
    $updatedAt = optional($record->updated_at)->format('Y-m-d H:i') ?: '-';
    $portalSentAt = optional($record->portal_last_sent_at)->format('Y-m-d H:i') ?: '-';
    $portalSubmittedAt = optional($record->portal_last_submitted_at)->format('Y-m-d H:i') ?: '-';
    $convertedAt = optional($record->converted_to_employment_at)->format('Y-m-d H:i') ?: null;

    $publicLink = filled($record->portal_token)
        ? url('/pre-employment/portal/' . $record->portal_token)
        : null;

    $statusTone = match ($record->status) {
        'ready_for_employment', 'converted_to_employment' => 'success',
        'awaiting_candidate_upload', 'additional_documents_required', 'pending_medical', 'pending_visa', 'pending_travel' => 'warning',
        'documents_under_review' => 'info',
        'returned_to_job_application', 'declined' => 'danger',
        default => 'neutral',
    };

    $prettyFileTitle = function (?string $title, ?string $category = null, ?string $path = null) use ($candidateName) {
        $raw = trim((string) ($title ?: $category ?: ''));

        $lower = strtolower($raw);

        $map = [
            'cv_path' => 'Candidate CV',
            'cv file' => 'Candidate CV',
            'cv_file' => 'Candidate CV',
            'resume_path' => 'Candidate CV',
            'caf_file_path' => 'CAF File',
            'gl_file_path' => 'GL File',
            'passport' => 'Passport',
            'contract' => 'Contract',
            'signed_contract' => 'Signed Contract',
            'medical_certificate' => 'Medical Certificate',
            'health certificate' => 'Medical Certificate',
            'atex_certificate' => 'ATEX Certificate',
            'atex cert' => 'ATEX Certificate',
            'photo' => 'Personal Photo',
            'visa' => 'Visa',
            'travel' => 'Travel Document',
            'certificate' => 'Certificate',
        ];

        foreach ($map as $needle => $label) {
            if (str_contains($lower, $needle)) {
                return $label . ' — ' . $candidateName;
            }
        }

        if ($raw !== '') {
            return \Illuminate\Support\Str::headline($raw) . ' — ' . $candidateName;
        }

        if ($path) {
            return \Illuminate\Support\Str::headline(pathinfo($path, PATHINFO_FILENAME)) . ' — ' . $candidateName;
        }

        return 'Uploaded File — ' . $candidateName;
    };

    $normalizePath = function ($path) {
        if (blank($path)) {
            return null;
        }

        if (is_array($path)) {
            $path = collect($path)->filter()->first();
        }

        $path = trim((string) $path);
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#^public/#', '', $path);
        $path = preg_replace('#^storage/#', '', $path);

        return $path ?: null;
    };

    $looksLikeFile = function ($label, $value) {
        $label = strtolower((string) $label);
        $value = is_array($value) ? implode(' ', $value) : (string) $value;
        $lowerValue = strtolower($value);

        return str_contains($label, 'cv')
            || str_contains($label, 'file')
            || str_contains($label, 'upload')
            || str_contains($label, 'certificate')
            || str_contains($label, 'passport')
            || str_contains($label, 'contract')
            || str_contains($lowerValue, '/')
            || preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|webp|rtf|xls|xlsx)$/i', $value);
    };

    $files = collect();
    $seenFiles = [];

    $addFile = function ($title, $category, $path, $source = 'Pre-Employment') use (&$files, &$seenFiles, $normalizePath, $prettyFileTitle) {
        $cleanPath = $normalizePath($path);

        if (! filled($cleanPath)) {
            return;
        }

        $key = strtolower($cleanPath);

        if (isset($seenFiles[$key])) {
            return;
        }

        $seenFiles[$key] = true;

        $files->push([
            'title' => $prettyFileTitle($title, $category, $cleanPath),
            'category' => filled($category) ? \Illuminate\Support\Str::headline($category) : 'File',
            'path' => $cleanPath,
            'url' => \Illuminate\Support\Facades\Storage::url($cleanPath),
            'source' => $source,
        ]);
    };

    foreach ($record->files ?? [] as $file) {
        $addFile(
            $file->title ?? $file->category ?? 'Uploaded File',
            $file->category ?? 'Pre-Employment File',
            $file->file_path ?? null,
            ($file->uploaded_by_type ?? null) === 'candidate' ? 'Candidate Portal' : 'ERP Upload'
        );
    }

    foreach (['caf_file_path' => 'CAF File', 'gl_file_path' => 'GL File'] as $column => $label) {
        if (filled($record->{$column} ?? null)) {
            $addFile($label, $label, $record->{$column}, 'Pre-Employment');
        }
    }

    foreach ($record->portalValues ?? [] as $value) {
        $field = $value->field ?? null;
        $label = $field?->label ?? $value->field_key ?? 'Portal Upload';
        $answer = $value->value ?? $value->file_path ?? null;

        if (is_array($answer)) {
            foreach ($answer as $one) {
                if ($looksLikeFile($label, $one)) {
                    $addFile($label, $field?->document_category ?? 'Portal Upload', $one, 'Candidate Portal');
                }
            }
        } elseif ($looksLikeFile($label, $answer)) {
            $addFile($label, $field?->document_category ?? 'Portal Upload', $answer, 'Candidate Portal');
        }
    }

    if ($record->jobApplication) {
        foreach (['cv_path' => 'Candidate CV', 'cv_file' => 'Candidate CV', 'resume_path' => 'Candidate CV', 'file_path' => 'Application File', 'attachment_path' => 'Application File'] as $column => $label) {
            if (filled($record->jobApplication->{$column} ?? null)) {
                $addFile($label, 'Job Application', $record->jobApplication->{$column}, 'Job Application');
            }
        }

        foreach ($record->jobApplication->values ?? [] as $value) {
            $field = $value->field ?? null;
            $label = $field?->label ?? $field?->name ?? 'Application Upload';
            $answer = $value->value ?? $value->answer ?? $value->field_value ?? null;

            if (is_array($answer)) {
                foreach ($answer as $one) {
                    if ($looksLikeFile($label, $one)) {
                        $addFile($label, 'Job Application Answer', $one, 'Job Application');
                    }
                }
            } elseif ($looksLikeFile($label, $answer)) {
                $addFile($label, 'Job Application Answer', $answer, 'Job Application');
            }
        }
    }

    $portalAnswers = collect();

    foreach ($record->portalValues ?? [] as $value) {
        $field = $value->field ?? null;
        $label = $field?->label ?? $value->field_key ?? 'Portal Answer';
        $answer = $value->value ?? null;

        if (is_array($answer)) {
            $answer = implode(', ', array_filter($answer));
        }

        if (filled($answer) && ! $looksLikeFile($label, $answer)) {
            $portalAnswers->push([
                'label' => $label,
                'value' => $answer,
            ]);
        }
    }

    $portalFileRequests = collect($record->portalFields ?? [])
        ->filter(fn ($field) => ($field->field_type ?? null) === 'file')
        ->values();

    $financeProfile = $record->currentFinanceProfile;

    $expenses = collect($record->financeExpenses ?? []);

    $timelineRows = collect();

    $pushTimeline = function ($type, $title, $subtitle = null, $date = null, array $meta = []) use (&$timelineRows) {
        if (! $date) {
            return;
        }

        try {
            $time = $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date);
        } catch (\Throwable $e) {
            return;
        }

        $timelineRows->push([
            'type' => $type ?: 'update',
            'title' => $title ?: 'Update',
            'subtitle' => $subtitle,
            'date' => $time,
            'meta' => array_filter($meta),
        ]);
    };

    $pushTimeline('created', 'Pre-Employment Created', 'Pre-employment record opened for ' . $candidateName . '.', $record->created_at, ['Pre-Employment', $statusLabel]);

    if (filled($record->portal_last_sent_at)) {
        $pushTimeline('portal', 'Portal Link Sent', 'Candidate public portal link was sent.', $record->portal_last_sent_at, ['Portal', $portalStatusLabel]);
    }

    if (filled($record->portal_last_submitted_at)) {
        $pushTimeline('portal', 'Portal Submitted', 'Candidate submitted portal answers/files.', $record->portal_last_submitted_at, ['Portal Submission']);
    }

    foreach ($portalFileRequests as $field) {
        $pushTimeline(
            'request',
            'File Requested: ' . ($field->label ?: 'Document'),
            $field->instructions ?: null,
            $field->created_at,
            [$field->document_category ? \Illuminate\Support\Str::headline($field->document_category) : 'File Request', $field->is_required ? 'Required' : 'Optional']
        );
    }

    foreach ($files as $file) {
        $pushTimeline(
            'file',
            'File Available: ' . $file['title'],
            $file['source'] ?? null,
            $record->updated_at ?? $record->created_at,
            [$file['category'] ?? 'File']
        );
    }

    foreach ($expenses as $expense) {
        $pushTimeline(
            'expense',
            'Expense Added: ' . ($expense->title ?: \Illuminate\Support\Str::headline($expense->category ?? 'Expense')),
            trim(($expense->amount ?? '') . ' ' . ($expense->currency ?? '')),
            $expense->expense_date ?? $expense->created_at,
            [\Illuminate\Support\Str::headline($expense->status ?? 'draft'), \Illuminate\Support\Str::headline($expense->paid_by ?? 'company')]
        );
    }

    if (filled($record->converted_to_employment_at)) {
        $pushTimeline('converted', 'Converted to Employment', 'Candidate moved from Pre-Employment to Employment.', $record->converted_to_employment_at, ['Employment']);
    }

    if (filled($record->updated_at) && (string) $record->updated_at !== (string) $record->created_at) {
        $pushTimeline('updated', 'Last Updated', 'Pre-employment profile was updated.', $record->updated_at, ['Update']);
    }

    $timelineRows = $timelineRows
        ->filter(fn ($row) => ! empty($row['date']))
        ->sortByDesc(fn ($row) => $row['date']->timestamp)
        ->values();

    $timelineIcon = function ($type) {
        return match ($type) {
            'file' => 'description',
            'portal' => 'language',
            'request' => 'assignment',
            'expense' => 'payments',
            'converted' => 'verified',
            'created' => 'badge',
            default => 'update',
        };
    };
@endphp


{{-- SFPE REAL HEADER FORCE START --}}
<section class="sfpe-real-hero" data-sfpe-main-hero="1">
    <div class="sfpe-real-kicker">Recruitment › Pre-Employment › Profile</div>

    <h1 class="sfpe-real-title">
        Pre-Employment<br>
        {{ $candidateName }}
    </h1>

    <p class="sfpe-real-subtitle">
        Review candidate pre-employment status, files, requests, finance profile, expenses, and full candidate timeline.
    </p>

    <div class="sfpe-real-badges">
        <span>{{ $statusLabel }}</span>
        <span>{{ $employeeCode }}</span>
    </div>

    <div class="sfpe-real-actions">
        @if($publicLink)
            <a href="{{ $publicLink }}" target="_blank" class="sfpe-real-btn sfpe-btn-light">
                <span class="material-symbols-rounded">open_in_new</span>
                Open Public Link
            </a>
        @endif

        <button type="button" wire:click="mountAction('sendPortalRequest')" class="sfpe-real-btn sfpe-btn-green">
            <span class="material-symbols-rounded">send</span>
            Resend Public Link
        </button>

        <button type="button" wire:click="mountAction('requestPreEmploymentFile')" class="sfpe-real-btn sfpe-btn-orange">
            <span class="material-symbols-rounded">request_page</span>
            Request File
        </button>

        <button type="button" wire:click="mountAction('uploadPreEmploymentFile')" class="sfpe-real-btn sfpe-btn-blue">
            <span class="material-symbols-rounded">upload_file</span>
            Upload File
        </button>

        <button type="button" wire:click="mountAction('addExpense')" class="sfpe-real-btn sfpe-btn-red">
            <span class="material-symbols-rounded">payments</span>
            Add Expense
        </button>

        <button type="button" wire:click="mountAction('editFinalProfile')" class="sfpe-real-btn sfpe-btn-purple">
            <span class="material-symbols-rounded">account_balance_wallet</span>
            Finance Profile
        </button>

        <button type="button" wire:click="mountAction('changePreEmploymentStatus')" class="sfpe-real-btn sfpe-btn-teal">
            <span class="material-symbols-rounded">published_with_changes</span>
            Change Status
        </button>

        <button type="button" wire:click="mountAction('convertToEmployment')" class="sfpe-real-btn sfpe-btn-dark">
            <span class="material-symbols-rounded">badge</span>
            Convert to Employment
        </button>
    </div>
</section>

<style id="sfpe-real-hero-final-css">
    .sfpe-real-hero {
        width: min(100%, 1280px);
        margin: 24px auto 28px !important;
        padding: 34px;
        border-radius: 32px;
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .20), transparent 34%),
            linear-gradient(135deg, #020617 0%, #0f172a 50%, #123456 100%);
        border: 1px solid rgba(148, 163, 184, .18);
        box-shadow: 0 24px 60px rgba(15, 23, 42, .22);
        color: #fff;
        z-index: 10;
        order: -9999 !important;
    }

    .sfpe-real-kicker {
        color: #cbd5e1;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .sfpe-real-title {
        margin: 0;
        color: #fff;
        font-size: clamp(40px, 5vw, 74px);
        line-height: .92;
        font-weight: 950;
        letter-spacing: -.065em;
    }

    .sfpe-real-subtitle {
        width: min(100%, 760px);
        margin: 18px 0 0;
        color: #dbeafe;
        font-size: 15px;
        line-height: 1.7;
        font-weight: 750;
    }

    .sfpe-real-badges,
    .sfpe-real-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sfpe-real-badges {
        margin-top: 20px;
    }

    .sfpe-real-badges span {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        color: #fff;
        border: 1px solid rgba(255,255,255,.14);
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sfpe-real-actions {
        margin-top: 24px;
    }

    .sfpe-real-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 46px;
        padding: 0 16px;
        border-radius: 999px;
        border: 0;
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 950;
        cursor: pointer;
        box-shadow: 0 14px 28px rgba(0,0,0,.18);
        white-space: nowrap;
    }

    .sfpe-real-btn .material-symbols-rounded {
        font-size: 20px;
        line-height: 1;
        font-variation-settings: 'FILL' 0, 'wght' 700, 'GRAD' 0, 'opsz' 24;
    }

    .sfpe-btn-light { background: #ffffff; color: #0f172a !important; }
    .sfpe-btn-green { background: #16a34a; color: #ffffff !important; }
    .sfpe-btn-orange { background: #f59e0b; color: #111827 !important; }
    .sfpe-btn-blue { background: #2563eb; color: #ffffff !important; }
    .sfpe-btn-red { background: #ef4444; color: #ffffff !important; }
    .sfpe-btn-purple { background: #7c3aed; color: #ffffff !important; }
    .sfpe-btn-teal { background: #0f766e; color: #ffffff !important; }
    .sfpe-btn-dark { background: #020617; color: #ffffff !important; border: 1px solid rgba(255,255,255,.14); }

    @media (max-width: 760px) {
        .sfpe-real-hero {
            padding: 24px;
            border-radius: 26px;
        }

        .sfpe-real-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .sfpe-real-btn {
            width: 100%;
        }
    }
</style>

<script id="sfpe-force-hero-top-script">
    (() => {
        const moveHeroTop = () => {
            const hero = document.querySelector('[data-sfpe-main-hero="1"]');
            if (!hero) return;

            const candidateTitle = Array.from(document.querySelectorAll('.pe-card-title, h2, h3, div'))
                .find((el) => (el.textContent || '').replace(/\s+/g, ' ').trim().includes('Candidate Details'));

            const candidateCard = candidateTitle?.closest('section, .pe-card, .sfpe-card, div');

            if (candidateCard && candidateCard.parentElement && candidateCard.previousElementSibling !== hero) {
                candidateCard.parentElement.insertBefore(hero, candidateCard);
            }
        };

        document.addEventListener('DOMContentLoaded', moveHeroTop);
        document.addEventListener('livewire:navigated', moveHeroTop);
        setTimeout(moveHeroTop, 100);
        setTimeout(moveHeroTop, 500);
        setTimeout(moveHeroTop, 1200);
    })();
</script>
{{-- SFPE REAL HEADER FORCE END --}}



<style>
    .fi-header {
        display: none !important;
    }

    .sfpe-page {
        width: min(100%, 1280px);
        margin: 0 auto 72px;
        display: grid;
        gap: 22px;
    }

    .sfpe-hero {
        position: relative;
        width: min(100%, 1280px);
        margin: 0 auto 26px;
        border-radius: 34px;
        overflow: hidden;
        padding: 30px;
        color: #ffffff;
        background:
            radial-gradient(circle at 78% 12%, rgba(34, 211, 238, .34), transparent 32%),
            radial-gradient(circle at 20% 0%, rgba(37, 99, 235, .38), transparent 36%),
            linear-gradient(135deg, #0f172a 0%, #12325a 50%, #0f766e 100%);
        box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
    }

    .sfpe-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: linear-gradient(120deg, rgba(255,255,255,.10), transparent 38%, rgba(255,255,255,.06));
    }

    .sfpe-hero-inner {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 22px;
    }

    .sfpe-kicker {
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: #bae6fd;
    }

    .sfpe-title {
        margin: 8px 0 0;
        font-size: clamp(34px, 5vw, 68px);
        line-height: .92;
        letter-spacing: -.07em;
        font-weight: 950;
        color: #ffffff;
    }

    .sfpe-subtitle {
        margin-top: 14px;
        max-width: 760px;
        color: rgba(255,255,255,.78);
        font-size: 15px;
        line-height: 1.7;
        font-weight: 700;
    }

    .sfpe-hero-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 22px;
        flex-wrap: wrap;
    }

    .sfpe-hero-status {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        color: #ffffff;
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .08em;
        backdrop-filter: blur(12px);
    }

    .sfpe-hero-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 4px;
    }

    .sfpe-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 17px;
        border-radius: 999px;
        border: 0;
        text-decoration: none !important;
        font-size: 13px;
        font-weight: 950;
        cursor: pointer;
        box-shadow: 0 14px 28px rgba(0,0,0,.18);
    }

    .sfpe-action-light {
        color: #0f172a !important;
        background: #ffffff;
    }

    .sfpe-action-blue {
        color: #ffffff !important;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
    }

    .sfpe-action-green {
        color: #ffffff !important;
        background: linear-gradient(135deg, #16a34a, #15803d);
    }

    .sfpe-action-orange {
        color: #111827 !important;
        background: linear-gradient(135deg, #fbbf24, #f97316);
    }

    .sfpe-action-red {
        color: #ffffff !important;
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .sfpe-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 8px;
    }

    .sfpe-stat {
        border-radius: 22px;
        padding: 16px;
        background: rgba(255,255,255,.10);
        border: 1px solid rgba(255,255,255,.14);
        backdrop-filter: blur(12px);
    }

    .sfpe-stat-label {
        color: rgba(255,255,255,.66);
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sfpe-stat-value {
        margin-top: 8px;
        color: #ffffff;
        font-size: 18px;
        font-weight: 950;
        overflow-wrap: anywhere;
    }

    .sfpe-card {
        border-radius: 30px;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.08), transparent 35%),
            #ffffff;
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 18px 46px rgba(15,23,42,.08);
        overflow: hidden;
    }

    .dark .sfpe-card {
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.12), transparent 35%),
            rgba(15,23,42,.82);
        border-color: rgba(148,163,184,.18);
        box-shadow: 0 18px 46px rgba(0,0,0,.24);
    }

    .sfpe-card-head {
        padding: 22px 26px;
        border-bottom: 1px solid rgba(15,23,42,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.08), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .dark .sfpe-card-head {
        border-bottom-color: rgba(148,163,184,.18);
        background: rgba(15,23,42,.44);
    }

    .sfpe-card-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: #0f172a;
        font-size: 20px;
        line-height: 1.2;
        letter-spacing: -.04em;
        font-weight: 950;
    }

    .dark .sfpe-card-title {
        color: #ffffff;
    }

    .sfpe-icon {
        font-family: 'Material Symbols Rounded';
        font-weight: 600;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        color: #1d4ed8;
        display: inline-flex;
    }

    .dark .sfpe-icon {
        color: #22d3ee;
    }

    .sfpe-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 900;
        color: #0f172a;
        background: rgba(224,242,254,.88);
        border: 1px solid rgba(37,99,235,.16);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .sfpe-chip.success {
        background: #dcfce7;
        color: #166534;
        border-color: #86efac;
    }

    .sfpe-chip.warning {
        background: #ffedd5;
        color: #c2410c;
        border-color: #fdba74;
    }

    .sfpe-chip.danger {
        background: #fee2e2;
        color: #b91c1c;
        border-color: #fca5a5;
    }

    .sfpe-chip.info {
        background: #dbeafe;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .dark .sfpe-chip {
        color: #bfdbfe;
        background: rgba(37,99,235,.18);
        border-color: rgba(147,197,253,.18);
    }

    .sfpe-grid {
        padding: 24px 26px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .sfpe-item {
        min-height: 104px;
        border-radius: 22px;
        padding: 18px;
        background: rgba(248,250,252,.82);
        border: 1px solid rgba(15,23,42,.08);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .dark .sfpe-item {
        background: rgba(15,23,42,.58);
        border-color: rgba(148,163,184,.16);
    }

    .sfpe-label {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .dark .sfpe-label {
        color: #94a3b8;
    }

    .sfpe-value {
        color: #0f172a;
        font-size: 16px;
        line-height: 1.45;
        font-weight: 850;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .dark .sfpe-value {
        color: #ffffff;
    }

    .sfpe-empty {
        margin: 24px 26px;
        padding: 20px;
        border-radius: 22px;
        border: 1px dashed rgba(15,23,42,.18);
        background: rgba(248,250,252,.68);
        color: #64748b;
        font-weight: 800;
    }

    .sfpe-file-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        margin-top: 12px;
        padding: 10px 14px;
        border-radius: 999px;
        background: linear-gradient(135deg, #e0f2fe, #dbeafe);
        color: #1d4ed8 !important;
        text-decoration: none !important;
        border: 1px solid rgba(37,99,235,.14);
        font-size: 12px;
        font-weight: 950;
    }

    .sfpe-request-list {
        padding: 24px 26px;
        display: grid;
        gap: 14px;
    }

    .sfpe-request {
        border-radius: 24px;
        border: 1px solid rgba(15,23,42,.08);
        background: rgba(248,250,252,.82);
        padding: 18px;
    }

    .dark .sfpe-request {
        background: rgba(15,23,42,.58);
        border-color: rgba(148,163,184,.16);
    }

    .sfpe-request-title {
        color: #0f172a;
        font-size: 18px;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .dark .sfpe-request-title {
        color: #ffffff;
    }

    .sfpe-request-meta {
        margin-top: 10px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .sfpe-notes {
        margin-top: 14px;
        color: #475569;
        font-weight: 700;
        line-height: 1.65;
        white-space: pre-wrap;
    }

    .dark .sfpe-notes {
        color: #cbd5e1;
    }

    .sfpe-timeline-card {
        margin-bottom: 72px;
    }

    .sfpe-timeline-list {
        position: relative;
        max-height: 520px;
        overflow-y: auto;
        padding: 24px 26px 28px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sfpe-timeline-row {
        position: relative;
        display: grid;
        grid-template-columns: 64px 1fr;
        gap: 16px;
    }

    .sfpe-timeline-marker {
        position: relative;
        display: flex;
        justify-content: center;
    }

    .sfpe-timeline-marker::after {
        content: "";
        position: absolute;
        top: 58px;
        bottom: -18px;
        width: 3px;
        border-radius: 999px;
        background: #dbeafe;
    }

    .sfpe-timeline-row:last-child .sfpe-timeline-marker::after {
        display: none;
    }

    .sfpe-timeline-icon {
        width: 48px;
        height: 48px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Material Symbols Rounded';
        font-size: 25px;
        font-weight: 650;
        color: #1d4ed8;
        background: #e0f2fe;
        border: 1px solid #bfdbfe;
        box-shadow: 0 12px 28px rgba(37,99,235,.12);
        z-index: 1;
    }

    .sfpe-timeline-body {
        border-radius: 22px;
        padding: 18px;
        background: #ffffff;
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 12px 28px rgba(15,23,42,.045);
    }

    .dark .sfpe-timeline-body {
        background: rgba(15,23,42,.58);
        border-color: rgba(148,163,184,.16);
    }

    .sfpe-timeline-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .sfpe-timeline-title {
        color: #0f172a;
        font-size: 16px;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .dark .sfpe-timeline-title {
        color: #ffffff;
    }

    .sfpe-timeline-date {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
    }

    .sfpe-timeline-subtitle {
        margin-top: 8px;
        color: #475569;
        line-height: 1.6;
        font-weight: 700;
    }

    .dark .sfpe-timeline-subtitle {
        color: #cbd5e1;
    }

    .sfpe-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    @media (max-width: 1100px) {
        .sfpe-grid,
        .sfpe-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .sfpe-hero {
            padding: 22px;
            border-radius: 26px;
        }

        .sfpe-grid,
        .sfpe-stats {
            grid-template-columns: 1fr;
        }

        .sfpe-grid,
        .sfpe-card-head,
        .sfpe-request-list,
        .sfpe-timeline-list {
            padding-left: 18px;
            padding-right: 18px;
        }

        .sfpe-timeline-row {
            grid-template-columns: 48px 1fr;
        }

        .sfpe-timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            font-size: 22px;
        }
    }

    @media print {
        .fi-sidebar,
        .fi-topbar,
        .sfpe-hero-actions,
        .sfpe-action,
        button {
            display: none !important;
        }

        .sfpe-timeline-list {
            max-height: none !important;
            overflow: visible !important;
        }
    }
</style>

<div class="sfpe-page">
    <section class="sfpe-card">
        <div class="sfpe-card-head">
            <h2 class="sfpe-card-title">
                <span class="sfpe-icon">assignment_ind</span>
                Candidate Details
            </h2>
            <span class="sfpe-chip {{ $statusTone }}">{{ $statusLabel }}</span>
        </div>

        <div class="sfpe-grid">
            <div class="sfpe-item">
                <div class="sfpe-label">Full Name</div>
                <div class="sfpe-value">{{ $candidateName }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Position</div>
                <div class="sfpe-value">{{ $position }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Project</div>
                <div class="sfpe-value">{{ $project }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Client</div>
                <div class="sfpe-value">{{ $client }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Email</div>
                <div class="sfpe-value">{{ $candidateEmail }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Phone</div>
                <div class="sfpe-value">{{ $candidatePhone }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Employee Code</div>
                <div class="sfpe-value">{{ $employeeCode }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Assigned HR / Officer</div>
                <div class="sfpe-value">{{ $record->assignedHrUser?->name ?: '-' }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Created At</div>
                <div class="sfpe-value">{{ $createdAt }}</div>
            </div>
        </div>
    </section>

    <section class="sfpe-card">
        <div class="sfpe-card-head">
            <h2 class="sfpe-card-title">
                <span class="sfpe-icon">route</span>
                Pre-Employment Status
            </h2>
            <span class="sfpe-chip {{ $statusTone }}">{{ $statusLabel }}</span>
        </div>

        <div class="sfpe-grid">
            <div class="sfpe-item">
                <div class="sfpe-label">Portal Status</div>
                <div class="sfpe-value">{{ $portalStatusLabel }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Portal Sent At</div>
                <div class="sfpe-value">{{ $portalSentAt }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Portal Submitted At</div>
                <div class="sfpe-value">{{ $portalSubmittedAt }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Availability Date</div>
                <div class="sfpe-value">{{ optional($record->availability_date)->format('Y-m-d') ?: '-' }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Contract Status</div>
                <div class="sfpe-value">{{ filled($record->contract_status) ? \Illuminate\Support\Str::headline($record->contract_status) : '-' }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Medical Status</div>
                <div class="sfpe-value">{{ filled($record->medical_status) ? \Illuminate\Support\Str::headline($record->medical_status) : '-' }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Visa Status</div>
                <div class="sfpe-value">{{ filled($record->visa_status) ? \Illuminate\Support\Str::headline($record->visa_status) : '-' }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Travel Status</div>
                <div class="sfpe-value">{{ filled($record->travel_status) ? \Illuminate\Support\Str::headline($record->travel_status) : '-' }}</div>
            </div>

            <div class="sfpe-item">
                <div class="sfpe-label">Converted To Employment</div>
                <div class="sfpe-value">{{ $convertedAt ?: 'Not Converted' }}</div>
            </div>
        </div>
    </section>

    <section class="sfpe-card">
        <div class="sfpe-card-head">
            <h2 class="sfpe-card-title">
                <span class="sfpe-icon">fact_check</span>
                Portal Answers
            </h2>
            <span class="sfpe-chip">{{ $portalAnswers->count() }} Answers</span>
        </div>

        @if($portalAnswers->isNotEmpty())
            <div class="sfpe-grid">
                @foreach($portalAnswers as $answer)
                    <div class="sfpe-item">
                        <div class="sfpe-label">{{ $answer['label'] }}</div>
                        <div class="sfpe-value">{{ $answer['value'] }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sfpe-empty">No portal text answers submitted yet.</div>
        @endif
    </section>

    
@php
    /*
     | Sada Fezzan ERP — Unified 

{{-- SF JOB APPLICATION SNAPSHOT + REQUESTS START --}}
@php
    /*
     | Pre-Employment must show what happened before this stage.
     | This block reads the linked Job Application and displays:
     | - Job Application Snapshot
     | - Candidate Requests created during Job Application
     | - Request timeline from the candidate request thread
     */

    $sfLinkedJobApplication = $record->jobApplication ?? null;

    $sfJobAppCandidateRequests = collect();
    $sfJobAppRequestTimeline = collect();

    if ($sfLinkedJobApplication) {
        try {
            $sfJobAppCandidateRequests = $sfLinkedJobApplication
                ->candidateRequests()
                ->with('items')
                ->latest()
                ->get();
        } catch (\Throwable $e) {
            $sfJobAppCandidateRequests = collect();
        }

        foreach ($sfJobAppCandidateRequests as $sfReq) {
            $decoded = json_decode((string) $sfReq->candidate_response, true);
            $decoded = is_array($decoded) ? $decoded : [];

            $thread = is_array($decoded['thread'] ?? null) ? $decoded['thread'] : [];

            if (empty($thread)) {
                $thread[] = [
                    'sender' => 'hr',
                    'event' => 'request_created',
                    'title' => $sfReq->title,
                    'message' => $sfReq->notes,
                    'salary' => $sfReq->proposed_salary ?? null,
                    'currency' => $sfReq->currency ?? null,
                    'created_at' => optional($sfReq->created_at)?->toDateTimeString(),
                ];
            }

            foreach ($thread as $entry) {
                if (! is_array($entry)) {
                    continue;
                }

                $date = $entry['created_at'] ?? optional($sfReq->updated_at)?->toDateTimeString() ?? optional($sfReq->created_at)?->toDateTimeString();

                $sfJobAppRequestTimeline->push([
                    'request_title' => $sfReq->title ?: 'Candidate Request',
                    'request_type' => $sfReq->type ?: 'request',
                    'request_status' => $sfReq->request_status ?: null,
                    'sender' => $entry['sender'] ?? 'system',
                    'event' => $entry['event'] ?? 'update',
                    'title' => $entry['title'] ?? $sfReq->title ?? 'Candidate Request',
                    'message' => $entry['message'] ?? null,
                    'salary' => $entry['salary'] ?? null,
                    'currency' => $entry['currency'] ?? null,
                    'date' => $date,
                ]);
            }

            $uploadedFiles = is_array($decoded['uploaded_files'] ?? null) ? $decoded['uploaded_files'] : [];

            foreach ($uploadedFiles as $uploadedFile) {
                $sfJobAppRequestTimeline->push([
                    'request_title' => $sfReq->title ?: 'Candidate Request',
                    'request_type' => $sfReq->type ?: 'request',
                    'request_status' => $sfReq->request_status ?: null,
                    'sender' => 'candidate',
                    'event' => 'file_uploaded',
                    'title' => $uploadedFile['item_label'] ?? $uploadedFile['label'] ?? $uploadedFile['original_name'] ?? 'Uploaded File',
                    'message' => $uploadedFile['original_name'] ?? null,
                    'salary' => null,
                    'currency' => null,
                    'date' => optional($sfReq->updated_at)?->toDateTimeString() ?? optional($sfReq->created_at)?->toDateTimeString(),
                ]);
            }
        }

        $sfJobAppRequestTimeline = $sfJobAppRequestTimeline
            ->filter(fn ($row) => filled($row['date']))
            ->sortByDesc(fn ($row) => strtotime((string) $row['date']))
            ->values();
    }

    $sfJobAppSnapshotItems = $sfLinkedJobApplication ? collect([
        'Full Name' => $sfLinkedJobApplication->full_name ?? $sfLinkedJobApplication->candidate_name ?? $sfLinkedJobApplication->name ?? '-',
        'Email' => $sfLinkedJobApplication->email ?? $sfLinkedJobApplication->candidate_email ?? '-',
        'Phone' => $sfLinkedJobApplication->phone ?? $sfLinkedJobApplication->phone_number ?? '-',
        'Status' => filled($sfLinkedJobApplication->status) ? ucfirst(str_replace('_', ' ', $sfLinkedJobApplication->status)) : '-',
        'Applied At' => optional($sfLinkedJobApplication->created_at)?->format('Y-m-d H:i') ?: '-',
        'Request Workflow' => filled($sfLinkedJobApplication->candidate_request_status) ? ucfirst(str_replace('_', ' ', $sfLinkedJobApplication->candidate_request_status)) : '-',
    ]) : collect();

    $sfReqIcon = function ($event, $sender = null) {
        $event = strtolower((string) $event);
        $sender = strtolower((string) $sender);

        if (str_contains($event, 'file')) {
            return 'description';
        }

        if (str_contains($event, 'approved') || str_contains($event, 'accepted')) {
            return 'check_circle';
        }

        if (str_contains($event, 'reconsidered') || str_contains($event, 'counter')) {
            return 'change_circle';
        }

        if (str_contains($event, 'final')) {
            return 'verified';
        }

        if ($sender === 'candidate') {
            return 'person';
        }

        return 'assignment';
    };
@endphp

@if($sfLinkedJobApplication)
    <section class="sfpe-ja-card">
        <div class="sfpe-ja-head">
            <div>
                <h2>
                    <span class="material-symbols-rounded">history_edu</span>
                    Job Application Snapshot
                </h2>
                <p>Summary of the original Job Application before this candidate moved to Pre-Employment.</p>
            </div>

            <span class="sfpe-ja-pill">Application #{{ $sfLinkedJobApplication->id }}</span>
        </div>

        <div class="sfpe-ja-grid">
            @foreach($sfJobAppSnapshotItems as $label => $value)
                <div class="sfpe-ja-item">
                    <div class="sfpe-ja-label">{{ $label }}</div>
                    <div class="sfpe-ja-value">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="sfpe-ja-card">
        <div class="sfpe-ja-head">
            <div>
                <h2>
                    <span class="material-symbols-rounded">assignment</span>
                    Candidate Requests
                </h2>
                <p>All candidate requests created during the Job Application stage.</p>
            </div>

            <span class="sfpe-ja-pill">{{ $sfJobAppCandidateRequests->count() }} Requests</span>
        </div>

        @if($sfJobAppCandidateRequests->isNotEmpty())
            <div class="sfpe-ja-request-list">
                @foreach($sfJobAppCandidateRequests as $sfReq)
                    @php
                        $decoded = json_decode((string) $sfReq->candidate_response, true);
                        $decoded = is_array($decoded) ? $decoded : [];

                        $uploadedFiles = is_array($decoded['uploaded_files'] ?? null) ? $decoded['uploaded_files'] : [];
                        $noteResponses = is_array($decoded['note_responses'] ?? null) ? $decoded['note_responses'] : [];
                        $thread = is_array($decoded['thread'] ?? null) ? $decoded['thread'] : [];

                        $portalUrl = filled($sfReq->public_token)
                            ? rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $sfReq->public_token
                            : null;
                    @endphp

                    <details class="sfpe-ja-request" open>
                        <summary>
                            <div>
                                <strong>{{ $sfReq->title ?: 'Candidate Request' }}</strong>
                                <div class="sfpe-ja-tags">
                                    <span>{{ ucfirst(str_replace('_', ' ', (string) $sfReq->type)) }}</span>
                                    <span>{{ ucfirst(str_replace('_', ' ', (string) $sfReq->request_status)) }}</span>
                                    @if($sfReq->due_date)
                                        <span>Due {{ optional($sfReq->due_date)->format('M j, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="material-symbols-rounded">expand_more</span>
                        </summary>

                        <div class="sfpe-ja-request-body">
                            @if($sfReq->items->count())
                                <div class="sfpe-ja-grid compact">
                                    @foreach($sfReq->items as $item)
                                        <div class="sfpe-ja-item">
                                            <div class="sfpe-ja-label">{{ ($item->item_type ?? 'file') === 'note' ? 'Requested Note' : 'Requested File' }}</div>
                                            <div class="sfpe-ja-value">{{ $item->label ?: '-' }}</div>
                                            <div class="sfpe-ja-muted">
                                                {{ $item->file_format ? ucfirst(str_replace('_', ' ', $item->file_format)) : '' }}
                                                {{ $item->is_required ? 'Required' : 'Optional' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($uploadedFiles))
                                <div class="sfpe-ja-grid compact">
                                    @foreach($uploadedFiles as $file)
                                        @php
                                            $filePath = $file['stored_path'] ?? $file['stored_path'] ?? $file['stored_path'] ?? $file['path'] ?? $file['file_path'] ?? null;
                                            $fileUrl = $filePath ? \Illuminate\Support\Facades\Storage::url($filePath) : null;
                                            $fileTitle = $file['item_label'] ?? $file['label'] ?? $file['original_name'] ?? 'Uploaded File';
                                        @endphp

                                        <div class="sfpe-ja-file">
                                            <div class="sfpe-ja-label">Candidate Uploaded File</div>
                                            <div class="sfpe-ja-value">{{ $fileTitle }}</div>
                                            @if(!empty($file['original_name']))
                                                <div class="sfpe-ja-muted">{{ $file['original_name'] }}</div>
                                            @endif

                                            @if($fileUrl)
                                                <a href="{{ $fileUrl }}" target="_blank" class="sfpe-ja-btn">Open File</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($noteResponses))
                                <div class="sfpe-ja-grid compact">
                                    @foreach($noteResponses as $note)
                                        <div class="sfpe-ja-item">
                                            <div class="sfpe-ja-label">{{ $note['label'] ?? 'Candidate Note' }}</div>
                                            <div class="sfpe-ja-value">{{ $note['value'] ?? $note['response'] ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($portalUrl)
                                <div class="sfpe-ja-actions">
                                    <a href="{{ $portalUrl }}" target="_blank" class="sfpe-ja-btn">Open Request Portal</a>
                                </div>
                            @endif
                        </div>
                    </details>
                @endforeach
            </div>
        @else
            <div class="sfpe-ja-empty">No Job Application candidate requests found.</div>
        @endif
    </section>

    <section class="sfpe-ja-card sfpe-ja-timeline-print">
        <div class="sfpe-ja-head">
            <div>
                <h2>
                    <span class="material-symbols-rounded">timeline</span>
                    Candidate Request Timeline
                </h2>
                <p>Timeline of request creation, candidate replies, salary negotiation, decisions, and uploaded request files.</p>
            </div>

            <span class="sfpe-ja-pill">{{ $sfJobAppRequestTimeline->count() }} Updates</span>
        </div>

        @if($sfJobAppRequestTimeline->isNotEmpty())
            <div class="sfpe-ja-timeline">
                @foreach($sfJobAppRequestTimeline as $row)
                    <div class="sfpe-ja-timeline-row">
                        <div class="sfpe-ja-timeline-icon">
                            <span class="material-symbols-rounded">{{ $sfReqIcon($row['event'] ?? '', $row['sender'] ?? '') }}</span>
                        </div>

                        <div class="sfpe-ja-timeline-card">
                            <div class="sfpe-ja-timeline-top">
                                <strong>{{ ucfirst($row['sender'] ?? 'System') }} · {{ ucfirst(str_replace('_', ' ', $row['event'] ?? 'Update')) }}</strong>
                                <span>{{ \Carbon\Carbon::parse($row['date'])->format('Y-m-d H:i') }}</span>
                            </div>

                            <div class="sfpe-ja-timeline-title">{{ $row['request_title'] ?? 'Candidate Request' }}</div>

                            @if(!empty($row['title']))
                                <div class="sfpe-ja-timeline-message">{{ $row['title'] }}</div>
                            @endif

                            @if(!empty($row['message']))
                                <div class="sfpe-ja-timeline-message">{{ $row['message'] }}</div>
                            @endif

                            @if(!empty($row['salary']))
                                <div class="sfpe-ja-tags">
                                    <span>{{ $row['salary'] }} {{ $row['currency'] ?? '' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sfpe-ja-empty">No Job Application request timeline updates found.</div>
        @endif
    </section>
@endif
{{-- SF JOB APPLICATION SNAPSHOT + REQUESTS END --}}
<section class="pe-card sfpe-candidate-files-card">
    <div class="pe-card-header">
        <div class="pe-card-title">
            <span class="material-symbols-rounded">folder_open</span>
            Candidate Files
        </div>

        @php
            /*
             | Safety bridge for Candidate Files block.
             | Some previous patches used $sfpeCandidateFiles before defining it.
             | This keeps the page alive and reuses any candidate-file collection already built above.
             */
            if (! isset($sfpeCandidateFiles)) {
                $sfpeCandidateFiles = collect();

                if (isset($sfCandidateFiles) && $sfCandidateFiles instanceof \Illuminate\Support\Collection) {
                    $sfpeCandidateFiles = $sfCandidateFiles;
                } elseif (isset($candidateFiles) && $candidateFiles instanceof \Illuminate\Support\Collection) {
                    $sfpeCandidateFiles = $candidateFiles;
                } elseif (isset($files) && $files instanceof \Illuminate\Support\Collection) {
                    $sfpeCandidateFiles = $files;
                }
            }
        @endphp

        <span class="sfpe-count-pill">{{ $sfpeCandidateFiles->count() }} Files</span>
    </div>

    @if(($sfpeCandidateFiles ?? collect())->isNotEmpty())
        <div class="sfpe-candidate-files-grid">
            @foreach(($sfpeCandidateFiles ?? collect()) as $file)
                <div class="sfpe-candidate-file-item">
                    <div class="sfpe-file-category">{{ $file['category'] ?? 'Candidate File' }}</div>
                    <div class="sfpe-file-title">{{ $file['title'] ?? 'Candidate File' }}</div>
                    <div class="sfpe-file-source">{{ $file['source'] ?? 'Candidate File' }}</div>

                    @if(!empty($file['url']))
                        <a class="sfpe-file-open-btn" href="{{ $file['url'] }}" target="_blank">
                            Open File
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="sfpe-empty-line">No candidate files found yet.</div>
    @endif
</section>

<style id="sfpe-candidate-files-final-style">
    .sfpe-candidate-files-card {
        width: min(100%, 1280px);
        margin-left: auto;
        margin-right: auto;
    }

    .sfpe-count-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0 14px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0f172a;
        border: 1px solid #bfdbfe;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sfpe-candidate-files-grid {
        padding: 24px 26px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .sfpe-candidate-file-item {
        min-height: 174px;
        border-radius: 24px;
        padding: 20px;
        background: rgba(248, 250, 252, .9);
        border: 1px solid rgba(15, 23, 42, .08);
        box-shadow: 0 12px 28px rgba(15, 23, 42, .04);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .sfpe-file-category {
        color: #64748b;
        font-size: 11px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .sfpe-file-title {
        color: #0f172a;
        font-size: 17px;
        line-height: 1.35;
        font-weight: 950;
        overflow-wrap: anywhere;
    }

    .sfpe-file-source {
        margin-top: 14px;
        color: #475569;
        font-size: 13px;
        font-weight: 850;
    }

    .sfpe-file-open-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        margin-top: 18px;
        min-height: 40px;
        padding: 0 16px;
        border-radius: 999px;
        background: linear-gradient(135deg, #e0f2fe, #dbeafe);
        color: #1d4ed8 !important;
        text-decoration: none !important;
        border: 1px solid rgba(37, 99, 235, .14);
        font-size: 12px;
        font-weight: 950;
    }

    .dark .sfpe-candidate-file-item {
        background: rgba(15, 23, 42, .58);
        border-color: rgba(148, 163, 184, .16);
    }

    .dark .sfpe-file-title {
        color: #ffffff;
    }

    .dark .sfpe-file-source {
        color: #cbd5e1;
    }

    @media (max-width: 1100px) {
        .sfpe-candidate-files-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .sfpe-candidate-files-grid {
            grid-template-columns: 1fr;
            padding: 18px;
        }
    }
</style>


    <section class="sfpe-card">
        <div class="sfpe-card-head">
            <h2 class="sfpe-card-title">
                <span class="sfpe-icon">assignment</span>
                Portal File Requests
            </h2>
            <span class="sfpe-chip">{{ $portalFileRequests->count() }} Requests</span>
        </div>

        @if($portalFileRequests->isNotEmpty())
            <div class="sfpe-request-list">
                @foreach($portalFileRequests as $field)
                    @php
                        $matchingFiles = $files->filter(function ($file) use ($field) {
                            $title = strtolower($file['title'] ?? '');
                            $category = strtolower($file['category'] ?? '');
                            $label = strtolower($field->label ?? '');
                            $docCategory = strtolower($field->document_category ?? '');

                            return ($label && str_contains($title, $label))
                                || ($docCategory && str_contains($category, str_replace('_', ' ', $docCategory)))
                                || ($docCategory && str_contains($title, str_replace('_', ' ', $docCategory)));
                        });
                    @endphp

                    <div class="sfpe-request">
                        <div class="sfpe-request-title">{{ $field->label ?: 'Requested File' }}</div>

                        <div class="sfpe-request-meta">
                            <span class="sfpe-chip">{{ filled($field->document_category) ? \Illuminate\Support\Str::headline($field->document_category) : 'File' }}</span>
                            <span class="sfpe-chip">{{ filled($field->request_type) ? \Illuminate\Support\Str::headline($field->request_type) : 'Upload' }}</span>
                            <span class="sfpe-chip {{ $field->is_required ? 'warning' : '' }}">{{ $field->is_required ? 'Required' : 'Optional' }}</span>
                            <span class="sfpe-chip {{ $matchingFiles->isNotEmpty() ? 'success' : 'warning' }}">{{ $matchingFiles->isNotEmpty() ? 'Uploaded' : 'Pending' }}</span>
                        </div>

                        @if(filled($field->instructions))
                            <div class="sfpe-notes">{{ $field->instructions }}</div>
                        @endif

                        @if(filled($field->document_to_sign_path))
                            <a class="sfpe-file-link" href="{{ \Illuminate\Support\Facades\Storage::url($field->document_to_sign_path) }}" target="_blank">Open Document To Sign</a>
                        @endif

                        @if($matchingFiles->isNotEmpty())
                            <div class="sfpe-grid" style="padding:14px 0 0;grid-template-columns:repeat(2,minmax(0,1fr));">
                                @foreach($matchingFiles as $file)
                                    <div class="sfpe-item">
                                        <div class="sfpe-label">Uploaded File</div>
                                        <div class="sfpe-value">{{ $file['title'] }}</div>
                                        <a class="sfpe-file-link" href="{{ $file['url'] }}" target="_blank">Open File</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="sfpe-empty">No portal file requests created yet.</div>
        @endif
    </section>

    <section class="sfpe-card">
        <div class="sfpe-card-head">
            <h2 class="sfpe-card-title">
                <span class="sfpe-icon">payments</span>
                Finance Profile
            </h2>
            <span class="sfpe-chip">{{ $financeProfile ? 'Ready' : 'Missing' }}</span>
        </div>

        @if($financeProfile)
            <div class="sfpe-grid">
                <div class="sfpe-item">
                    <div class="sfpe-label">Salary Basis</div>
                    <div class="sfpe-value">{{ filled($financeProfile->salary_basis) ? \Illuminate\Support\Str::headline($financeProfile->salary_basis) : '-' }}</div>
                </div>

                <div class="sfpe-item">
                    <div class="sfpe-label">Daily Rate</div>
                    <div class="sfpe-value">{{ $financeProfile->daily_rate ?? '-' }} {{ $financeProfile->payout_currency ?? '' }}</div>
                </div>

                <div class="sfpe-item">
                    <div class="sfpe-label">Monthly Salary</div>
                    <div class="sfpe-value">{{ $financeProfile->monthly_salary ?? '-' }} {{ $financeProfile->payout_currency ?? '' }}</div>
                </div>

                <div class="sfpe-item">
                    <div class="sfpe-label">Client Billing Basis</div>
                    <div class="sfpe-value">{{ filled($financeProfile->client_billing_basis) ? \Illuminate\Support\Str::headline($financeProfile->client_billing_basis) : '-' }}</div>
                </div>

                <div class="sfpe-item">
                    <div class="sfpe-label">Client Billing Rate</div>
                    <div class="sfpe-value">{{ $financeProfile->client_billing_rate ?? '-' }} {{ $financeProfile->client_billing_currency ?? '' }}</div>
                </div>

                <div class="sfpe-item">
                    <div class="sfpe-label">Effective From</div>
                    <div class="sfpe-value">{{ optional($financeProfile->effective_from)->format('Y-m-d') ?: '-' }}</div>
                </div>
            </div>
        @else
            <div class="sfpe-empty">Final finance profile is not completed yet.</div>
        @endif
    </section>

    <section class="sfpe-card">
        <div class="sfpe-card-head">
            <h2 class="sfpe-card-title">
                <span class="sfpe-icon">receipt_long</span>
                Pre-Employment Expenses
            </h2>
            <span class="sfpe-chip">{{ $expenses->count() }} Expenses</span>
        </div>

        @if($expenses->isNotEmpty())
            <div class="sfpe-grid">
                @foreach($expenses as $expense)
                    <div class="sfpe-item">
                        <div class="sfpe-label">{{ filled($expense->category) ? \Illuminate\Support\Str::headline($expense->category) : 'Expense' }}</div>
                        <div class="sfpe-value">{{ $expense->title ?: 'Pre-Employment Expense' }}</div>
                        <div class="sfpe-notes">
                            {{ $expense->amount ?? '0' }} {{ $expense->currency ?? '' }}
                            <br>
                            {{ optional($expense->expense_date)->format('Y-m-d') ?: '-' }}
                            <br>
                            {{ filled($expense->status) ? \Illuminate\Support\Str::headline($expense->status) : '-' }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sfpe-empty">No Pre-Employment expenses recorded yet.</div>
        @endif
    </section>

    <section class="sfpe-card sfpe-timeline-card">
        <div class="sfpe-card-head">
            <div>
                <h2 class="sfpe-card-title">
                    <span class="sfpe-icon">timeline</span>
                    Complete Pre-Employment Timeline
                </h2>
                <div class="sfpe-notes" style="margin-top:8px;">
                    Shows latest updates first. Scroll inside the block to review older history.
                </div>
            </div>

            <button type="button" class="sfpe-action sfpe-action-blue" onclick="window.print();">
                Print Timeline
            </button>
        </div>

        @if($timelineRows->isNotEmpty())
            <div class="sfpe-timeline-list">
                @foreach($timelineRows as $row)
                    <div class="sfpe-timeline-row">
                        <div class="sfpe-timeline-marker">
                            <div class="sfpe-timeline-icon">{{ $timelineIcon($row['type'] ?? 'update') }}</div>
                        </div>

                        <div class="sfpe-timeline-body">
                            <div class="sfpe-timeline-top">
                                <div class="sfpe-timeline-title">{{ $row['title'] }}</div>
                                <div class="sfpe-timeline-date">{{ $row['date']->format('Y-m-d H:i') }}</div>
                            </div>

                            @if(!empty($row['subtitle']))
                                <div class="sfpe-timeline-subtitle">{{ $row['subtitle'] }}</div>
                            @endif

                            @if(!empty($row['meta']))
                                <div class="sfpe-tags">
                                    @foreach($row['meta'] as $tag)
                                        <span class="sfpe-chip">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sfpe-empty">No timeline updates found yet.</div>
        @endif
    </section>
</div>


<style id="sfpe-job-app-snapshot-requests-style">
    .sfpe-ja-card {
        width: min(100%, 1280px);
        margin: 24px auto;
        border-radius: 30px;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, .08);
        box-shadow: 0 18px 46px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .dark .sfpe-ja-card {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 36%),
            rgba(15, 23, 42, .84);
        border-color: rgba(148, 163, 184, .18);
        box-shadow: 0 18px 46px rgba(0, 0, 0, .24);
    }

    .sfpe-ja-head {
        padding: 22px 26px;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .dark .sfpe-ja-head {
        border-bottom-color: rgba(148, 163, 184, .18);
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 32%),
            rgba(15, 23, 42, .44);
    }

    .sfpe-ja-head h2 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #0f172a;
        font-size: 22px;
        line-height: 1.2;
        letter-spacing: -.04em;
        font-weight: 950;
    }

    .dark .sfpe-ja-head h2 {
        color: #ffffff;
    }

    .sfpe-ja-head h2 .material-symbols-rounded {
        color: #1d4ed8;
        font-size: 25px;
    }

    .dark .sfpe-ja-head h2 .material-symbols-rounded {
        color: #22d3ee;
    }

    .sfpe-ja-head p {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 14px;
        font-weight: 750;
        line-height: 1.55;
    }

    .dark .sfpe-ja-head p {
        color: #94a3b8;
    }

    .sfpe-ja-pill,
    .sfpe-ja-tags span {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 950;
        color: #0f172a;
        background: rgba(224, 242, 254, .88);
        border: 1px solid rgba(37, 99, 235, .16);
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
    }

    .dark .sfpe-ja-pill,
    .dark .sfpe-ja-tags span {
        color: #bfdbfe;
        background: rgba(37, 99, 235, .18);
        border-color: rgba(147, 197, 253, .18);
    }

    .sfpe-ja-grid {
        padding: 24px 26px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .sfpe-ja-grid.compact {
        padding: 0;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sfpe-ja-item,
    .sfpe-ja-file {
        min-height: 104px;
        border-radius: 22px;
        padding: 18px;
        background: rgba(248, 250, 252, .82);
        border: 1px solid rgba(15, 23, 42, .08);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .dark .sfpe-ja-item,
    .dark .sfpe-ja-file {
        background: rgba(15, 23, 42, .58);
        border-color: rgba(148, 163, 184, .16);
    }

    .sfpe-ja-label {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sfpe-ja-value {
        color: #0f172a;
        font-size: 16px;
        line-height: 1.45;
        font-weight: 850;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .sfpe-ja-muted {
        margin-top: 8px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.4;
        font-weight: 750;
    }

    .dark .sfpe-ja-value {
        color: #ffffff;
    }

    .dark .sfpe-ja-label,
    .dark .sfpe-ja-muted {
        color: #94a3b8;
    }

    .sfpe-ja-request-list {
        padding: 24px 26px;
        display: grid;
        gap: 14px;
    }

    .sfpe-ja-request {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 24px;
        overflow: hidden;
        background: rgba(255, 255, 255, .96);
    }

    .dark .sfpe-ja-request {
        background: rgba(15, 23, 42, .58);
        border-color: rgba(148, 163, 184, .16);
    }

    .sfpe-ja-request summary {
        cursor: pointer;
        list-style: none;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 36%),
            rgba(248, 250, 252, .9);
    }

    .sfpe-ja-request summary::-webkit-details-marker {
        display: none;
    }

    .sfpe-ja-request summary strong {
        color: #0f172a;
        font-size: 18px;
        font-weight: 950;
    }

    .dark .sfpe-ja-request summary strong {
        color: #ffffff;
    }

    .sfpe-ja-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .sfpe-ja-request-body {
        padding: 20px;
        display: grid;
        gap: 14px;
        border-top: 1px solid rgba(15, 23, 42, .08);
    }

    .sfpe-ja-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        margin-top: 12px;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        background: linear-gradient(135deg, #e0f2fe, #dbeafe);
        color: #1d4ed8 !important;
        text-decoration: none !important;
        border: 1px solid rgba(37, 99, 235, .14);
        font-size: 12px;
        font-weight: 950;
    }

    .sfpe-ja-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sfpe-ja-empty {
        margin: 24px 26px;
        padding: 20px;
        border-radius: 22px;
        border: 1px dashed rgba(15, 23, 42, .18);
        background: rgba(248, 250, 252, .68);
        color: #64748b;
        font-weight: 800;
    }

    .sfpe-ja-timeline {
        padding: 24px 26px;
        display: grid;
        gap: 16px;
        position: relative;
    }

    .sfpe-ja-timeline-row {
        display: grid;
        grid-template-columns: 54px 1fr;
        gap: 16px;
        position: relative;
    }

    .sfpe-ja-timeline-row:not(:last-child)::before {
        content: "";
        position: absolute;
        left: 26px;
        top: 54px;
        bottom: -16px;
        width: 3px;
        border-radius: 999px;
        background: #dbeafe;
    }

    .sfpe-ja-timeline-icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        color: #1d4ed8;
        background: #e0f2fe;
        border: 1px solid rgba(37, 99, 235, .16);
        position: relative;
        z-index: 2;
    }

    .sfpe-ja-timeline-card {
        border-radius: 22px;
        padding: 18px;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, .08);
        box-shadow: 0 10px 28px rgba(15, 23, 42, .05);
    }

    .dark .sfpe-ja-timeline-card {
        background: rgba(15, 23, 42, .60);
        border-color: rgba(148, 163, 184, .16);
    }

    .sfpe-ja-timeline-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        color: #0f172a;
        font-size: 15px;
        font-weight: 950;
    }

    .sfpe-ja-timeline-top span {
        color: #64748b;
        font-size: 13px;
        font-weight: 850;
    }

    .sfpe-ja-timeline-title {
        margin-top: 10px;
        color: #1e293b;
        font-size: 16px;
        font-weight: 900;
    }

    .sfpe-ja-timeline-message {
        margin-top: 8px;
        color: #475569;
        font-size: 14px;
        font-weight: 750;
        line-height: 1.6;
    }

    .dark .sfpe-ja-timeline-top,
    .dark .sfpe-ja-timeline-title {
        color: #ffffff;
    }

    .dark .sfpe-ja-timeline-message,
    .dark .sfpe-ja-timeline-top span {
        color: #94a3b8;
    }

    @media (max-width: 900px) {
        .sfpe-ja-grid,
        .sfpe-ja-grid.compact {
            grid-template-columns: 1fr;
        }
    }
</style>


<style id="sfpe-clean-requests-files-final-fix">
    /*
     | Pre-Employment cleanup:
     | - Candidate request timeline scrollable
     | - Candidate files grid clean
     | - Keeps Job Application request history visible without stretching the page
     */
    .sfpe-job-application-requests,
    .sfpe-candidate-requests,
    .sfpe-request-history,
    .sfpe-thread-timeline,
    .sfpe-job-application-request-timeline {
        max-height: 520px !important;
        overflow-y: auto !important;
        padding-right: 8px !important;
        scrollbar-width: thin;
    }

    .sfpe-job-application-requests::-webkit-scrollbar,
    .sfpe-candidate-requests::-webkit-scrollbar,
    .sfpe-request-history::-webkit-scrollbar,
    .sfpe-thread-timeline::-webkit-scrollbar,
    .sfpe-job-application-request-timeline::-webkit-scrollbar {
        width: 8px;
    }

    .sfpe-job-application-requests::-webkit-scrollbar-thumb,
    .sfpe-candidate-requests::-webkit-scrollbar-thumb,
    .sfpe-request-history::-webkit-scrollbar-thumb,
    .sfpe-thread-timeline::-webkit-scrollbar-thumb,
    .sfpe-job-application-request-timeline::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(37, 99, 235, .22);
    }

    .sfpe-candidate-files-grid,
    .sfpe-files-grid {
        align-items: stretch !important;
    }

    .sfpe-candidate-file-item,
    .sfpe-file-card,
    .sfpe-file-item {
        min-height: 150px !important;
    }
</style>


<style id="sfpe-clean-flow-final-style">
    /*
     | Sada Fezzan ERP — Pre-Employment Clean Flow
     | Final visual/order cleanup for the profile page.
     */

    .sfpe-clean-hidden {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        height: 0 !important;
        min-height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
    }

    .sfpe-clean-scroll {
        max-height: 620px !important;
        overflow-y: auto !important;
        padding-right: 10px !important;
        scrollbar-width: thin;
    }

    .sfpe-clean-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .sfpe-clean-scroll::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(37, 99, 235, .25);
    }

    .sfpe-clean-flow-add-expense {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0 16px;
        border-radius: 999px;
        border: 0;
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #ffffff !important;
        font-size: 12px;
        font-weight: 950;
        text-decoration: none !important;
        box-shadow: 0 12px 26px rgba(22, 163, 74, .22);
        cursor: pointer;
        white-space: nowrap;
    }

    .sfpe-clean-flow-files-card {
        width: min(100%, 1280px);
        margin: 22px auto;
        border-radius: 30px;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, .08);
        box-shadow: 0 18px 46px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .dark .sfpe-clean-flow-files-card {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 36%),
            rgba(15, 23, 42, .82);
        border-color: rgba(148, 163, 184, .18);
    }

    .sfpe-clean-flow-files-head {
        padding: 22px 26px;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .sfpe-clean-flow-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: #0f172a;
        font-size: 20px;
        line-height: 1.2;
        letter-spacing: -.04em;
        font-weight: 950;
    }

    .dark .sfpe-clean-flow-title {
        color: #ffffff;
    }

    .sfpe-clean-flow-icon {
        font-family: 'Material Symbols Rounded';
        font-weight: 600;
        font-size: 24px;
        line-height: 1;
        color: #1d4ed8;
    }

    .sfpe-clean-flow-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 8px 13px;
        color: #0f172a;
        background: rgba(224, 242, 254, .88);
        border: 1px solid rgba(37, 99, 235, .16);
        font-size: 12px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .sfpe-clean-flow-files-grid {
        padding: 24px 26px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .sfpe-clean-flow-file {
        min-height: 150px;
        border-radius: 22px;
        padding: 18px;
        background: rgba(248, 250, 252, .82);
        border: 1px solid rgba(15, 23, 42, .08);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .sfpe-clean-flow-label {
        margin-bottom: 10px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sfpe-clean-flow-value {
        color: #0f172a;
        font-size: 16px;
        line-height: 1.45;
        font-weight: 900;
        overflow-wrap: anywhere;
    }

    .sfpe-clean-flow-source {
        margin-top: 10px;
        color: #475569;
        font-size: 13px;
        font-weight: 800;
    }

    .sfpe-clean-flow-open {
        width: fit-content;
        margin-top: 14px;
        padding: 10px 14px;
        border-radius: 999px;
        background: linear-gradient(135deg, #e0f2fe, #dbeafe);
        color: #1d4ed8 !important;
        border: 1px solid rgba(37, 99, 235, .14);
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 950;
    }

    .sfpe-clean-flow-empty {
        margin: 24px 26px;
        padding: 20px;
        border-radius: 22px;
        border: 1px dashed rgba(15, 23, 42, .18);
        background: rgba(248, 250, 252, .68);
        color: #64748b;
        font-weight: 850;
    }

    @media (max-width: 1100px) {
        .sfpe-clean-flow-files-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .sfpe-clean-flow-files-grid {
            grid-template-columns: 1fr;
            padding: 18px;
        }

        .sfpe-clean-flow-files-head {
            padding: 18px;
        }
    }
</style>

@php
    /*
     | Candidate Files unified collection.
     | Pulls files from:
     | - Job Application direct CV/path columns
     | - Job Application applicationFilePayloads()
     | - Job Application custom file answers
     | - Job Application candidate request uploaded files
     | - Pre-Employment ERP uploaded files
     | - Pre-Employment portal uploaded values
     */
    $sfpeUnifiedFiles = collect();
    $sfpeUnifiedSeen = [];

    $sfpeCleanPath = function ($path) {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#^storage/#', '', $path);
        $path = preg_replace('#^public/#', '', $path);
        $path = preg_replace('#^app/public/#', '', $path);

        return $path;
    };

    $sfpePrettyTitle = function ($title, $path = null, $candidateName = null) {
        $raw = trim((string) $title);

        if ($raw === '' && filled($path)) {
            $raw = basename((string) $path);
        }

        $lower = strtolower($raw);

        if (
            $raw === '' ||
            in_array($lower, ['cv_path', 'cv file', 'cv_file', 'resume_path', 'resume path', 'file_path', 'attachment_path', 'uploaded file'], true) ||
            str_contains($lower, 'cv_path')
        ) {
            return filled($candidateName)
                ? 'Candidate CV — ' . $candidateName
                : 'Candidate CV';
        }

        return str_replace(['_', '-'], ' ', $raw);
    };

    $sfpeAddUnifiedFile = function ($title, $category, $path, $source = 'Candidate File') use (&$sfpeUnifiedFiles, &$sfpeUnifiedSeen, $sfpeCleanPath, $sfpePrettyTitle, $record) {
        $cleanPath = $sfpeCleanPath($path);

        if (! filled($cleanPath)) {
            return;
        }

        $key = strtolower($cleanPath);

        if (isset($sfpeUnifiedSeen[$key])) {
            return;
        }

        $sfpeUnifiedSeen[$key] = true;

        $sfpeUnifiedFiles->push([
            'title' => $sfpePrettyTitle($title, $cleanPath, $record->candidate_name ?? $record->jobApplication?->full_name ?? null),
            'category' => filled($category) ? $category : 'Candidate File',
            'source' => filled($source) ? $source : 'Candidate File',
            'path' => $cleanPath,
            'url' => \Illuminate\Support\Facades\Storage::url($cleanPath),
        ]);
    };

    $sfpeJobApplication = $record->jobApplication ?? null;

    if ($sfpeJobApplication) {
        foreach (['cv_path', 'cv_file', 'resume_path', 'file_path', 'attachment_path'] as $fileColumn) {
            if (filled($sfpeJobApplication->{$fileColumn} ?? null)) {
                $sfpeAddUnifiedFile($fileColumn, 'CV', $sfpeJobApplication->{$fileColumn}, 'Job Application');
            }
        }

        try {
            if (method_exists($sfpeJobApplication, 'applicationFilePayloads')) {
                foreach (collect($sfpeJobApplication->applicationFilePayloads()) as $payload) {
                    $payloadPath = $payload['stored_path'] ?? $payload['path'] ?? $payload['file_path'] ?? null;

                    $sfpeAddUnifiedFile(
                        $payload['title'] ?? $payload['label'] ?? $payload['name'] ?? $payload['original_name'] ?? 'Application File',
                        $payload['category'] ?? $payload['type'] ?? 'Job Application File',
                        $payloadPath,
                        'Job Application'
                    );
                }
            }
        } catch (\Throwable $e) {
            //
        }

        try {
            foreach (collect($sfpeJobApplication->values ?? []) as $value) {
                $field = $value->field ?? null;

                $label = $field?->label
                    ?? $field?->field_label
                    ?? $field?->name
                    ?? 'Application File';

                $answer = $value->value
                    ?? $value->answer
                    ?? $value->field_value
                    ?? null;

                $answers = is_array($answer) ? $answer : [$answer];

                foreach ($answers as $singleAnswer) {
                    $answerText = trim((string) $singleAnswer);

                    if (
                        $answerText !== '' &&
                        (
                            str_contains(strtolower($answerText), '/') ||
                            str_contains(strtolower($answerText), '.pdf') ||
                            str_contains(strtolower($answerText), '.jpg') ||
                            str_contains(strtolower($answerText), '.jpeg') ||
                            str_contains(strtolower($answerText), '.png') ||
                            str_contains(strtolower($answerText), '.doc') ||
                            str_contains(strtolower($answerText), '.rtf')
                        )
                    ) {
                        $sfpeAddUnifiedFile($label, 'Application Answer File', $answerText, 'Job Application');
                    }
                }
            }
        } catch (\Throwable $e) {
            //
        }

        try {
            foreach (collect($sfpeJobApplication->candidateRequests ?? []) as $request) {
                $decoded = json_decode((string) ($request->candidate_response ?? ''), true);
                $decoded = is_array($decoded) ? $decoded : [];

                foreach (($decoded['uploaded_files'] ?? []) as $requestFile) {
                    if (! is_array($requestFile)) {
                        continue;
                    }

                    $requestPath = $requestFile['stored_path']
                        ?? $requestFile['path']
                        ?? $requestFile['file_path']
                        ?? null;

                    $sfpeAddUnifiedFile(
                        $requestFile['item_label']
                            ?? $requestFile['label']
                            ?? $requestFile['original_name']
                            ?? $requestFile['name']
                            ?? $request->title
                            ?? 'Candidate Request File',
                        'Candidate Request: ' . ($request->title ?? 'Request'),
                        $requestPath,
                        'Job Application Request'
                    );
                }
            }
        } catch (\Throwable $e) {
            //
        }
    }

    try {
        foreach (collect($record->files ?? []) as $file) {
            $sfpeAddUnifiedFile(
                $file->title ?? $file->label ?? $file->original_name ?? 'Pre-Employment File',
                $file->category ?? 'Pre-Employment File',
                $file->file_path ?? $file->path ?? $file->stored_path ?? null,
                ($file->uploaded_by_type ?? null) === 'candidate' ? 'Candidate Portal' : 'Pre-Employment ERP'
            );
        }
    } catch (\Throwable $e) {
        //
    }

    try {
        foreach (collect($record->portalValues ?? []) as $value) {
            $field = $value->field ?? null;

            $label = $field?->label
                ?? $value->label
                ?? 'Portal File';

            $rawValue = $value->value
                ?? $value->file_path
                ?? $value->path
                ?? null;

            $valueList = is_array($rawValue) ? $rawValue : [$rawValue];

            foreach ($valueList as $oneValue) {
                if (is_array($oneValue)) {
                    $portalPath = $oneValue['stored_path']
                        ?? $oneValue['path']
                        ?? $oneValue['file_path']
                        ?? $oneValue['value']
                        ?? null;

                    $portalTitle = $oneValue['original_name']
                        ?? $oneValue['name']
                        ?? $oneValue['title']
                        ?? $label;
                } else {
                    $portalPath = $oneValue;
                    $portalTitle = $label;
                }

                $sfpeAddUnifiedFile($portalTitle, $label, $portalPath, 'Pre-Employment Portal');
            }
        }
    } catch (\Throwable $e) {
        //
    }
@endphp

<section class="sfpe-clean-flow-files-card" data-sfpe-clean-candidate-files="1">
    <div class="sfpe-clean-flow-files-head">
        <h2 class="sfpe-clean-flow-title">
            <span class="sfpe-clean-flow-icon">folder_open</span>
            Candidate Files
        </h2>

        <span class="sfpe-clean-flow-pill">{{ $sfpeUnifiedFiles->count() }} Files</span>
    </div>

    @if($sfpeUnifiedFiles->isNotEmpty())
        <div class="sfpe-clean-flow-files-grid">
            @foreach($sfpeUnifiedFiles as $file)
                <div class="sfpe-clean-flow-file">
                    <div class="sfpe-clean-flow-label">{{ $file['category'] ?? 'Candidate File' }}</div>
                    <div class="sfpe-clean-flow-value">{{ $file['title'] ?? 'Candidate File' }}</div>
                    <div class="sfpe-clean-flow-source">{{ $file['source'] ?? 'Candidate File' }}</div>

                    @if(! empty($file['url']))
                        <a class="sfpe-clean-flow-open" href="{{ $file['url'] }}" target="_blank">
                            Open File
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="sfpe-clean-flow-empty">No candidate files found yet.</div>
    @endif
</section>

<script id="sfpe-clean-flow-final-script">
    (() => {
        const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim().toLowerCase();

        const closestBlock = (element) => {
            return element?.closest?.('section, .pe-card, .sfpe-card, .sfpe-panel, .sfja-md-card, div[class*="card"], div[class*="panel"]');
        };

        const findBlockByText = (needles) => {
            const blocks = Array.from(document.querySelectorAll('section, .pe-card, .sfpe-card, .sfpe-panel, .sfja-md-card, div[class*="card"], div[class*="panel"]'));

            return blocks.find((block) => {
                const text = normalize(block.innerText || block.textContent || '');
                return needles.every((needle) => text.includes(normalize(needle)));
            });
        };

        const hideByNeedles = (needles) => {
            const block = findBlockByText(needles);
            if (block) block.classList.add('sfpe-clean-hidden');
        };

        const renameByNeedles = (needles, newTitle) => {
            const block = findBlockByText(needles);
            if (! block) return;

            const title = Array.from(block.querySelectorAll('h1,h2,h3,.pe-card-title,.sfpe-panel-title,.sfja-md-title'))
                .find((el) => normalize(el.innerText || el.textContent || '').includes(normalize(needles[0])));

            if (title) title.innerHTML = title.innerHTML.replace(title.textContent.trim(), newTitle);
        };

        const moveAfter = (movingBlock, targetBlock) => {
            if (movingBlock && targetBlock && movingBlock !== targetBlock) {
                targetBlock.insertAdjacentElement('afterend', movingBlock);
            }
        };

        const addExpenseButton = () => {
            const expenseBlock = findBlockByText(['Pre-Employment Expenses']);
            if (! expenseBlock || expenseBlock.querySelector('[data-sfpe-add-expense-clean="1"]')) return;

            const header = expenseBlock.querySelector('.pe-card-header, .sfpe-panel-header, .sfja-md-card-head, header, .pe-card-head') || expenseBlock.firstElementChild;
            if (! header) return;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'sfpe-clean-flow-add-expense';
            btn.setAttribute('wire:click', "mountAction('addExpense')");
            btn.setAttribute('data-sfpe-add-expense-clean', '1');
            btn.textContent = 'Add Expense';

            header.appendChild(btn);
        };

        const apply = () => {
            // Remove old/unwanted blocks.
            hideByNeedles(['Portal Answers']);
            hideByNeedles(['Portal File Requests']);
            hideByNeedles(['Complete Pre-Employment Timeline']);
            hideByNeedles(['Job Application Snapshot']);

            // Hide old Candidate Files block, keep only the new unified one.
            const unifiedFiles = document.querySelector('[data-sfpe-clean-candidate-files="1"]');
            const oldFilesBlocks = Array.from(document.querySelectorAll('section, .pe-card, .sfpe-card, .sfpe-panel, div[class*="card"], div[class*="panel"]'))
                .filter((block) => {
                    if (block === unifiedFiles || block.contains(unifiedFiles)) return false;
                    const text = normalize(block.innerText || block.textContent || '');
                    return text.includes('candidate files') || text.includes('uploaded files');
                });

            oldFilesBlocks.forEach((block) => block.classList.add('sfpe-clean-hidden'));

            // Rename Candidate Requests block.
            renameByNeedles(['Candidate Requests'], 'Candidate Requests');
            renameByNeedles(['Job Application Candidate Requests'], 'Candidate Requests');

            // Make candidate request timeline the only timeline and scrollable.
            const requestTimeline = findBlockByText(['Candidate Request Timeline']) || findBlockByText(['request creation', 'candidate replies']);
            if (requestTimeline) {
                requestTimeline.classList.add('sfpe-clean-scroll');
            }

            // Move Finance Profile directly after Candidate Details.
            const candidateDetails = findBlockByText(['Candidate Details']);
            const financeProfile = findBlockByText(['Finance Profile']);
            moveAfter(financeProfile, candidateDetails);

            // Move unified Candidate Files after candidate request timeline if possible,
            // otherwise after Candidate Requests.
            const candidateRequests = findBlockByText(['Candidate Requests']);
            if (unifiedFiles) {
                if (requestTimeline) {
                    moveAfter(unifiedFiles, requestTimeline);
                } else if (candidateRequests) {
                    moveAfter(unifiedFiles, candidateRequests);
                }
            }

            // Add expense button to expenses block.
            addExpenseButton();
        };

        document.addEventListener('DOMContentLoaded', apply);
        document.addEventListener('livewire:navigated', apply);
        window.addEventListener('load', apply);

        setTimeout(apply, 150);
        setTimeout(apply, 500);
        setTimeout(apply, 1200);

        new MutationObserver(() => {
            clearTimeout(window.__sfpeCleanFlowTimer);
            window.__sfpeCleanFlowTimer = setTimeout(apply, 80);
        }).observe(document.body, { childList: true, subtree: true });
    })();
</script>


<style id="sfpe-force-dark-header-final">
    /*
     | FINAL Pre-Employment dark header restore.
     | CSS only. Does not touch candidate details, files, requests, expenses, or timeline logic.
     */

    .sfpe-page-header-restored,
    .sfpe-employment-hero,
    .pe-hero,
    .pe-header,
    .pe-premium-header,
    .pe-page-hero {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: min(100%, 1280px) !important;
        margin: 0 auto 28px auto !important;
        padding: 30px !important;
        border-radius: 34px !important;
        overflow: hidden !important;
        position: relative !important;
        z-index: 5 !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .22), transparent 34%),
            radial-gradient(circle at bottom left, rgba(37, 99, 235, .24), transparent 38%),
            linear-gradient(135deg, #07111f 0%, #0f172a 48%, #123057 100%) !important;
        border: 1px solid rgba(148, 163, 184, .18) !important;
        box-shadow: 0 24px 65px rgba(15, 23, 42, .26) !important;
        color: #ffffff !important;
    }

    .sfpe-page-header-restored *,
    .sfpe-employment-hero *,
    .pe-hero *,
    .pe-header *,
    .pe-premium-header *,
    .pe-page-hero * {
        color: inherit;
    }

    .sfpe-page-title,
    .sfpe-hero-title,
    .pe-title,
    .pe-hero-title,
    .pe-header-title,
    .sfpe-page-header-restored h1,
    .sfpe-employment-hero h1,
    .pe-hero h1,
    .pe-header h1 {
        color: #ffffff !important;
        font-weight: 950 !important;
        letter-spacing: -.055em !important;
        line-height: .98 !important;
        text-shadow: 0 10px 35px rgba(0, 0, 0, .18) !important;
    }

    .sfpe-page-subtitle,
    .sfpe-hero-subtitle,
    .pe-subtitle,
    .pe-breadcrumb,
    .sfpe-kicker,
    .sfpe-page-breadcrumbs,
    .pe-hero p,
    .pe-header p {
        color: rgba(226, 232, 240, .86) !important;
    }

    .sfpe-force-action-bar,
    .sfpe-actions,
    .pe-header-actions,
    .pe-hero-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        align-items: center !important;
        justify-content: flex-end !important;
    }

    .sfpe-force-btn,
    .sfpe-actions a,
    .sfpe-actions button,
    .pe-header-actions a,
    .pe-header-actions button,
    .pe-hero-actions a,
    .pe-hero-actions button {
        min-height: 46px !important;
        border-radius: 999px !important;
        padding: 0 18px !important;
        font-weight: 900 !important;
        text-decoration: none !important;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .18) !important;
    }

    .dark .sfpe-page-header-restored,
    .dark .sfpe-employment-hero,
    .dark .pe-hero,
    .dark .pe-header,
    .dark .pe-premium-header,
    .dark .pe-page-hero {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(37, 99, 235, .28), transparent 38%),
            linear-gradient(135deg, #020617 0%, #0f172a 52%, #123057 100%) !important;
        color: #ffffff !important;
    }

    @media (max-width: 720px) {
        .sfpe-page-header-restored,
        .sfpe-employment-hero,
        .pe-hero,
        .pe-header,
        .pe-premium-header,
        .pe-page-hero {
            padding: 22px !important;
            border-radius: 26px !important;
        }

        .sfpe-force-action-bar,
        .sfpe-actions,
        .pe-header-actions,
        .pe-hero-actions {
            justify-content: flex-start !important;
        }
    }
</style>



<style id="sfpe-real-header-force-style">
    /*
     | Real Pre-Employment header.
     | This creates a visible header even if old header blocks were hidden/removed.
     */

    .sfpe-real-header-force {
        width: min(100%, 1280px) !important;
        margin: 0 auto 28px auto !important;
        padding: 30px !important;
        border-radius: 34px !important;
        position: relative !important;
        z-index: 999 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        overflow: hidden !important;
        color: #ffffff !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .24), transparent 34%),
            radial-gradient(circle at bottom left, rgba(37, 99, 235, .28), transparent 38%),
            linear-gradient(135deg, #06101f 0%, #0f172a 52%, #123057 100%) !important;
        border: 1px solid rgba(148, 163, 184, .18) !important;
        box-shadow: 0 24px 65px rgba(15, 23, 42, .26) !important;
    }

    .sfpe-real-header-force,
    .sfpe-real-header-force * {
        box-sizing: border-box !important;
    }

    .sfpe-real-header-main {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 24px !important;
        flex-wrap: wrap !important;
    }

    .sfpe-real-kicker {
        margin-bottom: 12px !important;
        color: rgba(226, 232, 240, .82) !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase !important;
    }

    .sfpe-real-title {
        margin: 0 !important;
        color: #ffffff !important;
        font-size: clamp(36px, 5vw, 68px) !important;
        line-height: .95 !important;
        font-weight: 950 !important;
        letter-spacing: -.065em !important;
        text-shadow: 0 12px 35px rgba(0, 0, 0, .22) !important;
    }

    .sfpe-real-subtitle {
        max-width: 760px !important;
        margin: 16px 0 0 !important;
        color: rgba(226, 232, 240, .84) !important;
        font-size: 15px !important;
        line-height: 1.7 !important;
        font-weight: 650 !important;
    }

    .sfpe-real-status-stack {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        justify-content: flex-end !important;
        align-items: center !important;
    }

    .sfpe-real-status-pill,
    .sfpe-real-code-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 42px !important;
        padding: 0 16px !important;
        border-radius: 999px !important;
        color: #ffffff !important;
        background: rgba(255, 255, 255, .12) !important;
        border: 1px solid rgba(255, 255, 255, .18) !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase !important;
        backdrop-filter: blur(14px) !important;
    }

    .sfpe-real-code-pill {
        background: rgba(34, 211, 238, .16) !important;
        border-color: rgba(34, 211, 238, .28) !important;
    }

    .sfpe-real-action-row {
        margin-top: 24px !important;
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        align-items: center !important;
    }

    .sfpe-real-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 48px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        border: 0 !important;
        text-decoration: none !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        cursor: pointer !important;
        box-shadow: 0 14px 30px rgba(0, 0, 0, .18) !important;
        white-space: nowrap !important;
    }

    .sfpe-real-btn-light {
        background: #ffffff !important;
        color: #0f172a !important;
    }

    .sfpe-real-btn-green {
        background: linear-gradient(135deg, #16a34a, #15803d) !important;
        color: #ffffff !important;
    }

    .sfpe-real-btn-orange {
        background: linear-gradient(135deg, #f59e0b, #f97316) !important;
        color: #111827 !important;
    }

    .sfpe-real-btn-blue {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        color: #ffffff !important;
    }

    .sfpe-real-btn-red {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
    }

    .sfpe-real-btn-purple {
        background: linear-gradient(135deg, #7c3aed, #6d28d9) !important;
        color: #ffffff !important;
    }

    .sfpe-real-btn-teal {
        background: linear-gradient(135deg, #14b8a6, #0f766e) !important;
        color: #ffffff !important;
    }

    .sfpe-real-btn-dark {
        background: linear-gradient(135deg, #111827, #020617) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, .14) !important;
    }

    @media (max-width: 720px) {
        .sfpe-real-header-force {
            padding: 22px !important;
            border-radius: 26px !important;
        }

        .sfpe-real-status-stack {
            justify-content: flex-start !important;
        }

        .sfpe-real-btn {
            width: 100% !important;
        }
    }
</style>



<style id="sfpe-employment-like-compact-body-v1">
    /*
     | Sada Fezzan ERP — Pre-Employment compact body polish
     | Goal: make Pre-Employment body closer to Employment profile blocks.
     | Does not touch the dark hero/header.
     */

    .sfpe-real-hero {
        margin-bottom: 22px !important;
    }

    /* Main page cards: smaller, cleaner, Employment-like */
    .pe-card,
    .sfpe-candidate-files-card,
    .sfpe-clean-flow-files-card,
    section[class*="candidate-files"],
    section[class*="expenses"],
    section[class*="finance"] {
        width: min(100%, 1120px) !important;
        margin-left: auto !important;
        margin-right: auto !important;
        border-radius: 26px !important;
        border: 1px solid rgba(15, 23, 42, .075) !important;
        box-shadow: 0 18px 44px rgba(15, 23, 42, .075) !important;
        overflow: hidden !important;
    }

    .dark .pe-card,
    .dark .sfpe-candidate-files-card,
    .dark .sfpe-clean-flow-files-card {
        border-color: rgba(148, 163, 184, .16) !important;
        box-shadow: 0 18px 44px rgba(0, 0, 0, .22) !important;
    }

    /* Card headers: compact like Employment */
    .pe-card-header,
    .sfpe-md-card-head,
    .sfpe-card-header {
        min-height: 70px !important;
        padding: 18px 22px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
        border-bottom: 1px solid rgba(15, 23, 42, .07) !important;
    }

    .dark .pe-card-header,
    .dark .sfpe-md-card-head,
    .dark .sfpe-card-header {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .11), transparent 34%),
            rgba(15, 23, 42, .50) !important;
        border-bottom-color: rgba(148, 163, 184, .14) !important;
    }

    .pe-card-title,
    .sfpe-md-title,
    .sfpe-card-title {
        font-size: 20px !important;
        line-height: 1.15 !important;
        letter-spacing: -.045em !important;
        font-weight: 950 !important;
        color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .dark .pe-card-title,
    .dark .sfpe-md-title,
    .dark .sfpe-card-title {
        color: #ffffff !important;
    }

    .pe-card-title .material-symbols-rounded,
    .sfpe-md-icon,
    .sfpe-card-title .material-symbols-rounded {
        font-size: 24px !important;
        color: #2563eb !important;
    }

    .dark .pe-card-title .material-symbols-rounded,
    .dark .sfpe-md-icon,
    .dark .sfpe-card-title .material-symbols-rounded {
        color: #22d3ee !important;
    }

    /* Grid items: reduce huge boxes */
    .pe-grid,
    .sfpe-grid,
    .sfpe-md-grid,
    .sfpe-candidate-files-grid {
        padding: 18px 22px !important;
        gap: 12px !important;
    }

    .pe-grid,
    .sfpe-grid,
    .sfpe-md-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .pe-info-box,
    .pe-stat,
    .pe-file-card,
    .sfpe-info-item,
    .sfpe-md-item,
    .sfpe-candidate-file-item,
    .sfpe-expense-card,
    .sfpe-file-card,
    .sfpe-request-card,
    .sfpe-answer-item {
        min-height: auto !important;
        border-radius: 20px !important;
        padding: 16px 18px !important;
        background: rgba(248, 250, 252, .82) !important;
        border: 1px solid rgba(15, 23, 42, .07) !important;
        box-shadow: none !important;
    }

    .dark .pe-info-box,
    .dark .pe-stat,
    .dark .pe-file-card,
    .dark .sfpe-info-item,
    .dark .sfpe-md-item,
    .dark .sfpe-candidate-file-item,
    .dark .sfpe-expense-card,
    .dark .sfpe-file-card,
    .dark .sfpe-request-card,
    .dark .sfpe-answer-item {
        background: rgba(15, 23, 42, .58) !important;
        border-color: rgba(148, 163, 184, .15) !important;
    }

    .pe-label,
    .sfpe-label,
    .sfpe-md-label,
    .sfpe-file-category {
        font-size: 11px !important;
        line-height: 1.15 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
        color: #64748b !important;
        font-weight: 950 !important;
        margin-bottom: 8px !important;
    }

    .dark .pe-label,
    .dark .sfpe-label,
    .dark .sfpe-md-label,
    .dark .sfpe-file-category {
        color: #94a3b8 !important;
    }

    .pe-value,
    .sfpe-value,
    .sfpe-md-value,
    .sfpe-file-title {
        font-size: 15px !important;
        line-height: 1.35 !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        overflow-wrap: anywhere !important;
    }

    .dark .pe-value,
    .dark .sfpe-value,
    .dark .sfpe-md-value,
    .dark .sfpe-file-title {
        color: #ffffff !important;
    }

    /* Finance Profile specifically: compact and placed after Candidate Details by JS */
    .pe-card:has(.pe-card-title),
    .pe-card:has(.sfpe-md-title) {
        scroll-margin-top: 110px;
    }

    /* Candidate Files: same feeling as Employment latest files card, not huge */
    .sfpe-candidate-files-card .sfpe-candidate-files-grid,
    .sfpe-clean-flow-files-card .sfpe-candidate-files-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .sfpe-candidate-file-item {
        min-height: 132px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
    }

    .sfpe-file-source {
        margin-top: 6px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 800 !important;
    }

    .sfpe-file-open-btn,
    .sfpe-file-link,
    .pe-open-btn,
    a[class*="open"] {
        min-height: 34px !important;
        width: fit-content !important;
        padding: 0 12px !important;
        border-radius: 999px !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        background: #e0f2fe !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(37, 99, 235, .14) !important;
        text-decoration: none !important;
    }

    /* Expenses: stop giant cards */
    .sfpe-expenses-grid,
    .pe-expenses-grid,
    .sfpe-expense-list,
    .pe-card:has(.pe-card-title) .sfpe-expenses-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 12px !important;
        padding: 18px 22px !important;
    }

    .sfpe-expense-card,
    .pe-expense-card,
    .pe-card:has(.pe-card-title) .sfpe-expense-card {
        min-height: 142px !important;
        padding: 16px 18px !important;
        border-radius: 20px !important;
        display: grid !important;
        gap: 8px !important;
        align-content: start !important;
    }

    .sfpe-expense-card * ,
    .pe-expense-card * {
        line-height: 1.35 !important;
    }

    .sfpe-expense-card .amount,
    .pe-expense-card .amount,
    .sfpe-expense-amount {
        font-size: 16px !important;
        font-weight: 950 !important;
        margin: 4px 0 !important;
    }

    /* Header buttons inside cards, like Add Expense */
    .pe-card-header button,
    .pe-card-header a,
    .sfpe-card-header button,
    .sfpe-card-header a {
        min-height: 38px !important;
        padding: 0 15px !important;
        border-radius: 999px !important;
        font-size: 12px !important;
        font-weight: 950 !important;
    }

    /* Candidate details card stays clean but smaller */
    .pe-card:first-of-type .pe-grid,
    .sfpe-candidate-details .pe-grid {
        padding: 18px 22px !important;
    }

    /* Hide duplicated old file block only if the new Candidate Files block exists */
    body.sfpe-has-clean-candidate-files section[data-sfpe-old-files-block="1"] {
        display: none !important;
    }

    @media (max-width: 1180px) {
        .pe-grid,
        .sfpe-grid,
        .sfpe-md-grid,
        .sfpe-candidate-files-card .sfpe-candidate-files-grid,
        .sfpe-clean-flow-files-card .sfpe-candidate-files-grid,
        .sfpe-expenses-grid,
        .pe-expenses-grid,
        .sfpe-expense-list {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 760px) {
        .pe-card,
        .sfpe-candidate-files-card,
        .sfpe-clean-flow-files-card {
            width: min(100%, calc(100vw - 24px)) !important;
            border-radius: 22px !important;
        }

        .pe-grid,
        .sfpe-grid,
        .sfpe-md-grid,
        .sfpe-candidate-files-card .sfpe-candidate-files-grid,
        .sfpe-clean-flow-files-card .sfpe-candidate-files-grid,
        .sfpe-expenses-grid,
        .pe-expenses-grid,
        .sfpe-expense-list {
            grid-template-columns: 1fr !important;
            padding: 14px !important;
        }

        .pe-card-header,
        .sfpe-md-card-head,
        .sfpe-card-header {
            padding: 16px !important;
        }
    }
</style>

<script id="sfpe-employment-like-card-order-v1">
    (() => {
        const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim().toLowerCase();

        const findCardByTitle = (needle) => {
            const titles = Array.from(document.querySelectorAll('.pe-card-title, .sfpe-md-title, .sfpe-card-title, h2, h3'));
            const foundTitle = titles.find((el) => normalize(el.textContent).includes(normalize(needle)));
            return foundTitle ? foundTitle.closest('section, .pe-card, .sfpe-md-card, .sfpe-card') : null;
        };

        const compactExpenseCards = () => {
            const expensesCard = findCardByTitle('Pre-Employment Expenses');
            if (!expensesCard) return;

            expensesCard.querySelectorAll(':scope > div, .pe-card-body, .sfpe-card-body').forEach((body) => {
                const cards = body.querySelectorAll('.pe-expense-card, .sfpe-expense-card, [class*="expense-card"]');
                if (cards.length) {
                    body.classList.add('sfpe-expenses-grid');
                }
            });
        };

        const moveFinanceAfterCandidate = () => {
            const candidate = findCardByTitle('Candidate Details');
            const finance = findCardByTitle('Finance Profile');

            if (!candidate || !finance || !candidate.parentElement) return;

            if (candidate.nextElementSibling !== finance) {
                candidate.parentElement.insertBefore(finance, candidate.nextElementSibling);
            }
        };

        const markCandidateFiles = () => {
            const candidateFiles = findCardByTitle('Candidate Files');
            if (candidateFiles) {
                document.body.classList.add('sfpe-has-clean-candidate-files');
            }
        };

        const apply = () => {
            moveFinanceAfterCandidate();
            compactExpenseCards();
            markCandidateFiles();
        };

        document.addEventListener('DOMContentLoaded', apply);
        document.addEventListener('livewire:navigated', apply);

        setTimeout(apply, 80);
        setTimeout(apply, 400);
        setTimeout(apply, 1000);

        new MutationObserver(() => {
            window.clearTimeout(window.__sfpeCompactBodyTimer);
            window.__sfpeCompactBodyTimer = window.setTimeout(apply, 80);
        }).observe(document.body, { childList: true, subtree: true });
    })();
</script>



<style id="sfpe-force-employment-profile-compact-v2">
    /*
     | FINAL FORCE LAYER
     | Makes Pre-Employment body blocks closer to Employment profile style.
     | Does not remove the dark hero/header.
     */

    :root {
        --sfpe-compact-width: 980px;
    }

    /* Page content width */
    .fi-main,
    .fi-page,
    .fi-page-content,
    main.fi-main {
        --max-width: 1180px !important;
    }

    /* Keep every main block compact */
    .fi-page-content > *,
    .fi-main section,
    .pe-card,
    .sfpe-md-card,
    .sfpe-card,
    .sfpe-candidate-files-card,
    .sfpe-clean-flow-files-card {
        max-width: var(--sfpe-compact-width) !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /* Main profile blocks */
    .pe-card,
    .sfpe-md-card,
    .sfpe-card,
    .sfpe-candidate-files-card,
    .sfpe-clean-flow-files-card {
        border-radius: 22px !important;
        overflow: hidden !important;
        background: rgba(255,255,255,.96) !important;
        border: 1px solid rgba(15,23,42,.07) !important;
        box-shadow: 0 14px 34px rgba(15,23,42,.07) !important;
        margin-top: 16px !important;
        margin-bottom: 16px !important;
    }

    .dark .pe-card,
    .dark .sfpe-md-card,
    .dark .sfpe-card,
    .dark .sfpe-candidate-files-card,
    .dark .sfpe-clean-flow-files-card {
        background: rgba(15,23,42,.78) !important;
        border-color: rgba(148,163,184,.16) !important;
        box-shadow: 0 14px 34px rgba(0,0,0,.24) !important;
    }

    /* Card headers */
    .pe-card-header,
    .sfpe-md-card-head,
    .sfpe-card-header {
        min-height: 58px !important;
        padding: 14px 18px !important;
        border-bottom: 1px solid rgba(15,23,42,.065) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.07), transparent 32%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    }

    .pe-card-title,
    .sfpe-md-title,
    .sfpe-card-title {
        font-size: 18px !important;
        line-height: 1.15 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        margin: 0 !important;
        color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .pe-card-title .material-symbols-rounded,
    .sfpe-md-icon,
    .sfpe-card-title .material-symbols-rounded {
        font-size: 22px !important;
        width: 22px !important;
        height: 22px !important;
        color: #2563eb !important;
    }

    /* Force text icon names to become proper icon style */
    .material-symbols-rounded {
        font-family: 'Material Symbols Rounded' !important;
        font-weight: normal !important;
        font-style: normal !important;
        font-size: 22px !important;
        line-height: 1 !important;
        letter-spacing: normal !important;
        text-transform: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
        word-wrap: normal !important;
        direction: ltr !important;
        -webkit-font-feature-settings: 'liga' !important;
        -webkit-font-smoothing: antialiased !important;
        font-feature-settings: 'liga' !important;
    }

    /* Pills / counters */
    .sfpe-count-pill,
    .pe-pill,
    .sfpe-pill,
    .sfpe-status-pill,
    .pe-card-header > span,
    .pe-card-header > a,
    .pe-card-header > button {
        min-height: 34px !important;
        padding: 0 13px !important;
        border-radius: 999px !important;
        font-size: 11px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: .04em !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Body grid */
    .pe-grid,
    .sfpe-grid,
    .sfpe-md-grid,
    .sfpe-candidate-files-grid,
    .sfpe-clean-flow-files-grid {
        padding: 16px 18px !important;
        gap: 10px !important;
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    /* Any body direct wrapper */
    .pe-card-body,
    .sfpe-card-body,
    .sfpe-md-card-body {
        padding: 16px 18px !important;
    }

    /* Inner small boxes */
    .pe-grid > *,
    .sfpe-grid > *,
    .sfpe-md-grid > *,
    .pe-info-box,
    .pe-stat,
    .sfpe-md-item,
    .sfpe-info-item,
    .sfpe-answer-item,
    .sfpe-candidate-file-item,
    .sfpe-file-card,
    .pe-file-card,
    .sfpe-request-item,
    .sfpe-request-card {
        min-height: 92px !important;
        height: auto !important;
        border-radius: 16px !important;
        padding: 13px 14px !important;
        background: rgba(248,250,252,.86) !important;
        border: 1px solid rgba(15,23,42,.07) !important;
        box-shadow: none !important;
    }

    .dark .pe-grid > *,
    .dark .sfpe-grid > *,
    .dark .sfpe-md-grid > *,
    .dark .pe-info-box,
    .dark .pe-stat,
    .dark .sfpe-md-item,
    .dark .sfpe-info-item,
    .dark .sfpe-answer-item,
    .dark .sfpe-candidate-file-item,
    .dark .sfpe-file-card,
    .dark .pe-file-card,
    .dark .sfpe-request-item,
    .dark .sfpe-request-card {
        background: rgba(15,23,42,.58) !important;
        border-color: rgba(148,163,184,.14) !important;
    }

    /* Labels and values */
    .pe-label,
    .sfpe-label,
    .sfpe-md-label,
    .sfpe-file-category,
    .sfpe-expense-category,
    .sfpe-card-label {
        margin: 0 0 6px !important;
        font-size: 10px !important;
        line-height: 1.1 !important;
        letter-spacing: .11em !important;
        text-transform: uppercase !important;
        color: #64748b !important;
        font-weight: 950 !important;
    }

    .pe-value,
    .sfpe-value,
    .sfpe-md-value,
    .sfpe-file-title,
    .sfpe-expense-title,
    .sfpe-card-value {
        font-size: 14px !important;
        line-height: 1.3 !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        margin: 0 !important;
    }

    .dark .pe-card-title,
    .dark .sfpe-md-title,
    .dark .sfpe-card-title,
    .dark .pe-value,
    .dark .sfpe-value,
    .dark .sfpe-md-value,
    .dark .sfpe-file-title,
    .dark .sfpe-expense-title,
    .dark .sfpe-card-value {
        color: #ffffff !important;
    }

    .dark .pe-label,
    .dark .sfpe-label,
    .dark .sfpe-md-label,
    .dark .sfpe-file-category,
    .dark .sfpe-expense-category,
    .dark .sfpe-card-label {
        color: #94a3b8 !important;
    }

    /* Candidate Files specifically */
    .sfpe-candidate-files-card,
    .sfpe-clean-flow-files-card {
        margin-top: 16px !important;
    }

    .sfpe-candidate-files-card .sfpe-candidate-files-grid,
    .sfpe-clean-flow-files-card .sfpe-candidate-files-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .sfpe-candidate-file-item {
        min-height: 118px !important;
        display: grid !important;
        align-content: space-between !important;
        gap: 7px !important;
    }

    .sfpe-file-source {
        font-size: 11px !important;
        line-height: 1.25 !important;
        color: #64748b !important;
        font-weight: 800 !important;
        margin: 0 !important;
    }

    .sfpe-file-open-btn,
    .sfpe-file-link,
    .pe-open-btn,
    a.sfpe-file-open-btn {
        min-height: 30px !important;
        width: fit-content !important;
        padding: 0 11px !important;
        border-radius: 999px !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        background: #e0f2fe !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(37,99,235,.16) !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* Finance profile must be compact */
    .pe-card:has(.pe-card-title),
    .sfpe-md-card:has(.sfpe-md-title) {
        scroll-margin-top: 120px !important;
    }

    /* Expenses compact cards */
    .sfpe-expenses-grid,
    .pe-expenses-grid,
    .sfpe-expense-list,
    .pe-card-body:has(.sfpe-expense-card),
    .pe-card-body:has(.pe-expense-card) {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 10px !important;
        padding: 16px 18px !important;
    }

    .sfpe-expense-card,
    .pe-expense-card,
    [class*="expense-card"] {
        min-height: 116px !important;
        height: auto !important;
        border-radius: 16px !important;
        padding: 13px 14px !important;
        display: grid !important;
        align-content: start !important;
        gap: 7px !important;
        background: rgba(248,250,252,.86) !important;
        border: 1px solid rgba(15,23,42,.07) !important;
        box-shadow: none !important;
    }

    .sfpe-expense-card > *,
    .pe-expense-card > *,
    [class*="expense-card"] > * {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        line-height: 1.25 !important;
    }

    .sfpe-expense-amount,
    .pe-expense-amount,
    .amount,
    [class*="expense"] .amount {
        font-size: 15px !important;
        font-weight: 950 !important;
        color: #234b74 !important;
    }

    /* Timeline: smaller and scrollable */
    .sfpe-timeline-print-area,
    section:has(.pe-timeline),
    section:has(.sfpe-thread-timeline) {
        max-width: var(--sfpe-compact-width) !important;
    }

    .pe-timeline,
    .sfpe-thread-timeline,
    .sfpe-candidate-timeline-list,
    .sfpe-request-timeline-list {
        max-height: 540px !important;
        overflow-y: auto !important;
        padding: 16px 18px !important;
    }

    .pe-timeline-item,
    .sfpe-thread-row,
    .sfpe-candidate-timeline-row,
    .sfpe-request-timeline-row {
        min-height: 84px !important;
        border-radius: 16px !important;
        padding: 13px 14px !important;
    }

    /* Reduce spacing between all sections */
    .fi-page-content {
        gap: 16px !important;
    }

    .fi-page-content > div,
    .fi-page-content > section {
        margin-top: 0 !important;
    }

    /* Responsive */
    @media (max-width: 1100px) {
        :root {
            --sfpe-compact-width: 94vw;
        }

        .pe-grid,
        .sfpe-grid,
        .sfpe-md-grid,
        .sfpe-candidate-files-grid,
        .sfpe-clean-flow-files-grid,
        .sfpe-expenses-grid,
        .pe-expenses-grid,
        .sfpe-expense-list {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 720px) {
        .pe-grid,
        .sfpe-grid,
        .sfpe-md-grid,
        .sfpe-candidate-files-grid,
        .sfpe-clean-flow-files-grid,
        .sfpe-expenses-grid,
        .pe-expenses-grid,
        .sfpe-expense-list {
            grid-template-columns: 1fr !important;
            padding: 14px !important;
        }

        .pe-card-header,
        .sfpe-md-card-head,
        .sfpe-card-header {
            padding: 14px !important;
        }
    }
</style>


<style id="sfpe-employment-like-layout-final-v1">
    /*
     | Sada Fezzan ERP — Pre-Employment employment-like final layout
     | Goal:
     | - Same visual width as Employment profile.
     | - Hero must be the first main block.
     | - Candidate Details / Finance / Status / Files / Requests / Expenses use one consistent wide container.
     | - Cards become compact, not huge separated narrow blocks.
     */

    :root {
        --sfpe-page-width: 1180px;
        --sfpe-card-radius: 28px;
        --sfpe-blue: #2563eb;
        --sfpe-cyan: #22d3ee;
        --sfpe-title: #234b74;
        --sfpe-dark: #071424;
    }

    /* Main page content width */
    .fi-main,
    .fi-page,
    .fi-page > section,
    .fi-page-content,
    .fi-page-content > div,
    .fi-resource-view-record-page {
        max-width: none !important;
    }

    /* Force all direct visual blocks to share the same center width */
    .sfpe-employment-hero,
    .sfpe-page-header-restored,
    .pe-hero,
    .pe-card,
    .sfpe-clean-flow-files-card,
    .sfpe-candidate-files-card,
    .sfpe-ja-requests-card,
    .sfpe-ja-timeline-card,
    .sfpe-timeline-print-area,
    section[class*="sfpe"],
    section[class*="pe-card"] {
        width: min(100%, var(--sfpe-page-width)) !important;
        max-width: var(--sfpe-page-width) !important;
        margin-left: auto !important;
        margin-right: auto !important;
        box-sizing: border-box !important;
    }

    /* Hero should be at top and same width as Employment */
    .sfpe-employment-hero {
        order: -100 !important;
        margin-top: 22px !important;
        margin-bottom: 26px !important;
        padding: 34px 36px !important;
        border-radius: 30px !important;
        min-height: auto !important;
        overflow: visible !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .20), transparent 36%),
            linear-gradient(135deg, #071424 0%, #0b1b32 48%, #0f4c5c 100%) !important;
        box-shadow: 0 24px 55px rgba(15, 23, 42, .22) !important;
    }

    .sfpe-employment-hero h1,
    .sfpe-employment-hero .sfpe-page-title,
    .sfpe-employment-hero .sfpe-hero-title,
    .sfpe-employment-hero [class*="title"] {
        font-size: clamp(42px, 5vw, 72px) !important;
        line-height: .92 !important;
        letter-spacing: -.07em !important;
        color: #ffffff !important;
        max-width: 760px !important;
    }

    .sfpe-employment-hero p,
    .sfpe-employment-hero .sfpe-hero-subtitle {
        max-width: 780px !important;
        color: rgba(255, 255, 255, .78) !important;
        font-weight: 750 !important;
        line-height: 1.55 !important;
    }

    /* Hero action buttons: keep icons, compact like Employment */
    .sfpe-employment-hero button,
    .sfpe-employment-hero a,
    .sfpe-employment-hero .fi-btn,
    .sfpe-force-btn {
        min-height: 42px !important;
        border-radius: 999px !important;
        padding: 0 16px !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        box-shadow: 0 14px 28px rgba(0, 0, 0, .18) !important;
        white-space: nowrap !important;
    }

    .sfpe-employment-hero svg,
    .sfpe-employment-hero .material-symbols-rounded,
    .sfpe-employment-hero .material-symbols-outlined {
        width: 18px !important;
        height: 18px !important;
        font-size: 18px !important;
        display: inline-flex !important;
        flex: 0 0 auto !important;
    }

    /* Main cards */
    .pe-card,
    .sfpe-clean-flow-files-card,
    .sfpe-candidate-files-card,
    .sfpe-ja-requests-card,
    .sfpe-ja-timeline-card,
    .sfpe-timeline-print-area {
        border-radius: var(--sfpe-card-radius) !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
            #ffffff !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 20px 48px rgba(15, 23, 42, .08) !important;
        overflow: hidden !important;
        margin-top: 22px !important;
        margin-bottom: 22px !important;
    }

    .pe-card-header,
    .pe-card-head,
    .sfpe-card-head,
    .sfpe-clean-flow-files-card > div:first-child,
    .sfpe-candidate-files-card > div:first-child {
        min-height: 74px !important;
        padding: 22px 26px !important;
        border-bottom: 1px solid rgba(15, 23, 42, .08) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 30%),
            linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    }

    .pe-card-title,
    .sfpe-card-title,
    .sfpe-section-title {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        color: #0f172a !important;
        font-size: 22px !important;
        line-height: 1.15 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
    }

    .pe-card-title .material-symbols-rounded,
    .pe-card-title .material-symbols-outlined,
    .sfpe-card-title .material-symbols-rounded,
    .sfpe-card-title .material-symbols-outlined,
    .material-symbols-rounded {
        font-family: 'Material Symbols Rounded' !important;
        font-style: normal !important;
        font-weight: 600 !important;
        line-height: 1 !important;
        text-transform: none !important;
        letter-spacing: normal !important;
        color: var(--sfpe-blue) !important;
    }

    /* Detail grids same as Employment: compact 3 columns */
    .pe-grid,
    .sfpe-grid,
    .sfpe-info-grid,
    .sfpe-cards-grid,
    .sfpe-finance-grid,
    .sfpe-status-grid,
    .sfpe-candidate-files-grid,
    .sfpe-expenses-grid,
    .sfpe-files-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 14px !important;
        padding: 24px 26px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Compact internal cards */
    .pe-info-item,
    .pe-stat,
    .pe-file,
    .sfpe-info-item,
    .sfpe-card-item,
    .sfpe-candidate-file-item,
    .sfpe-expense-card,
    .sfpe-finance-item,
    .sfpe-status-item,
    .pe-card [class*="item"],
    .pe-card [class*="file-item"] {
        min-height: 112px !important;
        border-radius: 22px !important;
        padding: 18px !important;
        background: rgba(248, 250, 252, .82) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: none !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        gap: 8px !important;
    }

    /* Finance profile should feel like Employment summary cards */
    .sfpe-finance-item,
    .pe-card:has(.pe-card-title:contains("Finance")) .pe-info-item {
        position: relative !important;
    }

    .sfpe-finance-item::before,
    .pe-card [class*="finance"]::before {
        content: "";
        position: absolute;
        top: 0;
        left: 18px;
        right: 18px;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--sfpe-cyan), var(--sfpe-blue));
    }

    .sfpe-file-title,
    .sfpe-expense-title,
    .sfpe-value,
    .pe-info-value,
    .pe-stat-value,
    .pe-card strong {
        color: #0f172a !important;
        font-size: 16px !important;
        line-height: 1.35 !important;
        font-weight: 950 !important;
    }

    .sfpe-file-category,
    .sfpe-file-source,
    .sfpe-label,
    .pe-info-label,
    .pe-stat-label {
        color: #64748b !important;
        font-size: 11px !important;
        line-height: 1.1 !important;
        font-weight: 950 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
    }

    /* Candidate files: stop giant cards */
    .sfpe-candidate-files-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .sfpe-candidate-file-item {
        min-height: 150px !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
    }

    .sfpe-file-open-btn,
    .sfpe-open-btn,
    .sfpe-btn,
    .pe-card a[class*="btn"],
    .pe-card button[class*="btn"] {
        width: fit-content !important;
        min-height: 36px !important;
        padding: 0 14px !important;
        border-radius: 999px !important;
        background: #e0f2fe !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(37, 99, 235, .16) !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        text-decoration: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
    }

    /* Candidate Requests and Timeline should be scrollable, not enormous */
    .sfpe-ja-requests-list,
    .sfpe-ja-timeline-list,
    .sfpe-thread-timeline,
    .pe-timeline {
        max-height: 560px !important;
        overflow-y: auto !important;
        padding-right: 8px !important;
    }

    .sfpe-ja-request-row,
    .sfpe-ja-timeline-row,
    .pe-timeline-item {
        border-radius: 22px !important;
        background: #ffffff !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .04) !important;
    }

    /* Expenses block compact */
    .sfpe-expenses-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .sfpe-expense-card {
        min-height: 150px !important;
    }

    /* Count pills */
    .sfpe-count-pill,
    .pe-count,
    .pe-badge,
    .sfpe-status-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 34px !important;
        padding: 0 14px !important;
        border-radius: 999px !important;
        background: #e0f2fe !important;
        color: #0f172a !important;
        border: 1px solid rgba(37, 99, 235, .18) !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .04em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
    }

    /* Make the visual order logical */
    .sfpe-employment-hero { order: 1 !important; }
    .pe-card:has(.pe-card-title),
    .sfpe-candidate-files-card,
    .sfpe-ja-requests-card,
    .sfpe-ja-timeline-card {
        order: 5 !important;
    }

    /* Dark mode */
    .dark .pe-card,
    .dark .sfpe-clean-flow-files-card,
    .dark .sfpe-candidate-files-card,
    .dark .sfpe-ja-requests-card,
    .dark .sfpe-ja-timeline-card,
    .dark .sfpe-timeline-print-area {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
            rgba(15, 23, 42, .86) !important;
        border-color: rgba(148, 163, 184, .18) !important;
        box-shadow: 0 20px 48px rgba(0, 0, 0, .26) !important;
    }

    .dark .pe-card-header,
    .dark .pe-card-head,
    .dark .sfpe-card-head {
        background: rgba(15, 23, 42, .48) !important;
        border-bottom-color: rgba(148, 163, 184, .18) !important;
    }

    .dark .pe-card-title,
    .dark .sfpe-card-title,
    .dark .sfpe-section-title,
    .dark .sfpe-file-title,
    .dark .sfpe-expense-title,
    .dark .sfpe-value,
    .dark .pe-info-value,
    .dark .pe-stat-value {
        color: #ffffff !important;
    }

    .dark .pe-info-item,
    .dark .pe-stat,
    .dark .pe-file,
    .dark .sfpe-info-item,
    .dark .sfpe-card-item,
    .dark .sfpe-candidate-file-item,
    .dark .sfpe-expense-card,
    .dark .sfpe-finance-item,
    .dark .sfpe-status-item {
        background: rgba(15, 23, 42, .58) !important;
        border-color: rgba(148, 163, 184, .16) !important;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        :root { --sfpe-page-width: calc(100vw - 48px); }
    }

    @media (max-width: 980px) {
        .pe-grid,
        .sfpe-grid,
        .sfpe-info-grid,
        .sfpe-cards-grid,
        .sfpe-finance-grid,
        .sfpe-status-grid,
        .sfpe-candidate-files-grid,
        .sfpe-expenses-grid,
        .sfpe-files-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .sfpe-employment-hero {
            padding: 28px !important;
        }
    }

    @media (max-width: 680px) {
        :root { --sfpe-page-width: calc(100vw - 24px); }

        .pe-grid,
        .sfpe-grid,
        .sfpe-info-grid,
        .sfpe-cards-grid,
        .sfpe-finance-grid,
        .sfpe-status-grid,
        .sfpe-candidate-files-grid,
        .sfpe-expenses-grid,
        .sfpe-files-grid {
            grid-template-columns: 1fr !important;
            padding: 18px !important;
        }

        .sfpe-employment-hero h1,
        .sfpe-employment-hero .sfpe-page-title,
        .sfpe-employment-hero [class*="title"] {
            font-size: 40px !important;
        }
    }
</style>

<script id="sfpe-employment-like-layout-final-js-v1">
    (() => {
        const run = () => {
            const hero = document.querySelector('.sfpe-employment-hero, .sfpe-page-header-restored, .pe-hero');
            const page = hero?.parentElement;

            if (!hero || !page) return;

            /* Put hero before Candidate Details if previous patches inserted it lower */
            const firstCard = Array.from(page.children).find((node) => {
                const text = (node.textContent || '').replace(/\s+/g, ' ').trim();
                return text.includes('Candidate Details');
            });

            if (firstCard && hero.compareDocumentPosition(firstCard) & Node.DOCUMENT_POSITION_PRECEDING) {
                page.insertBefore(hero, firstCard);
            }

            /* Add missing Material Symbols style for literal icon text if any old block rendered icon name as text */
            document.querySelectorAll('.pe-card-title, .sfpe-card-title').forEach((title) => {
                const txt = (title.textContent || '').trim();
                if (txt.startsWith('assignment_ind')) {
                    title.innerHTML = title.innerHTML.replace('assignment_ind', '<span class="material-symbols-rounded">assignment_ind</span>');
                }
            });
        };

        document.addEventListener('DOMContentLoaded', run);
        document.addEventListener('livewire:navigated', run);
        setTimeout(run, 150);
        setTimeout(run, 600);
        setTimeout(run, 1200);
    })();
</script>




<style id="sfpe-safe-employment-like-layout-v3">
    /*
     | Safe Pre-Employment layout fix
     | This does NOT grid the whole Filament page.
     | It only:
     | - restores normal width
     | - keeps hero at top
     | - fixes header icons
     | - makes selected lower blocks more compact/scrollable
     */

    .sfpe-employment-hero,
    .pe-card,
    .sfpe-candidate-files-card,
    .sfpe-ja-requests-card,
    .sfpe-ja-timeline-card,
    .sfpe-timeline-print-area,
    .sfpe-clean-flow-files-card {
        width: min(100%, 1180px) !important;
        max-width: 1180px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        box-sizing: border-box !important;
    }

    .sfpe-employment-hero {
        margin-top: 28px !important;
        margin-bottom: 24px !important;
        border-radius: 30px !important;
        overflow: hidden !important;
    }

    /*
     | Header button icons must not become blue.
     | They should inherit the button text color.
     */
    .sfpe-employment-hero .material-symbols-rounded,
    .sfpe-employment-hero .material-symbols-outlined,
    .sfpe-employment-hero svg,
    .sfpe-employment-hero a svg,
    .sfpe-employment-hero button svg,
    .sfpe-employment-hero a .material-symbols-rounded,
    .sfpe-employment-hero button .material-symbols-rounded {
        color: currentColor !important;
        stroke: currentColor !important;
        fill: none !important;
    }

    .sfpe-employment-hero a,
    .sfpe-employment-hero button,
    .sfpe-employment-hero .sfpe-btn {
        white-space: nowrap !important;
    }

    /*
     | Make normal cards cleaner and not huge.
     */
    .pe-card,
    .sfpe-candidate-files-card,
    .sfpe-ja-requests-card,
    .sfpe-ja-timeline-card,
    .sfpe-timeline-print-area {
        border-radius: 28px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
            #ffffff !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 18px 44px rgba(15, 23, 42, .07) !important;
        overflow: hidden !important;
        margin-top: 18px !important;
        margin-bottom: 18px !important;
    }

    .pe-card-header,
    .pe-card-head,
    .sfpe-card-head {
        min-height: 64px !important;
        padding: 18px 22px !important;
        border-bottom: 1px solid rgba(15, 23, 42, .08) !important;
    }

    .pe-grid,
    .sfpe-grid,
    .sfpe-info-grid,
    .sfpe-finance-grid,
    .sfpe-status-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 14px !important;
        padding: 20px !important;
    }

    .pe-info-item,
    .pe-stat,
    .sfpe-info-item,
    .sfpe-finance-item,
    .sfpe-status-item {
        min-height: 96px !important;
        padding: 16px 18px !important;
        border-radius: 20px !important;
        background: rgba(248, 250, 252, .84) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
    }

    /*
     | Candidate Files / Requests / Expenses / Timeline become compact internally.
     | No full-page grid, no broken columns.
     */
    .sfpe-candidate-files-grid,
    .sfpe-expenses-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 14px !important;
        padding: 20px !important;
        max-height: 560px !important;
        overflow-y: auto !important;
    }

    .sfpe-ja-requests-list,
    .sfpe-ja-timeline-list,
    .sfpe-thread-timeline,
    .pe-timeline {
        max-height: 560px !important;
        overflow-y: auto !important;
        padding: 18px 20px !important;
    }

    .sfpe-candidate-file-item,
    .sfpe-expense-card,
    .sfpe-ja-request-row,
    .sfpe-ja-timeline-row,
    .pe-timeline-item {
        min-height: auto !important;
        padding: 16px !important;
        border-radius: 20px !important;
        background: rgba(248, 250, 252, .84) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: none !important;
    }

    /*
     | Finance numbers closer to Employment style, without breaking layout.
     */
    .sfpe-finance-grid .sfpe-finance-item,
    .sfpe-finance-grid > * {
        position: relative !important;
        min-height: 120px !important;
    }

    .sfpe-finance-grid .sfpe-finance-item::before,
    .sfpe-finance-grid > *::before {
        content: "";
        position: absolute;
        top: 0;
        left: 18px;
        right: 18px;
        height: 4px;
        border-radius: 999px;
        background: linear-gradient(90deg, #22d3ee, #2563eb);
    }

    .sfpe-finance-grid .sfpe-value,
    .sfpe-finance-grid .pe-info-value,
    .sfpe-finance-grid .pe-stat-value {
        color: #234b74 !important;
        font-size: clamp(24px, 2.1vw, 38px) !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: -.055em !important;
    }

    /*
     | Labels and values compact.
     */
    .pe-info-label,
    .pe-stat-label,
    .sfpe-label,
    .sfpe-file-category,
    .sfpe-file-source {
        font-size: 10px !important;
        letter-spacing: .11em !important;
        font-weight: 950 !important;
        text-transform: uppercase !important;
        color: #64748b !important;
    }

    .pe-info-value,
    .pe-stat-value,
    .sfpe-value,
    .sfpe-file-title,
    .sfpe-expense-title {
        font-size: 15px !important;
        line-height: 1.35 !important;
        font-weight: 950 !important;
        color: #0f172a !important;
    }

    /*
     | Make open buttons smaller like Employment cards.
     */
    .sfpe-file-open-btn,
    .sfpe-open-btn,
    .pe-card a[class*="btn"],
    .pe-card button[class*="btn"] {
        min-height: 34px !important;
        padding: 0 13px !important;
        border-radius: 999px !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        width: fit-content !important;
    }

    /*
     | Dark mode compatibility.
     */
    .dark .pe-card,
    .dark .sfpe-candidate-files-card,
    .dark .sfpe-ja-requests-card,
    .dark .sfpe-ja-timeline-card,
    .dark .sfpe-timeline-print-area {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
            rgba(15, 23, 42, .86) !important;
        border-color: rgba(148, 163, 184, .18) !important;
    }

    .dark .pe-info-item,
    .dark .pe-stat,
    .dark .sfpe-info-item,
    .dark .sfpe-finance-item,
    .dark .sfpe-status-item,
    .dark .sfpe-candidate-file-item,
    .dark .sfpe-expense-card,
    .dark .sfpe-ja-request-row,
    .dark .sfpe-ja-timeline-row,
    .dark .pe-timeline-item {
        background: rgba(15, 23, 42, .58) !important;
        border-color: rgba(148, 163, 184, .16) !important;
    }

    .dark .pe-info-value,
    .dark .pe-stat-value,
    .dark .sfpe-value,
    .dark .sfpe-file-title,
    .dark .sfpe-expense-title {
        color: #ffffff !important;
    }

    @media (max-width: 1100px) {
        .pe-grid,
        .sfpe-grid,
        .sfpe-info-grid,
        .sfpe-finance-grid,
        .sfpe-status-grid,
        .sfpe-candidate-files-grid,
        .sfpe-expenses-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 720px) {
        .pe-grid,
        .sfpe-grid,
        .sfpe-info-grid,
        .sfpe-finance-grid,
        .sfpe-status-grid,
        .sfpe-candidate-files-grid,
        .sfpe-expenses-grid {
            grid-template-columns: 1fr !important;
        }

        .sfpe-employment-hero,
        .pe-card,
        .sfpe-candidate-files-card,
        .sfpe-ja-requests-card,
        .sfpe-ja-timeline-card,
        .sfpe-timeline-print-area {
            width: calc(100vw - 24px) !important;
        }
    }
</style>


</x-filament-panels::page>
