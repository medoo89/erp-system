@php
    $clientName = $client->name ?? 'Unnamed Client';

    $clientCode = $client->client_code
        ?? $client->code
        ?? ('CL-' . str_pad((string) $client->id, 4, '0', STR_PAD_LEFT));

    $contactPerson = $client->contact_person
        ?? $client->contact_name
        ?? $client->representative_name
        ?? '-';

    $email = $client->email
        ?? $client->contact_email
        ?? '-';

    $phone = $client->phone
        ?? $client->contact_phone
        ?? '-';

    $address = $client->address
        ?? $client->location
        ?? '-';

    $isActive = true;

    if (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'is_active')) {
        $isActive = (bool) $client->is_active;
    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'active')) {
        $isActive = (bool) $client->active;
    }

    $projects = collect();

    if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'client_id')) {
        $projects = \App\Models\Project::query()
            ->where('client_id', $client->id)
            ->orderBy('name')
            ->get();
    }

    $projectsCount = $projects->count();

    $employeesCount = 0;

    if (\Illuminate\Support\Facades\Schema::hasColumn('employments', 'client_id')) {
        $employeesCount = \App\Models\Employment::query()
            ->where('client_id', $client->id)
            ->count();
    } elseif (\Illuminate\Support\Facades\Schema::hasColumn('employments', 'project_id') && $projects->isNotEmpty()) {
        $employeesCount = \App\Models\Employment::query()
            ->whereIn('project_id', $projects->pluck('id')->filter()->values())
            ->count();
    }

    $editUrl = url('/admin/clients/' . $client->id . '/edit');
    $indexUrl = url('/admin/clients');

    $createdAt = optional($client->created_at)->format('Y-m-d') ?? '-';
    $updatedAt = optional($client->updated_at)->format('Y-m-d') ?? '-';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $clientName }} - Client Review</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.13), transparent 30%),
                linear-gradient(180deg, #f6fbfb 0%, #edf6f8 100%);
            color: #0f172a;
            padding: 28px;
        }

        .wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .mini-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 950;
            color: #1f4664;
            letter-spacing: -.02em;
        }

        .mini-logo {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #081a34, #2f6f73);
            color: #fff;
            font-size: 13px;
            font-weight: 950;
        }

        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px 36px;
            border: 1px solid rgba(76, 167, 168, .24);
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .30), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .18), transparent 30%),
                linear-gradient(135deg, #081a34 0%, #12385d 56%, #2f6f73 100%);
            box-shadow: 0 18px 36px rgba(15, 23, 42, .14);
            color: #fff;
        }

        .hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 22px;
            flex-wrap: wrap;
        }

        .breadcrumb {
            font-size: 14px;
            color: rgba(255, 255, 255, .72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff;
        }

        .subtitle {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .14);
            color: #fff;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 950;
            transition: .18s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-primary {
            background: #f2b705;
            color: #3b2a00;
            box-shadow: 0 12px 24px rgba(242, 183, 5, .22);
        }

        .btn-secondary {
            background: rgba(255,255,255,.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,.16);
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .kpi,
        .card {
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .kpi {
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        .kpi::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #7ad6d7);
        }

        .kpi-label {
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #728195;
        }

        .kpi-value {
            margin-top: 12px;
            font-size: 34px;
            line-height: 1;
            font-weight: 950;
            color: #0f172a;
        }

        .kpi-sub {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 18px;
        }

        .card { overflow: hidden; }

        .card-head {
            padding: 16px 20px;
            border-bottom: 1px solid #e4ecef;
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%);
        }

        .kicker {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 0 10px;
            border-radius: 999px;
            background: rgba(76,167,168,.12);
            color: #1f4664;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .card-title {
            margin-top: 10px;
            font-size: 22px;
            line-height: 1.1;
            font-weight: 950;
            color: #0f172a;
        }

        .card-body { padding: 20px; }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .info {
            border-radius: 18px;
            border: 1px solid #e4ecef;
            background: #ffffff;
            padding: 14px;
        }

        .info-label {
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #728195;
        }

        .info-value {
            margin-top: 8px;
            font-size: 15px;
            line-height: 1.4;
            font-weight: 850;
            color: #0f172a;
            word-break: break-word;
        }

        .project-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .project {
            border-radius: 18px;
            border: 1px solid #e4ecef;
            background: #ffffff;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .project-name {
            font-size: 15px;
            font-weight: 950;
            color: #0f172a;
        }

        .project-sub {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
        }

        .project-badge {
            flex-shrink: 0;
            border-radius: 999px;
            padding: 7px 10px;
            background: #eef5f8;
            color: #1f4664;
            font-size: 11px;
            font-weight: 950;
        }

        .empty {
            border-radius: 18px;
            border: 1px dashed #b7c8d3;
            background: rgba(255,255,255,.70);
            padding: 22px;
            color: #64748b;
            font-weight: 800;
            text-align: center;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background:
                    radial-gradient(circle at top right, rgba(76,167,168,.12), transparent 26%),
                    radial-gradient(circle at bottom left, rgba(179,139,47,.06), transparent 30%),
                    linear-gradient(180deg, #071427 0%, #0b1628 45%, #0f172a 100%);
                color: #f8fafc;
            }

            .mini-brand { color: #f8fafc; }

            .kpi,
            .card {
                background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%);
                border-color: rgba(76,167,168,.18);
                box-shadow: 0 14px 30px rgba(0,0,0,.28);
            }

            .card-head,
            .info,
            .project {
                background: rgba(15,23,42,.92);
                border-color: rgba(76,167,168,.16);
            }

            .kpi-value,
            .card-title,
            .info-value,
            .project-name {
                color: #f8fafc;
            }

            .kpi-label,
            .kpi-sub,
            .info-label,
            .project-sub {
                color: #aab8c6;
            }

            .kicker,
            .project-badge {
                background: rgba(76,167,168,.12);
                color: #8fd6d7;
            }

            .empty {
                background: rgba(15,23,42,.72);
                border-color: rgba(76,167,168,.18);
                color: #aab8c6;
            }
        }

        @media (max-width: 1100px) {
            body { padding: 18px; }

            .kpi-grid,
            .grid,
            .info-grid {
                grid-template-columns: 1fr;
            }

            .hero { padding: 28px 24px; }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="top-nav">
            <div class="mini-brand">
                <div class="mini-logo">SF</div>
                <span>Sada Fezzan ERP</span>
            </div>
        </div>

        <section class="hero">
            <div class="hero-inner">
                <div>
                    <div class="breadcrumb">HR › Clients › Review</div>
                    <div class="title">{{ $clientName }}</div>
                    <div class="subtitle">
                        <span class="pill">{{ $clientCode }}</span>
                        <span class="pill">{{ $isActive ? 'Active Client' : 'Inactive Client' }}</span>
                    </div>
                </div>

                <div class="actions">
                    <a href="{{ $editUrl }}" class="btn btn-primary">Edit Client</a>
                    <a href="{{ $indexUrl }}" class="btn btn-secondary">Back to Clients</a>
                </div>
            </div>
        </section>

        <section class="kpi-grid">
            <div class="kpi">
                <div class="kpi-label">Projects</div>
                <div class="kpi-value">{{ $projectsCount }}</div>
                <div class="kpi-sub">Linked projects</div>
            </div>

            <div class="kpi">
                <div class="kpi-label">Employees</div>
                <div class="kpi-value">{{ $employeesCount }}</div>
                <div class="kpi-sub">Linked employees</div>
            </div>

            <div class="kpi">
                <div class="kpi-label">Status</div>
                <div class="kpi-value">{{ $isActive ? '✓' : '—' }}</div>
                <div class="kpi-sub">{{ $isActive ? 'Active' : 'Inactive' }}</div>
            </div>

            <div class="kpi">
                <div class="kpi-label">Updated</div>
                <div class="kpi-value" style="font-size: 24px;">{{ $updatedAt }}</div>
                <div class="kpi-sub">Last update</div>
            </div>
        </section>

        <section class="grid">
            <div class="card">
                <div class="card-head">
                    <div class="kicker">Client Information</div>
                    <div class="card-title">Profile & Contact Details</div>
                </div>

                <div class="card-body">
                    <div class="info-grid">
                        <div class="info">
                            <div class="info-label">Client Name</div>
                            <div class="info-value">{{ $clientName }}</div>
                        </div>

                        <div class="info">
                            <div class="info-label">Client Code</div>
                            <div class="info-value">{{ $clientCode }}</div>
                        </div>

                        <div class="info">
                            <div class="info-label">Contact Person</div>
                            <div class="info-value">{{ $contactPerson }}</div>
                        </div>

                        <div class="info">
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $phone }}</div>
                        </div>

                        <div class="info">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $email }}</div>
                        </div>

                        <div class="info">
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $address }}</div>
                        </div>

                        <div class="info">
                            <div class="info-label">Created</div>
                            <div class="info-value">{{ $createdAt }}</div>
                        </div>

                        <div class="info">
                            <div class="info-label">Updated</div>
                            <div class="info-value">{{ $updatedAt }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div class="kicker">Linked Projects</div>
                    <div class="card-title">Project Overview</div>
                </div>

                <div class="card-body">
                    @if($projects->isEmpty())
                        <div class="empty">No linked projects for this client.</div>
                    @else
                        <div class="project-list">
                            @foreach($projects as $project)
                                <div class="project">
                                    <div>
                                        <div class="project-name">{{ $project->name ?? 'Unnamed Project' }}</div>
                                        <div class="project-sub">
                                            {{ $project->project_code ?? $project->code ?? ('Project #' . $project->id) }}
                                        </div>
                                    </div>

                                    <div class="project-badge">Project</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</body>
</html>
