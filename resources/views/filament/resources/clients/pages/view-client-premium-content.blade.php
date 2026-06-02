
    @php

    $client = $record ?? $client ?? null;


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

        $status = $client->status ?? 'active';
        $statusLabel = filled($status) ? strtoupper((string) $status) : 'ACTIVE CLIENT';
    
@endphp

    <style>
        .sf-client-clean {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 8px 0 40px;
        }

        .sf-client-hero {
            border-radius: 30px;
            padding: 34px;
            color: white;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .22), transparent 34%),
                linear-gradient(135deg, #071827 0%, #123a5a 55%, #23706f 100%);
            box-shadow: 0 22px 60px rgba(15, 23, 42, .16);
            border: 1px solid rgba(255, 255, 255, .18);
            overflow: hidden;
            position: relative;
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

        .sf-client-hero-top {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
        }

        .sf-client-kicker {
            font-size: 12px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,.72);
            font-weight: 800;
            margin-bottom: 10px;
        }

        .sf-client-title {
            font-size: clamp(34px, 5vw, 64px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -0.06em;
            margin: 0;
        }

        .sf-client-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        .sf-client-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 8px 13px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.18);
            color: white;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .08em;
        }

        .sf-client-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .sf-client-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 13px 20px;
            font-weight: 950;
            font-size: 13px;
            text-decoration: none;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .18);
            white-space: nowrap;
        }

        .sf-client-btn-warning {
            background: #facc15;
            color: #111827;
        }

        .sf-client-btn-muted {
            background: rgba(255,255,255,.13);
            color: white;
            border: 1px solid rgba(255,255,255,.18);
        }

        .sf-client-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .sf-client-stat {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 36%),
                var(--filament-panels-card-background-color, #ffffff);
            border: 1px solid rgba(148, 163, 184, .22);
            box-shadow: 0 16px 40px rgba(15, 23, 42, .08);
            padding: 20px;
        }

        .sf-client-stat::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #22d3ee, #2563eb);
        }

        .sf-client-stat-label {
            font-size: 11px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 950;
        }

        .sf-client-stat-value {
            margin-top: 8px;
            color: #234b74;
            font-size: 30px;
            font-weight: 950;
            line-height: 1;
        }

        .sf-client-stat-note {
            margin-top: 8px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .sf-client-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, .85fr);
            gap: 18px;
            margin-top: 18px;
        }

        .sf-client-card {
            border-radius: 26px;
            background:
                radial-gradient(circle at top right, rgba(34,211,238,.08), transparent 34%),
                var(--filament-panels-card-background-color, #ffffff);
            border: 1px solid rgba(148, 163, 184, .22);
            box-shadow: 0 18px 46px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .sf-client-card-head {
            padding: 22px 24px;
            border-bottom: 1px solid rgba(148, 163, 184, .16);
        }

        .sf-client-chip {
            display: inline-flex;
            width: fit-content;
            border-radius: 999px;
            padding: 7px 12px;
            background: #e0f2fe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .sf-client-card-title {
            margin: 0;
            color: #0f172a;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -0.03em;
        }

        .sf-client-card-body {
            padding: 22px 24px 24px;
        }

        .sf-client-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .sf-client-field {
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, .18);
            background: rgba(248, 250, 252, .62);
            padding: 15px 16px;
        }

        .sf-client-field-label {
            font-size: 10px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 950;
            margin-bottom: 7px;
        }

        .sf-client-field-value {
            color: #0f172a;
            font-size: 14px;
            font-weight: 850;
            overflow-wrap: anywhere;
        }

        .sf-project-list {
            display: grid;
            gap: 12px;
        }

        .sf-project-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 16px;
            border-radius: 18px;
            border: 1px solid rgba(148, 163, 184, .20);
            background: rgba(248, 250, 252, .68);
            text-decoration: none;
            color: inherit;
            transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
        }

        .sf-project-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .10);
            border-color: rgba(37, 99, 235, .35);
        }

        .sf-project-name {
            color: #0f172a;
            font-size: 15px;
            font-weight: 950;
        }

        .sf-project-code {
            margin-top: 3px;
            color: #475569;
            font-size: 12px;
            font-weight: 750;
        }

        .sf-project-pill {
            border-radius: 999px;
            padding: 8px 12px;
            background: #e0f2fe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: 950;
            white-space: nowrap;
        }

        .sf-empty {
            padding: 18px;
            border-radius: 18px;
            border: 1px dashed rgba(148, 163, 184, .45);
            color: #64748b;
            font-weight: 800;
            text-align: center;
        }

        @media (max-width: 1050px) {
            .sf-client-stats,
            .sf-client-grid {
                grid-template-columns: 1fr;
            }

            .sf-client-info-grid {
                grid-template-columns: 1fr;
            }

            .sf-client-hero-top {
                flex-direction: column;
            }

            .sf-client-actions {
                justify-content: flex-start;
            }
        }
    </style>

    <div class="sf-client-clean">
        <section class="sf-client-hero">
            <div class="sf-client-hero-top">
                <div>
                    <div class="sf-client-kicker">HR › Clients › Review</div>
                    <h1 class="sf-client-title">{{ $client->name ?? 'Client' }}</h1>

                    <div class="sf-client-badges">
                        <span class="sf-client-badge">{{ $client->client_code ?? 'CLIENT' }}</span>
                        <span class="sf-client-badge">{{ $statusLabel }}</span>
                    </div>
                </div>

                <div class="sf-client-actions">
                    @if(auth()->user()?->canErp('clients', 'edit'))
                        <a class="sf-client-btn sf-client-btn-warning"
                           href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('edit', ['record' => $client]) }}">
                            Edit Client
                        </a>
                    @endif

                    <a class="sf-client-btn sf-client-btn-muted"
                       href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('index') }}">
                        Back to Clients
                    </a>
                </div>
            </div>
        </section>

        <section class="sf-client-stats">
            <div class="sf-client-stat">
                <div class="sf-client-stat-label">Projects</div>
                <div class="sf-client-stat-value">{{ $projects->count() }}</div>
                <div class="sf-client-stat-note">Linked projects</div>
            </div>

            <div class="sf-client-stat">
                <div class="sf-client-stat-label">Employees</div>
                <div class="sf-client-stat-value">{{ $linkedEmployees }}</div>
                <div class="sf-client-stat-note">Linked employees</div>
            </div>

            <div class="sf-client-stat">
                <div class="sf-client-stat-label">Status</div>
                <div class="sf-client-stat-value">✓</div>
                <div class="sf-client-stat-note">{{ filled($status) ? ucfirst((string) $status) : 'Active' }}</div>
            </div>

            <div class="sf-client-stat">
                <div class="sf-client-stat-label">Updated</div>
                <div class="sf-client-stat-value" style="font-size: 24px;">
                    {{ optional($client->updated_at)->format('Y-m-d') ?? '-' }}
                </div>
                <div class="sf-client-stat-note">Last update</div>
            </div>
        </section>

        <section class="sf-client-grid">
            <div class="sf-client-card">
                <div class="sf-client-card-head">
                    <div class="sf-client-chip">Client Information</div>
                    <h2 class="sf-client-card-title">Profile & Contact Details</h2>
                </div>

                <div class="sf-client-card-body">
                    <div class="sf-client-info-grid">
                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Client Name</div>
                            <div class="sf-client-field-value">{{ $client->name ?? '-' }}</div>
                        </div>

                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Client Code</div>
                            <div class="sf-client-field-value">{{ $client->client_code ?? '-' }}</div>
                        </div>

                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Contact Person</div>
                            <div class="sf-client-field-value">{{ $client->contact_person ?? '-' }}</div>
                        </div>

                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Phone</div>
                            <div class="sf-client-field-value">{{ $client->phone ?? '-' }}</div>
                        </div>

                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Email</div>
                            <div class="sf-client-field-value">{{ $client->email ?? '-' }}</div>
                        </div>

                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Address</div>
                            <div class="sf-client-field-value">{{ $client->address ?? '-' }}</div>
                        </div>

                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Created</div>
                            <div class="sf-client-field-value">{{ optional($client->created_at)->format('Y-m-d') ?? '-' }}</div>
                        </div>

                        <div class="sf-client-field">
                            <div class="sf-client-field-label">Updated</div>
                            <div class="sf-client-field-value">{{ optional($client->updated_at)->format('Y-m-d') ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sf-client-card">
                <div class="sf-client-card-head">
                    <div class="sf-client-chip">Linked Projects</div>
                    <h2 class="sf-client-card-title">Project Overview</h2>
                </div>

                <div class="sf-client-card-body">
                    <div class="sf-project-list">
                        @forelse($projects as $project)
                            <a class="sf-project-item"
                               href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('view', ['record' => $project]) }}">
                                <div>
                                    <div class="sf-project-name">{{ $project->name ?? 'Project' }}</div>
                                    <div class="sf-project-code">{{ $project->project_code ?? $project->code ?? '-' }}</div>
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

