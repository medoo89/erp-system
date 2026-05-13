<x-filament-panels::page>
    @php
        $record = $this->record ?? null;

        $employeeName = $record?->employee_name
            ?? $record?->name
            ?? 'Employment Profile';

        $employeeCode = $record?->employee_code ?? '-';
        $email = $record?->employee_email ?? '-';
        $status = $record?->status ?? $record?->current_work_status ?? 'Active';
        $client = $record?->client_name ?? '-';
        $project = $record?->project_name ?? '-';
        $position = $record?->position_title ?? $record?->position ?? '-';

        try {
            $resourceClass = static::getResource();
            $indexUrl = $resourceClass::getUrl('index');
            $viewUrl = method_exists($resourceClass, 'getUrl')
                ? $resourceClass::getUrl('view', ['record' => $record])
                : $indexUrl;
        } catch (\Throwable $e) {
            $indexUrl = url('/admin/employments');
            $viewUrl = $indexUrl;
        }
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .emp-edit-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .emp-edit-hero {
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

        .emp-edit-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg,#4ca7a8,#b38b2f);
        }

        .emp-edit-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 22px;
            flex-wrap: wrap;
        }

        .emp-edit-breadcrumb {
            font-size: 14px;
            color: rgba(255,255,255,.72);
            font-weight: 700;
            margin-bottom: 12px;
        }

        .emp-edit-title {
            font-size: clamp(44px,4vw,64px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .emp-edit-subtitle {
            margin-top: 16px;
            max-width: 860px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.82) !important;
        }

        .emp-edit-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .emp-edit-btn,
        .emp-edit-soft-btn,
        .emp-edit-danger-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none !important;
            border: 0;
            font-size: 14px;
            font-weight: 950;
            cursor: pointer;
            transition: .18s ease;
        }

        .emp-edit-btn {
            background: #f2b705;
            color: #3b2a00 !important;
            box-shadow: 0 12px 24px rgba(242,183,5,.22);
        }

        .emp-edit-soft-btn {
            background: rgba(255,255,255,.12);
            color: #fff !important;
            border: 1px solid rgba(255,255,255,.16);
        }

        .emp-edit-danger-btn {
            background: #e11d48;
            color: #fff !important;
            box-shadow: 0 12px 24px rgba(225,29,72,.22);
        }

        .emp-edit-btn:hover,
        .emp-edit-soft-btn:hover,
        .emp-edit-danger-btn:hover {
            transform: translateY(-1px);
        }

        .emp-edit-badges {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .emp-edit-badge {
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
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .emp-edit-form-shell {
            border-radius: 30px;
            border: 1px solid #d7e2e5;
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.08), transparent 34%),
                linear-gradient(180deg,#ffffff 0%,#f8fbfc 100%) !important;
            box-shadow: 0 14px 30px rgba(15,23,42,.07);
            padding: 26px 24px;
            overflow: visible !important;
        }

        .emp-edit-form-shell .fi-section {
            border-radius: 28px !important;
            border: 1px solid #d7e2e5 !important;
            background: linear-gradient(180deg,#ffffff 0%,#f8fbfc 100%) !important;
            box-shadow: 0 10px 22px rgba(15,23,42,.045) !important;
            overflow: hidden !important;
        }

        .emp-edit-form-shell .fi-section-header {
            background: linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%) !important;
            border-bottom: 1px solid #e4ecef !important;
            padding: 18px 22px !important;
        }

        .emp-edit-form-shell .fi-section-header-heading,
        .emp-edit-form-shell .fi-section h2,
        .emp-edit-form-shell .fi-section h3 {
            color: #0f172a !important;
            font-weight: 950 !important;
        }

        .emp-edit-form-shell .fi-section-content {
            padding: 24px !important;
        }

        .emp-edit-form-shell .fi-input-wrp,
        .emp-edit-form-shell .fi-select,
        .emp-edit-form-shell .fi-textarea,
        .emp-edit-form-shell input,
        .emp-edit-form-shell select,
        .emp-edit-form-shell textarea {
            border-radius: 18px !important;
        }

        .emp-edit-form-shell .fi-input-wrp,
        .emp-edit-form-shell .fi-select,
        .emp-edit-form-shell .fi-textarea {
            min-height: 48px !important;
            background: rgba(255,255,255,.96) !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 10px 22px rgba(15,23,42,.045) !important;
        }

        .emp-edit-form-shell input,
        .emp-edit-form-shell textarea,
        .emp-edit-form-shell select {
            color: #0f172a !important;
        }

        .emp-edit-form-shell label,
        .emp-edit-form-shell .fi-fo-field-wrp-label span {
            color: #334155 !important;
            font-weight: 800 !important;
        }

        .emp-edit-actions-bottom {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .emp-edit-actions-bottom button,
        .emp-edit-actions-bottom a {
            min-height: 44px !important;
            padding: 0 20px !important;
            border-radius: 999px !important;
            font-size: 13px !important;
            font-weight: 950 !important;
            text-decoration: none !important;
        }

        .emp-edit-save {
            background: #f2b705 !important;
            color: #3b2a00 !important;
            box-shadow: 0 12px 24px rgba(242,183,5,.22) !important;
        }

        .emp-edit-cancel {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #d7e2e5 !important;
        }

        .dark .emp-edit-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76,167,168,.20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179,139,47,.12), transparent 30%),
                linear-gradient(135deg,#071427 0%,#0b1a31 58%,#12385d 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .emp-edit-form-shell {
            background:
                radial-gradient(circle at 50% 8%, rgba(76,167,168,.10), transparent 34%),
                linear-gradient(180deg,rgba(12,23,38,.98) 0%,rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .emp-edit-form-shell .fi-section {
            background: linear-gradient(180deg,rgba(12,23,38,.98) 0%,rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        .dark .emp-edit-form-shell .fi-section-header {
            background: rgba(15,23,42,.92) !important;
            border-bottom-color: rgba(76,167,168,.16) !important;
        }

        .dark .emp-edit-form-shell .fi-section-header-heading,
        .dark .emp-edit-form-shell .fi-section h2,
        .dark .emp-edit-form-shell .fi-section h3 {
            color: #f8fafc !important;
        }

        .dark .emp-edit-form-shell .fi-input-wrp,
        .dark .emp-edit-form-shell .fi-select,
        .dark .emp-edit-form-shell .fi-textarea {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.20) !important;
            box-shadow: 0 10px 22px rgba(0,0,0,.24) !important;
        }

        .dark .emp-edit-form-shell input,
        .dark .emp-edit-form-shell textarea,
        .dark .emp-edit-form-shell select {
            color: #f8fafc !important;
        }

        .dark .emp-edit-form-shell label,
        .dark .emp-edit-form-shell .fi-fo-field-wrp-label span {
            color: #dbeafe !important;
        }

        .dark .emp-edit-cancel {
            background: rgba(15,23,42,.92) !important;
            color: #f8fafc !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        @media (max-width: 900px) {
            .emp-edit-hero {
                padding: 28px 24px;
            }

            .emp-edit-form-shell {
                padding: 18px;
            }
        }
    </style>

    <div class="emp-edit-wrap">
        <section class="emp-edit-hero">
            <div class="emp-edit-hero-inner">
                <div>
                    <div class="emp-edit-breadcrumb">HR › Employment › Edit</div>
                    <div class="emp-edit-title">Edit Employment Profile — {{ $employeeName }}</div>
                    <div class="emp-edit-subtitle">
                        Update operational employment information only. Salary configuration is managed from the finance profile workflow and is hidden here.
                    </div>

                    <div class="emp-edit-badges">
                        <div class="emp-edit-badge">{{ $employeeCode }}</div>
                        <div class="emp-edit-badge">{{ $status }}</div>
                        <div class="emp-edit-badge">{{ $client }}</div>
                        <div class="emp-edit-badge">{{ $project }}</div>
                    </div>
                </div>

                <div class="emp-edit-actions">
                    <a href="{{ $viewUrl }}" class="emp-edit-soft-btn">Back to Profile</a>
                    <button type="button" wire:click="save" class="emp-edit-btn">Save Changes</button>
                    <button type="button" wire:click="mountAction('delete')" class="emp-edit-danger-btn">Delete</button>
                </div>
            </div>
        </section>

        <section class="emp-edit-form-shell">
            {{ $this->form }}

            <div class="emp-edit-actions-bottom">
                <button type="button" wire:click="save" class="emp-edit-save">Save Changes</button>
                <a href="{{ $viewUrl }}" class="emp-edit-cancel">Cancel</a>
            </div>

            <x-filament-actions::modals />
        </section>
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
