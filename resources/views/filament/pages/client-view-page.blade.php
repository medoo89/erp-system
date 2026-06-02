<x-filament-panels::page>
    @php
        $client = $this->client;

        $clientName = $client->name ?? 'Unnamed Client';
        $clientCode = $client->client_code ?? $client->code ?? ('CL-' . str_pad((string) $client->id, 4, '0', STR_PAD_LEFT));

        $contactPerson = $client->contact_person ?? $client->contact_name ?? '-';
        $phone = $client->phone ?? $client->mobile ?? '-';
        $email = $client->email ?? '-';
        $address = $client->address ?? '-';

        $isActive = true;

        if (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'is_active')) {
            $isActive = (bool) $client->is_active;
        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'active')) {
            $isActive = (bool) $client->active;
        }

        $projects = method_exists($client, 'projects')
            ? $client->projects()->latest('id')->get()
            : collect();

        $linkedEmployees = 0;

        try {
            if (method_exists($client, 'employments')) {
                $linkedEmployees = $client->employments()->count();
            }
        } catch (\Throwable $e) {
            $linkedEmployees = 0;
        }

        $editUrl = url('/admin/clients/' . $client->id . '/edit');
        $clientsUrl = url('/admin/clients');
    @endphp

    <style>
        .sf-client-view {
            max-width: 1240px;
            margin: 0 auto;
            padding: 10px 0 44px;
        }

        .sf-client-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .20), transparent 35%),
                linear-gradient(135deg, #0f2740 0%, #173a57 48%, #276b6f 100%);
            box-shadow: 0 24px 60px rgba(15, 39, 64, .18);
            color: white;
        }

        .sf-client-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #22d3ee, #2563eb, #facc15);
        }

        .sf-client-hero-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
        }

        .sf-kicker {
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            opacity: .75;
            margin-bottom: 10px;
        }

        .sf-client-title {
            font-size: clamp(34px, 5vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.05em;
            margin: 0;
        }

        .sf-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .sf-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .20);
        }

        .sf-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .sf-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 22px;
            border-radius: 999px;
            font-weight: 950;
            text-decoration: none;
            box-shadow: 0 16px 28px rgba(0, 0, 0, .18);
            transition: transform .15s ease, opacity .15s ease;
        }

        .sf-action:hover {
            transform: translateY(-1px);
            opacity: .94;
        }

        .sf-action-edit {
            background: linear-gradient(135deg, #facc15, #f59e0b);
            color: #111827;
        }

        .sf-action-back {
            background: rgba(255, 255, 255, .16);
            color: white;
            border: 1px solid rgba(255, 255, 255, .20);
        }

        .sf-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .sf-stat {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 22px;
            background: white;
            border: 1px solid rgba(15, 39, 64, .08);
            box-shadow: 0 18px 44px rgba(15, 39, 64, .08);
        }

        .sf-stat::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: linear-gradient(90deg, #22d3ee, #2563eb);
        }

        .sf-stat-label {
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #64748b;
        }

        .sf-stat-number {
            margin-top: 10px;
            font-size: 32px;
            line-height: 1;
            font-weight: 950;
            color: #234b74;
        }

        .sf-stat-sub {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
        }

        .sf-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 20px;
            margin-top: 20px;
        }

        .sf-card {
            overflow: hidden;
            border-radius: 28px;
            background: white;
            border: 1px solid rgba(15, 39, 64, .08);
            box-shadow: 0 18px 44px rgba(15, 39, 64, .08);
        }

        .sf-card-head {
            padding: 22px 24px;
            border-bottom: 1px solid rgba(15, 39, 64, .08);
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
                #ffffff;
        }

        .sf-section-pill {
            display: inline-flex;
            border-radius: 999px;
            padding: 6px 12px;
            background: #e0f2fe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .sf-card-title {
            margin: 0;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.03em;
            color: #0f172a;
        }

        .sf-card-body {
            padding: 22px 24px 26px;
        }

        .sf-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .sf-info-box {
            border-radius: 18px;
            padding: 16px;
            border: 1px solid rgba(15, 39, 64, .08);
            background: #f8fafc;
        }

        .sf-info-label {
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 8px;
        }

        .sf-info-value {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
            word-break: break-word;
        }

        .sf-project-list {
            display: grid;
            gap: 12px;
        }

        .sf-project-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid rgba(15, 39, 64, .08);
            background: #f8fafc;
            text-decoration: none;
            color: inherit;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .sf-project-row:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(15, 39, 64, .10);
        }

        .sf-project-name {
            font-size: 16px;
            font-weight: 950;
            color: #0f172a;
        }

        .sf-project-code {
            margin-top: 4px;
            font-size: 13px;
            font-weight: 800;
            color: #64748b;
        }

        .sf-project-pill {
            flex: 0 0 auto;
            display: inline-flex;
            border-radius: 999px;
            padding: 7px 12px;
            background: #e0f2fe;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 950;
        }

        .sf-empty {
            border-radius: 18px;
            padding: 18px;
            background: #f8fafc;
            border: 1px dashed rgba(15, 39, 64, .16);
            color: #64748b;
            font-weight: 800;
        }

        @media (prefers-color-scheme: dark) {
            .sf-stat,
            .sf-card {
                background: #0f172a;
                border-color: rgba(255, 255, 255, .08);
            }

            .sf-card-head {
                background:
                    radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
                    #111827;
                border-bottom-color: rgba(255, 255, 255, .08);
            }

            .sf-card-title,
            .sf-info-value,
            .sf-project-name {
                color: #f8fafc;
            }

            .sf-stat-number {
                color: #e0f2fe;
            }

            .sf-info-box,
            .sf-project-row,
            .sf-empty {
                background: rgba(255, 255, 255, .04);
                border-color: rgba(255, 255, 255, .08);
            }
        }

        @media (max-width: 1100px) {
            .sf-stats,
            .sf-grid {
                grid-template-columns: 1fr;
            }

            .sf-client-hero-row {
                flex-direction: column;
            }

            .sf-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 700px) {
            .sf-info-grid {
                grid-template-columns: 1fr;
            }

            .sf-client-hero {
                padding: 26px;
            }
        }
    </style>

    <div class="sf-client-view">
        <section class="sf-client-hero">
            <div class="sf-client-hero-row">
                <div>
                    <div class="sf-kicker">HR › Clients › Review</div>
                    <h1 class="sf-client-title">{{ $clientName }}</h1>

                    <div class="sf-pills">
                        <span class="sf-pill">{{ $clientCode }}</span>
                        <span class="sf-pill">{{ $isActive ? 'Active Client' : 'Inactive Client' }}</span>
                    </div>
                </div>

                <div class="sf-actions">
                    @if ((bool) auth()->user()?->canErp('clients', 'edit'))
                        <a href="{{ $editUrl }}" class="sf-action sf-action-edit">Edit Client</a>
                    @endif

                    <a href="{{ $clientsUrl }}" class="sf-action sf-action-back">Back to Clients</a>
                </div>
            </div>
        </section>

        <section class="sf-stats">
            <div class="sf-stat">
                <div class="sf-stat-label">Projects</div>
                <div class="sf-stat-number">{{ $projects->count() }}</div>
                <div class="sf-stat-sub">Linked projects</div>
            </div>

            <div class="sf-stat">
                <div class="sf-stat-label">Employees</div>
                <div class="sf-stat-number">{{ $linkedEmployees }}</div>
                <div class="sf-stat-sub">Linked employees</div>
            </div>

            <div class="sf-stat">
                <div class="sf-stat-label">Status</div>
                <div class="sf-stat-number">{{ $isActive ? '✓' : '—' }}</div>
                <div class="sf-stat-sub">{{ $isActive ? 'Active' : 'Inactive' }}</div>
            </div>

            <div class="sf-stat">
                <div class="sf-stat-label">Updated</div>
                <div class="sf-stat-number">{{ optional($client->updated_at)->format('Y-m-d') ?? '-' }}</div>
                <div class="sf-stat-sub">Last update</div>
            </div>
        </section>

        <section class="sf-grid">
            <div class="sf-card">
                <div class="sf-card-head">
                    <span class="sf-section-pill">Client Information</span>
                    <h2 class="sf-card-title">Profile & Contact Details</h2>
                </div>

                <div class="sf-card-body">
                    <div class="sf-info-grid">
                        <div class="sf-info-box">
                            <div class="sf-info-label">Client Name</div>
                            <div class="sf-info-value">{{ $clientName }}</div>
                        </div>

                        <div class="sf-info-box">
                            <div class="sf-info-label">Client Code</div>
                            <div class="sf-info-value">{{ $clientCode }}</div>
                        </div>

                        <div class="sf-info-box">
                            <div class="sf-info-label">Contact Person</div>
                            <div class="sf-info-value">{{ $contactPerson }}</div>
                        </div>

                        <div class="sf-info-box">
                            <div class="sf-info-label">Phone</div>
                            <div class="sf-info-value">{{ $phone }}</div>
                        </div>

                        <div class="sf-info-box">
                            <div class="sf-info-label">Email</div>
                            <div class="sf-info-value">{{ $email }}</div>
                        </div>

                        <div class="sf-info-box">
                            <div class="sf-info-label">Address</div>
                            <div class="sf-info-value">{{ $address }}</div>
                        </div>

                        <div class="sf-info-box">
                            <div class="sf-info-label">Created</div>
                            <div class="sf-info-value">{{ optional($client->created_at)->format('Y-m-d') ?? '-' }}</div>
                        </div>

                        <div class="sf-info-box">
                            <div class="sf-info-label">Updated</div>
                            <div class="sf-info-value">{{ optional($client->updated_at)->format('Y-m-d') ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sf-card">
                <div class="sf-card-head">
                    <span class="sf-section-pill">Linked Projects</span>
                    <h2 class="sf-card-title">Project Overview</h2>
                </div>

                <div class="sf-card-body">
                    <div class="sf-project-list">
                        @forelse ($projects as $project)
                            @php
                                $projectName = $project->name ?? $project->project_name ?? 'Unnamed Project';
                                $projectCode = $project->project_code ?? $project->code ?? ('PR-' . str_pad((string) $project->id, 4, '0', STR_PAD_LEFT));
                                $projectUrl = url('/admin/projects/' . $project->id);
                            @endphp

                            <a href="{{ $projectUrl }}" class="sf-project-row">
                                <div>
                                    <div class="sf-project-name">{{ $projectName }}</div>
                                    <div class="sf-project-code">{{ $projectCode }}</div>
                                </div>

                                <span class="sf-project-pill">Open Project</span>
                            </a>
                        @empty
                            <div class="sf-empty">No linked projects yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
