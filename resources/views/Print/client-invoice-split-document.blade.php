<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->document_number }} - {{ strtoupper($document->document_type) }} Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @page {
            size: A4;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #0f172a;
            background: #e5e7eb;
            font-size: 10px;
            line-height: 1.35;
        }

        .print-actions {
            position: fixed;
            top: 14px;
            right: 14px;
            z-index: 99;
        }

        .print-actions button {
            border: 0;
            border-radius: 999px;
            padding: 9px 14px;
            font-weight: 900;
            color: #ffffff;
            background: #2563eb;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(37, 99, 235, .25);
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 10mm;
            overflow: hidden;
        }

        .commercial-hero {
            border-radius: 22px;
            border: 1px solid #cfe0ea;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(20, 184, 166, .14), transparent 34%),
                #f8fbfc;
            border-top: 7px solid #0b223d;
            padding: 14px 16px;
            display: grid;
            grid-template-columns: 1.22fr .95fr;
            gap: 16px;
            align-items: center;
        }

        .brand-row {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .brand-logo {
            width: 132px;
            height: 92px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f766e;
            font-weight: 950;
            font-size: 10px;
            text-align: center;
            line-height: 1.2;
            flex: 0 0 132px;
        }


        .brand-logo-img {
            width: 132px !important;
            height: 92px !important;
            background: #ffffff !important;
            padding: 6px !important;
            overflow: hidden !important;
            flex: 0 0 132px !important;
        }

        .brand-logo-img img,
        .brand-logo img {
            max-width: 100% !important;
            max-height: 100% !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
            display: block !important;
            margin: 0 auto !important;
        }

        .brand-title {
            color: #0b223d;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.04em;
            line-height: 1;
        }

        .brand-sub {
            margin-top: 7px;
            color: #0f766e;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .brand-small {
            margin-top: 8px;
            color: #334155;
            font-size: 10px;
            font-weight: 800;
        }

        
        .brand-reg {
            margin-top: 5px;
            color: #475569;
            font-size: 8.5px;
            font-weight: 850;
            letter-spacing: .02em;
        }

        .invoice-number-box {
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #cfe0ea;
            padding: 12px;
        }

        .invoice-number-label {
            color: #64748b;
            font-size: 8px;
            font-weight: 950;
            letter-spacing: .16em;
            text-align: right;
            text-transform: uppercase;
        }

        .invoice-number {
            margin-top: 3px;
            color: #0b223d;
            font-size: 17px;
            font-weight: 950;
            text-align: right;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            margin-top: 6px;
        }

        .info-row:first-of-type {
            border-top: 0;
        }

        .label {
            color: #64748b;
            font-size: 8px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .value {
            color: #0f172a;
            font-size: 9px;
            font-weight: 900;
            text-align: right;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
        }

        .section {
            border-radius: 16px;
            border: 1px solid #cfe0ea;
            background: #ffffff;
            overflow: hidden;
        }

        .section-title {
            background: #eaf4f8;
            color: #12365a;
            padding: 7px 10px;
            font-size: 9px;
            font-weight: 950;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .section-body {
            padding: 9px 10px;
        }

        .amount-band {
            margin-top: 10px;
            border-radius: 16px;
            padding: 13px;
            background:
                linear-gradient(90deg, rgba(224, 242, 254, .95), rgba(209, 250, 229, .76));
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .amount-item {
            border-radius: 14px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(207,224,234,.8);
            padding: 10px;
        }

        .amount-label {
            color: #64748b;
            font-size: 8px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .amount-value {
            margin-top: 5px;
            color: #0b223d;
            font-size: 14px;
            font-weight: 950;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-lines th,
        .invoice-lines td {
            border: 1px solid #dbe7ee;
            padding: 6px;
            vertical-align: top;
        }

        .invoice-lines th {
            background: #eaf4f8;
            color: #12365a;
            font-size: 8px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
            text-align: left;
        }

        .invoice-lines td {
            font-size: 9px;
            font-weight: 800;
        }

        .timesheet-wrap {
            margin-top: 10px;
        }

        .timesheet-title {
            border-radius: 16px 16px 0 0;
            background: #eaf4f8;
            border: 1px solid #cfe0ea;
            border-bottom: 0;
            color: #12365a;
            padding: 7px 10px;
            font-size: 9px;
            font-weight: 950;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .timesheet {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border: 1px solid #111827;
            font-size: 5.2px;
        }

        .timesheet th,
        .timesheet td {
            border: 1px solid #111827;
            text-align: center;
            vertical-align: middle;
            padding: 1px;
            line-height: 1;
        }

        .timesheet .month-head {
            background: #ffffff;
            color: #111827;
            font-size: 8px;
            font-weight: 950;
            padding: 4px;
            letter-spacing: .04em;
        }

        .timesheet .no-col {
            width: 3% !important;
            max-width: 3% !important;
            min-width: 3% !important;
            font-weight: 950 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .timesheet .name-col {
            width: 22% !important;
            max-width: 22% !important;
            min-width: 22% !important;
            text-align: left !important;
            padding: 2px 7px !important;
            font-weight: 950 !important;
            overflow: hidden !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.15 !important;
            font-size: 7.3px !important;
        }

        .timesheet .name-text {
            display: block !important;
            writing-mode: horizontal-tb !important;
            transform: none !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: break-word !important;
            line-height: 1.15 !important;
            font-size: 7.3px !important;
            font-weight: 950 !important;
        }

        .timesheet .day-col {
            width: auto !important;
            height: 18px !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .timesheet .day-head {
            height: 26px !important;
            padding: 0 !important;
            overflow: hidden !important;
        }

        .timesheet .day-vertical {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            writing-mode: horizontal-tb !important;
            transform: none !important;
            line-height: 1 !important;
            font-size: 6px !important;
            font-weight: 950 !important;
            color: #0f766e !important;
            max-height: none !important;
            white-space: nowrap !important;
        }

        .timesheet .mark {
            font-size: 5.5px !important;
            font-weight: 950 !important;
        }

        .timesheet .paid {
            color: #047857;
        }

        .timesheet .absent {
            color: #dc2626;
        }

        .timesheet .not-paid {
            color: #475569;
        }

        .timesheet .total-row td {
            background: #f8fafc;
            font-weight: 950;
        }

        .legend {
            margin-top: 4px;
            color: #475569;
            font-size: 7px;
            font-weight: 800;
        }

        .totals-grid {
            display: grid;
            grid-template-columns: 1.25fr .85fr;
            gap: 8px;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 7px 8px;
            border-top: 1px solid #e2e8f0;
            font-size: 9px;
            font-weight: 850;
        }

        .totals-table tr:first-child td {
            border-top: 0;
        }

        .totals-table .total {
            background: #dcfce7;
            font-weight: 950;
            font-size: 10px;
        }

        .bank-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
        }

        .bank-item {
            border-radius: 12px;
            border: 1px solid #dbe7ee;
            background: #f8fbfc;
            padding: 8px;
        }

        .bank-item .label {
            display: block;
        }

        .bank-item .value {
            display: block;
            text-align: left;
            margin-top: 4px;
            font-size: 8.5px;
        }

        .signature-grid {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .signature-box {
            border-radius: 16px;
            border: 1px dashed #9dbbd0;
            padding: 14px;
            min-height: 72px;
        }

        .signature-title {
            color: #12365a;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .signature-line {
            margin-top: 26px;
            border-top: 1px solid #64748b;
            padding-top: 5px;
            font-size: 8px;
            font-weight: 850;
            color: #475569;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .page {
                margin: 0;
                width: auto;
                min-height: auto;
                padding: 0;
            }

            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>

    @php
        $commercialRegistrationNo = env('SADA_FEZZAN_COMMERCIAL_REGISTRATION', '-');
    @endphp

@php
    /*
    |--------------------------------------------------------------------------
    | Split invoice logo fallback
    |--------------------------------------------------------------------------
    | The main commercial invoice may define $invoiceLogoUrl from its own view.
    | Foreign/local split invoice documents need their own safe definition.
    */
    $invoiceLogoUrl = $invoiceLogoUrl ?? asset('images/sada-fezzan-logo.png');
@endphp

    <div class="print-actions">
        <button onclick="window.print()">Print</button>
    </div>

    @php
        if (method_exists($invoice, 'generateTimesheetFromSalarySlips') && $invoice->workDays->isEmpty()) {
            $invoice->generateTimesheetFromSalarySlips(true);
            $invoice->refresh();
            $invoice->load(['client', 'project', 'projectContract', 'lines.salarySlip.days', 'lines.salarySlip.employment', 'workDays.employment', 'documents']);
        }

        $documentLabel = $document->document_type === 'foreign'
            ? 'Foreign Currency Invoice'
            : 'Local Currency Invoice';

        $displayCurrency = $document->currency ?: ($invoice->display_currency ?: $invoice->foreign_currency ?: '');
        $paidDaysTotal = (float) $invoice->workDays
            ->where('day_status', \App\Models\ClientInvoiceWorkDay::STATUS_PAID)
            ->sum('billable_units');

        $periodStart = $invoice->period_start
            ? \Carbon\Carbon::parse($invoice->period_start)
            : ($invoice->workDays->min('work_date') ? \Carbon\Carbon::parse($invoice->workDays->min('work_date')) : now()->startOfMonth());

        $periodEnd = $invoice->period_end
            ? \Carbon\Carbon::parse($invoice->period_end)
            : ($invoice->workDays->max('work_date') ? \Carbon\Carbon::parse($invoice->workDays->max('work_date')) : now()->endOfMonth());

        $tsDays = collect();
        $cursor = $periodStart->copy();

        while ($cursor->lte($periodEnd)) {
            $tsDays->push($cursor->copy());
            $cursor->addDay();
        }

        $workDaysByLine = $invoice->workDays->groupBy('client_invoice_line_id');
        $dailyTotals = [];

        foreach ($tsDays as $day) {
            $key = $day->format('Y-m-d');

            $dailyTotals[$key] = (float) $invoice->workDays
                ->filter(fn ($item) => optional($item->work_date)->format('Y-m-d') === $key)
                ->where('day_status', \App\Models\ClientInvoiceWorkDay::STATUS_PAID)
                ->sum('billable_units');
        }

        $subtotal = (float) ($invoice->subtotal_amount ?: $invoice->lines->sum('amount'));
        $tax = (float) ($invoice->tax_amount ?? 0);
        $total = (float) ($invoice->total_amount ?: $subtotal + $tax);

        /*
        |--------------------------------------------------------------------------
        | Currency-based split document calculations
        |--------------------------------------------------------------------------
        | Each split document must show only its own currency amount.
        | Foreign invoice = foreign document amount / currency.
        | Local invoice = local document amount / currency.
        | Billing rate = that document's line amount divided by paid days.
        */
        $documentAmount = (float) ($document->amount ?? 0);
        $baseInvoiceTotal = (float) ($invoice->total_amount ?: $invoice->lines->sum('amount') ?: 0);
        $documentCurrency = $displayCurrency;

        $lineDocumentAmounts = [];
        $lineDocumentRates = [];

        foreach ($invoice->lines as $line) {
            $lineBaseAmount = (float) ($line->amount ?? 0);

            if ($baseInvoiceTotal > 0 && $documentAmount > 0) {
                $lineDocAmount = round($documentAmount * ($lineBaseAmount / $baseInvoiceTotal), 2);
            } else {
                $lineDocAmount = 0.0;
            }

            $linePaid = (float) $invoice->workDays
                ->where('client_invoice_line_id', $line->id)
                ->where('day_status', \App\Models\ClientInvoiceWorkDay::STATUS_PAID)
                ->sum('billable_units');

            if ($linePaid <= 0) {
                $linePaid = (float) ($line->quantity ?? 0);
            }

            $lineDocumentAmounts[$line->id] = $lineDocAmount;
            $lineDocumentRates[$line->id] = $linePaid > 0
                ? round($lineDocAmount / $linePaid, 2)
                : 0.0;
        }

        $documentSubtotal = round(array_sum($lineDocumentAmounts), 2);
        if ($documentSubtotal <= 0 && $documentAmount > 0) {
            $documentSubtotal = $documentAmount;
        }

        $documentTax = 0.0;
        $documentTotal = $documentAmount > 0 ? $documentAmount : $documentSubtotal;

    @endphp

    <main class="page">
        <section class="commercial-hero">
            <div class="brand-row">
                <div class="brand-logo brand-logo-img">
                        <img src="{{ $invoiceLogoUrl ?: '' }}" alt="Sada Fezzan Logo">
                    </div>
                <div>
                    <div class="brand-title">Sada Fezzan</div>
                    <div class="brand-sub">Commercial Invoice</div>
                    <div class="brand-small">Sada Fezzan For Oil Services</div>
                    <div class="brand-reg">Commercial Reg. No: {{ $commercialRegistrationNo }}</div>

                </div>
            </div>

            <div class="invoice-number-box">
                <div class="invoice-number-label">Invoice Number</div>
                <div class="invoice-number">{{ $document->document_number }}</div>

                <div class="info-row">
                    <span class="label">Date</span>
                    <span class="value">{{ optional($invoice->invoice_date)->format('Y-m-d') ?: '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Project</span>
                    <span class="value">{{ $invoice->project?->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Document</span>
                    <span class="value">{{ $documentLabel }}</span>
                </div>
            </div>
        </section>

        <section class="grid-2">
            <div class="section">
                <div class="section-title">Bill To</div>
                <div class="section-body">
                    <div class="info-row">
                        <span class="label">Name</span>
                        <span class="value">{{ $invoice->bill_to_name ?: ($invoice->client?->name ?? '-') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Address</span>
                        <span class="value">{{ $invoice->bill_to_address ?: '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Phone</span>
                        <span class="value">{{ $invoice->bill_to_phone ?: '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Client</span>
                        <span class="value">{{ $invoice->client?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-title">Invoice Info</div>
                <div class="section-body">
                    <div class="info-row">
                        <span class="label">Period</span>
                        <span class="value">
                            {{ optional($invoice->period_start)->format('Y-m-d') ?: '-' }}
                            →
                            {{ optional($invoice->period_end)->format('Y-m-d') ?: '-' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Payment Terms</span>
                        <span class="value">{{ $invoice->payment_terms_label ?: '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Foreign Split</span>
                        <span class="value">{{ number_format((float) ($invoice->foreign_percentage ?? 0), 2) }}% {{ $invoice->foreign_currency ?: '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Local Split</span>
                        <span class="value">{{ number_format((float) ($invoice->local_percentage ?? 0), 2) }}% {{ $invoice->local_currency ?: '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Exchange Rate</span>
                        <span class="value">{{ number_format((float) ($invoice->exchange_rate ?? 0), 3) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="amount-band">
            <div class="amount-item">
                <div class="amount-label">Document Amount</div>
                <div class="amount-value">{{ number_format($documentAmount, 2) }} {{ $documentCurrency }}</div>
            </div>

            <div class="amount-item">
                <div class="amount-label">Split Percentage</div>
                <div class="amount-value">{{ number_format((float) $document->percentage, 2) }}%</div>
            </div>

            <div class="amount-item">
                <div class="amount-label">Paid Days From Timesheet</div>
                <div class="amount-value">{{ number_format($paidDaysTotal, 2) }}</div>
            </div>
        </section>

        <section class="section" style="margin-top:10px;">
            <div class="section-title">Employee / Service</div>
            <table class="invoice-lines">
                <thead>
                    <tr>
                        <th>Employee / Service</th>
                        <th>Description</th>
                        <th>Service Period</th>
                        <th>Paid Days</th>
                        <th>Billing Rate</th>
                        <th>Line Amount</th>
                        <th>Currency</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->lines as $line)
                        @php
                            $linePaidDays = (float) $invoice->workDays
                                ->where('client_invoice_line_id', $line->id)
                                ->where('day_status', \App\Models\ClientInvoiceWorkDay::STATUS_PAID)
                                ->sum('billable_units');

                            if ($linePaidDays <= 0) {
                                $linePaidDays = (float) ($line->quantity ?? 0);
                            }

                            $lineDocAmount = (float) ($lineDocumentAmounts[$line->id] ?? 0);
                            $lineDocRate = (float) ($lineDocumentRates[$line->id] ?? 0);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $line->candidate_name ?: ($line->salarySlip?->employment?->full_name ?? '-') }}</strong><br>
                                {{ $line->position_title ?: '-' }}<br>
                                Project: {{ $line->project_name ?: ($invoice->project?->name ?? '-') }}
                            </td>
                            <td>
                                Candidate Name: {{ $line->candidate_name ?: '-' }}<br>
                                Position: {{ $line->position_title ?: '-' }}<br>
                                Date & Duration:
                                {{ optional($line->service_period_start)->format('d M Y') ?: optional($invoice->period_start)->format('d M Y') }}
                                -
                                {{ optional($line->service_period_end)->format('d M Y') ?: optional($invoice->period_end)->format('d M Y') }}<br>
                                Paid Duration: {{ number_format($linePaidDays, 2) }} day(s)<br>
                                Billing Rate: {{ number_format($lineDocRate, 2) }} {{ $documentCurrency }}
                            </td>
                            <td>
                                {{ optional($line->service_period_start)->format('Y-m-d') ?: optional($invoice->period_start)->format('Y-m-d') }}<br>
                                {{ $line->service_month_label ?: optional($invoice->period_start)->format('F Y') }}
                            </td>
                            <td>{{ number_format($linePaidDays, 2) }}</td>
                            <td>{{ number_format($lineDocRate, 2) }}</td>
                            <td>{{ number_format($lineDocAmount, 2) }}</td>
                            <td>{{ $documentCurrency }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No invoice lines.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="timesheet-wrap">
            <div class="timesheet-title">Work Days / Timesheet</div>

            <table class="timesheet">
                
                
                <colgroup>
                    <col style="width:3%;">
                    <col style="width:22%;">
                    @foreach($tsDays as $day)
                        <col style="width:{{ 75 / max(1, $tsDays->count()) }}%;">
                    @endforeach
                </colgroup>



                <thead>
                    <tr>
                        <th colspan="{{ 2 + $tsDays->count() }}" class="month-head">
                            Summary Monthly Sheet — {{ $periodStart->format('F Y') }}
                        </th>
                    </tr>
                    <tr>
                        <th class="no-col">#</th>
                        <th class="name-col">Name</th>
                        @foreach($tsDays as $day)
                            <th class="day-col day-head">
                                <span class="day-vertical">{{ $day->format('d') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->lines as $index => $line)
                        @php
                            $lineDays = $workDaysByLine->get($line->id, collect());
                        @endphp
                        <tr>
                            @php
                                $employeeName = $line->candidate_name ?: ($line->salarySlip?->employment?->full_name ?? '-');
                                $isLongName = mb_strlen((string) $employeeName) > 18;
                            @endphp

                            <td class="no-col">{{ $index + 1 }}</td>
                            <td class="name-col">
                                <span class="name-text">
                                    {{ $employeeName }}
                                </span>
                            </td>

                            @foreach($tsDays as $day)
                                @php
                                    $dateKey = $day->format('Y-m-d');

                                    $workDay = $lineDays->first(function ($item) use ($dateKey) {
                                        return optional($item->work_date)->format('Y-m-d') === $dateKey;
                                    });

                                    $symbol = '';
                                    $class = '';

                                    if ($workDay) {
                                        if ($workDay->day_status === \App\Models\ClientInvoiceWorkDay::STATUS_PAID) {
                                            $symbol = 'P';
                                            $class = 'paid';
                                        } elseif ($workDay->day_status === \App\Models\ClientInvoiceWorkDay::STATUS_ABSENT) {
                                            $symbol = 'A';
                                            $class = 'absent';
                                        } else {
                                            $symbol = '0';
                                            $class = 'not-paid';
                                        }
                                    }
                                @endphp
                                <td class="day-col mark {{ $class }}">{{ $symbol }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td class="no-col">1</td>
                            <td class="name-col">-</td>
                            @foreach($tsDays as $day)
                                <td class="day-col"></td>
                            @endforeach
                        </tr>
                    @endforelse

                    <tr class="total-row">
                        <td colspan="2">DAILY TOTAL</td>
                        @foreach($tsDays as $day)
                            <td class="day-col">{{ number_format((float) ($dailyTotals[$day->format('Y-m-d')] ?? 0), 0) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>

            <div class="legend">
                P = Paid Day / 0 = Not Paid / A = Absent. Generated from Salary Slip attendance days.
            </div>
        </section>

        <section class="totals-grid">
            <div class="section">
                <div class="section-title">Terms & Notes</div>
                <div class="section-body" style="min-height:86px;">
                    {!! nl2br(e($invoice->terms_text ?: $invoice->notes ?: '')) !!}
                </div>
            </div>

            <div class="section">
                <div class="section-title">Totals</div>
                <table class="totals-table">
                    <tr>
                        <td>Subtotal This Document</td>
                        <td style="text-align:right;">{{ number_format($documentSubtotal, 2) }} {{ $documentCurrency }}</td>
                    </tr>
                    <tr>
                        <td>Tax</td>
                        <td style="text-align:right;">{{ number_format($documentTax, 2) }} {{ $documentCurrency }}</td>
                    </tr>
                    <tr class="total">
                        <td>Total This Document</td>
                        <td style="text-align:right;">{{ number_format($documentTotal, 2) }} {{ $documentCurrency }}</td>
                    </tr>
                    <tr>
                        <td>Currency Document Amount</td>
                        <td style="text-align:right;">{{ number_format($documentAmount, 2) }} {{ $documentCurrency }}</td>
                    </tr>
                </table>
            </div>
        </section>

        <section class="section" style="margin-top:10px;">
            <div class="section-title">Bank Details</div>
            <div class="section-body">
                <div class="bank-grid">
                    <div class="bank-item">
                        <span class="label">Bank Name</span>
                        <span class="value">{{ $invoice->bank_name ?: '-' }}</span>
                    </div>
                    <div class="bank-item">
                        <span class="label">Swift Code</span>
                        <span class="value">{{ $invoice->swift_code ?: '-' }}</span>
                    </div>
                    <div class="bank-item">
                        <span class="label">IBAN EUR</span>
                        <span class="value">{{ $invoice->iban_eur ?: '-' }}</span>
                    </div>
                    <div class="bank-item">
                        <span class="label">IBAN USD</span>
                        <span class="value">{{ $invoice->iban_usd ?: '-' }}</span>
                    </div>
                    <div class="bank-item">
                        <span class="label">IBAN LYD</span>
                        <span class="value">{{ $invoice->iban_lyd ?: '-' }}</span>
                    </div>
                    <div class="bank-item">
                        <span class="label">Account Number LYD</span>
                        <span class="value">{{ $invoice->account_number_lyd ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="signature-grid">
            <div class="signature-box">
                <div class="signature-title">Prepared By</div>
                <div class="signature-line">
                    {{ auth()->user()?->name ?? 'Authorized Signature' }}
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-title">Client Acknowledgement</div>
                <div class="signature-line">
                    Signature / Name
                </div>
            </div>
        </section>
    </main>
</body>
</html>
