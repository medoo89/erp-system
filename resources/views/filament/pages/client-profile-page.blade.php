<x-filament-panels::page>
    @php
        $client = $this->client;
        $projectSummaries = $this->projectSummaries;
    @endphp

    <style>
        .sf-shell{display:flex;flex-direction:column;gap:32px}
        .sf-grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:28px}
        .sf-grid-5{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:20px}
        .sf-hero{border-radius:30px;padding:34px;background:linear-gradient(135deg,#eff6ff 0%,#f8fbff 42%,#ecfeff 100%);border:1px solid #dbeafe;box-shadow:0 24px 70px rgba(15,23,42,.06)}
        .sf-hero-top{display:flex;justify-content:space-between;gap:24px;align-items:flex-start;flex-wrap:wrap}
        .sf-title{font-size:48px;line-height:1.01;font-weight:900;color:#234b7b;letter-spacing:-.04em;margin:0}
        .sf-sub{margin-top:12px;color:#64748b;font-size:18px;max-width:920px;line-height:1.6}
        .sf-badges,.sf-actions,.sf-project-badges,.sf-project-actions{display:flex;gap:12px;flex-wrap:wrap}
        .sf-actions{margin-top:20px}
        .sf-badge,.sf-mini-badge{padding:12px 16px;background:#fff;border:1px solid #dbeafe;border-radius:999px;font-weight:800;font-size:14px}
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
        .sf-project-stack{display:flex;flex-direction:column;gap:24px}
        .sf-project-box{border:1px solid #dbeafe;border-radius:28px;padding:28px;background:linear-gradient(180deg,#fff 0%,#fbfdff 100%);box-shadow:0 14px 32px rgba(15,23,42,.04)}
        .sf-project-top{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;flex-wrap:wrap;margin-bottom:22px}
        .sf-project-name{font-size:34px;font-weight:900;color:#234b7b;margin:0;line-height:1.04}
        .sf-project-meta{margin-top:4px;color:#64748b;font-size:16px;font-weight:700}
        .sf-summary-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:20px}
        .sf-summary-box{border:1px solid #e5edf5;border-radius:20px;padding:18px;background:#fff}
        .sf-summary-label{font-size:11px;color:#64748b;font-weight:900;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px}
        .sf-summary-value{font-size:28px;font-weight:900;color:#0f172a;line-height:1}
        .sf-btn{display:inline-flex;align-items:center;justify-content:center;padding:12px 18px;border-radius:999px;text-decoration:none;font-weight:900;font-size:14px;border:1px solid transparent;transition:.18s ease}
        .sf-btn:hover{transform:translateY(-1px)}
        .sf-btn-primary{background:#14b8a6;color:#fff}
        .sf-btn-secondary{background:#fff;color:#1d4ed8;border-color:#dbeafe}
        .sf-project-btn{display:inline-flex;align-items:center;justify-content:center;padding:12px 18px;border-radius:999px;text-decoration:none;font-size:14px;font-weight:900;transition:.18s ease}
        .sf-project-btn:hover{transform:translateY(-1px)}
        .sf-project-btn-primary{background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);color:#fff;box-shadow:0 10px 22px rgba(37,99,235,.18)}
        .sf-project-btn-secondary{background:#fff;color:#64748b;border:1px solid #e5edf5}
        .color-1{border-left:8px solid #2563eb}.color-2{border-left:8px solid #14b8a6}.color-3{border-left:8px solid #7c3aed}.color-4{border-left:8px solid #f59e0b}.color-5{border-left:8px solid #ef4444}
        .chip.color-1{background:#dbeafe;color:#1d4ed8}.chip.color-2{background:#ccfbf1;color:#0f766e}.chip.color-3{background:#ede9fe;color:#6d28d9}.chip.color-4{background:#fef3c7;color:#b45309}.chip.color-5{background:#fee2e2;color:#b91c1c}
        .chip{display:inline-flex;align-items:center;padding:9px 15px;border-radius:999px;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;width:fit-content}
        @media (max-width:1400px){.sf-grid-5,.sf-summary-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
        @media (max-width:1150px){.sf-grid-2,.sf-info-grid,.sf-grid-5,.sf-summary-grid{grid-template-columns:1fr}}
    </style>

    <div class="sf-shell">
        <div class="sf-hero">
            <div class="sf-hero-top">
                <div>
                    <h1 class="sf-title">{{ $client->name ?? 'Client' }}</h1>
                    <div class="sf-sub">Client executive profile with direct access to linked projects, totals, and filtered finance pages.</div>

                    <div class="sf-actions">
                        <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('edit', ['record' => $client]) }}" class="sf-btn sf-btn-primary">Edit Client</a>
                        <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('create', ['client_id' => $client->id]) }}" class="sf-btn sf-btn-secondary">Add Project</a>
                        <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('index') }}" class="sf-btn sf-btn-secondary">Back to Clients</a>
                    </div>
                </div>

                <div class="sf-badges">
                    <div class="sf-badge" style="color:#1d4ed8;">Code: {{ $client->code ?: '-' }}</div>
                    <div class="sf-badge" style="color:{{ $client->is_active ? '#15803d' : '#64748b' }};">{{ $client->is_active ? 'Active' : 'Inactive' }}</div>
                </div>
            </div>
        </div>

        <div class="sf-grid-5">
            <a href="#projects-section" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Projects</div>
                    <div class="sf-metric-value">{{ $this->projectsTotal }}</div>
                    <div class="sf-metric-hint">Client delivery structure</div>
                </div>
            </a>

            <a href="#projects-section" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Jobs</div>
                    <div class="sf-metric-value">{{ $this->jobsTotal }}</div>
                    <div class="sf-metric-hint">Hiring positions under all projects</div>
                </div>
            </a>

            <a href="#projects-section" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Employees</div>
                    <div class="sf-metric-value">{{ $this->employeesTotal }}</div>
                    <div class="sf-metric-hint">Active linked workforce</div>
                </div>
            </a>

            <a href="{{ \App\Filament\Pages\ClientInvoicesPage::getUrl(['client' => $client->id]) }}" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Client Invoices</div>
                    <div class="sf-metric-value">{{ $this->invoicesTotal }}</div>
                    <div class="sf-metric-hint">Open filtered invoice page</div>
                </div>
            </a>

            <a href="{{ \App\Filament\Pages\ClientExpensesPage::getUrl(['client' => $client->id]) }}" class="sf-metric-link">
                <div class="sf-metric-box">
                    <div class="sf-metric-label">Expenses</div>
                    <div class="sf-metric-value">{{ $this->expensesTotal }}</div>
                    <div class="sf-metric-hint">Open grouped expense page</div>
                </div>
            </a>
        </div>

        <div class="sf-grid-2">
            <div class="sf-card">
                <div class="sf-card-title">Client Information</div>
                <div class="sf-info-grid">
                    <div><div class="sf-label">Client Name</div><div class="sf-value">{{ $client->name ?: '-' }}</div></div>
                    <div><div class="sf-label">Client Code</div><div class="sf-value">{{ $client->code ?: '-' }}</div></div>
                    <div><div class="sf-label">Contact Person</div><div class="sf-value">{{ $client->contact_person ?: '-' }}</div></div>
                    <div><div class="sf-label">Email</div><div class="sf-value">{{ $client->email ?: '-' }}</div></div>
                    <div><div class="sf-label">Phone</div><div class="sf-value">{{ $client->phone ?: '-' }}</div></div>
                    <div><div class="sf-label">Status</div><div class="sf-value" style="color:{{ $client->is_active ? '#15803d' : '#64748b' }};">{{ $client->is_active ? 'Active' : 'Inactive' }}</div></div>
                </div>
            </div>

            <div class="sf-card">
                <div class="sf-card-title">Additional Information</div>
                <div class="sf-stack">
                    <div><div class="sf-label">Address</div><div class="sf-value">{{ $client->address ?: '-' }}</div></div>
                    <div><div class="sf-label">Notes</div><div class="sf-value">{{ $client->notes ?: '-' }}</div></div>
                    <div class="sf-info-grid">
                        <div><div class="sf-label">Created At</div><div class="sf-value">{{ optional($client->created_at)->format('M j, Y H:i') ?: '-' }}</div></div>
                        <div><div class="sf-label">Last Updated</div><div class="sf-value">{{ optional($client->updated_at)->format('M j, Y H:i') ?: '-' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sf-card" id="projects-section">
            <div class="sf-band">Projects</div>
            <div class="sf-card-title" style="margin-top:0;">Client Projects Hierarchy</div>

            <div class="sf-project-stack">
                @forelse($projectSummaries as $entry)
                    @php
                        $project = $entry['project'];
                        $colorClass = 'color-' . (($loop->index % 5) + 1);
                    @endphp

                    <div class="sf-project-box {{ $colorClass }}">
                        <div class="sf-project-top">
                            <div class="flex flex-col gap-3">
                                <div class="chip {{ $colorClass }}">Project</div>
                                <h3 class="sf-project-name">{{ $project->name ?: '-' }}</h3>
                                <div class="sf-project-meta">{{ $project->location ?: '-' }} • Code: {{ $project->project_code ?: '-' }}</div>
                            </div>

                            <div class="sf-project-badges">
                                <div class="sf-mini-badge" style="color:#1d4ed8;">Jobs: {{ $entry['jobs_count'] ?? 0 }}</div>
                                <div class="sf-mini-badge" style="color:#7c3aed;">Employees: {{ $entry['employees_count'] ?? 0 }}</div>
                                <div class="sf-mini-badge" style="color:#f59e0b;">Invoices: {{ $entry['invoices_count'] ?? 0 }}</div>
                                <div class="sf-mini-badge" style="color:#ef4444;">Expenses: {{ $entry['expenses_count'] ?? 0 }}</div>
                                <div class="sf-mini-badge" style="color:{{ $project->is_active ? '#15803d' : '#64748b' }};">{{ $project->is_active ? 'Active' : 'Inactive' }}</div>
                            </div>
                        </div>

                        <div class="sf-summary-grid">
                            <div class="sf-summary-box"><div class="sf-summary-label">Jobs</div><div class="sf-summary-value">{{ $entry['jobs_count'] ?? 0 }}</div></div>
                            <div class="sf-summary-box"><div class="sf-summary-label">Employees</div><div class="sf-summary-value">{{ $entry['employees_count'] ?? 0 }}</div></div>
                            <div class="sf-summary-box"><div class="sf-summary-label">Invoices</div><div class="sf-summary-value">{{ $entry['invoices_count'] ?? 0 }}</div></div>
                            <div class="sf-summary-box"><div class="sf-summary-label">Salary Slips</div><div class="sf-summary-value">{{ $entry['salary_slips_count'] ?? 0 }}</div></div>
                            <div class="sf-summary-box"><div class="sf-summary-label">Expenses</div><div class="sf-summary-value">{{ $entry['expenses_count'] ?? 0 }}</div></div>
                        </div>

                        <div class="sf-project-actions">
                            <a href="{{ \App\Filament\Pages\ProjectProfilePage::getUrl(['project' => $project->id]) }}" class="sf-project-btn sf-project-btn-primary">Open Project</a>
                            <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $project]) }}" class="sf-project-btn sf-project-btn-secondary">Edit Project</a>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-400">No projects linked to this client yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
