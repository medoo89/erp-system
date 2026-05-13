@php
    $emptyText = 'No data available';
    $range = $totals['range'] ?? [];
    $summary = $totals['summary'] ?? [];

    $byCurrency = $totals['by_currency'] ?? [];
    $byClient = $totals['by_client'] ?? [];
    $byProject = $totals['by_project'] ?? [];
    $byEmployee = $totals['by_employee'] ?? [];

    $revenueByCurrency = $summary['revenue_by_currency'] ?? [];
    $salaryCostByCurrency = $summary['salary_cost_by_currency'] ?? [];
    $salaryPaidByCurrency = $summary['salary_paid_by_currency'] ?? [];
    $salaryRemainingByCurrency = $summary['salary_remaining_by_currency'] ?? [];
    $salaryApprovedByCurrency = $summary['salary_approved_by_currency'] ?? [];
    $salaryDraftByCurrency = $summary['salary_draft_by_currency'] ?? [];
    $expensesByCurrency = $summary['expenses_by_currency'] ?? [];
    $netByCurrency = $summary['net_by_currency'] ?? [];

    $state = $this->form->getState();
    $baseCurrency = strtoupper((string) ($state['base_currency'] ?? 'EUR'));

    $exchangeRates = [
        'USD' => (float) ($state['rate_usd'] ?? 0),
        'EUR' => (float) ($state['rate_eur'] ?? 0),
        'LYD' => (float) ($state['rate_lyd'] ?? 0),
        'GBP' => (float) ($state['rate_gbp'] ?? 0),
    ];

    if (($exchangeRates[$baseCurrency] ?? 0) <= 0) {
        $exchangeRates[$baseCurrency] = 1.0;
    }

    $rangeText = (!empty($range['start']) && !empty($range['end']))
        ? $range['start'] . ' → ' . $range['end']
        : '-';

    $formatMoney = fn ($amount) => number_format((float) $amount, 2);

    $convertToBase = function (array $currencyMap) use ($baseCurrency, $exchangeRates) {
        $total = 0.0;

        foreach ($currencyMap as $currency => $amount) {
            $currency = strtoupper((string) $currency);
            $amount = (float) $amount;

            if ($currency === $baseCurrency) {
                $total += $amount;
                continue;
            }

            $rate = (float) ($exchangeRates[$currency] ?? 0);

            if ($rate <= 0) {
                continue;
            }

            $total += $amount * $rate;
        }

        return round($total, 2);
    };
    $convertedRevenue = $convertToBase($revenueByCurrency);
    $convertedSalary = $convertToBase($salaryCostByCurrency);
    $convertedExpenses = $convertToBase($expensesByCurrency);
    $convertedNet = $convertToBase($netByCurrency);

    $renderMap = function (array $map, bool $isNet = false) use ($formatMoney, $emptyText) {
        if (empty($map)) {
            return '<div class="gf-empty-text">' . e($emptyText) . '</div>';
        }

        $html = '<div class="gf-stack">';

        foreach ($map as $currency => $amount) {
            $colorClass = $isNet
                ? (((float) $amount >= 0) ? 'gf-positive' : 'gf-negative')
                : 'gf-text-strong';

            $html .= '<div class="gf-stack-row">'
                . '<span class="gf-stack-code">' . e($currency) . '</span>'
                . '<span class="' . $colorClass . '">' . e($formatMoney($amount)) . '</span>'
                . '</div>';
        }

        $html .= '</div>';

        return $html;
    };


    $renderCurrencyStack = function (array $map, string $emptyLabel = 'No data available', bool $isNet = false) use ($formatMoney) {
        if (empty($map)) {
            return '<div class="gf-empty-text">' . e($emptyLabel) . '</div>';
        }

        $html = '<div class="gf-stack">';

        foreach ($map as $currency => $amount) {
            $colorClass = $isNet
                ? (((float) $amount >= 0) ? 'gf-positive' : 'gf-negative')
                : 'gf-text-strong';

            $html .= '<div class="gf-stack-row">'
                . '<span class="gf-stack-code">' . e($currency) . '</span>'
                . '<span class="' . $colorClass . '">' . e($formatMoney($amount)) . '</span>'
                . '</div>';
        }

        $html .= '</div>';

        return $html;
    };

    $renderCompactMap = function (array $map, bool $isNet = false) use ($formatMoney, $emptyText) {
        if (empty($map)) {
            return '<span class="gf-empty-text">-</span>';
        }

        $html = '<div class="gf-compact-stack">';

        foreach ($map as $currency => $amount) {
            $colorClass = $isNet
                ? (((float) $amount >= 0) ? 'gf-positive' : 'gf-negative')
                : 'gf-text-strong';

            $html .= '<div class="' . $colorClass . '">'
                . e($currency) . ': ' . e($formatMoney($amount))
                . '</div>';
        }

        $html .= '</div>';

        return $html;
    };
@endphp

