<x-filament-panels::page>
    @php
        $isArchivedJobOpeningsPage = request()->is('admin/archived-job-openings*');
        $jobsPageBreadcrumb = $isArchivedJobOpeningsPage
            ? 'Archive › Job Openings › List'
            : '{{ $jobsPageBreadcrumb }}';
        $jobsPageTitle = $isArchivedJobOpeningsPage
            ? 'Archived Job Openings'
            : 'Job Openings';
        $jobsPageSubtitle = $isArchivedJobOpeningsPage
            ? 'Review archived, closed, and expired job openings. Restore selected records or permanently delete them when needed.'
            : '{{ $jobsPageSubtitle }}';
        $resourceClass = null;

        foreach ([
            \App\Filament\Resources\Jobs\JobResource::class,
            \App\Filament\Resources\JobOpenings\JobOpeningResource::class,
            \App\Filament\Resources\JobOpeningResource::class,
        ] as $class) {
            if (class_exists($class)) {
                $resourceClass = $class;
                break;
            }
        }

        try {
            $createUrl = $resourceClass ? $resourceClass::getUrl('create') : url('/admin/jobs/create');
        } catch (\Throwable $e) {
            $createUrl = url('/admin/jobs/create');
        }

        $totalRecords = null;
        $publishedRecords = null;

        foreach ([\App\Models\Job::class, \App\Models\JobOpening::class] as $modelClass) {
            try {
                if (! class_exists($modelClass)) continue;
                $model = new $modelClass;
                $table = $model->getTable();
                $totalRecords = $modelClass::query()->count();

                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'is_published')) {
                    $publishedRecords = $modelClass::query()->where('is_published', true)->count();
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn($table, 'published')) {
                    $publishedRecords = $modelClass::query()->where('published', true)->count();
                }

                break;
            } catch (\Throwable $e) {}
        }
    @endphp

    <style>
        .fi-header { display:none !important; }

        .job-premium-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .job-premium-hero {
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
            color:#fff;
        }

        .job-premium-hero::after {
            content:"";
            position:absolute;
            left:0;
            right:0;
            bottom:0;
            height:4px;
            background:linear-gradient(90deg,#4ca7a8,#b38b2f);
        }

        .job-premium-hero-inner {
            position:relative;
            z-index:1;
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:22px;
            flex-wrap:wrap;
        }

        .job-premium-breadcrumb {
            font-size:14px;
            color:rgba(255,255,255,.72);
            font-weight:650;
            margin-bottom:12px;
        }

        .job-premium-title {
            font-size:clamp(46px,4vw,66px);
            line-height:.95;
            font-weight:950;
            letter-spacing:-.055em;
            color:#fff !important;
        }

        .job-premium-subtitle {
            margin-top:16px;
            max-width:860px;
            font-size:15px;
            line-height:1.7;
            color:rgba(255,255,255,.82) !important;
        }

        .job-premium-badges {
            margin-top:18px;
            display:flex;
            flex-wrap:wrap;
            gap:10px;
        }

        .job-premium-badge {
            display:inline-flex;
            align-items:center;
            min-height:36px;
            padding:0 14px;
            border-radius:999px;
            background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.14);
            color:#fff;
            font-size:12px;
            font-weight:950;
            letter-spacing:.10em;
            text-transform:uppercase;
        }

        .job-premium-btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:46px;
            padding:0 18px;
            border-radius:999px;
            background:#f2b705;
            color:#3b2a00 !important;
            text-decoration:none !important;
            font-size:14px;
            font-weight:950;
            box-shadow:0 12px 24px rgba(242,183,5,.22);
            transition:.18s ease;
        }

        .job-premium-btn:hover {
            transform:translateY(-1px);
            box-shadow:0 16px 28px rgba(242,183,5,.30);
        }

        .job-table-shell,
        .job-form-shell {
            border-radius:30px;
            border:1px solid #d7e2e5;
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.08), transparent 34%),
                linear-gradient(180deg,#ffffff 0%,#f8fbfc 100%) !important;
            box-shadow:0 14px 30px rgba(15,23,42,.07);
            overflow:visible !important;
        }

        .job-table-shell {
            border-radius:26px;
        }

        .job-table-shell .fi-ta-outer,
        .job-table-shell .fi-ta,
        .job-table-shell .fi-ta-content,
        .job-table-shell .fi-ta-header,
        .job-table-shell .fi-ta-toolbar,
        .job-table-shell .fi-ta-table,
        .job-table-shell .fi-pagination {
            background:transparent !important;
            box-shadow:none !important;
            border-radius:0 !important;
        }

        .job-table-shell table thead th {
            background:#eef5f8 !important;
            color:#1f4664 !important;
            font-weight:900 !important;
            letter-spacing:.06em !important;
            text-transform:uppercase !important;
            border-color:#d7e2e5 !important;
        }

        .job-table-shell table tbody td {
            background:#ffffff !important;
            color:#0f172a !important;
            border-color:#eef2f7 !important;
        }

        .job-table-shell table tbody tr:hover td {
            background:#f8fcfd !important;
        }

        .job-table-shell .fi-input-wrp,
        .job-table-shell .fi-select,
        .job-table-shell .fi-input,
        .job-table-shell .fi-select-input,
        .job-table-shell .fi-ta-search-field input {
            border-radius:999px !important;
            background:#ffffff !important;
            color:#0f172a !important;
            border-color:#d7e2e5 !important;
            box-shadow:0 8px 18px rgba(15,23,42,.035) !important;
        }

        .job-table-shell .fi-badge {
            border-radius:999px !important;
            font-weight:850 !important;
        }

        .job-table-shell .fi-ta-actions,
        .job-table-shell .fi-ta-actions-cell {
            white-space:nowrap !important;
            text-align:right !important;
        }

        .job-table-shell .fi-ta-actions {
            display:inline-flex !important;
            align-items:center !important;
            justify-content:flex-end !important;
            gap:8px !important;
        }

        .job-table-shell .fi-ta-actions a,
        .job-table-shell .fi-ta-actions button,
        .job-table-shell a.fi-link,
        .job-table-shell button.fi-link {
            min-height:34px !important;
            height:34px !important;
            padding:0 14px !important;
            border-radius:999px !important;
            display:inline-flex !important;
            align-items:center !important;
            justify-content:center !important;
            font-size:12px !important;
            font-weight:950 !important;
            text-decoration:none !important;
        }

        .job-table-shell .fi-ta-actions a,
        .job-table-shell a.fi-link {
            background:#f2b705 !important;
            color:#3b2a00 !important;
            border:1px solid rgba(179,139,47,.28) !important;
            box-shadow:0 8px 18px rgba(242,183,5,.16) !important;
        }

        .job-form-shell {
            padding:26px 24px !important;
            min-height:520px;
            display:flex;
            flex-direction:column;
            align-items:center;
        }

        .job-form-shell form {
            width:100% !important;
        }

        .job-form-shell .fi-section {
            border-radius:28px !important;
            border:1px solid #d7e2e5 !important;
            background:linear-gradient(180deg,#ffffff 0%,#f8fbfc 100%) !important;
            box-shadow:0 10px 22px rgba(15,23,42,.05) !important;
            overflow:visible !important;
        }

        .job-form-shell .fi-section-header {
            background:linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%) !important;
            border-bottom:1px solid #e4ecef !important;
            padding:18px 22px !important;
        }

        .job-form-shell .fi-section-header-heading,
        .job-form-shell .fi-section h2,
        .job-form-shell .fi-section h3 {
            color:#0f172a !important;
            font-weight:950 !important;
        }

        .job-form-shell .fi-section-content {
            padding:24px !important;
        }

        .job-form-shell .fi-input-wrp,
        .job-form-shell .fi-select,
        .job-form-shell .fi-textarea,
        .job-form-shell input,
        .job-form-shell select,
        .job-form-shell textarea {
            border-radius:18px !important;
        }

        .job-form-shell .fi-input-wrp,
        .job-form-shell .fi-select,
        .job-form-shell .fi-textarea {
            min-height:48px !important;
            background:rgba(255,255,255,.96) !important;
            border:1px solid #d7e2e5 !important;
            box-shadow:0 10px 22px rgba(15,23,42,.045) !important;
        }

        .job-form-shell textarea {
            min-height:140px !important;
        }

        .job-form-shell input,
        .job-form-shell textarea,
        .job-form-shell select {
            color:#0f172a !important;
        }

        .job-form-shell label,
        .job-form-shell .fi-fo-field-wrp-label span {
            color:#334155 !important;
            font-weight:800 !important;
        }

        .job-actions-bottom {
            width:100%;
            padding:18px 0 0 !important;
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        }

        .job-save-btn,
        .job-create-btn,
        .job-create-another-btn,
        .job-cancel-btn {
            display:inline-flex !important;
            align-items:center !important;
            justify-content:center !important;
            min-height:44px !important;
            padding:0 20px !important;
            border-radius:999px !important;
            font-size:13px !important;
            font-weight:950 !important;
            text-decoration:none !important;
            cursor:pointer !important;
            transition:.18s ease !important;
        }

        .job-save-btn,
        .job-create-btn {
            background:#f2b705 !important;
            color:#3b2a00 !important;
            border:0 !important;
            box-shadow:0 12px 24px rgba(242,183,5,.22) !important;
        }

        .job-create-another-btn,
        .job-cancel-btn {
            background:#ffffff !important;
            color:#0f172a !important;
            border:1px solid #d7e2e5 !important;
            box-shadow:0 8px 18px rgba(15,23,42,.04) !important;
        }

        .job-save-btn:hover,
        .job-create-btn:hover,
        .job-create-another-btn:hover,
        .job-cancel-btn:hover {
            transform:translateY(-1px) !important;
        }

        .dark .job-premium-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg,#071427 0%,#0b1a31 58%,#12385d 100%) !important;
            border-color:rgba(76,167,168,.18) !important;
        }

        .dark .job-table-shell,
        .dark .job-form-shell {
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.10), transparent 34%),
                linear-gradient(180deg,rgba(12,23,38,.98) 0%,rgba(15,23,42,.96) 100%) !important;
            border-color:rgba(76,167,168,.18) !important;
            box-shadow:0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .job-table-shell table thead th {
            background:rgba(15,23,42,.92) !important;
            color:#8fd6d7 !important;
            border-color:rgba(76,167,168,.16) !important;
        }

        .dark .job-table-shell table tbody td {
            background:rgba(12,23,38,.96) !important;
            color:#f8fafc !important;
            border-color:rgba(76,167,168,.10) !important;
        }

        .dark .job-form-shell .fi-section {
            background:linear-gradient(180deg,rgba(12,23,38,.98) 0%,rgba(15,23,42,.96) 100%) !important;
            border-color:rgba(76,167,168,.18) !important;
        }

        .dark .job-form-shell .fi-section-header {
            background:rgba(15,23,42,.92) !important;
            border-bottom-color:rgba(76,167,168,.16) !important;
        }

        .dark .job-form-shell .fi-section-header-heading,
        .dark .job-form-shell .fi-section h2,
        .dark .job-form-shell .fi-section h3 {
            color:#f8fafc !important;
        }

        .dark .job-form-shell .fi-input-wrp,
        .dark .job-form-shell .fi-select,
        .dark .job-form-shell .fi-textarea,
        .dark .job-table-shell .fi-input-wrp,
        .dark .job-table-shell .fi-select,
        .dark .job-table-shell .fi-input,
        .dark .job-table-shell .fi-select-input,
        .dark .job-table-shell .fi-ta-search-field input {
            background:rgba(15,23,42,.92) !important;
            color:#f8fafc !important;
            border-color:rgba(76,167,168,.20) !important;
            box-shadow:0 10px 22px rgba(0,0,0,.24) !important;
        }

        .dark .job-form-shell input,
        .dark .job-form-shell textarea,
        .dark .job-form-shell select {
            color:#f8fafc !important;
        }

        .dark .job-form-shell label,
        .dark .job-form-shell .fi-fo-field-wrp-label span {
            color:#dbeafe !important;
        }

        .dark .job-create-another-btn,
        .dark .job-cancel-btn {
            background:rgba(15,23,42,.92) !important;
            color:#f8fafc !important;
            border-color:rgba(76,167,168,.18) !important;
        }

        @media (max-width:900px) {
            .job-premium-hero { padding:28px 24px; }
            .job-form-shell { padding:18px !important; }
        }
    </style>

    <div class="job-premium-wrap">
        <section class="job-premium-hero">
            <div class="job-premium-hero-inner">
                <div>
                    <div class="job-premium-breadcrumb">Recruitment › Job Openings › List</div>
                    <div class="job-premium-title">{{ $jobsPageTitle }}</div>
                    <div class="job-premium-subtitle">
                        Manage job openings, client/project linkage, employment type, locations, expiry dates, and public application visibility.
                    </div>
                    <div class="job-premium-badges">
                        @if(! is_null($totalRecords))
                            <div class="job-premium-badge">{{ $totalRecords }} Openings</div>
                        @endif
                        @if(! is_null($publishedRecords))
                            <div class="job-premium-badge">{{ $publishedRecords }} Published</div>
                        @endif
                    </div>
                </div>
                @unless($isArchivedJobOpeningsPage)
                <a href="{{ $createUrl }}" class="job-premium-btn">New Job Opening</a>
                            @endunless
