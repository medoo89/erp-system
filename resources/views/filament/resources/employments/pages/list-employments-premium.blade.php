<x-filament-panels::page>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0" />
    @php
        $resourceClass = \App\Filament\Resources\Employments\EmploymentResource::class;

        try {
            $createUrl = $resourceClass::getUrl('create');
        } catch (\Throwable $e) {
            $createUrl = url('/admin/employments/create');
        }

        try {
            $totalRecords = \App\Models\Employment::query()->count();
            $activeRecords = \Illuminate\Support\Facades\Schema::hasColumn('employments', 'status')
                ? \App\Models\Employment::query()->where('status', 'active')->count()
                : null;
        } catch (\Throwable $e) {
            $totalRecords = null;
            $activeRecords = null;
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .emp-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .emp-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px 36px;
            border: 1px solid rgba(76, 167, 168, .24);
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .26), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .16), transparent 30%),
                linear-gradient(135deg, #081a34 0%, #12385d 56%, #2f6f73 100%) !important;
            box-shadow: 0 18px 36px rgba(15, 23, 42, .14);
            color: #fff;
        }

        .emp-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .emp-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .emp-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .emp-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .emp-subtitle {
            margin-top: 16px;
            max-width: 840px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .emp-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .emp-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            background: #f2b705;
            color: #3b2a00 !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 950;
            box-shadow: 0 12px 24px rgba(242,183,5,.22);
            transition: .18s ease;
        }

        .emp-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(242,183,5,.30);
        }

        .emp-badge-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .emp-badge {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.14);
            color: #fff;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .emp-table-shell {
            position: relative;
            overflow: visible !important;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .emp-table-shell .fi-ta-outer,
        .emp-table-shell .fi-ta,
        .emp-table-shell .fi-ta-content,
        .emp-table-shell .fi-ta-header,
        .emp-table-shell .fi-ta-toolbar,
        .emp-table-shell .fi-ta-table,
        .emp-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .emp-table-shell .fi-ta-ctn,
        .emp-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .emp-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
        }

        .emp-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
        }

        .emp-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .emp-table-shell .fi-input-wrp,
        .emp-table-shell .fi-select,
        .emp-table-shell .fi-input,
        .emp-table-shell .fi-select-input,
        .emp-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .emp-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .emp-table-shell .fi-btn {
            border-radius: 999px !important;
            font-weight: 900 !important;
        }

        /* Columns dropdown fix */
        .emp-table-shell .fi-dropdown-panel {
            border-radius: 22px !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .22) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
        }

        .emp-table-shell .fi-dropdown-panel .fi-dropdown-list,
        .emp-table-shell .fi-dropdown-panel .fi-ta-column-toggle-dropdown,
        .emp-table-shell .fi-dropdown-panel [role="menu"] {
            max-height: 420px !important;
            overflow-y: auto !important;
            padding-right: 6px !important;
        }

        .emp-table-shell .fi-dropdown-panel input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #1f4664 !important;
        }

        .emp-table-shell .fi-dropdown-panel label,
        .emp-table-shell .fi-dropdown-panel span {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        .emp-table-shell .fi-dropdown-panel .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 2 !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
        }

        .dark .emp-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .emp-table-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .emp-table-shell table thead th {
            background: rgba(15,23,42,.92) !important;
            color: #8fd6d7 !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .emp-table-shell table tbody td {
            background: rgba(12,23,38,.96) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.10) !important;
        }

        .dark .emp-table-shell table tbody tr:hover td {
            background: rgba(15,23,42,.98) !important;
        }

        .dark .emp-table-shell .fi-input-wrp,
        .dark .emp-table-shell .fi-select,
        .dark .emp-table-shell .fi-input,
        .dark .emp-table-shell .fi-select-input,
        .dark .emp-table-shell .fi-ta-search-field input {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .emp-table-shell .fi-dropdown-panel {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
        }

        .dark .emp-table-shell .fi-dropdown-panel label,
        .dark .emp-table-shell .fi-dropdown-panel span {
            color: #f8fafc !important;
        }

        /* SADA EMPLOYMENT VIEW BUTTON FIX - CLEAN */
        .emp-table-shell .fi-ta-actions-cell,
        .emp-table-shell .fi-ta-actions {
            width: 1% !important;
            white-space: nowrap !important;
            text-align: right !important;
        }

        .emp-table-shell .fi-ta-actions {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 8px !important;
        }

        .emp-table-shell .fi-ta-actions a,
        .emp-table-shell .fi-ta-actions button,
        .emp-table-shell a.fi-link,
        .emp-table-shell button.fi-link {
            min-width: 72px !important;
            width: auto !important;
            max-width: 96px !important;
            min-height: 34px !important;
            height: 34px !important;
            padding: 0 14px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 1px solid rgba(179,139,47,.28) !important;
            font-size: 12px !important;
            line-height: 1 !important;
            font-weight: 950 !important;
            text-decoration: none !important;
            box-shadow: 0 8px 18px rgba(242,183,5,.16) !important;
            transition: .18s ease !important;
        }

        .emp-table-shell .fi-ta-actions a:hover,
        .emp-table-shell .fi-ta-actions button:hover,
        .emp-table-shell a.fi-link:hover,
        .emp-table-shell button.fi-link:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 12px 22px rgba(242,183,5,.24) !important;
        }

        .dark .emp-table-shell .fi-ta-actions a,
        .dark .emp-table-shell .fi-ta-actions button,
        .dark .emp-table-shell a.fi-link,
        .dark .emp-table-shell button.fi-link {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border-color: rgba(242,183,5,.36) !important;
        }


        @media (max-width: 900px) {
            .emp-wrap {
                gap: 18px;
            }

            .emp-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="emp-wrap">
        <section class="emp-hero">
            <div class="emp-hero-inner">
                <div>
                    <div class="emp-breadcrumb">HR › Employment › List</div>
                    <div class="emp-title">Employment</div>
                    <div class="emp-subtitle">
                        Manage active employees, project assignments, client linkage, positions, status, and operation officer details.
                    </div>

                    <div class="emp-badge-row">
                        @if(! is_null($totalRecords))
                            <div class="emp-badge">{{ $totalRecords }} Employees</div>
                        @endif

                        @if(! is_null($activeRecords))
                            <div class="emp-badge">{{ $activeRecords }} Active</div>
                        @endif
                    </div>
                </div>

                <div class="emp-actions">
                    <a href="{{ $createUrl }}" class="emp-btn">New Employment</a>
                </div>
            </div>
        </section>

        <section class="emp-table-shell">
@php
    $employeeScope = request()->query('employee_scope', 'all');

    $employmentCounts = [
        'all' => \App\Models\Employment::query()->count(),
        'office' => \App\Models\Employment::query()->where('employee_category', 'office')->count(),
        'operational' => \App\Models\Employment::query()
            ->where(function ($q) {
                $q->where('employee_category', 'operational')->orWhereNull('employee_category');
            })
            ->count(),
        'active' => \App\Models\Employment::query()->where('status', 'active')->count(),
        'on_rotation' => \App\Models\Employment::query()->where('current_work_status', 'on_rotation')->count(),
        'upcoming_mobilization' => \App\Models\Employment::query()
            ->whereNotNull('mobilization_date')
            ->whereDate('mobilization_date', '>=', now()->toDateString())
            ->whereDate('mobilization_date', '<=', now()->addDays(30)->toDateString())
            ->count(),
    ];

    $scopeCards = [
        'all' => ['label' => 'All Employees', 'icon' => 'groups', 'hint' => 'Full employment list'],
        'office' => ['label' => 'Office Employees', 'icon' => 'business_center', 'hint' => 'Internal Sada Fezzan staff'],
        'operational' => ['label' => 'Operational Employees', 'icon' => 'engineering', 'hint' => 'Client/project workforce'],
        'active' => ['label' => 'Active', 'icon' => 'verified', 'hint' => 'Currently active employees'],
        'on_rotation' => ['label' => 'On Rotation', 'icon' => 'sync_alt', 'hint' => 'Inside work period now'],
        'upcoming_mobilization' => ['label' => 'Upcoming Mobilization', 'icon' => 'flight_takeoff', 'hint' => 'Next 30 days'],
    ];

    $scopeUrl = function (string $scope): string {
        return $scope === 'all'
            ? url('/admin/employments')
            : url('/admin/employments') . '?employee_scope=' . $scope;
    };
@endphp

<style>
    .sf-employment-scope-bar {
        width: min(100%, 1280px);
        margin: 0 auto 22px;
        display: grid;
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px;
    }

    .sf-employment-scope-card {
        min-height: 96px;
        border-radius: 24px;
        padding: 15px;
        text-decoration: none !important;
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.11), transparent 36%),
            rgba(255,255,255,.94);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 14px 34px rgba(15,23,42,.06);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .sf-employment-scope-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 46px rgba(15,23,42,.10);
        border-color: rgba(20,184,166,.24);
    }

    .sf-employment-scope-card.is-active {
        border-color: rgba(37,99,235,.38);
        box-shadow: 0 22px 52px rgba(37,99,235,.13);
        background:
            radial-gradient(circle at top right, rgba(37,99,235,.17), transparent 36%),
            rgba(255,255,255,.98);
    }

    .dark .sf-employment-scope-card {
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.12), transparent 36%),
            rgba(15,23,42,.72);
        border-color: rgba(148,163,184,.18);
        box-shadow: 0 18px 42px rgba(0,0,0,.18);
    }

    .sf-employment-scope-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .sf-employment-scope-icon {
        width: 38px;
        height: 38px;
        border-radius: 16px;
        background: #e0f2fe;
        color: #234b74;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .sf-employment-scope-count {
        color: #0f172a;
        font-size: 28px;
        line-height: 1;
        font-weight: 950;
        letter-spacing: -.05em;
    }

    .dark .sf-employment-scope-count {
        color: #fff;
    }

    .sf-employment-scope-label {
        margin-top: 12px;
        color: #234b74;
        font-size: 13px;
        font-weight: 950;
        line-height: 1.2;
    }

    .dark .sf-employment-scope-label {
        color: #fff;
    }

    .sf-employment-scope-hint {
        margin-top: 5px;
        color: #64748b;
        font-size: 11px;
        font-weight: 750;
        line-height: 1.35;
    }

    .dark .sf-employment-scope-hint {
        color: #94a3b8;
    }

    @media (max-width: 1280px) {
        .sf-employment-scope-bar {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .sf-employment-scope-bar {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="sf-employment-scope-bar">
    @foreach($scopeCards as $scopeKey => $scopeCard)
        <a
            href="{{ $scopeUrl($scopeKey) }}"
            class="sf-employment-scope-card {{ $employeeScope === $scopeKey ? 'is-active' : '' }}"
        >
            <div class="sf-employment-scope-top">
                <span class="sf-employment-scope-icon material-symbols-rounded">{{ $scopeCard['icon'] }}</span>
                <span class="sf-employment-scope-count">{{ $employmentCounts[$scopeKey] ?? 0 }}</span>
            </div>

            <div>
                <div class="sf-employment-scope-label">{{ $scopeCard['label'] }}</div>
                <div class="sf-employment-scope-hint">{{ $scopeCard['hint'] }}</div>
            </div>
        </a>
    @endforeach
</div>


{{ $this->table }}
        </section>
    </div>

<style id="sf-employment-scope-cards-polish-final">
    .sf-employment-scope-bar {
        width: min(100%, 1280px) !important;
        margin: 0 auto 22px !important;
        display: grid !important;
        grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        gap: 14px !important;
        align-items: stretch !important;
    }

    .sf-employment-scope-card {
        min-height: 118px !important;
        border-radius: 26px !important;
        padding: 16px !important;
        overflow: hidden !important;
        position: relative !important;
        text-decoration: none !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 38%),
            linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.92)) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .075) !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease !important;
    }

    .sf-employment-scope-card::before {
        content: "" !important;
        position: absolute !important;
        inset: 0 0 auto 0 !important;
        height: 5px !important;
        background: linear-gradient(90deg, #22d3ee, #2563eb) !important;
        opacity: .9 !important;
    }

    .sf-employment-scope-card:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 22px 52px rgba(15, 23, 42, .12) !important;
        border-color: rgba(37, 99, 235, .20) !important;
    }

    .sf-employment-scope-card.is-active {
        background:
            radial-gradient(circle at top right, rgba(37, 99, 235, .16), transparent 38%),
            linear-gradient(180deg, #ffffff, #f8fbff) !important;
        border-color: rgba(37, 99, 235, .36) !important;
        box-shadow: 0 24px 60px rgba(37, 99, 235, .16) !important;
    }

    .sf-employment-scope-top {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        margin-top: 4px !important;
    }

    .sf-employment-scope-icon {
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        border-radius: 18px !important;
        background: linear-gradient(135deg, #e0f2fe, #eff6ff) !important;
        color: #234b74 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 22px !important;
        line-height: 1 !important;
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, .08) !important;
    }

    .sf-employment-scope-count {
        color: #0f172a !important;
        font-size: 34px !important;
        line-height: .9 !important;
        font-weight: 950 !important;
        letter-spacing: -.065em !important;
        text-align: right !important;
        font-variant-numeric: tabular-nums !important;
    }

    .sf-employment-scope-label {
        margin-top: 14px !important;
        color: #234b74 !important;
        font-size: 15px !important;
        font-weight: 950 !important;
        line-height: 1.15 !important;
        letter-spacing: -.025em !important;
    }

    .sf-employment-scope-hint {
        margin-top: 7px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 750 !important;
        line-height: 1.35 !important;
    }

    .dark .sf-employment-scope-card {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 38%),
            linear-gradient(180deg, rgba(15,23,42,.78), rgba(15,23,42,.62)) !important;
        border-color: rgba(148, 163, 184, .18) !important;
        box-shadow: 0 18px 46px rgba(0,0,0,.20) !important;
    }

    .dark .sf-employment-scope-card.is-active {
        border-color: rgba(96, 165, 250, .36) !important;
        box-shadow: 0 24px 60px rgba(37, 99, 235, .18) !important;
    }

    .dark .sf-employment-scope-count,
    .dark .sf-employment-scope-label {
        color: #ffffff !important;
    }

    .dark .sf-employment-scope-hint {
        color: #94a3b8 !important;
    }

    .dark .sf-employment-scope-icon {
        background: rgba(59, 130, 246, .16) !important;
        color: #bfdbfe !important;
        box-shadow: inset 0 0 0 1px rgba(147, 197, 253, .12) !important;
    }

    @media (max-width: 1450px) {
        .sf-employment-scope-bar {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 820px) {
        .sf-employment-scope-bar {
            grid-template-columns: 1fr !important;
        }
    }
</style>


<style id="sf-employment-material-symbols-fix">
    .sf-employment-scope-icon.material-symbols-rounded {
        font-family: 'Material Symbols Rounded' !important;
        font-weight: 500 !important;
        font-style: normal !important;
        font-size: 26px !important;
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

    .sf-employment-scope-icon {
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        border-radius: 18px !important;
        background: linear-gradient(135deg, #e0f2fe, #eff6ff) !important;
        color: #2563eb !important;
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, .10) !important;
    }

    .sf-employment-scope-card.is-active .sf-employment-scope-icon {
        background: linear-gradient(135deg, #dbeafe, #eef6ff) !important;
        color: #1d4ed8 !important;
    }

    .dark .sf-employment-scope-icon {
        background: rgba(59, 130, 246, .16) !important;
        color: #bfdbfe !important;
        box-shadow: inset 0 0 0 1px rgba(147, 197, 253, .12) !important;
    }
</style>

</x-filament-panels::page>
