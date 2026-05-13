<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Employment Profile - {{ $employment->employee_name }}</title>

    <style>
        @page {
            size: A4;
            margin: 13mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            font-size: 11.5px;
            line-height: 1.45;
        }

        .no-print {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 1000;
        }

        .print-btn {
            border: 0;
            border-radius: 999px;
            padding: 10px 18px;
            background: #2563eb;
            color: #ffffff;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 28px rgba(37, 99, 235, .18);
        }

        .doc {
            width: 100%;
        }

        .header {
            display: grid;
            grid-template-columns: 190px 1fr;
            align-items: center;
            gap: 18px;
            border-bottom: 4px solid #0f766e;
            padding-bottom: 13px;
            margin-bottom: 16px;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo {
            width: 150px;
            max-height: 58px;
            object-fit: contain;
        }

        .doc-title {
            text-align: right;
        }

        .doc-title h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1;
            color: #123a59;
            letter-spacing: -0.04em;
        }

        .doc-title .meta {
            margin-top: 7px;
            color: #64748b;
            font-weight: 700;
            font-size: 11px;
        }


        .cover-head {
            border-radius: 16px;
            border: 1px solid #dbe7ef;
            background:
                linear-gradient(135deg, #f8fbfd, #eefcf8);
            padding: 14px 16px;
            margin-bottom: 14px;
            display: grid;
            grid-template-columns: 1fr 330px;
            gap: 14px;
            align-items: center;
        }

        .cover-kicker {
            width: fit-content;
            border-radius: 999px;
            padding: 5px 9px;
            background: #e0f2fe;
            color: #075985;
            font-size: 9px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .cover-title {
            color: #123a59;
            font-size: 20px;
            font-weight: 950;
            letter-spacing: -.04em;
            line-height: 1;
        }

        .cover-sub {
            margin-top: 6px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .cover-ref {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 7px;
        }

        .cover-ref div {
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 8px;
        }

        .cover-ref span {
            display: block;
            color: #64748b;
            font-size: 8.5px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .cover-ref strong {
            display: block;
            color: #0f172a;
            font-size: 11px;
            font-weight: 950;
        }

        .doc-control-footer {
            margin-top: 16px;
            border: 1px solid #dbe7ef;
            border-radius: 14px;
            padding: 10px 12px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: center;
            background: #f8fbfd;
        }

        .doc-control-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .doc-control-item span {
            display: block;
            color: #64748b;
            font-size: 8.5px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .doc-control-item strong {
            display: block;
            margin-top: 3px;
            color: #0f172a;
            font-size: 10.5px;
            font-weight: 950;
        }

        .powered-by {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #64748b;
            font-size: 10px;
            font-weight: 850;
            white-space: nowrap;
        }

        .powered-by img {
            width: 26px;
            height: 26px;
            object-fit: cover;
            border-radius: 999px;
            border: 1px solid #dbe7ef;
            background: #fff;
        }

        .powered-by strong {
            color: #0f172a;
        }

        .profile-hero {
            border: 1px solid #dbe7ef;
            border-radius: 16px;
            background: #f8fbfd;
            padding: 16px;
            margin-bottom: 14px;
            display: grid;
            grid-template-columns: 1fr 250px;
            gap: 12px;
        }

        .employee-name {
            margin: 0 0 8px;
            font-size: 30px;
            line-height: 1;
            letter-spacing: -0.05em;
            color: #0f172a;
            font-weight: 900;
        }

        .employee-sub {
            color: #64748b;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 10px;
            background: #e0f2fe;
            color: #075985;
            border: 1px solid #bae6fd;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .summary-box {
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 6px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .summary-row:last-child {
            border-bottom: 0;
        }

        .summary-label {
            color: #64748b;
            font-weight: 800;
        }

        .summary-value {
            color: #0f172a;
            font-weight: 900;
            text-align: right;
        }

        .section {
            margin-top: 14px;
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #123a59;
            font-size: 14px;
            font-weight: 900;
            margin: 0 0 8px;
            padding-bottom: 7px;
            border-bottom: 1px solid #dbe7ef;
        }

        .section-title span {
            color: #94a3b8;
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .grid-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .field {
            border: 1px solid #e2e8f0;
            border-radius: 11px;
            padding: 9px 10px;
            min-height: 52px;
            background: #ffffff;
        }

        .label {
            color: #64748b;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .09em;
            margin-bottom: 4px;
        }

        .value {
            color: #0f172a;
            font-size: 12px;
            font-weight: 800;
            word-break: break-word;
        }

        .notes {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 11px;
            min-height: 56px;
            white-space: pre-wrap;
            color: #334155;
            background: #ffffff;
        }

        .signatures {
            margin-top: 22px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .signature {
            border-top: 1px solid #94a3b8;
            padding-top: 7px;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .footer {
            margin-top: 16px;
            padding-top: 9px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 9.5px;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .section {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">Print Document</button>
    </div>

    @php
        $category = ucfirst(str_replace('_', ' ', (string) ($employment->employee_category ?: 'operational')));
        $contractType = $employment->is_open_ended_contract
            ? 'Open-ended Contract'
            : ucfirst(str_replace('_', ' ', (string) ($employment->contract_type ?: '-')));

        $documentReference = 'SF-ERP-EMP-' . str_pad((string) $employment->id, 5, '0', STR_PAD_LEFT);
        $documentVersion = '1.2';
        $documentYear = '2026';
    @endphp

    <main class="doc">
        <header class="header">
            <div class="logo-wrap">
                <img class="logo" src="{{ asset('images/sada-horizontal.png') }}" alt="Sada Fezzan">
            </div>

            <div class="doc-title">
                <h1>Employment Profile</h1>
                <div class="meta">Sada Fezzan ERP · Official Employment Record · Ref: {{ $documentReference }}</div>
            </div>
        </header>

        <section class="cover-head">
            <div>
                <div class="cover-kicker">Document Control</div>
                <div class="cover-title">Employment Documentation File</div>
                <div class="cover-sub">Official employment profile generated from Sada Fezzan ERP.</div>
            </div>

            <div class="cover-ref">
                <div><span>Reference No.</span><strong>{{ $documentReference }}</strong></div>
                <div><span>Version</span><strong>{{ $documentVersion }}</strong></div>
                <div><span>Year</span><strong>{{ $documentYear }}</strong></div>
                <div><span>Print Date</span><strong>{{ now()->format('d M Y') }}</strong></div>
            </div>
        </section>


        <section class="profile-hero">
            <div>
                <h2 class="employee-name">{{ $employment->employee_name ?: '-' }}</h2>
                <div class="employee-sub">
                    {{ $employment->position_title ?: '-' }} · {{ $employment->employee_email ?: '-' }}
                </div>

                <div class="chips">
                    <span class="chip">{{ $category }}</span>
                    <span class="chip">{{ ucfirst(str_replace('_', ' ', $employment->status ?: '-')) }}</span>
                    <span class="chip">{{ $employment->employee_code ?: 'No Code' }}</span>
                    <span class="chip">{{ $contractType }}</span>
                </div>
            </div>

            <div class="summary-box">
                <div class="summary-row">
                    <div class="summary-label">Client</div>
                    <div class="summary-value">{{ $employment->client_name ?: '-' }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Project</div>
                    <div class="summary-value">{{ $employment->project_name ?: '-' }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Department</div>
                    <div class="summary-value">{{ $employment->office_department ?: '-' }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Generated By</div>
                    <div class="summary-value">{{ auth()->user()?->name ?: 'System' }}</div>
                </div>
            </div>
        </section>

        <section class="section">
            <h3 class="section-title">Basic Information <span>Employee Identity</span></h3>
            <div class="grid">
                <div class="field"><div class="label">Employee Name</div><div class="value">{{ $employment->employee_name ?: '-' }}</div></div>
                <div class="field"><div class="label">Employee Code</div><div class="value">{{ $employment->employee_code ?: '-' }}</div></div>
                <div class="field"><div class="label">Email</div><div class="value">{{ $employment->employee_email ?: '-' }}</div></div>
                <div class="field"><div class="label">Phone</div><div class="value">{{ $employment->employee_phone ?: '-' }}</div></div>
                <div class="field"><div class="label">Position</div><div class="value">{{ $employment->position_title ?: '-' }}</div></div>
                <div class="field"><div class="label">Operation Officer</div><div class="value">{{ $employment->operation_officer_name ?: '-' }}</div></div>
            </div>
        </section>

        <section class="section">
            <h3 class="section-title">Contract & Employment Status <span>Documentation</span></h3>
            <div class="grid grid-3">
                <div class="field"><div class="label">Employment Type</div><div class="value">{{ $category }}</div></div>
                <div class="field"><div class="label">Status</div><div class="value">{{ ucfirst(str_replace('_', ' ', $employment->status ?: '-')) }}</div></div>
                <div class="field"><div class="label">Current Work Status</div><div class="value">{{ ucfirst(str_replace('_', ' ', $employment->current_work_status ?: '-')) }}</div></div>
                <div class="field"><div class="label">Contract Type</div><div class="value">{{ $contractType }}</div></div>
                <div class="field"><div class="label">Contract Status</div><div class="value">{{ ucfirst(str_replace('_', ' ', $employment->contract_status ?: '-')) }}</div></div>
                <div class="field"><div class="label">Work Location</div><div class="value">{{ $employment->work_location ?: '-' }}</div></div>
                <div class="field"><div class="label">Contract Start Date</div><div class="value">{{ optional($employment->contract_start_date)->format('d M Y') ?: '-' }}</div></div>
                <div class="field"><div class="label">Contract End Date</div><div class="value">{{ $employment->is_open_ended_contract ? 'Open-ended' : (optional($employment->contract_end_date)->format('d M Y') ?: '-') }}</div></div>
                <div class="field"><div class="label">Rotation Pattern</div><div class="value">{{ $employment->rotation_pattern ?: '-' }}</div></div>
            </div>
        </section>

        <section class="section">
            <h3 class="section-title">Mobilization / Medical / Visa / Travel <span>Operational Readiness</span></h3>
            <div class="grid grid-3">
                <div class="field"><div class="label">Mobilization Date</div><div class="value">{{ optional($employment->mobilization_date)->format('d M Y') ?: '-' }}</div></div>
                <div class="field"><div class="label">Demobilization Date</div><div class="value">{{ optional($employment->demobilization_date)->format('d M Y') ?: '-' }}</div></div>
                <div class="field"><div class="label">Travel Status</div><div class="value">{{ ucfirst(str_replace('_', ' ', $employment->travel_status ?: '-')) }}</div></div>
                <div class="field"><div class="label">Medical Status</div><div class="value">{{ ucfirst(str_replace('_', ' ', $employment->medical_status ?: '-')) }}</div></div>
                <div class="field"><div class="label">Medical Date</div><div class="value">{{ optional($employment->medical_date)->format('d M Y') ?: '-' }}</div></div>
                <div class="field"><div class="label">Medical Expiry</div><div class="value">{{ optional($employment->medical_expiry_date)->format('d M Y') ?: '-' }}</div></div>
                <div class="field"><div class="label">Visa Status</div><div class="value">{{ ucfirst(str_replace('_', ' ', $employment->visa_status ?: '-')) }}</div></div>
                <div class="field"><div class="label">Visa Issue Date</div><div class="value">{{ optional($employment->visa_issue_date)->format('d M Y') ?: '-' }}</div></div>
                <div class="field"><div class="label">Visa Expiry</div><div class="value">{{ optional($employment->visa_expiry_date)->format('d M Y') ?: '-' }}</div></div>
            </div>
        </section>

        <section class="section">
            <h3 class="section-title">Notes <span>Internal Record</span></h3>
            <div class="notes">{{ $employment->notes ?: 'No notes added.' }}</div>
        </section>

        <section class="signatures">
            <div class="signature">Prepared By / HR</div>
            <div class="signature">Reviewed By / Operations</div>
            <div class="signature">Approved By / Management</div>
        </section>

        <section class="doc-control-footer">
            <div class="doc-control-grid">
                <div class="doc-control-item">
                    <span>Document Ref</span>
                    <strong>{{ $documentReference }}</strong>
                </div>
                <div class="doc-control-item">
                    <span>Version</span>
                    <strong>{{ $documentVersion }}</strong>
                </div>
                <div class="doc-control-item">
                    <span>Generated By</span>
                    <strong>{{ auth()->user()?->name ?: 'System' }}</strong>
                </div>
                <div class="doc-control-item">
                    <span>Printed At</span>
                    <strong>{{ now()->format('d M Y H:i') }}</strong>
                </div>
            </div>

            <div class="powered-by">
                <span>Powered by</span>
                <img src="{{ asset('images/cancello-studio-logo.jpeg') }}" alt="Cancello Studio">
                <strong>Cancello Studio</strong>
                <span>© 2026 · v{{ $documentVersion }}</span>
            </div>
        </section>
    </main>

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 500));
    </script>
</body>
</html>


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