<x-filament-panels::page>
    <style>
        .fi-header {
            display: none !important;
        }

        .gf-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 6px;
            border-radius: 34px;
            background:
                radial-gradient(circle at top right, rgba(59,130,246,.08), transparent 22%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.06), transparent 22%);
        }

        .gf-hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 28px 32px;
            border: 1px solid rgba(76,167,168,.14);
            background: linear-gradient(135deg, #081a34 0%, #0b2a56 52%, #0f3f48 100%);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
        }

        .gf-hero::before {
            content: "";
            position: absolute;
            right: -70px;
            top: -70px;
            width: 250px;
            height: 250px;
            border-radius: 999px;
            background: rgba(245,158,11,.22);
            filter: blur(42px);
            opacity: .95;
        }

        .gf-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b89332);
        }

        .gf-hero > * {
            position: relative;
            z-index: 1;
        }

        .gf-hero-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
        }

        .gf-title {
            font-size: 56px;
            line-height: .95;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: -.05em;
        }

        .gf-actions .fi-ac {
            gap: 10px !important;
        }

        .gf-actions .fi-btn {
            border-radius: 999px !important;
            min-height: 44px;
            padding-inline: 16px !important;
            font-weight: 800 !important;
            border: 0 !important;
            box-shadow: none !important;
            background: linear-gradient(135deg, #8b95a7 0%, #667085 100%) !important;
            color: #ffffff !important;
        }

        .gf-chip-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .gf-rate-chip {
            padding: 10px 14px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 999px;
            color: #dbeafe;
            font-weight: 800;
            font-size: 13px;
        }

        .gf-card,
        .gf-panel {
            background: #ffffff;
            border: 1px solid #dbe4ee;
            border-radius: 24px;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.04);
        }

        .gf-panel {
            padding: 24px;
        }

        .gf-filter-panel .fi-section {
            box-shadow: none !important;
            border-radius: 18px !important;
        }

        .gf-filter-panel .fi-section-header {
            background: linear-gradient(135deg, #17233d 0%, #0f1d36 100%) !important;
            color: #ffffff !important;
        }

        .gf-filter-panel .fi-section-content {
            background: linear-gradient(180deg, #16233d 0%, #0f1d36 100%) !important;
        }

        .dark .gf-filter-panel .fi-section-header,
        .dark .gf-filter-panel .fi-section-content {
            background: linear-gradient(180deg, #0e1a31 0%, #0a162a 100%) !important;
        }

        .gf-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 6px;
        }

        .gf-filter-actions .fi-btn {
            border-radius: 999px !important;
            font-weight: 800 !important;
            background: linear-gradient(135deg, #f59e0b 0%, #ea7a00 100%) !important;
            color: #1f1400 !important;
            border: 0 !important;
        }

        .gf-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .gf-kpi-card {
            padding: 22px;
            border-radius: 24px;
            border: 1px solid #dbe4ee;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 14px 30px rgba(15,23,42,.04);
        }

        .gf-kpi-card--revenue {
            border-left: 6px solid #2563eb;
        }

        .gf-kpi-card--salary {
            border-left: 6px solid #7c3aed;
            background: linear-gradient(180deg, #ffffff 0%, #fbf7ff 100%);
            border-color: #e9d5ff;
        }

        .gf-kpi-card--expenses {
            border-left: 6px solid #f59e0b;
            background: linear-gradient(180deg, #ffffff 0%, #fffaf0 100%);
            border-color: #fde68a;
        }

        .gf-kpi-card--net {
            border-left: 6px solid #16a34a;
            background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);
            border-color: #bbf7d0;
        }

        .gf-kpi-title {
            font-size: 14px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 14px;
        }

        .gf-summary-dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 26px;
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 18px 44px rgba(15, 23, 42, 0.14);
        }

        .gf-summary-dark-head {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .gf-summary-dark-title {
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
        }

        .gf-summary-dark-sub {
            margin-top: 8px;
            color: #cbd5e1;
            font-size: 14px;
        }

        .gf-base-chip {
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            font-size: 13px;
            font-weight: 800;
        }

        .gf-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .gf-summary-box {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 18px;
            padding: 18px;
        }

        .gf-summary-label {
            font-size: 12px;
            color: #cbd5e1;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: .05em;
        }

        .gf-summary-value {
            margin-top: 10px;
            font-size: 28px;
            font-weight: 900;
            color: #ffffff;
        }

        .gf-section-title-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 14px;
        }

        .gf-section-subtitle {
            color: #7c8aa0;
            margin-bottom: 18px;
        }

        .gf-table-wrap {
            overflow: auto;
        }

        .gf-table {
            width: 100%;
            border-collapse: collapse;
        }

        .gf-table thead tr {
            background: #f8fafc;
            color: #7c8aa0;
            text-transform: uppercase;
            font-size: 13px;
        }

        .gf-table th {
            text-align: left;
            padding: 14px;
        }

        .gf-table td {
            padding: 16px 14px;
            border-top: 1px solid #e5edf5;
            vertical-align: top;
            color: #0f172a;
        }

        .gf-text-strong {
            font-weight: 700;
            color: #0f172a;
        }

        .gf-row-title {
            font-weight: 800;
            color: #0f172a;
        }

        .gf-positive {
            font-weight: 900;
            color: #16a34a;
        }

        .gf-negative {
            font-weight: 900;
            color: #dc2626;
        }

        .gf-empty-text {
            color: #94a3b8;
        }

        .gf-stack {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .gf-stack-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 6px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .gf-stack-code {
            font-weight: 800;
            color: #334155;
        }

        .gf-compact-stack {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .dark .gf-wrap {
            background:
                radial-gradient(circle at top right, rgba(59,130,246,.08), transparent 22%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.06), transparent 22%),
                linear-gradient(180deg, rgba(7,20,39,.96) 0%, rgba(10,24,42,.96) 100%);
        }

        .dark .gf-card,
        .dark .gf-panel,
        .dark .gf-kpi-card {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.16);
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }

        .dark .gf-table thead tr {
            background: rgba(255,255,255,.03);
            color: #8ea8be;
        }

        .dark .gf-table td {
            color: #f6fbff;
            border-top-color: rgba(76,167,168,.12);
        }

        .dark .gf-text-strong,
        .dark .gf-row-title,
        .dark .gf-stack-code {
            color: #f6fbff;
        }

        .dark .gf-section-subtitle,
        .dark .gf-empty-text {
            color: #9fb2c3;
        }

        .dark .gf-stack-row {
            border-bottom-color: rgba(76,167,168,.12);
        }

        @media (max-width: 1100px) {
            .gf-kpi-grid,
            .gf-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .gf-title {
                font-size: 44px;
            }
        }

        @media (max-width: 760px) {
            .gf-kpi-grid,
            .gf-summary-grid {
                grid-template-columns: 1fr;
            }

            .gf-title {
                font-size: 36px;
            }

            .gf-hero {
                padding: 22px 20px;
            }
        }
    /* FINAL Global Finance Totals - Material Design 3 polish */
    .fi-header {
        display: none !important;
    }

    .gft-wrap,
    .global-finance-wrap,
    .global-finance-page,
    .finance-totals-page {
        width: min(100%, 1240px) !important;
        max-width: 1240px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .gft-wrap,
    .global-finance-wrap,
    .global-finance-page,
    .finance-totals-page,
    [class*="global-finance"],
    [class*="finance-totals"] {
        color: #0f172a;
    }

    /* Main hero/header */
    .gft-hero,
    .global-finance-hero,
    .finance-totals-hero,
    .sada-finance-hero {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 26px !important;
        padding: 28px 32px !important;
        border: 1px solid rgba(76,167,168,.18) !important;
        background:
            radial-gradient(circle at 90% 10%, rgba(76,167,168,.22), transparent 32%),
            linear-gradient(135deg,#18344d 0%,#234d6f 50%,#2f6f73 100%) !important;
        box-shadow: 0 18px 34px rgba(15,23,42,.10) !important;
        margin-bottom: 24px !important;
    }

    .gft-hero::after,
    .global-finance-hero::after,
    .finance-totals-hero::after,
    .sada-finance-hero::after {
        content: "" !important;
        position: absolute !important;
        inset: auto 0 0 0 !important;
        height: 4px !important;
        background: linear-gradient(90deg,#4ca7a8,#b38b2f) !important;
    }

    .gft-hero *,
    .global-finance-hero *,
    .finance-totals-hero *,
    .sada-finance-hero * {
        position: relative !important;
        z-index: 1 !important;
    }

    .gft-hero h1,
    .global-finance-hero h1,
    .finance-totals-hero h1,
    .sada-finance-hero h1,
    .gft-title,
    .global-finance-title,
    .finance-totals-title {
        color: #fff !important;
        font-size: 54px !important;
        line-height: .95 !important;
        font-weight: 950 !important;
        letter-spacing: -.045em !important;
        margin: 0 !important;
    }

    .gft-hero p,
    .global-finance-hero p,
    .finance-totals-hero p,
    .sada-finance-hero p,
    .gft-subtitle,
    .global-finance-subtitle,
    .finance-totals-subtitle {
        color: rgba(255,255,255,.82) !important;
        font-size: 15px !important;
        line-height: 1.7 !important;
        margin-top: 14px !important;
        max-width: 900px !important;
    }

    /* Exchange chips */
    .gft-hero .fi-badge,
    .global-finance-hero .fi-badge,
    .finance-totals-hero .fi-badge,
    .sada-finance-hero .fi-badge,
    .gft-rate-chip,
    .exchange-rate-chip,
    .finance-rate-chip {
        display: inline-flex !important;
        align-items: center !important;
        min-height: 36px !important;
        padding: 0 14px !important;
        border-radius: 999px !important;
        background: rgba(255,255,255,.12) !important;
        color: #fff !important;
        border: 1px solid rgba(255,255,255,.14) !important;
        font-weight: 900 !important;
        letter-spacing: .04em !important;
        box-shadow: none !important;
    }

    /* Form/filter cards */
    .fi-section,
    .fi-fo,
    .gft-filter-card,
    .gft-settings-card,
    .global-finance-filter-card,
    .finance-filter-card,
    .finance-settings-card {
        border-radius: 22px !important;
        border: 1px solid #d7e2e5 !important;
        background: linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%) !important;
        box-shadow: 0 10px 24px rgba(15,23,42,.045) !important;
        color: #0f172a !important;
        overflow: hidden !important;
    }

    .fi-section-header,
    .fi-section-content,
    .fi-fo-component-ctn {
        background: transparent !important;
    }

    .fi-section-header {
        border-bottom: 1px solid rgba(215,226,229,.85) !important;
        padding: 16px 18px !important;
    }

    .fi-section-header-heading,
    .fi-section-header h2,
    .fi-section-header h3 {
        color: #0f172a !important;
        font-weight: 950 !important;
        letter-spacing: -.02em !important;
    }

    .fi-section-content {
        padding: 18px !important;
    }

    /* Inputs */
    .fi-input-wrp,
    .fi-select,
    .fi-select-input,
    .fi-input,
    select,
    input {
        border-radius: 14px !important;
    }

    .fi-input-wrp,
    .fi-select {
        background: #fff !important;
        border-color: #d7e2e5 !important;
        box-shadow: 0 8px 18px rgba(15,23,42,.035) !important;
    }

    .fi-input,
    .fi-select-input,
    select,
    input {
        color: #0f172a !important;
        background: #fff !important;
    }

    .fi-fo-field-wrp-label,
    .fi-fo-field-wrp-label span,
    label {
        color: #334155 !important;
        font-weight: 850 !important;
    }

    .fi-fo-field-wrp-hint,
    .fi-fo-field-wrp-error-message {
        color: #64748b !important;
    }

    /* Buttons */
    .fi-btn {
        border-radius: 999px !important;
        min-height: 42px !important;
        padding-inline: 18px !important;
        font-weight: 900 !important;
        box-shadow: none !important;
    }

    .fi-btn-color-warning,
    .fi-btn-color-primary,
    button[type="submit"] {
        background: #f2b705 !important;
        color: #3b2a00 !important;
        border-color: #f2b705 !important;
        box-shadow: 0 10px 20px rgba(242,183,5,.18) !important;
    }

    .fi-btn-color-success {
        background: #10b981 !important;
        color: #052e2b !important;
        border-color: #10b981 !important;
    }

    .fi-btn-color-danger {
        background: #ef4444 !important;
        color: #fff !important;
        border-color: #ef4444 !important;
    }

    .fi-btn-color-gray {
        background: #eef2f7 !important;
        color: #334155 !important;
        border-color: #d7e2e5 !important;
    }

    /* Summary cards */
    .gft-summary-grid,
    .finance-summary-grid,
    .global-finance-summary-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }

    .gft-summary-card,
    .finance-summary-card,
    .global-finance-summary-card,
    .gft-total-card,
    .finance-total-card {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 20px !important;
        border: 1px solid #d7e2e5 !important;
        background: rgba(255,255,255,.96) !important;
        padding: 18px !important;
        box-shadow: 0 8px 18px rgba(15,23,42,.045) !important;
    }

    .gft-summary-card::before,
    .finance-summary-card::before,
    .global-finance-summary-card::before,
    .gft-total-card::before,
    .finance-total-card::before {
        content: "" !important;
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
        width: 5px !important;
        background: linear-gradient(180deg,#1f4664,#4ca7a8) !important;
    }

    .gft-summary-card h3,
    .finance-summary-card h3,
    .global-finance-summary-card h3,
    .gft-total-card h3,
    .finance-total-card h3 {
        color: #64748b !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .gft-summary-card strong,
    .finance-summary-card strong,
    .global-finance-summary-card strong,
    .gft-total-card strong,
    .finance-total-card strong,
    .gft-value,
    .finance-value {
        color: #0f172a !important;
        font-size: 28px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
    }

    /* Tables */
    .fi-ta {
        border-radius: 22px !important;
        border: 1px solid #d7e2e5 !important;
        background: #fff !important;
        box-shadow: 0 10px 24px rgba(15,23,42,.045) !important;
        overflow: hidden !important;
    }

    .fi-ta thead th {
        background: #eef5f8 !important;
        color: #1f4664 !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .1em !important;
        text-transform: uppercase !important;
    }

    .fi-ta tbody td {
        background: #fff !important;
        color: #0f172a !important;
        border-color: #eef2f7 !important;
        font-weight: 650 !important;
    }

    .fi-ta tbody tr:hover td {
        background: #f8fcfd !important;
    }

    /* Remove old dark navy blocks on this page */
    .gft-wrap .fi-section,
    .global-finance-wrap .fi-section,
    .finance-totals-page .fi-section {
        background: linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%) !important;
    }

    /* Dark mode */
    .dark .fi-section,
    .dark .fi-fo,
    .dark .fi-ta,
    .dark .gft-summary-card,
    .dark .finance-summary-card,
    .dark .global-finance-summary-card,
    .dark .gft-total-card,
    .dark .finance-total-card {
        background: rgba(12,23,38,.96) !important;
        border-color: rgba(76,167,168,.16) !important;
        color: #f8fafc !important;
    }

    .dark .fi-section-header-heading,
    .dark .fi-section-header h2,
    .dark .fi-section-header h3,
    .dark .gft-summary-card strong,
    .dark .finance-summary-card strong,
    .dark .global-finance-summary-card strong,
    .dark .gft-total-card strong,
    .dark .finance-total-card strong,
    .dark .gft-value,
    .dark .finance-value {
        color: #f8fafc !important;
    }

    .dark .fi-ta thead th {
        background: rgba(15,23,42,.92) !important;
        color: #8fd6d7 !important;
    }

    .dark .fi-ta tbody td {
        background: rgba(12,23,38,.96) !important;
        color: #f8fafc !important;
    }

    @media (max-width: 1100px) {
        .gft-summary-grid,
        .finance-summary-grid,
        .global-finance-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }

        .gft-hero h1,
        .global-finance-hero h1,
        .finance-totals-hero h1,
        .sada-finance-hero h1,
        .gft-title,
        .global-finance-title,
        .finance-totals-title {
            font-size: 42px !important;
        }
    }

    @media (max-width: 760px) {
        .gft-summary-grid,
        .finance-summary-grid,
        .global-finance-summary-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* FINAL Global Finance Totals Day Night Interactive Theme */
    :root {
        --gft-page-bg: #eef5f7;
        --gft-card-bg: rgba(255,255,255,.96);
        --gft-card-bg-soft: linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%);
        --gft-border: #d7e2e5;
        --gft-text: #0f172a;
        --gft-muted: #64748b;
        --gft-label: #1f4664;
        --gft-input-bg: #ffffff;
        --gft-input-text: #0f172a;
        --gft-hero-1: #18344d;
        --gft-hero-2: #234d6f;
        --gft-hero-3: #2f6f73;
        --gft-shadow: 0 10px 24px rgba(15,23,42,.045);
    }

    .dark {
        --gft-page-bg: #071427;
        --gft-card-bg: rgba(12,23,38,.96);
        --gft-card-bg-soft: linear-gradient(180deg,rgba(12,23,38,.98) 0%,rgba(15,23,42,.96) 100%);
        --gft-border: rgba(76,167,168,.18);
        --gft-text: #f8fafc;
        --gft-muted: #9fb2c3;
        --gft-label: #8fd6d7;
        --gft-input-bg: rgba(15,23,42,.92);
        --gft-input-text: #f8fafc;
        --gft-hero-1: #071427;
        --gft-hero-2: #10243d;
        --gft-hero-3: #0f766e;
        --gft-shadow: 0 14px 30px rgba(0,0,0,.28);
    }

    .fi-main,
    .fi-main-ctn {
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.09), transparent 30%),
            linear-gradient(180deg, var(--gft-page-bg) 0%, var(--gft-page-bg) 100%) !important;
        transition: background .25s ease !important;
    }

    .gft-wrap,
    .global-finance-wrap,
    .global-finance-page,
    .finance-totals-page {
        color: var(--gft-text) !important;
        transition: color .25s ease !important;
    }

    /* Hero */
    .gft-hero,
    .global-finance-hero,
    .finance-totals-hero,
    .sada-finance-hero {
        background:
            radial-gradient(circle at 90% 10%, rgba(76,167,168,.22), transparent 32%),
            linear-gradient(135deg,var(--gft-hero-1) 0%,var(--gft-hero-2) 52%,var(--gft-hero-3) 100%) !important;
        border-color: var(--gft-border) !important;
        box-shadow: var(--gft-shadow) !important;
        transition: background .25s ease, border-color .25s ease, box-shadow .25s ease !important;
    }

    .gft-hero h1,
    .global-finance-hero h1,
    .finance-totals-hero h1,
    .sada-finance-hero h1,
    .gft-title,
    .global-finance-title,
    .finance-totals-title {
        color: #ffffff !important;
    }

    .gft-hero p,
    .global-finance-hero p,
    .finance-totals-hero p,
    .sada-finance-hero p,
    .gft-subtitle,
    .global-finance-subtitle,
    .finance-totals-subtitle {
        color: rgba(255,255,255,.82) !important;
    }

    /* Cards / sections / filters */
    .fi-section,
    .fi-fo,
    .gft-filter-card,
    .gft-settings-card,
    .global-finance-filter-card,
    .finance-filter-card,
    .finance-settings-card,
    .gft-summary-card,
    .finance-summary-card,
    .global-finance-summary-card,
    .gft-total-card,
    .finance-total-card {
        background: var(--gft-card-bg-soft) !important;
        border-color: var(--gft-border) !important;
        box-shadow: var(--gft-shadow) !important;
        color: var(--gft-text) !important;
        transition: background .25s ease, border-color .25s ease, color .25s ease, box-shadow .25s ease !important;
    }

    .fi-section-header,
    .fi-section-content,
    .fi-fo-component-ctn {
        background: transparent !important;
    }

    .fi-section-header {
        border-bottom-color: var(--gft-border) !important;
    }

    .fi-section-header-heading,
    .fi-section-header h2,
    .fi-section-header h3,
    .gft-summary-card h3,
    .finance-summary-card h3,
    .global-finance-summary-card h3,
    .gft-total-card h3,
    .finance-total-card h3 {
        color: var(--gft-label) !important;
    }

    .gft-summary-card strong,
    .finance-summary-card strong,
    .global-finance-summary-card strong,
    .gft-total-card strong,
    .finance-total-card strong,
    .gft-value,
    .finance-value {
        color: var(--gft-text) !important;
    }

    .gft-summary-card p,
    .finance-summary-card p,
    .global-finance-summary-card p,
    .gft-total-card p,
    .finance-total-card p,
    .fi-fo-field-wrp-hint,
    .fi-fo-field-wrp-helper-text {
        color: var(--gft-muted) !important;
    }

    /* Inputs */
    .fi-input-wrp,
    .fi-select,
    .fi-select-input,
    .fi-input,
    select,
    input {
        background: var(--gft-input-bg) !important;
        color: var(--gft-input-text) !important;
        border-color: var(--gft-border) !important;
        transition: background .25s ease, color .25s ease, border-color .25s ease !important;
    }

    .fi-input::placeholder,
    input::placeholder {
        color: var(--gft-muted) !important;
    }

    .fi-fo-field-wrp-label,
    .fi-fo-field-wrp-label span,
    label {
        color: var(--gft-label) !important;
    }

    /* Tables */
    .fi-ta {
        background: var(--gft-card-bg) !important;
        border-color: var(--gft-border) !important;
        box-shadow: var(--gft-shadow) !important;
    }

    .fi-ta thead th {
        background: color-mix(in srgb, var(--gft-card-bg) 86%, #4ca7a8 14%) !important;
        color: var(--gft-label) !important;
    }

    .fi-ta tbody td {
        background: var(--gft-card-bg) !important;
        color: var(--gft-text) !important;
        border-color: var(--gft-border) !important;
    }

    .fi-ta tbody tr:hover td {
        background: color-mix(in srgb, var(--gft-card-bg) 88%, #4ca7a8 12%) !important;
    }

    /* Buttons remain readable in both modes */
    .fi-btn-color-warning,
    .fi-btn-color-primary,
    button[type="submit"] {
        background: #f2b705 !important;
        color: #3b2a00 !important;
        border-color: #f2b705 !important;
    }

    .fi-btn-color-success {
        background: #10b981 !important;
        color: #052e2b !important;
        border-color: #10b981 !important;
    }

    .fi-btn-color-danger {
        background: #ef4444 !important;
        color: #fff !important;
        border-color: #ef4444 !important;
    }

    .fi-btn-color-gray {
        background: color-mix(in srgb, var(--gft-card-bg) 78%, #94a3b8 22%) !important;
        color: var(--gft-text) !important;
        border-color: var(--gft-border) !important;
    }

    /* Force old dark blocks to become dynamic */
    [style*="background:#081a34"],
    [style*="background: #081a34"],
    [style*="background:#0f172a"],
    [style*="background: #0f172a"],
    [style*="background:#10243d"],
    [style*="background: #10243d"] {
        background: var(--gft-card-bg-soft) !important;
        color: var(--gft-text) !important;
        border-color: var(--gft-border) !important;
    }

    /* FINAL Global Finance Totals Light Filter Fix */
    .gft-wrap,
    .global-finance-wrap,
    .global-finance-page,
    .finance-totals-page {
        width: min(100%, 1240px) !important;
        max-width: 1240px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    /*
     * Force filter/settings blocks to be LIGHT in day mode.
     * This fixes the navy/night filter panels showing in day mode.
     */
    .gft-wrap .fi-section,
    .global-finance-wrap .fi-section,
    .finance-totals-page .fi-section,
    .gft-wrap .fi-fo,
    .global-finance-wrap .fi-fo,
    .finance-totals-page .fi-fo,
    .gft-filter-card,
    .gft-settings-card,
    .global-finance-filter-card,
    .finance-filter-card,
    .finance-settings-card,
    .gft-wrap [class*="filter"],
    .global-finance-wrap [class*="filter"],
    .finance-totals-page [class*="filter"],
    .gft-wrap [class*="settings"],
    .global-finance-wrap [class*="settings"],
    .finance-totals-page [class*="settings"] {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
        color: #0f172a !important;
        border: 1px solid #d7e2e5 !important;
        border-radius: 22px !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .045) !important;
    }

    .gft-wrap .fi-section-header,
    .global-finance-wrap .fi-section-header,
    .finance-totals-page .fi-section-header,
    .gft-wrap [class*="header"],
    .global-finance-wrap [class*="header"],
    .finance-totals-page [class*="header"] {
        background: transparent !important;
        color: #0f172a !important;
        border-bottom-color: #e4ecef !important;
    }

    .gft-wrap .fi-section-header-heading,
    .global-finance-wrap .fi-section-header-heading,
    .finance-totals-page .fi-section-header-heading,
    .gft-wrap h2,
    .gft-wrap h3,
    .global-finance-wrap h2,
    .global-finance-wrap h3,
    .finance-totals-page h2,
    .finance-totals-page h3 {
        color: #1f4664 !important;
        font-weight: 950 !important;
    }

    .gft-wrap label,
    .global-finance-wrap label,
    .finance-totals-page label,
    .gft-wrap .fi-fo-field-wrp-label,
    .global-finance-wrap .fi-fo-field-wrp-label,
    .finance-totals-page .fi-fo-field-wrp-label,
    .gft-wrap .fi-fo-field-wrp-label span,
    .global-finance-wrap .fi-fo-field-wrp-label span,
    .finance-totals-page .fi-fo-field-wrp-label span {
        color: #334155 !important;
        font-weight: 850 !important;
    }

    .gft-wrap .fi-input-wrp,
    .global-finance-wrap .fi-input-wrp,
    .finance-totals-page .fi-input-wrp,
    .gft-wrap .fi-select,
    .global-finance-wrap .fi-select,
    .finance-totals-page .fi-select,
    .gft-wrap input,
    .global-finance-wrap input,
    .finance-totals-page input,
    .gft-wrap select,
    .global-finance-wrap select,
    .finance-totals-page select {
        background: #ffffff !important;
        color: #0f172a !important;
        border-color: #d7e2e5 !important;
        border-radius: 14px !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .035) !important;
    }

    .gft-wrap input::placeholder,
    .global-finance-wrap input::placeholder,
    .finance-totals-page input::placeholder {
        color: #94a3b8 !important;
    }

    .gft-wrap .fi-fo-field-wrp-hint,
    .global-finance-wrap .fi-fo-field-wrp-hint,
    .finance-totals-page .fi-fo-field-wrp-hint,
    .gft-wrap p,
    .global-finance-wrap p,
    .finance-totals-page p {
        color: #64748b !important;
    }

    /*
     * Better Material cards for the rest of the page.
     */
    .gft-summary-card,
    .finance-summary-card,
    .global-finance-summary-card,
    .gft-total-card,
    .finance-total-card,
    .gft-wrap .fi-ta,
    .global-finance-wrap .fi-ta,
    .finance-totals-page .fi-ta {
        border-radius: 22px !important;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
        border: 1px solid #d7e2e5 !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .045) !important;
        color: #0f172a !important;
    }

    .gft-wrap table thead th,
    .global-finance-wrap table thead th,
    .finance-totals-page table thead th {
        background: #eef5f8 !important;
        color: #1f4664 !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .1em !important;
        text-transform: uppercase !important;
    }

    .gft-wrap table tbody td,
    .global-finance-wrap table tbody td,
    .finance-totals-page table tbody td {
        background: #ffffff !important;
        color: #0f172a !important;
        border-color: #eef2f7 !important;
    }

    .gft-wrap table tbody tr:hover td,
    .global-finance-wrap table tbody tr:hover td,
    .finance-totals-page table tbody tr:hover td {
        background: #f8fcfd !important;
    }

    .gft-wrap .fi-btn,
    .global-finance-wrap .fi-btn,
    .finance-totals-page .fi-btn {
        border-radius: 999px !important;
        font-weight: 900 !important;
    }

    .gft-wrap button[type="submit"],
    .global-finance-wrap button[type="submit"],
    .finance-totals-page button[type="submit"],
    .gft-wrap .fi-btn-color-warning,
    .global-finance-wrap .fi-btn-color-warning,
    .finance-totals-page .fi-btn-color-warning,
    .gft-wrap .fi-btn-color-primary,
    .global-finance-wrap .fi-btn-color-primary,
    .finance-totals-page .fi-btn-color-primary {
        background: #f2b705 !important;
        color: #3b2a00 !important;
        border-color: #f2b705 !important;
        box-shadow: 0 10px 20px rgba(242, 183, 5, .18) !important;
    }

    /*
     * Night mode: only when the app is actually dark.
     */
    .dark .gft-wrap .fi-section,
    .dark .global-finance-wrap .fi-section,
    .dark .finance-totals-page .fi-section,
    .dark .gft-wrap .fi-fo,
    .dark .global-finance-wrap .fi-fo,
    .dark .finance-totals-page .fi-fo,
    .dark .gft-filter-card,
    .dark .gft-settings-card,
    .dark .global-finance-filter-card,
    .dark .finance-filter-card,
    .dark .finance-settings-card,
    .dark .gft-wrap [class*="filter"],
    .dark .global-finance-wrap [class*="filter"],
    .dark .finance-totals-page [class*="filter"],
    .dark .gft-wrap [class*="settings"],
    .dark .global-finance-wrap [class*="settings"],
    .dark .finance-totals-page [class*="settings"],
    .dark .gft-summary-card,
    .dark .finance-summary-card,
    .dark .global-finance-summary-card,
    .dark .gft-total-card,
    .dark .finance-total-card,
    .dark .gft-wrap .fi-ta,
    .dark .global-finance-wrap .fi-ta,
    .dark .finance-totals-page .fi-ta {
        background: linear-gradient(180deg, rgba(12, 23, 38, .98) 0%, rgba(15, 23, 42, .96) 100%) !important;
        color: #f8fafc !important;
        border-color: rgba(76, 167, 168, .18) !important;
        box-shadow: 0 14px 30px rgba(0, 0, 0, .28) !important;
    }

    .dark .gft-wrap h2,
    .dark .gft-wrap h3,
    .dark .global-finance-wrap h2,
    .dark .global-finance-wrap h3,
    .dark .finance-totals-page h2,
    .dark .finance-totals-page h3,
    .dark .gft-wrap label,
    .dark .global-finance-wrap label,
    .dark .finance-totals-page label,
    .dark .gft-wrap .fi-fo-field-wrp-label,
    .dark .global-finance-wrap .fi-fo-field-wrp-label,
    .dark .finance-totals-page .fi-fo-field-wrp-label,
    .dark .gft-wrap .fi-fo-field-wrp-label span,
    .dark .global-finance-wrap .fi-fo-field-wrp-label span,
    .dark .finance-totals-page .fi-fo-field-wrp-label span {
        color: #8fd6d7 !important;
    }

    .dark .gft-wrap input,
    .dark .global-finance-wrap input,
    .dark .finance-totals-page input,
    .dark .gft-wrap select,
    .dark .global-finance-wrap select,
    .dark .finance-totals-page select,
    .dark .gft-wrap .fi-input-wrp,
    .dark .global-finance-wrap .fi-input-wrp,
    .dark .finance-totals-page .fi-input-wrp,
    .dark .gft-wrap .fi-select,
    .dark .global-finance-wrap .fi-select,
    .dark .finance-totals-page .fi-select {
        background: rgba(15, 23, 42, .92) !important;
        color: #f8fafc !important;
        border-color: rgba(76, 167, 168, .18) !important;
    }

    .dark .gft-wrap p,
    .dark .global-finance-wrap p,
    .dark .finance-totals-page p,
    .dark .finance-totals-page .fi-fo-field-wrp-helper-text {
        color: #9fb2c3 !important;
    }

    /* FINAL GFT HARD LIGHT FORM OVERRIDE */
    html:not(.dark) .fi-page:has(.gft-wrap) .fi-section,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-section,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-section,
    html:not(.dark) .fi-page:has(.gft-wrap) .fi-section *,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-section *,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-section * {
        color: #0f172a !important;
    }

    html:not(.dark) .fi-page:has(.gft-wrap) .fi-section,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-section,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-section {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
        border: 1px solid #d7e2e5 !important;
        border-radius: 22px !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .045) !important;
        overflow: hidden !important;
    }

    html:not(.dark) .fi-page:has(.gft-wrap) .fi-section-header,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-section-header,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-section-header {
        background: #f4f8fa !important;
        border-bottom: 1px solid #d7e2e5 !important;
    }

    html:not(.dark) .fi-page:has(.gft-wrap) .fi-section-header-heading,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-section-header-heading,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-section-header-heading {
        color: #1f4664 !important;
        font-weight: 950 !important;
    }

    html:not(.dark) .fi-page:has(.gft-wrap) .fi-section-content,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-section-content,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-section-content {
        background: transparent !important;
    }

    html:not(.dark) .fi-page:has(.gft-wrap) .fi-input-wrp,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-input-wrp,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-input-wrp,
    html:not(.dark) .fi-page:has(.gft-wrap) .fi-select,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-select,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-select {
        background: #ffffff !important;
        border: 1px solid #d7e2e5 !important;
        border-radius: 14px !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .035) !important;
    }

    html:not(.dark) .fi-page:has(.gft-wrap) input,
    html:not(.dark) .fi-page:has(.global-finance-wrap) input,
    html:not(.dark) .fi-page:has(.finance-totals-page) input,
    html:not(.dark) .fi-page:has(.gft-wrap) select,
    html:not(.dark) .fi-page:has(.global-finance-wrap) select,
    html:not(.dark) .fi-page:has(.finance-totals-page) select {
        background: #ffffff !important;
        color: #0f172a !important;
    }

    html:not(.dark) .fi-page:has(.gft-wrap) label,
    html:not(.dark) .fi-page:has(.global-finance-wrap) label,
    html:not(.dark) .fi-page:has(.finance-totals-page) label,
    html:not(.dark) .fi-page:has(.gft-wrap) .fi-fo-field-wrp-label span,
    html:not(.dark) .fi-page:has(.global-finance-wrap) .fi-fo-field-wrp-label span,
    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-fo-field-wrp-label span {
        color: #334155 !important;
        font-weight: 850 !important;
    }

    html:not(.dark) .fi-page:has(.finance-totals-page) .fi-fo-field-wrp-helper-text {
        color: #64748b !important;
    }

    .dark .fi-page:has(.gft-wrap) .fi-section,
    .dark .fi-page:has(.global-finance-wrap) .fi-section,
    .dark .fi-page:has(.finance-totals-page) .fi-section {
        background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
        border-color: rgba(76,167,168,.18) !important;
        color: #f8fafc !important;
    }

        /* GF FILTER PANEL SOURCE FIX FINAL */
        html:not(.dark) .gf-filter-panel .fi-section,
        html:not(.dark) .gf-filter-panel .fi-section-content {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            color: #0f172a !important;
        }

        html:not(.dark) .gf-filter-panel .fi-section-header {
            background: #f4f8fa !important;
            color: #1f4664 !important;
            border-bottom: 1px solid #d7e2e5 !important;
        }

        html:not(.dark) .gf-filter-panel .fi-section-header-heading,
        html:not(.dark) .gf-filter-panel label,
        html:not(.dark) .gf-filter-panel .fi-fo-field-wrp-label span {
            color: #1f4664 !important;
        }

        html:not(.dark) .gf-filter-panel .fi-input-wrp,
        html:not(.dark) .gf-filter-panel .fi-select,
        html:not(.dark) .gf-filter-panel input,
        html:not(.dark) .gf-filter-panel select {
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #d7e2e5 !important;
        }

        .dark .gf-filter-panel .fi-section,
        .dark .gf-filter-panel .fi-section-content {
            background: linear-gradient(180deg, rgba(12, 23, 38, .98) 0%, rgba(15, 23, 42, .96) 100%) !important;
            color: #f8fafc !important;
        }

        .dark .gf-filter-panel .fi-section-header {
            background: rgba(15, 23, 42, .92) !important;
            border-bottom-color: rgba(76, 167, 168, .18) !important;
        }

        .dark .gf-filter-panel .fi-section-header-heading,
        .dark .gf-filter-panel label,
        .dark .gf-filter-panel .fi-fo-field-wrp-label span {
            color: #8fd6d7 !important;
        }

        /* FINAL GFT MONTH EMPLOYEE TEMP SPACE FIX */
        .gf-filter-panel,
        .gf-filter-panel .fi-section,
        .gf-filter-panel .fi-section-content,
        .gf-filter-panel .fi-fo,
        .gf-filter-panel .fi-fo-component-ctn,
        .gf-filter-panel form {
            overflow: visible !important;
        }

        .gf-filter-panel {
            position: relative !important;
            z-index: 80 !important;
            transition: margin-bottom .16s ease !important;
        }

        /*
         * Only when Month or Employee select is opened:
         * push Exchange & Print Settings down temporarily.
         * Client and Project stay exactly as they are.
         */
        .gf-filter-panel.gft-month-employee-open {
            margin-bottom: 230px !important;
            z-index: 999 !important;
        }

        .gf-filter-panel.gft-month-employee-open .fi-section,
        .gf-filter-panel.gft-month-employee-open .fi-section-content,
        .gf-filter-panel.gft-month-employee-open .fi-fo-field-wrp,
        .gf-filter-panel.gft-month-employee-open .choices,
        .gf-filter-panel.gft-month-employee-open [role="combobox"] {
            overflow: visible !important;
            z-index: 9999 !important;
        }

        .gf-filter-panel.gft-month-employee-open .choices__list--dropdown,
        .gf-filter-panel.gft-month-employee-open .choices__list[aria-expanded],
        .gf-filter-panel.gft-month-employee-open [role="listbox"] {
            z-index: 999999 !important;
            max-height: 320px !important;
            overflow-y: auto !important;
            border-radius: 14px !important;
            box-shadow: 0 22px 45px rgba(15, 23, 42, .22) !important;
        }

        .gf-filter-panel.gft-month-employee-open + *,
        .gf-filter-panel.gft-month-employee-open ~ * {
            position: relative !important;
            z-index: 1 !important;
        }


    .gf-mini-stack {
        padding-top: 10px;
        border-top: 1px dashed rgba(148, 163, 184, .35);
    }

    .gf-mini-label {
        color: #94a3b8;
        font-size: 13px;
        font-weight: 750;
        margin-bottom: 4px;
    }

    .gf-mini-value {
        color: #0f172a;
        font-size: 14px;
        font-weight: 900;
    }

    .dark .gf-mini-value {
        color: #e5e7eb;
    }

</style>

    <div class="gf-wrap">
        <section class="gf-hero">
            <div class="gf-hero-row">
                <div>
                    <div class="gf-title">Global Finance Totals</div>

                    <div class="gf-chip-row">
                        @foreach($exchangeRates as $currency => $rate)
                            <div class="gf-rate-chip">
                                1 {{ $currency }} = {{ $formatMoney($rate) }} {{ $baseCurrency }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="gf-actions">
                    <x-filament::actions :actions="$this->getCachedHeaderActions()" />
                </div>
            </div>
        </section>

        <section class="gf-panel gf-filter-panel">
            <form wire:submit="applyFilters" style="display:flex; flex-direction:column; gap:18px;">
                {{ $this->form }}

                <div class="gf-filter-actions">
                    <x-filament::button type="submit" color="primary">
                        Apply Filters
                    </x-filament::button>
                </div>
            </form>
        </section>

        <section class="gf-kpi-grid">
            <div class="gf-kpi-card gf-kpi-card--revenue">
                <div class="gf-kpi-title">Total Revenue</div>
                {!! $renderCurrencyStack($revenueByCurrency, 'No revenue yet') !!}
                <div style="margin-top:16px; font-size:13px; color:#94a3b8;">Range: {{ $rangeText }}</div>
            </div>

            <div class="gf-kpi-card gf-kpi-card--salary">
                <div class="gf-kpi-title">Total Salary Cost</div>
                {!! $renderCurrencyStack($salaryCostByCurrency, 'No salary cost yet') !!}

                @if(!empty($salaryApprovedByCurrency))
                    <div class="gf-mini-stack" style="margin-top: 16px;">
                        <div class="gf-mini-label">Approved / not paid yet:</div>
                        <div class="gf-mini-value">{!! $renderCurrencyStack($salaryApprovedByCurrency, '-') !!}</div>
                    </div>
                @endif

                @if(!empty($salaryDraftByCurrency))
                    <div class="gf-mini-stack" style="margin-top: 10px;">
                        <div class="gf-mini-label">Draft / not included:</div>
                        <div class="gf-mini-value">{!! $renderCurrencyStack($salaryDraftByCurrency, '-') !!}</div>
                    </div>
                @endif
                <div style="margin-top:16px; font-size:13px; color:#94a3b8;">
                    Paid: {!! $renderCompactMap($salaryPaidByCurrency) !!}
                    <div style="margin-top:8px;">
                        Remaining: {!! $renderCompactMap($salaryRemainingByCurrency) !!}
                    </div>
                </div>
            </div>

            <div class="gf-kpi-card gf-kpi-card--expenses">
                <div class="gf-kpi-title">Total Expenses</div>
                {!! $renderCurrencyStack($expensesByCurrency, 'No expenses yet') !!}
                <div style="margin-top:16px; font-size:13px; color:#94a3b8;">By filtered scope and date range</div>
            </div>

            <div class="gf-kpi-card gf-kpi-card--net">
                <div class="gf-kpi-title">Net Result</div>
                {!! $renderCurrencyStack($netByCurrency, 'No net result yet', true) !!}
                <div style="margin-top:16px; font-size:13px; color:#94a3b8;">Revenue - Salary Cost - Expenses</div>
            </div>
        </section>

        <section class="gf-summary-dark">
            <div class="gf-summary-dark-head">
                <div>
                    <div class="gf-summary-dark-title">Converted Executive Summary</div>
                    <div class="gf-summary-dark-sub">
                        Final management view after conversion into {{ $baseCurrency }} using the selected exchange rates.
                    </div>
                </div>

                <div class="gf-base-chip">
                    Base Currency: {{ $baseCurrency }}
                </div>
            </div>

            <div class="gf-summary-grid">
                <div class="gf-summary-box">
                    <div class="gf-summary-label">Converted Revenue</div>
                    <div class="gf-summary-value">{{ $formatMoney($convertedRevenue) }} {{ $baseCurrency }}</div>
                </div>

                <div class="gf-summary-box">
                    <div class="gf-summary-label">Converted Salary Cost</div>
                    <div class="gf-summary-value">{{ $formatMoney($convertedSalary) }} {{ $baseCurrency }}</div>
                </div>

                <div class="gf-summary-box">
                    <div class="gf-summary-label">Converted Expenses</div>
                    <div class="gf-summary-value">{{ $formatMoney($convertedExpenses) }} {{ $baseCurrency }}</div>
                </div>

                <div class="gf-summary-box">
                    <div class="gf-summary-label">Converted Net Result</div>
                    <div class="gf-summary-value" style="color: {{ $convertedNet >= 0 ? '#4ade80' : '#f87171' }};">
                        {{ $formatMoney($convertedNet) }} {{ $baseCurrency }}
                    </div>
                </div>
            </div>
        </section>

        <section class="gf-panel">
            <div class="gf-section-title-chip">Breakdown by Currency</div>
            <div class="gf-section-subtitle">Revenue, salary cost, expenses, and net result by currency.</div>

            <div class="gf-table-wrap">
                <table class="gf-table">
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Revenue</th>
                            <th>Salary Cost</th>
                            <th>Expenses</th>
                            <th>Net Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byCurrency as $row)
                            <tr>
                                <td class="gf-row-title">{{ $row['currency'] ?? '-' }}</td>
                                <td class="gf-text-strong">{{ $formatMoney($row['revenue_total'] ?? 0) }}</td>
                                <td class="gf-text-strong">{{ $formatMoney($row['salary_cost_total'] ?? 0) }}</td>
                                <td class="gf-text-strong">{{ $formatMoney($row['expenses_total'] ?? 0) }}</td>
                                <td class="{{ ((float) ($row['net_result_total'] ?? 0) >= 0) ? 'gf-positive' : 'gf-negative' }}">
                                    {{ $formatMoney($row['net_result_total'] ?? 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="gf-empty-text">No currency breakdown found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="gf-panel">
            <div class="gf-section-title-chip">Breakdown by Client</div>
            <div class="gf-section-subtitle">Revenue, salary cost, expenses, and net result for each client by currency.</div>

            <div class="gf-table-wrap">
                <table class="gf-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Revenue</th>
                            <th>Salary Cost</th>
                            <th>Expenses</th>
                            <th>Net Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byClient as $row)
                            <tr>
                                <td class="gf-row-title">{{ $row['client_name'] ?? '-' }}</td>
                                <td>{!! $renderCompactMap($row['revenue_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['salary_cost_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['expenses_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['net_by_currency'] ?? [], true) !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="gf-empty-text">No client breakdown found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="gf-panel">
            <div class="gf-section-title-chip">Breakdown by Project</div>
            <div class="gf-section-subtitle">Revenue, salary cost, expenses, and net result for each project by currency.</div>

            <div class="gf-table-wrap">
                <table class="gf-table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Revenue</th>
                            <th>Salary Cost</th>
                            <th>Expenses</th>
                            <th>Net Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byProject as $row)
                            <tr>
                                <td class="gf-row-title">{{ $row['project_name'] ?? '-' }}</td>
                                <td>{!! $renderCompactMap($row['revenue_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['salary_cost_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['expenses_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['net_by_currency'] ?? [], true) !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="gf-empty-text">No project breakdown found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="gf-panel">
            <div class="gf-section-title-chip">Breakdown by Employee</div>
            <div class="gf-section-subtitle">Revenue, salary cost, expenses, and net result for each employee by currency.</div>

            <div class="gf-table-wrap">
                <table class="gf-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Revenue</th>
                            <th>Salary Cost</th>
                            <th>Expenses</th>
                            <th>Net Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byEmployee as $row)
                            <tr>
                                <td class="gf-row-title">{{ $row['employee_name'] ?? '-' }}</td>
                                <td class="gf-text-strong">{{ $row['position_title'] ?? '-' }}</td>
                                <td>{!! $renderCompactMap($row['revenue_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['salary_cost_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['expenses_by_currency'] ?? []) !!}</td>
                                <td>{!! $renderCompactMap($row['net_by_currency'] ?? [], true) !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="gf-empty-text">No employee breakdown found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.querySelector('.gf-filter-panel');
    if (!panel) return;

    function fieldWrapperFromTarget(target) {
        return target.closest('.fi-fo-field-wrp, [class*="field-wrp"], [class*="field"]');
    }

    function fieldLabel(wrapper) {
        if (!wrapper) return '';
        const label = wrapper.querySelector('label, .fi-fo-field-wrp-label, [class*="label"]');
        return (label ? label.textContent : wrapper.textContent || '').trim().toLowerCase();
    }

    function isMonthOrEmployee(wrapper) {
        const text = fieldLabel(wrapper);
        return text.includes('month') || text.includes('employee');
    }

    function openSpaceFor(target) {
        const wrapper = fieldWrapperFromTarget(target);
        if (isMonthOrEmployee(wrapper)) {
            panel.classList.add('gft-month-employee-open');
        } else {
            panel.classList.remove('gft-month-employee-open');
        }
    }

    panel.addEventListener('mousedown', function (event) {
        openSpaceFor(event.target);
    }, true);

    panel.addEventListener('focusin', function (event) {
        openSpaceFor(event.target);
    }, true);

    document.addEventListener('mousedown', function (event) {
        if (!panel.contains(event.target)) {
            panel.classList.remove('gft-month-employee-open');
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            panel.classList.remove('gft-month-employee-open');
        }
    });

    document.addEventListener('change', function () {
        setTimeout(function () {
            panel.classList.remove('gft-month-employee-open');
        }, 120);
    }, true);
});
</script>

</x-filament-panels::page>


<style id="sf-candidate-request-decision-colors">
    /*
     * Colored decision buttons — visual only.
     */

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]) {
        overflow: hidden !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
        border-color: rgba(34,197,94,.42) !important;
        color: #047857 !important;
        box-shadow: 0 12px 28px rgba(34,197,94,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: linear-gradient(135deg, #fef2f2, #fee2e2) !important;
        border-color: rgba(239,68,68,.38) !important;
        color: #b91c1c !important;
        box-shadow: 0 12px 28px rgba(239,68,68,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
        border-color: rgba(249,115,22,.38) !important;
        color: #c2410c !important;
        box-shadow: 0 12px 28px rgba(249,115,22,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]:checked) {
        transform: translateY(-1px) !important;
        filter: saturate(1.12) !important;
        box-shadow: 0 0 0 5px rgba(37,99,235,.10), 0 18px 38px rgba(15,23,42,.12) !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: rgba(6,78,59,.55) !important;
        border-color: rgba(52,211,153,.34) !important;
        color: #a7f3d0 !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: rgba(127,29,29,.48) !important;
        border-color: rgba(248,113,113,.34) !important;
        color: #fecaca !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: rgba(124,45,18,.48) !important;
        border-color: rgba(251,146,60,.34) !important;
        color: #fed7aa !important;
    }
</style>

