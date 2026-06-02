<x-filament-panels::page>
@php
    $project = $project ?? $this->record ?? $record ?? null;

    $projectId = $project->id ?? null;

    $projectName = $project->project_name
        ?? $project->name
        ?? $project->title
        ?? ('Project #' . ($project->id ?? ''));

    $projectCode = $project->project_code
        ?? $project->code
        ?? '-';

    $client = $project->client ?? null;
    $clientName = $client?->name
        ?? $client?->company_name
        ?? $project->client_name
        ?? '-';

    $status = $project->status ?? 'active';
    $statusLabel = ucfirst(str_replace('_', ' ', (string) $status));

    $fmt = function ($amount, $currency = null): string {
        $value = number_format((float) $amount, 2);
        return $currency ? $value . ' ' . strtoupper((string) $currency) : $value;
    };

    $safeTable = fn (string $table): bool => \Illuminate\Support\Facades\Schema::hasTable($table);
    $safeColumn = fn (string $table, string $column): bool => \Illuminate\Support\Facades\Schema::hasTable($table) && \Illuminate\Support\Facades\Schema::hasColumn($table, $column);

    $employeesCount = 0;
    if ($safeTable('employments') && $safeColumn('employments', 'project_id')) {
        $employeesCount = \Illuminate\Support\Facades\DB::table('employments')->where('project_id', $projectId)->count();
    }

    $contractRows = collect();
    if ($safeTable('project_contracts')) {
        $contractRows = \Illuminate\Support\Facades\DB::table('project_contracts')
            ->where('project_id', $projectId)
            ->orderByDesc('id')
            ->get();
    }

    $contractsCount = $contractRows->count();

    $contractTotals = [];
    $taxPaidTotals = [];

    foreach ($contractRows as $contract) {
        $currency = strtoupper((string) ($contract->currency ?? 'EUR'));
        $contractTotals[$currency] = ($contractTotals[$currency] ?? 0) + (float) ($contract->contract_value ?? 0);

        $taxCurrency = strtoupper((string) ($contract->tax_currency ?? 'LYD'));
        $taxPaidTotals[$taxCurrency] = ($taxPaidTotals[$taxCurrency] ?? 0) + (float) ($contract->tax_paid ?? 0);
    }

    $invoiceTotals = [];
    $allocatedTaxTotals = [];

    if ($safeTable('client_invoices') && $safeColumn('client_invoices', 'project_id')) {
        $invoiceRows = \Illuminate\Support\Facades\DB::table('client_invoices')
            ->where('project_id', $projectId)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get();

        foreach ($invoiceRows as $invoice) {
            if (isset($invoice->foreign_amount_due) && (float) $invoice->foreign_amount_due > 0) {
                $currency = strtoupper((string) ($invoice->foreign_currency ?? 'EUR'));
                $invoiceTotals[$currency] = ($invoiceTotals[$currency] ?? 0) + (float) $invoice->foreign_amount_due;
            } elseif (isset($invoice->foreign_amount) && (float) $invoice->foreign_amount > 0) {
                $currency = strtoupper((string) ($invoice->foreign_currency ?? 'EUR'));
                $invoiceTotals[$currency] = ($invoiceTotals[$currency] ?? 0) + (float) $invoice->foreign_amount;
            }

            if (isset($invoice->local_amount_due) && (float) $invoice->local_amount_due > 0) {
                $currency = strtoupper((string) ($invoice->local_currency ?? 'LYD'));
                $invoiceTotals[$currency] = ($invoiceTotals[$currency] ?? 0) + (float) $invoice->local_amount_due;
            } elseif (isset($invoice->local_amount) && (float) $invoice->local_amount > 0) {
                $currency = strtoupper((string) ($invoice->local_currency ?? 'LYD'));
                $invoiceTotals[$currency] = ($invoiceTotals[$currency] ?? 0) + (float) $invoice->local_amount;
            }

            if (
                (! isset($invoice->foreign_amount_due) || (float) $invoice->foreign_amount_due <= 0)
                && (! isset($invoice->local_amount_due) || (float) $invoice->local_amount_due <= 0)
                && (! isset($invoice->foreign_amount) || (float) $invoice->foreign_amount <= 0)
                && (! isset($invoice->local_amount) || (float) $invoice->local_amount <= 0)
            ) {
                foreach (['contract_consumption_amount', 'total_amount', 'grand_total', 'invoice_total', 'total', 'subtotal', 'amount'] as $amountColumn) {
                    if (isset($invoice->{$amountColumn}) && is_numeric($invoice->{$amountColumn})) {
                        $currency = strtoupper((string) ($invoice->currency ?? $invoice->display_currency ?? 'EUR'));
                        $invoiceTotals[$currency] = ($invoiceTotals[$currency] ?? 0) + (float) $invoice->{$amountColumn};
                        break;
                    }
                }
            }

            if (isset($invoice->allocated_contract_tax_amount) && (float) $invoice->allocated_contract_tax_amount > 0) {
                $currency = strtoupper((string) ($invoice->allocated_contract_tax_currency ?? 'LYD'));
                $allocatedTaxTotals[$currency] = ($allocatedTaxTotals[$currency] ?? 0) + (float) $invoice->allocated_contract_tax_amount;
            }
        }
    }

    $expenseTotals = [];
    $latestExpenses = collect();

    if ($safeTable('finance_expenses') && $safeColumn('finance_expenses', 'project_id')) {
        $latestExpenses = \Illuminate\Support\Facades\DB::table('finance_expenses')
            ->where('project_id', $projectId)
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $amountColumn = \Illuminate\Support\Facades\Schema::hasColumn('finance_expenses', 'amount')
            ? 'amount'
            : (\Illuminate\Support\Facades\Schema::hasColumn('finance_expenses', 'total_amount') ? 'total_amount' : null);

        if ($amountColumn) {
            $expenseRows = \Illuminate\Support\Facades\DB::table('finance_expenses')
                ->where('project_id', $projectId)
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->selectRaw('COALESCE(currency, "LYD") as currency, SUM(COALESCE(' . $amountColumn . ', 0)) as total_amount')
                ->groupBy('currency')
                ->get();

            foreach ($expenseRows as $row) {
                $currency = strtoupper((string) ($row->currency ?? 'LYD'));
                $expenseTotals[$currency] = (float) ($row->total_amount ?? 0);
            }
        }
    }

    $formatTotals = function (array $totals) use ($fmt): string {
        if (empty($totals)) {
            return '0.00';
        }

        return collect($totals)
            ->map(fn ($amount, $currency) => $fmt($amount, $currency))
            ->implode(' / ');
    };

    $mainCurrency = array_key_first($contractTotals) ?: 'EUR';
    $mainContractValue = (float) ($contractTotals[$mainCurrency] ?? 0);
    $mainConsumed = (float) ($invoiceTotals[$mainCurrency] ?? 0);
    $mainRemaining = $mainContractValue - $mainConsumed;
    $remainingPercent = $mainContractValue > 0 ? round(($mainRemaining / $mainContractValue) * 100, 1) : 0;

    $balanceState = $remainingPercent < 20
        ? 'danger'
        : ($remainingPercent <= 50 ? 'warning' : 'safe');

    $balanceMessage = match ($balanceState) {
        'danger' => 'Critical balance. Remaining is below 20%.',
        'warning' => 'Warning balance. Remaining is between 20% and 50%.',
        default => 'Healthy balance. Invoices are within safe range.',
    };

    $addContractUrl = url('/admin/project-contracts/create?project_id=' . $projectId);
    $addExpenseUrl = url('/admin/finance-expenses/create?project_id=' . $projectId);
    $editProjectUrl = url('/admin/projects/' . $projectId . '/edit');
    $generateInvoiceUrl = url('/admin/client-invoices/create?project_id=' . $projectId);

    $backClientUrl = $client?->id
        ? url('/admin/clients/' . $client->id)
        : url('/admin/clients');

    $contractList = $contractRows->take(5);
