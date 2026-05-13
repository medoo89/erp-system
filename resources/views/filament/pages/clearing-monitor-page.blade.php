<x-filament-panels::page>
    @php
        $typeBadge = function (?string $type) {
            return match ((string) $type) {
                'clearing' => ['Clearing', '#faf5ff', '#7e22ce', '#d8b4fe'],
                'bank' => ['Bank', '#eff6ff', '#1d4ed8', '#93c5fd'],
                'cash' => ['Cash', '#ecfdf5', '#047857', '#86efac'],
                default => [ucfirst((string) $type), '#f8fafc', '#475569', '#cbd5e1'],
            };
        };

        $directionBadge = function (?string $direction) {
            return match ((string) $direction) {
                'in' => ['Incoming', '#ecfdf5', '#047857', '#86efac'],
                'out' => ['Outgoing', '#fff1f2', '#be123c', '#fda4af'],
                default => [ucfirst((string) $direction), '#f8fafc', '#475569', '#cbd5e1'],
            };
        };
    @endphp

    <style>
        .fi-header,
        .fi-breadcrumbs,
        nav[aria-label="Breadcrumb"],
        .fi-page-header {
            display: none !important;
        }

        .cm-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .cm-filter-panel {
            background: #fff;
            border: 1px solid #dbe7ef;
            border-radius: 24px;
            padding: 18px;
            box-shadow: 0 8px 22px rgba(15,23,42,.05);
        }

        .cm-filter-grid {
            display: grid;
            grid-template-columns: 1.4fr repeat(3, minmax(160px, 1fr)) auto;
            gap: 12px;
            align-items: end;
        }

        .cm-filter-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #607085;
        }

        .cm-filter-field input,
        .cm-filter-field select {
            width: 100%;
            min-height: 44px;
            border: 1px solid #dbe7ef;
            border-radius: 14px;
            padding: 0 13px;
            background: #fff;
            color: #0f172a;
            font-weight: 700;
            outline: none;
        }

        .cm-filter-field input:focus,
        .cm-filter-field select:focus {
            border-color: #4ca7a8;
            box-shadow: 0 0 0 4px rgba(76,167,168,.12);
        }

        .cm-filter-reset {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0 16px;
            border: 1px solid #dbe7ef;
            background: #f8fafc;
            color: #1f4664;
            font-weight: 900;
            cursor: pointer;
        }

        .dark .cm-filter-panel {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.16);
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }

        .dark .cm-filter-field label {
            color: #9fb2c3;
        }

        .dark .cm-filter-field input,
        .dark .cm-filter-field select,
        .dark .cm-filter-reset {
            background: rgba(255,255,255,.04);
            border-color: rgba(76,167,168,.14);
            color: #f6fbff;
        }

        .cm-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .cm-kpi-card,
        .cm-section {
            background: #fff;
            border: 1px solid #dbe7ef;
            border-radius: 24px;
            box-shadow: 0 8px 22px rgba(15,23,42,.05);
        }

        .cm-kpi-card {
            padding: 22px;
        }

        .cm-section {
            padding: 28px;
        }

        .cm-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .cm-label {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .cm-value {
            margin-top: 12px;
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
        }

        .cm-sub {
            margin-top: 8px;
            font-size: 14px;
            color: #64748b;
        }

        .cm-pill {
            display: inline-block;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .cm-pill--currency {
            background: #eef2ff;
            color: #4338ca;
        }

        .cm-pill--account {
            background: #f0fdf4;
            color: #166534;
        }

        .cm-pill--transactions {
            background: #fff7ed;
            color: #c2410c;
        }

        .cm-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        .cm-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 16px 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
        }

        .cm-row-title {
            font-size: 16px;
            font-weight: 900;
            color: #0f172a;
        }

        .cm-row-value {
            font-size: 18px;
            font-weight: 900;
        }

        .cm-row-value--currency {
            color: #4338ca;
        }

        .cm-row-value--account {
            color: #166534;
        }

        .cm-empty {
            padding: 18px;
            background: #f8fafc;
            border-radius: 16px;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .cm-table-wrap {
            overflow: auto;
            margin-top: 20px;
        }

        .cm-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .cm-table th {
            text-align: left;
            padding: 10px 14px;
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .cm-table td {
            background: #f8fafc;
            padding: 16px;
            color: #0f172a;
            vertical-align: top;
        }

        .cm-table td:first-child {
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
            font-weight: 800;
        }

        .cm-table td:last-child {
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .cm-strong {
            font-weight: 900;
            color: #0f172a;
        }

        .cm-small {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }

        .cm-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 12px;
            border: 1px solid transparent;
        }

        .dark .cm-wrap {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.03), transparent 18%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.05), transparent 20%),
                linear-gradient(180deg, rgba(7,20,39,.96) 0%, rgba(10,24,42,.96) 100%);
            border-radius: 28px;
            padding: 4px;
        }

        .dark .cm-kpi-card,
        .dark .cm-section {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.16);
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }

        .dark .cm-row,
        .dark .cm-empty,
        .dark .cm-table td {
            background: rgba(255,255,255,.03);
            border-color: rgba(76,167,168,.12);
        }

        .dark .cm-label,
        .dark .cm-table th {
            color: #8ea8be;
        }

        .dark .cm-value,
        .dark .cm-row-title,
        .dark .cm-strong,
        .dark .cm-table td {
            color: #f6fbff;
        }

        .dark .cm-sub,
        .dark .cm-small,
        .dark .cm-empty {
            color: #9fb2c3;
        }

        .dark .cm-pill--currency {
            background: rgba(67,56,202,.18);
            color: #c7d2fe;
        }

        .dark .cm-pill--account {
            background: rgba(22,101,52,.22);
            color: #86efac;
        }

        .dark .cm-pill--transactions {
            background: rgba(194,65,12,.18);
            color: #fdba74;
        }

        .dark .cm-row-value--currency {
            color: #c7d2fe;
        }

        .dark .cm-row-value--account {
            color: #86efac;
        }

        @media (max-width: 1100px) {
            .cm-filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .cm-filter-reset {
                width: 100%;
            }
        }

        @media (max-width: 900px) {
            .cm-grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="cm-wrap">

        {{-- sf-clearing-monitor-hero-inserted --}}
        <x-filament.sf-finance-hero
            kicker="Treasury › Clearing Monitor"
            title="Clearing Monitor"
            subtitle="Review pending treasury receipts waiting for settlement, grouped by currency and treasury account type."
            :badge="$pendingCount . ' Pending Items'"
        />


        <section class="cm-filter-panel">
            <div class="cm-filter-grid">
                <div class="cm-filter-field">
                    <label>Search</label>
                    <input
                        type="search"
                        wire:model.live.debounce.350ms="search"
                        placeholder="Transaction, account, reference, client, project..."
                    >
                </div>

                <div class="cm-filter-field">
                    <label>Currency</label>
                    <select wire:model.live="currency">
                        <option value="">All Currencies</option>
                        @foreach($currencyOptions as $currencyOption)
                            <option value="{{ $currencyOption }}">{{ $currencyOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="cm-filter-field">
                    <label>Account Type</label>
                    <select wire:model.live="accountType">
                        <option value="">All Account Types</option>
                        @foreach($accountTypeOptions as $typeValue => $typeLabel)
                            <option value="{{ $typeValue }}">{{ $typeLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="cm-filter-field">
                    <label>Direction</label>
                    <select wire:model.live="direction">
                        <option value="">All Directions</option>
                        @foreach($directionOptions as $directionValue => $directionLabel)
                            <option value="{{ $directionValue }}">{{ $directionLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="button" wire:click="resetFilters" class="cm-filter-reset">
                    Reset
                </button>
            </div>
        </section>


        <section class="cm-kpi-grid">
            <div class="cm-kpi-card">
                <div class="cm-label">Pending Items</div>
                <div class="cm-value">{{ $pendingCount }}</div>
                <div class="cm-sub">Transactions waiting for settlement</div>
            </div>

            <div class="cm-kpi-card">
                <div class="cm-label">Currencies</div>
                <div class="cm-value">{{ count($pendingByCurrency) }}</div>
                <div class="cm-sub">Currencies currently in clearing</div>
            </div>

            <div class="cm-kpi-card">
                <div class="cm-label">Account Types</div>
                <div class="cm-value">{{ count($pendingByAccountType) }}</div>
                <div class="cm-sub">Bank / Cash / Clearing sources</div>
            </div>
        </section>

        <section class="cm-grid-2">
            <div class="cm-section">
                <div class="cm-pill cm-pill--currency">Pending by Currency</div>

                <div class="cm-list">
                    @forelse($pendingByCurrency as $currency => $amount)
                        <div class="cm-row">
                            <div class="cm-row-title">{{ $currency }}</div>
                            <div class="cm-row-value cm-row-value--currency">{{ number_format((float) $amount, 2) }}</div>
                        </div>
                    @empty
                        <div class="cm-empty">
                            No pending currency totals.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="cm-section">
                <div class="cm-pill cm-pill--account">Pending by Account Type</div>

                <div class="cm-list">
                    @forelse($pendingByAccountType as $type => $amount)
                        <div class="cm-row">
                            <div class="cm-row-title">{{ ucfirst($type) }}</div>
                            <div class="cm-row-value cm-row-value--account">{{ number_format((float) $amount, 2) }}</div>
                        </div>
                    @empty
                        <div class="cm-empty">
                            No pending account type totals.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="cm-section">
            <div class="cm-pill cm-pill--transactions">Pending Settlement Transactions</div>

            <div class="cm-table-wrap">
                <table class="cm-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transaction</th>
                            <th>Amount</th>
                            <th>Direction</th>
                            <th>Account Type</th>
                            <th>Treasury Account</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingTransactions as $txn)
                            @php

                                $referenceText = '-';
                                if ($txn->reference_type && $txn->reference_id) {
                                    $referenceText = $txn->reference_type . ' #' . $txn->reference_id;
                                } elseif ($txn->reference_type) {
                                    $referenceText = $txn->reference_type;
                                }
                            @endphp
                            <tr>
                                <td>
                                    {{ $txn->transaction_date?->format('Y-m-d') ?: '-' }}
                                </td>

                                <td>
                                    <div class="cm-strong">{{ $txn->transaction_no ?: '-' }}</div>
                                    <div class="cm-small">{{ $txn->transaction_type ?: '-' }}</div>
                                </td>

                                <td>
                                    <div class="cm-strong">
                                        {{ number_format((float) $txn->amount, 2) }} {{ $txn->currency ?: '-' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="cm-badge" style="background:{{ $directionBg }};color:{{ $directionColor }};border-color:{{ $directionBorder }};">
                                        {{ $directionText }}
                                    </span>
                                </td>

                                <td>
                                    <span class="cm-badge" style="background:{{ $typeBg }};color:{{ $typeColor }};border-color:{{ $typeBorder }};">
                                        {{ $typeText }}
                                    </span>
                                </td>

                                <td>
                                    <div class="cm-strong">{{ $txn->treasuryAccount?->account_name ?: '-' }}</div>
                                    <div class="cm-small">
                                        {{ $txn->treasuryAccount?->currency ?: '-' }}
                                        @if($txn->treasuryAccount?->institution_name)
                                            — {{ $txn->treasuryAccount->institution_name }}
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <div class="cm-strong">{{ $referenceText }}</div>
                                    @if($txn->description)
                                        <div class="cm-small">{{ $txn->description }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="cm-empty">
                                    No pending settlement transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-filament-panels::page>
