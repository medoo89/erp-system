<x-filament-panels::page>
    @php
        $resourceClass = null;

        foreach ([
            \App\Filament\Resources\JobApplicationFields\JobApplicationFieldResource::class,
            \App\Filament\Resources\JobApplicationFieldResource::class,
        ] as $class) {
            if (class_exists($class)) {
                $resourceClass = $class;
                break;
            }
        }

        try {
            $createUrl = $resourceClass ? $resourceClass::getUrl('create') : url('/admin/job-application-fields/create');
        } catch (\Throwable $e) {
            $createUrl = url('/admin/job-application-fields/create');
        }

        try {
            $modelClass = \App\Models\JobApplicationField::class;
            $totalRecords = class_exists($modelClass) ? $modelClass::query()->count() : null;

            $tableName = class_exists($modelClass) ? (new $modelClass)->getTable() : null;

            $activeRecords = $tableName && \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'is_active')
                ? $modelClass::query()->where('is_active', true)->count()
                : null;

            $globalRecords = $tableName && \Illuminate\Support\Facades\Schema::hasColumn($tableName, 'is_global')
                ? $modelClass::query()->where('is_global', true)->count()
                : null;
        } catch (\Throwable $e) {
            $totalRecords = null;
            $activeRecords = null;
            $globalRecords = null;
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .af-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .af-hero {
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

        .af-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .af-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .af-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .af-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .af-subtitle {
            margin-top: 16px;
            max-width: 860px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .af-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .af-btn {
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

        .af-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(242,183,5,.30);
        }

        .af-badge-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .af-badge {
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

        .af-table-shell {
            position: relative;
            overflow: visible !important;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .af-table-shell .fi-ta-outer,
        .af-table-shell .fi-ta,
        .af-table-shell .fi-ta-content,
        .af-table-shell .fi-ta-header,
        .af-table-shell .fi-ta-toolbar,
        .af-table-shell .fi-ta-table,
        .af-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .af-table-shell .fi-ta-ctn,
        .af-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .af-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
        }

        .af-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
        }

        .af-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .af-table-shell .fi-ta-group-header,
        .af-table-shell tr.fi-ta-group-header,
        .af-table-shell [data-group-header] {
            background: linear-gradient(90deg, rgba(76,167,168,.10), rgba(242,183,5,.08)) !important;
            color: #0f172a !important;
            font-weight: 950 !important;
        }

        .af-table-shell .fi-input-wrp,
        .af-table-shell .fi-select,
        .af-table-shell .fi-input,
        .af-table-shell .fi-select-input,
        .af-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .af-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .af-table-shell .fi-icon,
        .af-table-shell svg {
            flex-shrink: 0 !important;
        }

        .af-table-shell .fi-ta-actions,
        .af-table-shell .fi-ta-actions-cell {
            white-space: nowrap !important;
            text-align: right !important;
        }

        .af-table-shell .fi-ta-actions {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 8px !important;
        }

        .af-table-shell .fi-ta-actions a,
        .af-table-shell .fi-ta-actions button,
        .af-table-shell a.fi-link,
        .af-table-shell button.fi-link {
            min-height: 34px !important;
            height: 34px !important;
            padding: 0 14px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 12px !important;
            line-height: 1 !important;
            font-weight: 950 !important;
            text-decoration: none !important;
            transition: .18s ease !important;
        }

        .af-table-shell .fi-ta-actions a,
        .af-table-shell a.fi-link {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 1px solid rgba(179,139,47,.28) !important;
            box-shadow: 0 8px 18px rgba(242,183,5,.16) !important;
        }

        .af-table-shell .fi-ta-actions button,
        .af-table-shell button.fi-link {
            background: #fee2e2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
            box-shadow: 0 8px 18px rgba(239,68,68,.10) !important;
        }

        .af-table-shell .fi-ta-actions a:hover,
        .af-table-shell .fi-ta-actions button:hover,
        .af-table-shell a.fi-link:hover,
        .af-table-shell button.fi-link:hover {
            transform: translateY(-1px) !important;
        }

        /* Columns dropdown fix */
        .af-table-shell .fi-dropdown-panel {
            border-radius: 22px !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .22) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
        }

        .af-table-shell .fi-dropdown-panel .fi-dropdown-list,
        .af-table-shell .fi-dropdown-panel .fi-ta-column-toggle-dropdown,
        .af-table-shell .fi-dropdown-panel [role="menu"] {
            max-height: 420px !important;
            overflow-y: auto !important;
            padding-right: 6px !important;
        }

        .af-table-shell .fi-dropdown-panel input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #1f4664 !important;
        }

        .af-table-shell .fi-dropdown-panel label,
        .af-table-shell .fi-dropdown-panel span {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        .af-table-shell .fi-dropdown-panel .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 2 !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
        }

        .dark .af-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .af-table-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .af-table-shell table thead th {
            background: rgba(15,23,42,.92) !important;
            color: #8fd6d7 !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .af-table-shell table tbody td {
            background: rgba(12,23,38,.96) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.10) !important;
        }

        .dark .af-table-shell table tbody tr:hover td {
            background: rgba(15,23,42,.98) !important;
        }

        .dark .af-table-shell .fi-ta-group-header,
        .dark .af-table-shell tr.fi-ta-group-header,
        .dark .af-table-shell [data-group-header] {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
        }

        .dark .af-table-shell .fi-input-wrp,
        .dark .af-table-shell .fi-select,
        .dark .af-table-shell .fi-input,
        .dark .af-table-shell .fi-select-input,
        .dark .af-table-shell .fi-ta-search-field input {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .af-table-shell .fi-dropdown-panel {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
        }

        .dark .af-table-shell .fi-dropdown-panel label,
        .dark .af-table-shell .fi-dropdown-panel span {
            color: #f8fafc !important;
        }

        @media (max-width: 900px) {
            .af-wrap {
                gap: 18px;
            }

            .af-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="af-wrap">
        <section class="af-hero">
            <div class="af-hero-inner">
                <div>
                    <div class="af-breadcrumb">Admin Settings › Application Fields › List</div>
                    <div class="af-title">Application Fields</div>
                    <div class="af-subtitle">
                        Manage reusable public application fields, field types, groups, required flags, global visibility, and display order.
                    </div>

                    <div class="af-badge-row">
                        @if(! is_null($totalRecords))
                            <div class="af-badge">{{ $totalRecords }} Fields</div>
                        @endif

                        @if(! is_null($activeRecords))
                            <div class="af-badge">{{ $activeRecords }} Active</div>
                        @endif

                        @if(! is_null($globalRecords))
                            <div class="af-badge">{{ $globalRecords }} Global</div>
                        @endif
                    </div>
                </div>

                <div class="af-actions">
                    <a href="{{ $createUrl }}" class="af-btn">New Application Field</a>
                </div>
            </div>
        </section>

        <section class="af-table-shell">
            {{ $this->table }}
        </section>
    </div>
</x-filament-panels::page>
