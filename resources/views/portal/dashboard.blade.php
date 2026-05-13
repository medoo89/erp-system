@extends('portal.layouts.app')

@php
    $pageTitle = 'Portal Dashboard';
    $stageLabel = $currentIdentity ? (\App\Models\PortalIdentity::stageOptions()[$currentIdentity->current_stage] ?? $currentIdentity->current_stage) : '-';

    $dashboardEmployment = $employment ?? ($currentIdentity?->employment ?? ($portalAccount?->currentIdentity?->employment ?? null));

    $dashboardJobTitle = $dashboardEmployment
        ? ($dashboardEmployment->position_title
            ?: $dashboardEmployment->job_title
            ?: $dashboardEmployment->designation
            ?: 'Employee')
        : 'Employee';
    $weekDays = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

    $salaryBasisLabel = match ((string) ($compensationSnapshot['salary_basis'] ?? '')) {
        'daily_rate' => 'Daily Rate',
        'monthly' => 'Monthly',
        default => '-',
    };

    $paidSlipsCount = $recentSalarySlips->where('status', 'paid')->count();
    $pendingSlipsCount = $recentSalarySlips->whereIn('status', ['approved', 'sent_to_bank', 'draft'])->count();
@endphp

@section('content')
    @include('portal.partials.pending-file-requests')

