<x-filament-panels::page>
    <style>
        .fi-header,
        .fi-breadcrumbs,
        nav[aria-label="Breadcrumb"],
        .fi-page-header {
            display: none !important;
        }

        .sf-ttl-wrap { display: flex; flex-direction: column; gap: 24px; }

        .sf-ttl-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            padding: 26px 28px;
            background: linear-gradient(135deg, #18344d 0%, #234d6f 50%, #2f6f73 100%);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.10);
            position: relative;
            overflow: hidden;
        }

        .sf-ttl-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-ttl-left,
        .sf-ttl-right { position: relative; z-index: 1; }

        .sf-ttl-kicker {
            font-size: 14px;
            color: rgba(255,255,255,.78);
            margin-bottom: 8px;
        }

        .sf-ttl-title {
            font-size: 56px;
            line-height: .95;
            font-weight: 950;
            color: #fff;
            letter-spacing: -.04em;
        }

        .sf-ttl-sub {
            margin-top: 16px;
            max-width: 900px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.84);
        }

        .sf-ttl-badges {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-ttl-badge {
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

        .sf-ttl-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 999px;
            background: #f2b705;
            color: #3b2a00 !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(242,183,5,.22);
            transition: all .18s ease;
            white-space: nowrap;
        }

        .sf-ttl-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px rgba(242,183,5,.26);
        }

        .sf-ttl-table-card {
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            background: rgba(255,255,255,.82);
            box-shadow: 0 14px 30px rgba(15,23,42,.06);
            overflow: hidden;
        }

        .sf-ttl-table-card .fi-ta {
            border-radius: 0 !important;
            box-shadow: none !important;
            border: 0 !important;
        }

        .dark .sf-ttl-table-card {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.16);
        }

        @media (max-width: 900px) {
            .sf-ttl-hero { flex-direction: column; }
            .sf-ttl-title { font-size: 40px; }
            .sf-ttl-right, .sf-ttl-btn { width: 100%; }
        }
    </style>

    <div class="sf-ttl-wrap">
        <section class="sf-ttl-hero">
            <div class="sf-ttl-left">
                <div class="sf-ttl-kicker">Treasury Transactions › List</div>
                <div class="sf-ttl-title">Treasury Transactions</div>
                <div class="sf-ttl-sub">
                    Review all incoming and outgoing treasury movements by account, currency, client, project, and reference source.
                </div>

                <div class="sf-ttl-badges">
                    <span class="sf-ttl-badge">{{ $transactionsCount }} Total Transactions</span>
                    <span class="sf-ttl-badge">{{ $incomingCount }} Incoming</span>
                    <span class="sf-ttl-badge">{{ $outgoingCount }} Outgoing</span>
                    <span class="sf-ttl-badge">{{ $postedCount }} Posted</span>
                </div>
            </div>

            <div class="sf-ttl-right">
                @if(auth()->user()?->canErp('treasury', 'create'))
                    <a href="{{ \App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource::getUrl('create') }}" class="sf-ttl-btn">
                        New treasury transaction
                    </a>
                @endif
            </div>
        </section>

        <section class="sf-ttl-table-card">
            {{ $this->table }}
        </section>
    </div>
</x-filament-panels::page>
