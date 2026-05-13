<x-filament-panels::page>
    <script>
        document.body.classList.add('sf-archive-employments-page');
    </script>

    @php
        try {
            $totalArchivedEmployments = \App\Models\Employment::query()
                ->where(function ($query) {
                    $query
                        ->whereIn('status', ['inactive', 'resigned', 'terminated', 'archived'])
                        ->orWhereIn('contract_status', ['expired', 'terminated', 'closed']);
                })
                ->count();
        } catch (\Throwable $e) {
            $totalArchivedEmployments = null;
        }

        try {
            $terminatedEmployments = \App\Models\Employment::query()
                ->where(function ($query) {
                    $query
                        ->where('status', 'terminated')
                        ->orWhere('contract_status', 'terminated');
                })
                ->count();
        } catch (\Throwable $e) {
            $terminatedEmployments = null;
        }
    @endphp

    <style>
        .sf-archive-employments-page .fi-header {
            display: none !important;
        }

        .sf-archive-employments-page .sf-archive-emp-wrap {
            width: min(1240px, calc(100vw - 120px)) !important;
            max-width: 1240px !important;
            min-width: min(1240px, calc(100vw - 120px)) !important;
            margin: 0 auto !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 24px !important;
        }

        .sf-archive-employments-page .sf-archive-emp-hero {
            width: 100% !important;
            position: relative !important;
            overflow: hidden !important;
            border-radius: 30px !important;
            padding: 34px 36px !important;
            border: 1px solid rgba(76, 167, 168, .24) !important;
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .26), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .16), transparent 30%),
                linear-gradient(135deg, #081a34 0%, #12385d 56%, #2f6f73 100%) !important;
            box-shadow: 0 18px 36px rgba(15, 23, 42, .14) !important;
            color: #ffffff !important;
        }

        .sf-archive-employments-page .sf-archive-emp-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-archive-employments-page .sf-archive-emp-hero-inner {
            position: relative;
            z-index: 1;
        }

        .sf-archive-employments-page .sf-archive-emp-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .sf-archive-employments-page .sf-archive-emp-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #ffffff !important;
        }

        .sf-archive-employments-page .sf-archive-emp-subtitle {
            margin-top: 16px;
            max-width: 900px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .sf-archive-employments-page .sf-archive-emp-badge-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-archive-employments-page .sf-archive-emp-badge {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.14);
            color: #ffffff;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell {
            width: 100% !important;
            min-width: 100% !important;
            position: relative !important;
            overflow: visible !important;
            border-radius: 26px !important;
            border: 1px solid #d7e2e5 !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07) !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-outer,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-content,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-header,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-toolbar,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-table,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-outer,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-ctn,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-content {
            overflow-x: auto !important;
            overflow-y: visible !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell table {
            width: 100% !important;
            min-width: 1040px !important;
            table-layout: auto !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell table thead th {
            height: 48px !important;
            vertical-align: middle !important;
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
            white-space: nowrap !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell table tbody td {
            height: 58px !important;
            vertical-align: middle !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
            white-space: nowrap !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-empty-state {
            min-height: 260px !important;
            padding: 72px 20px !important;
            background: #ffffff !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-empty-state-heading {
            color: #0f172a !important;
            font-weight: 950 !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-input-wrp,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-select,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-input,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-select-input,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-toolbar,
        .sf-archive-employments-page .sf-archive-emp-table-shell .fi-ta-header-toolbar {
            position: relative !important;
            min-height: 88px !important;
            overflow: visible !important;
        }

        .sf-archive-employments-page:not(.sf-archive-emp-has-selection) .fi-ta-bulk-actions,
        .sf-archive-employments-page:not(.sf-archive-emp-has-selection) .fi-ta-bulk-actions-toolbar {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions,
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions-toolbar {
            position: absolute !important;
            left: 24px !important;
            top: 22px !important;
            z-index: 80 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 12px !important;
            width: auto !important;
            height: auto !important;
            min-width: 0 !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            overflow: visible !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }

        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions .fi-btn,
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions-toolbar .fi-btn {
            width: auto !important;
            height: 44px !important;
            min-width: 0 !important;
            min-height: 44px !important;
            max-width: none !important;
            max-height: 44px !important;
            padding: 0 18px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 9px !important;
            font-size: 13px !important;
            line-height: 1 !important;
            font-weight: 950 !important;
            letter-spacing: -.01em !important;
            white-space: nowrap !important;
            overflow: visible !important;
            transform: none !important;
        }

        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions .fi-btn:first-child,
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions-toolbar .fi-btn:first-child {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            color: #ffffff !important;
            border: 1px solid rgba(22, 163, 74, .30) !important;
            box-shadow: 0 14px 28px rgba(34, 197, 94, .22) !important;
        }

        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions .fi-btn:nth-child(2),
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions-toolbar .fi-btn:nth-child(2) {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: #ffffff !important;
            border: 1px solid rgba(220, 38, 38, .32) !important;
            box-shadow: 0 14px 28px rgba(239, 68, 68, .22) !important;
        }

        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions .fi-btn-label,
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions-toolbar .fi-btn-label {
            display: inline-flex !important;
            align-items: center !important;
            color: inherit !important;
            font-size: 13px !important;
            font-weight: 950 !important;
            line-height: 1 !important;
            white-space: nowrap !important;
        }

        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions .fi-btn svg,
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions-toolbar .fi-btn svg,
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions .fi-btn .fi-btn-icon,
        .sf-archive-employments-page.sf-archive-emp-has-selection .fi-ta-bulk-actions-toolbar .fi-btn .fi-btn-icon {
            width: 17px !important;
            height: 17px !important;
            min-width: 17px !important;
            min-height: 17px !important;
            color: #ffffff !important;
            stroke: currentColor !important;
            stroke-width: 2.35 !important;
            margin: 0 !important;
        }

        .sf-archive-employments-page .fi-ta-bulk-actions .animate-spin,
        .sf-archive-employments-page .fi-ta-bulk-actions-toolbar .animate-spin,
        .sf-archive-employments-page .fi-ta-bulk-actions [class*="spinner"],
        .sf-archive-employments-page .fi-ta-bulk-actions-toolbar [class*="spinner"],
        .sf-archive-employments-page .fi-ta-bulk-actions [class*="loading"],
        .sf-archive-employments-page .fi-ta-bulk-actions-toolbar [class*="loading"] {
            display: none !important;
        }

        .sf-archive-employments-page .fi-ta-selection-indicator button,
        .sf-archive-employments-page .fi-ta-selection-indicator a {
            display: none !important;
        }

        .sf-archive-employments-page .fi-ta-table thead tr th:last-child,
        .sf-archive-employments-page .fi-ta-table tbody tr td:last-child {
            width: 140px !important;
            min-width: 140px !important;
            max-width: 140px !important;
            text-align: center !important;
            vertical-align: middle !important;
        }

        .sf-archive-employments-page .fi-ta-table thead tr th:last-child {
            position: relative !important;
            color: transparent !important;
            font-size: 0 !important;
            background: #eef5f8 !important;
            height: 48px !important;
            min-height: 48px !important;
            padding: 0 16px !important;
            line-height: 48px !important;
        }

        .sf-archive-employments-page .fi-ta-table thead tr th:last-child::before {
            content: "ACTIONS" !important;
            height: 48px !important;
            min-height: 48px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            color: #1f4664 !important;
            font-size: 13px !important;
            font-weight: 950 !important;
            letter-spacing: .16em !important;
            text-transform: uppercase !important;
            line-height: 1 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .sf-archive-employments-page .fi-ta-actions,
        .sf-archive-employments-page .fi-ta-record-actions,
        .sf-archive-employments-page td:last-child .fi-ta-actions,
        .sf-archive-employments-page td:last-child .fi-ta-record-actions {
            display: inline-flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 10px !important;
            flex-wrap: nowrap !important;
            width: 100% !important;
        }

        .sf-archive-employments-page .sf-archive-row-action,
        .sf-archive-employments-page .sf-archive-row-action.fi-btn,
        .sf-archive-employments-page .fi-ta-actions .sf-archive-row-action,
        .sf-archive-employments-page .fi-ta-record-actions .sf-archive-row-action {
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            min-height: 44px !important;
            max-width: 44px !important;
            max-height: 44px !important;
            padding: 0 !important;
            border-radius: 16px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
        }

        .sf-archive-employments-page .sf-archive-row-action-restore {
            background: #f2b705 !important;
            color: #0f172a !important;
            border: 1px solid rgba(179, 139, 47, .30) !important;
            box-shadow: 0 12px 22px rgba(242, 183, 5, .18) !important;
        }

        .sf-archive-employments-page .sf-archive-row-action-delete,
        .sf-archive-employments-page .fi-ta-actions .fi-btn-color-danger,
        .sf-archive-employments-page .fi-ta-record-actions .fi-btn-color-danger {
            background: #f8fafc !important;
            color: #64748b !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 18px rgba(15, 23, 42, .045) !important;
        }

        .sf-archive-employments-page .sf-archive-row-action svg,
        .sf-archive-employments-page .sf-archive-row-action.fi-btn svg,
        .sf-archive-employments-page .fi-ta-actions .fi-btn svg,
        .sf-archive-employments-page .fi-ta-record-actions .fi-btn svg {
            width: 23px !important;
            height: 23px !important;
            min-width: 23px !important;
            min-height: 23px !important;
            stroke-width: 2.35 !important;
        }

        .sf-archive-employments-page .sf-archive-row-action .fi-btn-label,
        .sf-archive-employments-page .fi-ta-actions .fi-btn-label,
        .sf-archive-employments-page .fi-ta-record-actions .fi-btn-label {
            display: none !important;
        }

        .dark .sf-archive-employments-page .sf-archive-emp-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .sf-archive-employments-page .sf-archive-emp-table-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .sf-archive-employments-page .sf-archive-emp-table-shell table thead th {
            background: rgba(31,70,100,.55) !important;
            color: #dff7fb !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .sf-archive-employments-page .sf-archive-emp-table-shell table tbody td {
            background: rgba(15,23,42,.72) !important;
            color: #e5eef5 !important;
            border-color: rgba(148,163,184,.12) !important;
        }

        .dark .sf-archive-employments-page .fi-ta-table thead tr th:last-child {
            background: rgba(15, 23, 42, .88) !important;
        }

        .dark .sf-archive-employments-page .fi-ta-table thead tr th:last-child::before {
            color: #e0f2fe !important;
        }

        .dark .sf-archive-employments-page .sf-archive-row-action-delete,
        .dark .sf-archive-employments-page .fi-ta-actions .fi-btn-color-danger,
        .dark .sf-archive-employments-page .fi-ta-record-actions .fi-btn-color-danger {
            background: rgba(255, 255, 255, .075) !important;
            border-color: rgba(255, 255, 255, .12) !important;
            color: #cbd5e1 !important;
        }

        @media (max-width: 900px) {
            .sf-archive-employments-page .sf-archive-emp-wrap {
                width: calc(100vw - 32px) !important;
                min-width: 0 !important;
            }
        }

        @media (max-width: 720px) {
            .sf-archive-employments-page .sf-archive-emp-hero {
                padding: 28px 24px !important;
            }

            .sf-archive-employments-page .sf-archive-emp-title {
                font-size: 42px !important;
            }
        }
    </style>

    <div class="sf-archive-emp-wrap">
        <section class="sf-archive-emp-hero">
            <div class="sf-archive-emp-hero-inner">
                <div class="sf-archive-emp-breadcrumb">Archive › Employment</div>
                <div class="sf-archive-emp-title">Archived Employment</div>
                <div class="sf-archive-emp-subtitle">
                    Review archived employment records, project/client history, contract status, contract end dates, and demobilization data.
                </div>

                <div class="sf-archive-emp-badge-row">
                    @if(! is_null($totalArchivedEmployments))
                        <span class="sf-archive-emp-badge">{{ $totalArchivedEmployments }} Archived Employment</span>
                    @endif

                    @if(! is_null($terminatedEmployments))
                        <span class="sf-archive-emp-badge">{{ $terminatedEmployments }} Terminated / Closed</span>
                    @endif
                </div>
            </div>
        </section>

        <section class="sf-archive-emp-table-shell">
            {{ $this->table }}
        </section>
    </div>

    <script>
        (() => {
            const applyArchiveEmploymentCleanUI = () => {
                const checked = document.querySelectorAll('.sf-archive-employments-page table tbody input[type="checkbox"]:checked').length > 0;

                document.body.classList.toggle('sf-archive-emp-has-selection', checked);

                document
                    .querySelectorAll('.sf-archive-employments-page .fi-ta-bulk-actions .fi-btn, .sf-archive-employments-page .fi-ta-bulk-actions-toolbar .fi-btn')
                    .forEach((button, index) => {
                        button.querySelectorAll('.animate-spin, [class*="spinner"], [class*="loading"]').forEach((el) => el.remove());

                        let label = button.querySelector('.fi-btn-label');

                        if (!label) {
                            label = document.createElement('span');
                            label.className = 'fi-btn-label';
                            button.appendChild(label);
                        }

                        if (index === 0) {
                            label.textContent = 'Restore Selected';
                            button.setAttribute('title', 'Restore Selected');
                            button.setAttribute('aria-label', 'Restore Selected');
                        }

                        if (index === 1) {
                            label.textContent = 'Permanent Delete';
                            button.setAttribute('title', 'Permanent Delete');
                            button.setAttribute('aria-label', 'Permanent Delete');
                        }
                    });
            };

            document.addEventListener('DOMContentLoaded', applyArchiveEmploymentCleanUI);

            document.addEventListener('change', (event) => {
                if (event.target && event.target.matches('.sf-archive-employments-page input[type="checkbox"]')) {
                    setTimeout(applyArchiveEmploymentCleanUI, 40);
                }
            });

            document.addEventListener('click', () => {
                setTimeout(applyArchiveEmploymentCleanUI, 80);
            }, true);

            new MutationObserver(applyArchiveEmploymentCleanUI).observe(document.body, {
                childList: true,
                subtree: true,
            });

            setInterval(applyArchiveEmploymentCleanUI, 800);
        })();
    </script>
</x-filament-panels::page>
