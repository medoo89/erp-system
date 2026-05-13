<x-filament-panels::page>
    @php
        $record = $this->record ?? null;

        $fieldName = $record?->label
            ?? $record?->field_label
            ?? $record?->name
            ?? 'Application Field';

        $fieldKey = $record?->field_key
            ?? $record?->key
            ?? null;

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
            $indexUrl = $resourceClass ? $resourceClass::getUrl('index') : url('/admin/job-application-fields');
        } catch (\Throwable $e) {
            $indexUrl = url('/admin/job-application-fields');
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .af-form-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .af-form-hero {
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

        .af-form-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .af-form-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .af-form-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .af-form-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .af-form-subtitle {
            margin-top: 16px;
            max-width: 860px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .af-form-badge-row {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .af-form-badge {
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

        .af-form-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .af-back-btn {
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

        .af-back-btn:hover {
            transform: translateY(-1px);
        }

        .af-form-shell {
            border-radius: 30px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
            padding: 0;
            overflow: visible !important;
        }

        .af-form-shell .fi-section {
            border-radius: 24px !important;
            border: 1px solid #d7e2e5 !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            box-shadow: 0 10px 22px rgba(15,23,42,.05) !important;
            overflow: visible !important;
        }

        .af-form-shell .fi-section-header {
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%) !important;
            border-bottom: 1px solid #e4ecef !important;
            padding: 18px 22px !important;
        }

        .af-form-shell .fi-section-header-heading,
        .af-form-shell .fi-section h2,
        .af-form-shell .fi-section h3 {
            color: #0f172a !important;
            font-weight: 950 !important;
            letter-spacing: -.02em !important;
        }

        .af-form-shell .fi-section-content {
            background: transparent !important;
            padding: 22px !important;
        }

        .af-form-shell .fi-input-wrp,
        .af-form-shell .fi-select,
        .af-form-shell .fi-textarea,
        .af-form-shell input,
        .af-form-shell select,
        .af-form-shell textarea {
            border-radius: 16px !important;
        }

        .af-form-shell .fi-input-wrp,
        .af-form-shell .fi-select,
        .af-form-shell .fi-textarea {
            background: #ffffff !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .af-form-shell input,
        .af-form-shell textarea,
        .af-form-shell select {
            color: #0f172a !important;
        }

        .af-form-shell label,
        .af-form-shell .fi-fo-field-wrp-label span {
            color: #334155 !important;
            font-weight: 800 !important;
        }

        .af-form-shell .fi-fo-field-wrp-hint {
            color: #64748b !important;
        }

        .af-form-shell input[type="checkbox"] {
            opacity: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
            width: 16px !important;
            height: 16px !important;
            accent-color: #e17a00 !important;
        }

        .af-form-shell .fi-toggle,
        .af-form-shell [role="switch"] {
            cursor: pointer !important;
        }

        .af-form-shell .fi-btn {
            border-radius: 999px !important;
            min-height: 42px !important;
            padding-inline: 18px !important;
            font-weight: 950 !important;
        }

        .af-form-shell .fi-btn-color-primary,
        .af-form-shell .fi-btn-color-warning {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 0 !important;
            box-shadow: 0 10px 22px rgba(242,183,5,.20) !important;
        }

        .af-form-shell .fi-btn-color-gray {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #d7e2e5 !important;
        }

        .af-form-shell .fi-btn-color-danger {
            background: #ef4444 !important;
            color: #fff !important;
            border: 0 !important;
            box-shadow: 0 10px 22px rgba(239,68,68,.18) !important;
        }

        /* Make top delete action premium */
        .af-form-wrap .fi-ac,
        .af-form-wrap .fi-header-actions {
            display: flex !important;
            gap: 10px !important;
        }

        /* Dropdown / select panels */
        .af-form-shell .fi-dropdown-panel,
        .af-form-shell .choices__list--dropdown,
        .af-form-shell [role="listbox"] {
            border-radius: 18px !important;
            border: 1px solid #d7e2e5 !important;
            background: #ffffff !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .20) !important;
            z-index: 9999 !important;
            overflow: hidden !important;
        }

        .af-form-shell [role="listbox"] {
            max-height: 360px !important;
            overflow-y: auto !important;
        }

        .dark .af-form-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .af-form-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .af-form-shell .fi-section {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .af-form-shell .fi-section-header {
            background: rgba(15,23,42,.92) !important;
            border-bottom-color: rgba(76,167,168,.16) !important;
        }

        .dark .af-form-shell .fi-section-header-heading,
        .dark .af-form-shell .fi-section h2,
        .dark .af-form-shell .fi-section h3 {
            color: #f8fafc !important;
        }

        .dark .af-form-shell .fi-input-wrp,
        .dark .af-form-shell .fi-select,
        .dark .af-form-shell .fi-textarea {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .af-form-shell input,
        .dark .af-form-shell textarea,
        .dark .af-form-shell select {
            color: #f8fafc !important;
        }

        .dark .af-form-shell label,
        .dark .af-form-shell .fi-fo-field-wrp-label span {
            color: #dbeafe !important;
        }

        .dark .af-form-shell .fi-fo-field-wrp-hint {
            color: #aab8c6 !important;
        }

        .dark .af-form-shell .fi-btn-color-gray {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .af-form-shell .fi-dropdown-panel,
        .dark .af-form-shell .choices__list--dropdown,
        .dark .af-form-shell [role="listbox"] {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
            color: #f8fafc !important;
        }


        /* SADA APPLICATION FIELD ACTIONS */
        .af-form-wrap {
            max-width: 1180px !important;
        }

        .af-form-shell {
            overflow: hidden !important;
            background:
                radial-gradient(circle at 90% 12%, rgba(76,167,168,.10), transparent 28%),
                linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
        }

        .af-form-shell form {
            display: block !important;
        }

        .af-form-shell .fi-section {
            max-width: 760px !important;
            margin: 0 !important;
            border-radius: 26px !important;
        }

        .af-form-shell .fi-section-content {
            padding: 24px !important;
        }

        .af-form-shell .fi-grid {
            gap: 18px 22px !important;
        }

        .af-form-shell .fi-input-wrp,
        .af-form-shell .fi-select,
        .af-form-shell .fi-textarea {
            min-height: 46px !important;
            border-radius: 18px !important;
            background: rgba(255,255,255,.96) !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 10px 22px rgba(15,23,42,.045) !important;
        }

        .af-form-shell textarea {
            min-height: 108px !important;
        }

        .af-form-actions-bottom {
            padding: 0 24px 26px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        .af-save-btn,
        .af-cancel-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 44px !important;
            padding: 0 20px !important;
            border-radius: 999px !important;
            font-size: 13px !important;
            font-weight: 950 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            transition: .18s ease !important;
        }

        .af-save-btn {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 0 !important;
            box-shadow: 0 12px 24px rgba(242,183,5,.22) !important;
        }

        .af-cancel-btn {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.04) !important;
        }

        .af-save-btn:hover,
        .af-cancel-btn:hover {
            transform: translateY(-1px) !important;
        }

        .dark .af-form-shell {
            background:
                radial-gradient(circle at 90% 12%, rgba(76,167,168,.10), transparent 28%),
                linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
        }

        .dark .af-form-shell .fi-input-wrp,
        .dark .af-form-shell .fi-select,
        .dark .af-form-shell .fi-textarea {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 10px 22px rgba(0,0,0,.24) !important;
        }

        .dark .af-cancel-btn {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        /* SADA APPLICATION FIELD CENTERED LAYOUT FIX */
        .af-form-wrap {
            max-width: 1180px !important;
        }

        .af-form-shell {
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.09), transparent 34%),
                linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
        }

        .af-form-shell form {
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }

        .af-form-shell .fi-section {
            width: min(100%, 760px) !important;
            max-width: 760px !important;
            margin: 0 auto !important;
            border-radius: 28px !important;
            overflow: hidden !important;
        }

        .af-form-shell .fi-section-header {
            text-align: center !important;
            justify-content: center !important;
        }

        .af-form-shell .fi-section-header-heading,
        .af-form-shell .fi-section h2,
        .af-form-shell .fi-section h3 {
            width: 100% !important;
            text-align: center !important;
        }

        .af-form-shell .fi-section-content {
            padding: 28px !important;
        }

        .af-form-shell .fi-grid {
            gap: 20px 24px !important;
        }

        .af-form-shell .fi-input-wrp,
        .af-form-shell .fi-select,
        .af-form-shell .fi-textarea {
            min-height: 48px !important;
            border-radius: 18px !important;
        }

        .af-form-actions-bottom {
            width: min(100%, 760px) !important;
            max-width: 760px !important;
            margin: 0 auto !important;
            padding: 0 28px 28px !important;
            justify-content: flex-start !important;
        }

        .af-form-badge-row {
            gap: 10px !important;
        }

        .af-form-badge {
            text-transform: none !important;
            letter-spacing: .04em !important;
        }

        .dark .af-form-shell {
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.10), transparent 34%),
                linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
        }

        /* SADA APPLICATION FIELD TRUE CENTER CARD FIX */
        .af-form-shell {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 26px 24px !important;
            min-height: 520px !important;
        }

        .af-form-shell form {
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .af-form-shell .fi-section {
            width: min(100%, 720px) !important;
            max-width: 720px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            align-self: center !important;
        }

        .af-form-actions-bottom {
            width: min(100%, 720px) !important;
            max-width: 720px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding: 18px 0 0 !important;
            justify-content: center !important;
        }


        @media (max-width: 900px) {
            .af-form-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="af-form-wrap">
        <section class="af-form-hero">
            <div class="af-form-hero-inner">
                <div>
                    <div class="af-form-breadcrumb">Admin Settings › Application Fields › Edit</div>
                    <div class="af-form-title">Edit Application Field</div>
                    <div class="af-form-subtitle">
                        Update public application field configuration, validation behavior, grouping, visibility, and display order.
                    </div>
                    @if($fieldKey)
                        <div class="af-form-badge-row">
                            <div class="af-form-badge">Field Key: {{ $fieldKey }}</div>
                        </div>
                    @endif

                </div>

                <div class="af-form-actions">
                    <a href="{{ $indexUrl }}" class="af-back-btn">Back to Fields</a>
                </div>
            </div>
        </section>

        <section class="af-form-shell">
            {{ $this->form }}

            <div class="af-form-actions-bottom">
                <button type="button" wire:click="save" class="af-save-btn">
                    Save changes
                </button>

                <a href="{{ $indexUrl }}" class="af-cancel-btn">
                    Cancel
                </a>
            </div>

            <x-filament-actions::modals />

        </section>
    </div>
</x-filament-panels::page>