<style id="sf-pending-requests-clean-style">
    .sf-pending-requests-clean {
        margin: 22px 0 28px;
        overflow: hidden;
        border-radius: 30px;
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 22px 58px rgba(15,23,42,.08);
    }

    .sf-pending-head {
        padding: 24px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
            linear-gradient(135deg, #0f172a, #234b74);
        color: #fff;
    }

    .sf-pending-kicker {
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .sf-pending-title {
        margin: 0;
        font-size: 28px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: -.04em;
        color: #fff;
    }

    .sf-pending-subtitle {
        margin-top: 8px;
        color: rgba(255,255,255,.78);
        font-size: 14px;
        font-weight: 750;
        line-height: 1.55;
    }

    .sf-pending-open-files,
    .sf-pending-download-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 999px;
        background: #2563eb;
        color: #fff !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 950;
        box-shadow: 0 12px 28px rgba(37,99,235,.22);
    }

    .sf-pending-body {
        padding: 22px 24px 26px;
        display: grid;
        gap: 16px;
    }

    .sf-pending-card {
        border-radius: 26px;
        padding: 20px;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 35%),
            rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
    }

    .sf-pending-card-top {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .sf-pending-label {
        color: #0f172a;
        font-size: 20px;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .sf-pending-help {
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
        font-weight: 750;
        margin-top: 5px;
    }

    .sf-pending-badge {
        border-radius: 999px;
        padding: 8px 13px;
        background: #eff6ff;
        color: #075985;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .10em;
        text-transform: uppercase;
    }

    .sf-pending-download-line {
        margin: 12px 0 16px;
        padding: 12px;
        border-radius: 20px;
        background: #f8fafc;
        border: 1px solid rgba(15,23,42,.08);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .sf-pending-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) repeat(2, minmax(160px, .6fr));
        gap: 12px;
        align-items: end;
    }

    .sf-pending-field label {
        display: block;
        color: #334155;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .10em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .sf-pending-input,
    .sf-pending-textarea {
        width: 100%;
        min-height: 46px;
        border-radius: 18px;
        border: 1px solid rgba(15,23,42,.12);
        background: rgba(248,250,252,.95);
        color: #0f172a;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 750;
        outline: none;
    }

    .sf-pending-textarea {
        min-height: 80px;
        resize: vertical;
        margin-top: 12px;
    }

    .sf-pending-submit {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        border: none;
        border-radius: 999px;
        padding: 0 22px;
        background: #0f172a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 950;
        cursor: pointer;
    }

    .dark .sf-pending-requests-clean,
    .dark .sf-pending-card {
        background: rgba(15,23,42,.86);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-pending-label {
        color: #fff;
    }

    .dark .sf-pending-help,
    .dark .sf-pending-download-line {
        color: rgba(226,232,240,.76);
    }

    .dark .sf-pending-input,
    .dark .sf-pending-textarea,
    .dark .sf-pending-download-line {
        background: rgba(15,23,42,.70);
        border-color: rgba(255,255,255,.12);
        color: #ffffff;
    }

    @media (max-width: 900px) {
        .sf-pending-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,600,0,0" rel="stylesheet">

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


<style id="sf-dashboard-job-title-line-style">
    .sf-dashboard-job-title-line {
        margin-top: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        width: fit-content !important;
        max-width: 100% !important;
        padding: 8px 14px !important;
        border-radius: 999px !important;
        background: rgba(232, 240, 254, .82) !important;
        color: #0b57d0 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .05em !important;
        text-transform: uppercase !important;
        border: 1px solid rgba(11, 87, 208, .12) !important;
    }

    .dark .sf-dashboard-job-title-line {
        background: rgba(59, 130, 246, .16) !important;
        color: #bfdbfe !important;
        border-color: rgba(147, 197, 253, .20) !important;
    }
</style>


<style id="sf-calendar-legends-style">
    .sf-calendar-legends {
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid rgba(215,226,229,.85);
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .sf-calendar-legend-pill {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.88);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 8px 18px rgba(15,23,42,.035);
        color: #334155;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sf-calendar-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
        flex-shrink: 0;
    }

    .dark .sf-calendar-legends {
        border-top-color: rgba(255,255,255,.10);
    }

    .dark .sf-calendar-legend-pill {
        background: rgba(15,23,42,.72);
        border-color: rgba(255,255,255,.10);
        color: rgba(226,232,240,.82);
    }
</style>



@php
    $dashboardUpdateIcon = function (?string $status, ?string $title = null) {
        $text = strtolower(trim(($status ?? '') . ' ' . ($title ?? '')));


        return 'update';
    };

    $dashboardRenderUpdateSvg = function (string $name, string $class = 'sf-dash-update-svg') {
        $icons = [
            'update' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 6.25v6l4 2"/><path d="M20.25 12A8.25 8.25 0 1 1 12 3.75"/><path d="M17.25 3.75h3v3"/></svg>',
            'salary' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.75 7.25h14.5A1.75 1.75 0 0 1 21 9v6a1.75 1.75 0 0 1-1.75 1.75H4.75A1.75 1.75 0 0 1 3 15V9a1.75 1.75 0 0 1 1.75-1.75Z"/><circle cx="12" cy="12" r="2.25"/><path d="M6.25 9.75v4.5M17.75 9.75v4.5"/></svg>',
            'rotation' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 7.25A7 7 0 0 1 19 12.35"/><path d="M19.25 7.25v5.25H14"/><path d="M16.75 16.75A7 7 0 0 1 5 11.65"/><path d="M4.75 16.75V11.5H10"/></svg>',
            'travel' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.75 13.5 20.25 6.75l-6.75 16.5-3.25-7.5-6.5-2.25Z"/><path d="M10.25 15.75 20.25 6.75"/></svg>',
            'file' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 3.75h7.25L18.75 8v10.25A2 2 0 0 1 16.75 20.25h-9.5a2 2 0 0 1-2-2V5.75a2 2 0 0 1 2-2Z"/><path d="M14.5 3.75V8h4.25M8.25 12h7.5M8.25 15h7.5"/></svg>',
            'medical' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.25 6.75V5.5A1.75 1.75 0 0 1 10 3.75h4a1.75 1.75 0 0 1 1.75 1.75v1.25"/><path d="M5.75 6.75h12.5A2.25 2.25 0 0 1 20.5 9v8.25a2.25 2.25 0 0 1-2.25 2.25H5.75a2.25 2.25 0 0 1-2.25-2.25V9a2.25 2.25 0 0 1 2.25-2.25Z"/><path d="M12 10v6M9 13h6"/></svg>',
            'visa' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.75 14.35 6l3.25-.25.25 3.25L20.25 12l-2.4 3 .25 3.25-3.25-.25L12 20.25 9.15 18l-3.25.25.25-3.25-2.4-3 2.4-3-.25-3.25L9.15 6 12 3.75Z"/><path d="m8.75 12.25 2.05 2.05 4.45-4.6"/></svg>',
        ];

        return $icons[$name] ?? $icons['update'];
    };
@endphp

<style>
    .sf-dash-updates-list {
        display: grid;
        gap: 12px;
        margin-top: 16px;
    }

    .sf-dash-update-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 15px;
        background:
            radial-gradient(circle at top right, rgba(36,89,211,.09), transparent 36%),
            rgba(255,255,255,.92);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 14px 34px rgba(15,23,42,.05);
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 13px;
        align-items: flex-start;
    }

    .sf-dash-update-card::before {
        content: "";
        position: absolute;
        left: 39px;
        top: 66px;
        bottom: -10px;
        width: 2px;
        background: linear-gradient(180deg, rgba(36,89,211,.18), transparent);
    }

    .sf-dash-update-icon {
        position: relative;
        z-index: 2;
        width: 50px;
        height: 50px;
        display: grid;
        place-items: center;
        border-radius: 18px;
        background: #eff6ff;
        border: 1px solid rgba(36,89,211,.14);
        flex-shrink: 0;
    }

    .sf-dash-update-svg {
        width: 25px;
        height: 25px;
        display: block;
        stroke: #2459d3;
        stroke-width: 1.9;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sf-dash-update-title {
        color: #0f172a;
        font-size: 15px;
        line-height: 1.25;
        font-weight: 950;
        letter-spacing: -.02em;
        overflow-wrap: anywhere;
    }

    .sf-dash-update-meta {
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 700;
    }

    .sf-dash-update-footer {
        margin-top: 10px;
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .sf-dash-update-badge {
        display: inline-flex;
        border-radius: 999px;
        padding: 7px 10px;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
        background: #eff6ff;
        color: #2459d3;
        border: 1px solid rgba(36,89,211,.12);
    }

    .sf-dash-update-badge--success {
        background: #ecfdf5;
        color: #047857;
        border-color: rgba(16,185,129,.20);
    }

    .sf-dash-update-badge--danger {
        background: #fef2f2;
        color: #b91c1c;
        border-color: rgba(239,68,68,.18);
    }

    .sf-dash-update-badge--reimbursement {
        background: #fff7ed;
        color: #c2410c;
        border-color: rgba(249,115,22,.22);
    }

    .sf-dash-update-badge--travel {
        background: #f0f9ff;
        color: #0369a1;
        border-color: rgba(14,165,233,.20);
    }

    .sf-dash-update-badge--rotation {
        background: #ecfdf5;
        color: #047857;
        border-color: rgba(16,185,129,.20);
    }


    .dark .sf-dash-update-card {
        background: rgba(15,23,42,.72);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-dash-update-title {
        color: #ffffff;
    }

    .dark .sf-dash-update-meta {
        color: rgba(226,232,240,.72);
    }
</style>


@php
    $dashboardFileIcon = function (?string $type, ?string $title = null) {
        $text = strtolower(trim(($type ?? '') . ' ' . ($title ?? '')));


        return 'folder';
    };

    $dashboardRenderSvgIcon = function (string $name, string $class = 'sf-dash-file-svg') {
        $icons = [
            'folder' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.75 6.75A2.25 2.25 0 0 1 6 4.5h4.2c.52 0 1.02.18 1.42.51l1.13.93c.4.33.9.51 1.42.51H18A2.25 2.25 0 0 1 20.25 8.7v8.55A2.25 2.25 0 0 1 18 19.5H6a2.25 2.25 0 0 1-2.25-2.25V6.75Z"/></svg>',
            'badge' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.5 4.75h9A2.25 2.25 0 0 1 18.75 7v10A2.25 2.25 0 0 1 16.5 19.25h-9A2.25 2.25 0 0 1 5.25 17V7A2.25 2.25 0 0 1 7.5 4.75Z"/><path d="M9 8h6M9 16h6"/><circle cx="12" cy="11.5" r="2"/></svg>',
            'id' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25H6.75a2.25 2.25 0 0 1-2.25-2.25V6.75Z"/><circle cx="9.25" cy="10" r="1.75"/><path d="M6.9 15.8c.65-1.45 1.42-2.05 2.35-2.05s1.7.6 2.35 2.05M13.5 9h3.75M13.5 12h3.75M13.5 15h2.5"/></svg>',
            'verified' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.75 14.35 6l3.25-.25.25 3.25L20.25 12l-2.4 3 .25 3.25-3.25-.25L12 20.25 9.15 18l-3.25.25.25-3.25-2.4-3 2.4-3-.25-3.25L9.15 6 12 3.75Z"/><path d="m8.75 12.25 2.05 2.05 4.45-4.6"/></svg>',
            'medical' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.25 6.75V5.5A1.75 1.75 0 0 1 10 3.75h4a1.75 1.75 0 0 1 1.75 1.75v1.25"/><path d="M5.75 6.75h12.5A2.25 2.25 0 0 1 20.5 9v8.25a2.25 2.25 0 0 1-2.25 2.25H5.75a2.25 2.25 0 0 1-2.25-2.25V9a2.25 2.25 0 0 1 2.25-2.25Z"/><path d="M12 10v6M9 13h6"/></svg>',
            'award' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="9" r="4.5"/><path d="M9.5 13.1 8.25 20.25 12 18.25l3.75 2-1.25-7.15"/><path d="m10.25 9 1.15 1.15 2.35-2.55"/></svg>',
            'contract' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 3.75h7.25L18.75 8v10.25A2 2 0 0 1 16.75 20.25h-9.5a2 2 0 0 1-2-2V5.75a2 2 0 0 1 2-2Z"/><path d="M14.5 3.75V8h4.25M8.25 11h7.5M8.25 14h7.5M8.25 17h4.5"/></svg>',
            'flight' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.75 13.5 20.25 6.75l-6.75 16.5-3.25-7.5-6.5-2.25Z"/><path d="M10.25 15.75 20.25 6.75"/></svg>',
            'image' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M5.75 4.75h12.5a2 2 0 0 1 2 2v10.5a2 2 0 0 1-2 2H5.75a2 2 0 0 1-2-2V6.75a2 2 0 0 1 2-2Z"/><circle cx="8.75" cy="9" r="1.5"/><path d="m4.25 17 4.25-4.25 3.25 3.25 2.25-2.25 5.75 5.75"/></svg>',
        ];

        return $icons[$name] ?? $icons['folder'];
    };
@endphp

<style>
    .sf-dash-files-grid {
        display: grid;
        gap: 12px;
        margin-top: 16px;
    }

    .sf-dash-file-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 15px;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 36%),
            rgba(255,255,255,.90);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 14px 34px rgba(15,23,42,.05);
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 13px;
        align-items: flex-start;
    }

    .sf-dash-file-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 17px;
        background: #eff6ff;
        border: 1px solid rgba(36,89,211,.14);
        flex-shrink: 0;
    }

    .sf-dash-file-svg {
        width: 25px;
        height: 25px;
        display: block;
        stroke: #2459d3;
        stroke-width: 1.9;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sf-dash-file-title {
        color: #0f172a;
        font-size: 15px;
        line-height: 1.25;
        font-weight: 950;
        letter-spacing: -.02em;
        overflow-wrap: anywhere;
    }

    .sf-dash-file-meta {
        margin-top: 5px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 700;
    }

    .sf-dash-file-badge {
        margin-top: 9px;
        display: inline-flex;
        border-radius: 999px;
        padding: 7px 10px;
        background: #f8fafc;
        color: #2459d3;
        border: 1px solid rgba(36,89,211,.12);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dark .sf-dash-file-card {
        background: rgba(15,23,42,.72);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-dash-file-title {
        color: #ffffff;
    }

    .dark .sf-dash-file-meta {
        color: rgba(226,232,240,.72);
    }
</style>

    <section class="portal-card portal-card-soft">
        <div class="portal-section-head">
            <div>
                <div class="portal-title">Welcome, {{ $portalAccount->full_name }}</div>
                <div class="portal-muted" style="margin-top:14px;max-width:920px;">
                    This is your employee portal. Here you can follow your salary slips, notifications, updates, important lifecycle dates, and employee-visible files.
                </div>
            </div>

            @if($portalEmployment?->employee_code)
                <span class="portal-badge portal-badge--info">Employment Code: {{ $portalEmployment->employee_code }}</span>
            @endif
        </div>

        <div class="portal-grid-4" style="margin-top:22px;">
            <div class="portal-fast-stat">
                <div class="portal-kpi-label">Job Title</div>
                <div class="portal-fast-stat-value">{{ $dashboardJobTitle }}</div>
            </div>

            <div class="portal-fast-stat">
                <div class="portal-kpi-label">Unread Notifications</div>
                <div class="portal-fast-stat-value">{{ number_format((int) $unreadNotificationsCount) }}</div>
            </div>

            <div class="portal-fast-stat">
                <div class="portal-kpi-label">Portal Status</div>
                <div class="portal-fast-stat-value">{{ $portalAccount->is_active ? 'Active' : 'Inactive' }}</div>
            </div>

            <div class="portal-fast-stat">
                <div class="portal-kpi-label">Last Login</div>
                <div class="portal-fast-stat-value">
                    {{ $portalAccount->last_login_at ? $portalAccount->last_login_at->format('Y-m-d H:i') : '-' }}
                </div>
            </div>
        </div>
    </section>
@include('portal.partials.dashboard-payment-confirmations')

    {{-- SF_MONEY_REQUESTS_DASHBOARD_START --}}
    <section class="portal-card portal-card-soft sf-money-requests-card">
        <div class="portal-section-head">
            <div>
                <div class="portal-title-md">Reimbursement Claims</div>
                <div class="portal-muted" style="margin-top:8px;">
                    Candidate-submitted and candidate-paid reimbursement claims pending or tracked by finance.
                </div>
            </div>
            <span class="portal-badge portal-badge--slate">Finance</span>
        </div>

        @php
            $moneyRequests = $dashboardReimbursementClaims ?? collect();

            $moneyStatusClass = function (?string $status) {
                $status = strtolower((string) $status);

                return match ($status) {
                    'paid' => 'sf-money-badge sf-money-badge--success',
                    'approved' => 'sf-money-badge sf-money-badge--info',
                    'rejected', 'cancelled', 'bank_rejected' => 'sf-money-badge sf-money-badge--danger',
                    'pending', 'draft' => 'sf-money-badge sf-money-badge--warning',
                    default => 'sf-money-badge sf-money-badge--slate',
                };
            };

            $moneyPretty = fn ($value) => strtoupper(str_replace('_', ' ', (string) $value));
        @endphp

        @if($moneyRequests->count())
            <div class="sf-money-list">
                @foreach($moneyRequests as $request)
                    @php
                        $status = $request['status'] ?? 'pending';
                        $amount = $request['amount'] ?? 0;
                        $currency = $request['currency'] ?? 'EUR';
                        $date = $request['date'] ?? null;
                    @endphp

                    <div class="sf-money-item">
                        <div class="sf-money-main">
                            <div class="sf-money-title">{{ $request['title'] ?? 'Reimbursement Claim' }}</div>

                            <div class="sf-money-meta">
                                {{ ucfirst(str_replace('_', ' ', $request['category'] ?? 'other')) }}
                                @if($date)
                                    · {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                @endif
                            </div>

                            <div class="sf-money-badges">
                                <span class="{{ $moneyStatusClass($status) }}">{{ $moneyPretty($status) }}</span>
                                <span class="sf-money-badge sf-money-badge--purple">{{ $request['source'] ?? 'Request' }}</span>
                                <span class="sf-money-badge sf-money-badge--slate">{{ $moneyPretty($request['paid_by'] ?? 'candidate') }}</span>

                                @if(!empty($request['has_receipt']))
                                    <span class="sf-money-badge sf-money-badge--success">Receipt Uploaded</span>
                                @else
                                    <span class="sf-money-badge sf-money-badge--slate">No Receipt</span>
                                @endif
                            </div>
                        </div>

                        <div class="sf-money-amount">
                            {{ number_format((float) $amount, 2) }} {{ $currency }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="portal-empty" style="margin-top:14px;">
                No reimbursement claims recorded yet.
            </div>
        @endif
    </section>
    {{-- SF_MONEY_REQUESTS_DASHBOARD_END --}}


    <section class="portal-grid-2">
        <div class="portal-card portal-card-soft">
            <div class="portal-section-head">
                <div class="portal-title-md">Calendar</div>

                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <a href="{{ $calendarPrevUrl }}" class="portal-btn" style="background:#eff6ff;color:#1d4ed8;min-height:38px;padding:0 14px;">← Prev</a>
                    <span class="portal-badge portal-badge--info">{{ $calendarMonthLabel }}</span>
                    <a href="{{ $calendarNextUrl }}" class="portal-btn" style="background:#eff6ff;color:#1d4ed8;min-height:38px;padding:0 14px;">Next →</a>
                </div>
            </div>

            <div class="portal-calendar-large">
                @foreach($weekDays as $day)
                    <div class="portal-calendar-dayname">{{ $day }}</div>
                @endforeach

                @foreach($calendarWeeks as $week)
                    @foreach($week as $cell)
                        @php
                            $rawEventValue = $cell['eventType'] ?? '';

                            if (is_array($rawEventValue)) {
                                $rawEventValue = $rawEventValue['type']
                                    ?? $rawEventValue['title']
                                    ?? $rawEventValue[0]
                                    ?? 'other';
                            }

                            $rawEventType = strtolower(trim((string) $rawEventValue));
                            $normalizedEventType = str_replace([' ', '-', '/', '\\'], '_', $rawEventType);

                            if (str_contains($normalizedEventType, 'rotation')) {
                                $normalizedEventType = 'rotation';
                            } elseif (str_contains($normalizedEventType, 'hotel') || str_contains($normalizedEventType, 'accommodation')) {
                                $normalizedEventType = 'hotel';
                            } elseif (str_contains($normalizedEventType, 'expiry') || str_contains($normalizedEventType, 'expire') || str_contains($normalizedEventType, 'expiration')) {
                                $normalizedEventType = 'document_expiry';
                            } elseif (str_contains($normalizedEventType, 'mobilization') || str_contains($normalizedEventType, 'demobilization') || str_contains($normalizedEventType, 'travel')) {
                                $normalizedEventType = str_contains($normalizedEventType, 'ticket') || str_contains($normalizedEventType, 'request')
                                    ? 'ticket_travel'
                                    : 'travel';
                            } elseif (str_contains($normalizedEventType, 'ticket') || str_contains($normalizedEventType, 'request')) {
                                $normalizedEventType = 'ticket_travel';
                            } elseif (str_contains($normalizedEventType, 'visa') || str_contains($normalizedEventType, 'document')) {
                                $normalizedEventType = 'visa';
                            } else {
                                $normalizedEventType = 'other';
                            }

                            $eventBg = match ($normalizedEventType) {
                                'rotation' => '#ecfdf5',
                                'travel' => '#e0f2fe',
                                'ticket_travel' => '#fff7ed',
                                'hotel' => '#ccfbf1',
                                'visa' => '#eff6ff',
                                'document_expiry' => '#fef2f2',
                                default => '#f8fafc',
                            };

                            $eventBorder = match ($normalizedEventType) {
                                'rotation' => '#86efac',
                                'travel' => '#7dd3fc',
                                'ticket_travel' => '#fdba74',
                                'hotel' => '#5eead4',
                                'visa' => '#93c5fd',
                                'document_expiry' => '#fca5a5',
                                default => '#cbd5e1',
                            };

                            $eventDot = match ($normalizedEventType) {
                                'rotation' => '#16a34a',
                                'travel' => '#0ea5e9',
                                'ticket_travel' => '#d97706',
                                'hotel' => '#14b8a6',
                                'visa' => '#2563eb',
                                'document_expiry' => '#dc2626',
                                default => '#94a3b8',
                            };
                        @endphp

                        @php
                            $cellDateValue = $cell['date'] ?? ($cell['dateString'] ?? ($cell['day_date'] ?? null));

                            if ($cellDateValue instanceof \Carbon\CarbonInterface) {
                                $cellDateString = $cellDateValue->format('Y-m-d');
                            } elseif (is_array($cellDateValue)) {
                                $cellDateString = (string) (
                                    $cellDateValue['date']
                                    ?? $cellDateValue['value']
                                    ?? $cellDateValue['formatted']
                                    ?? $cellDateValue[0]
                                    ?? ''
                                );
                            } else {
                                $cellDateString = (string) ($cellDateValue ?: '');
                            }

                            if ($cellDateString === '' && !empty($calendarMonthLabel ?? null)) {
                                $cellDateString = (string) ($cell['fullDate'] ?? $cell['full_date'] ?? '');
                            }

                            $cellEventItems = collect($cell['items'] ?? $cell['events'] ?? []);

                            $safeEventTypeValue = $cell['eventType'] ?? 'event';

                            if (is_array($safeEventTypeValue)) {
                                $safeEventTypeValue = $safeEventTypeValue['type']
                                    ?? $safeEventTypeValue['title']
                                    ?? $safeEventTypeValue[0]
                                    ?? 'event';
                            }

                            $safeEventTypeValue = (string) ($safeEventTypeValue ?: 'event');

                            if (($cell['hasEvent'] ?? false) && $cellEventItems->isEmpty()) {
                                $cellEventItems = collect([[
                                    'title' => ucfirst(str_replace('_', ' ', $safeEventTypeValue)),
                                    'type' => $safeEventTypeValue,
                                    'notes' => null,
                                    'color' => $eventDot,
                                    'date' => $cellDateString,
                                ]]);
                            }

                            $cellEventsJson = $cellEventItems
                                ->map(function ($event) use ($eventDot, $cellDateString) {
                                    if (is_object($event)) {
                                        $event = (array) $event;
                                    }

                                    if (! is_array($event)) {
                                        $event = [
                                            'title' => (string) $event,
                                            'type' => 'event',
                                        ];
                                    }

                                    $eventDate = $event['date'] ?? $cellDateString;

                                    if ($eventDate instanceof \Carbon\CarbonInterface) {
                                        $eventDate = $eventDate->format('Y-m-d');
                                    } elseif (is_array($eventDate)) {
                                        $eventDate = (string) (
                                            $eventDate['date']
                                            ?? $eventDate['value']
                                            ?? $eventDate['formatted']
                                            ?? $eventDate[0]
                                            ?? $cellDateString
                                        );
                                    } else {
                                        $eventDate = (string) ($eventDate ?: $cellDateString);
                                    }

                                    $eventType = $event['type'] ?? 'event';

                                    if (is_array($eventType)) {
                                        $eventType = $eventType['type']
                                            ?? $eventType['title']
                                            ?? $eventType[0]
                                            ?? 'event';
                                    }

                                    return [
                                        'title' => (string) ($event['title'] ?? 'Event'),
                                        'type' => (string) ($eventType ?: 'event'),
                                        'notes' => is_array($event['notes'] ?? null) ? null : ($event['notes'] ?? null),
                                        'color' => (string) ($event['color'] ?? $eventDot),
                                        'date' => $eventDate,
                                    ];
                                })
                                ->values()
                                ->toJson();
                        @endphp

                        <div
                            class="
                                portal-calendar-cell
                                {{ !$cell['isCurrentMonth'] ? 'portal-calendar-cell--muted' : '' }}
                                {{ $cell['isToday'] ? 'portal-calendar-cell--today' : '' }}
                                {{ $cell['hasEvent'] ? 'sf-calendar-day-clickable' : 'sf-calendar-day-empty' }}
                            "
                            style="{{ $cell['hasEvent'] ? 'background:' . $eventBg . '; border-color:' . $eventBorder . ';' : '' }}"
                            @if($cell['hasEvent'])
                                role="button"
                                tabindex="0"
                                data-date="{{ $cellDateString }}"
                                data-events='{{ e($cellEventsJson) }}'
                                onclick="window.sfOpenCalendarDayPopover(this)"
                                onkeydown="if(event.key === 'Enter' || event.key === ' '){ event.preventDefault(); window.sfOpenCalendarDayPopover(this); }"
                            @endif
                        >
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                                <span>{{ $cell['day'] }}</span>

                                @if($cell['hasEvent'])
                                    <span style="width:10px;height:10px;border-radius:999px;background:{{ $eventDot }};display:inline-block;flex-shrink:0;margin-top:3px;"></span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div class="sf-calendar-legends">
                    <span class="sf-calendar-legend-pill"><span class="sf-calendar-legend-dot" style="background:#16a34a;"></span> Rotation</span>
                    <span class="sf-calendar-legend-pill"><span class="sf-calendar-legend-dot" style="background:#0ea5e9;"></span> Travel / Mobilization</span>
                    <span class="sf-calendar-legend-pill"><span class="sf-calendar-legend-dot" style="background:#f97316;"></span> Ticket / Travel Request</span>
                    <span class="sf-calendar-legend-pill"><span class="sf-calendar-legend-dot" style="background:#14b8a6;"></span> Hotel</span>
                    <span class="sf-calendar-legend-pill"><span class="sf-calendar-legend-dot" style="background:#2563eb;"></span> Visa / Documents</span>
                    <span class="sf-calendar-legend-pill"><span class="sf-calendar-legend-dot" style="background:#dc2626;"></span> Document Expiry</span>
                    <span class="sf-calendar-legend-pill"><span class="sf-calendar-legend-dot" style="background:#94a3b8;"></span> Other</span>
                </div>
            </div>

        <div style="display:flex;flex-direction:column;gap:18px;">
            <section class="portal-card portal-card-soft sf-ne-card">
                <div class="portal-section-head">
                    <div class="portal-title-md">Next Events</div>
                    <span class="portal-badge portal-badge--slate">Important</span>
                </div>

                @if($nextEvents->count())
                    <div class="sf-ne-list">
                        @foreach($nextEvents as $event)
                            @php
                                $neColor = $event['color'] ?? '#2563eb';
                                $neTitle = $event['title'] ?? 'Event';
                                $neType = strtoupper(str_replace('_', ' ', (string) ($event['type'] ?? 'event')));
                                $neDate = !empty($event['date']) ? $event['date']->format('Y-m-d') : '—';
                                $neNotes = $event['notes'] ?? null;
                            @endphp

                            <div class="sf-ne-item" style="--ne-color: {{ $neColor }};">
                                <span class="sf-ne-bar"></span>

                                <div class="sf-ne-content">
                                    <div class="sf-ne-title">{{ $neTitle }}</div>

                                    @if($neNotes)
                                        <div class="sf-ne-notes">{{ $neNotes }}</div>
                                    @endif

                                    <div class="sf-ne-meta">
                                        <span class="sf-ne-pill sf-ne-type">{{ $neType }}</span>
                                        <span class="sf-ne-pill sf-ne-date">{{ $neDate }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="portal-empty" style="margin-top:14px;">
                        No upcoming events recorded yet.
                    </div>
                @endif
            </section>

            <section class="portal-card portal-card-soft">
                <div class="portal-section-head">
                    <div class="portal-title-md">Fast Statistics</div>
                    <span class="portal-badge portal-badge--slate">Quick View</span>
                </div>

                <div class="portal-grid-2" style="margin-top:16px;">
                    <div class="portal-kpi">
                        <div class="portal-kpi-label">Paid Salary Slips</div>
                        <div class="portal-kpi-value" style="font-size:24px;">{{ $paidSlipsCount }}</div>
                    </div>

                    <div class="portal-kpi">
                        <div class="portal-kpi-label">Pending Salary Slips</div>
                        <div class="portal-kpi-value" style="font-size:24px;">{{ $pendingSlipsCount }}</div>
                    </div>

                    <div class="portal-kpi">
                        <div class="portal-kpi-label">Available Files</div>
                        <div class="portal-kpi-value" style="font-size:24px;">{{ $recentFiles->count() }}</div>
                    </div>

                    <div class="portal-kpi">
                        <div class="portal-kpi-label">Recent Updates</div>
                        <div class="portal-kpi-value" style="font-size:24px;">{{ $latestTimeline->count() }}</div>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <section class="portal-grid-2">
        <div class="portal-card portal-card-soft">
            <div class="portal-section-head">
                <div class="portal-title-md">Compensation Snapshot</div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <span class="portal-badge portal-badge--slate">Lifecycle</span>
                    @if(!empty($compensationSnapshot['source_label']))
                        <span class="portal-badge portal-badge--info">{{ $compensationSnapshot['source_label'] }}</span>
                    @endif
                </div>
            </div>

            <div class="portal-grid-2" style="margin-top:16px;">
                <div class="portal-kpi">
                    <div class="portal-kpi-label">Salary Basis</div>
                    <div class="portal-kpi-value" style="font-size:22px;">{{ $salaryBasisLabel }}</div>
                </div>

                <div class="portal-kpi">
                    <div class="portal-kpi-label">Currency</div>
                    <div class="portal-kpi-value" style="font-size:22px;">{{ $compensationSnapshot['salary_currency'] ?: '-' }}</div>
                </div>

                <div class="portal-kpi">
                    <div class="portal-kpi-label">Agreed Daily Rate</div>
                    <div class="portal-kpi-value" style="font-size:22px;">
                        {{ $compensationSnapshot['daily_rate'] !== null ? number_format((float) $compensationSnapshot['daily_rate'], 2) : '-' }}
                    </div>
                </div>

                <div class="portal-kpi">
                    <div class="portal-kpi-label">Monthly Salary</div>
                    <div class="portal-kpi-value" style="font-size:22px;">
                        {{ $compensationSnapshot['monthly_salary'] !== null ? number_format((float) $compensationSnapshot['monthly_salary'], 2) : '-' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="portal-card portal-card-soft">
            <div class="portal-section-head">
                <div class="portal-title-md">Operational Snapshot</div>
                <span class="portal-badge portal-badge--slate">Status</span>
            </div>

            <div class="portal-grid-2" style="margin-top:16px;">
                <div class="portal-kpi">
                    <div class="portal-kpi-label">Rotation Status</div>
                    <div class="portal-kpi-value" style="font-size:22px;">{{ $rotationSnapshot['rotation_status'] ?: '-' }}</div>
                </div>

                <div class="portal-kpi">
                    <div class="portal-kpi-label">Travel Status</div>
                    <div class="portal-kpi-value" style="font-size:22px;">{{ $rotationSnapshot['travel_status'] ?: '-' }}</div>
                </div>

                <div class="portal-kpi">
                    <div class="portal-kpi-label">Mobilization Date</div>
                    <div class="portal-kpi-value" style="font-size:22px;">
                        {{ $rotationSnapshot['mobilization_date'] ? $rotationSnapshot['mobilization_date']->format('Y-m-d') : '-' }}
                    </div>
                </div>

                <div class="portal-kpi">
                    <div class="portal-kpi-label">Demobilization Date</div>
                    <div class="portal-kpi-value" style="font-size:22px;">
                        {{ $rotationSnapshot['demobilization_date'] ? $rotationSnapshot['demobilization_date']->format('Y-m-d') : '-' }}
                    </div>
                </div>
            </div>

            <div class="portal-list-meta" style="margin-top:14px;">
                Work Location: {{ $rotationSnapshot['work_location'] ?: '-' }}
            </div>
        </div>
    </section>

    <section class="portal-grid-2">
        <div class="portal-card portal-card-soft">
            <div class="portal-section-head">
                <div>
                    <div class="portal-title-md">Latest Updates</div>
                    <div class="portal-muted" style="margin-top:8px;">
                        A live feed of your latest salary slips, reimbursement claims, mobilization, rotation, travel, ticket, file, and portal updates.
                    </div>
                </div>
                <a class="portal-section-link" href="{{ route('portal.timeline.index') }}">View All</a>
            </div>

            @if($latestTimeline->count())
                <div class="sf-dash-updates-list">
                    @foreach($latestTimeline as $item)
                        @php
                            $updateTitle = $item['title'] ?? 'Portal update';
                            $updateDescription = $item['description'] ?? null;
                            $updateStatus = $item['badge_status'] ?? null;
                            $updateDate = !empty($item['event_date']) ? $item['event_date']->format('Y-m-d H:i') : '-';
                            $updateIconKey = $dashboardUpdateIcon($updateStatus, $updateTitle . ' ' . $updateDescription);

                            $badgeClass = 'sf-dash-update-badge';
                            $updateType = (string) ($item['type'] ?? '');
                            $updateStatusText = (string) ($updateStatus ?? '');

                            if (in_array($updateStatusText, ['paid', 'approved', 'active'], true)) {
                                $badgeClass .= ' sf-dash-update-badge--success';
                            } elseif (in_array($updateStatusText, ['bank_rejected', 'cancelled', 'rejected'], true)) {
                                $badgeClass .= ' sf-dash-update-badge--danger';
                            } elseif ($updateType === 'reimbursement' || str_contains($updateType, 'reimbursement')) {
                                $badgeClass .= ' sf-dash-update-badge--reimbursement';
                            } elseif (str_contains($updateType, 'travel') || str_contains($updateType, 'ticket') || str_contains($updateType, 'mobilization')) {
                                $badgeClass .= ' sf-dash-update-badge--travel';
                            } elseif (str_contains($updateType, 'rotation')) {
                                $badgeClass .= ' sf-dash-update-badge--rotation';
                            }
                        @endphp

                        <div class="sf-dash-update-card">
                            <div class="sf-dash-update-icon">
                                {!! $dashboardRenderUpdateSvg($updateIconKey) !!}
                            </div>

                            <div>
                                <div class="sf-dash-update-title">{{ $updateTitle }}</div>

                                @if($updateDescription)
                                    <div class="sf-dash-update-meta">{{ $updateDescription }}</div>
                                @endif

                                <div class="sf-dash-update-footer">
                                    @if($updateStatus)
                                        <span class="{{ $badgeClass }}">{{ str_replace('_', ' ', $updateStatus) }}</span>
                                    @endif

                                    <span class="sf-dash-update-meta" style="margin-top:0;">{{ $updateDate }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="portal-empty" style="margin-top:14px;">
                    No updates yet.
                </div>
            @endif
        </div>
        <div class="portal-card portal-card-soft">
            <div class="portal-section-head">
                <div>
                    <div class="portal-title-md">Recent Files</div>
                    <div class="portal-muted" style="margin-top:8px;">
                        Latest employee-visible documents available in your portal.
                    </div>
                </div>
                <a class="portal-section-link" href="{{ route('portal.files.index') }}">Open Files</a>
            </div>

            @if($recentFiles->count())
                <div class="sf-dash-files-grid">
                    @foreach($recentFiles as $file)
                        @php
                            $fileTitle = $file['title'] ?? 'Untitled File';
                            $fileDescription = $file['description'] ?? 'File';
                            $fileDate = !empty($file['date']) ? $file['date']->format('Y-m-d H:i') : '-';
                            $fileIconKey = $dashboardFileIcon($fileDescription, $fileTitle);
                        @endphp

                        <div class="sf-dash-file-card">
                            <div class="sf-dash-file-icon">
                                {!! $dashboardRenderSvgIcon($fileIconKey) !!}
                            </div>

                            <div>
                                <div class="sf-dash-file-title">{{ $fileTitle }}</div>
                                <div class="sf-dash-file-meta">Added: {{ $fileDate }}</div>
                                <span class="sf-dash-file-badge">{{ ucfirst(str_replace('_', ' ', $fileDescription)) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="portal-empty" style="margin-top:14px;">
                    No files available yet.
                </div>
            @endif
        </div>

    </section>

    <section class="portal-card portal-card-soft">
        <div class="portal-section-head">
            <div>
                <div class="portal-title-md">Recent Salary Slips</div>
                <div class="portal-muted" style="margin-top:8px;">
                    Your most recent salary slips are shown here for quick access.
                </div>
            </div>

            <a class="portal-section-link" href="{{ route('portal.salary-slips.index') }}">View All Salary Slips</a>
        </div>

        @if($recentSalarySlips->count())
            <div style="overflow:auto;margin-top:16px;">
                <table style="width:100%;border-collapse:separate;border-spacing:0 12px;">
                    <thead>
                        <tr>
                            <th style="text-align:left;padding:10px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Period</th>
                            <th style="text-align:left;padding:10px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Client</th>
                            <th style="text-align:left;padding:10px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Project</th>
                            <th style="text-align:left;padding:10px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Amount</th>
                            <th style="text-align:left;padding:10px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Status</th>
                            <th style="text-align:left;padding:10px 14px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSalarySlips as $item)
                            @php
                                $badge = match ((string) $item->status) {
                                    'approved' => ['Approved', 'portal-badge portal-badge--info'],
                                    'sent_to_bank' => ['Sent to Bank', 'portal-badge portal-badge--warning'],
                                    'paid' => ['Paid', 'portal-badge portal-badge--success'],
                                    'bank_rejected' => ['Bank Rejected', 'portal-badge portal-badge--danger'],
                                    default => ['Draft', 'portal-badge portal-badge--slate'],
                                };
                            @endphp
                            <tr>
                                <td style="background:#f7fbff;padding:18px 14px;border-top-left-radius:20px;border-bottom-left-radius:20px;font-weight:800;">
                                    {{ sprintf('%02d / %04d', (int) ($item->salary_month ?? 0), (int) ($item->salary_year ?? 0)) }}
                                </td>
                                <td style="background:#f7fbff;padding:18px 14px;">{{ $item->client?->name ?: '-' }}</td>
                                <td style="background:#f7fbff;padding:18px 14px;">{{ $item->project?->name ?: '-' }}</td>
                                <td style="background:#f7fbff;padding:18px 14px;font-weight:900;">
                                    {{ number_format((float) ($item->net_amount ?? 0), 2) }} {{ $item->currency ?: '' }}
                                </td>
                                <td style="background:#f7fbff;padding:18px 14px;">
                                    <span class="{{ $badge[1] }}">{{ $badge[0] }}</span>
                                </td>
                                <td style="background:#f7fbff;padding:18px 14px;border-top-right-radius:20px;border-bottom-right-radius:20px;">
                                    <a href="{{ route('portal.salary-slips.show', $item) }}" style="font-weight:800;color:#2563eb;">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="portal-empty" style="margin-top:16px;">
                No salary slips available yet.
            </div>
        @endif
    </section>







<style id="sf-clean-next-events-final-fix">
    .sf-clean-next-events {
        display: grid !important;
        gap: 14px !important;
        margin-top: 16px !important;
    }

    .sf-clean-next-event {
        position: relative !important;
        display: grid !important;
        grid-template-columns: 8px minmax(0, 1fr) !important;
        gap: 14px !important;
        align-items: stretch !important;
        min-height: 92px !important;
        padding: 16px !important;
        border-radius: 24px !important;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 36%),
            rgba(255,255,255,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 14px 34px rgba(15,23,42,.055) !important;
        overflow: hidden !important;
    }

    .sf-clean-next-dot {
        width: 8px !important;
        min-height: 100% !important;
        border-radius: 999px !important;
        background: linear-gradient(180deg, #14b8a6, #2563eb) !important;
    }

    .sf-clean-next-main {
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
    }

    .sf-clean-next-title {
        color: #0f172a !important;
        font-size: 16px !important;
        line-height: 1.25 !important;
        font-weight: 950 !important;
        letter-spacing: -.025em !important;
        overflow-wrap: anywhere !important;
    }

    .sf-clean-next-meta {
        margin-top: 6px !important;
        color: #64748b !important;
        font-size: 12px !important;
        line-height: 1.45 !important;
        font-weight: 700 !important;
    }

    .sf-clean-next-footer {
        margin-top: 11px !important;
        display: flex !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
        align-items: center !important;
    }

    .sf-clean-next-badge,
    .sf-clean-next-date {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 30px !important;
        padding: 0 11px !important;
        border-radius: 999px !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
        border: 1px solid rgba(15,23,42,.10) !important;
    }

    .sf-clean-next-date {
        background: #f8fafc !important;
        color: #334155 !important;
    }

    .sf-clean-next-badge--success {
        background: #ecfdf5 !important;
        color: #047857 !important;
        border-color: rgba(16,185,129,.20) !important;
    }

    .sf-clean-next-badge--info {
        background: #f0f9ff !important;
        color: #0369a1 !important;
        border-color: rgba(14,165,233,.20) !important;
    }

    .sf-clean-next-badge--warning {
        background: #fff7ed !important;
        color: #c2410c !important;
        border-color: rgba(249,115,22,.20) !important;
    }

    .sf-clean-next-badge--blue {
        background: #eff6ff !important;
        color: #2459d3 !important;
        border-color: rgba(36,89,211,.16) !important;
    }

    .sf-clean-next-badge--slate {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border-color: rgba(100,116,139,.16) !important;
    }

    .dark .sf-clean-next-event {
        background: rgba(15,23,42,.78) !important;
        border-color: rgba(255,255,255,.10) !important;
        box-shadow: 0 18px 46px rgba(0,0,0,.26) !important;
    }

    .dark .sf-clean-next-title {
        color: #ffffff !important;
    }

    .dark .sf-clean-next-meta {
        color: rgba(226,232,240,.74) !important;
    }

    .dark .sf-clean-next-date {
        background: rgba(15,23,42,.88) !important;
        color: rgba(226,232,240,.82) !important;
        border-color: rgba(255,255,255,.10) !important;
    }

    @media (max-width: 760px) {
        .sf-clean-next-event {
            grid-template-columns: 6px minmax(0, 1fr) !important;
            border-radius: 20px !important;
            padding: 14px !important;
        }
    }
</style>











<style id="sf-next-events-hard-reset-clean-v2">
    .sf-ne-card {
        overflow: hidden !important;
    }

    .sf-ne-list {
        margin-top: 18px !important;
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 14px !important;
        width: 100% !important;
        background: transparent !important;
    }

    .sf-ne-list,
    .sf-ne-list *,
    .sf-ne-list *::before,
    .sf-ne-list *::after {
        box-sizing: border-box !important;
        writing-mode: horizontal-tb !important;
        text-orientation: mixed !important;
        direction: ltr !important;
        transform: none !important;
        rotate: none !important;
        word-break: normal !important;
    }

    .sf-ne-list *::before,
    .sf-ne-list *::after {
        content: none !important;
        display: none !important;
    }

    .sf-ne-item {
        position: relative !important;
        display: grid !important;
        grid-template-columns: 8px minmax(0, 1fr) !important;
        gap: 16px !important;
        align-items: center !important;
        width: 100% !important;
        min-height: 92px !important;
        height: auto !important;
        padding: 18px 20px !important;
        border-radius: 24px !important;
        overflow: hidden !important;
        background:
            radial-gradient(circle at top right, color-mix(in srgb, var(--ne-color) 10%, transparent), transparent 34%),
            rgba(255,255,255,.92) !important;
        border: 1px solid color-mix(in srgb, var(--ne-color) 26%, rgba(15,23,42,.08)) !important;
        box-shadow: 0 12px 28px rgba(15,23,42,.045) !important;
    }

    .sf-ne-bar {
        display: block !important;
        width: 8px !important;
        height: 56px !important;
        border-radius: 999px !important;
        background: var(--ne-color) !important;
        flex-shrink: 0 !important;
    }

    .sf-ne-content {
        display: block !important;
        min-width: 0 !important;
        width: 100% !important;
    }

    .sf-ne-title {
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #0f172a !important;
        font-size: 17px !important;
        line-height: 1.25 !important;
        font-weight: 950 !important;
        letter-spacing: -.035em !important;
        white-space: normal !important;
        text-align: left !important;
    }

    .sf-ne-notes {
        display: block !important;
        margin-top: 5px !important;
        color: #64748b !important;
        font-size: 12px !important;
        line-height: 1.45 !important;
        font-weight: 700 !important;
        white-space: normal !important;
        text-align: left !important;
    }

    .sf-ne-meta {
        margin-top: 12px !important;
        display: flex !important;
        flex-direction: row !important;
        gap: 8px !important;
        align-items: center !important;
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
    }

    .sf-ne-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: auto !important;
        min-width: 0 !important;
        max-width: 100% !important;
        min-height: 30px !important;
        padding: 0 12px !important;
        border-radius: 999px !important;
        font-size: 10px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
    }

    .sf-ne-type {
        background: color-mix(in srgb, var(--ne-color) 11%, white) !important;
        color: color-mix(in srgb, var(--ne-color) 74%, #0f172a) !important;
        border: 1px solid color-mix(in srgb, var(--ne-color) 22%, transparent) !important;
    }

    .sf-ne-date {
        background: #f8fafc !important;
        color: #334155 !important;
        border: 1px solid rgba(15,23,42,.10) !important;
    }

    .dark .sf-ne-item {
        background: rgba(15,23,42,.78) !important;
        border-color: color-mix(in srgb, var(--ne-color) 30%, rgba(255,255,255,.10)) !important;
        box-shadow: 0 16px 42px rgba(0,0,0,.24) !important;
    }

    .dark .sf-ne-title {
        color: #ffffff !important;
    }

    .dark .sf-ne-notes {
        color: rgba(226,232,240,.74) !important;
    }

    .dark .sf-ne-date {
        background: rgba(15,23,42,.92) !important;
        color: rgba(226,232,240,.82) !important;
        border-color: rgba(255,255,255,.10) !important;
    }
</style>


<style id="sf-calendar-event-day-color-polish">
    .portal-calendar-cell[style*="background"] {
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.65), 0 10px 24px rgba(15,23,42,.045) !important;
    }

    .portal-calendar-cell[style*="background"]:not(.portal-calendar-cell--muted) {
        font-weight: 950 !important;
    }

    .portal-calendar-cell[style*="background"]:hover {
        transform: translateY(-1px) !important;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.72), 0 16px 34px rgba(15,23,42,.09) !important;
    }

    .dark .portal-calendar-cell[style*="background"] {
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.08), 0 12px 30px rgba(0,0,0,.22) !important;
    }
</style>


<style id="sf-calendar-event-day-color-force">
    .portal-calendar-cell[style*="background:#ecfdf5"],
    .portal-calendar-cell[style*="background: #ecfdf5"] {
        background: linear-gradient(180deg, #ecfdf5 0%, #dcfce7 100%) !important;
        border-color: #86efac !important;
    }

    .portal-calendar-cell[style*="background:#e0f2fe"],
    .portal-calendar-cell[style*="background: #e0f2fe"] {
        background: linear-gradient(180deg, #e0f2fe 0%, #dbeafe 100%) !important;
        border-color: #7dd3fc !important;
    }

    .portal-calendar-cell[style*="background:#fff7ed"],
    .portal-calendar-cell[style*="background: #fff7ed"] {
        background: linear-gradient(180deg, #fff7ed 0%, #ffedd5 100%) !important;
        border-color: #fdba74 !important;
    }

    .portal-calendar-cell[style*="background:#eff6ff"],
    .portal-calendar-cell[style*="background: #eff6ff"] {
        background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%) !important;
        border-color: #93c5fd !important;
    }

    .portal-calendar-cell[style*="background"] {
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.72), 0 12px 26px rgba(15,23,42,.055) !important;
    }

    .portal-calendar-cell[style*="background"] span:first-child {
        color: #0f172a !important;
        font-weight: 950 !important;
    }
</style>


<div id="sfCalendarPopover" class="sf-calendar-popover" aria-hidden="true">
    <div class="sf-calendar-popover-head">
        <div>
            <div class="sf-calendar-popover-kicker">Calendar Day</div>
            <div id="sfCalendarPopoverTitle" class="sf-calendar-popover-title">Selected Day</div>
        </div>
        <button type="button" class="sf-calendar-popover-close" onclick="window.sfCloseCalendarDayPopover()">×</button>
    </div>

    <div id="sfCalendarPopoverList" class="sf-calendar-popover-list"></div>
</div>

<style id="sf-clickable-calendar-days-final">
    .sf-calendar-day-clickable {
        cursor: pointer !important;
        position: relative !important;
        transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease !important;
    }

    .sf-calendar-day-clickable:hover {
        transform: translateY(-2px) !important;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.72), 0 18px 36px rgba(15,23,42,.10) !important;
    }

    .sf-calendar-day-clickable:focus {
        outline: 3px solid rgba(37,99,235,.25) !important;
        outline-offset: 3px !important;
    }

    .sf-calendar-popover {
        position: fixed !important;
        z-index: 999999 !important;
        width: min(430px, calc(100vw - 28px)) !important;
        display: none;
        border-radius: 26px !important;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.14), transparent 38%),
            linear-gradient(180deg,#ffffff 0%,#f8fbff 100%) !important;
        border: 1px solid rgba(215,226,229,.95) !important;
        box-shadow: 0 30px 90px rgba(15,23,42,.24) !important;
        padding: 16px !important;
    }

    .sf-calendar-popover.is-open {
        display: block !important;
    }

    .sf-calendar-popover-head {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
        gap: 12px !important;
        padding-bottom: 12px !important;
        border-bottom: 1px solid rgba(215,226,229,.85) !important;
    }

    .sf-calendar-popover-kicker {
        color: #2459d3 !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        margin-bottom: 5px !important;
    }

    .sf-calendar-popover-title {
        color: #0f172a !important;
        font-size: 20px !important;
        line-height: 1.1 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
    }

    .sf-calendar-popover-close {
        width: 38px !important;
        height: 38px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        font-size: 22px !important;
        line-height: 1 !important;
        cursor: pointer !important;
        font-weight: 800 !important;
    }

    .sf-calendar-popover-list {
        display: grid !important;
        gap: 10px !important;
        margin-top: 13px !important;
        max-height: 330px !important;
        overflow: auto !important;
        padding-right: 2px !important;
    }

    .sf-calendar-popover-item {
        position: relative !important;
        border-radius: 18px !important;
        background: rgba(255,255,255,.90) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        padding: 13px 13px 13px 18px !important;
        box-shadow: 0 10px 24px rgba(15,23,42,.04) !important;
        overflow: hidden !important;
    }

    .sf-calendar-popover-item::before {
        content: "" !important;
        position: absolute !important;
        left: 0 !important;
        top: 12px !important;
        bottom: 12px !important;
        width: 5px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        display: block !important;
    }

    .sf-calendar-popover-item-title {
        color: #0f172a !important;
        font-size: 14px !important;
        line-height: 1.35 !important;
        font-weight: 950 !important;
    }

    .sf-calendar-popover-item-meta {
        margin-top: 5px !important;
        color: #64748b !important;
        font-size: 12px !important;
        line-height: 1.45 !important;
        font-weight: 700 !important;
    }

    .sf-calendar-popover-item-type {
        margin-top: 8px !important;
        display: inline-flex !important;
        border-radius: 999px !important;
        padding: 6px 10px !important;
        background: #eff6ff !important;
        color: #2459d3 !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase !important;
    }

    .dark .sf-calendar-popover,
    .dark .sf-calendar-popover-item {
        background: rgba(15,23,42,.92) !important;
        border-color: rgba(255,255,255,.10) !important;
    }

    .dark .sf-calendar-popover-title,
    .dark .sf-calendar-popover-item-title {
        color: #ffffff !important;
    }

    .dark .sf-calendar-popover-item-meta {
        color: rgba(226,232,240,.74) !important;
    }
</style>

<script id="sf-clickable-calendar-days-final-js">
    window.sfCloseCalendarDayPopover = function () {
        const popover = document.getElementById('sfCalendarPopover');
        if (!popover) return;

        popover.classList.remove('is-open');
        popover.setAttribute('aria-hidden', 'true');
    };

    window.sfOpenCalendarDayPopover = function (cell) {
        const popover = document.getElementById('sfCalendarPopover');
        const title = document.getElementById('sfCalendarPopoverTitle');
        const list = document.getElementById('sfCalendarPopoverList');

        if (!popover || !title || !list || !cell) return;

        let events = [];

        try {
            events = JSON.parse(cell.getAttribute('data-events') || '[]');
        } catch (error) {
            events = [];
        }

        const date = cell.getAttribute('data-date') || 'Selected Day';
        title.textContent = 'Events on ' + date;

        if (!events.length) {
            events = [{
                title: 'Event recorded',
                type: 'event',
                notes: 'There is an event on this day.',
                color: '#2563eb',
                date: date
            }];
        }

        list.innerHTML = events.map(function (event) {
            const color = event.color || '#2563eb';
            const itemTitle = event.title || 'Event';
            const type = event.type || 'event';
            const notes = event.notes || '';
            const eventDate = event.date || date;

            return `
                <div class="sf-calendar-popover-item" style="--event-color:${escapeHtml(color)};">
                    <div class="sf-calendar-popover-item-title">${escapeHtml(itemTitle)}</div>
                    ${notes ? `<div class="sf-calendar-popover-item-meta">${escapeHtml(notes)}</div>` : ''}
                    <div class="sf-calendar-popover-item-type">${escapeHtml(type)} · ${escapeHtml(eventDate)}</div>
                </div>
            `;
        }).join('');

        const rect = cell.getBoundingClientRect();
        const width = Math.min(430, window.innerWidth - 28);

        let left = rect.left;
        let top = rect.bottom + 12;

        if (left + width > window.innerWidth - 14) {
            left = window.innerWidth - width - 14;
        }

        if (top + 380 > window.innerHeight) {
            top = Math.max(14, rect.top - 380);
        }

        popover.style.left = left + 'px';
        popover.style.top = top + 'px';
        popover.classList.add('is-open');
        popover.setAttribute('aria-hidden', 'false');
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    document.addEventListener('click', function (event) {
        const popover = document.getElementById('sfCalendarPopover');
        if (!popover || !popover.classList.contains('is-open')) return;

        if (
            popover.contains(event.target) ||
            event.target.closest('.sf-calendar-day-clickable')
        ) {
            return;
        }

        window.sfCloseCalendarDayPopover();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            window.sfCloseCalendarDayPopover();
        }
    });
</script>



<style id="sf-calendar-click-modal-final">
    .sf-calendar-day-clickable {
        cursor: pointer !important;
        position: relative !important;
    }

    .sf-calendar-day-clickable:hover {
        transform: translateY(-2px) !important;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.75), 0 18px 38px rgba(15,23,42,.12) !important;
    }

    .sf-calendar-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99998;
        display: none;
        background: rgba(15,23,42,.38);
        backdrop-filter: blur(8px);
    }

    .sf-calendar-modal-backdrop.is-open {
        display: block;
    }

    .sf-calendar-modal {
        position: fixed;
        left: 50%;
        top: 50%;
        z-index: 99999;
        width: min(540px, calc(100vw - 32px));
        max-height: min(680px, calc(100vh - 48px));
        transform: translate(-50%, -50%);
        display: none;
        overflow: hidden;
        border-radius: 30px;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.14), transparent 36%),
            linear-gradient(180deg,#ffffff 0%,#f8fbff 100%);
        border: 1px solid rgba(215,226,229,.96);
        box-shadow: 0 35px 100px rgba(15,23,42,.28);
    }

    .sf-calendar-modal.is-open {
        display: block;
    }

    .sf-calendar-modal-head {
        padding: 20px 22px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        border-bottom: 1px solid rgba(215,226,229,.85);
    }

    .sf-calendar-modal-kicker {
        color: #2459d3;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .sf-calendar-modal-title {
        color: #0f172a;
        font-size: 25px;
        line-height: 1.08;
        font-weight: 950;
        letter-spacing: -.045em;
    }

    .sf-calendar-modal-close {
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 26px;
        line-height: 1;
        cursor: pointer;
        font-weight: 800;
    }

    .sf-calendar-modal-body {
        padding: 18px;
        display: grid;
        gap: 12px;
        max-height: 520px;
        overflow: auto;
    }

    .sf-calendar-modal-event {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        padding: 16px 16px 16px 24px;
        background: rgba(255,255,255,.90);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 12px 30px rgba(15,23,42,.045);
    }

    .sf-calendar-modal-event::before {
        content: "";
        position: absolute;
        left: 0;
        top: 14px;
        bottom: 14px;
        width: 6px;
        border-radius: 999px;
        background: var(--event-color, #2563eb);
    }

    .sf-calendar-modal-event-title {
        color: #0f172a;
        font-size: 16px;
        line-height: 1.25;
        font-weight: 950;
        letter-spacing: -.025em;
    }

    .sf-calendar-modal-event-notes {
        margin-top: 7px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.55;
        font-weight: 700;
    }

    .sf-calendar-modal-event-meta {
        margin-top: 11px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .sf-calendar-modal-pill {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        background: #eff6ff;
        color: #2459d3;
        border: 1px solid rgba(36,89,211,.14);
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dark .sf-calendar-modal {
        background: rgba(15,23,42,.96);
        border-color: rgba(255,255,255,.12);
    }

    .dark .sf-calendar-modal-head {
        border-bottom-color: rgba(255,255,255,.10);
    }

    .dark .sf-calendar-modal-title,
    .dark .sf-calendar-modal-event-title {
        color: #ffffff;
    }

    .dark .sf-calendar-modal-event {
        background: rgba(15,23,42,.82);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-calendar-modal-event-notes {
        color: rgba(226,232,240,.76);
    }
</style>


<div id="sfCalendarModalBackdrop" class="sf-calendar-modal-backdrop"></div>

<div id="sfCalendarModal" class="sf-calendar-modal" aria-hidden="true">
    <div class="sf-calendar-modal-head">
        <div>
            <div class="sf-calendar-modal-kicker">Calendar Day</div>
            <div id="sfCalendarModalTitle" class="sf-calendar-modal-title">Selected Day</div>
        </div>
        <button type="button" id="sfCalendarModalClose" class="sf-calendar-modal-close">×</button>
    </div>
    <div id="sfCalendarModalBody" class="sf-calendar-modal-body"></div>
</div>


<script id="sf-calendar-click-modal-final-js">
(function () {
    function escapeHtml(value) {
        return String(value || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function closeCalendarModal() {
        const modal = document.getElementById('sfCalendarModal');
        const backdrop = document.getElementById('sfCalendarModalBackdrop');

        if (modal) {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        }

        if (backdrop) {
            backdrop.classList.remove('is-open');
        }
    }

    function openCalendarModal(cell) {
        if (!cell) return;

        const hasEvent = cell.classList.contains('sf-calendar-day-clickable') || parseInt(cell.dataset.eventsCount || '0', 10) > 0;
        if (!hasEvent) return;

        const modal = document.getElementById('sfCalendarModal');
        const backdrop = document.getElementById('sfCalendarModalBackdrop');
        const title = document.getElementById('sfCalendarModalTitle');
        const body = document.getElementById('sfCalendarModalBody');

        if (!modal || !backdrop || !title || !body) return;

        let events = [];
        try {
            events = JSON.parse(cell.getAttribute('data-events') || cell.dataset.events || '[]');
        } catch (error) {
            events = [];
        }

        const date = cell.getAttribute('data-date') || cell.dataset.date || 'Selected Day';
        title.textContent = 'Events on ' + date;

        if (!events.length) {
            const dayText = (cell.innerText || '').trim().split(/\s+/)[0] || '';
            events = [{
                title: 'Event recorded',
                type: 'event',
                notes: dayText ? 'Event exists on day ' + dayText + '.' : 'Event exists on this date.',
                color: '#2563eb',
                date: date
            }];
        }

        body.innerHTML = events.map(function (event) {
            const color = event.color || '#2563eb';
            const eventTitle = event.title || 'Event';
            const type = event.type || 'event';
            const notes = event.notes || '';
            const eventDate = event.date || date;

            return `
                <div class="sf-calendar-modal-event" style="--event-color:${escapeHtml(color)};">
                    <div class="sf-calendar-modal-event-title">${escapeHtml(eventTitle)}</div>
                    ${notes ? `<div class="sf-calendar-modal-event-notes">${escapeHtml(notes)}</div>` : ''}
                    <div class="sf-calendar-modal-event-meta">
                        <span class="sf-calendar-modal-pill">${escapeHtml(type)}</span>
                        <span class="sf-calendar-modal-pill">${escapeHtml(eventDate)}</span>
                    </div>
                </div>
            `;
        }).join('');

        backdrop.classList.add('is-open');
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    window.sfPortalCalendarOpenFinal = openCalendarModal;
    window.sfPortalCalendarCloseFinal = closeCalendarModal;

    document.addEventListener('click', function (event) {
        const cell = event.target.closest('.portal-calendar-cell');

        if (cell && (cell.classList.contains('sf-calendar-day-clickable') || parseInt(cell.dataset.eventsCount || '0', 10) > 0)) {
            event.preventDefault();
            event.stopPropagation();
            openCalendarModal(cell);
            return;
        }

        if (
            event.target.id === 'sfCalendarModalBackdrop' ||
            event.target.id === 'sfCalendarModalClose'
        ) {
            event.preventDefault();
            closeCalendarModal();
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeCalendarModal();
        }

        if ((event.key === 'Enter' || event.key === ' ') && event.target.classList && event.target.classList.contains('sf-calendar-day-clickable')) {
            event.preventDefault();
            openCalendarModal(event.target);
        }
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.portal-calendar-cell').forEach(function (cell) {
            const hasDot = !!cell.querySelector('span[style*="border-radius:999px"]');
            const hasColor = (cell.getAttribute('style') || '').includes('background:');
            const count = parseInt(cell.dataset.eventsCount || '0', 10);

            if ((hasDot || hasColor || count > 0) && !cell.classList.contains('portal-calendar-cell--muted')) {
                cell.classList.add('sf-calendar-day-clickable');
                cell.setAttribute('role', 'button');
                cell.setAttribute('tabindex', '0');

                if (!cell.dataset.eventsCount || cell.dataset.eventsCount === '0') {
                    cell.dataset.eventsCount = '1';
                }

                if (!cell.dataset.date) {
                    const day = (cell.innerText || '').trim().split(/\s+/)[0] || '';
                    cell.dataset.date = day;
                }

                if (!cell.dataset.events) {
                    cell.dataset.events = JSON.stringify([{
                        title: 'Event recorded',
                        type: 'event',
                        notes: 'Event exists on this calendar day.',
                        color: '#2563eb',
                        date: cell.dataset.date || ''
                    }]);
                }
            }
        });
    });
})();
</script>


<style id="sf-money-requests-and-next-events-scroll">
    .sf-money-requests-card {
        overflow: hidden !important;
    }

    .sf-money-list {
        margin-top: 16px;
        display: grid;
        gap: 12px;
        max-height: 390px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .sf-money-list::-webkit-scrollbar,
    .sf-ne-list::-webkit-scrollbar {
        width: 8px;
    }

    .sf-money-list::-webkit-scrollbar-thumb,
    .sf-ne-list::-webkit-scrollbar-thumb {
        background: rgba(100,116,139,.30);
        border-radius: 999px;
    }

    .sf-money-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        border-radius: 24px;
        padding: 16px 18px;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 34%),
            rgba(255,255,255,.92);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 12px 28px rgba(15,23,42,.045);
    }

    .sf-money-title {
        color: #0f172a;
        font-size: 16px;
        line-height: 1.25;
        font-weight: 950;
        letter-spacing: -.025em;
    }

    .sf-money-meta {
        margin-top: 5px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.45;
        font-weight: 750;
    }

    .sf-money-badges {
        margin-top: 11px;
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .sf-money-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .sf-money-badge--success {
        background: #ecfdf5;
        color: #047857;
        border-color: rgba(16,185,129,.20);
    }

    .sf-money-badge--info {
        background: #eff6ff;
        color: #2459d3;
        border-color: rgba(36,89,211,.16);
    }

    .sf-money-badge--warning {
        background: #fff7ed;
        color: #c2410c;
        border-color: rgba(249,115,22,.20);
    }

    .sf-money-badge--danger {
        background: #fef2f2;
        color: #b91c1c;
        border-color: rgba(239,68,68,.18);
    }

    .sf-money-badge--purple {
        background: #f5f3ff;
        color: #6d28d9;
        border-color: rgba(124,58,237,.18);
    }

    .sf-money-badge--slate {
        background: #f8fafc;
        color: #475569;
        border-color: rgba(100,116,139,.16);
    }

    .sf-money-amount {
        color: #0f172a;
        font-size: 18px;
        line-height: 1;
        font-weight: 950;
        white-space: nowrap;
    }

    .sf-ne-list,
    .portal-list {
        scrollbar-width: thin;
    }

    .sf-ne-card .sf-ne-list,
    section:has(.portal-title-md):has(+ .portal-list) .portal-list {
        max-height: 560px !important;
        overflow-y: auto !important;
        padding-right: 4px !important;
    }

    .portal-card:has(.portal-title-md):has(.sf-ne-list) {
        overflow: hidden !important;
    }

    .dark .sf-money-item {
        background: rgba(15,23,42,.78);
        border-color: rgba(255,255,255,.10);
        box-shadow: 0 16px 42px rgba(0,0,0,.24);
    }

    .dark .sf-money-title,
    .dark .sf-money-amount {
        color: #ffffff;
    }

    .dark .sf-money-meta {
        color: rgba(226,232,240,.74);
    }

    @media (max-width: 760px) {
        .sf-money-item {
            grid-template-columns: 1fr;
        }

        .sf-money-amount {
            font-size: 16px;
        }
    }
</style>


<style id="sf-dashboard-scrollable-blocks-final">
    /*
        Keep portal dashboard blocks compact:
        - Latest Updates becomes scrollable after a few items.
        - Reimbursement Claims becomes scrollable after a few items.
        No data logic is changed here.
    */

    .sf-dash-updates-list,
    .sf-dashboard-reimbursement-list,
    .sf-reimbursement-claims-list,
    .sf-claims-list,
    .portal-reimbursement-claims-list {
        max-height: 390px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding-right: 6px !important;
        scroll-behavior: smooth !important;
    }

    .sf-dash-updates-list::-webkit-scrollbar,
    .sf-dashboard-reimbursement-list::-webkit-scrollbar,
    .sf-reimbursement-claims-list::-webkit-scrollbar,
    .sf-claims-list::-webkit-scrollbar,
    .portal-reimbursement-claims-list::-webkit-scrollbar {
        width: 8px !important;
    }

    .sf-dash-updates-list::-webkit-scrollbar-track,
    .sf-dashboard-reimbursement-list::-webkit-scrollbar-track,
    .sf-reimbursement-claims-list::-webkit-scrollbar-track,
    .sf-claims-list::-webkit-scrollbar-track,
    .portal-reimbursement-claims-list::-webkit-scrollbar-track {
        background: rgba(148, 163, 184, .12) !important;
        border-radius: 999px !important;
    }

    .sf-dash-updates-list::-webkit-scrollbar-thumb,
    .sf-dashboard-reimbursement-list::-webkit-scrollbar-thumb,
    .sf-reimbursement-claims-list::-webkit-scrollbar-thumb,
    .sf-claims-list::-webkit-scrollbar-thumb,
    .portal-reimbursement-claims-list::-webkit-scrollbar-thumb {
        background: rgba(36, 89, 211, .35) !important;
        border-radius: 999px !important;
    }

    .dark .sf-dash-updates-list::-webkit-scrollbar-thumb,
    .dark .sf-dashboard-reimbursement-list::-webkit-scrollbar-thumb,
    .dark .sf-reimbursement-claims-list::-webkit-scrollbar-thumb,
    .dark .sf-claims-list::-webkit-scrollbar-thumb,
    .dark .portal-reimbursement-claims-list::-webkit-scrollbar-thumb {
        background: rgba(96, 165, 250, .45) !important;
    }
</style>



<style id="sf-dashboard-scrollable-latest-claims-fix">
    /* Keep dashboard blocks compact: latest updates + reimbursement claims scroll instead of stretching the whole page */
    .sf-dash-updates-list {
        max-height: 430px !important;
        overflow-y: auto !important;
        padding-right: 6px !important;
        scroll-behavior: smooth !important;
    }

    .sf-reimbursement-claims-list,
    .sf-money-requests-list,
    .sf-dashboard-claims-list {
        max-height: 360px !important;
        overflow-y: auto !important;
        padding-right: 6px !important;
        scroll-behavior: smooth !important;
    }

    .sf-dash-updates-list::-webkit-scrollbar,
    .sf-reimbursement-claims-list::-webkit-scrollbar,
    .sf-money-requests-list::-webkit-scrollbar,
    .sf-dashboard-claims-list::-webkit-scrollbar {
        width: 8px;
    }

    .sf-dash-updates-list::-webkit-scrollbar-thumb,
    .sf-reimbursement-claims-list::-webkit-scrollbar-thumb,
    .sf-money-requests-list::-webkit-scrollbar-thumb,
    .sf-dashboard-claims-list::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(100, 116, 139, .28);
    }

    .dark .sf-dash-updates-list::-webkit-scrollbar-thumb,
    .dark .sf-reimbursement-claims-list::-webkit-scrollbar-thumb,
    .dark .sf-money-requests-list::-webkit-scrollbar-thumb,
    .dark .sf-dashboard-claims-list::-webkit-scrollbar-thumb {
        background: rgba(226, 232, 240, .25);
    }
</style>

@endsection


<style>
.sf-event-md-icon {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(37, 99, 235, .10);
    color: #1d4ed8;
    margin-right: 10px;
    vertical-align: middle;
    flex: 0 0 auto;
}
.sf-event-md-icon .material-symbols-rounded {
    font-size: 20px;
    line-height: 1;
}
.dark .sf-event-md-icon {
    background: rgba(34, 211, 238, .14);
    color: #67e8f9;
}
</style>


