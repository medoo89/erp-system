<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Portal Preview - {{ $employment->employee_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        :root {
            --navy: #081a34;
            --blue: #12385d;
            --teal: #4ca7a8;
            --gold: #f2b705;
            --text: #0f172a;
            --muted: #64748b;
            --line: #d7e2e5;
            --bg: #edf7f8;
            --card: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 90% 0%, rgba(76,167,168,.18), transparent 34%),
                radial-gradient(circle at 8% 20%, rgba(242,183,5,.08), transparent 26%),
                linear-gradient(180deg, #f8fcfd 0%, var(--bg) 100%);
        }

        .page {
            max-width: 1240px;
            margin: 0 auto;
            padding: 28px 22px 50px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            margin-bottom: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--teal), var(--blue));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 950;
            box-shadow: 0 14px 28px rgba(15,23,42,.12);
        }

        .brand-title {
            font-weight: 950;
            color: var(--navy);
            line-height: 1.1;
        }

        .brand-subtitle {
            font-size: 12px;
            color: var(--muted);
            font-weight: 750;
            margin-top: 3px;
        }

        .admin-badge {
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            display: inline-flex;
            align-items: center;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 34px;
            color: #fff;
            background:
                radial-gradient(circle at 92% 18%, rgba(76,167,168,.26), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(242,183,5,.14), transparent 30%),
                linear-gradient(135deg,#081a34 0%,#12385d 56%,#2f6f73 100%);
            box-shadow: 0 22px 46px rgba(15,23,42,.16);
            border: 1px solid rgba(76,167,168,.24);
            margin-bottom: 22px;
        }

        .hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--teal), var(--gold));
        }

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 20px;
            align-items: end;
        }

        .kicker {
            color: rgba(255,255,255,.72);
            font-size: 13px;
            font-weight: 850;
            margin-bottom: 10px;
        }

        h1 {
            margin: 0;
            font-size: clamp(42px, 5vw, 72px);
            line-height: .94;
            letter-spacing: -.06em;
        }

        .subtitle {
            margin-top: 14px;
            max-width: 760px;
            color: rgba(255,255,255,.82);
            font-size: 15px;
            line-height: 1.7;
            font-weight: 650;
        }

        .hero-pills {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pill {
            min-height: 34px;
            padding: 0 13px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.14);
            color: #fff;
            display: inline-flex;
            align-items: center;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hero-status {
            display: grid;
            gap: 12px;
        }

        .status-card {
            border-radius: 22px;
            padding: 16px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.14);
            backdrop-filter: blur(12px);
        }

        .status-label {
            color: rgba(255,255,255,.68);
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .status-value {
            color: #fff;
            font-size: 18px;
            font-weight: 950;
            line-height: 1.2;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat {
            border-radius: 24px;
            border: 1px solid var(--line);
            background: rgba(255,255,255,.86);
            box-shadow: 0 14px 30px rgba(15,23,42,.07);
            padding: 18px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 22px;
            line-height: 1.15;
            font-weight: 950;
            letter-spacing: -.03em;
            color: var(--text);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .card {
            border-radius: 28px;
            border: 1px solid var(--line);
            background:
                radial-gradient(circle at 88% 8%, rgba(76,167,168,.08), transparent 30%),
                linear-gradient(180deg,#ffffff 0%,#f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15,23,42,.07);
            overflow: hidden;
        }

        .card-head {
            padding: 18px 20px;
            border-bottom: 1px solid #e4ecef;
            background: linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%);
        }

        .card-title {
            font-size: 16px;
            font-weight: 950;
            color: var(--text);
        }

        .card-body {
            padding: 18px 20px;
        }

        .list {
            display: grid;
            gap: 10px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 13px;
            border-radius: 16px;
            background: rgba(248,252,253,.92);
            border: 1px solid #e4ecef;
        }

        .row-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 850;
        }

        .row-value {
            color: var(--text);
            font-size: 13px;
            font-weight: 950;
            text-align: right;
        }

        .empty {
            padding: 18px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: var(--muted);
            font-weight: 800;
            text-align: center;
        }

        .timeline {
            display: grid;
            gap: 12px;
        }

        .timeline-item {
            display: flex;
            gap: 12px;
            padding: 13px;
            border-radius: 18px;
            background: rgba(248,252,253,.92);
            border: 1px solid #e4ecef;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: var(--teal);
            margin-top: 3px;
            box-shadow: 0 0 0 4px rgba(76,167,168,.15);
            flex: 0 0 auto;
        }

        .timeline-title {
            font-size: 13px;
            font-weight: 950;
            color: var(--text);
        }

        .timeline-meta {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        @media (max-width: 1050px) {
            .hero-grid,
            .content-grid {
                grid-template-columns: 1fr;
            }

            .grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .grid-4 {
                grid-template-columns: 1fr;
            }

            .hero {
                padding: 26px 22px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    @php
        $portalStatus = $portalUser?->portal_status ?? 'not_created';
        $portalAccess = (bool) ($portalUser?->portal_access_enabled ?? false);
        $latestSalarySlip = $employment->salarySlips->first();
        $currentRotation = $employment->currentRotation;
        $latestDocs = $employment->latestDocuments->take(5);
        $latestFiles = $employment->files->take(5);
        $latestExpenses = $employment->financeExpenses->take(5);
    @endphp

    <div class="page">
        <div class="topbar">
            <div class="brand">
                <div class="brand-logo">SF</div>
                <div>
                    <div class="brand-title">Sada Fezzan Employee Portal</div>
                    <div class="brand-subtitle">Admin preview mode</div>
                </div>
            </div>

            <div class="admin-badge">Admin Preview Only</div>
        </div>

        <section class="hero">
            <div class="hero-grid">
                <div>
                    <div class="kicker">Employee Portal Preview</div>
                    <h1>{{ $employment->employee_name ?: 'Employee' }}</h1>
                    <div class="subtitle">
                        This page shows how the employee portal profile will look for this employment record. It is a protected admin preview and does not log in as the employee.
                    </div>

                    <div class="hero-pills">
                        <div class="pill">{{ $employment->employee_code ?: 'No Code' }}</div>
                        <div class="pill">{{ $employment->position_title ?: 'No Position' }}</div>
                        <div class="pill">{{ $employment->client_name ?: 'No Client' }}</div>
                        <div class="pill">{{ $employment->project_name ?: 'No Project' }}</div>
                    </div>
                </div>

                <div class="hero-status">
                    <div class="status-card">
                        <div class="status-label">Portal Access</div>
                        <div class="status-value">{{ $portalAccess ? 'Enabled' : 'Disabled' }}</div>
                    </div>

                    <div class="status-card">
                        <div class="status-label">Portal Status</div>
                        <div class="status-value">{{ str_replace('_', ' ', $portalStatus) }}</div>
                    </div>

                    <div class="status-card">
                        <div class="status-label">Portal Email</div>
                        <div class="status-value">{{ $portalUser?->email ?? $employment->employee_email ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid-4">
            <div class="stat">
                <div class="stat-label">Current Work Status</div>
                <div class="stat-value">{{ $employment->current_work_status ?: $employment->status ?: '-' }}</div>
            </div>

            <div class="stat">
                <div class="stat-label">Rotation Status</div>
                <div class="stat-value">{{ $employment->rotation_status ?: '-' }}</div>
            </div>

            <div class="stat">
                <div class="stat-label">Salary Slips</div>
                <div class="stat-value">{{ $employment->salarySlips->count() }}</div>
            </div>

            <div class="stat">
                <div class="stat-label">Files / Documents</div>
                <div class="stat-value">{{ $employment->files->count() + $employment->documents->count() }}</div>
            </div>
        </section>

        <section class="content-grid">
            <div class="card">
                <div class="card-head">
                    <div class="card-title">Current Rotation</div>
                </div>
                <div class="card-body">
                    @if($currentRotation)
                        <div class="list">
                            <div class="row">
                                <div class="row-label">Rotation</div>
                                <div class="row-value">{{ $currentRotation->rotation_label ?? $currentRotation->rotation_code ?? 'Current Rotation' }}</div>
                            </div>
                            <div class="row">
                                <div class="row-label">From</div>
                                <div class="row-value">{{ optional($currentRotation->from_date)->format('Y-m-d') ?: '-' }}</div>
                            </div>
                            <div class="row">
                                <div class="row-label">To</div>
                                <div class="row-value">{{ optional($currentRotation->to_date)->format('Y-m-d') ?: '-' }}</div>
                            </div>
                            <div class="row">
                                <div class="row-label">Travel Status</div>
                                <div class="row-value">{{ $currentRotation->travel_status ?? '-' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty">No current rotation is available.</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-title">Latest Salary Slip</div>
                </div>
                <div class="card-body">
                    @if($latestSalarySlip)
                        <div class="list">
                            <div class="row">
                                <div class="row-label">Period</div>
                                <div class="row-value">{{ $latestSalarySlip->salary_month }}/{{ $latestSalarySlip->salary_year }}</div>
                            </div>
                            <div class="row">
                                <div class="row-label">Status</div>
                                <div class="row-value">{{ $latestSalarySlip->status }}</div>
                            </div>
                            <div class="row">
                                <div class="row-label">Net Amount</div>
                                <div class="row-value">{{ number_format((float) $latestSalarySlip->net_amount, 2) }} {{ $latestSalarySlip->currency ?? $employment->resolvedSalaryCurrency() }}</div>
                            </div>
                        </div>
                    @else
                        <div class="empty">No salary slips are available yet.</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-title">Recent Files</div>
                </div>
                <div class="card-body">
                    @if($latestFiles->count())
                        <div class="timeline">
                            @foreach($latestFiles as $file)
                                <div class="timeline-item">
                                    <div class="dot"></div>
                                    <div>
                                        <div class="timeline-title">{{ $file->title ?? $file->name ?? $file->file_name ?? 'File' }}</div>
                                        <div class="timeline-meta">{{ optional($file->created_at)->format('Y-m-d H:i') ?: '-' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">No recent files.</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="card-title">Recent Expenses</div>
                </div>
                <div class="card-body">
                    @if($latestExpenses->count())
                        <div class="timeline">
                            @foreach($latestExpenses as $expense)
                                <div class="timeline-item">
                                    <div class="dot"></div>
                                    <div>
                                        <div class="timeline-title">{{ $expense->title ?? $expense->category ?? 'Expense' }}</div>
                                        <div class="timeline-meta">{{ number_format((float) ($expense->amount ?? 0), 2) }} {{ $expense->currency ?? '' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">No recent portal-visible expenses.</div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</body>
</html>
