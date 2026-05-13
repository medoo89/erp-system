<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Profile</title>
    <style>
        * { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
        }

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
        .badge.purple { color: #7c3aed; }

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

        .metric-box.jobs { border-left: 6px solid #2563eb; }
        .metric-box.employees { border-left: 6px solid #14b8a6; }
        .metric-box.invoices { border-left: 6px solid #7c3aed; }
        .metric-box.slips { border-left: 6px solid #f59e0b; }
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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 14px;
            background: #f8fafc;
            color: #7c8aa0;
            text-transform: uppercase;
            font-size: 13px;
        }

        td {
            padding: 16px 14px;
            border-top: 1px solid #e5edf5;
            color: #0f172a;
            vertical-align: top;
        }

        .table-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 800;
            font-size: 12px;
            text-decoration: none;
        }

        .status-green { color: #15803d; font-weight: 700; }
        .status-gray { color: #64748b; font-weight: 700; }
        .status-blue { color: #1d4ed8; font-weight: 700; }
        .status-orange { color: #c2410c; font-weight: 700; }

        .empty {
            color: #94a3b8;
            padding: 18px 14px;
        }

        @media (max-width: 1300px) {
            .grid-5 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1100px) {
            .grid-2,
            .info-grid,
            .grid-5 {
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
                <h1 class="hero-title">{{ $project->name ?? 'Project' }}</h1>
                <div class="hero-subtitle">
                </div>

                <div class="actions">
                    <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $project]) }}" class="btn btn-primary">
                        Edit Project
                    </a>

                    @if($client)
                        <a href="{{ route('admin.clients.profile', ['client' => $client]) }}" class="btn btn-secondary">
                            Back to Client
                        </a>
                    @endif

                    <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('index') }}" class="btn btn-secondary">
                        Back to Clients
                    </a>
                </div>
            </div>

            <div class="badge-row">
                <div class="badge blue">Code: {{ $project->project_code ?: '-' }}</div>
                <div class="badge purple">Client: {{ $client?->name ?: '-' }}</div>
                <div class="badge {{ $project->is_active ? 'green' : 'gray' }}">
                    {{ $project->is_active ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>
    </div>

    <div class="grid-5">
        <a href="#jobs-section" class="metric-link">
            <div class="metric-box jobs">
                <div class="metric-label">Jobs</div>
                <div class="metric-value">{{ $jobsCount ?? 0 }}</div>
                <div class="metric-hint">Open jobs and hiring context</div>
            </div>
        </a>

        <a href="#employees-section" class="metric-link">
            <div class="metric-box employees">
                <div class="metric-label">Employees</div>
                <div class="metric-value">{{ $employeesCount ?? 0 }}</div>
                <div class="metric-hint">Current linked workforce</div>
            </div>
        </a>

        <a href="{{ route('admin.projects.invoices', ['project' => $project]) }}" class="metric-link">
            <div class="metric-box invoices">
                <div class="metric-label">Client Invoices</div>
                <div class="metric-value">{{ $clientInvoicesCount ?? 0 }}</div>
                <div class="metric-hint">Financial billing records</div>
            </div>
        </a>

        <a href="{{ route('admin.projects.salary-slips', ['project' => $project]) }}" class="metric-link">
            <div class="metric-box slips">
                <div class="metric-label">Salary Slips</div>
                <div class="metric-value">{{ $salarySlipsCount ?? 0 }}</div>
                <div class="metric-hint">Payroll records</div>
            </div>
        </a>

        <a href="{{ route('admin.projects.expenses', ['project' => $project]) }}" class="metric-link">
            <div class="metric-box expenses">
                <div class="metric-label">Expenses</div>
                <div class="metric-value">{{ $expensesCount ?? 0 }}</div>
                <div class="metric-hint">Project-linked spending</div>
            </div>
        </a>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-title">Project Information</div>

            <div class="info-grid">
                <div>
                    <div class="info-label">Project Name</div>
                    <div class="info-value">{{ $project->name ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Project Code</div>
                    <div class="info-value">{{ $project->project_code ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Client</div>
                    <div class="info-value">{{ $client?->name ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ $project->location ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Status</div>
                    <div class="info-value" style="color:{{ $project->is_active ? '#15803d' : '#64748b' }};">
                        {{ $project->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Additional Information</div>

            <div class="stack">
                <div>
                    <div class="info-label">Description</div>
                    <div class="info-value">{{ $project->description ?: '-' }}</div>
                </div>

                <div>
                    <div class="info-label">Notes</div>
                    <div class="info-value">{{ $project->notes ?: '-' }}</div>
                </div>

                <div class="info-grid">
                    <div>
                        <div class="info-label">Created At</div>
                        <div class="info-value">{{ optional($project->created_at)->format('M j, Y H:i') ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">{{ optional($project->updated_at)->format('M j, Y H:i') ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" id="jobs-section">
        <div class="section-band">Jobs</div>
        <div class="card-title" style="margin-top:0;">Project Jobs</div>

        <div style="overflow:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Status</th>
                        <th>Applicants</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            <td style="font-weight:800;">{{ $job->title ?: '-' }}</td>
                            <td class="{{ $job->is_active ? 'status-green' : 'status-gray' }}">
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </td>
                            <td>{{ $job->applications_count ?? 0 }}</td>
                            <td>{{ optional($job->created_at)->format('M j, Y') ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">No jobs linked to this project yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" id="employees-section">
        <div class="section-band">Employees</div>
        <div class="card-title" style="margin-top:0;">Project Employees</div>

        <div style="overflow:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Position</th>
                        <th>Employment Status</th>
                        <th>Current Work Status</th>
                        <th>Rotation Status</th>
                        <th>Rotation Pattern</th>
                        <th>Mobilization</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td style="font-weight:800;">{{ $employee->employee_name ?: '-' }}</td>
                            <td>{{ $employee->position_title ?: '-' }}</td>
                            <td class="status-blue">{{ $employee->status ?: '-' }}</td>
                            <td>{{ $employee->current_work_status ?: '-' }}</td>
                            <td>{{ $employee->rotation_status ?: '-' }}</td>
                            <td>{{ $employee->rotation_pattern ?: '-' }}</td>
                            <td>
                                @if($employee->mobilization_date)
                                    {{ optional($employee->mobilization_date)->format('M j, Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ \App\Filament\Resources\Employments\EmploymentResource::getUrl('view', ['record' => $employee]) }}" class="table-btn">
                                    Open Employment
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty">No employees linked to this project yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" id="invoices-section">
        <div class="section-band">Client Invoices</div>
        <div class="card-title" style="margin-top:0;">Invoice Summary</div>
        <div class="empty">This project currently has {{ $clientInvoicesCount ?? 0 }} invoice record(s). We can make this section open the filtered invoice list in the next step.</div>
    </div>

    <div class="card" id="slips-section">
        <div class="section-band">Salary Slips</div>
        <div class="card-title" style="margin-top:0;">Payroll Summary</div>
        <div class="empty">This project currently has {{ $salarySlipsCount ?? 0 }} salary slip record(s). We can make this section open the filtered salary list in the next step.</div>
    </div>

    <div class="card" id="expenses-section">
        <div class="section-band">Expenses</div>
        <div class="card-title" style="margin-top:0;">Expense Summary</div>
        <div class="empty">This project currently has {{ $expensesCount ?? 0 }} finance expense record(s). We can make this section open the filtered expense list in the next step.</div>
    </div>
</div>
</body>
</html>
