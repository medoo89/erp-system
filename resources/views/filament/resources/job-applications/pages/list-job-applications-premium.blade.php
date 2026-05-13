<x-filament-panels::page>
    @php
        $resourceClass = null;

        foreach ([
            \App\Filament\Resources\JobApplications\JobApplicationResource::class,
            \App\Filament\Resources\JobApplicationResource::class,
        ] as $class) {
            if (class_exists($class)) {
                $resourceClass = $class;
                break;
            }
        }

        $totalApplications = null;
        $pendingApplications = null;

        foreach ([
            \App\Models\JobApplication::class,
            \App\Models\Application::class,
        ] as $modelClass) {
            try {
                if (! class_exists($modelClass)) {
                    continue;
                }

                $model = new $modelClass;
                $table = $model->getTable();

                $totalApplications = $modelClass::query()->count();

                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'status')) {
                    $pendingApplications = $modelClass::query()
                        ->whereIn('status', ['new', 'pending', 'submitted', 'initiated'])
                        ->count();
                }

                break;
            } catch (\Throwable $e) {
                $totalApplications = null;
                $pendingApplications = null;
            }
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .ja-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .ja-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px 36px;
            border: 1px solid rgba(76,167,168,.24);
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.26), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.16), transparent 30%),
                linear-gradient(135deg,#081a34 0%,#12385d 56%,#2f6f73 100%) !important;
            box-shadow: 0 18px 36px rgba(15,23,42,.14);
            color: #fff;
        }

        .ja-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg,#4ca7a8,#b38b2f);
        }

        .ja-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 22px;
            flex-wrap: wrap;
        }

        .ja-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .ja-title {
            font-size: clamp(46px,4vw,66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .ja-subtitle {
            margin-top: 16px;
            max-width: 860px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .ja-badges {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ja-badge {
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

        .ja-table-shell {
            position: relative;
            overflow: visible !important;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.08), transparent 34%),
                linear-gradient(180deg,#ffffff 0%,#f8fbfc 100%) !important;
            box-shadow: 0 14px 30px rgba(15,23,42,.07);
        }

        .ja-table-shell .fi-ta-outer,
        .ja-table-shell .fi-ta,
        .ja-table-shell .fi-ta-content,
        .ja-table-shell .fi-ta-header,
        .ja-table-shell .fi-ta-toolbar,
        .ja-table-shell .fi-ta-table,
        .ja-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .ja-table-shell .fi-ta-ctn,
        .ja-table-shell .fi-ta-table {
            overflow: visible !important;
        }

        .ja-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-weight: 900 !important;
            letter-spacing: .06em !important;
            text-transform: uppercase !important;
            border-color: #d7e2e5 !important;
        }

        .ja-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
        }

        .ja-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .ja-table-shell .fi-input-wrp,
        .ja-table-shell .fi-select,
        .ja-table-shell .fi-input,
        .ja-table-shell .fi-select-input,
        .ja-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .ja-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 850 !important;
        }

        .ja-table-shell .fi-ta-actions,
        .ja-table-shell .fi-ta-actions-cell {
            white-space: nowrap !important;
            text-align: right !important;
        }

        .ja-table-shell .fi-ta-actions {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 8px !important;
        }

        .ja-table-shell .fi-ta-actions a,
        .ja-table-shell .fi-ta-actions button,
        .ja-table-shell a.fi-link,
        .ja-table-shell button.fi-link {
            min-height: 34px !important;
            height: 34px !important;
            padding: 0 14px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 12px !important;
            font-weight: 950 !important;
            text-decoration: none !important;
            transition: .18s ease !important;
        }

        .ja-table-shell .fi-ta-actions a,
        .ja-table-shell a.fi-link {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 1px solid rgba(179,139,47,.28) !important;
            box-shadow: 0 8px 18px rgba(242,183,5,.16) !important;
        }

        .ja-table-shell .fi-ta-actions a:hover,
        .ja-table-shell .fi-ta-actions button:hover,
        .ja-table-shell a.fi-link:hover,
        .ja-table-shell button.fi-link:hover {
            transform: translateY(-1px) !important;
        }

        .ja-table-shell .fi-dropdown-panel {
            border-radius: 22px !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 70px rgba(15,23,42,.22) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
        }

        .ja-table-shell .fi-dropdown-panel .fi-dropdown-list,
        .ja-table-shell .fi-dropdown-panel .fi-ta-column-toggle-dropdown,
        .ja-table-shell .fi-dropdown-panel [role="menu"] {
            max-height: 420px !important;
            overflow-y: auto !important;
            padding-right: 6px !important;
        }

        .ja-table-shell .fi-dropdown-panel input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #1f4664 !important;
        }

        .ja-table-shell .fi-dropdown-panel label,
        .ja-table-shell .fi-dropdown-panel span {
            color: #0f172a !important;
            font-weight: 800 !important;
        }

        .ja-table-shell .fi-dropdown-panel .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 2 !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
        }

        .dark .ja-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg,#071427 0%,#0b1a31 58%,#12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .ja-table-shell {
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.10), transparent 34%),
                linear-gradient(180deg,rgba(12,23,38,.98) 0%,rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .ja-table-shell table thead th {
            background: rgba(15,23,42,.92) !important;
            color: #8fd6d7 !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .ja-table-shell table tbody td {
            background: rgba(12,23,38,.96) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.10) !important;
        }

        .dark .ja-table-shell table tbody tr:hover td {
            background: rgba(15,23,42,.98) !important;
        }

        .dark .ja-table-shell .fi-input-wrp,
        .dark .ja-table-shell .fi-select,
        .dark .ja-table-shell .fi-input,
        .dark .ja-table-shell .fi-select-input,
        .dark .ja-table-shell .fi-ta-search-field input {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 10px 22px rgba(0,0,0,.24) !important;
        }

        .dark .ja-table-shell .fi-dropdown-panel {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
        }

        .dark .ja-table-shell .fi-dropdown-panel label,
        .dark .ja-table-shell .fi-dropdown-panel span {
            color: #f8fafc !important;
        }

        @media (max-width:900px) {
            .ja-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="ja-wrap">
        <section class="ja-hero">
            <div class="ja-hero-inner">
                <div>
                    <div class="ja-breadcrumb">Recruitment › Job Applications › List</div>
                    <div class="ja-title">Job Applications</div>
                    <div class="ja-subtitle">
                        Review submitted candidate applications, open the candidate profile, and continue the recruitment workflow into pre-employment or employment.
                    </div>

                    <div class="ja-badges">
                        @if(! is_null($totalApplications))
                            <div class="ja-badge">{{ $totalApplications }} Applications</div>
                        @endif

                        @if(! is_null($pendingApplications))
                            <div class="ja-badge">{{ $pendingApplications }} Pending</div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="ja-table-shell">
            {{ $this->table }}
        </section>
    </div>
</x-filament-panels::page>


<style id="sf-job-applications-force-hide-toolbar-actions-final">
    /*
     | Job Applications toolbar bulk actions final fix.
     | Hide workflow/action buttons until a table row is selected.
     */

    body:not(.sf-job-applications-has-selection) .sf-job-app-toolbar-bulk-btn {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    body.sf-job-applications-has-selection .sf-job-app-toolbar-bulk-btn {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .fi-ta-selection-indicator button,
    .fi-ta-selection-indicator a {
        display: none !important;
    }

    .sf-job-app-toolbar-bulk-btn .animate-spin,
    .sf-job-app-toolbar-bulk-btn [class*="spinner"],
    .sf-job-app-toolbar-bulk-btn [class*="loading"] {
        display: none !important;
    }
</style>

<script id="sf-job-applications-force-hide-toolbar-actions-final-script">
    (() => {
        const bulkLabels = [
            'Create Request',
            'Export Selected CSV',
            'Move to Screening',
            'Move to Under Review',
            'Move to Client Submitted',
            'Move to Qualified',
            'Move to Hired',
            'Decline and Archive',
            'Archive',
            'Permanent Delete'
        ];

        const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim();

        const isTableToolButton = (el) => {
            return el.closest('.fi-ta-toolbar, .fi-ta-header-toolbar, .fi-ta-header, .fi-ta');
        };

        const isSearchFilterColumnControl = (el) => {
            const text = normalize(el.innerText || el.textContent || '');
            const aria = normalize(el.getAttribute('aria-label') || '');
            const title = normalize(el.getAttribute('title') || '');
            const all = `${text} ${aria} ${title}`.toLowerCase();

            return (
                all.includes('search') ||
                all.includes('filter') ||
                all.includes('column') ||
                all.includes('toggle') ||
                all.includes('per page')
            );
        };

        const markBulkButtons = () => {
            document
                .querySelectorAll('button, a')
                .forEach((el) => {
                    if (!isTableToolButton(el)) return;
                    if (isSearchFilterColumnControl(el)) return;

                    const text = normalize(el.innerText || el.textContent || el.getAttribute('aria-label') || el.getAttribute('title') || '');

                    const matched = bulkLabels.some((label) => text.includes(label));

                    if (matched) {
                        el.classList.add('sf-job-app-toolbar-bulk-btn');
                    }
                });
        };

        const hasSelectedRows = () => {
            return document.querySelectorAll(
                '.fi-ta-table tbody input[type="checkbox"]:checked, table tbody input[type="checkbox"]:checked'
            ).length > 0;
        };

        const apply = () => {
            markBulkButtons();
            document.body.classList.toggle('sf-job-applications-has-selection', hasSelectedRows());
        };

        document.addEventListener('DOMContentLoaded', apply);
        document.addEventListener('livewire:navigated', apply);

        document.addEventListener('change', (event) => {
            if (event.target && event.target.matches('input[type="checkbox"]')) {
                setTimeout(apply, 20);
                setTimeout(apply, 120);
                setTimeout(apply, 300);
            }
        }, true);

        document.addEventListener('click', () => {
            setTimeout(apply, 40);
            setTimeout(apply, 160);
            setTimeout(apply, 350);
        }, true);

        new MutationObserver(apply).observe(document.body, {
            childList: true,
            subtree: true,
        });

        setTimeout(apply, 100);
        setTimeout(apply, 400);
        setTimeout(apply, 1000);
    })();
</script>
