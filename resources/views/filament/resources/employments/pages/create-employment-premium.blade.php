<x-filament-panels::page>
    @php
        $resourceClass = \App\Filament\Resources\Employments\EmploymentResource::class;

        try {
            $indexUrl = $resourceClass::getUrl('index');
        } catch (\Throwable $e) {
            $indexUrl = url('/admin/employments');
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .emp-form-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .emp-form-hero {
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

        .emp-form-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .emp-form-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .emp-form-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .emp-form-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .emp-form-subtitle {
            margin-top: 16px;
            max-width: 820px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .emp-form-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .emp-back-btn {
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

        .emp-back-btn:hover {
            transform: translateY(-1px);
        }

        .emp-form-shell {
            border-radius: 30px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
            padding: 0;
            overflow: visible !important;
        }

        .emp-form-shell .fi-section {
            border-radius: 24px !important;
            border: 1px solid #d7e2e5 !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            box-shadow: 0 10px 22px rgba(15,23,42,.05) !important;
            overflow: hidden !important;
        }

        .emp-form-shell .fi-section-header {
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%) !important;
            border-bottom: 1px solid #e4ecef !important;
            padding: 18px 22px !important;
        }

        .emp-form-shell .fi-section-header-heading,
        .emp-form-shell .fi-section h2,
        .emp-form-shell .fi-section h3 {
            color: #0f172a !important;
            font-weight: 950 !important;
            letter-spacing: -.02em !important;
        }

        .emp-form-shell .fi-section-content {
            background: transparent !important;
            padding: 22px !important;
        }

        .emp-form-shell .fi-input-wrp,
        .emp-form-shell .fi-select,
        .emp-form-shell .fi-textarea,
        .emp-form-shell .fi-fo-file-upload,
        .emp-form-shell input,
        .emp-form-shell select,
        .emp-form-shell textarea {
            border-radius: 16px !important;
        }

        .emp-form-shell .fi-input-wrp,
        .emp-form-shell .fi-select,
        .emp-form-shell .fi-textarea {
            background: #ffffff !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
        }

        .emp-form-shell input,
        .emp-form-shell textarea,
        .emp-form-shell select {
            color: #0f172a !important;
        }

        .emp-form-shell label,
        .emp-form-shell .fi-fo-field-wrp-label span {
            color: #334155 !important;
            font-weight: 800 !important;
        }

        .emp-form-shell .fi-fo-field-wrp-helper-text {
            color: #64748b !important;
        }

        .emp-form-shell .fi-btn {
            border-radius: 999px !important;
            min-height: 42px !important;
            padding-inline: 18px !important;
            font-weight: 950 !important;
        }

        .emp-form-shell .fi-btn-color-primary,
        .emp-form-shell .fi-btn-color-warning {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            border: 0 !important;
            box-shadow: 0 10px 22px rgba(242,183,5,.20) !important;
        }

        .emp-form-shell .fi-btn-color-gray {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #d7e2e5 !important;
        }

        /* Dropdown / select panels */
        .emp-form-shell .fi-dropdown-panel,
        .emp-form-shell .choices__list--dropdown,
        .emp-form-shell [role="listbox"] {
            border-radius: 18px !important;
            border: 1px solid #d7e2e5 !important;
            background: #ffffff !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .20) !important;
            z-index: 9999 !important;
        }

        .dark .emp-form-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .emp-form-shell {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .emp-form-shell .fi-section {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .emp-form-shell .fi-section-header {
            background: rgba(15,23,42,.92) !important;
            border-bottom-color: rgba(76,167,168,.16) !important;
        }

        .dark .emp-form-shell .fi-section-header-heading,
        .dark .emp-form-shell .fi-section h2,
        .dark .emp-form-shell .fi-section h3 {
            color: #f8fafc !important;
        }

        .dark .emp-form-shell .fi-input-wrp,
        .dark .emp-form-shell .fi-select,
        .dark .emp-form-shell .fi-textarea {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 8px 18px rgba(0,0,0,.22) !important;
        }

        .dark .emp-form-shell input,
        .dark .emp-form-shell textarea,
        .dark .emp-form-shell select {
            color: #f8fafc !important;
        }

        .dark .emp-form-shell label,
        .dark .emp-form-shell .fi-fo-field-wrp-label span {
            color: #dbeafe !important;
        }

        .dark .emp-form-shell .fi-fo-field-wrp-helper-text {
            color: #aab8c6 !important;
        }

        .dark .emp-form-shell .fi-btn-color-gray {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .emp-form-shell .fi-dropdown-panel,
        .dark .emp-form-shell .choices__list--dropdown,
        .dark .emp-form-shell [role="listbox"] {
            background: #0f172a !important;
            border-color: rgba(76,167,168,.20) !important;
            color: #f8fafc !important;
        }

        @media (max-width: 900px) {
            .emp-form-hero {
                padding: 28px 24px;
            }
        }
    </style>

    <div class="emp-form-wrap">
        <section class="emp-form-hero">
            <div class="emp-form-hero-inner">
                <div>
                    <div class="emp-form-breadcrumb">HR › Employment › Create</div>
                    <div class="emp-form-title">Create Employment</div>
                    <div class="emp-form-subtitle">
                        Add a new employment record, connect employee data to client/project structure, and configure salary and operational information.
                    </div>
                </div>

                <div class="emp-form-actions">
                    <a href="{{ $indexUrl }}" class="emp-back-btn">Back to Employment</a>
                </div>
            </div>
        </section>

        <section class="emp-form-shell">
            {{ $this->form }}

            <div style="padding: 0 22px 22px;">
                <x-filament-actions::modals />
            </div>
        </section>
    </div>

<style id="sf-create-employment-save-button-final">
    .sf-create-save-dock {
        position: sticky;
        bottom: 22px;
        z-index: 60;
        width: min(100%, 1280px);
        margin: 24px auto 0;
        display: flex;
        justify-content: flex-end;
        pointer-events: none;
    }

    .sf-create-save-btn {
        pointer-events: auto;
        border: 0;
        min-height: 54px;
        padding: 0 26px;
        border-radius: 999px;
        cursor: pointer;
        background: linear-gradient(135deg, #2563eb, #14b8a6);
        color: #ffffff;
        font-size: 15px;
        font-weight: 950;
        box-shadow: 0 18px 42px rgba(37, 99, 235, .25);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .sf-create-save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 24px 56px rgba(37, 99, 235, .32);
    }

    .sf-create-save-btn:disabled {
        opacity: .65;
        cursor: wait;
        transform: none;
    }

    .dark .sf-create-save-btn {
        box-shadow: 0 18px 42px rgba(0, 0, 0, .28);
    }
</style>

<div class="sf-create-save-dock">
    <button
        type="button"
        class="sf-create-save-btn"
        wire:click="create"
        wire:loading.attr="disabled"
        wire:target="create"
    >
        <span wire:loading.remove wire:target="create">Save Employee</span>
        <span wire:loading wire:target="create">Saving...</span>
    </button>
</div>


<style id="sf-employment-select-dropdown-overflow-fix">
    /*
     |--------------------------------------------------------------------------
     | Filament select dropdown clipping fix
     |--------------------------------------------------------------------------
     | Some premium cards/sections use rounded containers with overflow hidden.
     | That clips the Choices/Select dropdown menu. These overrides allow the
     | dropdown to expand outside the ERP Login Setup card.
     */

    .fi-fo-component-ctn,
    .fi-section,
    .fi-section-content,
    .fi-section-content-ctn,
    .fi-fo-field-wrp,
    .fi-fo-field-wrp > div,
    .fi-input-wrp,
    .choices,
    .choices__inner {
        overflow: visible !important;
    }

    .fi-section,
    .fi-fo-component-ctn,
    .fi-fo-field-wrp,
    .choices {
        position: relative !important;
    }

    .choices__list--dropdown,
    .choices__list[aria-expanded] {
        z-index: 999999 !important;
        max-height: 320px !important;
        overflow-y: auto !important;
        border-radius: 18px !important;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .18) !important;
    }

    .fi-modal .choices__list--dropdown,
    .fi-modal .choices__list[aria-expanded] {
        z-index: 1000000 !important;
    }

    /*
     | Keep the page clean while allowing dropdowns outside the field area.
     */
    .sf-create-employment-page,
    .sf-edit-employment-page,
    .sf-employment-form-shell,
    .sf-premium-form-shell,
    .sf-premium-card,
    .sf-form-card {
        overflow: visible !important;
    }
</style>


<style id="sf-employment-erp-select-open-height-final">
    /*
     |--------------------------------------------------------------------------
     | ERP Login Setup dropdown fix
     |--------------------------------------------------------------------------
     | The role dropdown is not only clipped by overflow. The parent card height
     | also ends too early. When a Choices select is open, we temporarily give
     | the active Filament section enough vertical room.
     */

    .fi-section:has(.choices.is-open),
    .fi-section:has(.choices__list--dropdown.is-active),
    .fi-section:has(.choices__list[aria-expanded="true"]) {
        overflow: visible !important;
        z-index: 999999 !important;
        position: relative !important;
        margin-bottom: 240px !important;
    }

    .fi-section:has(.choices.is-open) .fi-section-content,
    .fi-section:has(.choices__list--dropdown.is-active) .fi-section-content,
    .fi-section:has(.choices__list[aria-expanded="true"]) .fi-section-content {
        overflow: visible !important;
        min-height: 300px !important;
        padding-bottom: 220px !important;
    }

    .fi-section:has(.choices.is-open) .fi-section-content-ctn,
    .fi-section:has(.choices__list--dropdown.is-active) .fi-section-content-ctn,
    .fi-section:has(.choices__list[aria-expanded="true"]) .fi-section-content-ctn {
        overflow: visible !important;
    }

    .choices.is-open,
    .choices:has(.choices__list--dropdown.is-active),
    .choices:has(.choices__list[aria-expanded="true"]) {
        z-index: 1000000 !important;
        position: relative !important;
    }

    .choices__list--dropdown,
    .choices__list[aria-expanded] {
        z-index: 1000001 !important;
        max-height: 360px !important;
        overflow-y: auto !important;
        border-radius: 18px !important;
        box-shadow: 0 28px 70px rgba(15, 23, 42, .22) !important;
        background: #ffffff !important;
    }

    .dark .choices__list--dropdown,
    .dark .choices__list[aria-expanded] {
        background: #0f172a !important;
        border-color: rgba(148, 163, 184, .22) !important;
    }
</style>

</x-filament-panels::page>