</div>
        </section>

        <section class="job-table-shell">
            {{ $this->table }}
        </section>
    </div>

<style id="sf-jobs-archive-mode-hide-create-final">
    body.sf-archived-job-openings-page .job-premium-btn[href*="/admin/jobs/create"],
    body.sf-archived-job-openings-page a[href*="/admin/jobs/create"],
    body.sf-archived-job-openings-page a[href*="/admin/job-openings/create"],
    body.sf-archived-job-openings-page a[href*="/admin/archived-job-openings/create"] {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
</style>

<script id="sf-jobs-archive-mode-hide-create-js-final">
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.location.pathname.includes('/admin/archived-job-openings')) {
            return;
        }

        document.body.classList.add('sf-archived-job-openings-page');

        const removeArchiveCreateButton = function () {
            document.querySelectorAll('a, button').forEach(function (el) {
                const text = (el.textContent || '').trim().toLowerCase();
                const href = (el.getAttribute('href') || '').toLowerCase();

                if (
                    text.includes('new job opening') ||
                    href.includes('/admin/jobs/create') ||
                    href.includes('/admin/job-openings/create') ||
                    href.includes('/admin/archived-job-openings/create')
                ) {
                    el.remove();
                }
            });
        };

        removeArchiveCreateButton();

        new MutationObserver(removeArchiveCreateButton).observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>


