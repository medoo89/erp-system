<x-filament-panels::page>
    @php

        $queryText = trim((string) request()->query('q', ''));

        $clientsQuery = \App\Models\Client::query()->orderBy('name');

        if ($queryText !== '') {
            $clientsQuery->where(function ($query) use ($queryText) {
                foreach (['name', 'client_code', 'contact_person', 'email', 'phone'] as $column) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('clients', $column)) {
                    }
                }
            });
        }

        $clients = $clientsQuery->get();

        $projectClientColumn = \Illuminate\Support\Facades\Schema::hasColumn('projects', 'client_id') ? 'client_id' : null;

        $employmentHasClientId = \Illuminate\Support\Facades\Schema::hasColumn('employments', 'client_id');
        $employmentHasProjectId = \Illuminate\Support\Facades\Schema::hasColumn('employments', 'project_id');

        $clientResource = \App\Filament\Resources\Clients\ClientResource::class;

        $safeUrl = function ($page, $record = null) use ($clientResource) {
            try {
                return $record
                    ? $clientResource::getUrl($page, ['record' => $record])
                    : $clientResource::getUrl($page);
            } catch (\Throwable $e) {
                try {
                    return '#';
                } catch (\Throwable $e) {
                    return '#';
                }
            }
        };

        $createUrl = $safeUrl('create');

        $clientStats = function ($client) use ($projectClientColumn, $employmentHasClientId, $employmentHasProjectId) {
            $projectsCount = 0;
            $employeesCount = 0;

            if ($projectClientColumn) {
                $projectsCount = \App\Models\Project::query()
                    ->where($projectClientColumn, $client->id)
                    ->count();
            }

            if ($employmentHasClientId) {
                $employeesCount = \App\Models\Employment::query()
                    ->where('client_id', $client->id)
                    ->count();
            } elseif ($employmentHasProjectId && $projectClientColumn) {
                $projectIds = \App\Models\Project::query()
                    ->where($projectClientColumn, $client->id)
                    ->pluck('id')
                    ->filter()
                    ->values();

                if ($projectIds->isNotEmpty()) {
                    $employeesCount = \App\Models\Employment::query()
                        ->whereIn('project_id', $projectIds)
                        ->count();
                }
            }

            return [$projectsCount, $employeesCount];
        };
    @endphp

    <style>
        .fi-header {
            display: none !important;
        }

        .clients-wrap {
            max-width: 1240px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .clients-hero {
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

        .clients-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .clients-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 22px;
            flex-wrap: wrap;
        }

        .clients-breadcrumb {
            font-size: 14px;
            color: rgba(255, 255, 255, .72);
            font-weight: 650;
            margin-bottom: 12px;
        }

        .clients-title {
            font-size: clamp(46px, 4vw, 66px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.055em;
            color: #fff !important;
        }

        .clients-subtitle {
            margin-top: 16px;
            max-width: 820px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, .82) !important;
        }

        .clients-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .clients-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            background: #f2b705;
            color: #3b2a00 !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 950;
            box-shadow: 0 12px 24px rgba(242, 183, 5, .22);
            transition: .18s ease;
        }

        .clients-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(242, 183, 5, .30);
        }

        .clients-tools {
            border-radius: 24px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
            padding: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .clients-search {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 280px;
        }

        .clients-search input {
            width: 100%;
            min-height: 46px;
            border-radius: 999px;
            border: 1px solid #d7e2e5;
            background: #ffffff;
            color: #0f172a;
            padding: 0 16px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .035);
            outline: none;
        }

        .clients-search button,
        .clients-reset {
            min-height: 42px;
            border-radius: 999px;
            border: 0;
            padding: 0 16px;
            font-weight: 950;
            cursor: pointer;
            text-decoration: none !important;
        }

        .clients-search button {
            background: #f2b705;
            color: #3b2a00;
        }

        .clients-reset {
            display: inline-flex;
            align-items: center;
            background: #eef5f8;
            color: #1f4664 !important;
        }

        .clients-count {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(76, 167, 168, .12);
            color: #1f4664;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .clients-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .client-card {
            position: relative;
            display: block;
            text-decoration: none !important;
            color: inherit !important;
            overflow: hidden;
            border-radius: 26px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .07);
            padding: 22px;
            transition: .20s ease;
        }

        .client-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 38px rgba(15, 23, 42, .12);
        }

        .client-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: linear-gradient(180deg, #1f4664, #4ca7a8);
        }

        .client-top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
        }

        .client-name {
            font-size: 26px;
            line-height: 1.08;
            font-weight: 950;
            letter-spacing: -.035em;
            color: #0f172a;
        }

        .client-code {
            margin-top: 8px;
            display: inline-flex;
            min-height: 28px;
            align-items: center;
            padding: 0 10px;
            border-radius: 999px;
            background: #eef5f8;
            color: #1f4664;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .client-status {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 0 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .client-status-active {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #86efac;
        }

        .client-status-inactive {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        .client-info {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .client-info-box {
            border-radius: 18px;
            border: 1px solid #e4ecef;
            background: #ffffff;
            padding: 13px;
        }

        .client-label {
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #728195;
        }

        .client-value {
            margin-top: 7px;
            font-size: 14px;
            line-height: 1.35;
            font-weight: 850;
            color: #0f172a;
            word-break: break-word;
        }

        .client-stats {
            margin-top: 16px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .client-stat {
            border-radius: 18px;
            border: 1px solid #e4ecef;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
            padding: 13px;
        }

        .client-stat-number {
            font-size: 28px;
            line-height: 1;
            font-weight: 950;
            color: #0f172a;
        }

        .client-stat-label {
            margin-top: 7px;
            font-size: 10px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #728195;
        }

        .client-footer {
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: #64748b;
            font-size: 12px;
            font-weight: 750;
        }

        .client-open {
            color: #1f4664;
            font-weight: 950;
        }

        .clients-empty {
            border-radius: 26px;
            border: 1px dashed #b7c8d3;
            background: rgba(255, 255, 255, .70);
            padding: 40px;
            text-align: center;
            color: #64748b;
            font-weight: 800;
        }

        .dark .clients-hero {
            background:
                radial-gradient(circle at 92% 20%, rgba(76, 167, 168, .20), transparent 24%),
                radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .12), transparent 30%),
                linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
            border-color: rgba(76, 167, 168, .18);
        }

        .dark .clients-tools,
        .dark .client-card,
        .dark .client-stat {
            background: linear-gradient(180deg, rgba(12, 23, 38, .98) 0%, rgba(15, 23, 42, .96) 100%) !important;
            border-color: rgba(76, 167, 168, .18) !important;
            box-shadow: 0 14px 30px rgba(0, 0, 0, .28) !important;
        }

        .dark .clients-search input,
        .dark .client-info-box {
            background: rgba(15, 23, 42, .92) !important;
            color: #f8fafc !important;
            border-color: rgba(76, 167, 168, .20) !important;
        }

        .dark .clients-search input::placeholder {
            color: rgba(226, 232, 240, .58) !important;
        }

        .dark .client-name,
        .dark .client-value,
        .dark .client-stat-number {
            color: #f8fafc !important;
        }

        .dark .client-label,
        .dark .client-stat-label,
        .dark .client-footer {
            color: #aab8c6 !important;
        }

        .dark .client-code,
        .dark .clients-count,
        .dark .clients-reset {
            background: rgba(76, 167, 168, .12) !important;
            color: #8fd6d7 !important;
        }

        .dark .clients-empty {
            background: rgba(12, 23, 38, .70);
            border-color: rgba(76, 167, 168, .20);
            color: #aab8c6;
        }

        @media (max-width: 1100px) {
            .clients-grid {
                grid-template-columns: 1fr;
            }

            .client-info,
            .client-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="clients-wrap">
        <section class="clients-hero">
            <div class="clients-hero-inner">
                <div>
                    <div class="clients-breadcrumb">HR › Clients</div>
                    <div class="clients-title">Clients</div>
                    <div class="clients-subtitle">
                        Client overview with contact details, linked projects, and employee counters in one operational view.
                    </div>
                </div>

                <div class="clients-actions">
                    <a href="{{ $createUrl }}" class="clients-btn">New Client</a>
                </div>
            </div>
        </section>

        <section class="clients-tools">
            <form method="GET" action="{{ url()->current() }}" class="clients-search">
                <input
                    type="search"
                    name="q"
                    value="{{ $queryText }}"
                    placeholder="Search client name, code, contact, email, or phone..."
                >
                <button type="submit">Search</button>

                @if($queryText !== '')
                    <a href="{{ url()->current() }}" class="clients-reset">Reset</a>
                @endif
            </form>

            <div class="clients-count">
                {{ $clients->count() }} Clients
            </div>
        </section>

        @if($clients->isEmpty())
            <section class="clients-empty">
                No clients found.
            </section>
        @else
            <section class="clients-grid">
                @foreach($clients as $client)
                    @php
                        [$projectsCount, $employeesCount] = $clientStats($client);

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

                        $clientCode = $client->client_code
                            ?? $client->code
                            ?? ('CL-' . str_pad((string) $client->id, 4, '0', STR_PAD_LEFT));

                        $isActive = true;
                        if (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'is_active')) {
                            $isActive = (bool) $client->is_active;
                        } elseif (\Illuminate\Support\Facades\Schema::hasColumn('clients', 'active')) {
                            $isActive = (bool) $client->active;
                        }

                        $clientUrl = url('/admin/clients/' . $client->id . '/view');
                    @endphp

                    <a href="{{ $clientUrl }}" class="client-card">
                        <div class="client-top">
                            <div>
                                <div class="client-name">{{ $client->name ?? 'Unnamed Client' }}</div>
                                <div class="client-code">{{ $clientCode }}</div>
                            </div>

                            <div class="client-status {{ $isActive ? 'client-status-active' : 'client-status-inactive' }}">
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </div>
                        </div>

                        <div class="client-info">
                            <div class="client-info-box">
                                <div class="client-label">Contact Person</div>
                                <div class="client-value">{{ $contactPerson }}</div>
                            </div>

                            <div class="client-info-box">
                                <div class="client-label">Phone</div>
                                <div class="client-value">{{ $phone }}</div>
                            </div>

                            <div class="client-info-box" style="grid-column: 1 / -1;">
                                <div class="client-label">Email</div>
                                <div class="client-value">{{ $email }}</div>
                            </div>
                        </div>

                        <div class="client-stats">
                            <div class="client-stat">
                                <div class="client-stat-number">{{ $projectsCount }}</div>
                                <div class="client-stat-label">Projects</div>
                            </div>

                            <div class="client-stat">
                                <div class="client-stat-number">{{ $employeesCount }}</div>
                                <div class="client-stat-label">Employees</div>
                            </div>

                            <div class="client-stat">
                                <div class="client-stat-number">{{ $isActive ? '✓' : '—' }}</div>
                                <div class="client-stat-label">Status</div>
                            </div>
                        </div>

                        <div class="client-footer">
                            <span>Created: {{ optional($client->created_at)->format('Y-m-d') ?? '-' }}</span>
                            <span class="client-open">Open Client →</span>
                        </div>
                    </a>
                @endforeach
            </section>
        @endif
    </div>
</x-filament-panels::page>
