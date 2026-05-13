<x-filament-panels::page>
    @php
        $record = $this->record ?? null;

        $templateName = $record?->name
            ?? $record?->template_name
            ?? 'Template';

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
            $indexUrl = $resourceClass ? $resourceClass::getUrl('index') : url('/admin/job-application-templates');
        } catch (\Throwable $e) {
            $indexUrl = url('/admin/job-application-templates');
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .tpl-form-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .tpl-form-hero {
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

        .tpl-form-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .tpl-form-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .tpl-form-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .tpl-form-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .tpl-form-subtitle {
            margin-top: 16px;
            max-width: 860px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .tpl-form-badge {
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

        .tpl-form-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .tpl-back-btn {
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

        .tpl-back-btn:hover {
            transform: translateY(-1px);
        }

        .tpl-form-shell {
            border-radius: 30px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
            padding: 0;
            overflow: visible !important;
        }

        .tpl-form-shell .fi-section {
            border-radius: 24px !important;
            border: 1px solid #d7e2e5 !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            box-shadow: 0 10px 22px rgba(15,23,42,.05) !important;
            overflow: visible !important;
        }

        .tpl-form-shell .fi-section-header {
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%) !important;
            border-bottom: 1px solid #e4ecef !important;
            padding: 18px 22px !important;
        }

        .tpl-form-shell .fi-section-header-heading,
        .tpl-form-shell .fi-section h2,
        .tpl-form-shell .fi-section h3 {
            color: #0f172a !important;
            font-weight: 950 !important;
            letter-spacing: -.02em !important;
        }

        .tpl-form-shell .fi-section-content {
            background: transparent !important;
            padding: 22px !important;
        }

        .tpl-form-shell .fi-input-wrp,
        .tpl-form-shell .fi-select,
        .tpl-form-shell .fi-textarea,
        .tpl-form-shell input,
        .tpl-form-shell select,
        .tpl-form-shell textarea {
            border-radius: 16px !important;
        }

        .tpl-form-shell .fi-input-wrp,
        .tpl-form-shell .fi-select,
        .tpl-form-shell .fi-textarea {
            background: #ffffff !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .tpl-form-shell input,
        .tpl-form-shell textarea,
        .tpl-form-shell select {
            color: #0f172a !important;
        }

        .tpl-form-shell label,
        .tpl-form-shell .fi-fo-field-wrp-label span {
            color: #334155 !important;
            font-weight: 800 !important;
        }

        .tpl-form-shell .fi-fo-field-wrp-hint {
            color: #64748b !important;
        }

        /* Checkboxes as premium compact boxes */
        .tpl-form-shell input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #e17a00 !important;
        }

        .tpl-form-shell .fi-checkbox-input {
            border-radius: 6px !important;
        }

        .tpl-form-shell .fi-fo-checkbox-list-option-label,
        .tpl-form-shell .fi-checkbox-list-option-label,
        .tpl-form-shell label {
            font-weight: 750 !important;
        }

        .tpl-form-shell .fi-btn {
            border-radius: 999px !important;
            min-height: 42px !important;
            padding-inline: 18px !important;
            font-weight: 950 !important;
        }

        .tpl-form-shell .fi-btn-color-primary,
        .tpl-form-shell .fi-btn-color-warning {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 0 !important;
            box-shadow: 0 10px 22px rgba(242,183,5,.20) !important;
        }

        .tpl-form-shell .fi-btn-color-gray {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #d7e2e5 !important;
        }

        .tpl-form-shell .fi-btn-color-danger {
            background: #ef4444 !important;
            color: #fff !important;
            border: 0 !important;
            box-shadow: 0 10px 22px rgba(239,68,68,.18) !important;
        }

        /* Dropdown / select panels */
        .tpl-form-shell .fi-dropdown-panel,
        .tpl-form-shell .choices__list--dropdown,
        .tpl-form-shell [role="listbox"] {
            border-radius: 18px !important;
            border: 1px solid #d7e2e5 !important;
            background: #ffffff !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .20) !important;
            z-index: 9999 !important;
            overflow: hidden !important;
        }

        .tpl-form-shell [role="listbox"] {
            max-height: 360px !important;
            overflow-y: auto !important;
        }

        .dark .tpl-form-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .tpl-form-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .tpl-form-shell .fi-section {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .tpl-form-shell .fi-section-header {
            background: rgba(15,23,42,.92) !important;
            border-bottom-color: rgba(76,167,168,.16) !important;
        }

        .dark .tpl-form-shell .fi-section-header-heading,
        .dark .tpl-form-shell .fi-section h2,
        .dark .tpl-form-shell .fi-section h3 {
            color: #f8fafc !important;
        }

        .dark .tpl-form-shell .fi-input-wrp,
        .dark .tpl-form-shell .fi-select,
        .dark .tpl-form-shell .fi-textarea {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .tpl-form-shell input,
        .dark .tpl-form-shell textarea,
        .dark .tpl-form-shell select {
            color: #f8fafc !important;
        }

        .dark .tpl-form-shell label,
        .dark .tpl-form-shell .fi-fo-field-wrp-label span,
        .dark .tpl-form-shell .fi-fo-checkbox-list-option-label,
        .dark .tpl-form-shell .fi-checkbox-list-option-label {
            color: #dbeafe !important;
        }

        .dark .tpl-form-shell .fi-fo-field-wrp-hint {
            color: #aab8c6 !important;
        }

        .dark .tpl-form-shell .fi-btn-color-gray {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .tpl-form-shell .fi-dropdown-panel,
        .dark .tpl-form-shell .choices__list--dropdown,
        .dark .tpl-form-shell [role="listbox"] {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
            color: #f8fafc !important;
        }

        /* SADA TEMPLATE FORM ACTIONS BOTTOM FIX */
        .tpl-form-actions-bottom {
            padding: 0 22px 24px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        .tpl-save-btn,
        .tpl-cancel-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 42px !important;
            padding: 0 18px !important;
            border-radius: 999px !important;
            font-size: 13px !important;
            font-weight: 950 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: .18s ease !important;
        }

        .tpl-save-btn {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 0 !important;
            box-shadow: 0 10px 22px rgba(242,183,5,.20) !important;
        }

        .tpl-cancel-btn {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .tpl-save-btn:hover,
        .tpl-cancel-btn:hover {
            transform: translateY(-1px) !important;
        }

        .dark .tpl-cancel-btn {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.18) !important;
        }


        @media (max-width: 900px) {
            .tpl-form-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="tpl-form-wrap">
        <section class="tpl-form-hero">
            <div class="tpl-form-hero-inner">
                <div>
                    <div class="tpl-form-breadcrumb">Admin Settings › Templates › Edit</div>
                    <div class="tpl-form-title">Edit Template</div>
                    <div class="tpl-form-subtitle">
                        Update the application template details and control which fields appear in the public job application form.
                    </div>

                    <div class="tpl-form-badge">{{ $templateName }}</div>
                </div>

                <div class="tpl-form-actions">
                    <a href="{{ $indexUrl }}" class="tpl-back-btn">Back to Templates</a>
                </div>
            </div>
        </section>

        <section class="tpl-form-shell">
            {{ $this->form }}

            <div class="tpl-form-actions-bottom">
                <button type="button" wire:click="save" class="tpl-save-btn">
                    Save changes
                </button>

                <a href="{{ $indexUrl }}" class="tpl-cancel-btn">
                    Cancel
                </a>
            </div>

            <x-filament-actions::modals />


        </section>
    </div>
</x-filament-panels::page>