<style id="sf-archive-table-icon-actions-final">
    /*
     | Archive table actions polish.
     | Converts Restore Selected / Permanent Delete into premium icon-style controls
     | without changing the actual Livewire/Filament actions.
     */

    body.sf-archived-job-openings-page .fi-ta-header-toolbar,
    body.sf-archived-job-openings-page .fi-ta-toolbar,
    body.sf-archived-job-openings-page .fi-ta-actions,
    body.sf-archived-job-openings-page .fi-ta-header .fi-actions,
    body.sf-archived-job-openings-page .fi-ac {
        align-items: center !important;
        gap: 12px !important;
    }

    body.sf-archived-job-openings-page .fi-ta-header-toolbar .fi-btn,
    body.sf-archived-job-openings-page .fi-ta-toolbar .fi-btn,
    body.sf-archived-job-openings-page .fi-ta-actions .fi-btn,
    body.sf-archived-job-openings-page .fi-ta-header .fi-actions .fi-btn,
    body.sf-archived-job-openings-page .fi-ac .fi-btn {
        width: 58px !important;
        height: 58px !important;
        min-width: 58px !important;
        min-height: 58px !important;
        padding: 0 !important;
        border-radius: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
        border: 1px solid rgba(226, 232, 240, .92) !important;
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.92), transparent 42%),
            linear-gradient(180deg, #f8fafc 0%, #eef4f8 100%) !important;
        box-shadow:
            0 12px 26px rgba(15, 23, 42, .055),
            inset 0 1px 0 rgba(255,255,255,.75) !important;
        color: #64748b !important;
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background .18s ease !important;
    }

    body.sf-archived-job-openings-page .fi-ta-header-toolbar .fi-btn:hover,
    body.sf-archived-job-openings-page .fi-ta-toolbar .fi-btn:hover,
    body.sf-archived-job-openings-page .fi-ta-actions .fi-btn:hover,
    body.sf-archived-job-openings-page .fi-ta-header .fi-actions .fi-btn:hover,
    body.sf-archived-job-openings-page .fi-ac .fi-btn:hover {
        transform: translateY(-2px) !important;
        border-color: rgba(37, 99, 235, .26) !important;
        box-shadow:
            0 18px 34px rgba(15, 23, 42, .10),
            inset 0 1px 0 rgba(255,255,255,.85) !important;
    }

    /*
     | Hide button text visually, keep action accessible.
     */
    body.sf-archived-job-openings-page .fi-ta-header-toolbar .fi-btn-label,
    body.sf-archived-job-openings-page .fi-ta-toolbar .fi-btn-label,
    body.sf-archived-job-openings-page .fi-ta-actions .fi-btn-label,
    body.sf-archived-job-openings-page .fi-ta-header .fi-actions .fi-btn-label,
    body.sf-archived-job-openings-page .fi-ac .fi-btn-label {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        overflow: hidden !important;
        clip: rect(0 0 0 0) !important;
        white-space: nowrap !important;
    }

    body.sf-archived-job-openings-page .fi-ta-header-toolbar .fi-btn svg,
    body.sf-archived-job-openings-page .fi-ta-toolbar .fi-btn svg,
    body.sf-archived-job-openings-page .fi-ta-actions .fi-btn svg,
    body.sf-archived-job-openings-page .fi-ta-header .fi-actions .fi-btn svg,
    body.sf-archived-job-openings-page .fi-ac .fi-btn svg {
        width: 28px !important;
        height: 28px !important;
        color: currentColor !important;
        stroke-width: 2.15px !important;
    }

    /*
     | Restore = calm green icon glass.
     */
    body.sf-archived-job-openings-page .fi-btn-color-success,
    body.sf-archived-job-openings-page button[wire\:click*="restore"],
    body.sf-archived-job-openings-page button[title*="Restore"],
    body.sf-archived-job-openings-page button[aria-label*="Restore"] {
        color: #059669 !important;
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.92), transparent 42%),
            linear-gradient(180deg, #ecfdf5 0%, #dff7ec 100%) !important;
        border-color: rgba(16, 185, 129, .24) !important;
    }

    body.sf-archived-job-openings-page .fi-btn-color-success:hover,
    body.sf-archived-job-openings-page button[wire\:click*="restore"]:hover,
    body.sf-archived-job-openings-page button[title*="Restore"]:hover,
    body.sf-archived-job-openings-page button[aria-label*="Restore"]:hover {
        color: #047857 !important;
        border-color: rgba(16, 185, 129, .36) !important;
        box-shadow: 0 18px 34px rgba(16, 185, 129, .14) !important;
    }

    /*
     | Permanent Delete = calm red icon glass.
     */
    body.sf-archived-job-openings-page .fi-btn-color-danger,
    body.sf-archived-job-openings-page button[wire\:click*="delete"],
    body.sf-archived-job-openings-page button[title*="Delete"],
    body.sf-archived-job-openings-page button[aria-label*="Delete"] {
        color: #dc2626 !important;
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.92), transparent 42%),
            linear-gradient(180deg, #fff1f2 0%, #fee2e2 100%) !important;
        border-color: rgba(239, 68, 68, .22) !important;
    }

    body.sf-archived-job-openings-page .fi-btn-color-danger:hover,
    body.sf-archived-job-openings-page button[wire\:click*="delete"]:hover,
    body.sf-archived-job-openings-page button[title*="Delete"]:hover,
    body.sf-archived-job-openings-page button[aria-label*="Delete"]:hover {
        color: #b91c1c !important;
        border-color: rgba(239, 68, 68, .36) !important;
        box-shadow: 0 18px 34px rgba(239, 68, 68, .14) !important;
    }

    /*
     | Add clean tooltip from aria-label/title.
     */
    body.sf-archived-job-openings-page .fi-btn {
        position: relative !important;
    }

    body.sf-archived-job-openings-page .fi-btn::after {
        content: attr(aria-label);
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%) translateY(4px);
        opacity: 0;
        pointer-events: none;
        white-space: nowrap;
        padding: 7px 10px;
        border-radius: 999px;
        background: #0f172a;
        color: #ffffff;
        font-size: 11px;
        font-weight: 850;
        letter-spacing: .02em;
        box-shadow: 0 14px 28px rgba(15,23,42,.18);
        transition: opacity .16s ease, transform .16s ease;
        z-index: 50;
    }

    body.sf-archived-job-openings-page .fi-btn:hover::after {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .dark body.sf-archived-job-openings-page .fi-ta-header-toolbar .fi-btn,
    .dark body.sf-archived-job-openings-page .fi-ta-toolbar .fi-btn,
    .dark body.sf-archived-job-openings-page .fi-ta-actions .fi-btn,
    .dark body.sf-archived-job-openings-page .fi-ta-header .fi-actions .fi-btn,
    .dark body.sf-archived-job-openings-page .fi-ac .fi-btn {
        background:
            radial-gradient(circle at 30% 20%, rgba(255,255,255,.12), transparent 42%),
            linear-gradient(180deg, rgba(30,41,59,.88), rgba(15,23,42,.84)) !important;
        border-color: rgba(148,163,184,.18) !important;
        color: #cbd5e1 !important;
    }

    .dark body.sf-archived-job-openings-page .fi-btn-color-success {
        color: #86efac !important;
        background: rgba(6,78,59,.38) !important;
        border-color: rgba(52,211,153,.25) !important;
    }

    .dark body.sf-archived-job-openings-page .fi-btn-color-danger {
        color: #fecaca !important;
        background: rgba(127,29,29,.38) !important;
        border-color: rgba(248,113,113,.25) !important;
    }
</style>

<script id="sf-archive-table-icon-actions-labels-final">
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.location.pathname.includes('/admin/archived-job-openings')) {
            return;
        }

        document.body.classList.add('sf-archived-job-openings-page');

        const polishArchiveButtons = function () {
            document.querySelectorAll('button, a').forEach(function (el) {
                const text = (el.textContent || '').trim();

                if (text.includes('Restore Selected')) {
                    el.setAttribute('aria-label', 'Restore Selected');
                    el.setAttribute('title', 'Restore Selected');
                }

                if (text.includes('Permanent Delete')) {
                    el.setAttribute('aria-label', 'Permanent Delete');
                    el.setAttribute('title', 'Permanent Delete');
                }
            });
        };

        polishArchiveButtons();

        new MutationObserver(polishArchiveButtons).observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>


