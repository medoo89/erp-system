@php
    $summary = $totals['summary'] ?? [];
    $byClient = $totals['by_client'] ?? [];
    $byProject = $totals['by_project'] ?? [];
    $byEmployee = $totals['by_employee'] ?? [];

    $baseCurrency = $baseCurrency ?? strtoupper((string) request('base_currency', 'EUR'));
    $exchangeRates = $exchangeRates ?? [
        'USD' => (float) request('rate_usd', 0),
        'EUR' => (float) request('rate_eur', 1),
        'LYD' => (float) request('rate_lyd', 0),
        'GBP' => (float) request('rate_gbp', 0),
    ];

    $generatedAt = $generatedAt ?? now();
    $filters = $filters ?? [];

    $money = function ($amount, $currency = null) {
        if ($amount === null || $amount === '') {
            return '—';
        }

        return number_format((float) $amount, 2) . ($currency ? ' ' . $currency : '');
    };

    $currencyMapText = function ($map) use ($money) {
        if (empty($map) || ! is_array($map)) {
            return '—';
        }

        return collect($map)
            ->filter(fn ($amount) => (float) $amount != 0.0)
            ->map(fn ($amount, $currency) => $money($amount, strtoupper((string) $currency)))
            ->implode(' | ') ?: '—';
    };

    $revenueMap = $summary['revenue_by_currency'] ?? [];
    $salaryCostMap = $summary['salary_cost_by_currency'] ?? [];
    $salaryPaidMap = $summary['salary_paid_by_currency'] ?? [];
    $salaryRemainingMap = $summary['salary_remaining_by_currency'] ?? [];
    $expensesMap = $summary['expenses_by_currency'] ?? [];
    $netMap = $summary['net_by_currency'] ?? [];

    $convertedSummary = $convertedSummary ?? [
        'revenue_total' => null,
        'salary_cost_total' => null,
        'expenses_total' => null,
        'net_total' => null,
    ];

    $logoUrl = file_exists(public_path('images/sada-horizontal.png'))
        ? asset('images/sada-horizontal.png')
        : (file_exists(public_path('portal-assets/sada-fezzan-logo-white.jpeg'))
            ? asset('portal-assets/sada-fezzan-logo-white.jpeg')
            : null);

    $monthName = ! empty($filters['month'])
        ? \Carbon\Carbon::createFromDate((int) ($filters['year'] ?? now()->year), (int) $filters['month'], 1)->format('F')
        : 'All Months';

    $yearLabel = $filters['year'] ?? now()->year;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Global Finance Totals Report</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 11mm 10mm 12mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #ffffff;
            color: #0f172a;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.42;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            width: 100%;
        }

        .hero {
            border: 1px solid #d7e2e5;
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #f8fbff 0%, #eef7f8 100%);
        }

        .hero-top {
            height: 5px;
            background: linear-gradient(90deg, #1f4664, #4ca7a8, #b38b2f);
        }

        .hero-body {
            display: table;
            width: 100%;
            padding: 14px 16px;
        }

        .brand,
        .report-meta {
            display: table-cell;
            vertical-align: middle;
        }

        .brand {
            width: 58%;
        }

        .report-meta {
            width: 42%;
            text-align: right;
        }

        .brand-row {
            display: table;
            width: 100%;
        }

        .logo,
        .brand-text {
            display: table-cell;
            vertical-align: middle;
        }

        .logo {
            width: 76px;
        }

        .logo-box {
            width: 64px;
            height: 64px;
            border-radius: 14px;
            border: 1px solid #d7e2e5;
            background: #ffffff;
            text-align: center;
            overflow: hidden;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .company {
            font-size: 23px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: #0f213a;
        }

        .subtitle {
            margin-top: 5px;
            font-size: 9.5px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #1f4664;
        }

        .report-title {
            font-size: 25px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.04em;
            color: #0f213a;
        }

        .meta-line {
            margin-top: 4px;
            font-size: 10px;
            color: #475569;
            font-weight: 700;
        }

        .meta-line strong {
            color: #0f172a;
        }

        .chips {
            margin: 8px 0 10px;
        }

        .chip {
            display: inline-block;
            padding: 6px 9px;
            margin-right: 4px;
            margin-bottom: 4px;
            border-radius: 999px;
            background: #eef7f8;
            border: 1px solid #d7e2e5;
            color: #1f4664;
            font-weight: 900;
            font-size: 9px;
        }

        .grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 10px;
        }

        .col-25 {
            width: 25%;
            vertical-align: top;
        }

        .card {
            border: 1px solid #d7e2e5;
            border-radius: 15px;
            background: #ffffff;
            overflow: hidden;
            min-height: 82px;
        }

        .card-accent {
            height: 4px;
            background: linear-gradient(90deg, #1f4664, #4ca7a8);
        }

        .card-body {
            padding: 11px 12px;
        }

        .label {
            font-size: 8.7px;
            font-weight: 900;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
        }

        .value {
            font-size: 15px;
            line-height: 1.15;
            font-weight: 900;
            color: #0f172a;
        }

        .muted {
            margin-top: 6px;
            font-size: 8.8px;
            color: #64748b;
        }

        .summary-band {
            margin-top: 10px;
            border-radius: 18px;
            padding: 14px;
            background: linear-gradient(135deg, #10243d 0%, #1f4664 58%, #2f6f73 100%);
            color: #ffffff;
        }

        .summary-title {
            font-size: 19px;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .summary-sub {
            margin-top: 4px;
            color: rgba(255,255,255,.78);
            font-size: 9.5px;
        }

        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-top: 10px;
        }

        .summary-box {
            width: 25%;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 14px;
            background: rgba(255,255,255,.08);
            padding: 10px;
        }

        .summary-box-label {
            font-size: 8.4px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,.72);
        }

        .summary-box-value {
            margin-top: 7px;
            font-size: 17px;
            font-weight: 900;
            color: #ffffff;
        }

        .section {
            margin-top: 10px;
            border: 1px solid #d7e2e5;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .section-head {
            padding: 9px 12px;
            background: #edf4fa;
            border-bottom: 1px solid #d7e2e5;
            font-size: 9.2px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #1f4664;
        }

        .section-body {
            padding: 10px 12px;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.report-table th {
            padding: 8px 9px;
            background: #f4f8fa;
            color: #64748b;
            text-align: left;
            font-size: 8.3px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
            border-bottom: 1px solid #e4ecef;
        }

        table.report-table td {
            padding: 8px 9px;
            border-bottom: 1px solid #eef2f7;
            color: #0f172a;
            font-weight: 700;
            vertical-align: top;
        }

        table.report-table tr:last-child td {
            border-bottom: none;
        }

        .sign-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-top: 14px;
        }

        .sign-card {
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 11px;
            min-height: 72px;
        }

        .sign-title {
            font-size: 9.5px;
            font-weight: 900;
            color: #1f4664;
            margin-bottom: 26px;
        }

        .sign-line {
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
            font-size: 9px;
            color: #64748b;
        }

        .footer {
            margin-top: 10px;
            font-size: 8.5px;
            color: #64748b;
        }

        @media print {
            .section,
            .card,
            .summary-band {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="hero">
            <div class="hero-top"></div>
            <div class="hero-body">
                <div class="brand">
                    <div class="brand-row">
                        <div class="logo">
                            <div class="logo-box">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Sada Fezzan Logo">
                                @endif
                            </div>
                        </div>

                        <div class="brand-text">
                            <div class="company">Sada Fezzan</div>
                            <div class="subtitle">For Oil Services</div>
                        </div>
                    </div>
                </div>

                <div class="report-meta">
                    <div class="report-title">Finance Report</div>
                    <div class="meta-line"><strong>Year:</strong> {{ $yearLabel }}</div>
                    <div class="meta-line"><strong>Month:</strong> {{ $monthName }}</div>
                    <div class="meta-line"><strong>Base Currency:</strong> {{ $baseCurrency }}</div>
                    <div class="meta-line"><strong>Printed:</strong> {{ $generatedAt->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="chips">
            <span class="chip">1 USD = {{ $exchangeRates['USD'] ?? '-' }} {{ $baseCurrency }}</span>
            <span class="chip">1 EUR = {{ $exchangeRates['EUR'] ?? '-' }} {{ $baseCurrency }}</span>
            <span class="chip">1 LYD = {{ $exchangeRates['LYD'] ?? '-' }} {{ $baseCurrency }}</span>
            <span class="chip">1 GBP = {{ $exchangeRates['GBP'] ?? '-' }} {{ $baseCurrency }}</span>
        </div>

        <table class="grid">
            <tr>
                <td class="col-25">
                    <div class="card">
                        <div class="card-accent"></div>
                        <div class="card-body">
                            <div class="label">Total Revenue</div>
                            <div class="value">{{ $currencyMapText($revenueMap) }}</div>
                            <div class="muted">By filtered scope</div>
                        </div>
                    </div>
                </td>

                <td class="col-25">
                    <div class="card">
                        <div class="card-accent"></div>
                        <div class="card-body">
                            <div class="label">Salary Cost</div>
                            <div class="value">{{ $currencyMapText($salaryCostMap) }}</div>
                            <div class="muted">Paid: {{ $currencyMapText($salaryPaidMap) }}</div>
                        </div>
                    </div>
                </td>

                <td class="col-25">
                    <div class="card">
                        <div class="card-accent"></div>
                        <div class="card-body">
                            <div class="label">Expenses</div>
                            <div class="value">{{ $currencyMapText($expensesMap) }}</div>
                            <div class="muted">By filtered scope</div>
                        </div>
                    </div>
                </td>

                <td class="col-25">
                    <div class="card">
                        <div class="card-accent"></div>
                        <div class="card-body">
                            <div class="label">Net Result</div>
                            <div class="value">{{ $currencyMapText($netMap) }}</div>
                            <div class="muted">Revenue - Salary Cost - Expenses</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="summary-band">
            <div class="summary-title">Converted Executive Summary</div>
            <div class="summary-sub">Final management view after conversion into {{ $baseCurrency }} using the selected exchange rates.</div>

            <table class="summary-grid">
                <tr>
                    <td class="summary-box">
                        <div class="summary-box-label">Converted Revenue</div>
                        <div class="summary-box-value">{{ $money($convertedSummary['revenue_total'] ?? null, $baseCurrency) }}</div>
                    </td>
                    <td class="summary-box">
                        <div class="summary-box-label">Converted Salary Cost</div>
                        <div class="summary-box-value">{{ $money($convertedSummary['salary_cost_total'] ?? null, $baseCurrency) }}</div>
                    </td>
                    <td class="summary-box">
                        <div class="summary-box-label">Converted Expenses</div>
                        <div class="summary-box-value">{{ $money($convertedSummary['expenses_total'] ?? null, $baseCurrency) }}</div>
                    </td>
                    <td class="summary-box">
                        <div class="summary-box-label">Converted Net Result</div>
                        <div class="summary-box-value">{{ $money($convertedSummary['net_total'] ?? null, $baseCurrency) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-head">Breakdown by Currency</div>
            <div class="section-body">
                <table class="report-table">
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
                        @php
                            $currencies = collect(array_keys($revenueMap + $salaryCostMap + $expensesMap + $netMap))->unique()->values();
                        @endphp

                        @forelse($currencies as $currency)
                            <tr>
                                <td>{{ strtoupper($currency) }}</td>
                                <td>{{ $money($revenueMap[$currency] ?? 0) }}</td>
                                <td>{{ $money($salaryCostMap[$currency] ?? 0) }}</td>
                                <td>{{ $money($expensesMap[$currency] ?? 0) }}</td>
                                <td>{{ $money($netMap[$currency] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No currency totals found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-head">Breakdown by Client</div>
            <div class="section-body">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Revenue</th>
                            <th>Salary Cost</th>
                            <th>Expenses</th>
                            <th>Net Result</th>
                            <th>Converted Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byClient as $row)
                            <tr>
                                <td>{{ $row['client_name'] ?? $row['name'] ?? '-' }}</td>
                                <td>{{ $currencyMapText($row['revenue_by_currency'] ?? []) }}</td>
                                <td>{{ $currencyMapText($row['salary_cost_by_currency'] ?? []) }}</td>
                                <td>{{ $currencyMapText($row['expenses_by_currency'] ?? []) }}</td>
                                <td>{{ $currencyMapText($row['net_by_currency'] ?? []) }}</td>
                                <td>{{ $money($row['converted_net_total'] ?? null, $baseCurrency) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">No client breakdown found for the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(!empty($byProject))
            <div class="section">
                <div class="section-head">Breakdown by Project</div>
                <div class="section-body">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Revenue</th>
                                <th>Salary Cost</th>
                                <th>Expenses</th>
                                <th>Net Result</th>
                                <th>Converted Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byProject as $row)
                                <tr>
                                    <td>{{ $row['project_name'] ?? $row['name'] ?? '-' }}</td>
                                    <td>{{ $currencyMapText($row['revenue_by_currency'] ?? []) }}</td>
                                    <td>{{ $currencyMapText($row['salary_cost_by_currency'] ?? []) }}</td>
                                    <td>{{ $currencyMapText($row['expenses_by_currency'] ?? []) }}</td>
                                    <td>{{ $currencyMapText($row['net_by_currency'] ?? []) }}</td>
                                    <td>{{ $money($row['converted_net_total'] ?? null, $baseCurrency) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(!empty($byEmployee))
            <div class="section">
                <div class="section-head">Breakdown by Employee</div>
                <div class="section-body">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Revenue</th>
                                <th>Salary Cost</th>
                                <th>Expenses</th>
                                <th>Net Result</th>
                                <th>Converted Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byEmployee as $row)
                                <tr>
                                    <td>{{ $row['employee_name'] ?? $row['name'] ?? '-' }}</td>
                                    <td>{{ $currencyMapText($row['revenue_by_currency'] ?? []) }}</td>
                                    <td>{{ $currencyMapText($row['salary_cost_by_currency'] ?? []) }}</td>
                                    <td>{{ $currencyMapText($row['expenses_by_currency'] ?? []) }}</td>
                                    <td>{{ $currencyMapText($row['net_by_currency'] ?? []) }}</td>
                                    <td>{{ $money($row['converted_net_total'] ?? null, $baseCurrency) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <table class="sign-grid">
            <tr>
                <td style="width:50%;">
                    <div class="sign-card">
                        <div class="sign-title">Prepared By</div>
                        <div class="sign-line">Name / Signature / Company Stamp</div>
                    </div>
                </td>
                <td style="width:50%;">
                    <div class="sign-card">
                        <div class="sign-title">Management Approval</div>
                        <div class="sign-line">Name / Signature / Approval Stamp</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="footer">
            This report was generated by Sada Fezzan ERP.
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
