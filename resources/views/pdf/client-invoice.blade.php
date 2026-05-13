<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Invoice - {{ $invoice->invoice_number }}</title>

    <style>

        @page {

            margin: 16mm 12mm 18mm 12mm;

        }

        * { box-sizing: border-box; }

        body {

            margin: 0;

            color: #12233a;

            font-family: DejaVu Sans, sans-serif;

            font-size: 10.5px;

            line-height: 1.45;

            background: #ffffff;

        }

        .page {

            width: 100%;

        }

        .header-shell {

            border: 1px solid #d9e2ec;

            border-radius: 18px;

            overflow: hidden;

            margin-bottom: 14px;

        }

        .header-bar {

            height: 8px;

            background: #0f2f57;

        }

        .header {

            width: 100%;

            border-collapse: collapse;

            background:

                linear-gradient(135deg, #f8fbff 0%, #eef5fb 55%, #e4eef8 100%);

        }

        .header td {

            vertical-align: top;

            padding: 18px 18px 16px 18px;

        }

        .brand-cell {

            width: 58%;

        }

        .meta-cell {

            width: 42%;

            text-align: right;

        }

        .brand-table {

            width: 100%;

            border-collapse: collapse;

        }

        .brand-table td {

            padding: 0;

            vertical-align: middle;

        }

        .logo-wrap {

            width: 78px;

        }

        .logo-box {

            width: 72px;

            height: 72px;

            border: 1px solid #dce5ef;

            border-radius: 14px;

            background: #ffffff;

            text-align: center;

            vertical-align: middle;

            overflow: hidden;

        }

        .logo-box img {

            width: 72px;

            height: 72px;

            object-fit: contain;

        }

        .logo-fallback {

            font-size: 10px;

            color: #66788a;

            font-weight: 700;

            line-height: 1.25;

            padding-top: 18px;

        }

        .brand-title {

            margin: 0;

            font-size: 25px;

            font-weight: 900;

            letter-spacing: -0.03em;

            color: #0f213a;

            line-height: 1;

        }

        .brand-subtitle {

            margin-top: 6px;

            font-size: 11px;

            font-weight: 900;

            letter-spacing: 0.12em;

            text-transform: uppercase;

            color: #1d4f86;

        }

        .brand-mini {

            margin-top: 6px;

            font-size: 10.5px;

            font-weight: 700;

            color: #4c6178;

        }

        .invoice-title {

            margin: 0;

            font-size: 30px;

            font-weight: 900;

            letter-spacing: -0.04em;

            color: #0f213a;

            line-height: 0.95;

        }

        .meta-block {

            margin-top: 8px;

        }

        .meta-line {

            font-size: 11px;

            font-weight: 700;

            color: #37526d;

            margin-top: 5px;

        }

        .meta-line strong {

            color: #0f213a;

        }

        .section-grid {

            width: 100%;

            border-collapse: separate;

            border-spacing: 0 14px;

        }

        .section-grid td {

            vertical-align: top;

        }

        .col-left {

            width: 50%;

            padding-right: 7px;

        }

        .col-right {

            width: 50%;

            padding-left: 7px;

        }

        .card {

            border: 1px solid #d9e2ec;

            border-radius: 16px;

            overflow: hidden;

            background: #ffffff;

        }

        .card-head {

            padding: 10px 14px;

            background: #edf4fa;

            border-bottom: 1px solid #dde6ef;

            font-size: 11px;

            font-weight: 900;

            color: #10243d;

        }

        .card-body {

            padding: 12px 14px;

        }

        .info-table {

            width: 100%;

            border-collapse: collapse;

        }

        .info-table td {

            padding: 5px 0;

            vertical-align: top;

        }

        .info-table td:first-child {

            width: 38%;

            color: #657a90;

            font-weight: 800;

            padding-right: 8px;

        }

        .info-table td:last-child {

            color: #10243d;

            font-weight: 700;

        }

        .line-shell {

            margin-top: 2px;

            margin-bottom: 14px;

            border: 1px solid #d9e2ec;

            border-radius: 16px;

            overflow: hidden;

        }

        .line-table {

            width: 100%;

            border-collapse: collapse;

        }

        .line-table th {

            background: #edf4fa;

            color: #10243d;

            font-size: 10.5px;

            font-weight: 900;

            text-align: left;

            padding: 10px 11px;

            border-bottom: 1px solid #dde6ef;

        }

        .line-table td {

            padding: 11px 11px;

            border-bottom: 1px solid #e8eef5;

            vertical-align: top;

        }

        .line-table tr:last-child td {

            border-bottom: none;

        }

        .line-table tbody tr:nth-child(even) td {

            background: #fbfdff;

        }

        .service-title {

            font-weight: 900;

            color: #112640;

            line-height: 1.35;

        }

        .candidate-sub {

            margin-top: 3px;

            color: #71859a;

            font-size: 10px;

            font-weight: 700;

        }

        .desc {

            white-space: pre-line;

            color: #31475e;

        }

        .num {

            text-align: right;

            font-weight: 800;

            color: #112640;

            white-space: nowrap;

        }

        .totals-row {

            width: 100%;

            border-collapse: separate;

            border-spacing: 0 14px;

        }

        .totals-row td {

            vertical-align: top;

        }

        .terms-col {

            width: 58%;

            padding-right: 7px;

        }

        .totals-col {

            width: 42%;

            padding-left: 7px;

        }

        .terms-body {

            white-space: pre-line;

            color: #31475e;

            min-height: 150px;

        }

        .totals-table {

            width: 100%;

            border-collapse: collapse;

        }

        .totals-table td {

            padding: 8px 0;

            border-bottom: 1px solid #edf2f7;

        }

        .totals-table tr:last-child td {

            border-bottom: none;

        }

        .totals-table td:first-child {

            color: #657a90;

            font-weight: 800;

        }

        .totals-table td:last-child {

            text-align: right;

            color: #0f213a;

            font-weight: 900;

            white-space: nowrap;

        }

        .grand-row td {

            background: #e7f0fb;

            font-size: 11px;

            padding: 9px 8px;

        }

        .bank-grid {

            width: 100%;

            border-collapse: separate;

            border-spacing: 0 10px;

        }

        .bank-grid td {

            width: 33.333%;

            vertical-align: top;

            padding-right: 10px;

        }

        .bank-grid td:last-child {

            padding-right: 0;

        }

        .bank-k {

            font-size: 10px;

            color: #71859a;

            font-weight: 800;

            margin-bottom: 3px;

        }

        .bank-v {

            font-size: 10.5px;

            color: #112640;

            font-weight: 800;

            word-wrap: break-word;

        }

        .sign-row {

            width: 100%;

            border-collapse: separate;

            border-spacing: 0 14px;

            margin-top: 2px;

        }

        .sign-row td {

            width: 50%;

            vertical-align: top;

        }

        .sign-left {

            padding-right: 7px;

        }

        .sign-right {

            padding-left: 7px;

        }

        .sign-card {

            min-height: 102px;

            border: 1px dashed #c8d5e3;

            border-radius: 16px;

            padding: 12px 14px;

            background: #ffffff;

        }

        .sign-title {

            font-size: 10.5px;

            color: #71859a;

            font-weight: 800;

            margin-bottom: 40px;

        }

        .sign-line {

            border-top: 1px solid #8ca0b5;

            padding-top: 6px;

            font-size: 10.5px;

            color: #10243d;

            font-weight: 900;

        }

        .footer-note {

            margin-top: 8px;

            font-size: 9px;

            color: #6e8094;

            font-weight: 700;

        }

    </style>

</head>

<body>

    @php

        $logoPath = null;

        $candidates = [

            public_path('logo'),

            public_path('logo.png'),

            public_path('logo.jpg'),

            public_path('logo.jpeg'),

            public_path('logo.webp'),

            public_path('logo/logo.png'),

            public_path('logo/logo.jpg'),

            public_path('logo/logo.jpeg'),

            public_path('logo/logo.webp'),

        ];

        foreach ($candidates as $candidate) {

            if (is_file($candidate)) {

                $logoPath = $candidate;

                break;

            }

        }

    @endphp

    <div class="page">

        <div class="header-shell">

            <div class="header-bar"></div>

            <table class="header">

                <tr>

                    <td class="brand-cell">

                        <table class="brand-table">

                            <tr>

                                <td class="logo-wrap">

                                    <div class="logo-box">

                                        @if ($logoPath)

                                            <img src="{{ $logoPath }}" alt="Company Logo">

                                        @else

                                            <div class="logo-fallback">SADA<br>FEZZAN</div>

                                        @endif

                                    </div>

                                </td>

                                <td>

                                    <div class="brand-title">Sada Fezzan</div>

                                    <div class="brand-subtitle">Commercial Invoice</div>

                                    <div class="brand-mini">Sada Fezzan For Oil Services</div>

                                </td>

                            </tr>

                        </table>

                    </td>

                    <td class="meta-cell">

                        <div class="invoice-title">INVOICE</div>

                        <div class="meta-block">

                            <div class="meta-line"><strong>Invoice No:</strong> {{ $invoice->invoice_number ?: '-' }}</div>

                            <div class="meta-line"><strong>Date:</strong> {{ optional($invoice->invoice_date)->format('Y-m-d') ?: '-' }}</div>

                            <div class="meta-line"><strong>Project:</strong> {{ $invoice->project?->name ?: '-' }}</div>

                        </div>

                    </td>

                </tr>

            </table>

        </div>

        <table class="section-grid">

            <tr>

                <td class="col-left">

                    <div class="card">

                        <div class="card-head">Bill To</div>

                        <div class="card-body">

                            <table class="info-table">

                                <tr><td>Name</td><td>{{ $invoice->bill_to_name ?: ($invoice->client?->name ?: '-') }}</td></tr>

                                <tr><td>Address</td><td>{{ $invoice->bill_to_address ?: '-' }}</td></tr>

                                <tr><td>Phone</td><td>{{ $invoice->bill_to_phone ?: '-' }}</td></tr>

                                <tr><td>Client</td><td>{{ $invoice->client?->name ?: '-' }}</td></tr>

                            </table>

                        </div>

                    </div>

                </td>

                <td class="col-right">

                    <div class="card">

                        <div class="card-head">Invoice Info</div>

                        <div class="card-body">

                            <table class="info-table">

                                <tr><td>Period</td><td>{{ optional($invoice->period_start)->format('Y-m-d') ?: '-' }} → {{ optional($invoice->period_end)->format('Y-m-d') ?: '-' }}</td></tr>

                                <tr><td>Payment Terms</td><td>{{ $invoice->payment_terms_label ?: '-' }}</td></tr>

                                <tr><td>Foreign Split</td><td>{{ rtrim(rtrim(number_format((float) $invoice->foreign_percentage, 2), '0'), '.') }}% {{ $invoice->foreign_currency ?: '-' }}</td></tr>

                                <tr><td>Local Split</td><td>{{ rtrim(rtrim(number_format((float) $invoice->local_percentage, 2), '0'), '.') }}% {{ $invoice->local_currency ?: '-' }}</td></tr>

                                <tr><td>Exchange Rate</td><td>{{ filled($invoice->exchange_rate) ? rtrim(rtrim(number_format((float) $invoice->exchange_rate, 4), '0'), '.') : '-' }}</td></tr>

                            </table>

                        </div>

                    </div>

                </td>

            </tr>

        </table>

        <div class="line-shell">

            <table class="line-table">

                <thead>

                    <tr>

                        <th style="width: 20%;">Employee / Service</th>

                        <th style="width: 26%;">Description</th>

                        <th style="width: 14%;">Service Period</th>

                        <th style="width: 8%;">Paid Days</th>

                        <th style="width: 12%;">Billing Rate</th>

                        <th style="width: 12%;">Line Amount</th>

                        <th style="width: 8%;">Currency</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($invoice->lines as $line)

                        <tr>

                            <td>

                                <div class="service-title">{{ $line->candidate_name ?: '-' }}</div>

                                <div class="candidate-sub">{{ $line->position_title ?: ($line->service_title ?: '-') }}</div>

                                @if($line->project_name)

                                    <div class="candidate-sub">Project: {{ $line->project_name }}</div>

                                @endif

                            </td>

                            <td class="desc">{{ $line->scope_description ?: '-' }}</td>

                            <td>

                                <div style="font-weight: 800; color: #112640;">

                                    {{ optional($line->service_period_start)->format('Y-m-d') ?: '-' }}

                                    <br>

                                    {{ optional($line->service_period_end)->format('Y-m-d') ?: '-' }}

                                </div>

                                @if($line->service_month_label)

                                    <div class="candidate-sub">{{ $line->service_month_label }}</div>

                                @endif

                            </td>

                            <td class="num">

                                {{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}

                            </td>

                            <td class="num">

                                {{ number_format((float) $line->unit_rate, 2) }}

                            </td>

                            <td class="num">

                                {{ number_format((float) $line->amount, 2) }}

                            </td>

                            <td class="num" style="text-align:left;">

                                {{ $line->currency ?: '-' }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7">No invoice lines found.</td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <table class="totals-row">

            <tr>

                <td class="terms-col">

                    <div class="card">

                        <div class="card-head">Terms & Notes</div>

                        <div class="card-body terms-body">{{ $invoice->terms_text ?: ($invoice->notes ?: '-') }}</div>

                    </div>

                </td>

                <td class="totals-col">

                    <div class="card">

                        <div class="card-head">Totals</div>

                        <div class="card-body">

                            <table class="totals-table">

                                <tr><td>Subtotal</td><td>{{ number_format((float) $invoice->subtotal_amount, 2) }} {{ $invoice->display_currency ?: ($invoice->foreign_currency ?: '') }}</td></tr>

                                <tr><td>Tax</td><td>{{ number_format((float) $invoice->tax_amount, 2) }} {{ $invoice->display_currency ?: ($invoice->foreign_currency ?: '') }}</td></tr>

                                <tr class="grand-row"><td>Total</td><td>{{ number_format((float) $invoice->total_amount, 2) }} {{ $invoice->display_currency ?: ($invoice->foreign_currency ?: '') }}</td></tr>

                                <tr><td>Foreign Amount Due</td><td>{{ number_format((float) $invoice->foreign_amount_due, 2) }} {{ $invoice->foreign_currency ?: '' }}</td></tr>

                                <tr><td>Local Amount Due</td><td>{{ number_format((float) $invoice->local_amount_due, 2) }} {{ $invoice->local_currency ?: '' }}</td></tr>

                            </table>

                        </div>

                    </div>

                </td>

            </tr>

        </table>

        <div class="card">

            <div class="card-head">Bank Details</div>

            <div class="card-body">

                <table class="bank-grid">

                    <tr>

                        <td>

                            <div class="bank-k">Bank Name</div>

                            <div class="bank-v">{{ $invoice->bank_name ?: '-' }}</div>

                        </td>

                        <td>

                            <div class="bank-k">Swift Code</div>

                            <div class="bank-v">{{ $invoice->swift_code ?: '-' }}</div>

                        </td>

                        <td>

                            <div class="bank-k">IBAN EUR</div>

                            <div class="bank-v">{{ $invoice->iban_eur ?: '-' }}</div>

                        </td>

                    </tr>

                    <tr>

                        <td>

                            <div class="bank-k">IBAN USD</div>

                            <div class="bank-v">{{ $invoice->iban_usd ?: '-' }}</div>

                        </td>

                        <td>

                            <div class="bank-k">IBAN LYD</div>

                            <div class="bank-v">{{ $invoice->iban_lyd ?: '-' }}</div>

                        </td>

                        <td>

                            <div class="bank-k">Account Number LYD</div>

                            <div class="bank-v">{{ $invoice->account_number_lyd ?: '-' }}</div>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

        <table class="sign-row">

            <tr>

                <td class="sign-left">

                    <div class="sign-card">

                        <div class="sign-title">Prepared By</div>

                        <div class="sign-line">{{ $invoice->createdBy?->name ?: 'Sada Fezzan' }}</div>

                    </div>

                </td>

                <td class="sign-right">

                    <div class="sign-card">

                        <div class="sign-title">Authorized Signature / Stamp</div>

                        <div class="sign-line">Sada Fezzan For Oil Services</div>

                    </div>

                </td>

            </tr>

        </table>

        <div class="footer-note">

            This invoice is system-generated and formatted in line with Sada Fezzan commercial identity.

        </div>

    </div>

    <script type="text/php">

        if (isset($pdf)) {

            $font = $fontMetrics->get_font("DejaVu Sans", "normal");


        }

    </script>

</body>

</html>
