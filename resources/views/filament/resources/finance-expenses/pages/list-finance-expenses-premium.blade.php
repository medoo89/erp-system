<x-filament-panels::page>
    <style>
        .fi-header {
            display: none !important;
        }

        .fe-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .fe-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px 36px;
            border: 1px solid rgba(76, 167, 168, .24);
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .30), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .18), transparent 30%),
                linear-gradient(135deg, #081a34 0%, #12385d 56%, #2f6f73 100%) !important;
            box-shadow: 0 18px 36px rgba(15, 23, 42, .14);
            color: #fff;
        }

        .fe-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .fe-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 22px;
            flex-wrap: wrap;
        }

        .fe-breadcrumb {
            font-size: 14px;
            color: rgba(255, 255, 255, .72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .fe-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .fe-subtitle {
            margin-top: 16px;
            max-width: 780px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, .82) !important;
        }

        .fe-add-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 22px;
            border-radius: 999px;
            background: #f2b705;
            color: #3b2a00 !important;
            font-weight: 950;
            text-decoration: none !important;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .18);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .fe-add-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, .22);
        }

        .fe-actions {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 10px;
            padding-top: 18px;
        }

        .fe-actions .fi-ac {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
            justify-content: flex-end !important;
        }

        .fe-actions .fi-btn {
            min-height: 46px !important;
            border-radius: 999px !important;
            padding-inline: 20px !important;
            font-weight: 900 !important;
            border: 0 !important;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .18) !important;
        }

        .fe-actions .fi-btn-color-primary,
        .fe-actions .fi-btn-color-warning {
            background: #f2b705 !important;
            color: #3b2a00 !important;
        }

        .fe-actions .fi-btn-color-success {
            background: #10b981 !important;
            color: #fff !important;
        }

        .fe-table-shell {
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
            overflow: visible !important;
        }

        .fe-table-shell .fi-ta-outer,
        .fe-table-shell .fi-ta,
        .fe-table-shell .fi-ta-content,
        .fe-table-shell .fi-ta-table,
        .fe-table-shell .fi-ta-header,
        .fe-table-shell .fi-ta-toolbar,
        .fe-table-shell .fi-ta-filters,
        .fe-table-shell .fi-pagination {
            background: transparent !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .fe-table-shell .fi-ta-header,
        .fe-table-shell .fi-ta-toolbar {
            background: #ffffff !important;
            border-bottom: 1px solid #e4ecef !important;
        }

        .fe-table-shell .fi-ta-filters {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            border-bottom: 1px solid #e4ecef !important;
            padding: 18px !important;
        }

        .fe-table-shell .fi-input-wrp,
        .fe-table-shell .fi-select,
        .fe-table-shell .fi-input,
        .fe-table-shell .fi-select-input,
        .fe-table-shell input,
        .fe-table-shell select {
            border-radius: 14px !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .035) !important;
        }

        .fe-table-shell .fi-ta-search-field .fi-input-wrp,
        .fe-table-shell .fi-ta-search-field input {
            border-radius: 999px !important;
            min-height: 42px !important;
        }

        .fe-table-shell table thead th {
            background: #eef5f8 !important;
            color: #1f4664 !important;
            font-size: 11px !important;
            font-weight: 950 !important;
            letter-spacing: .10em !important;
            text-transform: uppercase !important;
            border-bottom: 1px solid #d7e2e5 !important;
        }

        .fe-table-shell table tbody td {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #eef2f7 !important;
            font-weight: 650 !important;
        }

        .fe-table-shell table tbody tr:hover td {
            background: #f8fcfd !important;
        }

        .fe-table-shell .fi-badge {
            border-radius: 999px !important;
            font-weight: 900 !important;
        }

        .fe-table-shell .fi-btn {
            border-radius: 999px !important;
            font-weight: 900 !important;
        }

        .fe-table-shell .fi-pagination {
            border-top: 1px solid #e4ecef !important;
            background: #ffffff !important;
        }

        .fe-table-shell,
        .fe-table-shell .fi-ta-ctn,
        .fe-table-shell .fi-ta-outer,
        .fe-table-shell .fi-ta,
        .fe-table-shell .fi-ta-content,
        .fe-table-shell .fi-ta-filters,
        .fe-table-shell .fi-fo,
        .fe-table-shell .fi-fo-component-ctn {
            overflow: visible !important;
        }

        .fe-table-shell .choices__list--dropdown,
        .fe-table-shell .choices__list[aria-expanded],
        .fe-table-shell [role="listbox"] {
            z-index: 99999 !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 42px rgba(15, 23, 42, .18) !important;
        }

        .dark .fe-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76, 167, 168, .18);
        }

        .dark .fe-table-shell {
            background: linear-gradient(180deg, rgba(12, 23, 38, .98) 0%, rgba(15, 23, 42, .96) 100%);
            border-color: rgba(76, 167, 168, .18);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .28);
        }

        .dark .fe-table-shell .fi-ta-header,
        .dark .fe-table-shell .fi-ta-toolbar,
        .dark .fe-table-shell .fi-pagination,
        .dark .fe-table-shell .fi-ta-filters {
            background: rgba(15, 23, 42, .92) !important;
            border-color: rgba(76, 167, 168, .16) !important;
        }

        .dark .fe-table-shell .fi-input-wrp,
        .dark .fe-table-shell .fi-select,
        .dark .fe-table-shell .fi-input,
        .dark .fe-table-shell .fi-select-input,
        .dark .fe-table-shell input,
        .dark .fe-table-shell select {
            background: rgba(15, 23, 42, .92) !important;
            color: #f8fafc !important;
            border-color: rgba(76, 167, 168, .18) !important;
        }

        .dark .fe-table-shell table thead th {
            background: rgba(15, 23, 42, .92) !important;
            color: #8fd6d7 !important;
            border-color: rgba(76, 167, 168, .16) !important;
        }

        .dark .fe-table-shell table tbody td {
            background: rgba(12, 23, 38, .96) !important;
            color: #f8fafc !important;
            border-color: rgba(76, 167, 168, .10) !important;
        }

        .dark .fe-table-shell table tbody tr:hover td {
            background: rgba(20, 35, 56, .96) !important;
        }

        /* FINANCE EXPENSES COLUMNS DROPDOWN REAL SCROLL FIX */
        .fi-dropdown-panel:has(input[type="checkbox"]) {
            width: 300px !important;
            max-width: 300px !important;
            max-height: 460px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            border-radius: 22px !important;
            background: rgba(255,255,255,.98) !important;
            border: 1px solid #d7e2e5 !important;
            box-shadow: 0 24px 55px rgba(15,23,42,.18) !important;
            padding: 14px !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar {
            width: 7px !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-track {
            background: transparent !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-thumb {
            border-radius: 999px !important;
            background: rgba(148,163,184,.58) !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) label {
            display: grid !important;
            grid-template-columns: 18px 1fr !important;
            align-items: center !important;
            gap: 11px !important;
            min-height: 38px !important;
            padding: 6px 8px !important;
            border-radius: 13px !important;
            color: #0f172a !important;
            font-weight: 850 !important;
            cursor: pointer !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) label:hover {
            background: #f4f8fa !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"] {
            appearance: none !important;
            -webkit-appearance: none !important;
            display: inline-grid !important;
            place-content: center !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: static !important;
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            margin: 0 !important;
            border-radius: 4px !important;
            background: #ffffff !important;
            border: 1.5px solid #94a3b8 !important;
            box-shadow: none !important;
            cursor: pointer !important;
            pointer-events: auto !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]::before {
            content: "" !important;
            width: 8px !important;
            height: 8px !important;
            transform: scale(0) !important;
            transition: transform .12s ease !important;
            background: #ffffff !important;
            clip-path: polygon(14% 44%, 0 65%, 43% 100%, 100% 18%, 80% 0%, 38% 62%) !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:checked {
            background: #1f4664 !important;
            border-color: #1f4664 !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:checked::before {
            transform: scale(1) !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) input[type="checkbox"]:disabled {
            opacity: 1 !important;
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        .fi-dropdown-panel:has(input[type="checkbox"]) button[type="submit"],
        .fi-dropdown-panel:has(input[type="checkbox"]) .fi-btn {
            position: sticky !important;
            bottom: 0 !important;
            z-index: 20 !important;
            margin-top: 10px !important;
            border-radius: 999px !important;
            background: #f2b705 !important;
            color: #3b2a00 !important;
            font-weight: 950 !important;
            min-height: 40px !important;
            padding-inline: 16px !important;
            box-shadow: 0 10px 22px rgba(242,183,5,.22) !important;
        }

        .dark .fi-dropdown-panel:has(input[type="checkbox"]) {
            background: rgba(15,23,42,.98) !important;
            border-color: rgba(76,167,168,.22) !important;
            box-shadow: 0 24px 55px rgba(0,0,0,.38) !important;
        }

        .dark .fi-dropdown-panel:has(input[type="checkbox"]) label,
        .dark .fi-dropdown-panel:has(input[type="checkbox"]) label span {
            color: #f8fafc !important;
        }

        .dark .fi-dropdown-panel:has(input[type="checkbox"]) label:hover {
            background: rgba(76,167,168,.10) !important;
        }

        .dark .fi-dropdown-panel:has(input[type="checkbox"])::-webkit-scrollbar-thumb {
            background: rgba(148,163,184,.36) !important;
        }

</style>

    <div class="fe-wrap">
        <section class="fe-hero">
            <div class="fe-hero-inner">
                <div>
                    <div class="fe-breadcrumb">Finance Expenses › List</div>
                    <div class="fe-title">Finance Expenses</div>
                    <div class="fe-subtitle">
                        Track and manage finance expenses across pre-employment, employment, rotation, and ad hoc operations.
                    </div>
                </div>

                <div class="fe-actions">
                    <a
                        href="{{ \App\Filament\Resources\FinanceExpenses\FinanceExpenseResource::getUrl('create') }}"
                        class="fe-add-btn"
                    >
                        Add Expense
                    </a>
                </div>
            </div>
        </section>

        <section class="fe-table-shell">
            {{ $this->table }}
        </section>
    </div>

<!-- FINANCE EXPENSES FORCE ENABLE COLUMNS CHECKBOXES -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    function enableFinanceExpenseColumnCheckboxes() {
        document
            .querySelectorAll('.fe-table-shell .fi-dropdown-panel input[type="checkbox"]')
            .forEach(function (checkbox) {
                checkbox.removeAttribute('disabled');
                checkbox.disabled = false;
                checkbox.style.pointerEvents = 'auto';
                checkbox.style.opacity = '1';

                const label = checkbox.closest('label');
                if (label) {
                    label.style.pointerEvents = 'auto';
                    label.style.opacity = '1';
                    label.style.cursor = 'pointer';

                    label.querySelectorAll('span').forEach(function (span) {
                        span.style.opacity = '1';
                    });
                }
            });
    }

    enableFinanceExpenseColumnCheckboxes();

    document.addEventListener('click', function () {
        setTimeout(enableFinanceExpenseColumnCheckboxes, 50);
        setTimeout(enableFinanceExpenseColumnCheckboxes, 200);
    }, true);

    const observer = new MutationObserver(function () {
        enableFinanceExpenseColumnCheckboxes();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['disabled', 'class', 'style']
    });
});
</script>
<!-- /FINANCE EXPENSES FORCE ENABLE COLUMNS CHECKBOXES -->

</x-filament-panels::page>
