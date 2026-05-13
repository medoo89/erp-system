<x-filament-panels::page>
    @php
        $resourceClass = null;

        foreach ([
            \App\Filament\Resources\JobApplicationTemplates\JobApplicationTemplateResource::class,
            \App\Filament\Resources\JobApplicationTemplateResource::class,
        ] as $class) {
            if (class_exists($class)) {
                $resourceClass = $class;
                break;
            }
        }

        try {
            $createUrl = $resourceClass ? $resourceClass::getUrl('create') : url('/admin/job-application-templates/create');
        } catch (\Throwable $e) {
            $createUrl = url('/admin/job-application-templates/create');
        }

        try {
            $modelClass = \App\Models\JobApplicationTemplate::class;
            $totalRecords = class_exists($modelClass) ? $modelClass::query()->count() : null;
            $activeRecords = class_exists($modelClass) && \Illuminate\Support\Facades\Schema::hasColumn((new $modelClass)->getTable(), 'is_active')
                ? $modelClass::query()->where('is_active', true)->count()
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

        .tpl-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .tpl-hero {
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

        .tpl-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .tpl-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .tpl-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .tpl-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .tpl-subtitle {
            margin-top: 16px;
            max-width: 840px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .tpl-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .tpl-btn {
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

        .tpl-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(242,183,5,.30);
        }

        .tpl-badge-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tpl-badge {
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

        .tpl-table-shell {
            position: relative;
            overflow: visible !important;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .tpl-table-shell .fi-ta-outer,
        .tpl-table-shell .fi-ta,
        .tpl-table-shell .fi-ta-content,
        .tpl-table-shell .fi-ta-header,
        .tpl-table-shell .fi-ta-toolbar,
        .tpl-table-shell .fi-ta-table,
        .tpl-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .tpl-table-shell .fi-ta-ctn,
        .tpl-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .tpl-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
        }

        .tpl-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
        }

        .tpl-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .tpl-table-shell .fi-input-wrp,
        .tpl-table-shell .fi-select,
        .tpl-table-shell .fi-input,
        .tpl-table-shell .fi-select-input,
        .tpl-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .tpl-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .tpl-table-shell .fi-ta-actions,
        .tpl-table-shell .fi-ta-actions-cell {
            white-space: nowrap !important;
            text-align: right !important;
        }

        .tpl-table-shell .fi-ta-actions {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 8px !important;
        }

        .tpl-table-shell .fi-ta-actions a,
        .tpl-table-shell .fi-ta-actions button,
        .tpl-table-shell a.fi-link,
        .tpl-table-shell button.fi-link {
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

        .tpl-table-shell .fi-ta-actions a,
        .tpl-table-shell a.fi-link {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 1px solid rgba(179,139,47,.28) !important;
            box-shadow: 0 8px 18px rgba(242,183,5,.16) !important;
        }

        .tpl-table-shell .fi-ta-actions button,
        .tpl-table-shell button.fi-link {
            background: #fee2e2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca !important;
            box-shadow: 0 8px 18px rgba(239,68,68,.10) !important;
        }

        .tpl-table-shell .fi-ta-actions a:hover,
        .tpl-table-shell .fi-ta-actions button:hover,
        .tpl-table-shell a.fi-link:hover,
        .tpl-table-shell button.fi-link:hover {
            transform: translateY(-1px) !important;
        }

        /* Columns dropdown fix */
        .tpl-table-shell .fi-dropdown-panel {
            border-radius: 22px !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .22) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
        }

        .tpl-table-shell .fi-dropdown-panel .fi-dropdown-list,
        .tpl-table-shell .fi-dropdown-panel .fi-ta-column-toggle-dropdown,
        .tpl-table-shell .fi-dropdown-panel [role="menu"] {
            max-height: 420px !important;
            overflow-y: auto !important;
            padding-right: 6px !important;
        }

        .tpl-table-shell .fi-dropdown-panel input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #1f4664 !important;
        }

        .tpl-table-shell .fi-dropdown-panel label,
        .tpl-table-shell .fi-dropdown-panel span {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        .tpl-table-shell .fi-dropdown-panel .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 2 !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
        }

        .dark .tpl-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .tpl-table-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .tpl-table-shell table thead th {
            background: rgba(15,23,42,.92) !important;
            color: #8fd6d7 !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .tpl-table-shell table tbody td {
            background: rgba(12,23,38,.96) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.10) !important;
        }

        .dark .tpl-table-shell table tbody tr:hover td {
            background: rgba(15,23,42,.98) !important;
        }

        .dark .tpl-table-shell .fi-input-wrp,
        .dark .tpl-table-shell .fi-select,
        .dark .tpl-table-shell .fi-input,
        .dark .tpl-table-shell .fi-select-input,
        .dark .tpl-table-shell .fi-ta-search-field input {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .tpl-table-shell .fi-dropdown-panel {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
        }

        .dark .tpl-table-shell .fi-dropdown-panel label,
        .dark .tpl-table-shell .fi-dropdown-panel span {
            color: #f8fafc !important;
        }

        @media (max-width: 900px) {
            .tpl-wrap {
                gap: 18px;
            }

            .tpl-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="tpl-wrap">
        <section class="tpl-hero">
            <div class="tpl-hero-inner">
                <div>
                    <div class="tpl-breadcrumb">Admin Settings › Templates › List</div>
                    <div class="tpl-title">Templates</div>
                    <div class="tpl-subtitle">
                        Manage application form templates used for job openings, public forms, and recruitment workflows.
                    </div>

                    <div class="tpl-badge-row">
                        @if(! is_null($totalRecords))
                            <div class="tpl-badge">{{ $totalRecords }} Templates</div>
                        @endif

                        @if(! is_null($activeRecords))
                            <div class="tpl-badge">{{ $activeRecords }} Active</div>
                        @endif
                    </div>
                </div>

                <div class="tpl-actions">
                    <a href="{{ $createUrl }}" class="tpl-btn">New Template</a>
                </div>
            </div>
        </section>

        <section class="tpl-table-shell">
            {{ $this->table }}
        </section>
    </div>
</x-filament-panels::page>
