<x-filament-panels::page>
    @php
        $transaction = $this->record;

        $direction = (string) ($transaction->direction ?? '');
        $isIncoming = $direction === 'in';
        $directionText = $isIncoming ? 'Incoming' : 'Outgoing';

        $statusText = $transaction->is_posted ? 'Posted' : 'Draft / Not Posted';
        $account = $transaction->treasuryAccount?->account_name ?: '-';
        $currency = $transaction->currency ?: '-';
        $amount = number_format((float) ($transaction->amount ?? 0), 2);
        $transactionNo = $transaction->transaction_no ?: ('Transaction #' . $transaction->id);

        $directionBadge = $isIncoming
            ? ['#ecfdf5', '#047857', '#86efac']
            : ['#fff1f2', '#be123c', '#fda4af'];
    @endphp

    <style>
        .fi-header,
        .fi-breadcrumbs,
        nav[aria-label="Breadcrumb"],
        .fi-page-header {
            display: none !important;
        }

        .sf-ttv-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-ttv-hero {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            padding: 26px 28px;
            background: linear-gradient(135deg, #18344d 0%, #234d6f 50%, #2f6f73 100%);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.10);
            position: relative;
            overflow: hidden;
        }

        .sf-ttv-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-ttv-left,
        .sf-ttv-actions {
            position: relative;
            z-index: 1;
        }

        .sf-ttv-kicker {
            font-size: 14px;
            color: rgba(255,255,255,.78);
            margin-bottom: 8px;
        }

        .sf-ttv-title {
            font-size: 50px;
            line-height: .95;
            font-weight: 950;
            color: #fff;
            letter-spacing: -.04em;
        }

        .sf-ttv-sub {
            margin-top: 16px;
            max-width: 900px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.84);
        }

        .sf-ttv-badges {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-ttv-badge {
            display: inline-flex;
            align-items: center;
            padding: 9px 14px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.12);
            color: #fff;
        }

        .sf-ttv-actions {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            flex-shrink: 0;
        }

        .sf-ttv-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 900;
            white-space: nowrap;
        }

        .sf-ttv-btn-secondary {
            background: rgba(255,255,255,.12);
            color: #fff !important;
            border: 1px solid rgba(255,255,255,.16);
        }

        .sf-ttv-btn-primary {
            background: #f2b705;
            color: #3b2a00 !important;
            box-shadow: 0 10px 20px rgba(242,183,5,.22);
        }

        .sf-ttv-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .sf-ttv-card,
        .sf-ttv-section {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%);
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }

        .sf-ttv-card {
            padding: 22px;
        }

        .sf-ttv-section {
            padding: 24px;
        }

        .sf-ttv-label {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #8ea0b5;
        }

        .sf-ttv-value {
            margin-top: 12px;
            font-size: 26px;
            line-height: 1.1;
            font-weight: 950;
            color: #0f172a;
            word-break: break-word;
        }

        .sf-ttv-small {
            margin-top: 8px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .sf-ttv-section-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .sf-ttv-mini {
            padding: 16px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .sf-ttv-pill {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            border: 1px solid transparent;
        }

        .dark .sf-ttv-card,
        .dark .sf-ttv-section {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.16);
        }

        .dark .sf-ttv-mini {
            background: rgba(255,255,255,.03);
            border-color: rgba(76,167,168,.12);
        }

        .dark .sf-ttv-value {
            color: #f6fbff;
        }

        @media (max-width: 1100px) {
            .sf-ttv-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .sf-ttv-section-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 800px) {
            .sf-ttv-hero { flex-direction: column; }
            .sf-ttv-title { font-size: 38px; }
            .sf-ttv-grid { grid-template-columns: 1fr; }
            .sf-ttv-actions { flex-wrap: wrap; width: 100%; }
        }
    </style>

    <div class="sf-ttv-wrap">
        <section class="sf-ttv-hero">
            <div class="sf-ttv-left">
                <div class="sf-ttv-kicker">Treasury Transactions › View</div>
                <div class="sf-ttv-title">{{ $transactionNo }}</div>
                <div class="sf-ttv-sub">
                    {{ $directionText }} treasury transaction for {{ $amount }} {{ $currency }} through {{ $account }}.
                </div>

                <div class="sf-ttv-badges">
                    <span class="sf-ttv-badge">{{ $directionText }}</span>
                    <span class="sf-ttv-badge">{{ $statusText }}</span>
                    <span class="sf-ttv-badge">{{ $currency }}</span>
                </div>
            </div>

            <div class="sf-ttv-actions">
                <a href="{{ \App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource::getUrl('index') }}" class="sf-ttv-btn sf-ttv-btn-secondary">
                    Back to Transactions
                </a>

                @if(auth()->user()?->canErp('treasury', 'edit'))
                    <a href="{{ \App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource::getUrl('edit', ['record' => $transaction]) }}" class="sf-ttv-btn sf-ttv-btn-primary">
                        Edit
                    </a>
                @endif
            </div>
        </section>

        <section class="sf-ttv-grid">
            <div class="sf-ttv-card">
                <div class="sf-ttv-label">Amount</div>
                <div class="sf-ttv-value">{{ $amount }}</div>
                <div class="sf-ttv-small">{{ $currency }}</div>
            </div>

            <div class="sf-ttv-card">
                <div class="sf-ttv-label">Direction</div>
                <div class="sf-ttv-value">{{ $directionText }}</div>
                <div class="sf-ttv-small">Account movement direction</div>
            </div>

            <div class="sf-ttv-card">
                <div class="sf-ttv-label">Account</div>
                <div class="sf-ttv-value">{{ $account }}</div>
                <div class="sf-ttv-small">Affected treasury account</div>
            </div>

            <div class="sf-ttv-card">
                <div class="sf-ttv-label">Status</div>
                <div class="sf-ttv-value">{{ $statusText }}</div>
                <div class="sf-ttv-small">Posting state</div>
            </div>
        </section>

        <section class="sf-ttv-section">
            <div class="sf-ttv-label">Transaction Details</div>

            <div class="sf-ttv-section-grid" style="margin-top: 18px;">
                <div class="sf-ttv-mini">
                    <div class="sf-ttv-label">Transaction Type</div>
                    <div class="sf-ttv-value" style="font-size: 22px;">{{ str_replace('_', ' ', ucwords((string) $transaction->transaction_type, '_')) ?: '-' }}</div>
                </div>

                <div class="sf-ttv-mini">
                    <div class="sf-ttv-label">Transaction Date</div>
                    <div class="sf-ttv-value" style="font-size: 22px;">{{ optional($transaction->transaction_date)->format('Y-m-d') ?: '-' }}</div>
                </div>

                <div class="sf-ttv-mini">
                    <div class="sf-ttv-label">Client</div>
                    <div class="sf-ttv-value" style="font-size: 22px;">{{ $transaction->client?->name ?: '-' }}</div>
                </div>

                <div class="sf-ttv-mini">
                    <div class="sf-ttv-label">Project</div>
                    <div class="sf-ttv-value" style="font-size: 22px;">{{ $transaction->project?->name ?: '-' }}</div>
                </div>

                <div class="sf-ttv-mini">
                    <div class="sf-ttv-label">Employment</div>
                    <div class="sf-ttv-value" style="font-size: 22px;">{{ $transaction->employment?->employee_name ?: '-' }}</div>
                </div>

                <div class="sf-ttv-mini">
                    <div class="sf-ttv-label">Reference</div>
                    <div class="sf-ttv-value" style="font-size: 22px;">{{ $transaction->reference_type ?: '-' }} #{{ $transaction->reference_id ?: '-' }}</div>
                </div>

                <div class="sf-ttv-mini" style="grid-column: 1 / -1;">
                    <div class="sf-ttv-label">Description</div>
                    <div class="sf-ttv-value" style="font-size: 20px;">{{ $transaction->description ?: '-' }}</div>
                </div>

                <div class="sf-ttv-mini" style="grid-column: 1 / -1;">
                    <div class="sf-ttv-label">Notes</div>
                    <div class="sf-ttv-value" style="font-size: 20px;">{{ $transaction->notes ?: 'No notes added.' }}</div>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
