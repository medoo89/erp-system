<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rotation History - {{ $employment->employee_name }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }

        :root {
            --bg: #f4f7fb;
            --panel: #ffffff;
            --line: #e5e7eb;
            --text: #0f172a;
            --muted: #64748b;
            --accent: #1d4ed8;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px;
            background: #fff;
        }

        .hero {
            border: 1px solid var(--line);
            border-radius: 28px;
            background:
                radial-gradient(circle at top right, rgba(29, 78, 216, 0.12), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: var(--shadow);
            padding: 18px 20px;
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
            font-size: 28px;
            line-height: 1.14;
            font-weight: 900;
            margin: 0;
            letter-spacing: -.4px;
        }

        .hero-subtitle {
            margin-top: 10px;
            color: var(--muted);
            font-size: 13px;
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

        .section {
            border: 1px solid var(--line);
            border-radius: 24px;
            overflow: hidden;
            background: #fff;
            box-shadow: var(--shadow);
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

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 18px;
        }

        th, td {
            padding: 8px 7px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
            font-size: 12px;
        }

        th {
            background: #f8fafc;
            color: #334155;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .35px;
            font-size: 10px;
        }

        tbody tr:nth-child(even) td {
            background: #fcfdff;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            background: #eef2f7;
            color: #334155;
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

        .empty {
            border: 1px dashed var(--line);
            border-radius: 16px;
            padding: 14px;
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

            .hero,
            .section {
                box-shadow: none;
            }

            .section {
                break-inside: auto;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="hero">
            <div class="hero-top">
                <div>
                    <div class="eyebrow">Sada Fezzan · Rotation History</div>
                    <h1 class="hero-title">{{ $employment->employee_name ?: '-' }}</h1>
                    <div class="hero-subtitle">
                        {{ $employment->position_title ?: '-' }} — {{ $employment->client_name ?: '-' }} / {{ $employment->project_name ?: '-' }}
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
                <div class="section-icon">📅</div>
                <div>
                    <div class="section-title">Full Rotation Timeline</div>
                    <div class="section-subtitle">Printable full history of all recorded rotations</div>
                </div>
            </div>
            <div class="section-body">
                @if ($rotations->count())
                    <table>
                        <thead>
                            <tr>
                                <th>Rotation</th>
                                <th>Status</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Mobilization</th>
                                <th>Demobilization</th>
                                <th>Pattern</th>
                                <th>Travel Status</th>
                                <th>Current</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rotations as $rotation)
                                <tr>
                                    <td>{{ $rotation->rotation_label ?: '-' }}</td>
                                    <td>
                                        <span class="status">
                                            {{ $rotation->status ? ucfirst(str_replace('_', ' ', $rotation->status)) : '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $rotation->from_date?->format('M j, Y') ?: '-' }}</td>
                                    <td>{{ $rotation->to_date?->format('M j, Y') ?: '-' }}</td>
                                    <td>{{ $rotation->mobilization_date?->format('M j, Y') ?: '-' }}</td>
                                    <td>{{ $rotation->demobilization_date?->format('M j, Y') ?: '-' }}</td>
                                    <td>{{ $rotation->rotation_pattern ?: '-' }}</td>
                                    <td>{{ $rotation->travel_status ? ucfirst(str_replace('_', ' ', $rotation->travel_status)) : '-' }}</td>
                                    <td>{{ $rotation->is_current ? 'Yes' : 'No' }}</td>
                                    <td>{{ $rotation->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No rotation history available yet.</div>
                @endif
            </div>
        </div>

        <div class="footer-actions">
            <button class="btn" onclick="window.print()">Print Rotation History</button>
        </div>
    </div>
</body>
</html>