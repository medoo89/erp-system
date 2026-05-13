<x-filament-panels::page>
    @php
        $resourceClass = \App\Filament\Resources\PreEmployments\PreEmploymentResource::class;

        try {
            $indexUrl = $resourceClass::getUrl('index');
        } catch (\Throwable $e) {
            $indexUrl = url('/admin/pre-employments');
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .pe-form-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .pe-form-hero {
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

        .pe-form-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .pe-form-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .pe-form-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .pe-form-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .pe-form-subtitle {
            margin-top: 16px;
            max-width: 860px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .pe-form-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pe-back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            color: #fff !important;
            border: 1px solid rgba(255,255,255,.16);
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 950;
            transition: .18s ease;
        }

        .pe-back-btn:hover {
            transform: translateY(-1px);
        }

        .pe-form-shell {
            border-radius: 30px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
            padding: 0;
            overflow: visible !important;
        }

        .pe-form-shell .fi-section {
            border-radius: 24px !important;
            border: 1px solid #d7e2e5 !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            box-shadow: 0 10px 22px rgba(15,23,42,.05) !important;
            overflow: visible !important;
        }

        .pe-form-shell .fi-section-header {
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%) !important;
            border-bottom: 1px solid #e4ecef !important;
            padding: 18px 22px !important;
        }

        .pe-form-shell .fi-section-header-heading,
        .pe-form-shell .fi-section h2,
        .pe-form-shell .fi-section h3 {
            color: #0f172a !important;
            font-weight: 950 !important;
            letter-spacing: -.02em !important;
        }

        .pe-form-shell .fi-section-content {
            background: transparent !important;
            padding: 22px !important;
        }

        .pe-form-shell .fi-input-wrp,
        .pe-form-shell .fi-select,
        .pe-form-shell .fi-textarea,
        .pe-form-shell .fi-fo-file-upload,
        .pe-form-shell input,
        .pe-form-shell select,
        .pe-form-shell textarea {
            border-radius: 16px !important;
        }

        .pe-form-shell .fi-input-wrp,
        .pe-form-shell .fi-select,
        .pe-form-shell .fi-textarea {
            background: #ffffff !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .pe-form-shell input,
        .pe-form-shell textarea,
        .pe-form-shell select {
            color: #0f172a !important;
        }

        .pe-form-shell label,
        .pe-form-shell .fi-fo-field-wrp-label span {
            color: #334155 !important;
            font-weight: 800 !important;
        }

        .pe-form-shell .fi-fo-field-wrp-hint {
            color: #64748b !important;
        }

        .pe-form-shell .fi-fo-file-upload,
        .pe-form-shell .filepond--root,
        .pe-form-shell .filepond--panel-root {
            border-radius: 18px !important;
        }

        .pe-form-shell .filepond--panel-root {
            background: #ffffff !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .pe-form-shell .fi-btn {
            border-radius: 999px !important;
            min-height: 42px !important;
            padding-inline: 18px !important;
            font-weight: 950 !important;
        }

        .pe-form-shell .fi-btn-color-primary,
        .pe-form-shell .fi-btn-color-warning {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 0 !important;
            box-shadow: 0 10px 22px rgba(242,183,5,.20) !important;
        }

        .pe-form-shell .fi-btn-color-gray {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #d7e2e5 !important;
        }

        /* Select/dropdown panels */
        .pe-form-shell .fi-dropdown-panel,
        .pe-form-shell .choices__list--dropdown,
        .pe-form-shell [role="listbox"] {
            border-radius: 18px !important;
            border: 1px solid #d7e2e5 !important;
            background: #ffffff !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .20) !important;
            z-index: 9999 !important;
            overflow: hidden !important;
        }

        .pe-form-shell [role="listbox"] {
            max-height: 360px !important;
            overflow-y: auto !important;
        }

        .dark .pe-form-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .pe-form-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .pe-form-shell .fi-section {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .pe-form-shell .fi-section-header {
            background: rgba(15,23,42,.92) !important;
            border-bottom-color: rgba(76,167,168,.16) !important;
        }

        .dark .pe-form-shell .fi-section-header-heading,
        .dark .pe-form-shell .fi-section h2,
        .dark .pe-form-shell .fi-section h3 {
            color: #f8fafc !important;
        }

        .dark .pe-form-shell .fi-input-wrp,
        .dark .pe-form-shell .fi-select,
        .dark .pe-form-shell .fi-textarea {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .pe-form-shell input,
        .dark .pe-form-shell textarea,
        .dark .pe-form-shell select {
            color: #f8fafc !important;
        }

        .dark .pe-form-shell label,
        .dark .pe-form-shell .fi-fo-field-wrp-label span {
            color: #dbeafe !important;
        }

        .dark .pe-form-shell .fi-fo-field-wrp-hint {
            color: #aab8c6 !important;
        }

        .dark .pe-form-shell .filepond--panel-root {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.20) !important;
        }

        .dark .pe-form-shell .fi-btn-color-gray {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .pe-form-shell .fi-dropdown-panel,
        .dark .pe-form-shell .choices__list--dropdown,
        .dark .pe-form-shell [role="listbox"] {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
            color: #f8fafc !important;
        }

        @media (max-width: 900px) {
            .pe-form-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="pe-form-wrap">
        <section class="pe-form-hero">
            <div class="pe-form-hero-inner">
                <div>
                    <div class="pe-form-breadcrumb">Recruitment › Pre-Employment › Create</div>
                    <div class="pe-form-title">Create Pre-Employment</div>
                    <div class="pe-form-subtitle">
                        Register and control the pre-employment process, client tracking, candidate status, documents, and operation officer assignment.
                    </div>
                </div>

                <div class="pe-form-actions">
                    <a href="{{ $indexUrl }}" class="pe-back-btn">Back to Pre-Employment</a>
                </div>
            </div>
        </section>

        <section class="pe-form-shell">
            {{ $this->form }}

            <div style="padding: 0 22px 22px;">
                <x-filament-actions::modals />
            </div>
        </section>
    </div>
</x-filament-panels::page>
