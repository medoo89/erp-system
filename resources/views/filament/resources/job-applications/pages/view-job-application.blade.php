<x-filament-panels::page>
    <style>
        .fi-header,
        .fi-page-header,
        .fi-page-header-heading,
        .fi-page-header-breadcrumbs,
        .fi-breadcrumbs {
            display: none !important;
        }

        .fi-page-header-actions,
        .fi-page-header-ctas {
            display: none !important;
        }

        .sf-workflow-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-workflow-card {
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbfd 58%, #eef8fb 100%);
            border: 1px solid #dbe7ee;
            border-radius: 30px;
            padding: 28px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .sf-workflow-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 8px;
            background: var(--sf-status-accent, linear-gradient(180deg, #234b74 0%, #2f6aa3 100%));
        }

        .sf-workflow-head {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .sf-workflow-title {
            min-width: 260px;
            flex: 1;
        }

        .sf-workflow-title .kicker {
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 10px;
        }

        .sf-workflow-title h1 {
            margin: 0;
            font-size: clamp(42px, 5vw, 74px);
            line-height: 0.92;
            letter-spacing: -0.05em;
            font-weight: 900;
            color: var(--sf-status-title, #234b74);
            transition: color 180ms ease;
        }

        .sf-workflow-meta {
            display: flex;
            align-items: flex-start;
            justify-content: flex-end;
        }

        .sf-current-status {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 999px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
        }

        .sf-status-grid,
        .sf-action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .sf-action-grid {
            margin-top: 14px;
        }

        .sf-pill {
            width: 100%;
            border: none;
            outline: none;
            cursor: pointer;
            border-radius: 22px;
            padding: 16px 18px;
            text-align: left;
            font-size: 16px;
            font-weight: 900;
            letter-spacing: -0.02em;
            transition: 180ms ease;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        }

        .sf-pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.11);
        }

        .sf-pill-muted {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #dbe4ea;
        }

        .sf-pill-current {
            background: linear-gradient(135deg, #234b74 0%, #2f6aa3 100%);
            color: #ffffff;
            border: 1px solid #234b74;
        }

        .sf-pill-warning {
            background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%);
            color: #643000;
        }

        .sf-pill-info {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
        }

        .sf-pill-primary {
            background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
            color: #083344;
        }

        .sf-pill-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #052e16;
        }

        .sf-pill-danger {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #78350f;
        }

        .sf-pill-dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
        }

        .sf-pill-soft {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            box-shadow: none;
        }

        .sf-pill small {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            font-weight: 700;
            opacity: 0.82;
            letter-spacing: 0;
        }
    
        /* SF APPLICATION FULL TIMELINE PRINT STYLE */
        .sfja-app-timeline-card {
            position: relative;
            margin-top: 28px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbfd 62%, #eef8fb 100%);
            border: 1px solid #dbe7ee;
            border-radius: 30px;
            padding: 28px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .sfja-app-timeline-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .sfja-app-timeline-title {
            margin: 0;
            color: #0f172a;
            font-size: clamp(28px, 3vw, 44px);
            line-height: 1;
            font-weight: 950;
            letter-spacing: -0.055em;
        }

        .sfja-app-timeline-subtitle {
            margin-top: 10px;
            color: #64748b;
            font-size: 16px;
            font-weight: 650;
            line-height: 1.6;
        }

        .sfja-print-timeline-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            border-radius: 999px;
            padding: 13px 18px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .22);
            white-space: nowrap;
        }

        .sfja-app-timeline-list {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .sfja-app-timeline-row {
            position: relative;
            display: grid;
            grid-template-columns: 74px 1fr;
            gap: 18px;
        }

        .sfja-app-timeline-icon-wrap {
            position: relative;
            display: flex;
            justify-content: center;
        }

        .sfja-app-timeline-icon-wrap::after {
            content: "";
            position: absolute;
            top: 64px;
            bottom: -18px;
            width: 3px;
            border-radius: 999px;
            background: #dbeafe;
        }

        .sfja-app-timeline-row:last-child .sfja-app-timeline-icon-wrap::after {
            display: none;
        }

        .sfja-app-timeline-icon {
            position: relative;
            z-index: 2;
            width: 58px;
            height: 58px;
            border-radius: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #2563eb;
            font-family: 'Material Symbols Rounded';
            font-size: 30px;
            box-shadow: 0 10px 24px rgba(37, 99, 235, .08);
        }

        .sfja-app-timeline-body {
            min-height: 92px;
            border: 1px solid #dbe4ea;
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(37, 99, 235, .07), transparent 36%),
                #ffffff;
            padding: 20px 22px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .035);
        }

        .sfja-app-timeline-top {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .sfja-app-timeline-event-title {
            color: #0f172a;
            font-size: 18px;
            font-weight: 950;
            line-height: 1.2;
        }

        .sfja-app-timeline-date {
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
            white-space: nowrap;
        }

        .sfja-app-timeline-event-subtitle {
            margin-top: 8px;
            color: #64748b;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.55;
        }

        .sfja-app-timeline-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .sfja-app-timeline-tag {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 7px 11px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .sfja-app-timeline-tag.is-status {
            background: #ecfdf5;
            color: #047857;
            border-color: #bbf7d0;
        }

        .sfja-app-timeline-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 24px;
            background: #ffffff;
            padding: 22px;
            color: #64748b;
            font-weight: 800;
        }

        @media print {
            body.sfja-print-timeline-mode * {
                visibility: hidden !important;
            }

            body.sfja-print-timeline-mode .sfja-app-timeline-print,
            body.sfja-print-timeline-mode .sfja-app-timeline-print * {
                visibility: visible !important;
            }

            body.sfja-print-timeline-mode .sfja-app-timeline-print {
                position: absolute !important;
                inset: 0 auto auto 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                box-shadow: none !important;
                border: 0 !important;
            }

            body.sfja-print-timeline-mode .sfja-print-timeline-btn {
                display: none !important;
            }

            body.sfja-print-timeline-mode .sfja-app-timeline-card {
                box-shadow: none !important;
                border: 0 !important;
                border-radius: 0 !important;
                padding: 18px !important;
                background: #ffffff !important;
            }

            body.sfja-print-timeline-mode .sfja-app-timeline-body {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                box-shadow: none !important;
                background: #ffffff !important;
            }
        }

    </style>

<style id="sf-candidate-request-decision-colors">
    /*
     * Colored decision buttons — visual only.
     */

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]) {
        overflow: hidden !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
        border-color: rgba(34,197,94,.42) !important;
        color: #047857 !important;
        box-shadow: 0 12px 28px rgba(34,197,94,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: linear-gradient(135deg, #fef2f2, #fee2e2) !important;
        border-color: rgba(239,68,68,.38) !important;
        color: #b91c1c !important;
        box-shadow: 0 12px 28px rgba(239,68,68,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
        border-color: rgba(249,115,22,.38) !important;
        color: #c2410c !important;
        box-shadow: 0 12px 28px rgba(249,115,22,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]:checked) {
        transform: translateY(-1px) !important;
        filter: saturate(1.12) !important;
        box-shadow: 0 0 0 5px rgba(37,99,235,.10), 0 18px 38px rgba(15,23,42,.12) !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: rgba(6,78,59,.55) !important;
        border-color: rgba(52,211,153,.34) !important;
        color: #a7f3d0 !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: rgba(127,29,29,.48) !important;
        border-color: rgba(248,113,113,.34) !important;
        color: #fecaca !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: rgba(124,45,18,.48) !important;
        border-color: rgba(251,146,60,.34) !important;
        color: #fed7aa !important;
    }
</style>


    @php
        $currentStatus = (string) ($record->status ?? 'screening');
        $currentStatusLabel = match ($currentStatus) {
            'under_review' => 'Under Review',
            'client_submitted' => 'Client Submitted',
            'qualified' => 'Qualified',
            'hired' => 'Hired / Onboarding',
            'declined' => 'Declined',
            default => ucfirst(str_replace('_', ' ', $currentStatus)),
        };

        $isArchived = (bool) ($record->is_archived ?? false);

        $heroStyles = match ($currentStatus) {
            'screening' => [
                'accent' => 'linear-gradient(180deg, #facc15 0%, #f59e0b 100%)',
                'title' => '#7c3d00',
            ],
            'under_review' => [
                'accent' => 'linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%)',
                'title' => '#1d4ed8',
            ],
            'client_submitted' => [
                'accent' => 'linear-gradient(180deg, #14b8a6 0%, #0f766e 100%)',
                'title' => '#0f766e',
            ],
            'qualified' => [
                'accent' => 'linear-gradient(180deg, #0f172a 0%, #334155 100%)',
                'title' => '#0f172a',
            ],
            'hired' => [
                'accent' => 'linear-gradient(180deg, #22c55e 0%, #16a34a 100%)',
                'title' => '#15803d',
            ],
            'declined' => [
                'accent' => 'linear-gradient(180deg, #f59e0b 0%, #d97706 100%)',
                'title' => '#b45309',
            ],
            default => [
                'accent' => 'linear-gradient(180deg, #234b74 0%, #2f6aa3 100%)',
                'title' => '#234b74',
            ],
        };
    @endphp

    
<style id="sf-job-application-dark-hero-final">
    /*
     | Job Application View — restore dark premium header only.
     | This block targets only the top workflow hero/card.
     | It does not change body cards, candidate requests, files, timeline, or Livewire actions.
     */

    .sf-workflow-card {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .20), transparent 34%),
            linear-gradient(135deg, #071827 0%, #0b2742 42%, #123f63 100%) !important;
        border: 1px solid rgba(148, 163, 184, .18) !important;
        box-shadow: 0 26px 70px rgba(2, 8, 23, .24) !important;
    }

    .sf-workflow-card::before {
        background: var(--sf-status-accent, linear-gradient(180deg, #22d3ee 0%, #2563eb 100%)) !important;
        opacity: 1 !important;
    }

    .sf-workflow-title .kicker {
        color: rgba(226, 232, 240, .78) !important;
    }

    .sf-workflow-title h1 {
        color: #ffffff !important;
        text-shadow: 0 12px 34px rgba(0, 0, 0, .22);
    }

    .sf-current-status {
        background: rgba(255, 255, 255, .10) !important;
        border: 1px solid rgba(255, 255, 255, .20) !important;
        color: #ffffff !important;
        box-shadow: 0 16px 34px rgba(0, 0, 0, .16) !important;
        backdrop-filter: blur(14px);
    }

    .sf-pill-muted,
    .sf-pill-soft {
        background: rgba(255, 255, 255, .94) !important;
        color: #0f172a !important;
        border: 1px solid rgba(255, 255, 255, .20) !important;
    }

    .sf-pill-current {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        color: #ffffff !important;
        border: 1px solid rgba(147, 197, 253, .35) !important;
    }

    .sf-pill-warning {
        background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%) !important;
        color: #422006 !important;
    }

    .sf-pill-info {
        background: linear-gradient(135deg, #234b74 0%, #2563eb 100%) !important;
        color: #ffffff !important;
    }

    .sf-pill-primary {
        background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%) !important;
        color: #042f2e !important;
    }

    .sf-pill-success {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
        color: #052e16 !important;
    }

    .sf-pill-danger {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
        color: #78350f !important;
    }

    .sf-pill-dark {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        color: #ffffff !important;
    }
</style>


<div class="sf-workflow-shell">
        <section class="sf-workflow-card" style="--sf-status-accent: {{ $heroStyles['accent'] }}; --sf-status-title: {{ $heroStyles['title'] }};">
            <div class="sf-workflow-head">
                <div class="sf-workflow-title">
                    <div class="kicker">Job Applications</div>
                    <h1>
                        View<br>{{ $record->full_name ?? 'Applicant' }}
                    </h1>
                </div>

                <div class="sf-workflow-meta">
                    <div class="sf-current-status">
                        <span>Current Status</span>
                        <span>•</span>
                        <span>{{ $currentStatusLabel }}</span>
                    </div>
                </div>
            </div>

            <div class="sf-status-grid">
                <button type="button" wire:click="mountAction('set_screening')" class="sf-pill {{ $currentStatus === 'screening' ? 'sf-pill-current' : 'sf-pill-warning' }}">
                    Screening
                    <small>Initial internal screening stage</small>
                </button>

                <button type="button" wire:click="mountAction('set_under_review')" class="sf-pill {{ $currentStatus === 'under_review' ? 'sf-pill-current' : 'sf-pill-info' }}">
                    Under Review
                    <small>Recruitment review in progress</small>
                </button>

                <button type="button" wire:click="mountAction('set_client_submitted')" class="sf-pill {{ $currentStatus === 'client_submitted' ? 'sf-pill-current' : 'sf-pill-primary' }}">
                    Client Submitted
                    <small>Profile sent to client</small>
                </button>

                <button type="button" wire:click="mountAction('set_qualified')" class="sf-pill {{ $currentStatus === 'qualified' ? 'sf-pill-current' : 'sf-pill-muted' }}">
                    Qualified
                    <small>Candidate marked as qualified</small>
                </button>
            </div>

            <div class="sf-action-grid">
                <button type="button" wire:click="mountAction('set_hired')" class="sf-pill sf-pill-success">
                    Hire / Start Onboarding
                    <small>Move applicant into onboarding flow</small>
                </button>

                <button type="button" wire:click="mountAction('set_declined')" class="sf-pill sf-pill-danger">
                    Declined
                    <small>Decline and archive this application</small>
                </button>

                <button type="button" wire:click="mountAction('preHireCostSummary')" class="sf-pill sf-pill-soft">
                    Pre-Hire Cost Snapshot
                    <small>View candidate pre-hire costs</small>
                </button>

                <button type="button" wire:click="mountAction('create_candidate_request')" class="sf-pill sf-pill-soft">
                    Create Request
                    <small>Send request, files, or negotiation</small>
                </button>

                @if ($currentStatus === 'hired' || $isArchived)
                    <button type="button" wire:click="mountAction('reopen_application')" class="sf-pill sf-pill-dark">
                        Reopen Application
                        <small>Move candidate back into Job Applications</small>
                    </button>
                @endif

                <button type="button" wire:click="mountAction('edit')" class="sf-pill sf-pill-soft">
                    Edit Application
                    <small>Update applicant details</small>
                </button>
            </div>
        </section>



@php
    /*
     * Safe display variables for the clean Job Application body.
     * Header/actions are not touched.
     */
    $candidateName = $record->full_name
        ?? $record->candidate_name
        ?? $record->name
        ?? 'Applicant';

    $position = optional($record->job)->title
        ?? $record->position
        ?? $record->position_title
        ?? '-';

    $project = optional(optional($record->job)->project)->name ?? '-';
    $client = optional(optional(optional($record->job)->project)->client)->name ?? '-';

    $email = $record->email
        ?? $record->candidate_email
        ?? $record->applicant_email
        ?? '-';

    $phone = $record->phone
        ?? $record->phone_number
        ?? $record->mobile
        ?? $record->whatsapp_number
        ?? '-';

    $nationality = $record->nationality ?? '-';

    $yearsExperience = $record->years_of_experience
        ?? $record->years_of_experience_display
        ?? '-';

    $appliedAt = optional($record->created_at)->format('Y-m-d H:i') ?: '-';

    $workflow = $record->candidate_request_status
        ? ucfirst(str_replace('_', ' ', $record->candidate_request_status))
        : 'No Active Request';

    $source = $record->source
        ? ucfirst(str_replace('_', ' ', $record->source))
        : '-';

    $expectedSalary = $record->expected_salary ?? '-';
    $currentSalary = $record->current_salary ?? '-';
    $noticePeriod = $record->notice_period ?? '-';
    $updatedAt = optional($record->updated_at)->format('Y-m-d H:i') ?: '-';
    $notes = $record->notes ?? null;

    $extraAttributes = collect();
@endphp

<style id="sf-job-application-body-data-only">
    /*
     | Sada Fezzan ERP — Clean Job Application View Body
     | Header/action buttons are intentionally untouched.
     */
    .sfja-body {
        width: min(100%, 1280px);
        margin: 28px auto 72px;
        display: grid;
        gap: 22px;
        position: relative;
        z-index: 1;
    }

    .sfja-md-card,
    .sfja-app-timeline-card {
        width: min(100%, 1280px);
        margin-left: auto;
        margin-right: auto;
        border-radius: 30px;
        background: #ffffff !important;
        border: 1px solid rgba(15, 23, 42, .08);
        box-shadow: 0 18px 46px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .dark .sfja-md-card,
    .dark .sfja-app-timeline-card {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 36%),
            rgba(15, 23, 42, .82) !important;
        border-color: rgba(148, 163, 184, .18);
        box-shadow: 0 18px 46px rgba(0, 0, 0, .24);
    }

    .sfja-md-card-head,
    .sfja-app-timeline-head {
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

    .dark .sfja-md-card-head,
    .dark .sfja-app-timeline-head {
        border-bottom-color: rgba(148, 163, 184, .18);
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 32%),
            rgba(15, 23, 42, .44);
    }

    .sfja-md-title,
    .sfja-app-timeline-title {
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

    .sfja-app-timeline-title {
        font-size: clamp(26px, 2.6vw, 38px);
    }

    .dark .sfja-md-title,
    .dark .sfja-app-timeline-title {
        color: #ffffff;
    }

    .sfja-md-icon {
        font-family: 'Material Symbols Rounded';
        font-weight: 600;
        font-style: normal;
        font-size: 24px;
        line-height: 1;
        color: #1d4ed8;
        display: inline-flex;
    }

    .dark .sfja-md-icon {
        color: #22d3ee;
    }

    .sfja-status-chip,
    .sfja-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 900;
        color: #0f172a;
        background: rgba(224, 242, 254, .88);
        border: 1px solid rgba(37, 99, 235, .16);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .dark .sfja-status-chip,
    .dark .sfja-chip {
        color: #bfdbfe;
        background: rgba(37, 99, 235, .18);
        border-color: rgba(147, 197, 253, .18);
    }

    .sfja-md-grid {
        padding: 24px 26px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .sfja-md-item,
    .sfja-file-item,
    .sfja-answer-item {
        min-height: 104px;
        border-radius: 22px;
        padding: 18px;
        background: rgba(248, 250, 252, .82);
        border: 1px solid rgba(15, 23, 42, .08);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .dark .sfja-md-item,
    .dark .sfja-file-item,
    .dark .sfja-answer-item {
        background: rgba(15, 23, 42, .58);
        border-color: rgba(148, 163, 184, .16);
    }

    .sfja-md-label {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 11px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .dark .sfja-md-label {
        color: #94a3b8;
    }

    .sfja-md-value {
        color: #0f172a;
        font-size: 16px;
        line-height: 1.45;
        font-weight: 850;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .dark .sfja-md-value {
        color: #ffffff;
    }

    .sfja-notes-wrap {
        padding: 0 26px 26px;
    }

    .sfja-notes-box {
        border-radius: 22px;
        padding: 18px;
        color: #334155;
        background: rgba(248, 250, 252, .82);
        border: 1px solid rgba(15, 23, 42, .08);
        font-weight: 750;
        line-height: 1.7;
        white-space: pre-wrap;
    }

    .dark .sfja-notes-box {
        color: #cbd5e1;
        background: rgba(15, 23, 42, .58);
        border-color: rgba(148, 163, 184, .16);
    }

    .sfja-empty-box {
        margin: 24px 26px;
        padding: 20px;
        border-radius: 22px;
        border: 1px dashed rgba(15, 23, 42, .18);
        background: rgba(248, 250, 252, .68);
        color: #64748b;
        font-weight: 800;
    }

    .sfja-file-link {
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
        border: 1px solid rgba(37, 99, 235, .14);
        font-size: 12px;
        font-weight: 950;
    }

    .sfja-request-list {
        padding: 24px 26px;
        display: grid;
        gap: 14px;
    }

    .sfja-request-details {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 24px;
        overflow: hidden;
        background: rgba(255, 255, 255, .96);
    }

    .dark .sfja-request-details {
        background: rgba(15, 23, 42, .58);
        border-color: rgba(148, 163, 184, .16);
    }

    .sfja-request-summary {
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

    .sfja-request-summary::-webkit-details-marker {
        display: none;
    }

    .sfja-request-title {
        color: #0f172a;
        font-size: 17px;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .dark .sfja-request-title {
        color: #ffffff;
    }

    .sfja-request-body {
        padding: 20px;
        display: grid;
        gap: 14px;
        border-top: 1px solid rgba(15, 23, 42, .08);
    }

    .sfja-request-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 4px;
    }

    .sfja-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 16px;
        border-radius: 999px;
        border: 0;
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 950;
        cursor: pointer;
    }

    .sfja-btn-blue {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #ffffff !important;
    }

    .sfja-btn-gray {
        background: #f8fafc;
        color: #0f172a !important;
        border: 1px solid rgba(15, 23, 42, .10);
    }

    .sfja-btn-red {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #ffffff !important;
    }

    .sfja-app-timeline-card {
        margin-top: 0 !important;
        margin-bottom: 72px !important;
    }

    .sfja-app-timeline-body {
        background: #ffffff !important;
    }

    .sfja-request-timeline {
        position: relative;
        display: grid;
        gap: 14px;
        padding: 4px 0;
    }

    .sfja-request-step {
        position: relative;
        display: grid;
        grid-template-columns: 48px 1fr;
        gap: 14px;
        align-items: flex-start;
    }

    .sfja-request-step:not(:last-child)::before {
        content: "";
        position: absolute;
        left: 23px;
        top: 48px;
        bottom: -16px;
        width: 2px;
        border-radius: 999px;
        background: #dbeafe;
    }

    .sfja-request-step-icon {
        position: relative;
        z-index: 2;
        width: 48px;
        height: 48px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: 'Material Symbols Rounded';
        font-size: 25px;
        color: #1d4ed8;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .sfja-request-step-card {
        border: 1px solid rgba(15, 23, 42, .08);
        border-radius: 22px;
        background: #ffffff;
        padding: 16px 18px;
        box-shadow: 0 10px 24px rgba(15,23,42,.045);
    }

    .sfja-request-step-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        flex-wrap: wrap;
    }

    .sfja-request-step-title {
        color: #0f172a;
        font-weight: 950;
        font-size: 15px;
        letter-spacing: -.02em;
    }

    .sfja-request-step-text {
        margin-top: 8px;
        color: #334155;
        font-size: 14px;
        font-weight: 750;
        line-height: 1.55;
        white-space: pre-wrap;
    }

    .sfja-request-step-salary {
        margin-top: 10px;
        display: inline-flex;
        width: fit-content;
        border-radius: 999px;
        padding: 7px 12px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #bbf7d0;
        font-size: 13px;
        font-weight: 950;
    }

    .sfja-request-step-date {
        color: #64748b;
        font-size: 12px;
        font-weight: 850;
        white-space: nowrap;
    }

    .sfja-request-pill-success {
        background: #ecfdf5;
        color: #047857;
        border-color: #bbf7d0;
    }

    .sfja-request-pill-warning {
        background: #fff7ed;
        color: #c2410c;
        border-color: #fdba74;
    }

    .sfja-request-pill-danger {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .sfja-app-timeline-list {
        max-height: 560px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .sfja-app-timeline-list::-webkit-scrollbar {
        width: 8px;
    }

    .sfja-app-timeline-list::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #bfdbfe;
    }

    .dark .sfja-request-step-card {
        background: rgba(15,23,42,.72);
        border-color: rgba(148,163,184,.18);
    }

    .dark .sfja-request-step-title {
        color: #ffffff;
    }

    .dark .sfja-request-step-text {
        color: #cbd5e1;
    }


    @media (max-width: 1100px) {
        .sfja-md-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .sfja-md-grid {
            grid-template-columns: 1fr;
            padding: 18px;
        }

        .sfja-md-card-head,
        .sfja-notes-wrap,
        .sfja-request-list {
            padding-left: 18px;
            padding-right: 18px;
        }
    }
</style>

@php
    /*
     | Clean display collections for the Job Application view.
     | Header/action buttons are intentionally untouched.
     | Files are separated from answers and deduplicated by path.
     */
    $sfLooksLikeFile = function ($label, $value = null): bool {
        $labelText = strtolower((string) $label);
        $valueText = strtolower((string) $value);

        return str_contains($labelText, 'cv')
            || str_contains($labelText, 'resume')
            || str_contains($labelText, 'file')
            || str_contains($labelText, 'upload')
            || str_contains($labelText, 'path')
            || str_contains($labelText, 'certificate')
            || str_contains($labelText, 'cert')
            || str_contains($labelText, 'passport')
            || str_contains($labelText, 'contract')
            || str_contains($valueText, '/')
            || preg_match('/\.(pdf|rtf|doc|docx|jpg|jpeg|png|webp)$/i', (string) $valueText);
    };

    $sfPrettyFileTitle = function ($label, $path = null) use ($candidateName) {
        $labelText = strtolower((string) $label);
        $pathText = strtolower((string) $path);
        $combined = trim($labelText . ' ' . $pathText);

        if (str_contains($combined, 'atex')) {
            return 'ATEX Certificate';
        }

        if (str_contains($combined, 'contract')) {
            return 'Contract';
        }

        if (str_contains($combined, 'passport')) {
            return 'Passport Copy';
        }

        if (str_contains($combined, 'medical')) {
            return 'Medical Document';
        }

        if (str_contains($combined, 'certificate') || str_contains($combined, 'cert')) {
            return 'Certificate';
        }

        if (str_contains($combined, 'cv') || str_contains($combined, 'resume')) {
            return 'Candidate CV';
        }

        return filled($label) ? ucfirst(str_replace(['_', '-'], ' ', (string) $label)) : 'Uploaded File';
    };

    $sfNormalizeFilePath = function ($path) {
        return trim((string) $path);
    };

    $sfApplicationAnswers = collect($record->values ?? [])
        ->mapWithKeys(function ($value) use ($sfLooksLikeFile) {
            $field = $value->field ?? null;

            $label = $field?->label
                ?? $field?->field_label
                ?? $field?->name
                ?? 'Answer';

            $answer = $value->value
                ?? $value->answer
                ?? $value->field_value
                ?? null;

            if (is_array($answer)) {
                $answer = implode(', ', array_filter($answer));
            }

            if (! filled($answer) || $sfLooksLikeFile($label, $answer)) {
                return [];
            }

            return [$label => $answer];
        })
        ->filter(fn ($value) => filled($value));

    $sfApplicationFiles = collect();
    $sfSeenFilePaths = [];

    $sfAddFile = function ($title, $category, $path, $source = 'Job Application') use (&$sfApplicationFiles, &$sfSeenFilePaths, $sfNormalizeFilePath, $sfPrettyFileTitle) {
        $cleanPath = $sfNormalizeFilePath($path);

        if (! filled($cleanPath)) {
            return;
        }

        $key = strtolower($cleanPath);

        if (isset($sfSeenFilePaths[$key])) {
            return;
        }

        $sfSeenFilePaths[$key] = true;

        $sfApplicationFiles->push([
            'title' => $sfPrettyFileTitle($title, $cleanPath),
            'category' => $category ?: $source,
            'path' => $cleanPath,
            'url' => \Illuminate\Support\Facades\Storage::url($cleanPath),
            'source' => $source,
        ]);
    };

    try {
        if (method_exists($record, 'applicationFilePayloads')) {
            foreach (collect($record->applicationFilePayloads()) as $file) {
                $path = $file['file_path'] ?? $file['path'] ?? null;
                $sfAddFile(
                    $file['title'] ?? $file['label'] ?? $file['name'] ?? 'Uploaded File',
                    $file['category'] ?? $file['type'] ?? 'Job Application',
                    $path,
                    'Job Application'
                );
            }
        }
    } catch (\Throwable $e) {
        //
    }

    foreach (['cv_path', 'cv_file', 'resume_path', 'file_path', 'attachment_path'] as $fileColumn) {
        if (filled($record->{$fileColumn} ?? null)) {
            $sfAddFile($fileColumn, 'Job Application', $record->{$fileColumn}, 'Job Application');
        }
    }

    foreach (collect($record->values ?? []) as $value) {
        $field = $value->field ?? null;

        $label = $field?->label
            ?? $field?->field_label
            ?? $field?->name
            ?? 'Uploaded File';

        $answer = $value->value
            ?? $value->answer
            ?? $value->field_value
            ?? null;

        if (is_array($answer)) {
            foreach ($answer as $oneAnswer) {
                if ($sfLooksLikeFile($label, $oneAnswer)) {
                    $sfAddFile($label, 'Job Application Answer', $oneAnswer, 'Job Application');
                }
            }
        } elseif ($sfLooksLikeFile($label, $answer)) {
            $sfAddFile($label, 'Job Application Answer', $answer, 'Job Application');
        }
    }

    foreach ($this->candidateRequests as $requestForFiles) {
        $decodedForFiles = json_decode((string) $requestForFiles->candidate_response, true);
        $decodedForFiles = is_array($decodedForFiles) ? $decodedForFiles : [];

        $requestUploadedFiles = is_array($decodedForFiles['uploaded_files'] ?? null)
            ? $decodedForFiles['uploaded_files']
            : [];

        foreach ($requestUploadedFiles as $requestFile) {
            $requestFilePath = $requestFile['path']
                ?? $requestFile['file_path']
                ?? $requestFile['stored_path']
                ?? null;

            $sfAddFile(
                $requestFile['item_label']
                    ?? $requestFile['label']
                    ?? $requestFile['title']
                    ?? $requestFile['name']
                    ?? $requestFile['original_name']
                    ?? $requestForFiles->title
                    ?? 'Candidate Request File',
                'Candidate Request: ' . ($requestForFiles->title ?: 'Request'),
                $requestFilePath,
                'Candidate Request'
            );
        }
    }
@endphp


<style id="sf-force-job-application-dark-hero-final">
    .sf-workflow-card {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .16), transparent 36%),
            linear-gradient(135deg, #0f172a 0%, #12345a 46%, #234b74 100%) !important;
        border-color: rgba(34, 211, 238, .22) !important;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .22) !important;
    }

    .sf-workflow-card .kicker,
    .sf-workflow-card .sf-workflow-title .kicker {
        color: rgba(226, 232, 240, .78) !important;
    }

    .sf-workflow-card h1,
    .sf-workflow-card .sf-workflow-title h1 {
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        background: none !important;
    }

    .sf-workflow-card .sf-current-status {
        background: rgba(255, 255, 255, .96) !important;
        color: #0f172a !important;
        border-color: rgba(226, 232, 240, .88) !important;
    }
</style>


<div class="sfja-body">
    <section class="sfja-md-card">
        <div class="sfja-md-card-head">
            <h2 class="sfja-md-title">
                <span class="sfja-md-icon">assignment_ind</span>
                Applicant Details
            </h2>

            <span class="sfja-status-chip">{{ $currentStatusLabel }}</span>
        </div>

        <div class="sfja-md-grid">
            <div class="sfja-md-item">
                <div class="sfja-md-label">Full Name</div>
                <div class="sfja-md-value">{{ $candidateName }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Position</div>
                <div class="sfja-md-value">{{ $position }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Project</div>
                <div class="sfja-md-value">{{ $project }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Client</div>
                <div class="sfja-md-value">{{ $client }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Email</div>
                <div class="sfja-md-value">{{ $email }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Phone</div>
                <div class="sfja-md-value">{{ $phone }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Nationality</div>
                <div class="sfja-md-value">{{ $nationality }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Years of Experience</div>
                <div class="sfja-md-value">{{ $yearsExperience }}</div>
            </div>

            <div class="sfja-md-item">
                <div class="sfja-md-label">Applied At</div>
                <div class="sfja-md-value">{{ $appliedAt }}</div>
            </div>
        </div>
    </section>

    <section class="sfja-md-card">
        <div class="sfja-md-card-head">
            <h2 class="sfja-md-title">
                <span class="sfja-md-icon">fact_check</span>
                Application Answers
            </h2>

            <span class="sfja-status-chip">{{ $sfApplicationAnswers->count() }} Answers</span>
        </div>

        @if($sfApplicationAnswers->isNotEmpty())
            <div class="sfja-md-grid">
                @foreach($sfApplicationAnswers as $label => $answer)
                    <div class="sfja-answer-item">
                        <div class="sfja-md-label">{{ $label }}</div>
                        <div class="sfja-md-value">{{ $answer }}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sfja-empty-box">No custom application answers were submitted.</div>
        @endif
    </section>

    <section class="sfja-md-card">
        <div class="sfja-md-card-head">
            <h2 class="sfja-md-title">
                <span class="sfja-md-icon">folder_open</span>
                Uploaded Files
            </h2>

            <span class="sfja-status-chip">{{ $sfApplicationFiles->count() }} Files</span>
        </div>

        @if($sfApplicationFiles->isNotEmpty())
            <div class="sfja-md-grid">
                @foreach($sfApplicationFiles as $file)
                    <div class="sfja-file-item">
                        <div class="sfja-md-label">{{ $file['category'] ?? 'File' }}</div>
                        <div class="sfja-md-value">
                            {{ $file['title'] ?? 'Uploaded File' }}
                            @if(!empty($file['source']))
                                <br><span style="font-size:13px;color:#64748b;">{{ $file['source'] }}</span>
                            @endif
                        </div>

                        @if(!empty($file['url']))
                            <a class="sfja-file-link" href="{{ $file['url'] }}" target="_blank">
                                Open File
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="sfja-empty-box">No uploaded files were found for this application.</div>
        @endif
    </section>

    <section class="sfja-md-card">
        <div class="sfja-md-card-head">
            <h2 class="sfja-md-title">
                <span class="sfja-md-icon">assignment</span>
                Candidate Requests
            </h2>

            <span class="sfja-status-chip">{{ $this->candidateRequests->count() }} Requests</span>
        </div>

        @if($this->candidateRequests->count())
            <div class="sfja-request-list">
                @foreach($this->candidateRequests as $request)
                    @php
                        $decoded = json_decode((string) $request->candidate_response, true);
                        $decoded = is_array($decoded) ? $decoded : [];

                        $uploadedFiles = is_array($decoded['uploaded_files'] ?? null) ? $decoded['uploaded_files'] : [];
                        $noteResponses = is_array($decoded['note_responses'] ?? null) ? $decoded['note_responses'] : [];
                        $thread = is_array($decoded['thread'] ?? null) ? $decoded['thread'] : [];

                        $requestTypeLabel = ucfirst(str_replace('_', ' ', (string) $request->type));
                        $requestStatusLabel = ucfirst(str_replace('_', ' ', (string) $request->request_status));
                        $requestPortalUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $request->public_token;
                    @endphp

                    <details class="sfja-request-details">
                        <summary class="sfja-request-summary">
                            <div>
                                <div class="sfja-request-title">{{ $request->title ?: 'Candidate Request' }}</div>
                                <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:8px;">
                                    <span class="sfja-chip">{{ $requestTypeLabel }}</span>
                                    <span class="sfja-chip">{{ $requestStatusLabel }}</span>
                                    @if($request->due_date)
                                        <span class="sfja-chip">Due {{ optional($request->due_date)->format('M j, Y') }}</span>
                                    @endif
                                </div>
                            </div>

                            <span class="sfja-md-icon">expand_more</span>
                        </summary>

                        <div class="sfja-request-body">
                            @if(filled($request->notes))
                                <div class="sfja-notes-box">{{ $request->notes }}</div>
                            @endif

                            @if($request->items->count())
                                <div class="sfja-md-grid" style="padding:0;grid-template-columns:repeat(2,minmax(0,1fr));">
                                    @foreach($request->items as $item)
                                        <div class="sfja-md-item">
                                            <div class="sfja-md-label">
                                                {{ ($item->item_type ?? 'file') === 'note' ? 'Requested Note' : 'Requested File' }}
                                            </div>
                                            <div class="sfja-md-value">
                                                {{ $item->label ?: '-' }}
                                                @if($item->file_format)
                                                    <br><span style="font-size:13px;color:#64748b;">{{ ucfirst(str_replace('_', ' ', $item->file_format)) }}</span>
                                                @endif
                                                <br><span style="font-size:13px;color:#64748b;">{{ $item->is_required ? 'Required' : 'Optional' }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($uploadedFiles))
                                <div class="sfja-md-grid" style="padding:0;grid-template-columns:repeat(2,minmax(0,1fr));">
                                    @foreach($uploadedFiles as $file)
                                        @php
                                            $filePath = $file['path']
                                                ?? $file['file_path']
                                                ?? $file['stored_path']
                                                ?? null;

                                            $fileUrl = $filePath ? \Illuminate\Support\Facades\Storage::url($filePath) : null;
                                        @endphp
                                        <div class="sfja-file-item">
                                            <div class="sfja-md-label">Candidate Uploaded File</div>
                                            <div class="sfja-md-value">{{ $sfPrettyFileTitle($file['item_label'] ?? $file['label'] ?? $file['name'] ?? $file['title'] ?? $file['original_name'] ?? 'Uploaded File', $filePath) }}</div>
                                            @if($fileUrl)
                                                <a class="sfja-file-link" href="{{ $fileUrl }}" target="_blank">Open File</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($noteResponses))
                                <div class="sfja-md-grid" style="padding:0;grid-template-columns:repeat(2,minmax(0,1fr));">
                                    @foreach($noteResponses as $note)
                                        <div class="sfja-answer-item">
                                            <div class="sfja-md-label">{{ $note['label'] ?? 'Candidate Note' }}</div>
                                            <div class="sfja-md-value">{{ $note['value'] ?? $note['response'] ?? '-' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @php
                                $requestTimelineRows = collect($thread);

                                if ($requestTimelineRows->isEmpty()) {
                                    $requestTimelineRows->push([
                                        'sender' => 'hr',
                                        'event' => 'request_created',
                                        'title' => $request->title,
                                        'message' => $request->notes,
                                        'salary' => $request->proposed_salary,
                                        'currency' => $request->currency,
                                        'created_at' => optional($request->created_at)?->toDateTimeString(),
                                    ]);
                                }

                                $requestTimelineRows = $requestTimelineRows
                                    ->filter(fn ($entry) => is_array($entry))
                                    ->values();

                                $requestEventIcon = function ($entry) {
                                    $event = strtolower((string) ($entry['event'] ?? ''));
                                    $sender = strtolower((string) ($entry['sender'] ?? ''));

                                    if (str_contains($event, 'approved') || str_contains($event, 'accepted')) {
                                        return 'check_circle';
                                    }

                                    if (str_contains($event, 'declined') || str_contains($event, 'rejected')) {
                                        return 'cancel';
                                    }

                                    if (str_contains($event, 'reconsider') || str_contains($event, 'counter')) {
                                        return 'change_circle';
                                    }

                                    if (str_contains($event, 'final')) {
                                        return 'verified';
                                    }

                                    if (! empty($entry['salary'])) {
                                        return 'payments';
                                    }

                                    return $sender === 'candidate' ? 'person' : 'admin_panel_settings';
                                };

                                $requestEventTone = function ($entry) {
                                    $event = strtolower((string) ($entry['event'] ?? ''));

                                    if (str_contains($event, 'approved') || str_contains($event, 'accepted')) {
                                        return 'sfja-request-pill-success';
                                    }

                                    if (str_contains($event, 'declined') || str_contains($event, 'rejected')) {
                                        return 'sfja-request-pill-danger';
                                    }

                                    if (str_contains($event, 'reconsider') || str_contains($event, 'counter')) {
                                        return 'sfja-request-pill-warning';
                                    }

                                    return '';
                                };

                                $requestEventLabel = function ($entry) {
                                    $event = $entry['event'] ?? 'update';

                                    return ucfirst(str_replace('_', ' ', (string) $event));
                                };
                            @endphp

                            @if($requestTimelineRows->isNotEmpty())
                                <div class="sfja-request-timeline">
                                    @foreach($requestTimelineRows as $entry)
                                        @php
                                            $senderLabel = ucfirst((string) ($entry['sender'] ?? 'update'));
                                            $eventLabel = $requestEventLabel($entry);
                                            $messageText = $entry['message'] ?? $entry['title'] ?? null;
                                            $createdText = null;

                                            try {
                                                $createdText = ! empty($entry['created_at'])
                                                    ? \Carbon\Carbon::parse($entry['created_at'])->format('Y-m-d H:i')
                                                    : null;
                                            } catch (\Throwable $e) {
                                                $createdText = null;
                                            }
                                        @endphp

                                        <div class="sfja-request-step">
                                            <div class="sfja-request-step-icon">{{ $requestEventIcon($entry) }}</div>

                                            <div class="sfja-request-step-card">
                                                <div class="sfja-request-step-top">
                                                    <div class="sfja-request-step-title">{{ $senderLabel }} · {{ $eventLabel }}</div>

                                                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                                        <span class="sfja-chip {{ $requestEventTone($entry) }}">{{ $eventLabel }}</span>

                                                        @if($createdText)
                                                            <span class="sfja-request-step-date">{{ $createdText }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if(filled($messageText))
                                                    <div class="sfja-request-step-text">{{ $messageText }}</div>
                                                @endif

                                                @if(!empty($entry['salary']))
                                                    <div class="sfja-request-step-salary">
                                                        {{ number_format((float) $entry['salary'], 2) }} {{ $entry['currency'] ?? $request->currency ?? '' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="sfja-request-actions">
                                <a href="{{ $requestPortalUrl }}" target="_blank" class="sfja-btn sfja-btn-gray">Open Portal</a>

                                <button type="button" wire:click="resendCandidateRequestEmail({{ $request->id }})" class="sfja-btn sfja-btn-blue">
                                    Resend Email
                                </button>

                                <button type="button" wire:click="mountAction('deleteCandidateRequestAction', { request_id: {{ $request->id }} })" class="sfja-btn sfja-btn-red">
                                    Delete Request
                                </button>
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        @else
            <div class="sfja-empty-box">No candidate requests created yet.</div>
        @endif
    </section>
</div>

<!-- SF APPLICATION FULL TIMELINE START -->
@php
    
    

    $sfTimelineRows = collect();

    $sfPushTimeline = function ($type, $title, $subtitle = null, $date = null, array $meta = []) use (&$sfTimelineRows) {
        if (! $date) {
            return;
        }

        try {
            $time = $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date);
        } catch (\Throwable $e) {
            return;
        }

        $sfTimelineRows->push([
            'type' => $type ?: 'update',
            'title' => $title ?: 'Update',
            'subtitle' => $subtitle,
            'date' => $time,
            'meta' => $meta,
        ]);
    };

    $sfColumn = function ($row, array $columns, $default = null) {
        foreach ($columns as $column) {
            if (is_object($row) && property_exists($row, $column) && filled($row->{$column})) {
                return $row->{$column};
            }
        }

        return $default;
    };

    $sfHasTable = fn ($table) => \Illuminate\Support\Facades\Schema::hasTable($table);
    $sfHasColumn = fn ($table, $column) => \Illuminate\Support\Facades\Schema::hasColumn($table, $column);

    $sfApplicationName = $record->full_name
        ?? $record->candidate_name
        ?? $record->name
        ?? 'Candidate';

    $sfPushTimeline(
        'application',
        'Application Created',
        'Job application record was created for ' . $sfApplicationName . '.',
        $record->created_at ?? now(),
        ['Application', $record->status ? str_replace('_', ' ', ucfirst($record->status)) : null]
    );

    if (! empty($record->updated_at) && (string) $record->updated_at !== (string) ($record->created_at ?? null)) {
        $sfPushTimeline(
            'update',
            'Application Updated',
            'Candidate/application profile was updated in the ERP.',
            $record->updated_at,
            ['Application Update']
        );
    }

    try {
        if ($sfHasTable('candidate_requests')) {
            $requestForeignKey = $sfHasColumn('candidate_requests', 'job_application_id')
                ? 'job_application_id'
                : ($sfHasColumn('candidate_requests', 'application_id') ? 'application_id' : null);

            if ($requestForeignKey) {
                $candidateRequestsForTimeline = \Illuminate\Support\Facades\DB::table('candidate_requests')
                    ->where($requestForeignKey, $record->id)
                    ->orderBy('created_at')
                    ->get();

                foreach ($candidateRequestsForTimeline as $timelineRequest) {
                    $requestId = $timelineRequest->id ?? null;
                    $requestTitle = $sfColumn($timelineRequest, ['title', 'subject', 'name'], 'Candidate Request');
                    $requestType = $sfColumn($timelineRequest, ['request_type', 'type', 'category'], 'request');
                    $requestStatus = $sfColumn($timelineRequest, ['request_status', 'status'], null);
                    $requestDue = $sfColumn($timelineRequest, ['due_date', 'deadline'], null);

                    $requestMeta = array_filter([
                        $requestType ? str_replace('_', ' ', ucfirst($requestType)) : null,
                        $requestStatus ? str_replace('_', ' ', ucfirst($requestStatus)) : null,
                        $requestDue ? 'Due ' . \Carbon\Carbon::parse($requestDue)->format('M j, Y') : null,
                    ]);

                    $sfPushTimeline(
                        'request',
                        'Request Created: ' . $requestTitle,
                        'A candidate request was created and linked to this application.',
                        $timelineRequest->created_at ?? null,
                        $requestMeta
                    );

                    if ($requestStatus) {
                        $statusLower = strtolower((string) $requestStatus);
                        if (in_array($statusLower, ['submitted', 'accepted', 'approved', 'declined', 'reconsidered', 'closed'], true)) {
                            $sfPushTimeline(
                                'decision',
                                'Request Status: ' . str_replace('_', ' ', ucfirst($requestStatus)),
                                $requestTitle,
                                $timelineRequest->updated_at ?? $timelineRequest->created_at ?? null,
                                $requestMeta
                            );
                        }
                    }

                    $candidateMessage = $sfColumn($timelineRequest, ['candidate_response_text', 'candidate_message', 'response_text', 'message'], null);
                    if ($candidateMessage) {
                        $sfPushTimeline(
                            'message',
                            'Candidate Response Submitted',
                            \Illuminate\Support\Str::limit(strip_tags((string) $candidateMessage), 180),
                            $timelineRequest->updated_at ?? $timelineRequest->created_at ?? null,
                            ['Candidate Reply']
                        );
                    }

                    $counterOffer = $sfColumn($timelineRequest, ['counter_offer', 'counter_offer_amount', 'candidate_counter_offer', 'proposed_salary'], null);
                    if ($counterOffer) {
                        $currency = $sfColumn($timelineRequest, ['currency', 'salary_currency'], '');
                        $sfPushTimeline(
                            'salary',
                            'Salary Negotiation / Counter Offer',
                            'Candidate proposed salary: ' . trim($counterOffer . ' ' . $currency),
                            $timelineRequest->updated_at ?? $timelineRequest->created_at ?? null,
                            ['Salary Negotiation']
                        );
                    }

                    foreach (['candidate_request_items', 'candidate_request_files', 'candidate_request_uploads'] as $childTable) {
                        if ($requestId && $sfHasTable($childTable) && $sfHasColumn($childTable, 'candidate_request_id')) {
                            $items = \Illuminate\Support\Facades\DB::table($childTable)
                                ->where('candidate_request_id', $requestId)
                                ->orderBy('created_at')
                                ->get();

                            foreach ($items as $item) {
                                $itemTitle = $sfColumn($item, ['title', 'label', 'file_name', 'filename', 'name'], ucfirst(str_replace('_', ' ', $childTable)));
                                $itemStatus = $sfColumn($item, ['status', 'response_status'], null);
                                $itemValue = $sfColumn($item, ['response', 'response_text', 'value', 'file_path', 'path'], null);

                                $sfPushTimeline(
                                    str_contains($childTable, 'file') || str_contains($childTable, 'upload') ? 'file' : 'item',
                                    str_contains($childTable, 'file') || str_contains($childTable, 'upload')
                                        ? 'File Uploaded: ' . $itemTitle
                                        : 'Request Item Updated: ' . $itemTitle,
                                    $itemValue ? \Illuminate\Support\Str::limit(strip_tags((string) $itemValue), 150) : null,
                                    $item->updated_at ?? $item->created_at ?? null,
                                    array_filter([
                                        str_contains($childTable, 'file') || str_contains($childTable, 'upload') ? 'File' : 'Request Item',
                                        $itemStatus ? str_replace('_', ' ', ucfirst($itemStatus)) : null,
                                    ])
                                );
                            }
                        }
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        $sfPushTimeline(
            'warning',
            'Timeline Notice',
            'Some request details could not be loaded safely.',
            now(),
            ['System']
        );
    }

    try {
        foreach (['job_application_files', 'application_files', 'candidate_files'] as $fileTable) {
            if ($sfHasTable($fileTable)) {
                $fileForeignKey = $sfHasColumn($fileTable, 'job_application_id')
                    ? 'job_application_id'
                    : ($sfHasColumn($fileTable, 'application_id') ? 'application_id' : null);

                if ($fileForeignKey) {
                    $files = \Illuminate\Support\Facades\DB::table($fileTable)
                        ->where($fileForeignKey, $record->id)
                        ->orderBy('created_at')
                        ->get();

                    foreach ($files as $fileRow) {
                        $fileName = $sfColumn($fileRow, ['title', 'file_name', 'filename', 'name', 'type'], 'Uploaded File');
                        $fileType = $sfColumn($fileRow, ['file_type', 'type', 'category'], 'File');

                        $sfPushTimeline(
                            'file',
                            'File Uploaded: ' . $fileName,
                            $fileType,
                            $fileRow->created_at ?? $fileRow->updated_at ?? null,
                            ['File']
                        );
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        //
    }

    $sfTimelineRows = $sfTimelineRows
        ->filter(fn ($row) => ! empty($row['date']))
        ->sortByDesc(fn ($row) => $row['date']->timestamp)
        ->values();

    $sfTimelineIcon = function ($type) {
        return match ($type) {
            'salary' => 'payments',
            'file' => 'description',
            'request' => 'assignment',
            'decision' => 'verified',
            'message' => 'chat',
            'application' => 'badge',
            'warning' => 'warning',
            default => 'update',
        };
    };
@endphp

<section class="sfja-app-timeline-card sfja-app-timeline-print" style="background:#ffffff;">
    <div class="sfja-app-timeline-head">
        <div>
            <h2 class="sfja-app-timeline-title">Complete Application Timeline</h2>
            <div class="sfja-app-timeline-subtitle">
                Full history of application updates, salary negotiation, candidate requests, decisions, files, and portal activity.
            </div>
        </div>

        <button type="button" class="sfja-print-timeline-btn" onclick="document.body.classList.add('sfja-print-timeline-mode'); window.print(); setTimeout(function(){ document.body.classList.remove('sfja-print-timeline-mode'); }, 600);">
            Print Timeline
        </button>
    </div>

    @if($sfTimelineRows->isNotEmpty())
        <div class="sfja-app-timeline-list">
            @foreach($sfTimelineRows as $timelineRow)
                <div class="sfja-app-timeline-row">
                    <div class="sfja-app-timeline-icon-wrap">
                        <div class="sfja-app-timeline-icon">{{ $sfTimelineIcon($timelineRow['type'] ?? 'update') }}</div>
                    </div>

                    <div class="sfja-app-timeline-body">
                        <div class="sfja-app-timeline-top">
                            <div class="sfja-app-timeline-event-title">{{ $timelineRow['title'] }}</div>
                            <div class="sfja-app-timeline-date">{{ $timelineRow['date']->format('Y-m-d H:i') }}</div>
                        </div>

                        @if(! empty($timelineRow['subtitle']))
                            <div class="sfja-app-timeline-event-subtitle">{{ $timelineRow['subtitle'] }}</div>
                        @endif

                        @if(! empty($timelineRow['meta']))
                            <div class="sfja-app-timeline-tags">
                                @foreach(array_filter($timelineRow['meta']) as $tag)
                                    <span class="sfja-app-timeline-tag {{ str_contains(strtolower($tag), 'approved') || str_contains(strtolower($tag), 'accepted') ? 'is-status' : '' }}">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="sfja-app-timeline-empty">
            No timeline updates found for this application yet.
        </div>
    @endif
</section>
<!-- SF APPLICATION FULL TIMELINE END -->


<!-- SF JOB APPLICATION BODY DATA END -->


</x-filament-panels::page>


<style id="sf-candidate-request-material-polish">
    /*
     * Sada Fezzan ERP — Candidate Request / Negotiation Response polish
     * Visual-only patch. No workflow logic changed.
     */

    .sf-candidate-request-material-scope,
    form:has(textarea[name*="negotiation"]),
    form:has(textarea[name*="candidate"]),
    form:has(textarea[name*="response"]) {
        --sf-primary: #2563eb;
        --sf-teal: #14b8a6;
        --sf-success: #16a34a;
        --sf-danger: #dc2626;
        --sf-warning: #d97706;
        --sf-ink: #0f172a;
        --sf-muted: #64748b;
        --sf-border: rgba(15, 23, 42, .10);
        --sf-soft: rgba(248, 250, 252, .92);
    }

    form:has(textarea[name*="negotiation"]) textarea,
    form:has(textarea[name*="candidate"]) textarea,
    form:has(textarea[name*="response"]) textarea,
    textarea[name*="negotiation"],
    textarea[name*="candidate"],
    textarea[name*="response"],
    textarea[name*="notes"] {
        width: 100% !important;
        min-height: 138px !important;
        resize: vertical !important;
        border-radius: 22px !important;
        border: 1px solid rgba(15, 23, 42, .12) !important;
        background:
            radial-gradient(circle at top right, rgba(20, 184, 166, .08), transparent 34%),
            rgba(255, 255, 255, .94) !important;
        color: #0f172a !important;
        padding: 16px 18px !important;
        font-size: 15px !important;
        line-height: 1.55 !important;
        font-weight: 650 !important;
        outline: none !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.75), 0 12px 28px rgba(15,23,42,.045) !important;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease !important;
    }

    form:has(textarea[name*="negotiation"]) textarea:focus,
    form:has(textarea[name*="candidate"]) textarea:focus,
    form:has(textarea[name*="response"]) textarea:focus,
    textarea[name*="negotiation"]:focus,
    textarea[name*="candidate"]:focus,
    textarea[name*="response"]:focus,
    textarea[name*="notes"]:focus {
        border-color: rgba(37, 99, 235, .42) !important;
        box-shadow: 0 0 0 5px rgba(37, 99, 235, .10), 0 16px 34px rgba(15,23,42,.08) !important;
        transform: translateY(-1px) !important;
    }

    form:has(textarea[name*="negotiation"]) textarea::placeholder,
    form:has(textarea[name*="candidate"]) textarea::placeholder,
    form:has(textarea[name*="response"]) textarea::placeholder,
    textarea[name*="negotiation"]::placeholder,
    textarea[name*="candidate"]::placeholder,
    textarea[name*="response"]::placeholder,
    textarea[name*="notes"]::placeholder {
        color: #94a3b8 !important;
        font-weight: 650 !important;
    }

    form:has(textarea[name*="negotiation"]) label,
    form:has(textarea[name*="candidate"]) label,
    form:has(textarea[name*="response"]) label {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        letter-spacing: -.015em !important;
    }

    form:has(textarea[name*="negotiation"]) input[type="radio"],
    form:has(textarea[name*="candidate"]) input[type="radio"],
    form:has(textarea[name*="response"]) input[type="radio"] {
        position: absolute !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]) {
        position: relative !important;
        min-height: 58px !important;
        border-radius: 20px !important;
        border: 1px solid rgba(15,23,42,.12) !important;
        background: rgba(255,255,255,.88) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 20px !important;
        cursor: pointer !important;
        color: #334155 !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        box-shadow: 0 12px 28px rgba(15,23,42,.045) !important;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]):hover,
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]):hover,
    form:has(textarea[name*="response"]) label:has(input[type="radio"]):hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 16px 34px rgba(15,23,42,.08) !important;
        border-color: rgba(37,99,235,.24) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]:checked) {
        background: linear-gradient(135deg, rgba(236,253,245,.98), rgba(240,253,250,.96)) !important;
        border-color: rgba(34,197,94,.48) !important;
        color: #047857 !important;
        box-shadow: 0 0 0 5px rgba(34,197,94,.10), 0 16px 34px rgba(15,23,42,.08) !important;
    }

    form:has(textarea[name*="negotiation"]) button[type="submit"],
    form:has(textarea[name*="candidate"]) button[type="submit"],
    form:has(textarea[name*="response"]) button[type="submit"],
    button[type="submit"]:has(span),
    .sf-submit-response-btn {
        min-height: 54px !important;
        border-radius: 999px !important;
        border: 0 !important;
        padding: 0 24px !important;
        background: linear-gradient(135deg, #14b8a6, #2563eb) !important;
        color: #ffffff !important;
        font-size: 15px !important;
        font-weight: 950 !important;
        box-shadow: 0 18px 38px rgba(37,99,235,.18) !important;
        cursor: pointer !important;
        transition: transform .18s ease, box-shadow .18s ease, filter .18s ease !important;
    }

    form:has(textarea[name*="negotiation"]) button[type="submit"]:hover,
    form:has(textarea[name*="candidate"]) button[type="submit"]:hover,
    form:has(textarea[name*="response"]) button[type="submit"]:hover,
    button[type="submit"]:has(span):hover,
    .sf-submit-response-btn:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 22px 46px rgba(37,99,235,.23) !important;
        filter: saturate(1.08) !important;
    }

    .dark form:has(textarea[name*="negotiation"]) textarea,
    .dark form:has(textarea[name*="candidate"]) textarea,
    .dark form:has(textarea[name*="response"]) textarea,
    .dark textarea[name*="negotiation"],
    .dark textarea[name*="candidate"],
    .dark textarea[name*="response"],
    .dark textarea[name*="notes"] {
        background: rgba(15,23,42,.86) !important;
        border-color: rgba(148,163,184,.18) !important;
        color: #f8fafc !important;
        box-shadow: 0 14px 32px rgba(0,0,0,.24) !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"]) {
        background: rgba(15,23,42,.78) !important;
        border-color: rgba(148,163,184,.18) !important;
        color: #e2e8f0 !important;
    }
</style>





<style id="sf-job-applications-toolbar-actions-selection-final">
    /*
     | Job Applications final toolbar action visibility.
     | Hide action buttons until at least one table row checkbox is selected.
     | Keeps search, filter and columns controls visible.
     */

    body:not(.sf-job-applications-has-selection) [data-sf-job-app-bulk-action="1"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    body.sf-job-applications-has-selection [data-sf-job-app-bulk-action="1"] {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .fi-ta-selection-indicator button,
    .fi-ta-selection-indicator a {
        display: none !important;
    }

    [data-sf-job-app-bulk-action="1"] .animate-spin,
    [data-sf-job-app-bulk-action="1"] [class*="spinner"],
    [data-sf-job-app-bulk-action="1"] [class*="loading"] {
        display: none !important;
    }
</style>

<script id="sf-job-applications-toolbar-actions-selection-final-script">
    (() => {
        const bulkActionLabels = [
            'Create Request',
            'Export Selected CSV',
            'Move to Screening',
            'Move to Under Review',
            'Move to Client Submitted',
            'Move to Qualified',
            'Move to Hired',
            'Decline and Archive',
            'Archive',
            'Permanent Delete',
        ];

        const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim();

        const hasSelectedRows = () => {
            return document.querySelectorAll(
                '.fi-ta-table tbody input[type="checkbox"]:checked, table tbody input[type="checkbox"]:checked'
            ).length > 0;
        };

        const markToolbarBulkButtons = () => {
            document
                .querySelectorAll('.fi-ta-toolbar button, .fi-ta-toolbar a, .fi-ta-header-toolbar button, .fi-ta-header-toolbar a')
                .forEach((button) => {
                    const text = normalize(button.innerText || button.textContent || button.getAttribute('aria-label') || button.getAttribute('title'));

                    const isBulkAction = bulkActionLabels.some((label) => text.includes(label));

                    if (isBulkAction) {
                        button.setAttribute('data-sf-job-app-bulk-action', '1');
                    }
                });
        };

        const applyState = () => {
            markToolbarBulkButtons();

            document.body.classList.toggle(
                'sf-job-applications-has-selection',
                hasSelectedRows()
            );
        };

        document.addEventListener('DOMContentLoaded', applyState);
        document.addEventListener('livewire:navigated', applyState);

        document.addEventListener('change', (event) => {
            if (event.target && event.target.matches('input[type="checkbox"]')) {
                setTimeout(applyState, 20);
                setTimeout(applyState, 120);
                setTimeout(applyState, 300);
            }
        }, true);

        document.addEventListener('click', () => {
            setTimeout(applyState, 50);
            setTimeout(applyState, 180);
        }, true);

        new MutationObserver(applyState).observe(document.body, {
            childList: true,
            subtree: true,
        });

        setTimeout(applyState, 300);
        setTimeout(applyState, 900);
    })();
</script>
