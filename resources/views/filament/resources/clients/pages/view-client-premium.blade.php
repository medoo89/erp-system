<x-filament-panels::page>
    @php
        $client = $this->record;

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

        $clientResource = \App\Filament\Resources\Clients\ClientResource::class;
        $editUrl = $clientResource::getUrl('edit', ['record' => $client]);
        $indexUrl = $clientResource::getUrl('index');

        $createdAt = optional($client->created_at)->format('Y-m-d') ?? '-';
        $updatedAt = optional($client->updated_at)->format('Y-m-d') ?? '-';
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .cv-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .cv-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px 36px;
            border: 1px solid rgba(76, 167, 168, .24);
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .30), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .18), transparent 30%),
                linear-gradient(135deg, #081a34 0%, #12385d 56%, #2f6f73 100%) !important;
            box-shadow: 0 18px 36px rgba(15, 23, 42, .14);
            color: #fff;
        }

        .cv-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .cv-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .cv-breadcrumb {
            font-size: 14px;
            color: rgba(255, 255, 255, .72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .cv-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .cv-subtitle {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            color: rgba(255, 255, 255, .84);
            font-size: 15px;
            font-weight: 750;
        }

        .cv-pill {
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

        .cv-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .cv-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 950;
            transition: .18s ease;
        }

        .cv-btn-primary {
            background: #f2b705;
            color: #3b2a00 !important;
            box-shadow: 0 12px 24px rgba(242, 183, 5, .22);
        }

        .cv-btn-secondary {
            background: rgba(255,255,255,.12);
            color: #fff !important;
            border: 1px solid rgba(255,255,255,.16);
        }

        .cv-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .cv-kpi,
        .cv-card {
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
        }

        .cv-kpi {
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .cv-kpi::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #7ad6d7);
        }

        .cv-kpi-label {
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #728195;
        }

        .cv-kpi-value {
            margin-top: 12px;
            font-size: 34px;
            line-height: 1;
            font-weight: 950;
            color: #0f172a;
        }

        .cv-kpi-sub {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
        }

        .cv-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 18px;
        }

        .cv-card {
            overflow: hidden;
        }

        .cv-card-head {
            padding: 16px 20px;
            border-bottom: 1px solid #e4ecef;
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%);
        }

        .cv-card-kicker {
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

        .cv-card-title {
            margin-top: 10px;
            font-size: 22px;
            line-height: 1.1;
            font-weight: 950;
            color: #0f172a;
        }

        .cv-card-body {
            padding: 20px;
        }

        .cv-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .cv-info {
            border-radius: 18px;
            border: 1px solid #e4ecef;
            background: #ffffff;
            padding: 14px;
        }

        .cv-info-label {
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #728195;
        }

        .cv-info-value {
            margin-top: 8px;
            font-size: 15px;
            line-height: 1.4;
            font-weight: 850;
            color: #0f172a;
            word-break: break-word;
        }

        .cv-project-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .cv-project {
            border-radius: 18px;
            border: 1px solid #e4ecef;
            background: #ffffff;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .cv-project-name {
            font-size: 15px;
            font-weight: 950;
            color: #0f172a;
        }

        .cv-project-sub {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
            font-weight: 700;
        }

        .cv-project-badge {
            flex-shrink: 0;
            border-radius: 999px;
            padding: 7px 10px;
            background: #eef5f8;
            color: #1f4664;
            font-size: 11px;
            font-weight: 950;
        }

        .cv-empty {
            border-radius: 18px;
            border: 1px dashed #b7c8d3;
            background: rgba(255,255,255,.70);
            padding: 22px;
            color: #64748b;
            font-weight: 800;
            text-align: center;
        }

        .dark .cv-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76, 167, 168, .18);
        }

        .dark .cv-kpi,
        .dark .cv-card {
            background: linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(15,23,42,.96) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            box-shadow: 0 14px 30px rgba(0,0,0,.28) !important;
        }

        .dark .cv-card-head,
        .dark .cv-info,
        .dark .cv-project {
            background: rgba(15,23,42,.92) !important;
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .cv-kpi-value,
        .dark .cv-card-title,
        .dark .cv-info-value,
        .dark .cv-project-name {
            color: #f8fafc !important;
        }

        .dark .cv-kpi-label,
        .dark .cv-kpi-sub,
        .dark .cv-info-label,
        .dark .cv-project-sub {
            color: #aab8c6 !important;
        }

        .dark .cv-card-kicker,
        .dark .cv-project-badge {
            background: rgba(76,167,168,.12) !important;
            color: #8fd6d7 !important;
        }

        .dark .cv-empty {
            background: rgba(15,23,42,.72) !important;
            border-color: rgba(76,167,168,.18) !important;
            color: #aab8c6 !important;
        }

        @media (max-width: 1100px) {
            .cv-kpi-grid,
            .cv-grid,
            .cv-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="cv-wrap">
        <section class="cv-hero">
            <div class="cv-hero-inner">
                <div>
                    <div class="cv-breadcrumb">HR › Clients › View</div>
                    <div class="cv-title">{{ $clientName }}</div>
                    <div class="cv-subtitle">
                        <span class="cv-pill">{{ $clientCode }}</span>
                        <span class="cv-pill">{{ $isActive ? 'Active Client' : 'Inactive Client' }}</span>
                    </div>
                </div>

                <div class="cv-actions">
                    <a href="{{ $editUrl }}" class="cv-btn cv-btn-primary">Edit Client</a>
                    <a href="{{ $indexUrl }}" class="cv-btn cv-btn-secondary">Back to Clients</a>
                </div>
            </div>
        </section>

        <section class="cv-kpi-grid">
            <div class="cv-kpi">
                <div class="cv-kpi-label">Projects</div>
                <div class="cv-kpi-value">{{ $projectsCount }}</div>
                <div class="cv-kpi-sub">Linked projects</div>
            </div>

            <div class="cv-kpi">
                <div class="cv-kpi-label">Employees</div>
                <div class="cv-kpi-value">{{ $employeesCount }}</div>
                <div class="cv-kpi-sub">Linked employees</div>
            </div>

            <div class="cv-kpi">
                <div class="cv-kpi-label">Status</div>
                <div class="cv-kpi-value">{{ $isActive ? '✓' : '—' }}</div>
                <div class="cv-kpi-sub">{{ $isActive ? 'Active' : 'Inactive' }}</div>
            </div>

            <div class="cv-kpi">
                <div class="cv-kpi-label">Updated</div>
                <div class="cv-kpi-value" style="font-size: 24px;">{{ $updatedAt }}</div>
                <div class="cv-kpi-sub">Last update</div>
            </div>
        </section>

        <section class="cv-grid">
            <div class="cv-card">
                <div class="cv-card-head">
                    <div class="cv-card-kicker">Client Information</div>
                    <div class="cv-card-title">Profile & Contact Details</div>
                </div>

                <div class="cv-card-body">
                    <div class="cv-info-grid">
                        <div class="cv-info">
                            <div class="cv-info-label">Client Name</div>
                            <div class="cv-info-value">{{ $clientName }}</div>
                        </div>

                        <div class="cv-info">
                            <div class="cv-info-label">Client Code</div>
                            <div class="cv-info-value">{{ $clientCode }}</div>
                        </div>

                        <div class="cv-info">
                            <div class="cv-info-label">Contact Person</div>
                            <div class="cv-info-value">{{ $contactPerson }}</div>
                        </div>

                        <div class="cv-info">
                            <div class="cv-info-label">Phone</div>
                            <div class="cv-info-value">{{ $phone }}</div>
                        </div>

                        <div class="cv-info">
                            <div class="cv-info-label">Email</div>
                            <div class="cv-info-value">{{ $email }}</div>
                        </div>

                        <div class="cv-info">
                            <div class="cv-info-label">Address</div>
                            <div class="cv-info-value">{{ $address }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cv-card">
                <div class="cv-card-head">
                    <div class="cv-card-kicker">Linked Projects</div>
                    <div class="cv-card-title">Project Overview</div>
                </div>

                <div class="cv-card-body">
                    @if($projects->isEmpty())
                        <div class="cv-empty">No linked projects for this client.</div>
                    @else
                        <div class="cv-project-list">
                            @foreach($projects as $project)
                                <div class="cv-project">
                                    <div>
                                        <div class="cv-project-name">{{ $project->name ?? 'Unnamed Project' }}</div>
                                        <div class="cv-project-sub">
                                            {{ $project->project_code ?? $project->code ?? ('Project #' . $project->id) }}
                                        </div>
                                    </div>

                                    <div class="cv-project-badge">Project</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