<style id="sf-job-openings-icon-actions-final">
    .job-premium-table-shell .fi-ta-actions,
    .job-premium-table-shell .fi-ta-actions-cell,
    .job-premium-table-shell td:last-child {
        text-align: right !important;
    }

    .job-premium-table-shell .fi-ta-actions {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
    }

    .job-premium-table-shell .sf-job-icon-action,
    .job-premium-table-shell .sf-job-icon-action.fi-btn {
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        padding: 0 !important;
        border-radius: 16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #f8fafc !important;
        border: 1px solid rgba(148, 163, 184, .22) !important;
        color: #64748b !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .06) !important;
        transition: .18s ease !important;
    }

    .job-premium-table-shell .sf-job-icon-action:hover {
        transform: translateY(-1px) !important;
        background: #eef6ff !important;
        color: #1d4ed8 !important;
        box-shadow: 0 16px 34px rgba(15, 23, 42, .12) !important;
    }

    .job-premium-table-shell .sf-job-icon-archive:hover {
        background: #fff7ed !important;
        color: #c2410c !important;
        border-color: rgba(249, 115, 22, .28) !important;
    }

    .job-premium-table-shell .sf-job-icon-action .fi-btn-label {
        display: none !important;
    }

    .job-premium-table-shell .sf-job-icon-action svg {
        width: 22px !important;
        height: 22px !important;
    }

    .dark .job-premium-table-shell .sf-job-icon-action,
    .dark .job-premium-table-shell .sf-job-icon-action.fi-btn {
        background: rgba(255, 255, 255, .06) !important;
        border-color: rgba(148, 163, 184, .18) !important;
        color: #cbd5e1 !important;
    }

    .dark .job-premium-table-shell .sf-job-icon-action:hover {
        background: rgba(37, 99, 235, .18) !important;
        color: #bfdbfe !important;
    }
