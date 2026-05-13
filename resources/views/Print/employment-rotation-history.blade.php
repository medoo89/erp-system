<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Rotation History - {{ $employment->employee_name }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            font-size: 11px;
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


        .cover-head {
            border-radius: 16px;
            border: 1px solid #dbe7ef;
            background: linear-gradient(135deg, #f8fbfd, #eefcf8);
            padding: 12px 14px;
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

        .header {
            display: grid;
            grid-template-columns: 180px 1fr;
            align-items: center;
            gap: 18px;
            border-bottom: 4px solid #0f766e;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .logo {
            width: 150px;
            max-height: 56px;
            object-fit: contain;
        }

        .title {
            text-align: right;
        }

        .title h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1;
            color: #123a59;
            letter-spacing: -0.04em;
        }

        .title .sub {
            margin-top: 7px;
            color: #64748b;
            font-weight: 700;
            font-size: 11px;
        }

        .employee {
            border: 1px solid #dbe7ef;
            border-radius: 14px;
            padding: 12px;
            background: #f8fbfd;
            margin-bottom: 14px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .label {
            color: #64748b;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .09em;
            margin-bottom: 3px;
        }

        .value {
            color: #0f172a;
            font-weight: 900;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #dbe7ef;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #eaf3f7;
            color: #123a59;
            text-align: left;
            padding: 9px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
            border-bottom: 1px solid #dbe7ef;
        }

        td {
            padding: 9px;
            border-top: 1px solid #e2e8f0;
            vertical-align: top;
            font-weight: 700;
        }

        tr:nth-child(even) td {
            background: #fbfdff;
        }

        .badge {
            display: inline-block;
            border-radius: 999px;
            padding: 4px 8px;
            background: #e0f2fe;
            color: #075985;
            font-weight: 900;
            font-size: 10px;
        }

        .empty {
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            color: #64748b;
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
        }
    </style>
</head>

<body>
    @php
        $documentReference = 'SF-ERP-ROT-' . str_pad((string) $employment->id, 5, '0', STR_PAD_LEFT);
        $documentVersion = '1.2';
        $documentYear = '2026';
    @endphp
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">Print Document</button>
    </div>

    <div class="header">
        <img class="logo" src="{{ asset('images/sada-horizontal.png') }}" alt="Sada Fezzan">

        <div class="title">
            <h1>Rotation History</h1>
            <div class="sub">Sada Fezzan ERP · Official Rotation Record · Ref: {{ $documentReference }}</div>
        </div>
    </div>


    <section class="cover-head">
        <div>
            <div class="cover-kicker">Document Control</div>
            <div class="cover-title">Rotation History Documentation</div>
            <div class="cover-sub">Official rotation history generated from Sada Fezzan ERP.</div>
        </div>

        <div class="cover-ref">
            <div><span>Reference No.</span><strong>{{ $documentReference }}</strong></div>
            <div><span>Version</span><strong>{{ $documentVersion }}</strong></div>
            <div><span>Year</span><strong>{{ $documentYear }}</strong></div>
            <div><span>Print Date</span><strong>{{ now()->format('d M Y') }}</strong></div>
        </div>
    </section>

    <section class="employee">
        <div><div class="label">Employee</div><div class="value">{{ $employment->employee_name ?: '-' }}</div></div>
        <div><div class="label">Code</div><div class="value">{{ $employment->employee_code ?: '-' }}</div></div>
        <div><div class="label">Position</div><div class="value">{{ $employment->position_title ?: '-' }}</div></div>
        <div><div class="label">Project</div><div class="value">{{ $employment->project_name ?: '-' }}</div></div>
    </section>

    @if($rotations->count())
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Rotation</th>
                    <th>Status</th>
                    <th>Pattern</th>
                    <th>Mobilization</th>
                    <th>Work From</th>
                    <th>Work To</th>
                    <th>Demobilization</th>
                    <th>Travel Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rotations as $rotation)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $rotation->rotation_label ?: 'Rotation #' . $rotation->id }}</td>
                        <td><span class="badge">{{ ucfirst(str_replace('_', ' ', $rotation->status ?: '-')) }}</span></td>
                        <td>{{ $rotation->rotation_pattern ?: '-' }}</td>
                        <td>{{ optional($rotation->mobilization_date)->format('d M Y') ?: '-' }}</td>
                        <td>{{ optional($rotation->from_date)->format('d M Y') ?: '-' }}</td>
                        <td>{{ optional($rotation->to_date)->format('d M Y') ?: '-' }}</td>
                        <td>{{ optional($rotation->demobilization_date)->format('d M Y') ?: '-' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $rotation->travel_status ?: '-')) }}</td>
                        <td>{{ $rotation->notes ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty">No rotations found for this employee.</div>
    @endif

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

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 500));
    </script>
</body>
</html>
