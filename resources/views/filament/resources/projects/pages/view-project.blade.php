<x-filament-panels::page>
    <style>
        .fi-header,
        .fi-page-header,
        .fi-page-header-heading,
        .fi-page-header-breadcrumbs,
        .fi-page-header-actions,
        .fi-page-header-ctas {
            display: none !important;
        }

        .sf-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-hero {
            border-radius: 30px;
            padding: 28px;
            border: 1px solid #dbe7ee;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbfd 60%, #eef8fb 100%);
        }

        .sf-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.85), transparent 36%),
                linear-gradient(135deg, rgba(255,255,255,.10), rgba(255,255,255,0));
            pointer-events: none;
        }

        .sf-hero--planning {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 55%, #fde68a 100%);
        }

        .sf-hero--active {
            background: linear-gradient(135deg, #ecfeff 0%, #cffafe 45%, #dbeafe 100%);
        }

        .sf-hero--hold {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
        }

        .sf-hero--completed {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 50%, #bbf7d0 100%);
        }

        .sf-hero--cancelled {
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 50%, #fecdd3 100%);
        }

        .sf-hero-head {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
        }

        .sf-hero-title .kicker {
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 10px;
        }

        .sf-hero-title h1 {
            margin: 0;
            font-size: clamp(42px, 5vw, 72px);
            line-height: .92;
            letter-spacing: -.05em;
            font-weight: 900;
            color: #12385f;
        }

        .sf-meta-wrap {
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: flex-end;
        }

        .sf-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 999px;
            border: 1px solid rgba(148,163,184,.45);
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(8px);
            color: #0f172a;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 8px 20px rgba(15,23,42,.05);
        }

        .sf-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .sf-btn {
            border: none;
            outline: none;
            cursor: pointer;
            border-radius: 16px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: -.02em;
            transition: 160ms ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 22px rgba(15,23,42,.08);
        }

        .sf-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(15,23,42,.11);
        }

        .sf-btn-primary {
            background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%);
            color: #fff;
        }

        .sf-btn-soft {
            background: rgba(255,255,255,.92);
            color: #0f172a;
            border: 1px solid #dbe4ea;
        }

        .sf-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .sf-stat {
            border-radius: 22px;
            padding: 18px 20px;
            background: #fff;
            border: 1px solid #dbe4ea;
            box-shadow: 0 10px 22px rgba(15,23,42,.05);
        }

        .sf-stat .label {
            font-size: 13px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 8px;
        }

        .sf-stat .value {
            font-size: 30px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
        }

        .sf-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 18px;
        }

        .sf-panel {
            background: #fff;
            border: 1px solid #dbe7ee;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 12px 24px rgba(15,23,42,.04);
        }

        .sf-panel h3 {
            margin: 0 0 6px;
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
        }

        .sf-panel .sub {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 16px;
        }

        .sf-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sf-table td {
            padding: 10px 0;
            border-bottom: 1px solid #eef2f7;
            vertical-align: top;
        }

        .sf-table tr:last-child td {
            border-bottom: none;
        }

        .sf-table td:first-child {
            width: 34%;
            color: #64748b;
            font-weight: 800;
        }

        .sf-table td:last-child {
            color: #0f172a;
            font-weight: 700;
        }

        .sf-currency-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .sf-currency-card {
            border: 1px solid #dbe7ee;
            border-radius: 18px;
            padding: 16px 16px 14px;
            background: #fbfdff;
        }

        .sf-currency-card .ccy {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .sf-currency-card .meta {
            font-size: 13px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .sf-currency-card .amount {
            font-size: 28px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.05;
        }

        .sf-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .sf-list-item {
            padding: 14px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #fff;
        }

        .sf-list-item .title {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
        }

        .sf-list-item .meta {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 700;
        }

        .sf-empty {
            padding: 18px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #64748b;
            font-weight: 700;
        }

        @media (max-width: 1024px) {
            .sf-grid {
                grid-template-columns: 1fr;
            }

            .sf-meta-wrap {
                align-items: flex-start;
            }

            .sf-actions {
                justify-content: flex-start;
            }
        }
    </style>

    @php
        $stats = $this->projectStats();
        $expenses = $stats['expenses'] ?? [];
        $latestEmployees = $this->latestEmployees();
        $latestExpenses = $this->latestExpenses();
    @endphp

    <div class="sf-shell">
        <section class="sf-hero {{ $this->projectHeroClass() }}">
            <div class="sf-hero-head">
                <div class="sf-hero-title">
                    <div class="kicker">Projects</div>
                    <h1>{{ $record->name ?? 'Project' }}</h1>
                </div>

                <div class="sf-meta-wrap">
                    <div class="sf-status-badge">
                        <span>Current Status</span>
                        <span>•</span>
                        <span>{{ $this->projectStatusLabel() }}</span>
                    </div>

                    <div class="sf-actions">
                        <button type="button" wire:click="mountAction('generateInvoice')" class="sf-btn sf-btn-primary">
                            Generate Invoice
                        </button>

                        <a href="{{ \App\Filament\Resources\Projects\ProjectResource::getUrl('edit', ['record' => $record]) }}" class="sf-btn sf-btn-soft">
                            Edit Project
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="sf-stats-grid">
            <div class="sf-stat">
                <div class="label">Client</div>
                <div class="value" style="font-size: 22px;">{{ $record->client?->name ?: '-' }}</div>
            </div>

            <div class="sf-stat">
                <div class="label">Jobs</div>
                <div class="value">{{ $stats['jobs_count'] ?? 0 }}</div>
            </div>

            <div class="sf-stat">
                <div class="label">Employees</div>
                <div class="value">{{ $stats['employees_count'] ?? 0 }}</div>
            </div>

            <div class="sf-stat">
                <div class="label">Active Employees</div>
                <div class="value">{{ $stats['active_employees_count'] ?? 0 }}</div>
            </div>
        </section>

        @livewire(\App\Filament\Resources\Projects\Widgets\ProjectFinanceSummary::class, ['record' => $record])

        <div class="sf-grid">
            <section class="sf-panel">
                <h3>Project Overview</h3>
                <div class="sub">Operational and commercial identity for this project.</div>

                <table class="sf-table">
                    <tr><td>Project Name</td><td>{{ $record->name ?: '-' }}</td></tr>
                    <tr><td>Project Code</td><td>{{ $record->project_code ?: '-' }}</td></tr>
                    <tr><td>Client</td><td>{{ $record->client?->name ?: '-' }}</td></tr>
                    <tr><td>Location</td><td>{{ $record->location ?: '-' }}</td></tr>
                    <tr><td>Site Type</td><td>{{ $record->code ?: '-' }}</td></tr>
                    <tr><td>Start Date</td><td>{{ !empty($record->start_date) ? \Illuminate\Support\Carbon::parse($record->start_date)->format('Y-m-d') : '-' }}</td></tr>
                    <tr><td>End Date</td><td>{{ !empty($record->end_date) ? \Illuminate\Support\Carbon::parse($record->end_date)->format('Y-m-d') : '-' }}</td></tr>
                    <tr><td>Description</td><td>{{ $record->description ?: '-' }}</td></tr>
                    <tr><td>Notes</td><td>{{ $record->notes ?: '-' }}</td></tr>
                </table>
            </section>

            <section class="sf-panel">
                <h3>Expense Snapshot</h3>
                <div class="sub">Direct project-level expenses by currency.</div>

                <div class="sf-currency-cards">
                    @foreach (['USD', 'EUR', 'GBP', 'LYD'] as $currency)
                        <div class="sf-currency-card">
                            <div class="ccy">{{ $currency }}</div>
                            <div class="meta">Linked project expenses</div>
                            <div class="amount">{{ number_format((float) ($expenses[$currency] ?? 0), 2) }} {{ $currency }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="sf-grid">
            <section class="sf-panel">
                <h3>Latest Employees</h3>
                <div class="sub">Employees currently or previously linked to this project.</div>

                <div class="sf-list">
                    @forelse ($latestEmployees as $employee)
                        <div class="sf-list-item">
                            <div class="title">{{ $employee->employee_name ?: '-' }}</div>
                            <div class="meta">
                                {{ $employee->position_title ?: '-' }} • {{ $employee->status ?: '-' }}
                            </div>
                        </div>
                    @empty
                        <div class="sf-empty">No employees linked to this project yet.</div>
                    @endforelse
                </div>
            </section>

            <section class="sf-panel">
                <h3>Latest Expenses</h3>
                <div class="sub">Recent costs linked directly to this project.</div>

                <div class="sf-list">
                    @forelse ($latestExpenses as $expense)
                        <div class="sf-list-item">
                            <div class="title">
                                {{ $expense->title ?: ($expense->category ? ucfirst(str_replace('_', ' ', $expense->category)) : 'Expense') }}
                            </div>
                            <div class="meta">
                                {{ number_format((float) $expense->amount, 2) }} {{ $expense->currency ?: '' }}
                                •
                                {{ optional($expense->expense_date)->format('Y-m-d') ?: '-' }}
                            </div>
                        </div>
                    @empty
                        <div class="sf-empty">No direct project expenses yet.</div>
                    @endforelse
                </div>
            </section>
        </div>

        {{ $this->infolist }}
    </div>
</x-filament-panels::page>