</style>


<style id="sf-job-openings-table-icon-actions-final">
    /*
     | Job Openings table actions
     | Premium icon-only style.
     */

    .job-premium-table-shell table thead th:last-child,
    .job-premium-table-shell table tbody td:last-child {
        text-align: right !important;
        width: 190px !important;
        min-width: 190px !important;
    }

    .job-premium-table-shell .fi-ta-actions,
    .job-premium-table-shell .fi-ta-actions-cell {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        width: 100% !important;
    }

    .job-premium-table-shell .sf-job-table-icon-action,
    .job-premium-table-shell .sf-job-table-icon-action.fi-btn,
    .job-premium-table-shell a.sf-job-table-icon-action,
    .job-premium-table-shell button.sf-job-table-icon-action {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        max-width: 48px !important;
        padding: 0 !important;
        border-radius: 17px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(248, 250, 252, .96) !important;
        border: 1px solid rgba(148, 163, 184, .22) !important;
        color: #64748b !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .06) !important;
        transition: transform .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease, border-color .18s ease !important;
    }

    .job-premium-table-shell .sf-job-table-icon-action:hover {
        transform: translateY(-1px) !important;
        background: #eef6ff !important;
        color: #1d4ed8 !important;
        border-color: rgba(37, 99, 235, .24) !important;
        box-shadow: 0 16px 34px rgba(37, 99, 235, .12) !important;
    }

    .job-premium-table-shell .sf-job-table-view-action:hover {
        background: #eef6ff !important;
        color: #1d4ed8 !important;
    }

    .job-premium-table-shell .sf-job-table-edit-action:hover {
        background: #fefce8 !important;
        color: #a16207 !important;
        border-color: rgba(234, 179, 8, .34) !important;
        box-shadow: 0 16px 34px rgba(234, 179, 8, .14) !important;
    }

    .job-premium-table-shell .sf-job-table-archive-action:hover {
        background: #fff7ed !important;
        color: #c2410c !important;
        border-color: rgba(249, 115, 22, .34) !important;
        box-shadow: 0 16px 34px rgba(249, 115, 22, .14) !important;
    }

    .job-premium-table-shell .sf-job-table-icon-action .fi-btn-label,
    .job-premium-table-shell .sf-job-table-icon-action span.fi-btn-label,
    .job-premium-table-shell .sf-job-table-icon-action span:not(.fi-btn-icon):not(.fi-icon):not(.sr-only) {
        display: none !important;
    }

    .job-premium-table-shell .sf-job-table-icon-action svg {
        width: 22px !important;
        height: 22px !important;
        min-width: 22px !important;
        stroke-width: 2.15 !important;
    }

    .job-premium-table-shell .fi-ta-header-cell:last-child,
    .job-premium-table-shell th:last-child {
        color: #1f4664 !important;
        font-weight: 950 !important;
    }

    .dark .job-premium-table-shell .sf-job-table-icon-action,
    .dark .job-premium-table-shell .sf-job-table-icon-action.fi-btn {
        background: rgba(255, 255, 255, .06) !important;
        border-color: rgba(148, 163, 184, .18) !important;
        color: #cbd5e1 !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .22) !important;
    }

    .dark .job-premium-table-shell .sf-job-table-icon-action:hover {
        background: rgba(37, 99, 235, .18) !important;
        color: #bfdbfe !important;
    }

    @media (max-width: 900px) {
        .job-premium-table-shell table thead th:last-child,
        .job-premium-table-shell table tbody td:last-child {
            min-width: 170px !important;
            width: 170px !important;
        }

        .job-premium-table-shell .sf-job-table-icon-action,
        .job-premium-table-shell .sf-job-table-icon-action.fi-btn {
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            max-width: 44px !important;
        }
    }