@endphp

<style>
    .sf-clean-project-page {
        width: min(1040px, calc(100vw - 64px));
        margin: 0 auto 80px;
        color: #0f172a;
    }

    .sf-clean-hero {
        position: relative;
        overflow: hidden;
        border-radius: 34px;
        padding: 34px 38px;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .18), transparent 36%),
            linear-gradient(135deg, #0b2a4a 0%, #0e3a5b 50%, #0f766e 100%);
        color: white;
        box-shadow: 0 26px 60px rgba(15, 39, 67, .18);
        border-bottom: 5px solid #22d3ee;
    }

    .sf-clean-hero-inner {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 28px;
        align-items: start;
    }

    .sf-clean-kicker {
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .22em;
        text-transform: uppercase;
        opacity: .82;
        margin-bottom: 10px;
    }

    .sf-clean-title {
        font-size: clamp(44px, 6vw, 74px);
        line-height: .9;
        font-weight: 1000;
        letter-spacing: -.06em;
        margin: 0;
        max-width: 520px;
    }

    .sf-clean-subtitle {
        margin-top: 18px;
        font-size: 14px;
        font-weight: 850;
        color: rgba(255,255,255,.82);
    }

    .sf-clean-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: flex-end;
        max-width: 560px;
    }

    .sf-clean-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 20px;
        border-radius: 999px;
        color: #fff;
        font-size: 13px;
        font-weight: 950;
        text-decoration: none;
        box-shadow: 0 14px 28px rgba(15,23,42,.18);
        border: 1px solid rgba(255,255,255,.18);
        white-space: nowrap;
    }

    .sf-clean-btn.green { background: linear-gradient(135deg, #10b981, #14b8a6); }
    .sf-clean-btn.red { background: linear-gradient(135deg, #ef4444, #dc2626); }
    .sf-clean-btn.blue { background: linear-gradient(135deg, #0ea5e9, #2563eb); }
    .sf-clean-btn.gray { background: rgba(255,255,255,.16); }
    .sf-clean-btn.yellow {
        background: linear-gradient(135deg, #facc15, #f59e0b);
        color: #111827;
    }

    .sf-clean-top-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin: 22px 0;
    }

    .sf-clean-stat,
    .sf-clean-card,
    .sf-clean-fin-card {
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.10), transparent 34%),
            rgba(255,255,255,.96);
        border: 1px solid rgba(148,163,184,.18);
        box-shadow: 0 22px 48px rgba(15,23,42,.075);
    }

    .sf-clean-stat {
        border-radius: 28px;
        padding: 22px;
        position: relative;
        overflow: hidden;
    }

    .sf-clean-stat::before,
    .sf-clean-card::before,
    .sf-clean-fin-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 5px;
        background: linear-gradient(90deg, #22d3ee, #2563eb);
    }

    .sf-clean-label {
        display: inline-flex;
        padding: 7px 12px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #1d4ed8;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .20em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .sf-clean-stat-title {
        color: #234b74;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .sf-clean-stat-value {
        color: #234b74;
        font-size: 28px;
        line-height: 1.05;
        font-weight: 1000;
        letter-spacing: -.04em;
    }

    .sf-clean-stat-note {
        margin-top: 8px;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
    }

    .sf-clean-finance-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin: 22px 0;
    }

    .sf-clean-fin-card {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        padding: 22px;
        min-height: 178px;
    }

    .sf-clean-fin-card.warning::before {
        background: linear-gradient(90deg, #facc15, #f97316);
    }

    .sf-clean-fin-card.danger::before,
    .sf-clean-fin-card.expense::before {
        background: linear-gradient(90deg, #fb7185, #dc2626);
    }

    .sf-clean-fin-card.tax::before {
        background: linear-gradient(90deg, #8b5cf6, #2563eb);
    }

    .sf-clean-fin-title {
        color: #234b74;
        font-size: 14px;
        font-weight: 950;
        margin-bottom: 8px;
    }

    .sf-clean-fin-value {
        font-size: 28px;
        line-height: 1.05;
        font-weight: 1000;
        letter-spacing: -.04em;
        color: #0f172a;
        margin-bottom: 10px;
        word-break: break-word;
    }

    .sf-clean-fin-note {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.45;
    }

    .sf-clean-fin-note.safe { color: #0f766e; font-weight: 950; }
    .sf-clean-fin-note.warning { color: #b45309; font-weight: 950; }
    .sf-clean-fin-note.danger { color: #dc2626; font-weight: 950; }

    .sf-clean-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding-top: 10px;
        margin-top: 10px;
        border-top: 1px solid rgba(148,163,184,.18);
        color: #334155;
        font-size: 12px;
        font-weight: 900;
    }

    .sf-clean-pill {
        padding: 6px 10px;
        border-radius: 999px;
        background: #ecfeff;
        color: #0f766e;
        border: 1px solid rgba(20,184,166,.20);
        font-size: 11px;
        font-weight: 950;
    }

    .sf-clean-pill.warning {
        background: #fffbeb;
        color: #b45309;
        border-color: rgba(245,158,11,.30);
    }

    .sf-clean-pill.danger {
        background: #fef2f2;
        color: #dc2626;
        border-color: rgba(239,68,68,.30);
    }

    .sf-clean-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
        gap: 22px;
        margin-top: 22px;
    }

    .sf-clean-card {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        padding: 24px;
    }

    .sf-clean-card-title {
        color: #0f172a;
        font-size: 24px;
        line-height: 1.1;
        font-weight: 1000;
        letter-spacing: -.04em;
        margin: 0 0 18px;
    }

    .sf-clean-list {
        display: grid;
        gap: 10px;
    }

    .sf-clean-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 15px 16px;
        border-radius: 18px;
        background: rgba(248,250,252,.92);
        border: 1px solid rgba(148,163,184,.10);
    }

    .sf-clean-row-label {
        color: #64748b;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .sf-clean-row-value {
        color: #0f172a;
        font-size: 14px;
        font-weight: 950;
        text-align: right;
    }

    .sf-clean-empty {
        padding: 18px;
        border-radius: 20px;
        border: 1px dashed rgba(148,163,184,.38);
        background: rgba(248,250,252,.74);
        color: #64748b;
        font-size: 13px;
        font-weight: 850;
    }

    .sf-clean-bottom-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
        margin-top: 22px;
    }

    @media (max-width: 1100px) {
        .sf-clean-project-page {
            width: calc(100vw - 32px);
        }

        .sf-clean-hero-inner,
        .sf-clean-main-grid,
        .sf-clean-bottom-grid {
            grid-template-columns: 1fr;
        }

        .sf-clean-actions {
            justify-content: flex-start;
        }

        .sf-clean-top-stats,
        .sf-clean-finance-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .sf-clean-project-page {
            width: calc(100vw - 20px);
        }

        .sf-clean-hero {
            padding: 28px 24px;
            border-radius: 28px;
        }

        .sf-clean-title {
            font-size: 44px;
        }

        .sf-clean-top-stats,
        .sf-clean-finance-grid {
            grid-template-columns: 1fr;
        }
    }

    .dark .sf-clean-stat,
    .dark .sf-clean-card,
    .dark .sf-clean-fin-card {
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.12), transparent 34%),
            rgba(15,23,42,.92);
        border-color: rgba(148,163,184,.18);
    }

    .dark .sf-clean-card-title,
    .dark .sf-clean-fin-value,
    .dark .sf-clean-row-value {
        color: #f8fafc;
    }

    .dark .sf-clean-row {
        background: rgba(15,23,42,.60);
    }
</style>

<div class="sf-clean-project-page">
    <section class="sf-clean-hero">
        <div class="sf-clean-hero-inner">
            <div>
                <div class="sf-clean-kicker">Projects › Review</div>
                <h1 class="sf-clean-title">{{ $projectName }}</h1>
                <div class="sf-clean-subtitle">
                    Client: {{ $clientName }} · Code: {{ $projectCode }}
                </div>
            </div>

            <div class="sf-clean-actions">
                <a href="{{ $addContractUrl }}" class="sf-clean-btn green">⊕ Add Contract</a>
                <a href="{{ $addExpenseUrl }}" class="sf-clean-btn red">▣ Add Expense</a>
                <a href="{{ $backClientUrl }}" class="sf-clean-btn gray">← Back Client</a>
                <a href="{{ $generateInvoiceUrl }}" class="sf-clean-btn blue">▣ Generate Invoice</a>
                <a href="{{ $editProjectUrl }}" class="sf-clean-btn yellow">✎ Edit Project</a>
            </div>
        </div>
    </section>

    <section class="sf-clean-top-stats">
        <div class="sf-clean-stat">
            <div class="sf-clean-stat-title">Client</div>
            <div class="sf-clean-stat-value" style="font-size:20px;">{{ $clientName }}</div>
            <div class="sf-clean-stat-note">Linked client</div>
        </div>

        <div class="sf-clean-stat">
            <div class="sf-clean-stat-title">Employees</div>
            <div class="sf-clean-stat-value">{{ $employeesCount }}</div>
            <div class="sf-clean-stat-note">Linked employees</div>
        </div>

        <div class="sf-clean-stat">
            <div class="sf-clean-stat-title">Contracts</div>
            <div class="sf-clean-stat-value">{{ $contractsCount }}</div>
            <div class="sf-clean-stat-note">Project contract records</div>
        </div>

        <div class="sf-clean-stat">
            <div class="sf-clean-stat-title">Status</div>
            <div class="sf-clean-stat-value">✓</div>
            <div class="sf-clean-stat-note">{{ $statusLabel }}</div>
        </div>
    </section>

    <section class="sf-clean-finance-grid">
        <div class="sf-clean-fin-card {{ $balanceState }}">
            <div class="sf-clean-label">Contract</div>
            <div class="sf-clean-fin-title">Remaining Contract Balance</div>
            <div class="sf-clean-fin-value">{{ $fmt($mainRemaining, $mainCurrency) }}</div>
            <div class="sf-clean-fin-note {{ $balanceState }}">{{ $balanceMessage }}</div>
            <div class="sf-clean-line">
                <span>Total Contract</span>
                <strong>{{ $formatTotals($contractTotals) }}</strong>
            </div>
            <div class="sf-clean-line">
                <span>Remaining</span>
                <span class="sf-clean-pill {{ $balanceState }}">{{ $remainingPercent }}%</span>
            </div>
        </div>

        <div class="sf-clean-fin-card">
            <div class="sf-clean-label">Invoices</div>
            <div class="sf-clean-fin-title">Client Invoices Consumed</div>
            <div class="sf-clean-fin-value">{{ $formatTotals($invoiceTotals) }}</div>
            <div class="sf-clean-fin-note">Only client invoices consume the contract balance.</div>
            <div class="sf-clean-line">
                <span>Consumption source</span>
                <strong>Invoices only</strong>
            </div>
        </div>

        <div class="sf-clean-fin-card expense">
            <div class="sf-clean-label">Expenses</div>
            <div class="sf-clean-fin-title">Company Costs Linked</div>
            <div class="sf-clean-fin-value">{{ $formatTotals($expenseTotals) }}</div>
            <div class="sf-clean-fin-note danger">Tracking only. Does not reduce contract balance.</div>
            <div class="sf-clean-line">
                <span>Contract effect</span>
                <strong>0.00</strong>
            </div>
        </div>

        <div class="sf-clean-fin-card tax">
            <div class="sf-clean-label">Tax</div>
            <div class="sf-clean-fin-title">Internal Tax Allocation</div>
            <div class="sf-clean-fin-value">{{ $formatTotals($allocatedTaxTotals) }}</div>
            <div class="sf-clean-fin-note">Allocated internally by invoice value vs contract value.</div>
            <div class="sf-clean-line">
                <span>Tax Paid</span>
                <strong>{{ $formatTotals($taxPaidTotals) }}</strong>
            </div>
        </div>
    </section>

    <section class="sf-clean-main-grid">
        <div class="sf-clean-card">
            <div class="sf-clean-label">Project Overview</div>
            <h2 class="sf-clean-card-title">Operational & Commercial Details</h2>

            <div class="sf-clean-list">
                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">Project Name</div>
                    <div class="sf-clean-row-value">{{ $projectName }}</div>
                </div>

                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">Project Code</div>
                    <div class="sf-clean-row-value">{{ $projectCode }}</div>
                </div>

                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">Client</div>
                    <div class="sf-clean-row-value">{{ $clientName }}</div>
                </div>

                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">Location</div>
                    <div class="sf-clean-row-value">{{ $project->location ?? '-' }}</div>
                </div>

                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">Site Type</div>
                    <div class="sf-clean-row-value">{{ $project->site_type ?? '-' }}</div>
                </div>

                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">Start Date</div>
                    <div class="sf-clean-row-value">{{ filled($project?->start_date ?? null) ? \Illuminate\Support\Carbon::parse($project->start_date)->format('Y-m-d') : '-' }}</div>
                </div>

                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">End Date</div>
                    <div class="sf-clean-row-value">{{ filled($project?->end_date ?? null) ? \Illuminate\Support\Carbon::parse($project->end_date)->format('Y-m-d') : '-' }}</div>
                </div>

                <div class="sf-clean-row">
                    <div class="sf-clean-row-label">Description</div>
                    <div class="sf-clean-row-value">{{ $project->description ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="sf-clean-card">
            <div class="sf-clean-label">Finance Snapshot</div>
            <h2 class="sf-clean-card-title">Expenses by Currency</h2>

            <div class="sf-clean-list">
                @forelse ($expenseTotals as $currency => $amount)
                    <div class="sf-clean-row">
                        <div>
                            <div class="sf-clean-row-label">{{ $currency }}</div>
                            <div class="sf-clean-row-value" style="text-align:left;">Project expenses</div>
                        </div>
                        <div class="sf-clean-row-value">{{ $fmt($amount, $currency) }}</div>
                    </div>
                @empty
                    <div class="sf-clean-empty">No linked expenses yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="sf-clean-bottom-grid">
        <div class="sf-clean-card">
            <div class="sf-clean-label">Contract Records</div>
            <h2 class="sf-clean-card-title">Latest Contracts / Amendments</h2>

            <div class="sf-clean-list">
                @forelse ($contractList as $contract)
                    <div class="sf-clean-row">
                        <div>
                            <div class="sf-clean-row-label">Contract</div>
                            <div class="sf-clean-row-value" style="text-align:left;">
                                {{ $contract->contract_no ?? $contract->title ?? ('Contract #' . $contract->id) }}
                            </div>
                        </div>
                        <div class="sf-clean-row-value">
                            {{ $fmt($contract->contract_value ?? 0, $contract->currency ?? 'EUR') }}
                        </div>
                    </div>
                @empty
                    <div class="sf-clean-empty">No contract records yet.</div>
                @endforelse
            </div>
        </div>

        <div class="sf-clean-card">
            <div class="sf-clean-label">Expenses</div>
            <h2 class="sf-clean-card-title">Latest Project Expenses</h2>

            <div class="sf-clean-list">
                @forelse ($latestExpenses as $expense)
                    <div class="sf-clean-row">
                        <div>
                            <div class="sf-clean-row-label">Expense</div>
                            <div class="sf-clean-row-value" style="text-align:left;">
                                {{ $expense->title ?? $expense->description ?? $expense->expense_type ?? ('Expense #' . $expense->id) }}
                            </div>
                        </div>
                        <div class="sf-clean-row-value">
                            {{ $fmt($expense->amount ?? $expense->total_amount ?? 0, $expense->currency ?? 'LYD') }}
                        </div>
                    </div>
                @empty
                    <div class="sf-clean-empty">No latest expenses yet.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>
</x-filament-panels::page>
