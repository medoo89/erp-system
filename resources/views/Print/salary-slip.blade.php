@php
    $logoDataUri = $logoDataUri ?? null;

    $employeeName = $employeeName ?? ($salarySlip->employment?->employee_name ?? '-');
    $employeeCode = $employeeCode ?? ($salarySlip->employment?->employee_code ?? '-');
    $positionTitle = $positionTitle ?? ($salarySlip->employment?->position_title ?? '-');

    $clientName = $clientName ?? ($salarySlip->client?->name ?? $salarySlip->project?->client?->name ?? '-');
    $projectName = $projectName ?? ($salarySlip->project?->name ?? '-');

    $periodLabel = $periodLabel ?? (
        ($salarySlip->salary_year && $salarySlip->salary_month)
            ? sprintf('%04d-%02d', (int) $salarySlip->salary_year, (int) $salarySlip->salary_month)
            : '-'
    );

    $currency = $currency ?? ($salarySlip->currency ?? '-');
    $paymentMethodLabel = $paymentMethodLabel ?? (
        $salarySlip->payment_method
            ? (\App\Models\SalarySlip::paymentMethodLabels()[$salarySlip->payment_method] ?? $salarySlip->payment_method)
            : '-'
    );

    $baseAmount = $baseAmount ?? (float) ($salarySlip->base_amount ?? 0);
    $adjustmentsAmount = $adjustmentsAmount ?? (float) ($salarySlip->adjustments_amount ?? $salarySlip->adjustments ?? 0);
    $deductionsAmount = $deductionsAmount ?? (float) ($salarySlip->deductions_amount ?? $salarySlip->deductions ?? 0);
    $netAmount = $netAmount ?? (float) ($salarySlip->net_amount ?? 0);

    $workedDays = $workedDays ?? (float) ($salarySlip->days_worked ?? 0);
    $paidDays = $paidDays ?? (float) ($salarySlip->days_worked ?? $salarySlip->paid_days ?? 0);
    $totalDaysInSlip = $totalDaysInSlip ?? (float) ($salarySlip->total_days ?? $workedDays);
    $absentDays = $absentDays ?? max(0, (float) $totalDaysInSlip - (float) $paidDays);

    $treasuryAccountName = $treasuryAccountName ?? ($salarySlip->treasuryAccount?->account_name ?? '-');
    $notes = $notes ?? ($salarySlip->notes ?? '-');

    $printAdditionNote = trim((string) ($salarySlip->addition_note ?? ''));
    $printDeductionNote = trim((string) ($salarySlip->deduction_note ?? ''));

    $showAdditionDeductionNotes = ((float) ($salarySlip->adjustments_amount ?? 0) > 0 && $printAdditionNote !== '')
        || ((float) ($salarySlip->deductions_amount ?? 0) > 0 && $printDeductionNote !== '');

    $printLinkedReimbursements = \App\Models\FinanceExpense::query()
        ->where('reimbursed_salary_slip_id', $salarySlip->id)
        ->whereIn('reimbursement_status', [
            \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED,
            \App\Models\FinanceExpense::REIMBURSEMENT_PAID,
        ])
        ->get();

    $printReimbursementByCurrency = $printLinkedReimbursements
        ->groupBy(fn ($item) => strtoupper((string) ($item->reimbursement_currency ?: $item->currency ?: $currency)))
        ->map(fn ($items, $cur) => [
            'currency' => $cur,
            'amount' => (float) $items->sum(fn ($item) => (float) ($item->reimbursement_amount ?: $item->amount ?: 0)),
            'count' => $items->count(),
        ])
        ->values();

    $printAllOriginalReimbursement = (float) $printReimbursementByCurrency->sum('amount');
    $printConvertedReimbursement = (float) ($salarySlip->reimbursement_converted_total ?? 0);
    $printSameCurrencyReimbursement = (float) ($salarySlip->reimbursement_same_currency_total ?? 0);
    $printPaymentTotal = (float) ($salarySlip->payment_total_amount ?: 0);

    if ($printConvertedReimbursement <= 0 && $printLinkedReimbursements->count() > 0) {
        $printConvertedReimbursement = (float) $printLinkedReimbursements
            ->filter(fn ($item) => strtoupper((string) ($item->reimbursement_currency ?: $item->currency ?: $currency)) === strtoupper((string) $currency))
            ->sum(fn ($item) => (float) ($item->reimbursement_amount ?: $item->amount ?: 0));
    }

    if ($printConvertedReimbursement <= 0 && $printLinkedReimbursements->count() > 0) {
        $printConvertedReimbursement = (float) $printLinkedReimbursements
            ->filter(fn ($item) => strtoupper((string) ($item->reimbursement_currency ?: $item->currency ?: $currency)) === strtoupper((string) $currency))
            ->sum(fn ($item) => (float) ($item->reimbursement_amount ?: $item->amount ?: 0));
    }

    if ($printPaymentTotal <= 0 || $printPaymentTotal < ((float) $netAmount + (float) $printConvertedReimbursement)) {
        $printPaymentTotal = (float) $netAmount + (float) $printConvertedReimbursement;
    }

    $printBreakdown = collect((array) ($salarySlip->reimbursement_breakdown ?? []));


    $amountInWords = $amountInWords ?? (
        number_format((float) $netAmount, 2) . ' ' . ($currency ?: '')
    );

    $statusBreakdown = $statusBreakdown ?? [
        'present' => (float) $workedDays,
        'absent' => (float) $absentDays,
        'sick' => 0,
        'leave' => 0,
        'unpaid_leave' => 0,
        'holiday' => 0,
        'travel' => 0,
        'other' => 0,
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - {{ $salarySlip->employment?->employee_name ?? 'Employee' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            background: #f4f7fb;
            color: #0f172a;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            line-height: 1.28;
        }

        .print-actions {
            width: 194mm;
            margin: 0 auto 6px;
            display: flex;
            justify-content: flex-end;
        }

        .print-btn {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .sheet {
            width: 194mm;
            min-height: 279mm;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d9e2ec;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .top-bar {
            height: 7px;
            background: linear-gradient(90deg, #0b1f3a 0%, #1c3f6e 50%, #0b1f3a 100%);
        }

        .header {
            display: grid;
            grid-template-columns: 78px 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 14px 16px;
            border-bottom: 1px solid #d9e2ec;
            background: linear-gradient(180deg, #f9fbfe 0%, #f3f7fc 100%);
        }

        .logo-box {
            width: 78px;
            height: 78px;
            border: 1px solid #d9e2ec;
            border-radius: 14px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-fallback {
            font-size: 10px;
            color: #64748b;
            font-weight: 800;
            text-align: center;
            padding: 6px;
        }

        .brand-title {
            margin: 0;
            font-size: 22px;
            font-weight: 900;
            color: #0b1f3a;
            line-height: 1.02;
            letter-spacing: 0.2px;
        }

        .brand-subtitle {
            margin-top: 4px;
            font-size: 11px;
            color: #4b5d73;
            font-weight: 600;
        }

        .doc-title-wrap {
            text-align: right;
        }

        .doc-title {
            margin: 0;
            font-size: 23px;
            font-weight: 900;
            color: #0b1f3a;
        }

        .doc-period {
            margin-top: 4px;
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
        }

        .content {
            padding: 12px 16px 14px;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .card {
            border: 1px solid #dbe3ee;
            border-radius: 13px;
            overflow: hidden;
            background: #fff;
        }

        .card-head {
            padding: 8px 10px;
            background: linear-gradient(180deg, #f8fafc 0%, #f3f6fa 100%);
            border-bottom: 1px solid #dbe3ee;
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
        }

        .card-body {
            padding: 8px 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 42%;
            color: #64748b;
            font-weight: 800;
            padding-right: 8px;
        }

        .info-table td:last-child {
            color: #0f172a;
            font-weight: 800;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        .summary-box {
            border-radius: 13px;
            padding: 9px 10px;
            border: 1px solid #dbe3ee;
        }

        .summary-label {
            font-size: 10px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 900;
            line-height: 1.1;
        }

        .summary-neutral {
            background: #f8fafc;
            border-color: #dbe3ee;
        }

        .summary-neutral .summary-label,
        .summary-neutral .summary-value {
            color: #0f172a;
        }

        .summary-green {
            background: #eefbf3;
            border-color: #bfe8cb;
        }

        .summary-green .summary-label,
        .summary-green .summary-value {
            color: #166534;
        }

        .summary-red {
            background: #fff1f2;
            border-color: #fecdd3;
        }

        .summary-red .summary-label,
        .summary-red .summary-value {
            color: #b42318;
        }

        .summary-dark {
            background: #0f172a;
            border-color: #0f172a;
        }

        .summary-dark .summary-label {
            color: #cbd5e1;
        }

        .summary-dark .summary-value {
            color: #ffffff;
        }

        .money-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .money-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #eef2f7;
        }

        .money-table tr:last-child td {
            border-bottom: none;
        }

        .money-table td:first-child {
            color: #64748b;
            font-weight: 800;
        }

        .money-table td:last-child {
            text-align: right;
            color: #0f172a;
            font-weight: 900;
        }

        .highlight-net td {
            background: #f3f8ff;
            font-size: 12px;
        }

        .amount-box {
            border: 1px solid #dbe3ee;
            border-radius: 13px;
            padding: 10px 12px;
            margin-bottom: 10px;
            background: linear-gradient(180deg, #fcfdff 0%, #f9fbfd 100%);
        }

        .amount-box .label {
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .amount-box .value {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.35;
        }

        .breakdown-box {
            border: 1px solid #dbe3ee;
            border-radius: 13px;
            padding: 10px 12px;
            margin-bottom: 10px;
            background: #fff;
        }

        .breakdown-title {
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 8px;
            color: #0f172a;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .tag {
            border: 1px solid #dbe3ee;
            background: #f8fafc;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
        }

        .notes-box {
            border: 1px solid #dbe3ee;
            border-radius: 13px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        .notes-box h3 {
            margin: 0 0 6px;
            font-size: 11px;
            color: #0f172a;
        }

        .notes-box p {
            margin: 0;
            color: #334155;
            white-space: pre-line;
            line-height: 1.45;
        }

        .footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 4px;
        }

        .sign-box {
            border: 1px dashed #cbd5e1;
            border-radius: 13px;
            min-height: 82px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            background: #fff;
        }

        .sign-title {
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            margin-bottom: 24px;
        }

        .sign-line {
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
        }

        .foot-note {
            margin-top: 8px;
            text-align: center;
            color: #64748b;
            font-size: 9px;
            font-weight: 700;
        }

        @media print {
            .print-actions {
                display: none !important;
            }

            .sheet {
                width: 100%;
                min-height: auto;
                border: none;
                border-radius: 0;
                box-shadow: none;
            }

            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    
        .sf-print-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .sf-print-summary-card {
            border: 1px solid #dbe4ef;
            border-radius: 14px;
            padding: 12px;
            background: #ffffff;
            min-height: 88px;
        }

        .sf-print-summary-card span {
            display: block;
            font-size: 10px;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .sf-print-summary-card strong {
            display: block;
            font-size: 15px;
            color: #0f172a;
            font-weight: 950;
            line-height: 1.45;
        }

        .sf-print-summary-card small {
            display: block;
            margin-top: 7px;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
        }

        .sf-print-summary-card-final {
            background: #0f172a;
            border-color: #0f172a;
        }

        .sf-print-summary-card-final span,
        .sf-print-summary-card-final small {
            color: #cbd5e1;
        }

        .sf-print-summary-card-final strong {
            color: #ffffff;
        }

        @media print {
            .sf-print-summary-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
                page-break-inside: avoid;
            }

            .sf-print-summary-card {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

</style>
</head>
<body>
    <div class="print-actions">
        <button class="print-btn" onclick="window.print()">Print Salary Slip</button>
    </div>

    <div class="sheet">
        <div class="top-bar"></div>

        <div class="header">
            <div class="logo-box">
                @if ($logoDataUri)
                    <img src="{{ $logoDataUri }}" alt="Company Logo">
                @else
                    <div class="logo-fallback">SADA FEZZAN</div>
                @endif
            </div>

            <div>
                <h1 class="brand-title">Sada Fezzan For Oil Services</h1>
                <div class="brand-subtitle">Payroll / Salary Slip Statement</div>
            </div>

            <div class="doc-title-wrap">
                <h2 class="doc-title">Salary Slip</h2>
                <div class="doc-period">
                    {{ \Carbon\Carbon::create($salarySlip->salary_year, $salarySlip->salary_month, 1)->format('F Y') }}
                </div>
            </div>
        </div>

        <div class="content">
            <div class="two-col">
                <div class="card">
                    <div class="card-head">Employee Information</div>
                    <div class="card-body">
                        <table class="info-table">
                            <tr>
                                <td>Name</td>
                                <td>{{ $salarySlip->employment?->employee_name ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td>Employee Code</td>
                                <td>{{ $salarySlip->employment?->employee_code ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td>Position</td>
                                <td>{{ $salarySlip->employment?->position_title ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td>Client</td>
                                <td>{{ $salarySlip->client?->name ?: ($salarySlip->employment?->client_name ?: '-') }}</td>
                            </tr>
                            <tr>
                                <td>Project</td>
                                <td>{{ $salarySlip->project?->name ?: ($salarySlip->employment?->project_name ?: '-') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">Payroll Information</div>
                    <div class="card-body">
                        <table class="info-table">
                            <tr>
                                <td>Period</td>
                                <td>{{ optional($salarySlip->period_start)->format('Y-m-d') }} → {{ optional($salarySlip->period_end)->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <td>Salary Basis</td>
                                <td>{{ $salarySlip->salary_basis === 'daily_rate' ? 'Daily Rate' : ucfirst((string) $salarySlip->salary_basis) }}</td>
                            </tr>
                            <tr>
                                <td>Daily Rate</td>
                                <td>{{ filled($salarySlip->daily_rate) ? number_format((float) $salarySlip->daily_rate, 2) . ' ' . $salarySlip->currency : '-' }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>{{ ucfirst((string) $salarySlip->status) }}</td>
                            </tr>
                            <tr>
                                <td>Rotation</td>
                                <td>{{ $salarySlip->employmentRotation?->rotation_label ?: ('#' . ($salarySlip->employment_rotation_id ?: '-')) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="summary-grid">
                <div class="summary-box summary-neutral">
                    <div class="summary-label">Total Days</div>
                    <div class="summary-value">{{ $totalDaysInSlip }}</div>
                </div>
                <div class="summary-box summary-green">
                    <div class="summary-label">Paid Days</div>
                    <div class="summary-value">{{ $paidDays }}</div>
                </div>
                <div class="summary-box summary-red">
                    <div class="summary-label">Absent Days</div>
                    <div class="summary-value">{{ $absentDays }}</div>
                </div>
                <div class="summary-box summary-dark">
                    <div class="summary-label">Currency</div>
                    <div class="summary-value">{{ $salarySlip->currency }}</div>
                </div>
            </div>

            <div class="money-grid">
                <div class="card">
                    <div class="card-head">Earnings</div>
                    <table class="money-table">
                        <tr>
                            <td>Daily Rate</td>
                            <td>{{ number_format((float) ($salarySlip->daily_rate ?? 0), 2) }} {{ $salarySlip->currency }}</td>
                        </tr>
                        <tr>
                            <td>Paid Days</td>
                            <td>{{ $paidDays }}</td>
                        </tr>
                        <tr>
                            <td>Base Amount</td>
                            <td>{{ number_format((float) $salarySlip->base_amount, 2) }} {{ $salarySlip->currency }}</td>
                        </tr>
                        <tr>
                            <td>Adjustments</td>
                            <td>{{ number_format((float) $salarySlip->adjustments_amount, 2) }} {{ $salarySlip->currency }}</td>
                        </tr>
                    </table>
                </div>

                <div class="card">
                    <div class="card-head">Deductions / Net</div>
                    <table class="money-table">
                        <tr>
                            <td>Deductions</td>
                            <td>{{ number_format((float) $salarySlip->deductions_amount, 2) }} {{ $salarySlip->currency }}</td>
                        </tr>
                        <tr>
                            <td>Absent Days</td>
                            <td>{{ $statusBreakdown['absent'] }}</td>
                        </tr>
                        <tr>
                            <td>Unpaid Days</td>
                            <td>{{ $salarySlip->unpaidDaysCount() }}</td>
                        </tr>
                        <tr class="highlight-net">
                            <td>Net Amount</td>
                            <td>{{ number_format((float) $salarySlip->net_amount, 2) }} {{ $salarySlip->currency }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="amount-box">
                <div class="label">Amount in Words</div>
                <div class="value">{{ $amountInWords }}</div>
            </div>

            <div class="breakdown-box">
                <div class="breakdown-title">Attendance Breakdown</div>
                <div class="tags">
                    <div class="tag">Present: {{ $statusBreakdown['present'] }}</div>
                    <div class="tag">Absent: {{ $statusBreakdown['absent'] }}</div>
                    <div class="tag">Sick: {{ $statusBreakdown['sick'] }}</div>
                    <div class="tag">Leave: {{ $statusBreakdown['leave'] }}</div>
                    <div class="tag">Unpaid Leave: {{ $statusBreakdown['unpaid_leave'] }}</div>
                    <div class="tag">Holiday: {{ $statusBreakdown['holiday'] }}</div>
                    <div class="tag">Travel: {{ $statusBreakdown['travel'] }}</div>
                    <div class="tag">Other: {{ $statusBreakdown['other'] }}</div>
                </div>
            </div>

            
            
            <section class="section sf-print-payment-summary">
                <div class="section-title">Salary Payment & Reimbursement Summary</div>

                <div class="sf-print-summary-grid">
                    <div class="sf-print-summary-card">
                        <span>Salary Net Amount</span>
                        <strong>{{ number_format((float) $netAmount, 2) }} {{ $currency }}</strong>
                        <small>After additions and deductions.</small>
                    </div>

                    <div class="sf-print-summary-card">
                        <span>Linked Reimbursements</span>
                        <strong>
                            @if($printReimbursementByCurrency->count())
                                @foreach($printReimbursementByCurrency as $row)
                                    <div>{{ number_format((float) $row['amount'], 2) }} {{ $row['currency'] }}</div>
                                @endforeach
                            @else
                                -
                            @endif
                        </strong>
                        <small>Original reimbursement currencies.</small>
                    </div>

                    <div class="sf-print-summary-card">
                        <span>Converted Reimbursement</span>
                        <strong>{{ number_format((float) $printConvertedReimbursement, 2) }} {{ $currency }}</strong>
                        <small>Converted to salary payment currency.</small>
                    </div>

                    <div class="sf-print-summary-card sf-print-summary-card-final">
                        <span>Final Payable Amount</span>
                        <strong>{{ number_format((float) $printPaymentTotal, 2) }} {{ $currency }}</strong>
                        <small>Salary net + converted reimbursements.</small>
                    </div>
                </div>

                @if($printBreakdown->count())
                    <table style="margin-top:12px;">
                        <thead>
                            <tr>
                                <th>Original Currency</th>
                                <th>Original Amount</th>
                                <th>Rate</th>
                                <th>Converted Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($printBreakdown as $row)
                                <tr>
                                    <td>{{ strtoupper((string) ($row['currency'] ?? $row['from_currency'] ?? '-')) }}</td>
                                    <td>{{ number_format((float) ($row['original_amount'] ?? $row['amount'] ?? 0), 2) }}</td>
                                    <td>{{ $row['rate'] ?? $row['exchange_rate'] ?? '-' }}</td>
                                    <td>{{ number_format((float) ($row['converted_amount'] ?? $row['converted'] ?? 0), 2) }} {{ $currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </section>


            @if ($showAdditionDeductionNotes)
                <div class="notes-box">
                    <h3>Addition / Deduction Notes</h3>

                    @if ((float) ($salarySlip->adjustments_amount ?? 0) > 0 && $printAdditionNote !== '')
                        <p>
                            <strong>Addition:</strong>
                            {{ number_format((float) ($salarySlip->adjustments_amount ?? 0), 2) }} {{ $salarySlip->currency }}
                        </p>
                        <p>{{ $printAdditionNote }}</p>
                    @endif

                    @if ((float) ($salarySlip->deductions_amount ?? 0) > 0 && $printDeductionNote !== '')
                        <p>
                            <strong>Deduction:</strong>
                            {{ number_format((float) ($salarySlip->deductions_amount ?? 0), 2) }} {{ $salarySlip->currency }}
                        </p>
                        <p>{{ $printDeductionNote }}</p>
                    @endif
                </div>
            @endif


            <div class="footer">
                <div class="sign-box">
                    <div class="sign-title">Prepared By</div>
                    <div class="sign-line">{{ $salarySlip->generatedBy?->name ?: 'Sada Fezzan' }}</div>
                </div>

                <div class="sign-box">
                    <div class="sign-title">Authorized Signature / Stamp</div>
                    <div class="sign-line">Sada Fezzan For Oil Services</div>
                </div>
            </div>

            <div class="foot-note">
                This salary slip is system-generated and intended for internal payroll / finance use.
            </div>
        </div>
    </div>
</body>
</html>