</style>

</x-filament-panels::page>

<style id="sf-job-openings-actions-final-polish">
    /*
     | Sada Fezzan ERP - Job Openings Actions Column
     | Clean final version:
     | - one Actions header
     | - same header height/background as other columns
     | - action icons horizontal
     | - premium square icon buttons
     | - no broken stretching / no duplicated CSS
     */

    .job-table-shell table,
    .job-premium-table-shell table {
        table-layout: auto !important;
        width: 100% !important;
    }

    .job-table-shell table thead th,
    .job-premium-table-shell table thead th {
        height: 58px !important;
        vertical-align: middle !important;
        background: #eef5f8 !important;
        color: #1f4664 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
        border-color: #d7e2e5 !important;
    }

    .job-table-shell table tbody td,
    .job-premium-table-shell table tbody td {
        height: 72px !important;
        vertical-align: middle !important;
    }

    .job-table-shell table thead th:last-child,
    .job-premium-table-shell table thead th:last-child {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        text-align: center !important;
        padding: 0 18px !important;
        position: relative !important;
    }

    .job-table-shell table thead th:last-child > *,
    .job-premium-table-shell table thead th:last-child > * {
        display: none !important;
    }

    .job-table-shell table thead th:last-child::before,
    .job-premium-table-shell table thead th:last-child::before {
        content: "ACTIONS" !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 58px !important;
        color: #1f4664 !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        line-height: 1 !important;
    }

    .job-table-shell table tbody td:last-child,
    .job-premium-table-shell table tbody td:last-child {
        width: 150px !important;
        min-width: 150px !important;
        max-width: 150px !important;
        text-align: center !important;
        padding: 0 18px !important;
        vertical-align: middle !important;
    }

    .job-table-shell table tbody td:last-child > *,
    .job-premium-table-shell table tbody td:last-child > *,
    .job-table-shell .fi-ta-actions,
    .job-table-shell .fi-ta-record-actions,
    .job-premium-table-shell .fi-ta-actions,
    .job-premium-table-shell .fi-ta-record-actions {
        display: inline-flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 12px !important;
        width: auto !important;
        margin: 0 auto !important;
        white-space: nowrap !important;
    }

    .job-table-shell .fi-ta-actions > *,
    .job-table-shell .fi-ta-record-actions > *,
    .job-premium-table-shell .fi-ta-actions > *,
    .job-premium-table-shell .fi-ta-record-actions > * {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
    }

    .job-table-shell .fi-ta-actions .fi-btn,
    .job-table-shell .fi-ta-record-actions .fi-btn,
    .job-table-shell .sf-job-row-action,
    .job-premium-table-shell .fi-ta-actions .fi-btn,
    .job-premium-table-shell .fi-ta-record-actions .fi-btn,
    .job-premium-table-shell .sf-job-row-action {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        min-height: 48px !important;
        max-width: 48px !important;
        max-height: 48px !important;
        padding: 0 !important;
        border-radius: 17px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .08) !important;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease !important;
    }

    .job-table-shell .fi-ta-actions .fi-btn:hover,
    .job-table-shell .fi-ta-record-actions .fi-btn:hover,
    .job-table-shell .sf-job-row-action:hover,
    .job-premium-table-shell .fi-ta-actions .fi-btn:hover,
    .job-premium-table-shell .fi-ta-record-actions .fi-btn:hover,
    .job-premium-table-shell .sf-job-row-action:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 14px 28px rgba(15, 23, 42, .13) !important;
    }

    .job-table-shell .sf-job-row-action-edit,
    .job-premium-table-shell .sf-job-row-action-edit {
        background: #f2b705 !important;
        color: #3b2a00 !important;
        border: 1px solid rgba(179, 139, 47, .28) !important;
    }

    .job-table-shell .sf-job-row-action-archive,
    .job-premium-table-shell .sf-job-row-action-archive {
        background: #f8fbfd !important;
        color: #64748b !important;
        border: 1px solid #d7e2e5 !important;
    }

    .job-table-shell .fi-ta-actions .fi-btn svg,
    .job-table-shell .fi-ta-record-actions .fi-btn svg,
    .job-table-shell .sf-job-row-action svg,
    .job-premium-table-shell .fi-ta-actions .fi-btn svg,
    .job-premium-table-shell .fi-ta-record-actions .fi-btn svg,
    .job-premium-table-shell .sf-job-row-action svg {
        width: 25px !important;
        height: 25px !important;
        min-width: 25px !important;
        min-height: 25px !important;
        stroke-width: 2.35 !important;
        display: block !important;
    }

    .job-table-shell .fi-ta-actions .fi-btn-label,
    .job-table-shell .fi-ta-record-actions .fi-btn-label,
    .job-table-shell .sf-job-row-action .fi-btn-label,
    .job-premium-table-shell .fi-ta-actions .fi-btn-label,
    .job-premium-table-shell .fi-ta-record-actions .fi-btn-label,
    .job-premium-table-shell .sf-job-row-action .fi-btn-label {
        display: none !important;
    }

    .dark .job-table-shell table thead th,
    .dark .job-premium-table-shell table thead th {
        background: rgba(15, 23, 42, .88) !important;
        color: #e0f2fe !important;
        border-color: rgba(148, 163, 184, .18) !important;
    }

    .dark .job-table-shell table thead th:last-child::before,
    .dark .job-premium-table-shell table thead th:last-child::before {
        color: #e0f2fe !important;
    }

    .dark .job-table-shell .sf-job-row-action-archive,
    .dark .job-premium-table-shell .sf-job-row-action-archive {
        background: rgba(15, 23, 42, .72) !important;
        color: #cbd5e1 !important;
        border-color: rgba(148, 163, 184, .20) !important;
    }
