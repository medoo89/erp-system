<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Profile</title>
    <style>
        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .page-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .hero {
            border-radius: 28px;
            padding: 28px;
            background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 42%, #ecfeff 100%);
            border: 1px solid #dbeafe;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.06);
        }

        .hero-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .hero-title {
            font-size: 42px;
            line-height: 1.02;
            font-weight: 900;
            color: #234b7b;
            letter-spacing: -0.03em;
            margin: 0;
        }

        .hero-subtitle {
            margin-top: 10px;
            color: #64748b;
            font-size: 16px;
            max-width: 860px;
        }

        .badge-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .badge {
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 999px;
            font-weight: 800;
            font-size: 13px;
        }

        .badge.blue { color: #1d4ed8; }
        .badge.green { color: #15803d; }
        .badge.gray { color: #64748b; }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
            border: 1px solid transparent;
            transition: .18s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #14b8a6;
            color: #ffffff;
        }

        .btn-secondary {
            background: #ffffff;
            color: #1d4ed8;
            border-color: #dbeafe;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .grid-5 {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #dbe4ee;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.04);
        }

        .card-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .info-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .stack {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .metric-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .metric-box {
            border: 1px solid #dbeafe;
            border-radius: 20px;
            padding: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            transition: .18s ease;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            min-height: 120px;
        }

        .metric-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
            border-color: #bfdbfe;
        }

        .metric-box.projects { border-left: 6px solid #2563eb; }
        .metric-box.jobs { border-left: 6px solid #14b8a6; }
        .metric-box.employees { border-left: 6px solid #7c3aed; }
        .metric-box.invoices { border-left: 6px solid #f59e0b; }
        .metric-box.expenses { border-left: 6px solid #ef4444; }

        .metric-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: .04em;
        }

        .metric-value {
            font-size: 30px;
            font-weight: 900;
            color: #234b7b;
        }

        .metric-hint {
            margin-top: 10px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .section-band {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 14px;
        }

        .project-stack {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .project-box {
            border: 1px solid #dbeafe;
            border-radius: 24px;
            padding: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
        }

        .project-box.color-1 { border-left: 8px solid #2563eb; }
        .project-box.color-2 { border-left: 8px solid #14b8a6; }
        .project-box.color-3 { border-left: 8px solid #7c3aed; }
        .project-box.color-4 { border-left: 8px solid #f59e0b; }
        .project-box.color-5 { border-left: 8px solid #ef4444; }

        .project-top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .project-name-wrap {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .project-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            width: fit-content;
        }

        .project-chip.color-1 { background: #dbeafe; color: #1d4ed8; }
        .project-chip.color-2 { background: #ccfbf1; color: #0f766e; }
        .project-chip.color-3 { background: #ede9fe; color: #6d28d9; }
        .project-chip.color-4 { background: #fef3c7; color: #b45309; }
        .project-chip.color-5 { background: #fee2e2; color: #b91c1c; }

        .project-name {
            font-size: 30px;
            font-weight: 900;
            color: #234b7b;
            margin: 0;
            line-height: 1.05;
        }

        .project-meta {
            margin-top: 2px;
            color: #64748b;
            font-size: 15px;
            font-weight: 700;
        }

        .project-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .mini-badge {
            padding: 8px 12px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            font-weight: 800;
            font-size: 12px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .summary-box {
            border: 1px solid #e5edf5;
            border-radius: 16px;
            padding: 14px;
            background: #ffffff;
        }

        .summary-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
        }

        .project-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .project-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 16px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 900;
            transition: .18s ease;
        }

        .project-btn:hover {
            transform: translateY(-1px);
        }

        .project-btn.primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(37, 99, 235, 0.18);
        }

        .project-btn.secondary {
            background: #ffffff;
            color: #64748b;
            border: 1px solid #e5edf5;
        }

        @media (max-width: 1300px) {
            .grid-5,
            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1100px) {
            .grid-2,
            .info-grid,
            .grid-5,
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <div class="hero">
        <div class="hero-top">
            <div>
                <h1 class="hero-title">{{ $client->name ?? 'Client' }}</h1>
                <div class="hero-subtitle">
                    Client master profile with direct access to linked projects, totals, and operational structure.
                </div>

                <div class="actions">
                    <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('edit', ['record' => $client]) }}" class="btn btn-primary">
                        Edit Client
                    </a>

                    <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('create', ['client_id' => $client->id]) }}" class="btn btn-secondary">
                        Add Project
                    </a>

                    <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('index') }}" class="btn btn-secondary">
                        Back to Clients
                    </a>
                </div>
            </div>

            <div class="badge-row">
                <div class="badge blue">Code: {{ $client->code ?: '-' }}</div>
                <div class="badge {{ $client->is_active ? 'green' : 'gray' }}">
                    {{ $client->is_active ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid-5">
        <a href="#projects-section" class="metric-link">
            <div class="metric-box projects">
                <div class="metric-label">Projects</div>
                <div class="metric-value">{{ $projectsTotal ?? 0 }}</div>
                <div class="metric-hint">Client delivery structure</div>
            </div>
        </a>

        <a href="#projects-section" class="metric-link">
            <div class="metric-box jobs">
                <div class="metric-label">Jobs</div>
                <div class="metric-value">{{ $jobsTotal ?? 0 }}</div>
                <div class="metric-hint">Hiring positions under all projects</div>
            </div>
        </a>

        <a href="#projects-section" class="metric-link">
            <div class="metric-box employees">
                <div class="metric-label">Employees</div>
                <div class="metric-value">{{ $employeesTotal ?? 0 }}</div>
                <div class="metric-hint">Active linked workforce</div>
            </div>
        </a>

        <a href="{{ route('admin.clients.invoices', ['client' => $client]) }}" class="metric-link">
            <div class="metric-box invoices">
                <div class="metric-label">Client Invoices</div>
                <div class="metric-value">{{ $invoicesTotal ?? 0 }}</div>
                <div class="metric-hint">Billing records across projects</div>
            </div>
        </a>

        <a href="{{ route('admin.clients.expenses', ['client' => $client]) }}" class="metric-link">
            <div class="metric-box expenses">
                <div class="metric-label">Expenses</div>
                <div class="metric-value">{{ $expensesTotal ?? 0 }}</div>
                <div class="metric-hint">Client-linked expenses</div>
            </div>
        </a>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-title">Client Information</div>

            <div class="info-grid">
                <div>
                    <div class="info-label">Client Name</div>
                    <div class="info-value">{{ $client->name ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Client Code</div>
                    <div class="info-value">{{ $client->code ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Contact Person</div>
                    <div class="info-value">{{ $client->contact_person ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $client->email ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Phone</div>
                    <div class="info-value">{{ $client->phone ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Status</div>
                    <div class="info-value" style="color:{{ $client->is_active ? '#15803d' : '#64748b' }};">
                        {{ $client->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Additional Information</div>

            <div class="stack">
                <div>
                    <div class="info-label">Address</div>
                    <div class="info-value">{{ $client->address ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Notes</div>
                    <div class="info-value">{{ $client->notes ?: '-' }}</div>
                </div>

                <div class="info-grid">
                    <div>
                        <div class="info-label">Created At</div>
                        <div class="info-value">{{ optional($client->created_at)->format('M j, Y H:i') ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ optional($client->updated_at)->format('M j, Y H:i') ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="projects-section">
        <div class="section-band">Projects</div>
        <div class="card-title" style="margin-top:0;">Client Projects Hierarchy</div>

        <div class="project-stack">
            @forelse($projectSummaries as $entry)
                @php
                    $project = $entry['project'];
                    $colorClass = 'color-' . (($loop->index % 5) + 1);
                @endphp

                <div class="project-box {{ $colorClass }}">
                    <div class="project-top">
                        <div class="project-name-wrap">
                            <div class="project-chip {{ $colorClass }}">Project</div>
                            <h3 class="project-name">{{ $project->name ?: '-' }}</h3>
                            <div class="project-meta">
                                {{ $project->location ?: '-' }} • Code: {{ $project->project_code ?: '-' }}
                            </div>
                        </div>

                        <div class="project-badges">
                            <div class="mini-badge" style="color:#1d4ed8;">Jobs: {{ $entry['jobs_count'] ?? 0 }}</div>
                            <div class="mini-badge" style="color:#7c3aed;">Employees: {{ $entry['employees_count'] ?? 0 }}</div>
                            <div class="mini-badge" style="color:#f59e0b;">Invoices: {{ $entry['invoices_count'] ?? 0 }}</div>
                            <div class="mini-badge" style="color:#ef4444;">Expenses: {{ $entry['expenses_count'] ?? 0 }}</div>
                            <div class="mini-badge" style="color:{{ $project->is_active ? '#15803d' : '#64748b' }};">
                                {{ $project->is_active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                    </div>

                    <div class="summary-grid">
                        <div class="summary-box">
                            <div class="summary-label">Jobs</div>
                            <div class="summary-value">{{ $entry['jobs_count'] ?? 0 }}</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Employees</div>
                            <div class="summary-value">{{ $entry['employees_count'] ?? 0 }}</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Invoices</div>
                            <div class="summary-value">{{ $entry['invoices_count'] ?? 0 }}</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Salary Slips</div>
                            <div class="summary-value">{{ $entry['salary_slips_count'] ?? 0 }}</div>
                        </div>

                        <div class="summary-box">
                            <div class="summary-label">Expenses</div>
                            <div class="summary-value">{{ $entry['expenses_count'] ?? 0 }}</div>
                        </div>
                    </div>

                    <div class="project-actions">
                        <a href="{{ route('admin.projects.profile', ['project' => $project]) }}" class="project-btn primary">
                            Open Project
                        </a>

                        <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $project]) }}" class="project-btn secondary">
                            Edit Project
                        </a>
                    </div>
                </div>
            @empty
                <div style="color:#94a3b8;">No projects linked to this client yet.</div>
            @endforelse
        </div>
    </div>
</div>
</body>
</html>
