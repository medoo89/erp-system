<x-filament-panels::page>
    @php
        $account = $this->record;

        $typeRaw = (string) ($account->account_type ?? '');
        $typeLabel = match ($typeRaw) {
            \App\Models\TreasuryAccount::TYPE_BANK => 'Bank',
            \App\Models\TreasuryAccount::TYPE_CASH => 'Cash',
            \App\Models\TreasuryAccount::TYPE_CLEARING => 'Clearing',
            default => ucfirst($typeRaw ?: 'Unknown'),
        };

        $currency = $account->currency ?: '-';
        $institution = $account->institution_name ?: '-';
        $bankProfile = $account->bankProfile?->profile_name ?: '-';

        $theme = match ($typeRaw) {
            \App\Models\TreasuryAccount::TYPE_BANK => [
                'page' => 'radial-gradient(circle at top right, rgba(59,130,246,.14), transparent 22%), radial-gradient(circle at bottom left, rgba(56,189,248,.12), transparent 22%)',
                'header' => 'linear-gradient(90deg, #eff6ff 0%, #ffffff 45%, #dbeafe 100%)',
                'glow' => 'rgba(59,130,246,.42)',
                'title' => '#1d4ed8',
                'meta' => '#4b6b92',
                'border' => '#93c5fd',
                'badgeBg' => '#eff6ff',
                'badgeText' => '#1d4ed8',
            ],
            \App\Models\TreasuryAccount::TYPE_CASH => [
                'page' => 'radial-gradient(circle at top right, rgba(16,185,129,.16), transparent 22%), radial-gradient(circle at bottom left, rgba(74,222,128,.12), transparent 22%)',
                'header' => 'linear-gradient(90deg, #ecfdf5 0%, #ffffff 40%, #bbf7d0 100%)',
                'glow' => 'rgba(16,185,129,.44)',
                'title' => '#047857',
                'meta' => '#3b7e69',
                'border' => '#34d399',
                'badgeBg' => '#ecfdf5',
                'badgeText' => '#047857',
            ],
            \App\Models\TreasuryAccount::TYPE_CLEARING => [
                'page' => 'radial-gradient(circle at top right, rgba(245,158,11,.16), transparent 22%), radial-gradient(circle at bottom left, rgba(250,204,21,.12), transparent 22%)',
                'header' => 'linear-gradient(90deg, #fff7ed 0%, #ffffff 40%, #fde68a 100%)',
                'glow' => 'rgba(245,158,11,.48)',
                'title' => '#b45309',
                'meta' => '#9a6b20',
                'border' => '#f59e0b',
                'badgeBg' => '#fff7ed',
                'badgeText' => '#c2410c',
            ],
            default => [
                'page' => 'radial-gradient(circle at top right, rgba(148,163,184,.13), transparent 22%), radial-gradient(circle at bottom left, rgba(203,213,225,.15), transparent 22%)',
                'header' => 'linear-gradient(90deg, #f8fafc 0%, #ffffff 45%, #e2e8f0 100%)',
                'glow' => 'rgba(148,163,184,.38)',
                'title' => '#475569',
                'meta' => '#64748b',
                'border' => '#cbd5e1',
                'badgeBg' => '#f8fafc',
                'badgeText' => '#475569',
            ],
        };

        $latestTransactions = \App\Models\TreasuryTransaction::query()
            ->where('treasury_account_id', $account->id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $incomingTotal = (float) \App\Models\TreasuryTransaction::query()
            ->where('treasury_account_id', $account->id)
            ->where('is_posted', true)
            ->where('direction', \App\Models\TreasuryTransaction::DIRECTION_IN)
            ->sum('amount');

        $outgoingTotal = (float) \App\Models\TreasuryTransaction::query()
            ->where('treasury_account_id', $account->id)
            ->where('is_posted', true)
            ->where('direction', \App\Models\TreasuryTransaction::DIRECTION_OUT)
            ->sum('amount');
    @endphp

    <style>
        .fi-page { gap: 1rem !important; }

        .fi-header {
            position: relative;
            overflow: hidden;
            border: 1px solid {{ $theme['border'] }} !important;
            border-radius: 32px !important;
            padding: 20px 24px 18px 24px !important;
            background: {{ $theme['header'] }} !important;
            box-shadow: 0 16px 38px rgba(15, 23, 42, .06) !important;
            margin-bottom: 4px !important;
        }

        .fi-header::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            right: -70px;
            top: -70px;
            border-radius: 999px;
            background: {{ $theme['glow'] }};
            filter: blur(42px);
            opacity: .95;
            pointer-events: none;
        }

        .fi-header > * { position: relative; z-index: 1; }

        .fi-header-heading {
            color: {{ $theme['title'] }} !important;
            font-size: 34px !important;
            line-height: 1.05 !important;
            font-weight: 900 !important;
            max-width: 720px !important;
            white-space: normal !important;
            overflow-wrap: anywhere !important;
        }

        .fi-header-subheading {
            color: {{ $theme['meta'] }} !important;
            font-size: 17px !important;
            line-height: 1.7 !important;
            font-weight: 500 !important;
            max-width: 100% !important;
            margin-top: 10px !important;
        }

        .sf-ta-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 6px;
            border-radius: 34px;
            background: {{ $theme['page'] }};
            transition: all .35s ease;
        }

        .sf-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .sf-kpi-card,
        .sf-section-card {
            background: #fff;
            border: 1px solid #dbe7ef;
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 6px 18px rgba(15,23,42,.04);
            transition: .2s;
        }

        .sf-kpi-card:hover,
        .sf-section-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(15,23,42,.08);
        }

        .sf-kpi-label,
        .sf-meta-label,
        .sf-table-head {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .sf-kpi-value {
            margin-top: 12px;
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.1;
        }

        .sf-kpi-sub {
            margin-top: 8px;
            font-size: 14px;
            color: #64748b;
        }

        .sf-main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .sf-pill {
            display: inline-block;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sf-pill--type {
            background: {{ $theme['badgeBg'] }};
            color: {{ $theme['badgeText'] }};
        }

        .sf-pill--banking {
            background: #f8fafc;
            color: #334155;
        }

        .sf-pill--tx {
            background: #eef2ff;
            color: #4338ca;
        }

        .sf-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .sf-detail-block,
        .sf-stack-item {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 18px;
            background: #fcfdff;
        }

        .sf-detail-value {
            margin-top: 8px;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.45;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .sf-detail-value--lg {
            font-size: 20px;
            font-weight: 900;
        }

        .sf-detail-value--notes {
            font-size: 15px;
            font-weight: 500;
            color: #475569;
            line-height: 1.8;
        }

        .sf-table-wrap {
            overflow: auto;
            margin-top: 20px;
        }

        .sf-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .sf-table th {
            text-align: left;
            padding: 10px 14px;
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .sf-table td {
            background: #f8fafc;
            padding: 14px;
            color: #0f172a;
        }

        .sf-table td:first-child {
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
            font-weight: 700;
        }

        .sf-table td:last-child {
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .sf-empty-row {
            padding: 18px !important;
            background: #f8fafc !important;
            border-radius: 16px !important;
            color: #64748b !important;
        }

        .dark .sf-ta-shell {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.03), transparent 18%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.05), transparent 20%),
                linear-gradient(180deg, rgba(7,20,39,.96) 0%, rgba(10,24,42,.96) 100%);
        }

        .dark .sf-kpi-card,
        .dark .sf-section-card {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.14);
            box-shadow: 0 8px 22px rgba(0,0,0,.22);
        }

        .dark .sf-detail-block,
        .dark .sf-stack-item {
            background: rgba(255,255,255,.02);
            border-color: rgba(76,167,168,.12);
        }

        .dark .sf-pill--banking {
            background: rgba(148,163,184,.12);
            color: #cbd5e1;
        }

        .dark .sf-pill--tx {
            background: rgba(67,56,202,.18);
            color: #c7d2fe;
        }

        .dark .sf-kpi-label,
        .dark .sf-meta-label,
        .dark .sf-table-head,
        .dark .sf-table th {
            color: #8ea8be;
        }

        .dark .sf-kpi-value,
        .dark .sf-detail-value,
        .dark .sf-table td {
            color: #f6fbff;
        }

        .dark .sf-kpi-sub,
        .dark .sf-detail-value--notes,
        .dark .sf-empty-row {
            color: #9fb2c3 !important;
        }

        .dark .sf-table td {
            background: rgba(255,255,255,.03);
        }

        @media (max-width: 1100px) {
            .sf-main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .fi-header-heading {
                font-size: 40px !important;
            }

            .fi-header {
                padding: 22px 20px 18px 20px !important;
            }
        }

        @media (max-width: 768px) {
            .sf-detail-grid {
                grid-template-columns: 1fr;
            }

            .sf-kpi-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="sf-ta-shell">
        <section class="sf-kpi-grid">
            <div class="sf-kpi-card">
                <div class="sf-kpi-label">Current Balance</div>
                <div class="sf-kpi-value">{{ number_format((float) ($account->current_balance ?? 0), 2) }}</div>
                <div class="sf-kpi-sub">{{ $currency }}</div>
            </div>

            <div class="sf-kpi-card">
                <div class="sf-kpi-label">Opening Balance</div>
                <div class="sf-kpi-value" style="font-size:30px;">{{ number_format((float) ($account->opening_balance ?? 0), 2) }}</div>
                <div class="sf-kpi-sub">{{ $currency }}</div>
            </div>

            <div class="sf-kpi-card">
                <div class="sf-kpi-label">Incoming Total</div>
                <div class="sf-kpi-value" style="font-size:30px;">{{ number_format($incomingTotal, 2) }}</div>
                <div class="sf-kpi-sub">Posted incoming movements</div>
            </div>

            <div class="sf-kpi-card">
                <div class="sf-kpi-label">Outgoing Total</div>
                <div class="sf-kpi-value" style="font-size:30px;">{{ number_format($outgoingTotal, 2) }}</div>
                <div class="sf-kpi-sub">Posted outgoing movements</div>
            </div>
        </section>

        <section class="sf-main-grid">
            <div class="sf-section-card">
                <div class="sf-pill sf-pill--type">Account Details</div>

                <div class="sf-detail-grid">
                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Account Name</div>
                        <div class="sf-detail-value sf-detail-value--lg">{{ $account->account_name ?: '-' }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Type</div>
                        <div class="sf-detail-value sf-detail-value--lg">{{ $typeLabel }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Currency</div>
                        <div class="sf-detail-value">{{ $currency }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Institution</div>
                        <div class="sf-detail-value">{{ $institution }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Default</div>
                        <div class="sf-detail-value">{{ $account->is_default ? 'Yes' : 'No' }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Active</div>
                        <div class="sf-detail-value">{{ $account->is_active ? 'Yes' : 'No' }}</div>
                    </div>

                    <div class="sf-detail-block" style="grid-column: 1 / -1;">
                        <div class="sf-meta-label">Notes</div>
                        <div class="sf-detail-value sf-detail-value--notes">{{ $account->notes ?: 'No notes added.' }}</div>
                    </div>
                </div>
            </div>

            <div class="sf-section-card">
                <div class="sf-pill sf-pill--banking">Banking & Links</div>

                <div class="sf-detail-grid">
                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Bank Profile</div>
                        <div class="sf-detail-value">{{ $bankProfile }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Account Holder</div>
                        <div class="sf-detail-value">{{ $account->account_holder_name ?: '-' }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Account Number</div>
                        <div class="sf-detail-value">{{ $account->account_number ?: '-' }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">IBAN</div>
                        <div class="sf-detail-value">{{ $account->iban ?: '-' }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Swift Code</div>
                        <div class="sf-detail-value">{{ $account->swift_code ?: '-' }}</div>
                    </div>

                    <div class="sf-detail-block">
                        <div class="sf-meta-label">Branch</div>
                        <div class="sf-detail-value">{{ $account->branch_name ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sf-section-card">
            <div class="sf-pill sf-pill--tx">Latest Transactions</div>

            <div class="sf-table-wrap">
                <table class="sf-table">
                    <thead>
                        <tr>
                            <th class="sf-table-head">Date</th>
                            <th class="sf-table-head">Transaction No.</th>
                            <th class="sf-table-head">Type</th>
                            <th class="sf-table-head">Direction</th>
                            <th class="sf-table-head">Amount</th>
                            <th class="sf-table-head">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestTransactions as $txn)
                            <tr>
                                <td>{{ $txn->transaction_date?->format('Y-m-d') ?: '-' }}</td>
                                <td>{{ $txn->transaction_no ?: '-' }}</td>
                                <td>{{ $txn->transaction_type ?: '-' }}</td>
                                <td>{{ ucfirst((string) ($txn->direction ?: '-')) }}</td>
                                <td style="font-weight:900;">{{ number_format((float) ($txn->amount ?? 0), 2) }} {{ $txn->currency ?: '' }}</td>
                                <td>{{ $txn->description ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="sf-empty-row">
                                    No transactions found for this account yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-filament-panels::page>
