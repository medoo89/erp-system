<x-filament-panels::page>
    @php
        $project = $this->project;
        $client = $this->client;
        $employees = collect($this->employees ?? []);
        $jobs = collect($this->jobs ?? []);
    @endphp

    <style>
        .sf-shell{display:flex;flex-direction:column;gap:32px}
        .sf-grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:28px}
        .sf-grid-5{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:20px}
        .sf-hero{border-radius:30px;padding:34px;background:linear-gradient(135deg,#eff6ff 0%,#f8fbff 42%,#ecfeff 100%);border:1px solid #dbeafe;box-shadow:0 24px 70px rgba(15,23,42,.06)}
        .sf-hero-top{display:flex;justify-content:space-between;gap:24px;align-items:flex-start;flex-wrap:wrap}
        .sf-title{font-size:48px;line-height:1.01;font-weight:900;color:#234b7b;letter-spacing:-.04em;margin:0}
        .sf-sub{margin-top:12px;color:#64748b;font-size:18px;max-width:920px;line-height:1.6}
        .sf-badges,.sf-actions{display:flex;gap:12px;flex-wrap:wrap}
        .sf-actions{margin-top:20px}
        .sf-badge{padding:12px 16px;background:#fff;border:1px solid #dbeafe;border-radius:999px;font-weight:800;font-size:14px}
        .sf-card{background:#fff;border:1px solid #dbe4ee;border-radius:28px;padding:30px;box-shadow:0 16px 40px rgba(15,23,42,.045)}
        .sf-card-title{font-size:24px;font-weight:900;color:#0f172a;margin-bottom:22px}
        .sf-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
        .sf-label{font-size:12px;color:#64748b;font-weight:800;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px}
        .sf-value{font-size:18px;font-weight:800;color:#0f172a;line-height:1.45}
        .sf-stack{display:flex;flex-direction:column;gap:22px}
        .sf-metric-link{text-decoration:none;color:inherit;display:block}
        .sf-metric-box{border:1px solid #dbeafe;border-radius:24px;padding:22px;background:linear-gradient(180deg,#ffffff 0%,#f8fbff 100%);transition:.18s ease;box-shadow:0 12px 28px rgba(15,23,42,.04);min-height:152px}
        .sf-metric-box:hover{transform:translateY(-3px);box-shadow:0 18px 34px rgba(15,23,42,.08);border-color:#bfdbfe}
        .sf-metric-label{font-size:12px;color:#64748b;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px}
        .sf-metric-value{font-size:34px;font-weight:900;color:#234b7b;line-height:1}
        .sf-metric-hint{margin-top:14px;color:#64748b;font-size:14px;font-weight:700;line-height:1.45}
        .sf-band{display:inline-flex;align-items:center;padding:9px 16px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-weight:900;font-size:12px;text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px}
        .sf-btn{display:inline-flex;align-items:center;justify-content:center;padding:12px 18px;border-radius:999px;text-decoration:none;font-weight:900;font-size:14px;border:1px solid transparent;transition:.18s ease}
        .sf-btn:hover{transform:translateY(-1px)}
        .sf-btn-primary{background:#14b8a6;color:#fff}
        .sf-btn-secondary{background:#fff;color:#1d4ed8;border-color:#dbeafe}
        .sf-summary-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px}
        .sf-summary-box{border:1px solid #e5edf5;border-radius:20px;padding:18px;background:#fff}
        .sf-summary-label{font-size:11px;color:#64748b;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px}
        .sf-summary-value{font-size:28px;font-weight:900;color:#0f172a;line-height:1}
        .sf-note{margin-top:18px;color:#64748b;font-size:14px;line-height:1.65}
        table{width:100%;border-collapse:collapse}
        th{text-align:left;padding:14px;background:#f8fafc;color:#7c8aa0;text-transform:uppercase;font-size:13px}
        td{padding:16px 14px;border-top:1px solid #e5edf5;color:#0f172a;vertical-align:top}
        .sf-status{
            display:inline-flex;
            align-items:center;
            padding:8px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:800;
            border:1px solid #e5edf5;
            background:#fff;
            white-space:nowrap;
        }
        .sf-status.green{color:#15803d;background:#ecfdf3;border-color:#bbf7d0}
        .sf-status.blue{color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe}
        .sf-status.orange{color:#c2410c;background:#fff7ed;border-color:#fdba74}
        .sf-status.red{color:#b91c1c;background:#fef2f2;border-color:#fecaca}
        .sf-status.gray{color:#64748b;background:#f8fafc;border-color:#e2e8f0}
        .sf-alerts{display:flex;flex-direction:column;gap:8px;min-width:220px}
        .sf-alert{
            display:inline-flex;
            align-items:center;
            padding:8px 12px;
            border-radius:12px;
            font-size:12px;
            font-weight:800;
            line-height:1.35;
            width:fit-content;
            max-width:100%;
        }
        .sf-alert.red{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
        .sf-alert.orange{background:#fff7ed;color:#c2410c;border:1px solid #fdba74}
        .sf-alert.blue{background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe}
        .sf-alert.gray{background:#f8fafc;color:#64748b;border:1px solid #e2e8f0}
        .sf-open-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:10px 14px;
            border-radius:999px;
            text-decoration:none;
            font-size:13px;
            font-weight:900;
            color:#fff;
            background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
            box-shadow:0 10px 22px rgba(37,99,235,.18);
            white-space:nowrap;
        }
        .sf-muted{color:#94a3b8;padding:16px 0}
        @media (max-width:1400px){
            .sf-grid-5,.sf-summary-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
        }
        @media (max-width:1200px){
            .sf-grid-2,.sf-info-grid,.sf-grid-5,.sf-summary-grid{grid-template-columns:1fr}
        }
    </style>

    @php
        $formatValue = function ($value) {
            if (blank($value)) return '-';
            return str_replace('_', ' ', (string) $value);
        };

        $statusClass = function ($value) {
            $value = strtolower((string) $value);

            return match (true) {
                str_contains($value, 'active'),
                str_contains($value, 'approved'),
                str_contains($value, 'valid') => 'green',

                str_contains($value, 'pending'),
                str_contains($value, 'travel'),
                str_contains($value, 'scheduled') => 'orange',

                str_contains($value, 'expired'),
                str_contains($value, 'rejected'),
                str_contains($value, 'cancelled'),
                str_contains($value, 'inactive') => 'red',

                str_contains($value, 'leave'),
                str_contains($value, 'vacation') => 'blue',

                default => 'gray',
            };
        };

        $dateText = function ($value) {
            if (blank($value)) return null;

            try {
                return \Illuminate\Support\Carbon::parse($value)->format('M j, Y');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        };

        $daysLeft = function ($value) {
            if (blank($value)) return null;

            try {
                return now()->startOfDay()->diffInDays(\Illuminate\Support\Carbon::parse($value)->startOfDay(), false);
            } catch (\Throwable $e) {
                return null;
            }
        };

        $buildAlerts = function ($employee) use ($daysLeft, $dateText) {
            $alerts = [];

            $visaDate = $employee->visa_expiry_date ?? $employee->visa_expiry ?? null;
            $desertPassDate = $employee->desert_pass_expiry_date ?? $employee->desert_pass_expiry ?? null;
            $passportDate = $employee->passport_expiry_date ?? $employee->passport_expiry ?? null;
            $medicalDate = $employee->medical_expiry_date ?? $employee->medical_expiry ?? null;

            foreach ([
                ['label' => 'Visa', 'date' => $visaDate],
                ['label' => 'Desert Pass', 'date' => $desertPassDate],
                ['label' => 'Passport', 'date' => $passportDate],
                ['label' => 'Medical', 'date' => $medicalDate],
            ] as $item) {
                $days = $daysLeft($item['date']);

                if ($days === null) continue;

                $textDate = $dateText($item['date']);

                if ($days < 0) {
                } elseif ($days <= 30) {
                }
            }

            if (blank($employee->mobilization_date ?? null) && str_contains(strtolower((string) ($employee->current_work_status ?? '')), 'pending')) {
            }

            return $alerts;
        };
    @endphp

    <div class="sf-shell">
        <div class="sf-hero">
            <div class="sf-hero-top">
                <div>
                    <h1 class="sf-title">{{ $project->name ?? 'Project' }}</h1>
                    <div class="sf-sub">
                    </div>

                    <div class="sf-actions">
                        <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $project]) }}" class="sf-btn sf-btn-primary">Edit Project</a>
                        @if($client)
                            <a href="{{ \App\Filament\Pages\ClientProfilePage::getUrl(['client' => $client->id]) }}" class="sf-btn sf-btn-secondary">Back to Client</a>
                        @endif
                        <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('index') }}" class="sf-btn sf-btn-secondary">Back to Clients</a>
                    </div>
                </div>

                <div class="sf-badges">
                    <div class="sf-badge" style="color:#1d4ed8;">Code: {{ $project->project_code ?: '-' }}</div>
                    <div class="sf-badge" style="color:#7c3aed;">Client: {{ $client?->name ?: '-' }}</div>
                    <div class="sf-badge" style="color:{{ $project->is_active ? '#15803d' : '#64748b' }};">
                        {{ $project->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="sf-grid-5">
            <a href="#jobs-summary" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Jobs</div>
                    <div class="sf-metric-value">{{ $this->jobsCount }}</div>
                    <div class="sf-metric-hint">Open jobs and hiring context</div>
                </div>
            </a>

            <a href="#employees-section" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Employees</div>
                    <div class="sf-metric-value">{{ $this->employeesCount }}</div>
                    <div class="sf-metric-hint">Current linked workforce</div>
                </div>
            </a>

            <a href="{{ \App\Filament\Pages\ProjectInvoicesPage::getUrl(['project' => $project->id]) }}" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Client Invoices</div>
                    <div class="sf-metric-value">{{ $this->clientInvoicesCount }}</div>
                    <div class="sf-metric-hint">Open filtered invoice page</div>
                </div>
            </a>

            <a href="{{ \App\Filament\Pages\ProjectSalarySlipsPage::getUrl(['project' => $project->id]) }}" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Salary Slips</div>
                    <div class="sf-metric-value">{{ $this->salarySlipsCount }}</div>
                    <div class="sf-metric-hint">Open filtered payroll page</div>
                </div>
            </a>

            <a href="{{ \App\Filament\Pages\ProjectExpensesPage::getUrl(['project' => $project->id]) }}" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Expenses</div>
                    <div class="sf-metric-value">{{ $this->expensesCount }}</div>
                    <div class="sf-metric-hint">Open filtered expense page</div>
                </div>
            </a>
        </div>

        <div class="sf-grid-2">
            <div class="sf-card">
                <div class="sf-card-title">Project Information</div>
                <div class="sf-info-grid">
                    <div><div class="sf-label">Project Name</div><div class="sf-value">{{ $project->name ?: '-' }}</div></div>
                    <div><div class="sf-label">Project Code</div><div class="sf-value">{{ $project->project_code ?: '-' }}</div></div>
                    <div><div class="sf-label">Client</div><div class="sf-value">{{ $client?->name ?: '-' }}</div></div>
                    <div><div class="sf-label">Location</div><div class="sf-value">{{ $project->location ?: '-' }}</div></div>
                    <div><div class="sf-label">Status</div><div class="sf-value" style="color:{{ $project->is_active ? '#15803d' : '#64748b' }};">{{ $project->is_active ? 'Active' : 'Inactive' }}</div></div>
                </div>
            </div>

            <div class="sf-card">
                <div class="sf-card-title">Additional Information</div>
                <div class="sf-stack">
                    <div><div class="sf-label">Description</div><div class="sf-value">{{ $project->description ?: '-' }}</div></div>
                    <div><div class="sf-label">Notes</div><div class="sf-value">{{ $project->notes ?: '-' }}</div></div>
                    <div class="sf-info-grid">
                        <div><div class="sf-label">Created At</div><div class="sf-value">{{ optional($project->created_at)->format('M j, Y H:i') ?: '-' }}</div></div>
                        <div><div class="sf-label">Last Updated</div><div class="sf-value">{{ optional($project->updated_at)->format('M j, Y H:i') ?: '-' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sf-card" id="jobs-summary">
            <div class="sf-band">Jobs Summary</div>
            <div class="sf-card-title" style="margin-top:0;">Hiring Snapshot</div>

            <div class="sf-summary-grid">
                <div class="sf-summary-box">
                    <div class="sf-summary-label">Total Jobs</div>
                    <div class="sf-summary-value">{{ $this->jobsCount }}</div>
                </div>

                <div class="sf-summary-box">
                    <div class="sf-summary-label">Total Applicants</div>
                    <div class="sf-summary-value">{{ $jobs->sum(fn($job) => (int) ($job->applications_count ?? 0)) }}</div>
                </div>

                <div class="sf-summary-box">
                    <div class="sf-summary-label">Active Jobs</div>
                    <div class="sf-summary-value">{{ $jobs->filter(fn($job) => (bool) ($job->is_active ?? false))->count() }}</div>
                </div>

                <div class="sf-summary-box">
                    <div class="sf-summary-label">Latest Job</div>
                    <div class="sf-summary-value" style="font-size:18px;">{{ optional($jobs->first())->title ?: '-' }}</div>
                </div>

                <div class="sf-summary-box">
                    <div class="sf-summary-label">Latest Created</div>
                    <div class="sf-summary-value" style="font-size:18px;">{{ optional(optional($jobs->first())->created_at)->format('M j, Y') ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="sf-card" id="employees-section">
            <div class="sf-band">Project Workforce</div>
            <div class="sf-card-title" style="margin-top:0;">Project Employments</div>

            <div style="overflow:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Employment</th>
                            <th>Current Status</th>
                            <th>Alerts</th>
                            <th>Open Profile</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            @php
                                $employmentStatus = $formatValue($employee->status ?? '-');
                                $currentStatus = $formatValue($employee->current_work_status ?? '-');
                                $alerts = $buildAlerts($employee);
                            @endphp
                            <tr>
                                <td style="font-weight:900;">{{ $employee->employee_name ?: '-' }}</td>
                                <td>{{ $employee->position_title ?: '-' }}</td>
                                <td>
                                    <span class="sf-status {{ $statusClass($employee->status ?? '') }}">
                                        {{ $employmentStatus }}
                                    </span>
                                </td>
                                <td>
                                    <span class="sf-status {{ $statusClass($employee->current_work_status ?? '') }}">
                                        {{ $currentStatus }}
                                    </span>
                                </td>
                                <td>
                                    <div class="sf-alerts">
                                        @forelse($alerts as $alert)
                                            <span class="sf-alert {{ $alert['class'] }}">{{ $alert['text'] }}</span>
                                        @empty
                                            <span class="sf-alert gray">No current alerts</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ \App\Filament\Resources\Employments\EmploymentResource::getUrl('view', ['record' => $employee]) }}" class="sf-open-link">
                                        Open Employment
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="sf-muted">No employees linked to this project yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
