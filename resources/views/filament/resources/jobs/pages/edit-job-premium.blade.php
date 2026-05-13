<x-filament-panels::page>
    @php
        $record = $this->record ?? null;

        $jobTitle = $record?->title
            ?? $record?->job_title
            ?? $record?->position
            ?? $record?->position_title
            ?? 'Job Opening';

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
            $indexUrl = $resourceClass ? $resourceClass::getUrl('index') : url('/admin/jobs');
        } catch (\Throwable $e) {
            $indexUrl = url('/admin/jobs');
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
                    <div class="job-premium-breadcrumb">Recruitment › Job Openings › Edit</div>
                    <div class="job-premium-title">Edit {{ $jobTitle }}</div>
                    <div class="job-premium-subtitle">
                        Update the job opening details, template, client/project connection, content, published status, and expiry date.
                    </div>
                    <div class="job-premium-badges">
                        <div class="job-premium-badge">{{ $jobTitle }}</div>
                    </div>
                </div>
                <a href="{{ $indexUrl }}" class="job-premium-btn">Back to Jobs</a>
            </div>
        </section>

        <section class="job-form-shell">
            {{ $this->form }}

            <div class="job-actions-bottom">
                <button type="button" wire:click="save" class="job-save-btn">Save changes</button>
                <a href="{{ $indexUrl }}" class="job-cancel-btn">Cancel</a>
            </div>

            <x-filament-actions::modals />
        </section>
    </div>
</x-filament-panels::page>
