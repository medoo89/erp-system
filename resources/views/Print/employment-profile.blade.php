<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employment Profile - {{ $employment->employee_name }}</title>
    <style>
        @page {
            margin: 11mm;
            size: A4;
        }

        :root {
            --bg: #f4f7fb;
            --panel: #ffffff;
            --line: #e5e7eb;
            --line-strong: #d7dce3;
            --text: #0f172a;
            --muted: #64748b;
            --soft: #94a3b8;
            --accent: #0f172a;
            --accent-2: #1d4ed8;
            --success: #166534;
            --success-bg: #dcfce7;
            --warning: #92400e;
            --warning-bg: #fef3c7;
            --danger: #991b1b;
            --danger-bg: #fee2e2;
            --info: #1d4ed8;
            --info-bg: #dbeafe;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            --radius-xl: 24px;
            --radius-lg: 18px;
            --radius-md: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Gilroy', 'Inter', Arial, sans-serif;
            color: var(--text);
            margin: 0;
            background: var(--bg);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            max-width: 1120px;
            margin: 0 auto;
            padding: 16px;
            background: var(--panel);
        }

        .hero {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 28px;
            background:
                radial-gradient(circle at top right, rgba(29, 78, 216, 0.12), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: var(--shadow);
            padding: 20px 22px 18px;
            margin-bottom: 14px;
        }

        .hero-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
        }

        .eyebrow {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .hero-title {
            font-size: 30px;
            line-height: 1.1;
            font-weight: 900;
            margin: 0;
            letter-spacing: -.4px;
            color: var(--text);
        }

        .hero-subtitle {
            margin-top: 10px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .mini-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid var(--line);
            color: #334155;
            font-size: 11px;
            font-weight: 700;
        }

        .code-card {
            min-width: 235px;
            border: 1px solid #dbe4ef;
            border-radius: 22px;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff;
            padding: 15px 17px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
        }

        .code-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
            opacity: .8;
            margin-bottom: 8px;
        }

        .code-value {
            font-size: 22px;
            line-height: 1.2;
            font-weight: 900;
            letter-spacing: .3px;
            word-break: break-word;
        }

        .print-meta {
            margin-top: 12px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 600;
        }

        .section {
            margin-top: 14px;
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            background: var(--panel);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: linear-gradient(180deg, #fbfdff 0%, #f8fafc 100%);
            border-bottom: 1px solid var(--line);
        }

        .section-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 16px;
            font-weight: 900;
        }

        .section-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: -.2px;
            color: var(--text);
            margin: 0;
        }

        .section-subtitle {
            margin-top: 2px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 600;
        }

        .section-body {
            padding: 14px 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .info-card {
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 12px 12px 10px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            min-height: 68px;
        }

        .label {
            font-size: 10px;
            font-weight: 800;
            color: var(--soft);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 7px;
        }

        .value {
            font-size: 14px;
            line-height: 1.35;
            font-weight: 800;
            color: var(--text);
            word-break: break-word;
        }

        .status-box {
            border-radius: var(--radius-lg);
            padding: 12px;
            min-height: 68px;
            color: #fff;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.08);
        }

        .status-box small {
            display: block;
            margin-bottom: 8px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            opacity: .88;
        }

        .status-box .status-value {
            font-size: 14px;
            font-weight: 900;
            line-height: 1.3;
        }

        .green { background: linear-gradient(135deg, #16a34a, #15803d); }
        .amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .blue { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
        .slate { background: linear-gradient(135deg, #475569, #334155); }

        .rotation-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .notes-box {
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 12px 14px;
            min-height: 56px;
            white-space: pre-wrap;
            line-height: 1.6;
            background: #fff;
            color: #334155;
            font-weight: 600;
        }

        .summary-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .summary-pill {
            border: 1px solid var(--line);
            background: #fbfdff;
            border-radius: 14px;
            padding: 10px 12px;
        }

        .summary-pill .num {
            font-size: 20px;
            font-weight: 900;
            color: var(--text);
            line-height: 1.1;
        }

        .summary-pill .txt {
            margin-top: 5px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 18px;
        }

        th, td {
            padding: 9px 8px;
            text-align: left;
            vertical-align: top;
            font-size: 12px;
            border-bottom: 1px solid var(--line);
        }

        th {
            background: #f8fafc;
            color: #334155;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .4px;
            font-size: 10px;
        }

        tbody tr:nth-child(even) td {
            background: #fcfdff;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .doc-badge {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap;
        }

        .doc-current {
            background: var(--success-bg);
            color: var(--success);
        }

        .doc-old {
            background: #eef2f7;
            color: #475569;
        }

        .doc-status {
            background: var(--info-bg);
            color: var(--info);
        }

        .footer-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
        }

        .btn {
            border: 0;
            border-radius: 14px;
            padding: 11px 16px;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.14);
        }

        .muted-empty {
            border: 1px dashed var(--line-strong);
            border-radius: 16px;
            padding: 12px 14px;
            color: var(--muted);
            background: #fafcff;
            font-weight: 700;
        }

        @media print {
            body {
                background: #fff;
            }

            .page {
                max-width: 100%;
                padding: 0;
            }

            .footer-actions {
                display: none;
            }

            .hero {
                break-inside: avoid;
                box-shadow: none;
            }

            .section {
                break-inside: auto;
                box-shadow: none;
            }

            .grid-3,
            .rotation-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="hero">
            <div class="hero-top">
                <div>
                    <div class="eyebrow">Sada Fezzan · Employment Profile</div>
                    <h1 class="hero-title">{{ $employment->employee_name ?: '-' }}</h1>

                    <div class="hero-subtitle">
                        <span class="mini-badge">Position: {{ $employment->position_title ?: '-' }}</span>
                        <span class="mini-badge">Client: {{ $employment->client_name ?: '-' }}</span>
                        <span class="mini-badge">Project: {{ $employment->project_name ?: '-' }}</span>
                    </div>

                    <div class="print-meta">
                        Printed on {{ now()->format('F j, Y · h:i A') }}
                    </div>
                </div>

                <div class="code-card">
                    <div class="code-label">Employee Code</div>
                    <div class="code-value">{{ $employment->employee_code ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div class="section-icon">👤</div>
                <div>
                    <div class="section-title">Executive Overview</div>
                    <div class="section-subtitle">Core employee identity and assignment details</div>
                </div>
            </div>
            <div class="section-body">
                <div class="grid-3">
                    <div class="info-card">
                        <div class="label">Employee</div>
                        <div class="value">{{ $employment->employee_name ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Position</div>
                        <div class="value">{{ $employment->position_title ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Operation Officer</div>
                        <div class="value">{{ $employment->operation_officer_name ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Client</div>
                        <div class="value">{{ $employment->client_name ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Project</div>
                        <div class="value">{{ $employment->project_name ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Overall Status</div>
                        <div class="value">{{ $employment->status ? ucfirst(str_replace('_', ' ', $employment->status)) : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div class="section-icon">📊</div>
                <div>
                    <div class="section-title">Tracking Status Dashboard</div>
                    <div class="section-subtitle">High-level operational, contractual, and compliance statuses</div>
                </div>
            </div>
            <div class="section-body">
                <div class="grid-4">
                    <div class="status-box green">
                        <small>Current Work Status</small>
                        <div class="status-value">{{ $employment->current_work_status ? ucfirst(str_replace('_', ' ', $employment->current_work_status)) : '-' }}</div>
                    </div>
                    <div class="status-box amber">
                        <small>Contract Status</small>
                        <div class="status-value">{{ $employment->contract_status ? ucfirst(str_replace('_', ' ', $employment->contract_status)) : '-' }}</div>
                    </div>
                    <div class="status-box blue">
                        <small>Medical Status</small>
                        <div class="status-value">{{ $employment->medical_status ? ucfirst(str_replace('_', ' ', $employment->medical_status)) : '-' }}</div>
                    </div>
                    <div class="status-box slate">
                        <small>Visa Status</small>
                        <div class="status-value">{{ $employment->visa_status ? ucfirst(str_replace('_', ' ', $employment->visa_status)) : '-' }}</div>
                    </div>
                </div>

                <div class="grid-3" style="margin-top: 12px;">
                    <div class="info-card">
                        <div class="label">Travel Status</div>
                        <div class="value">{{ $employment->travel_status ? ucfirst(str_replace('_', ' ', $employment->travel_status)) : '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Rotation Status</div>
                        <div class="value">{{ $employment->rotation_status ? ucfirst(str_replace('_', ' ', $employment->rotation_status)) : '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Rotation Pattern</div>
                        <div class="value">{{ $employment->rotation_pattern ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Mobilization Date</div>
                        <div class="value">{{ $employment->mobilization_date?->format('M j, Y') ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Demobilization Date</div>
                        <div class="value">{{ $employment->demobilization_date?->format('M j, Y') ?: '-' }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Work Location</div>
                        <div class="value">{{ $employment->work_location ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div class="section-icon">🔁</div>
                <div>
                    <div class="section-title">Current / Upcoming Rotation</div>
                    <div class="section-subtitle">Latest operational rotation snapshot</div>
                </div>
            </div>
            <div class="section-body">
                @if ($rotation)
                    <div class="rotation-grid">
                        <div class="info-card">
                            <div class="label">Rotation Label</div>
                            <div class="value">{{ $rotation->rotation_label ?: '-' }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">From Date</div>
                            <div class="value">{{ $rotation->from_date?->format('M j, Y') ?: '-' }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">To Date</div>
                            <div class="value">{{ $rotation->to_date?->format('M j, Y') ?: '-' }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Mobilization</div>
                            <div class="value">{{ $rotation->mobilization_date?->format('M j, Y') ?: '-' }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Demobilization</div>
                            <div class="value">{{ $rotation->demobilization_date?->format('M j, Y') ?: '-' }}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Travel Status</div>
                            <div class="value">{{ $rotation->travel_status ? ucfirst(str_replace('_', ' ', $rotation->travel_status)) : '-' }}</div>
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <div class="label">Rotation Notes</div>
                        <div class="notes-box" style="{{ blank($rotation->notes) ? 'min-height: 40px;' : '' }}">{{ $rotation->notes ?: '-' }}</div>
                    </div>
                @else
                    <div class="muted-empty">No rotation records available yet.</div>
                @endif
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div class="section-icon">📁</div>
                <div>
                    <div class="section-title">Uploaded Documents Summary</div>
                    <div class="section-subtitle">Compact printable summary of uploaded files</div>
                </div>
            </div>
            <div class="section-body">
                @if ($documents->count())
                    <div class="summary-strip">
                        <div class="summary-pill">
                            <div class="num">{{ $documents->count() }}</div>
                            <div class="txt">Total Documents</div>
                        </div>
                        <div class="summary-pill">
                            <div class="num">{{ $documents->where('current', 'Current')->count() }}</div>
                            <div class="txt">Current Versions</div>
                        </div>
                        <div class="summary-pill">
                            <div class="num">{{ $documents->where('submitted_by', 'Candidate')->count() }}</div>
                            <div class="txt">Candidate Uploads</div>
                        </div>
                        <div class="summary-pill">
                            <div class="num">{{ $documents->where('submitted_by', 'Admin')->count() }}</div>
                            <div class="txt">Admin Uploads</div>
                        </div>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Version</th>
                                <th>Current</th>
                                <th>Submitted By</th>
                                <th>Status</th>
                                <th>Document Date</th>
                                <th>Expiry Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $document)
                                <tr>
                                    <td>{{ $document['title'] }}</td>
                                    <td>{{ $document['category'] }}</td>
                                    <td>{{ $document['version'] }}</td>
                                    <td>
                                        <span class="doc-badge {{ $document['current'] === 'Current' ? 'doc-current' : 'doc-old' }}">
                                            {{ $document['current'] }}
                                        </span>
                                    </td>
                                    <td>{{ $document['submitted_by'] }}</td>
                                    <td>
                                        <span class="doc-badge doc-status">
                                            {{ $document['document_status'] }}
                                        </span>
                                    </td>
                                    <td>{{ $document['document_date'] }}</td>
                                    <td>{{ $document['expiry_date'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="muted-empty">No uploaded documents yet.</div>
                @endif
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <div class="section-icon">📝</div>
                <div>
                    <div class="section-title">Notes & Internal Remarks</div>
                    <div class="section-subtitle">Operational and internal notes summary</div>
                </div>
            </div>
            <div class="section-body">
                <div class="grid-3" style="grid-template-columns: 1fr 1fr;">
                    <div>
                        <div class="label">Operations Notes</div>
                        <div class="notes-box" style="{{ blank($employment->notes) ? 'min-height: 40px;' : '' }}">
                            {{ $employment->notes ?: '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="label">Internal Notes</div>
                        <div class="notes-box" style="{{ blank($employment->internal_notes) ? 'min-height: 40px;' : '' }}">
                            {{ $employment->internal_notes ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-actions">
            <button class="btn" onclick="window.print()">Print Profile</button>
        </div>
    </div>
</body>
</html>