</style>

<style id="sf-job-actions-hide-loading-spinner-final">
    /*
     | Hide Filament loading spinner / duplicate indicator inside Job Opening action buttons.
     | Keep only the real action SVG icon visible.
     */

    .job-table-shell .fi-ta-actions .fi-btn .fi-loading-indicator,
    .job-table-shell .fi-ta-record-actions .fi-btn .fi-loading-indicator,
    .job-premium-table-shell .fi-ta-actions .fi-btn .fi-loading-indicator,
    .job-premium-table-shell .fi-ta-record-actions .fi-btn .fi-loading-indicator,
    .job-table-shell .sf-job-row-action .fi-loading-indicator,
    .job-premium-table-shell .sf-job-row-action .fi-loading-indicator,
    .job-table-shell .sf-job-row-action [wire\:loading],
    .job-premium-table-shell .sf-job-row-action [wire\:loading],
    .job-table-shell .sf-job-row-action [class*="loading"],
    .job-premium-table-shell .sf-job-row-action [class*="loading"],
    .job-table-shell .sf-job-row-action [class*="spinner"],
    .job-premium-table-shell .sf-job-row-action [class*="spinner"] {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        min-width: 0 !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        pointer-events: none !important;
    }

    .job-table-shell .sf-job-row-action,
    .job-premium-table-shell .sf-job-row-action {
        overflow: hidden !important;
    }

    .job-table-shell .sf-job-row-action > *,
    .job-premium-table-shell .sf-job-row-action > * {
        flex-shrink: 0 !important;
    }

    .job-table-shell .sf-job-row-action svg:not(.fi-loading-indicator),
    .job-premium-table-shell .sf-job-row-action svg:not(.fi-loading-indicator) {
        opacity: 1 !important;
        visibility: visible !important;
    }
</style>

