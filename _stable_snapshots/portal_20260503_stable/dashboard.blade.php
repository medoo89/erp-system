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






<style id="sf-portal-calendar-popover-style">
    .sf-calendar-legend {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .sf-calendar-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.78);
        border: 1px solid rgba(15,23,42,.08);
        color: #334155;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .05em;
        text-transform: uppercase;
        box-shadow: 0 8px 20px rgba(15,23,42,.035);
    }

    .sf-calendar-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
        flex-shrink: 0;
    }

    .sf-calendar-day-clickable {
        cursor: pointer;
        transition: transform .16s ease, box-shadow .16s ease;
    }

    .sf-calendar-day-clickable:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(15,23,42,.10);
    }

    .sf-calendar-popover {
        position: fixed;
        z-index: 9999;
        width: min(420px, calc(100vw - 32px));
        border-radius: 26px;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.13), transparent 36%),
            linear-gradient(180deg,#ffffff 0%,#f8fbff 100%);
        border: 1px solid rgba(215,226,229,.95);
        box-shadow: 0 28px 80px rgba(15,23,42,.22);
        padding: 16px;
        display: none;
    }

    .sf-calendar-popover.is-open {
        display: block;
    }

    .sf-calendar-popover-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(215,226,229,.85);
    }

    .sf-calendar-popover-kicker {
        color: #2459d3;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .14em;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .sf-calendar-popover-title {
        color: #0f172a;
        font-size: 20px;
        line-height: 1.1;
        font-weight: 950;
        letter-spacing: -.04em;
    }

    .sf-calendar-popover-close {
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        font-weight: 700;
    }

    .sf-calendar-popover-list {
        display: grid;
        gap: 10px;
        margin-top: 13px;
        max-height: 330px;
        overflow: auto;
        padding-right: 2px;
    }

    .sf-calendar-popover-item {
        position: relative;
        border-radius: 18px;
        background: rgba(255,255,255,.88);
        border: 1px solid rgba(15,23,42,.08);
        padding: 13px 13px 13px 17px;
        box-shadow: 0 10px 24px rgba(15,23,42,.04);
        overflow: hidden;
    }

    .sf-calendar-popover-item::before {
        content: "";
        position: absolute;
        left: 0;
        top: 12px;
        bottom: 12px;
        width: 5px;
        border-radius: 999px;
        background: var(--event-color, #2563eb);
    }

    .sf-calendar-popover-item-title {
        color: #0f172a;
        font-size: 14px;
        line-height: 1.3;
        font-weight: 950;
        letter-spacing: -.02em;
    }

    .sf-calendar-popover-item-meta {
        margin-top: 5px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
        font-weight: 700;
    }

    .sf-calendar-popover-item-type {
        margin-top: 8px;
        display: inline-flex;
        border-radius: 999px;
        padding: 6px 9px;
        background: #eff6ff;
        color: #2459d3;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .dark .sf-calendar-legend-item,
    .dark .sf-calendar-popover,
    .dark .sf-calendar-popover-item {
        background: rgba(15,23,42,.86);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-calendar-popover-title,
    .dark .sf-calendar-popover-item-title {
        color: #ffffff;
    }

    .dark .sf-calendar-popover-item-meta,
    .dark .sf-calendar-legend-item {
        color: rgba(226,232,240,.74);
    }
</style>


@php
    $dashboardUpdateIcon = function (?string $status, ?string $title = null) {
        $text = strtolower(trim(($status ?? '') . ' ' . ($title ?? '')));

        if (str_contains($text, 'salary') || str_contains($text, 'paid') || str_contains($text, 'bank')) return 'salary';
        if (str_contains($text, 'rotation') || str_contains($text, 'mobilization') || str_contains($text, 'demobilization')) return 'rotation';
        if (str_contains($text, 'travel') || str_contains($text, 'ticket') || str_contains($text, 'flight')) return 'travel';
        if (str_contains($text, 'file') || str_contains($text, 'document')) return 'file';
        if (str_contains($text, 'medical')) return 'medical';
        if (str_contains($text, 'visa')) return 'visa';

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

        if (str_contains($text, 'passport')) return 'id';
        if (str_contains($text, 'visa')) return 'verified';
        if (str_contains($text, 'medical')) return 'medical';
        if (str_contains($text, 'certificate')) return 'award';
        if (str_contains($text, 'contract')) return 'contract';
        if (str_contains($text, 'ticket') || str_contains($text, 'travel')) return 'flight';
        if (str_contains($text, 'cv') || str_contains($text, 'resume')) return 'badge';
        if (str_contains($text, 'photo') || str_contains($text, 'image')) return 'image';

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
                            $eventBg = match ($cell['eventType'] ?? null) {
                                'travel' => '#fff7ed',
                                'visa' => '#eff6ff',
                                'medical' => '#ecfdf5',
                                default => '#ffffff',
                            };

                            $eventBorder = match ($cell['eventType'] ?? null) {
                                'travel' => '#fdba74',
                                'visa' => '#93c5fd',
                                'medical' => '#86efac',
                                default => '#d9e4ef',
                            };

                            $eventDot = match ($cell['eventType'] ?? null) {
                                'travel' => '#d97706',
                                'visa' => '#2563eb',
                                'medical' => '#16a34a',
                                default => '#94a3b8',
                            };
                        @endphp

                        <div class="
                            portal-calendar-cell
                            {{ !$cell['isCurrentMonth'] ? 'portal-calendar-cell--muted' : '' }}
                            {{ $cell['isToday'] ? 'portal-calendar-cell--today' : '' }}
                        "
                        style="
                            {{ $cell['hasEvent'] ? 'background:' . $eventBg . '; border-color:' . $eventBorder . ';' : '' }}
                        ">
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
                <span class="sf-calendar-legend-pill">
                    <span class="sf-calendar-legend-dot" style="background:#16a34a;"></span>
                    Rotation
                </span>

                <span class="sf-calendar-legend-pill">
                    <span class="sf-calendar-legend-dot" style="background:#0ea5e9;"></span>
                    Travel / Mobilization
                </span>

                <span class="sf-calendar-legend-pill">
                    <span class="sf-calendar-legend-dot" style="background:#d97706;"></span>
                    Ticket / Travel Request
                </span>

                <span class="sf-calendar-legend-pill">
                    <span class="sf-calendar-legend-dot" style="background:#2563eb;"></span>
                    Visa / Documents
                </span>

                <span class="sf-calendar-legend-pill">
                    <span class="sf-calendar-legend-dot" style="background:#94a3b8;"></span>
                    Other
                </span>
            </div>

        </div>

        <div style="display:flex;flex-direction:column;gap:18px;">
            <section class="portal-card portal-card-soft">
                <div class="portal-section-head">
                    <div class="portal-title-md">Next Events</div>
                    <span class="portal-badge portal-badge--slate">Important</span>
                </div>

                @if($nextEvents->count())
                    <div class="portal-list">
                        @foreach($nextEvents as $event)
                            <div class="portal-list-item">
                                <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                                    <div class="portal-list-title">{{ $event['title'] }}</div>
                                    <span class="portal-badge portal-badge--info">{{ $event['type'] }}</span>
                                </div>
                                <div class="portal-list-meta">{{ $event['date']->format('Y-m-d') }}</div>
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
                        A quick timeline of your latest salary, rotation, travel, file, and portal updates.
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
                            if (($updateStatus ?? null) === 'paid') {
                                $badgeClass .= ' sf-dash-update-badge--success';
                            } elseif(in_array(($updateStatus ?? ''), ['bank_rejected', 'cancelled'], true)) {
                                $badgeClass .= ' sf-dash-update-badge--danger';
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

<script>
    window.portalOpenCalendarDay = function (cell) {
        const count = parseInt(cell.dataset.eventsCount || '0', 10);
        const popover = document.getElementById('portalDayPopover');
        const title = document.getElementById('portalDayPopoverTitle');
        const list = document.getElementById('portalDayPopoverList');

        if (!popover || !title || !list || count < 1) return;

        let events = [];
        try {
            events = JSON.parse(cell.dataset.events || '[]');
        } catch (e) {
            events = [];
        }

        title.textContent = 'Events on ' + (cell.dataset.date || 'selected day');
        list.innerHTML = '';

        if (!events.length) {
            list.innerHTML = '<div class="portal-day-event"><div class="portal-day-event-main"><strong>No details available</strong><span>Event exists on this date.</span></div></div>';
        } else {
            events.forEach(function (event) {
                const item = document.createElement('div');
                item.className = 'portal-day-event';
                item.style.setProperty('--event-color', event.color || '#2563eb');

                const dot = document.createElement('div');
                dot.className = 'portal-day-event-dot';

                const main = document.createElement('div');
                main.className = 'portal-day-event-main';

                const strong = document.createElement('strong');
                strong.textContent = event.title || 'Event';

                const meta = document.createElement('span');
                meta.textContent = [event.type ? event.type.replaceAll('_', ' ') : null, event.notes || null].filter(Boolean).join(' · ');

                main.appendChild(strong);
                main.appendChild(meta);
                item.appendChild(dot);
                item.appendChild(main);
                list.appendChild(item);
            });
        }

        popover.style.display = 'block';
        popover.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    window.portalCloseCalendarDay = function () {
        const popover = document.getElementById('portalDayPopover');
        if (popover) popover.style.display = 'none';
    };
</script>


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

<script>
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
        } catch (e) {
            events = [];
        }

        const date = cell.getAttribute('data-date') || 'Selected Day';

        title.textContent = date;

        if (!events.length) {
            list.innerHTML = `
                <div class="sf-calendar-popover-item">
                    <div class="sf-calendar-popover-item-title">Event recorded</div>
                    <div class="sf-calendar-popover-item-meta">There is an event on this day.</div>
                    <div class="sf-calendar-popover-item-type">Event</div>
                </div>
            `;
        } else {
            list.innerHTML = events.map(function (event) {
                const color = event.color || '#2563eb';
                const itemTitle = event.title || 'Event';
                const type = event.type || 'event';
                const notes = event.notes || '';

                return `
                    <div class="sf-calendar-popover-item" style="--event-color:${color};">
                        <div class="sf-calendar-popover-item-title">${escapeHtml(itemTitle)}</div>
                        ${notes ? `<div class="sf-calendar-popover-item-meta">${escapeHtml(notes)}</div>` : ''}
                        <div class="sf-calendar-popover-item-type">${escapeHtml(type)}</div>
                    </div>
                `;
            }).join('');
        }

        const rect = cell.getBoundingClientRect();
        const width = Math.min(420, window.innerWidth - 32);

        let left = rect.left;
        let top = rect.bottom + 12;

        if (left + width > window.innerWidth - 16) {
            left = window.innerWidth - width - 16;
        }

        if (top + 360 > window.innerHeight) {
            top = Math.max(16, rect.top - 360);
        }

        popover.style.left = left + 'px';
        popover.style.top = top + 'px';
        popover.classList.add('is-open');
        popover.setAttribute('aria-hidden', 'false');
    };

    function escapeHtml(value) {
        return String(value)
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

@endsection


<style>
    /* FINAL DIRECT PATCH ON /portal PAGE */
    .portal-calendar-cell {
        position: relative !important;
        cursor: pointer !important;
    }

    .portal-calendar-cell.sf-no-events {
        cursor: default !important;
    }

    .sf-portal-force-dots {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        display: flex !important;
        gap: 5px !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        max-width: 62px !important;
        z-index: 100 !important;
        pointer-events: none !important;
    }

    .sf-portal-force-dot {
        width: 11px !important;
        height: 11px !important;
        min-width: 11px !important;
        min-height: 11px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        background-color: var(--event-color, #2563eb) !important;
        box-shadow: 0 0 0 5px color-mix(in srgb, var(--event-color, #2563eb) 18%, transparent) !important;
    }

    .sf-portal-force-card {
        position: relative !important;
        display: flex !important;
        align-items: stretch !important;
        gap: 12px !important;
        background: color-mix(in srgb, var(--event-color, #2563eb) 12%, #ffffff) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 32%, transparent) !important;
        box-shadow: 0 12px 28px color-mix(in srgb, var(--event-color, #2563eb) 10%, transparent) !important;
    }

    .sf-portal-force-card::before {
        content: "" !important;
        width: 7px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        align-self: stretch !important;
        flex: 0 0 7px !important;
    }

    .dark .sf-portal-force-card {
        background: color-mix(in srgb, var(--event-color, #2563eb) 22%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 36%, transparent) !important;
    }

    .sf-portal-force-badge {
        background: var(--event-color, #2563eb) !important;
        color: #ffffff !important;
        border-color: var(--event-color, #2563eb) !important;
    }

    .sf-portal-day-box {
        margin-top: 14px !important;
        border-radius: 24px !important;
        padding: 16px !important;
        background: rgba(255,255,255,.96) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 18px 42px rgba(15,23,42,.08) !important;
    }

    .dark .sf-portal-day-box {
        background: rgba(15,23,42,.78) !important;
        border-color: rgba(148,163,184,.18) !important;
    }

    .sf-portal-day-head {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 12px !important;
        margin-bottom: 12px !important;
    }

    .sf-portal-day-title {
        color: #0f172a !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        letter-spacing: -.03em !important;
    }

    .dark .sf-portal-day-title {
        color: #fff !important;
    }

    .sf-portal-day-close {
        border: 0 !important;
        border-radius: 999px !important;
        width: 32px !important;
        height: 32px !important;
        cursor: pointer !important;
        background: #eef6ff !important;
        color: #234b74 !important;
        font-weight: 950 !important;
    }

    .sf-portal-day-list {
        display: grid !important;
        gap: 10px !important;
    }

    .sf-portal-day-item {
        display: flex !important;
        gap: 10px !important;
        align-items: flex-start !important;
        border-radius: 18px !important;
        padding: 12px 14px !important;
        background: color-mix(in srgb, var(--event-color, #2563eb) 10%, #ffffff) !important;
        border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 24%, transparent) !important;
    }

    .dark .sf-portal-day-item {
        background: color-mix(in srgb, var(--event-color, #2563eb) 18%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 28%, transparent) !important;
    }

    .sf-portal-day-dot {
        width: 11px !important;
        height: 11px !important;
        margin-top: 5px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        flex: 0 0 auto !important;
    }

    .sf-portal-day-main strong {
        display: block !important;
        color: #0f172a !important;
        font-size: 13px !important;
        font-weight: 950 !important;
    }

    .dark .sf-portal-day-main strong {
        color: #fff !important;
    }

    .sf-portal-day-main span {
        display: block !important;
        margin-top: 3px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    .dark .sf-portal-day-main span {
        color: #94a3b8 !important;
    }
</style>

<script>
(function () {
    const colors = {
        ticket_travel: '#0ea5e9',
        travel: '#0ea5e9',
        mobilization: '#0ea5e9',
        rotation_start: '#10b981',
        rotation_end: '#14b8a6',
        demobilization: '#6366f1',
        visa_expiry: '#f97316',
        medical_expiry: '#ef4444',
        contract_end: '#8b5cf6',
        passport_expiry: '#2563eb',
        certificate_expiry: '#7c3aed',
        desert_pass_expiry: '#d97706',
        default: '#2563eb'
    };

    function normalize(text) {
        return String(text || '')
            .trim()
            .toLowerCase()
            .replace(/[\s\-\/]+/g, '_')
            .replace(/[^a-z0-9_]/g, '');
    }

    function colorFor(text) {
        const key = normalize(text);

        if (colors[key]) return colors[key];

        if (key.includes('ticket') || key.includes('travel') || key.includes('mobilization')) return colors.ticket_travel;
        if (key.includes('rotation_start')) return colors.rotation_start;
        if (key.includes('rotation_end')) return colors.rotation_end;
        if (key.includes('demobilization')) return colors.demobilization;
        if (key.includes('visa')) return colors.visa_expiry;
        if (key.includes('medical')) return colors.medical_expiry;
        if (key.includes('contract')) return colors.contract_end;
        if (key.includes('passport')) return colors.passport_expiry;
        if (key.includes('certificate')) return colors.certificate_expiry;
        if (key.includes('desert')) return colors.desert_pass_expiry;

        return colors.default;
    }

    function monthYearFromPage() {
        const text = document.body.innerText || '';
        const match = text.match(/\b(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+(\d{4})\b/i);
        const now = new Date();

        if (!match) {
            return { month: now.getMonth() + 1, year: now.getFullYear() };
        }

        const months = {
            jan:1,january:1,
            feb:2,february:2,
            mar:3,march:3,
            apr:4,april:4,
            may:5,
            jun:6,june:6,
            jul:7,july:7,
            aug:8,august:8,
            sep:9,september:9,
            oct:10,october:10,
            nov:11,november:11,
            dec:12,december:12,
        };

        return {
            month: months[match[1].toLowerCase()] || now.getMonth() + 1,
            year: parseInt(match[2], 10) || now.getFullYear()
        };
    }

    function dateKey(year, month, day) {
        return String(year).padStart(4, '0') + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
    }

    function nextEventsContainer() {
        const nodes = Array.from(document.querySelectorAll('h1,h2,h3,h4,.portal-title-md,.portal-title,.portal-card-title,div'));
        const title = nodes.find(el => {
            const t = (el.textContent || '').trim();
            return /^Next Events$/i.test(t);
        });

        return title ? (title.closest('.portal-card,section,article') || title.parentElement) : null;
    }

    function collectNextEvents() {
        const container = nextEventsContainer();
        const map = {};

        if (!container) return map;

        const candidates = Array.from(container.querySelectorAll('div,article,li'))
            .filter(el => {
                const text = (el.textContent || '').trim();
                return /20\d{2}-\d{2}-\d{2}/.test(text) && text.length < 500;
            })
            .filter((el, _, arr) => {
                return !arr.some(other => other !== el && other.contains(el) && /20\d{2}-\d{2}-\d{2}/.test(other.textContent || ''));
            });

        candidates.forEach(card => {
            const text = card.textContent || '';
            const dateMatch = text.match(/20\d{2}-\d{2}-\d{2}/);
            if (!dateMatch) return;

            const date = dateMatch[0];
            const lines = text.split('\n').map(v => v.trim()).filter(Boolean);

            const title = lines.find(v =>
                !/20\d{2}-\d{2}-\d{2}/.test(v)
                && !/^important$/i.test(v)
                && !/^[A-Z0-9_]{3,}$/.test(v)
            ) || 'Event';

            const type = lines.find(v => /^[A-Z0-9_]{3,}$/.test(v)) || title;
            const color = colorFor(type + ' ' + title);

            card.classList.add('sf-portal-force-card');
            card.style.setProperty('--event-color', color);

            Array.from(card.querySelectorAll('span,div')).forEach(el => {
                const t = (el.textContent || '').trim();
                if (/^[A-Z0-9_]{3,}$/.test(t)) {
                    el.classList.add('sf-portal-force-badge');
                    el.style.setProperty('--event-color', color);
                }
            });

            if (!map[date]) map[date] = [];
            map[date].push({ date, title, type, color });
        });

        return map;
    }

    function cleanCalendarCell(cell) {
        cell.querySelectorAll('.sf-portal-force-dots, .portal-calendar-event-dot').forEach(el => el.remove());
    }

    function applyCalendarDots(eventMap) {
        const { month, year } = monthYearFromPage();
        const cells = Array.from(document.querySelectorAll('.portal-calendar-cell'));

        cells.forEach(cell => {
            cleanCalendarCell(cell);

            const text = cell.textContent || '';
            const dayMatch = text.match(/\b\d{1,2}\b/);

            if (!dayMatch) return;

            const day = parseInt(dayMatch[0], 10);

            if (!day || cell.classList.contains('portal-calendar-cell--muted')) {
                cell.classList.add('sf-no-events');
                cell.onclick = null;
                return;
            }

            const key = dateKey(year, month, day);
            const events = eventMap[key] || [];

            if (!events.length) {
                cell.classList.add('sf-no-events');
                cell.onclick = null;
                return;
            }

            cell.classList.remove('sf-no-events');

            const wrap = document.createElement('span');
            wrap.className = 'sf-portal-force-dots';

            events.slice(0, 4).forEach(event => {
                const dot = document.createElement('span');
                dot.className = 'sf-portal-force-dot';
                dot.style.setProperty('--event-color', event.color || colors.default);
                wrap.appendChild(dot);
            });

            cell.appendChild(wrap);

            cell.onclick = function () {
                showDayDetails(key, events, cell);
            };
        });
    }

    function ensureDayBox(anchor) {
        let box = document.getElementById('sfPortalDayBox');

        if (!box) {
            box = document.createElement('div');
            box.id = 'sfPortalDayBox';
            box.className = 'sf-portal-day-box';
            box.style.display = 'none';
            box.innerHTML = `
                <div class="sf-portal-day-head">
                    <div class="sf-portal-day-title" id="sfPortalDayTitle">Selected Day</div>
                    <button type="button" class="sf-portal-day-close" onclick="document.getElementById('sfPortalDayBox').style.display='none'">×</button>
                </div>
                <div class="sf-portal-day-list" id="sfPortalDayList"></div>
            `;

            const calendar = document.querySelector('.portal-calendar-large') || anchor.closest('.portal-card') || anchor.parentElement;
            calendar.insertAdjacentElement('afterend', box);
        }

        return box;
    }

    function showDayDetails(date, events, anchor) {
        const box = ensureDayBox(anchor);
        const title = document.getElementById('sfPortalDayTitle');
        const list = document.getElementById('sfPortalDayList');

        if (!box || !title || !list) return;

        title.textContent = 'Events on ' + date;
        list.innerHTML = '';

        events.forEach(event => {
            const item = document.createElement('div');
            item.className = 'sf-portal-day-item';
            item.style.setProperty('--event-color', event.color || colors.default);
            item.innerHTML = `
                <div class="sf-portal-day-dot"></div>
                <div class="sf-portal-day-main">
                    <strong>${event.title || 'Event'}</strong>
                    <span>${String(event.type || '').replaceAll('_', ' ')}</span>
                </div>
            `;

            list.appendChild(item);
        });

        box.style.display = 'block';
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function runPortalCalendarColoring() {
        const map = collectNextEvents();
        applyCalendarDots(map);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(runPortalCalendarColoring, 200);
        setTimeout(runPortalCalendarColoring, 800);
        setTimeout(runPortalCalendarColoring, 1600);
        setTimeout(runPortalCalendarColoring, 2600);
    });

    window.addEventListener('load', function () {
        setTimeout(runPortalCalendarColoring, 300);
        setTimeout(runPortalCalendarColoring, 1200);
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('a,button');
        if (!btn) return;

        const text = btn.textContent || '';
        if (/prev|next/i.test(text)) {
            setTimeout(runPortalCalendarColoring, 500);
            setTimeout(runPortalCalendarColoring, 1200);
        }
    });
})();
</script>


<style>
    /* FINAL PORTAL MD3 POLISH — dashboard only */
    :root {
        --sf-md3-primary: #2563eb;
        --sf-md3-teal: #14b8a6;
        --sf-md3-ink: #0f172a;
        --sf-md3-muted: #64748b;
        --sf-md3-surface: rgba(255,255,255,.92);
        --sf-md3-border: rgba(15,23,42,.08);
    }

    .portal-card,
    .portal-kpi,
    .portal-panel,
    .portal-section,
    .portal-calendar-large,
    .portal-calendar-cell,
    .portal-update-card,
    .portal-file-card,
    .portal-slip-card {
        border-radius: 28px !important;
        border-color: var(--sf-md3-border) !important;
        box-shadow: 0 16px 40px rgba(15,23,42,.055) !important;
    }

    .portal-card,
    .portal-panel,
    .portal-section {
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.07), transparent 36%),
            rgba(255,255,255,.94) !important;
    }

    .dark .portal-card,
    .dark .portal-panel,
    .dark .portal-section {
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.11), transparent 36%),
            rgba(15,23,42,.74) !important;
        border-color: rgba(148,163,184,.16) !important;
    }

    .portal-btn,
    .portal-nav a,
    .portal-nav button,
    button,
    a.portal-btn {
        border-radius: 999px !important;
        font-weight: 850 !important;
    }

    .portal-title,
    .portal-title-md,
    .portal-card-title {
        letter-spacing: -.04em !important;
        font-weight: 950 !important;
    }

    /* Next Events final clean layout */
    .sf-portal-force-card {
        display: grid !important;
        grid-template-columns: 7px minmax(0, 1fr) auto !important;
        align-items: center !important;
        gap: 14px !important;
        min-height: 64px !important;
        padding: 14px 18px !important;
        border-radius: 22px !important;
        background: color-mix(in srgb, var(--event-color, #2563eb) 12%, #ffffff) !important;
        border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 32%, transparent) !important;
        box-shadow: 0 12px 28px color-mix(in srgb, var(--event-color, #2563eb) 10%, transparent) !important;
    }

    .sf-portal-force-card::before {
        content: "" !important;
        width: 7px !important;
        height: 36px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        grid-column: 1 !important;
    }

    .sf-portal-force-card .sf-next-main {
        grid-column: 2 !important;
        min-width: 0 !important;
    }

    .sf-portal-force-card .sf-next-title {
        color: #0f172a !important;
        font-size: 15px !important;
        font-weight: 950 !important;
        letter-spacing: -.025em !important;
        line-height: 1.25 !important;
    }

    .sf-portal-force-card .sf-next-type {
        margin-top: 4px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 750 !important;
    }

    .sf-portal-force-card .sf-next-date {
        grid-column: 3 !important;
        justify-self: end !important;
        color: #334155 !important;
        background: rgba(255,255,255,.74) !important;
        border: 1px solid rgba(15,23,42,.07) !important;
        border-radius: 999px !important;
        padding: 8px 12px !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        white-space: nowrap !important;
    }

    .dark .sf-portal-force-card {
        background: color-mix(in srgb, var(--event-color, #2563eb) 22%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 36%, transparent) !important;
    }

    .dark .sf-portal-force-card .sf-next-title {
        color: #fff !important;
    }

    .dark .sf-portal-force-card .sf-next-type {
        color: #94a3b8 !important;
    }

    .dark .sf-portal-force-card .sf-next-date {
        color: #e2e8f0 !important;
        background: rgba(255,255,255,.08) !important;
        border-color: rgba(148,163,184,.14) !important;
    }

    .sf-portal-force-badge {
        display: none !important;
    }

    /* hide old inline day box if previous patch created it */
    #sfPortalDayBox {
        display: none !important;
    }

    /* MD3 popup for calendar day */
    .sf-md3-day-modal-backdrop {
        position: fixed !important;
        inset: 0 !important;
        z-index: 999999 !important;
        display: none;
        place-items: center !important;
        padding: 18px !important;
        background: rgba(15,23,42,.58) !important;
        backdrop-filter: blur(10px) !important;
    }

    .sf-md3-day-modal {
        width: min(560px, 100%) !important;
        max-height: min(720px, 88vh) !important;
        overflow: hidden !important;
        border-radius: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.10), transparent 38%),
            #ffffff !important;
        border: 1px solid rgba(15,23,42,.10) !important;
        box-shadow: 0 34px 90px rgba(0,0,0,.28) !important;
    }

    .dark .sf-md3-day-modal {
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.15), transparent 38%),
            #0f172a !important;
        border-color: rgba(148,163,184,.18) !important;
    }

    .sf-md3-day-modal-head {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        padding: 20px 22px !important;
        border-bottom: 1px solid rgba(15,23,42,.08) !important;
    }

    .dark .sf-md3-day-modal-head {
        border-color: rgba(148,163,184,.16) !important;
    }

    .sf-md3-day-kicker {
        width: fit-content !important;
        border-radius: 999px !important;
        padding: 6px 10px !important;
        background: #eef6ff !important;
        color: #234b74 !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
        margin-bottom: 8px !important;
    }

    .sf-md3-day-title {
        color: #0f172a !important;
        font-size: 22px !important;
        font-weight: 950 !important;
        letter-spacing: -.05em !important;
    }

    .dark .sf-md3-day-title {
        color: #fff !important;
    }

    .sf-md3-day-close {
        border: 0 !important;
        width: 40px !important;
        height: 40px !important;
        border-radius: 999px !important;
        cursor: pointer !important;
        background: #eef6ff !important;
        color: #234b74 !important;
        font-size: 22px !important;
        font-weight: 950 !important;
    }

    .sf-md3-day-list {
        padding: 18px !important;
        overflow-y: auto !important;
        max-height: 560px !important;
        display: grid !important;
        gap: 12px !important;
    }

    .sf-md3-day-item {
        display: grid !important;
        grid-template-columns: 12px minmax(0, 1fr) !important;
        gap: 12px !important;
        align-items: start !important;
        border-radius: 22px !important;
        padding: 14px !important;
        background: color-mix(in srgb, var(--event-color, #2563eb) 11%, #ffffff) !important;
        border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 28%, transparent) !important;
    }

    .dark .sf-md3-day-item {
        background: color-mix(in srgb, var(--event-color, #2563eb) 20%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 34%, transparent) !important;
    }

    .sf-md3-day-dot {
        width: 12px !important;
        height: 12px !important;
        margin-top: 5px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        box-shadow: 0 0 0 5px color-mix(in srgb, var(--event-color, #2563eb) 15%, transparent) !important;
    }

    .sf-md3-day-item-title {
        color: #0f172a !important;
        font-size: 15px !important;
        font-weight: 950 !important;
        line-height: 1.35 !important;
    }

    .dark .sf-md3-day-item-title {
        color: #fff !important;
    }

    .sf-md3-day-item-meta {
        margin-top: 4px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 750 !important;
    }

    .dark .sf-md3-day-item-meta {
        color: #94a3b8 !important;
    }

    .sf-md3-no-events {
        border-radius: 22px !important;
        padding: 16px !important;
        background: #f8fafc !important;
        color: #64748b !important;
        font-weight: 850 !important;
    }

    .dark .sf-md3-no-events {
        background: rgba(255,255,255,.06) !important;
        color: #94a3b8 !important;
    }

    @media (max-width: 720px) {
        .sf-portal-force-card {
            grid-template-columns: 6px minmax(0, 1fr) !important;
        }

        .sf-portal-force-card .sf-next-date {
            grid-column: 2 !important;
            justify-self: start !important;
            margin-top: 8px !important;
        }
    }
</style>

<div id="sfMd3DayModalBackdrop" class="sf-md3-day-modal-backdrop">
    <div class="sf-md3-day-modal">
        <div class="sf-md3-day-modal-head">
            <div>
                <div class="sf-md3-day-kicker">Calendar Day</div>
                <div id="sfMd3DayModalTitle" class="sf-md3-day-title">Selected Day</div>
            </div>
            <button type="button" id="sfMd3DayModalClose" class="sf-md3-day-close">×</button>
        </div>
        <div id="sfMd3DayModalList" class="sf-md3-day-list"></div>
    </div>
</div>

<script>
(function () {
    const colors = {
        ticket_travel: '#0ea5e9',
        travel: '#0ea5e9',
        mobilization: '#0ea5e9',
        rotation_start: '#10b981',
        rotation_end: '#14b8a6',
        demobilization: '#6366f1',
        visa_expiry: '#f97316',
        medical_expiry: '#ef4444',
        contract_end: '#8b5cf6',
        passport_expiry: '#2563eb',
        certificate_expiry: '#7c3aed',
        desert_pass_expiry: '#d97706',
        default: '#2563eb'
    };

    function nice(text) {
        const raw = String(text || '').trim();
        if (!raw) return '';
        return raw
            .toLowerCase()
            .replace(/_/g, ' ')
            .replace(/\b\w/g, function (m) { return m.toUpperCase(); });
    }

    function normalize(text) {
        return String(text || '')
            .trim()
            .toLowerCase()
            .replace(/[\s\-\/]+/g, '_')
            .replace(/[^a-z0-9_]/g, '');
    }

    function colorFor(text) {
        const key = normalize(text);

        if (colors[key]) return colors[key];
        if (key.includes('ticket') || key.includes('travel') || key.includes('mobilization')) return colors.ticket_travel;
        if (key.includes('rotation_start')) return colors.rotation_start;
        if (key.includes('rotation_end')) return colors.rotation_end;
        if (key.includes('demobilization')) return colors.demobilization;
        if (key.includes('visa')) return colors.visa_expiry;
        if (key.includes('medical')) return colors.medical_expiry;
        if (key.includes('contract')) return colors.contract_end;
        if (key.includes('passport')) return colors.passport_expiry;
        if (key.includes('certificate')) return colors.certificate_expiry;
        if (key.includes('desert')) return colors.desert_pass_expiry;

        return colors.default;
    }

    function currentMonthYear() {
        const text = document.body.innerText || '';
        const match = text.match(/\b(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+(\d{4})\b/i);
        const now = new Date();

        if (!match) return { month: now.getMonth() + 1, year: now.getFullYear() };

        const months = {
            jan:1,january:1,
            feb:2,february:2,
            mar:3,march:3,
            apr:4,april:4,
            may:5,
            jun:6,june:6,
            jul:7,july:7,
            aug:8,august:8,
            sep:9,september:9,
            oct:10,october:10,
            nov:11,november:11,
            dec:12,december:12,
        };

        return {
            month: months[match[1].toLowerCase()] || now.getMonth() + 1,
            year: parseInt(match[2], 10) || now.getFullYear()
        };
    }

    function dateKey(year, month, day) {
        return String(year).padStart(4, '0') + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
    }

    function nextEventsContainer() {
        const title = Array.from(document.querySelectorAll('h1,h2,h3,h4,.portal-title-md,.portal-title,.portal-card-title,div'))
            .find(el => /^Next Events$/i.test((el.textContent || '').trim()));

        return title ? (title.closest('.portal-card,section,article') || title.parentElement) : null;
    }

    function collectEventsAndRewriteCards() {
        const container = nextEventsContainer();
        const map = {};
        if (!container) return map;

        const candidates = Array.from(container.querySelectorAll('div,article,li'))
            .filter(el => {
                const text = (el.textContent || '').trim();
                return /20\d{2}-\d{2}-\d{2}/.test(text) && text.length < 500;
            })
            .filter((el, _, arr) => !arr.some(other => other !== el && other.contains(el) && /20\d{2}-\d{2}-\d{2}/.test(other.textContent || '')));

        candidates.forEach(card => {
            if (card.dataset.sfRewritten === '1') {
                const date = card.dataset.sfDate;
                const title = card.dataset.sfTitle;
                const type = card.dataset.sfType;
                const color = card.dataset.sfColor || colorFor(type + ' ' + title);
                if (!map[date]) map[date] = [];
                map[date].push({ date, title, type, color });
                return;
            }

            const text = card.textContent || '';
            const dateMatch = text.match(/20\d{2}-\d{2}-\d{2}/);
            if (!dateMatch) return;

            const date = dateMatch[0];
            const lines = text.split('\n').map(v => v.trim()).filter(Boolean);

            const title = lines.find(v =>
                !/20\d{2}-\d{2}-\d{2}/.test(v)
                && !/^important$/i.test(v)
                && !/^[A-Z0-9_]{3,}$/.test(v)
            ) || 'Event';

            const typeRaw = lines.find(v => /^[A-Z0-9_]{3,}$/.test(v)) || title;
            const type = nice(typeRaw);
            const color = colorFor(typeRaw + ' ' + title);

            card.dataset.sfRewritten = '1';
            card.dataset.sfDate = date;
            card.dataset.sfTitle = title;
            card.dataset.sfType = type;
            card.dataset.sfColor = color;

            card.classList.add('sf-portal-force-card');
            card.style.setProperty('--event-color', color);

            card.innerHTML = `
                <div class="sf-next-main">
                    <div class="sf-next-title">${title}</div>
                    <div class="sf-next-type">${type}</div>
                </div>
                <div class="sf-next-date">${date}</div>
            `;

            if (!map[date]) map[date] = [];
            map[date].push({ date, title, type, color });
        });

        return map;
    }

    function cleanCalendarCell(cell) {
        cell.querySelectorAll('.sf-portal-force-dots, .portal-calendar-event-dot, .sf-force-portal-dots').forEach(el => el.remove());
    }

    function applyCalendarDots(eventMap) {
        const { month, year } = currentMonthYear();
        const cells = Array.from(document.querySelectorAll('.portal-calendar-cell'));

        cells.forEach(cell => {
            cleanCalendarCell(cell);

            const text = cell.textContent || '';
            const dayMatch = text.match(/\b\d{1,2}\b/);

            if (!dayMatch) return;

            const day = parseInt(dayMatch[0], 10);

            if (!day || cell.classList.contains('portal-calendar-cell--muted')) {
                cell.onclick = null;
                return;
            }

            const key = dateKey(year, month, day);
            const events = eventMap[key] || [];

            if (!events.length) {
                cell.onclick = null;
                return;
            }

            const wrap = document.createElement('span');
            wrap.className = 'sf-portal-force-dots';

            events.slice(0, 4).forEach(event => {
                const dot = document.createElement('span');
                dot.className = 'sf-portal-force-dot';
                dot.style.setProperty('--event-color', event.color || colors.default);
                wrap.appendChild(dot);
            });

            cell.appendChild(wrap);

            cell.onclick = function () {
                openDayModal(key, events);
            };
        });
    }

    function openDayModal(date, events) {
        const backdrop = document.getElementById('sfMd3DayModalBackdrop');
        const title = document.getElementById('sfMd3DayModalTitle');
        const list = document.getElementById('sfMd3DayModalList');

        if (!backdrop || !title || !list) return;

        title.textContent = date;
        list.innerHTML = '';

        if (!events.length) {
            list.innerHTML = '<div class="sf-md3-no-events">No events on this date.</div>';
        } else {
            events.forEach(event => {
                const item = document.createElement('div');
                item.className = 'sf-md3-day-item';
                item.style.setProperty('--event-color', event.color || colors.default);
                item.innerHTML = `
                    <div class="sf-md3-day-dot"></div>
                    <div>
                        <div class="sf-md3-day-item-title">${event.title || 'Event'}</div>
                        <div class="sf-md3-day-item-meta">${event.type || ''}</div>
                    </div>
                `;
                list.appendChild(item);
            });
        }

        backdrop.style.display = 'grid';
        document.body.style.overflow = 'hidden';
    }

    function closeDayModal() {
        const backdrop = document.getElementById('sfMd3DayModalBackdrop');
        if (backdrop) backdrop.style.display = 'none';
        document.body.style.overflow = '';
    }

    function cleanUnderscoreTexts() {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);

        nodes.forEach(node => {
            const text = node.nodeValue;
            if (!text || !text.includes('_')) return;

            if (/^[\sA-Za-z0-9_/-]+$/.test(text) && /[a-zA-Z]+_[a-zA-Z]+/.test(text)) {
                node.nodeValue = text.replace(/[A-Za-z]+(?:_[A-Za-z0-9]+)+/g, function (m) {
                    return nice(m);
                });
            }
        });
    }

    function run() {
        cleanUnderscoreTexts();
        const map = collectEventsAndRewriteCards();
        applyCalendarDots(map);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('sfMd3DayModalClose')?.addEventListener('click', closeDayModal);
        document.getElementById('sfMd3DayModalBackdrop')?.addEventListener('click', function (e) {
            if (e.target === this) closeDayModal();
        });

        setTimeout(run, 200);
        setTimeout(run, 800);
        setTimeout(run, 1600);
        setTimeout(run, 2600);
    });

    window.addEventListener('load', function () {
        setTimeout(run, 300);
        setTimeout(run, 1200);
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('a,button');
        if (!btn) return;

        const text = btn.textContent || '';
        if (/prev|next/i.test(text)) {
            setTimeout(run, 500);
            setTimeout(run, 1200);
        }
    });
})();
</script>


<style>
    /* FINAL CALENDAR PREV/NEXT BUTTONS TUNE */
    .portal-calendar-large a,
    .portal-calendar-large button,
    .portal-calendar-large .portal-btn,
    .portal-calendar-large [href*="month"],
    .portal-calendar-large [href*="year"] {
        opacity: 1 !important;
        visibility: visible !important;
    }

    .portal-calendar-large a[href*="month"],
    .portal-calendar-large a[href*="year"],
    .portal-calendar-large .portal-btn {
        min-height: 42px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        background: #eef6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(37,99,235,.16) !important;
        box-shadow: 0 10px 24px rgba(37,99,235,.10) !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: -.01em !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
    }

    .portal-calendar-large a[href*="month"]:hover,
    .portal-calendar-large a[href*="year"]:hover,
    .portal-calendar-large .portal-btn:hover {
        background: #dbeafe !important;
        color: #1e40af !important;
        transform: translateY(-1px) !important;
    }

    .portal-calendar-large .portal-badge,
    .portal-calendar-large [class*="badge"] {
        min-height: 42px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(37,99,235,.12) !important;
        font-weight: 950 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase !important;
    }
</style>


<style>
    /* HARD FIX — Portal calendar Prev / Next / Month visibility */
    .portal-calendar-large .portal-btn,
    .portal-calendar-large a,
    .portal-calendar-large button {
        opacity: 1 !important;
        visibility: visible !important;
        text-shadow: none !important;
    }

    .portal-calendar-large a[href*="month"],
    .portal-calendar-large a[href*="year"],
    .portal-calendar-large .portal-btn {
        background: #e8f0ff !important;
        color: #0b57d0 !important;
        border: 1px solid rgba(11,87,208,.18) !important;
        min-height: 42px !important;
        height: 42px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
        box-shadow: 0 10px 24px rgba(11,87,208,.10) !important;
        text-decoration: none !important;
    }

    .portal-calendar-large a[href*="month"]:hover,
    .portal-calendar-large a[href*="year"]:hover,
    .portal-calendar-large .portal-btn:hover {
        background: #dbe7ff !important;
        color: #0842a0 !important;
        transform: translateY(-1px) !important;
    }

    .portal-calendar-large .portal-badge,
    .portal-calendar-large [class*="badge"] {
        background: #eef6ff !important;
        color: #0b57d0 !important;
        border: 1px solid rgba(11,87,208,.12) !important;
        min-height: 42px !important;
        height: 42px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .08em !important;
        text-transform: uppercase !important;
        box-shadow: none !important;
    }
</style>

<script>
(function () {
    function hardFixCalendarButtons() {
        const calendar = document.querySelector('.portal-calendar-large');
        if (!calendar) return;

        const elements = Array.from(calendar.querySelectorAll('a,button,span,div'));

        elements.forEach(function (el) {
            const text = (el.textContent || '').trim();

            if (/prev|next|←|→/i.test(text)) {
                el.style.setProperty('background', '#e8f0ff', 'important');
                el.style.setProperty('color', '#0b57d0', 'important');
                el.style.setProperty('border', '1px solid rgba(11,87,208,.18)', 'important');
                el.style.setProperty('min-height', '42px', 'important');
                el.style.setProperty('height', '42px', 'important');
                el.style.setProperty('padding', '0 18px', 'important');
                el.style.setProperty('border-radius', '999px', 'important');
                el.style.setProperty('display', 'inline-flex', 'important');
                el.style.setProperty('align-items', 'center', 'important');
                el.style.setProperty('justify-content', 'center', 'important');
                el.style.setProperty('font-size', '13px', 'important');
                el.style.setProperty('font-weight', '950', 'important');
                el.style.setProperty('opacity', '1', 'important');
                el.style.setProperty('visibility', 'visible', 'important');
                el.style.setProperty('text-shadow', 'none', 'important');
            }

            if (/^(january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{4}$/i.test(text)) {
                el.style.setProperty('background', '#eef6ff', 'important');
                el.style.setProperty('color', '#0b57d0', 'important');
                el.style.setProperty('border', '1px solid rgba(11,87,208,.12)', 'important');
                el.style.setProperty('min-height', '42px', 'important');
                el.style.setProperty('height', '42px', 'important');
                el.style.setProperty('padding', '0 18px', 'important');
                el.style.setProperty('border-radius', '999px', 'important');
                el.style.setProperty('display', 'inline-flex', 'important');
                el.style.setProperty('align-items', 'center', 'important');
                el.style.setProperty('justify-content', 'center', 'important');
                el.style.setProperty('font-size', '12px', 'important');
                el.style.setProperty('font-weight', '950', 'important');
                el.style.setProperty('letter-spacing', '.08em', 'important');
                el.style.setProperty('text-transform', 'uppercase', 'important');
                el.style.setProperty('opacity', '1', 'important');
                el.style.setProperty('visibility', 'visible', 'important');
                el.style.setProperty('text-shadow', 'none', 'important');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(hardFixCalendarButtons, 100);
        setTimeout(hardFixCalendarButtons, 500);
        setTimeout(hardFixCalendarButtons, 1200);
    });

    window.addEventListener('load', function () {
        setTimeout(hardFixCalendarButtons, 300);
    });

    document.addEventListener('click', function () {
        setTimeout(hardFixCalendarButtons, 300);
    });
})();
</script>




