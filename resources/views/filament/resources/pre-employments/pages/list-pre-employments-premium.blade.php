<x-filament-panels::page>
    @php
        $resourceClass = \App\Filament\Resources\PreEmployments\PreEmploymentResource::class;

        try {
            $createUrl = $resourceClass::getUrl('create');
        } catch (\Throwable $e) {
            $createUrl = url('/admin/pre-employments/create');
        }

        try {
            $totalRecords = \App\Models\PreEmployment::query()->count();
        } catch (\Throwable $e) {
            $totalRecords = null;
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .pe-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .pe-hero {
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

        .pe-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .pe-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .pe-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .pe-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .pe-subtitle {
            margin-top: 16px;
            max-width: 820px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .pe-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .pe-btn {
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

        .pe-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(242,183,5,.30);
        }

        .pe-badge {
            margin-top: 18px;
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

        .pe-table-shell {
            position: relative;
            overflow: visible !important;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .pe-table-shell .fi-ta-outer,
        .pe-table-shell .fi-ta,
        .pe-table-shell .fi-ta-content,
        .pe-table-shell .fi-ta-header,
        .pe-table-shell .fi-ta-toolbar,
        .pe-table-shell .fi-ta-table,
        .pe-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .pe-table-shell .fi-ta-ctn,
        .pe-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .pe-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
        }

        .pe-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
        }

        .pe-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .pe-table-shell .fi-input-wrp,
        .pe-table-shell .fi-select,
        .pe-table-shell .fi-input,
        .pe-table-shell .fi-select-input,
        .pe-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .pe-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .pe-table-shell .fi-btn {
            border-radius: 999px !important;
            font-weight: 900 !important;
        }

        /*
         * Columns dropdown fix:
         * - checkboxes visible
         * - panel scrolls
         * - apply button stays available
         */
        .pe-table-shell .fi-dropdown-panel {
            border-radius: 22px !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .22) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
        }

        .pe-table-shell .fi-dropdown-panel .fi-dropdown-list,
        .pe-table-shell .fi-dropdown-panel .fi-ta-column-toggle-dropdown,
        .pe-table-shell .fi-dropdown-panel [role="menu"] {
            max-height: 420px !important;
            overflow-y: auto !important;
            padding-right: 6px !important;
        }

        .pe-table-shell .fi-dropdown-panel input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #1f4664 !important;
        }

        .pe-table-shell .fi-dropdown-panel label,
        .pe-table-shell .fi-dropdown-panel span {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        .pe-table-shell .fi-dropdown-panel .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 2 !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
        }

        .dark .pe-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .pe-table-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .pe-table-shell table thead th {
            background: rgba(15,23,42,.92) !important;
            color: #8fd6d7 !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .pe-table-shell table tbody td {
            background: rgba(12,23,38,.96) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.10) !important;
        }

        .dark .pe-table-shell table tbody tr:hover td {
            background: rgba(15,23,42,.98) !important;
        }

        .dark .pe-table-shell .fi-input-wrp,
        .dark .pe-table-shell .fi-select,
        .dark .pe-table-shell .fi-input,
        .dark .pe-table-shell .fi-select-input,
        .dark .pe-table-shell .fi-ta-search-field input {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .pe-table-shell .fi-dropdown-panel {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
        }

        .dark .pe-table-shell .fi-dropdown-panel label,
        .dark .pe-table-shell .fi-dropdown-panel span {
            color: #f8fafc !important;
        }

        @media (max-width: 900px) {
            .pe-wrap {
                gap: 18px;
            }

            .pe-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="pe-wrap">
        <section class="pe-hero">
            <div class="pe-hero-inner">
                <div>
                    <div class="pe-breadcrumb">Recruitment › Pre-Employment › List</div>
                    <div class="pe-title">Pre-Employment</div>
                    <div class="pe-subtitle">
                        Track candidates before employment approval, monitor projects, clients, status, and operation officer assignments.
                    </div>

                    @if(! is_null($totalRecords))
                        <div class="pe-badge">{{ $totalRecords }} Records</div>
                    @endif
                </div>

                <div class="pe-actions">
                    <a href="{{ $createUrl }}" class="pe-btn">New Pre-Employment Record</a>
                </div>
            </div>
        </section>

        <section class="pe-table-shell">
            {{ $this->table }}
        </section>
    </div>
</x-filament-panels::page>
