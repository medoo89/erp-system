@php
    use Illuminate\Support\Facades\File;

    $logoCandidates = [
        public_path('images/sada-horizontal.png') => asset('images/sada-horizontal.png'),
        public_path('images/sada-logo.png') => asset('images/sada-logo.png'),
        public_path('portal-assets/sada-fezzan-logo-white.jpeg') => asset('portal-assets/sada-fezzan-logo-white.jpeg'),
    ];

    $invoiceLogoUrl = null;

    foreach ($logoCandidates as $path => $url) {
        if (File::exists($path)) {
            $invoiceLogoUrl = $url;
            break;
        }
    }

    $client = $invoice->client ?? null;
    $project = $invoice->project ?? null;
    $profile = $invoice->invoiceProfile ?? null;
    $bankProfile = $invoice->bankProfile ?? null;

    $fmtDate = fn ($date) => $date ? optional($date)->format('Y-m-d') : '-';

    $money = function ($amount, $currency = null) {
        $amount = is_numeric($amount) ? (float) $amount : 0;
        return number_format($amount, 2) . ($currency ? ' ' . $currency : '');
    };

    $displayCurrency = $invoice->display_currency ?: $invoice->foreign_currency ?: 'EUR';

    $billToName = $invoice->bill_to_name ?: ($client->name ?? '-');
    $billToAddress = $invoice->bill_to_address ?: ($client->address ?? '-');
    $billToPhone = $invoice->bill_to_phone ?: ($client->phone ?? $client->phone_number ?? '-');

    $periodText = trim(($fmtDate($invoice->period_start) ?: '-') . ' → ' . ($fmtDate($invoice->period_end) ?: '-'));

    $foreignSplit = trim((string) ($invoice->foreign_percentage ?? ''));
    $localSplit = trim((string) ($invoice->local_percentage ?? ''));

    $paymentTerms = $invoice->payment_terms_label ?: (
        ($foreignSplit !== '' || $localSplit !== '')
            ? trim(($foreignSplit !== '' ? $foreignSplit . '% in ' . ($invoice->foreign_currency ?: '-') : '') . (($foreignSplit !== '' && $localSplit !== '') ? ' + ' : '') . ($localSplit !== '' ? $localSplit . '% in ' . ($invoice->local_currency ?: '-') : ''))
            : '-'
    );

    $preparedBy = $invoice->createdBy?->name ?? auth()->user()?->name ?? 'Sada Fezzan';

    $termsText = $invoice->terms_text ?: $invoice->notes ?: 'It is agreed that the invoice amount shall be paid according to the payment terms stated above.';

    $bankName = $invoice->bank_name ?: ($bankProfile->bank_name ?? '-');
    $swift = $invoice->swift_code ?: ($bankProfile->swift_code ?? '-');

    $ibanLyd = $invoice->iban_lyd ?: '-';
    $ibanUsd = $invoice->iban_usd ?: '-';
    $ibanEur = $invoice->iban_eur ?: '-';
    $accountNumberLyd = $invoice->account_number_lyd ?: '-';

    $statusLabel = ucfirst(str_replace('_', ' ', (string) ($invoice->status ?: 'draft')));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eef5f7;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            padding: 12px;
        }

        .invoice-page {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
        }

        .hero {
            border-radius: 24px;
            overflow: hidden;
            background:
                radial-gradient(circle at 15% 10%, rgba(76, 167, 168, .22), transparent 34%),
                linear-gradient(135deg, #ffffff 0%, #f7fbfd 46%, #e7f6f4 100%);
            border: 1px solid #d4e4ea;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
            margin-bottom: 12px;
        }

        .hero-top {
            height: 7px;
            background: linear-gradient(90deg, #10243d, #1f4e76, #0f766e);
        }

        .hero-inner {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
            align-items: center;
            padding: 14px 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .logo-card {
            width: 86px;
            height: 66px;
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #d8e6eb;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .06);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex: 0 0 86px;
        }

        .logo-card img {
            width: 76px;
            max-width: 76px;
            height: 56px;
            max-height: 56px;
            object-fit: contain;
            display: block;
        }

        .logo-fallback {
            text-align: center;
            font-size: 8px;
            color: #1f4e76;
            font-weight: 900;
            line-height: 1.15;
        }

        .brand-name {
            font-size: 26px;
            line-height: 1;
            font-weight: 950;
            letter-spacing: -0.04em;
            color: #10243d;
            margin: 0;
        }

        .brand-kicker {
            margin-top: 5px;
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            background: #e5f4f3;
            color: #0f766e;
            font-size: 8px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .brand-sub {
            margin-top: 5px;
            color: #4b647c;
            font-size: 9px;
            font-weight: 750;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-title {
            margin: 0 0 8px;
            font-size: 34px;
            line-height: .9;
            font-weight: 950;
            letter-spacing: -0.05em;
            color: #10243d;
            text-transform: uppercase;
        }

        .meta-chip-wrap {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 6px;
        }

        .meta-chip {
            border-radius: 999px;
            background: rgba(255, 255, 255, .86);
            border: 1px solid #d6e5ec;
            padding: 6px 10px;
            color: #24445f;
            font-size: 8.5px;
            font-weight: 850;
        }

        .meta-chip strong {
            color: #0f172a;
            font-weight: 950;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .grid-main {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            align-items: start;
            margin-bottom: 10px;
        }

        .card {
            background: rgba(255, 255, 255, .94);
            border: 1px solid #d4e4ea;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
        }

        .card-title {
            background: linear-gradient(180deg, #edf6fa 0%, #e6f1f7 100%);
            color: #183a59;
            font-size: 8.7px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
            padding: 8px 10px;
            border-bottom: 1px solid #d8e6ee;
        }

        .card-body {
            padding: 9px 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 34%;
            color: #64748b;
            font-weight: 850;
            padding-right: 8px;
            font-size: 8.6px;
        }

        .info-table td:last-child {
            color: #0f172a;
            font-weight: 820;
            font-size: 9px;
        }

        .lines-card {
            margin-bottom: 10px;
        }

        .line-table {
            width: 100%;
            border-collapse: collapse;
        }

        .line-table th {
            background: linear-gradient(180deg, #edf6fa 0%, #e5f0f7 100%);
            color: #183a59;
            padding: 8px 8px;
            text-align: left;
            border-bottom: 1px solid #d8e6ee;
            font-size: 8.2px;
            font-weight: 950;
            letter-spacing: .05em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .line-table td {
            padding: 8px 8px;
            border-bottom: 1px solid #e3edf2;
            vertical-align: top;
            font-size: 8.7px;
        }

        .line-table tr:last-child td {
            border-bottom: 0;
        }

        .service-title {
            font-weight: 950;
            color: #10243d;
            margin-bottom: 2px;
        }

        .service-sub {
            color: #51677f;
            font-size: 8px;
            font-weight: 750;
            line-height: 1.35;
        }

        .description {
            color: #263d55;
            line-height: 1.35;
        }

        .description div {
            margin-bottom: 1px;
        }

        .num {
            text-align: right;
            white-space: nowrap;
            font-weight: 900;
            color: #10243d;
        }

        .center {
            text-align: center;
        }

        .terms-totals {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 10px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5edf3;
            font-size: 8.9px;
        }

        .totals-table tr:last-child td {
            border-bottom: 0;
        }

        .totals-table td:first-child {
            color: #52677e;
            font-weight: 850;
        }

        .totals-table td:last-child {
            text-align: right;
            color: #10243d;
            font-weight: 950;
            white-space: nowrap;
        }

        .total-row td {
            background: linear-gradient(90deg, #eaf3ff 0%, #e8fbf7 100%);
            color: #10243d !important;
            font-weight: 950 !important;
        }

        .terms-text {
            color: #263d55;
            font-size: 9px;
            line-height: 1.45;
            min-height: 46px;
        }

        .bank-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .bank-item {
            border-radius: 14px;
            background: #f7fbfd;
            border: 1px solid #e0ebf1;
            padding: 8px;
            min-height: 44px;
        }

        .bank-label {
            color: #64748b;
            font-size: 7.8px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 3px;
        }

        .bank-value {
            color: #0f172a;
            font-size: 8.6px;
            font-weight: 850;
            word-break: break-word;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .signature-box {
            min-height: 62px;
            padding: 10px;
            border-radius: 18px;
            border: 1px dashed #cbdde6;
            background: rgba(255,255,255,.78);
        }

        .signature-title {
            color: #183a59;
            font-size: 8.5px;
            font-weight: 950;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .signature-line {
            border-top: 1px solid #aebfca;
            padding-top: 5px;
            font-size: 8.5px;
            color: #64748b;
            font-weight: 850;
        }

        .footer {
            margin-top: 8px;
            color: #64748b;
            font-size: 8px;
            font-weight: 700;
        }

        .print-actions {
            position: fixed;
            right: 18px;
            bottom: 18px;
            display: flex;
            gap: 8px;
            z-index: 99;
        }

        .print-actions button {
            border: 0;
            border-radius: 999px;
            padding: 10px 14px;
            font-weight: 900;
            cursor: pointer;
            color: #fff;
            background: #0f766e;
            box-shadow: 0 10px 22px rgba(15, 118, 110, .22);
        }

        .print-actions button.secondary {
            background: #1f4e76;
        }

        @media print {
            html,
            body {
                background: #ffffff !important;
                font-size: 10px;
            }

            body {
                padding: 0;
            }

            .invoice-page {
                width: 100%;
                transform: none;
                transform-origin: top left;
            }

            .print-actions {
                display: none !important;
            }

            .hero,
            .card {
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .hero {
                margin-bottom: 7px;
            }

            .hero-inner {
                padding: 9px 12px;
                gap: 10px;
            }

            .logo-card {
                width: 72px;
                height: 52px;
                border-radius: 13px;
                flex-basis: 72px;
            }

            .logo-card img {
                width: 64px;
                max-width: 64px;
                height: 44px;
                max-height: 44px;
            }

            .brand-name {
                font-size: 20px;
            }

            .invoice-title {
                font-size: 26px;
            }

            .meta-chip {
                padding: 4px 8px;
                font-size: 7.5px;
            }

            .grid-2,
            .grid-main,
            .terms-totals,
            .signature-grid {
                gap: 6px;
                margin-bottom: 6px;
            }

            .card {
                border-radius: 12px;
            }

            .card-title {
                padding: 5px 7px;
                font-size: 7.2px;
            }

            .card-body {
                padding: 6px 7px;
            }

            .info-table td {
                padding: 2.5px 0;
            }

            .line-table th {
                padding: 5px 6px;
                font-size: 7px;
            }

            .line-table td {
                padding: 5px 6px;
                font-size: 7.3px;
            }

            .service-sub,
            .bank-label,
            .footer {
                font-size: 6.8px;
            }

            .bank-value,
            .totals-table td,
            .signature-line {
                font-size: 7.3px;
            }

            .bank-grid {
                gap: 5px;
            }

            .bank-item {
                padding: 5px;
                min-height: 34px;
                border-radius: 10px;
            }

            .signature-box {
                min-height: 42px;
                padding: 7px;
            }

            .signature-title {
                margin-bottom: 10px;
                font-size: 7px;
            }
        }


        /* FINAL PORTRAIT INVOICE OVERRIDES */
        .invoice-page {
            max-width: 794px;
            margin: 0 auto;
        }

        .hero-inner {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 14px 16px;
        }

        .invoice-meta {
            text-align: left;
        }

        .invoice-title {
            font-size: 30px;
            line-height: 1;
            margin-top: 6px;
        }

        .meta-chip-wrap {
            justify-content: flex-start;
        }

        .brand-name {
            font-size: 24px;
            line-height: 1.05;
            white-space: normal;
        }

        .grid-2,
        .grid-main,
        .terms-totals,
        .signature-grid {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .bank-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .line-table {
            table-layout: fixed;
        }

        .line-table th,
        .line-table td {
            white-space: normal;
            word-break: normal;
            overflow-wrap: anywhere;
        }

        .line-table th:nth-child(1),
        .line-table td:nth-child(1) {
            width: 17%;
        }

        .line-table th:nth-child(2),
        .line-table td:nth-child(2) {
            width: 31%;
        }

        .line-table th:nth-child(3),
        .line-table td:nth-child(3) {
            width: 15%;
        }

        .line-table th:nth-child(4),
        .line-table td:nth-child(4) {
            width: 8%;
        }

        .line-table th:nth-child(5),
        .line-table td:nth-child(5) {
            width: 10%;
        }

        .line-table th:nth-child(6),
        .line-table td:nth-child(6) {
            width: 12%;
        }

        .line-table th:nth-child(7),
        .line-table td:nth-child(7) {
            width: 7%;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            body {
                padding: 0 !important;
            }

            .invoice-page {
                width: 100% !important;
                max-width: 100% !important;
                transform: none !important;
            }

            .hero {
                margin-bottom: 6px !important;
                border-radius: 14px !important;
            }

            .hero-inner {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 6px !important;
                padding: 9px 10px !important;
            }

            .brand {
                gap: 8px !important;
            }

            .logo-card {
                width: 68px !important;
                height: 52px !important;
                flex: 0 0 68px !important;
                border-radius: 10px !important;
            }

            .logo-card img {
                width: 60px !important;
                max-width: 60px !important;
                height: 44px !important;
                max-height: 44px !important;
                object-fit: contain !important;
            }

            .brand-name {
                font-size: 18px !important;
                letter-spacing: -0.02em !important;
                white-space: normal !important;
            }

            .brand-kicker {
                font-size: 6.6px !important;
                padding: 3px 7px !important;
            }

            .brand-sub {
                font-size: 7px !important;
            }

            .invoice-title {
                font-size: 22px !important;
                margin: 2px 0 4px !important;
                text-align: left !important;
                letter-spacing: -0.03em !important;
            }

            .invoice-meta {
                text-align: left !important;
            }

            .meta-chip-wrap {
                justify-content: flex-start !important;
                gap: 4px !important;
            }

            .meta-chip {
                padding: 3px 6px !important;
                font-size: 6.8px !important;
            }

            .grid-2,
            .grid-main,
            .terms-totals,
            .signature-grid {
                display: grid !important;
                grid-template-columns: 1fr !important;
                gap: 5px !important;
                margin-bottom: 5px !important;
            }

            .card {
                border-radius: 10px !important;
            }

            .card-title {
                padding: 4px 6px !important;
                font-size: 6.8px !important;
            }

            .card-body {
                padding: 5px 6px !important;
            }

            .info-table td {
                padding: 2px 0 !important;
                font-size: 7px !important;
            }

            .line-table {
                table-layout: fixed !important;
                width: 100% !important;
            }

            .line-table th {
                padding: 4px 4px !important;
                font-size: 5.9px !important;
                line-height: 1.1 !important;
                white-space: normal !important;
            }

            .line-table td {
                padding: 4px 4px !important;
                font-size: 6.4px !important;
                line-height: 1.15 !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
            }

            .service-title {
                font-size: 6.8px !important;
            }

            .service-sub,
            .description {
                font-size: 6.1px !important;
                line-height: 1.18 !important;
            }

            .totals-table td,
            .bank-value {
                font-size: 6.8px !important;
                line-height: 1.2 !important;
            }

            .bank-grid {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 4px !important;
            }

            .bank-item {
                min-height: 28px !important;
                padding: 4px !important;
                border-radius: 8px !important;
            }

            .bank-label {
                font-size: 5.8px !important;
            }

            .signature-box {
                min-height: 34px !important;
                padding: 5px !important;
                border-radius: 9px !important;
            }

            .signature-title {
                font-size: 6px !important;
                margin-bottom: 7px !important;
            }

            .signature-line {
                font-size: 6.6px !important;
            }

            .footer {
                font-size: 6.2px !important;
                margin-top: 4px !important;
            }
        }

        /* FINAL: portrait readable print, no forced one-page shrinking */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            html,
            body {
                font-size: 10px !important;
                line-height: 1.35 !important;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                padding: 0 !important;
                transform: none !important;
                width: 100% !important;
            }

            .invoice-page {
                width: 100% !important;
                max-width: 100% !important;
                transform: none !important;
                transform-origin: initial !important;
            }

            .hero-inner {
                padding: 12px 14px !important;
                gap: 10px !important;
            }

            .brand-name {
                font-size: 24px !important;
                line-height: 1.05 !important;
            }

            .invoice-title {
                font-size: 30px !important;
                line-height: 1 !important;
            }

            .meta-chip {
                font-size: 8px !important;
                padding: 5px 8px !important;
            }

            .card-title {
                font-size: 8px !important;
                padding: 7px 9px !important;
            }

            .card-body {
                padding: 8px 9px !important;
            }

            .info-table td {
                font-size: 8.5px !important;
                padding: 3px 0 !important;
            }

            .line-table th {
                font-size: 7px !important;
                padding: 6px 5px !important;
                line-height: 1.2 !important;
            }

            .line-table td {
                font-size: 7.8px !important;
                padding: 6px 5px !important;
                line-height: 1.25 !important;
            }

            .service-title {
                font-size: 8px !important;
            }

            .service-sub,
            .description {
                font-size: 7.4px !important;
                line-height: 1.25 !important;
            }

            .totals-table td,
            .bank-value {
                font-size: 8px !important;
                line-height: 1.3 !important;
            }

            .bank-label {
                font-size: 6.8px !important;
            }

            .signature-box {
                min-height: 48px !important;
            }

            .signature-title {
                font-size: 7.2px !important;
                margin-bottom: 12px !important;
            }

            .signature-line {
                font-size: 8px !important;
            }

            .footer {
                font-size: 7px !important;
            }
        }

        /* FINAL READABLE PORTRAIT MODE - no forced one-page compression */
        @media print {
            @page {
                size: A4 portrait;
                margin: 11mm 10mm 12mm 10mm;
            }

            html,
            body {
                background: #ffffff !important;
                font-size: 11px !important;
                line-height: 1.45 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                padding: 0 !important;
                width: 100% !important;
                transform: none !important;
            }

            .invoice-page {
                width: 100% !important;
                max-width: 100% !important;
                transform: none !important;
                transform-origin: initial !important;
            }

            .hero {
                margin-bottom: 12px !important;
                border-radius: 18px !important;
            }

            .hero-inner {
                padding: 16px 18px !important;
                gap: 14px !important;
            }

            .logo-card {
                width: 86px !important;
                height: 66px !important;
                flex: 0 0 86px !important;
                border-radius: 16px !important;
            }

            .logo-card img {
                width: 76px !important;
                max-width: 76px !important;
                height: 56px !important;
                max-height: 56px !important;
                object-fit: contain !important;
            }

            .brand-name {
                font-size: 27px !important;
                line-height: 1.05 !important;
                letter-spacing: -0.03em !important;
            }

            .brand-kicker {
                font-size: 8px !important;
                padding: 4px 9px !important;
            }

            .brand-sub {
                font-size: 9px !important;
            }

            .invoice-title {
                font-size: 34px !important;
                line-height: 1 !important;
                letter-spacing: -0.04em !important;
            }

            .meta-chip {
                font-size: 8.5px !important;
                padding: 6px 10px !important;
            }

            .grid-2,
            .grid-main,
            .terms-totals {
                gap: 10px !important;
                margin-bottom: 10px !important;
            }

            .card {
                border-radius: 16px !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .card-title {
                font-size: 8.8px !important;
                padding: 8px 10px !important;
            }

            .card-body {
                padding: 10px 11px !important;
            }

            .info-table td {
                font-size: 9.4px !important;
                padding: 4px 0 !important;
                line-height: 1.35 !important;
            }

            .line-table {
                table-layout: fixed !important;
                width: 100% !important;
            }

            .line-table th {
                font-size: 7.6px !important;
                padding: 7px 6px !important;
                line-height: 1.25 !important;
                white-space: normal !important;
            }

            .line-table td {
                font-size: 8.6px !important;
                padding: 8px 6px !important;
                line-height: 1.35 !important;
                white-space: normal !important;
                overflow-wrap: anywhere !important;
            }

            .service-title {
                font-size: 9px !important;
            }

            .service-sub,
            .description {
                font-size: 8.2px !important;
                line-height: 1.35 !important;
            }

            .terms-text {
                font-size: 9.2px !important;
                line-height: 1.45 !important;
                min-height: 70px !important;
            }

            .totals-table td {
                font-size: 9.2px !important;
                padding: 7px 9px !important;
            }

            .bank-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }

            .bank-item {
                min-height: 46px !important;
                padding: 8px !important;
                border-radius: 12px !important;
            }

            .bank-label {
                font-size: 7.2px !important;
            }

            .bank-value {
                font-size: 8.8px !important;
                line-height: 1.35 !important;
            }

            .footer {
                font-size: 8px !important;
                margin-top: 8px !important;
            }
        }

        /* Signature / stamp area - screen and print */
        .signature-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 12px !important;
            margin-top: 14px !important;
            align-items: stretch !important;
        }

        .signature-box {
            min-height: 145px !important;
            padding: 12px !important;
            border-radius: 18px !important;
            border: 1px dashed #b9ccd8 !important;
            background: rgba(255,255,255,.92) !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .signature-title {
            color: #183a59 !important;
            font-size: 9px !important;
            font-weight: 950 !important;
            letter-spacing: .08em !important;
            text-transform: uppercase !important;
            margin-bottom: 12px !important;
        }

        .signature-content {
            display: grid !important;
            grid-template-columns: 120px 1fr !important;
            gap: 14px !important;
            align-items: end !important;
            min-height: 92px !important;
        }

        .stamp-area {
            height: 86px !important;
            border-radius: 999px !important;
            border: 2px dashed #8fb0bf !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #6b8494 !important;
            font-size: 8px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: .08em !important;
            text-align: center !important;
            background: #f8fbfd !important;
        }

        .client-stamp {
            border-style: dashed !important;
        }

        .signature-area {
            padding-bottom: 8px !important;
        }

        .signature-line {
            border-top: 1px solid #8fa6b4 !important;
            padding-top: 7px !important;
            color: #10243d !important;
            font-size: 9px !important;
            font-weight: 850 !important;
        }

        .signature-caption {
            margin-top: 4px !important;
            color: #64748b !important;
            font-size: 7.6px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: .06em !important;
        }

        @media print {
            .signature-grid {
                grid-template-columns: 1fr 1fr !important;
                gap: 10px !important;
                margin-top: 12px !important;
            }

            .signature-box {
                min-height: 130px !important;
                padding: 10px !important;
                border-radius: 14px !important;
            }

            .signature-content {
                grid-template-columns: 108px 1fr !important;
                min-height: 82px !important;
                gap: 12px !important;
            }

            .stamp-area {
                height: 78px !important;
                font-size: 7px !important;
            }

            .signature-line {
                font-size: 8.4px !important;
            }

            .signature-caption {
                font-size: 7px !important;
            }
        }

        /* FINAL TWO-COLUMN BLOCKS FOR PORTRAIT INVOICE */
        .grid-2 {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 12px !important;
            align-items: stretch !important;
        }

        .terms-totals {
            display: grid !important;
            grid-template-columns: 1.15fr .85fr !important;
            gap: 12px !important;
            align-items: stretch !important;
        }

        .grid-2 .card,
        .terms-totals .card {
            height: 100% !important;
        }

        .terms-totals .card:nth-child(2) {
            border-color: #bcd7e8 !important;
            background: linear-gradient(180deg, #ffffff 0%, #f4fbff 100%) !important;
        }

        .terms-totals .total-row td {
            background: linear-gradient(90deg, #dff1ff 0%, #ddfbf5 100%) !important;
            font-size: 10px !important;
        }

        @media print {
            .grid-2 {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 8px !important;
                margin-bottom: 8px !important;
            }

            .terms-totals {
                display: grid !important;
                grid-template-columns: 1.15fr .85fr !important;
                gap: 8px !important;
                margin-bottom: 8px !important;
            }

            .grid-2 .card,
            .terms-totals .card {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .terms-totals .total-row td {
                font-size: 9.4px !important;
                font-weight: 950 !important;
            }
        }

        @media screen and (max-width: 820px) {
            .grid-2,
            .terms-totals {
                grid-template-columns: 1fr !important;
            }
        }

        /* FINAL HEADER INVOICE NUMBER FOCUS */
        .invoice-label {
            color: #1f4e76;
            font-size: 9px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .invoice-number {
            margin: 0 0 8px;
            color: #10243d;
            font-size: 23px;
            line-height: 1.05;
            font-weight: 950;
            letter-spacing: -0.035em;
            word-break: break-word;
        }

        .meta-chip-wrap.compact {
            gap: 5px;
        }

        .meta-chip-wrap.compact .meta-chip {
            padding: 5px 8px;
            font-size: 8px;
        }

        .invoice-title {
            font-size: 22px !important;
        }

        @media print {
            .hero-inner {
                padding-top: 11px !important;
                padding-bottom: 11px !important;
            }

            .invoice-label {
                font-size: 7.2px !important;
                margin-bottom: 3px !important;
            }

            .invoice-number {
                font-size: 18px !important;
                margin-bottom: 5px !important;
                line-height: 1.05 !important;
            }

            .meta-chip-wrap.compact {
                gap: 3px !important;
            }

            .meta-chip-wrap.compact .meta-chip {
                padding: 3px 6px !important;
                font-size: 7px !important;
            }
        }

        /* FINAL HEADER: logo left, invoice details right */
        .hero-inner {
            grid-template-columns: minmax(0, 1fr) 330px !important;
            align-items: center !important;
        }

        .invoice-meta-right {
            text-align: right !important;
            justify-self: end !important;
            width: 100% !important;
            max-width: 330px !important;
            padding: 10px 12px !important;
            border-radius: 18px !important;
            background: rgba(255,255,255,.72) !important;
            border: 1px solid rgba(212,228,234,.95) !important;
        }

        .invoice-mini-label {
            color: #1f4e76 !important;
            font-size: 8px !important;
            font-weight: 950 !important;
            letter-spacing: .16em !important;
            text-transform: uppercase !important;
            margin-bottom: 4px !important;
        }

        .invoice-number-main {
            color: #10243d !important;
            font-size: 18px !important;
            line-height: 1.08 !important;
            font-weight: 950 !important;
            letter-spacing: -0.025em !important;
            margin-bottom: 8px !important;
            word-break: break-word !important;
        }

        .invoice-meta-row {
            display: flex !important;
            justify-content: space-between !important;
            gap: 10px !important;
            padding: 4px 0 !important;
            border-top: 1px solid rgba(216,230,238,.85) !important;
            font-size: 8.5px !important;
            line-height: 1.2 !important;
        }

        .invoice-meta-row span {
            color: #64748b !important;
            font-weight: 850 !important;
            text-transform: uppercase !important;
            letter-spacing: .06em !important;
        }

        .invoice-meta-row strong {
            color: #10243d !important;
            font-weight: 950 !important;
            text-align: right !important;
        }

        @media print {
            .hero-inner {
                grid-template-columns: minmax(0, 1fr) 300px !important;
                padding: 10px 12px !important;
            }

            .invoice-meta-right {
                max-width: 300px !important;
                padding: 8px 10px !important;
                border-radius: 14px !important;
            }

            .invoice-mini-label {
                font-size: 6.8px !important;
                margin-bottom: 3px !important;
            }

            .invoice-number-main {
                font-size: 15px !important;
                margin-bottom: 5px !important;
            }

            .invoice-meta-row {
                padding: 3px 0 !important;
                font-size: 7.2px !important;
            }
        }

        @media screen and (max-width: 820px) {
            .hero-inner {
                grid-template-columns: 1fr !important;
            }

            .invoice-meta-right {
                justify-self: stretch !important;
                max-width: none !important;
                text-align: left !important;
            }

            .invoice-meta-row strong {
                text-align: right !important;
            }
        }

    </style>
</head>

<body>
    <div class="invoice-page">
        <section class="hero">
            <div class="hero-top"></div>

            <div class="hero-inner">
                <div class="brand">
                    <div class="logo-card">
                        @if($invoiceLogoUrl)
                            <img src="{{ $invoiceLogoUrl }}" alt="Sada Fezzan Logo">
                        @else
                            <div class="logo-fallback">SADA<br>FEZZAN</div>
                        @endif
                    </div>

                    <div>
                        <h1 class="brand-name">Sada Fezzan</h1>
                        <div class="brand-kicker">Commercial Invoice</div>
                        <div class="brand-sub">Sada Fezzan For Oil Services</div>
                    </div>
                </div>

                <div class="invoice-meta invoice-meta-right">
                    <div class="invoice-mini-label">Invoice Number</div>
                    <div class="invoice-number-main">{{ $invoice->invoice_number ?: ('INV-' . $invoice->id) }}</div>

                    <div class="invoice-meta-row">
                        <span>Date</span>
                        <strong>{{ $fmtDate($invoice->invoice_date) }}</strong>
                    </div>

                    <div class="invoice-meta-row">
                        <span>Project</span>
                        <strong>{{ $project->name ?? $invoice->project_name ?? '-' }}</strong>
                    </div>

                    <div class="invoice-meta-row">
                        <span>Status</span>
                        <strong>{{ $statusLabel }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid-2">
            <div class="card">
                <div class="card-title">Bill To</div>
                <div class="card-body">
                    <table class="info-table">
                        <tr>
                            <td>Name</td>
                            <td>{{ $billToName }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $billToAddress }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{{ $billToPhone }}</td>
                        </tr>
                        <tr>
                            <td>Client</td>
                            <td>{{ $client->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Invoice Info</div>
                <div class="card-body">
                    <table class="info-table">
                        <tr>
                            <td>Period</td>
                            <td>{{ $periodText }}</td>
                        </tr>
                        <tr>
                            <td>Payment Terms</td>
                            <td>{{ $paymentTerms }}</td>
                        </tr>
                        <tr>
                            <td>Foreign Split</td>
                            <td>{{ $invoice->foreign_percentage ?? '-' }}% {{ $invoice->foreign_currency ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Local Split</td>
                            <td>{{ $invoice->local_percentage ?? '-' }}% {{ $invoice->local_currency ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Exchange Rate</td>
                            <td>{{ $invoice->exchange_rate ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

        <section class="card lines-card">
            <table class="line-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Employee / Service</th>
                        <th style="width: 31%;">Description</th>
                        <th style="width: 15%;">Service Period</th>
                        <th class="center" style="width: 8%;">Paid Days</th>
                        <th class="num" style="width: 10%;">Billing Rate</th>
                        <th class="num" style="width: 11%;">Line Amount</th>
                        <th style="width: 5%;">Currency</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($invoice->lines as $line)
                        @php
                            $candidateName = $line->candidate_name ?: $line->employment?->employee_name ?: '-';
                            $position = $line->position_title ?: $line->employment?->position_title ?: '-';
                            $lineProject = $line->project_name ?: ($project->name ?? '-');
                            $start = $fmtDate($line->service_period_start);
                            $end = $fmtDate($line->service_period_end);
                            $paidDays = $line->quantity ?? '-';
                            $unitRate = is_numeric($line->unit_rate) ? number_format((float) $line->unit_rate, 2) : '-';
                            $lineAmount = is_numeric($line->amount) ? number_format((float) $line->amount, 2) : '-';
                            $lineCurrency = $line->currency ?: $displayCurrency;
                        @endphp

                        <tr>
                            <td>
                                <div class="service-title">{{ $candidateName }}</div>
                                <div class="service-sub">{{ $position }}</div>
                                <div class="service-sub">Project: {{ $lineProject }}</div>
                            </td>

                            <td>
                                <div class="description">
                                    @if($line->scope_description)
                                        {!! nl2br(e($line->scope_description)) !!}
                                    @else
                                        <div>Candidate Name: {{ $candidateName }}</div>
                                        <div>Position: {{ $position }}</div>
                                        <div>Project Name: {{ $lineProject }}</div>
                                        <div>Date & Duration: {{ $start }} → {{ $end }}</div>
                                        <div>Paid Duration: {{ $paidDays }} day(s)</div>
                                        <div>Billing Rate: {{ $unitRate }} {{ $lineCurrency }}</div>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <strong>{{ $start }}</strong><br>
                                <strong>{{ $end }}</strong><br>
                                <span class="service-sub">{{ $line->service_month_label ?: '-' }}</span>
                            </td>

                            <td class="center"><strong>{{ $paidDays }}</strong></td>
                            <td class="num">{{ $unitRate }}</td>
                            <td class="num">{{ $lineAmount }}</td>
                            <td><strong>{{ $lineCurrency }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="center">No invoice lines available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="terms-totals">
            <div class="card">
                <div class="card-title">Terms & Notes</div>
                <div class="card-body">
                    <div class="terms-text">{!! nl2br(e($termsText)) !!}</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">Totals</div>
                <div class="card-body" style="padding: 0;">
                    <table class="totals-table">
                        <tr>
                            <td>Subtotal</td>
                            <td>{{ $money($invoice->subtotal_amount, $displayCurrency) }}</td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td>{{ $money($invoice->tax_amount, $displayCurrency) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td>Total</td>
                            <td>{{ $money($invoice->total_amount, $displayCurrency) }}</td>
                        </tr>
                        <tr>
                            <td>Foreign Amount Due</td>
                            <td>{{ $money($invoice->foreign_amount_due, $invoice->foreign_currency ?: $displayCurrency) }}</td>
                        </tr>
                        <tr>
                            <td>Local Amount Due</td>
                            <td>{{ $money($invoice->local_amount_due, $invoice->local_currency ?: 'LYD') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-title">Bank Details</div>
            <div class="card-body">
                <div class="bank-grid">
                    <div class="bank-item">
                        <div class="bank-label">Bank Name</div>
                        <div class="bank-value">{{ $bankName }}</div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-label">Swift Code</div>
                        <div class="bank-value">{{ $swift }}</div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-label">IBAN EUR</div>
                        <div class="bank-value">{{ $ibanEur }}</div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-label">IBAN USD</div>
                        <div class="bank-value">{{ $ibanUsd }}</div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-label">IBAN LYD</div>
                        <div class="bank-value">{{ $ibanLyd }}</div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-label">Account Number LYD</div>
                        <div class="bank-value">{{ $accountNumberLyd }}</div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-label">Branch</div>
                        <div class="bank-value">{{ $bankProfile->branch_name ?? '-' }}</div>
                    </div>

                    <div class="bank-item">
                        <div class="bank-label">Beneficiary</div>
                        <div class="bank-value">{{ $bankProfile->beneficiary_name ?? 'Sada Fezzan' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="signature-grid">
            <div class="signature-box signature-company">
                <div class="signature-title">Prepared By</div>

                <div class="signature-content">
                    <div class="stamp-area">
                        <div class="stamp-label">Company Stamp</div>
                    </div>

                    <div class="signature-area">
                        <div class="signature-line">{{ $preparedBy }}</div>
                        <div class="signature-caption">Authorized Signature</div>
                    </div>
                </div>
            </div>

            <div class="signature-box signature-client">
                <div class="signature-title">Client Acknowledgement</div>

                <div class="signature-content">
                    <div class="stamp-area client-stamp">
                        <div class="stamp-label">Client Stamp</div>
                    </div>

                    <div class="signature-area">
                        <div class="signature-line">Signature / Name</div>
                        <div class="signature-caption">Client Confirmation</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="footer">
            This invoice was generated by Sada Fezzan ERP.
        </div>
    </div>

    <div class="print-actions no-print">
        <button type="button" onclick="window.print()">Print Invoice</button>
        <button type="button" class="secondary" onclick="window.close()">Close</button>
    </div>
</body>
</html>
