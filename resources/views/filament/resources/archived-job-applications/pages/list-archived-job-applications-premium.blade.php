<x-filament-panels::page>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,500,0,0" />

    @php
        try {
            $totalArchivedApplications = \App\Models\JobApplication::query()
                ->when(\Illuminate\Support\Facades\Schema::hasColumn('job_applications', 'status'), function ($query) {
                    $query->whereIn('status', ['archived', 'declined', 'closed', 'rejected']);
                })
                ->count();
        } catch (\Throwable $e) {
            $totalArchivedApplications = null;
        }

        try {
            $declinedApplications = \App\Models\JobApplication::query()
                ->when(\Illuminate\Support\Facades\Schema::hasColumn('job_applications', 'status'), function ($query) {
                    $query->whereIn('status', ['declined', 'rejected']);
                })
                ->count();
        } catch (\Throwable $e) {
            $declinedApplications = null;
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .sf-archive-app-wrap {
            width: min(1240px, calc(100vw - 120px)) !important;
            max-width: 1240px !important;
            min-width: min(1240px, calc(100vw - 120px)) !important;
            margin: 0 auto !important;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-archive-app-hero {
            width: 100% !important;
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

        .sf-archive-app-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-archive-app-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .sf-archive-app-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .sf-archive-app-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .sf-archive-app-subtitle {
            margin-top: 16px;
            max-width: 900px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .sf-archive-app-badge-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-archive-app-badge {
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

        .sf-archive-app-table-shell {
            width: 100% !important;
            min-width: 100% !important;
            position: relative;
            overflow: visible !important;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .sf-archive-app-table-shell .fi-ta-outer,
        .sf-archive-app-table-shell .fi-ta,
        .sf-archive-app-table-shell .fi-ta-content,
        .sf-archive-app-table-shell .fi-ta-header,
        .sf-archive-app-table-shell .fi-ta-toolbar,
        .sf-archive-app-table-shell .fi-ta-table,
        .sf-archive-app-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .sf-archive-app-table-shell .fi-ta-ctn,
        .sf-archive-app-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .sf-archive-app-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
        }

        .sf-archive-app-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
        }

        .sf-archive-app-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .sf-archive-app-table-shell .fi-ta-empty-state {
            padding: 72px 20px !important;
            background: #ffffff !important;
        }

        .sf-archive-app-table-shell .fi-ta-empty-state-heading {
            color: #0f172a !important;
            font-weight: 950 !important;
        }

        .sf-archive-app-table-shell .fi-input-wrp,
        .sf-archive-app-table-shell .fi-select,
        .sf-archive-app-table-shell .fi-input,
        .sf-archive-app-table-shell .fi-select-input,
        .sf-archive-app-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .sf-archive-app-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .sf-archive-app-table-shell .fi-btn {
            border-radius: 999px !important;
            font-weight: 900 !important;
        }

        .sf-archive-app-table-shell .fi-dropdown-panel {
            border-radius: 22px !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .22) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
        }

        .sf-archive-app-table-shell .fi-dropdown-panel .fi-dropdown-list,
        .sf-archive-app-table-shell .fi-dropdown-panel .fi-ta-column-toggle-dropdown,
        .sf-archive-app-table-shell .fi-dropdown-panel [role="menu"] {
            max-height: 420px !important;
            overflow-y: auto !important;
            padding-right: 6px !important;
        }

        .sf-archive-app-table-shell .fi-dropdown-panel input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #1f4664 !important;
        }

        .sf-archive-app-table-shell .fi-dropdown-panel label,
        .sf-archive-app-table-shell .fi-dropdown-panel span {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        .sf-archive-app-table-shell .fi-dropdown-panel .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 2 !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
        }

        .sf-archive-app-table-shell .fi-ta,
        .sf-archive-app-table-shell .fi-ta-outer,
        .sf-archive-app-table-shell .fi-ta-ctn,
        .sf-archive-app-table-shell .fi-ta-content {
            width: 100% !important;
            min-width: 100% !important;
        }

        .sf-archive-app-table-shell .fi-ta-table {
            width: 100% !important;
            min-width: 1040px !important;
        }

        .sf-archive-app-table-shell .fi-ta-content {
            overflow-x: auto !important;
        }

        .sf-archive-app-table-shell table {
            width: 100% !important;
            min-width: 1040px !important;
            table-layout: auto !important;
        }

        .sf-archive-app-table-shell .fi-ta-empty-state {
            min-height: 260px !important;
        }


        .dark .sf-archive-app-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .sf-archive-app-table-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .sf-archive-app-table-shell table thead th {
            background: rgba(31,70,100,.55) !important;
            color: #dff7fb !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .sf-archive-app-table-shell table tbody td {
            background: rgba(15,23,42,.72) !important;
            color: #e5eef5 !important;
            border-color: rgba(148,163,184,.12) !important;
        }

        .dark .sf-archive-app-table-shell table tbody tr:hover td {
            background: rgba(31,70,100,.35) !important;
        }

        .dark .sf-archive-app-table-shell .fi-ta-empty-state {
            background: rgba(15,23,42,.72) !important;
        }

        .dark .sf-archive-app-table-shell .fi-ta-empty-state-heading {
            color: #f8fafc !important;
        }

        .dark .sf-archive-app-table-shell .fi-input-wrp,
        .dark .sf-archive-app-table-shell .fi-select,
        .dark .sf-archive-app-table-shell .fi-input,
        .dark .sf-archive-app-table-shell .fi-select-input,
        .dark .sf-archive-app-table-shell .fi-ta-search-field input {
            background: rgba(15,23,42,.82) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.20) !important;
        }

        @media (max-width: 900px) {
            .sf-archive-app-wrap {
                width: calc(100vw - 32px) !important;
                min-width: 0 !important;
            }
        }

        @media (max-width: 720px) {
            .sf-archive-app-hero {
                padding: 28px 24px;
            }

            .sf-archive-app-title {
                font-size: 42px;
            }
        }
    </style>

    <div class="sf-archive-app-wrap">
        <section class="sf-archive-app-hero">
            <div class="sf-archive-app-hero-inner">
                <div>
                    <div class="sf-archive-app-breadcrumb">Archive › Job Applications</div>
                    <div class="sf-archive-app-title">Archived Job Applications</div>
                    <div class="sf-archive-app-subtitle">
                        Review archived applications, decline reasons, archive notes, and historical recruitment decisions.
                    </div>

                    <div class="sf-archive-app-badge-row">
                        @if(! is_null($totalArchivedApplications))
                            <span class="sf-archive-app-badge">{{ $totalArchivedApplications }} Archived Applications</span>
                        @endif

                        @if(! is_null($declinedApplications))
                            <span class="sf-archive-app-badge">{{ $declinedApplications }} Declined / Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="sf-archive-app-table-shell">
            {{ $this->table }}
        </section>
    </div>
</x-filament-panels::page>

<style id="sf-archived-job-applications-one-clean-final">
    /*
     | ONE CLEAN FINAL BLOCK — Archived Job Applications
     | This replaces all previous stacked patches.
     */

    /* Table shell */
    .fi-ta,
    .fi-ta-ctn,
    .fi-ta-content,
    .fi-ta-table,
    .fi-ta-outer {
        overflow: visible !important;
    }

    .fi-ta-toolbar,
    .fi-ta-header-toolbar {
        position: relative !important;
        min-height: 88px !important;
        overflow: visible !important;
    }

    /*
     | Bulk actions:
     | Hidden until a row is selected.
     */
    body:not(.sf-archive-apps-has-selection) .fi-ta-bulk-actions,
    body:not(.sf-archive-apps-has-selection) .fi-ta-bulk-actions-toolbar {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    body.sf-archive-apps-has-selection .fi-ta-bulk-actions,
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions-toolbar {
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

    /*
     | Bulk buttons exactly like Archived Job Openings:
     | Green restore + red permanent delete.
     */
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions .fi-btn,
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions-toolbar .fi-btn {
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

    body.sf-archive-apps-has-selection .fi-ta-bulk-actions .fi-btn:first-child,
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions-toolbar .fi-btn:first-child {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #ffffff !important;
        border: 1px solid rgba(22, 163, 74, .30) !important;
        box-shadow: 0 14px 28px rgba(34, 197, 94, .22) !important;
    }

    body.sf-archive-apps-has-selection .fi-ta-bulk-actions .fi-btn:nth-child(2),
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions-toolbar .fi-btn:nth-child(2) {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
        border: 1px solid rgba(220, 38, 38, .32) !important;
        box-shadow: 0 14px 28px rgba(239, 68, 68, .22) !important;
    }

    body.sf-archive-apps-has-selection .fi-ta-bulk-actions .fi-btn-label,
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions-toolbar .fi-btn-label {
        display: inline-flex !important;
        align-items: center !important;
        color: inherit !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
    }

    body.sf-archive-apps-has-selection .fi-ta-bulk-actions .fi-btn svg,
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions-toolbar .fi-btn svg,
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions .fi-btn .fi-btn-icon,
    body.sf-archive-apps-has-selection .fi-ta-bulk-actions-toolbar .fi-btn .fi-btn-icon {
        width: 17px !important;
        height: 17px !important;
        min-width: 17px !important;
        min-height: 17px !important;
        color: #ffffff !important;
        stroke: currentColor !important;
        stroke-width: 2.35 !important;
        margin: 0 !important;
    }

    .fi-ta-bulk-actions .animate-spin,
    .fi-ta-bulk-actions-toolbar .animate-spin,
    .fi-ta-bulk-actions [class*="spinner"],
    .fi-ta-bulk-actions-toolbar [class*="spinner"],
    .fi-ta-bulk-actions [class*="loading"],
    .fi-ta-bulk-actions-toolbar [class*="loading"] {
        display: none !important;
    }

    /*
     | Row actions — saved ERP icon style.
     */
    .fi-ta-actions,
    .fi-ta-record-actions,
    td:last-child .fi-ta-actions,
    td:last-child .fi-ta-record-actions {
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        flex-wrap: nowrap !important;
        width: 100% !important;
    }

    .sf-archive-row-action,
    .sf-archive-row-action.fi-btn,
    .fi-ta-actions .sf-archive-row-action,
    .fi-ta-record-actions .sf-archive-row-action {
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

    .sf-archive-row-action-restore {
        background: #f2b705 !important;
        color: #0f172a !important;
        border: 1px solid rgba(179, 139, 47, .30) !important;
        box-shadow: 0 12px 22px rgba(242, 183, 5, .18) !important;
    }

    .sf-archive-row-action-delete,
    .fi-ta-actions .fi-btn-color-danger,
    .fi-ta-record-actions .fi-btn-color-danger {
        background: #f8fafc !important;
        color: #64748b !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 10px 18px rgba(15, 23, 42, .045) !important;
    }

    .sf-archive-row-action svg,
    .sf-archive-row-action.fi-btn svg,
    .fi-ta-actions .fi-btn svg,
    .fi-ta-record-actions .fi-btn svg {
        width: 23px !important;
        height: 23px !important;
        min-width: 23px !important;
        min-height: 23px !important;
        stroke-width: 2.35 !important;
    }

    .sf-archive-row-action .fi-btn-label,
    .fi-ta-actions .fi-btn-label,
    .fi-ta-record-actions .fi-btn-label {
        display: none !important;
    }

    .dark .sf-archive-row-action-delete,
    .dark .fi-ta-actions .fi-btn-color-danger,
    .dark .fi-ta-record-actions .fi-btn-color-danger {
        background: rgba(255, 255, 255, .075) !important;
        border-color: rgba(255, 255, 255, .12) !important;
        color: #cbd5e1 !important;
    }
</style>

<script id="sf-archived-job-applications-one-clean-final-script">
    (() => {
        const applyArchiveApplicationsCleanUI = () => {
            const checked = document.querySelectorAll('table tbody input[type="checkbox"]:checked').length > 0;

            document.body.classList.toggle('sf-archive-apps-has-selection', checked);

            document
                .querySelectorAll('.fi-ta-bulk-actions .fi-btn, .fi-ta-bulk-actions-toolbar .fi-btn')
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

        document.addEventListener('DOMContentLoaded', applyArchiveApplicationsCleanUI);

        document.addEventListener('change', (event) => {
            if (event.target && event.target.matches('input[type="checkbox"]')) {
                setTimeout(applyArchiveApplicationsCleanUI, 40);
            }
        });

        new MutationObserver(applyArchiveApplicationsCleanUI).observe(document.body, {
            childList: true,
            subtree: true,
        });

        setInterval(applyArchiveApplicationsCleanUI, 800);
    })();
</script>


<style id="sf-archive-applications-actions-column-label-final">
    /*
     | Add visible ACTIONS header above the row action buttons.
     | Keep current cleaned style untouched.
     */

    .fi-ta-table thead tr th:last-child,
    .fi-ta-table tbody tr td:last-child {
        width: 140px !important;
        min-width: 140px !important;
        max-width: 140px !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .fi-ta-table thead tr th:last-child {
        position: relative !important;
        color: transparent !important;
        font-size: 0 !important;
        background: #eef5f8 !important;
    }

    .fi-ta-table thead tr th:last-child::before {
        content: "ACTIONS" !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        color: #1f4664 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
        line-height: 1 !important;
    }

    .fi-ta-table tbody tr td:last-child > *,
    .fi-ta-table tbody tr td:last-child .fi-ta-actions,
    .fi-ta-table tbody tr td:last-child .fi-ta-record-actions {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-direction: row !important;
        gap: 10px !important;
        width: 100% !important;
        margin: 0 !important;
    }

    .dark .fi-ta-table thead tr th:last-child {
        background: rgba(15, 23, 42, .88) !important;
    }

    .dark .fi-ta-table thead tr th:last-child::before {
        color: #e0f2fe !important;
    }
</style>

<style id="sf-archive-applications-actions-header-align-final">
    /*
     | Fix ACTIONS header vertical alignment.
     | Make it same height/center as other table headers.
     */

    .fi-ta-table thead tr th:last-child {
        height: 48px !important;
        min-height: 48px !important;
        padding: 0 16px !important;
        vertical-align: middle !important;
        text-align: center !important;
        background: #eef5f8 !important;
        color: transparent !important;
        font-size: 0 !important;
        line-height: 48px !important;
    }

    .fi-ta-table thead tr th:last-child::before {
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
        transform: translateY(0) !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .fi-ta-table tbody tr td:last-child {
        text-align: center !important;
        vertical-align: middle !important;
    }

    .dark .fi-ta-table thead tr th:last-child {
        background: rgba(15, 23, 42, .88) !important;
    }

    .dark .fi-ta-table thead tr th:last-child::before {
        color: #e0f2fe !important;
    }
</style>
