<x-filament-panels::page>
    <style>
        .fi-header { display: none !important; }

        .sf-ta-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-btn-primary {
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
        }

        .sf-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px rgba(242,183,5,.26);
        }

        .sf-ta-hero {
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

        .dark .sf-ta-hero {
            border-color: rgba(255,255,255,.10);
            box-shadow: 0 18px 34px rgba(0,0,0,.22);
        }

        .sf-ta-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-ta-hero-left,
        .sf-ta-hero-right {
            position: relative;
            z-index: 1;
        }

        .sf-ta-hero-right {
            flex-shrink: 0;
        }

        .sf-ta-hero-kicker {
            font-size: 14px;
            color: rgba(255,255,255,.78);
        }

        .sf-ta-hero-title {
            margin-top: 8px;
            font-size: 56px;
            line-height: .95;
            font-weight: 900;
            color: #fff;
        }

        .sf-ta-hero-sub {
            margin-top: 16px;
            max-width: 920px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.84);
        }

        .sf-ta-summary {
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.12);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .sf-ta-section {
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }

        .sf-ta-section--bank { background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%); }
        .sf-ta-section--cash { background: linear-gradient(180deg, #ffffff 0%, #f4faf8 100%); }
        .sf-ta-section--clearing { background: linear-gradient(180deg, #ffffff 0%, #fbf8f2 100%); }

        .dark .sf-ta-section {
            border-color: rgba(76,167,168,.14);
            box-shadow: 0 10px 24px rgba(0,0,0,.18);
        }

        .dark .sf-ta-section--bank,
        .dark .sf-ta-section--cash,
        .dark .sf-ta-section--clearing {
            background: linear-gradient(180deg, rgba(11,22,38,.96) 0%, rgba(10,27,45,.95) 100%);
        }

        .sf-ta-section-kicker {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .sf-ta-section--bank .sf-ta-section-kicker { color: #1f4664; }
        .sf-ta-section--cash .sf-ta-section-kicker { color: #4ca7a8; }
        .sf-ta-section--clearing .sf-ta-section-kicker { color: #b89332; }

        .dark .sf-ta-section--bank .sf-ta-section-kicker,
        .dark .sf-ta-section--cash .sf-ta-section-kicker,
        .dark .sf-ta-section--clearing .sf-ta-section-kicker { color: #7fcfd0; }

        .sf-ta-section-title {
            margin-top: 8px;
            font-size: 28px;
            line-height: 1.1;
            font-weight: 900;
            color: #0f172a;
        }

        .dark .sf-ta-section-title { color: #f6fbff; }

        .sf-ta-section-sub {
            margin-top: 8px;
            font-size: 15px;
            line-height: 1.7;
            color: #667085;
        }

        .dark .sf-ta-section-sub { color: #9fb2c3; }

        .sf-ta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .sf-ta-card {
            display: block;
            text-decoration: none !important;
            color: inherit !important;
            border-radius: 18px;
            padding: 18px;
            transition: all .18s ease;
            position: relative;
            overflow: hidden;
        }

        .sf-ta-card:hover { transform: translateY(-2px); }

        .sf-ta-card--bank,
        .sf-ta-card--cash,
        .sf-ta-card--clearing {
            background: rgba(255,255,255,.96);
            border: 1px solid #d7e2e5;
            box-shadow: 0 8px 18px rgba(15,23,42,.04);
        }

        .dark .sf-ta-card--bank,
        .dark .sf-ta-card--cash,
        .dark .sf-ta-card--clearing {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.14);
            box-shadow: 0 8px 18px rgba(0,0,0,.18);
        }

        .sf-ta-card:hover {
            box-shadow: 0 14px 24px rgba(15,23,42,.08);
            border-color: #b9cbd1;
        }

        .dark .sf-ta-card:hover {
            border-color: rgba(76,167,168,.24);
            box-shadow: 0 16px 28px rgba(0,0,0,.24);
        }

        .sf-ta-card--bank::before,
        .sf-ta-card--cash::before,
        .sf-ta-card--clearing::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .sf-ta-card--bank::before { background: linear-gradient(180deg, #1f4664, #2b5c7e); }
        .sf-ta-card--cash::before { background: linear-gradient(180deg, #4ca7a8, #2f8e8f); }
        .sf-ta-card--clearing::before { background: linear-gradient(180deg, #b89332, #d0a13a); }

        .sf-ta-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
        }

        .sf-ta-type {
            display: inline-flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sf-ta-type--bank { background: #eaf2f8; color: #1f4664; }
        .sf-ta-type--cash { background: #eaf7f5; color: #1d7a7b; }
        .sf-ta-type--clearing { background: #fcf5e8; color: #a67718; }

        .dark .sf-ta-type--bank { background: rgba(31,70,100,.22); color: #b6d2ea; }
        .dark .sf-ta-type--cash { background: rgba(76,167,168,.16); color: #99e0dc; }
        .dark .sf-ta-type--clearing { background: rgba(184,147,50,.16); color: #f0cf79; }

        .sf-ta-currency {
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #667085;
        }

        .dark .sf-ta-currency { color: #9fb2c3; }

        .sf-ta-name {
            margin-top: 14px;
            font-size: 22px;
            line-height: 1.2;
            font-weight: 900;
            color: #0f172a;
        }

        .dark .sf-ta-name { color: #f6fbff; }

        .sf-ta-institution {
            margin-top: 8px;
            font-size: 14px;
            line-height: 1.6;
            color: #667085;
        }

        .dark .sf-ta-institution { color: #9fb2c3; }

        .sf-ta-balance {
            margin-top: 18px;
            font-size: 34px;
            line-height: 1;
            font-weight: 900;
            color: #0f172a;
        }

        .dark .sf-ta-balance { color: #f6fbff; }

        .sf-ta-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .sf-ta-meta {
            border: 1px solid #e4ecef;
            background: #fff;
            border-radius: 14px;
            padding: 12px;
        }

        .dark .sf-ta-meta {
            border-color: rgba(76,167,168,.12);
            background: rgba(255,255,255,.02);
        }

        .sf-ta-meta-label {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #607085;
        }

        .dark .sf-ta-meta-label { color: #8ea8be; }

        .sf-ta-meta-value {
            margin-top: 8px;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.4;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
        }

        .dark .sf-ta-meta-value { color: #f6fbff; }

        .sf-ta-meta-value--iban {
            font-size: 12px;
            line-height: 1.55;
            letter-spacing: .02em;
        }

        .sf-ta-open {
            margin-top: 16px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #4ca7a8;
        }

        .sf-ta-empty {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px dashed #c8d3de;
            background: rgba(255,255,255,.85);
            color: #667085;
        }

        .dark .sf-ta-empty {
            border-color: rgba(76,167,168,.14);
            background: rgba(255,255,255,.02);
            color: #9fb2c3;
        }

        @media (max-width: 768px) {
            .sf-ta-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .sf-ta-hero-title {
                font-size: 42px;
            }

            .sf-ta-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="sf-ta-wrap">
        <section class="sf-ta-hero">
            <div class="sf-ta-hero-left">
                <div class="sf-ta-hero-kicker">Treasury Accounts &nbsp; › &nbsp; List</div>
                <div class="sf-ta-hero-title">Treasury Accounts</div>
                <div class="sf-ta-hero-sub">
                    Box-based treasury account overview grouped by account type. Open any account to review balance position, bank information, and latest transaction details.
                </div>
                <div class="sf-ta-summary">{{ $allAccountsCount }} Active Accounts</div>
            </div>

            <div class="sf-ta-hero-right">
                <a href="{{ \App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource::getUrl('create') }}" class="sf-btn-primary">
                    New treasury account
                </a>
            </div>
        </section>

        <section class="sf-ta-section sf-ta-section--bank">
            <div class="sf-ta-section-kicker">Bank Accounts</div>
            <div class="sf-ta-section-title">Institutional Treasury Accounts</div>
            <div class="sf-ta-section-sub">Linked treasury accounts for bank profiles and institutional currency holdings.</div>

            @if(($bankAccounts->count() ?? 0) > 0)
                <div class="sf-ta-grid">
                    @foreach($bankAccounts as $account)
                        <a class="sf-ta-card sf-ta-card--bank" href="{{ \App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource::getUrl('view', ['record' => $account]) }}">
                            <div class="sf-ta-top">
                                <span class="sf-ta-type sf-ta-type--bank">Bank</span>
                                <span class="sf-ta-currency">{{ $account->currency ?: '-' }}</span>
                            </div>

                            <div class="sf-ta-name">{{ $account->account_name ?: 'Treasury Account' }}</div>
                            <div class="sf-ta-institution">{{ $account->institution_name ?: 'No Institution' }}</div>
                            <div class="sf-ta-balance">{{ number_format((float) ($account->current_balance ?? 0), 2) }}</div>

                            <div class="sf-ta-meta-grid">
                                <div class="sf-ta-meta">
                                    <div class="sf-ta-meta-label">Opening Balance</div>
                                    <div class="sf-ta-meta-value">{{ number_format((float) ($account->opening_balance ?? 0), 2) }}</div>
                                </div>
                                <div class="sf-ta-meta">
                                    <div class="sf-ta-meta-label">IBAN</div>
                                    <div class="sf-ta-meta-value sf-ta-meta-value--iban">{{ $account->iban ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="sf-ta-open">Open Account ↗</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sf-ta-empty">No active bank treasury accounts found.</div>
            @endif
        </section>

        <section class="sf-ta-section sf-ta-section--cash">
            <div class="sf-ta-section-kicker">Main Cash</div>
            <div class="sf-ta-section-title">Cash Treasury Accounts</div>
            <div class="sf-ta-section-sub">Company cash accounts by currency with direct access to detailed account view.</div>

            @if(($cashAccounts->count() ?? 0) > 0)
                <div class="sf-ta-grid">
                    @foreach($cashAccounts as $account)
                        <a class="sf-ta-card sf-ta-card--cash" href="{{ \App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource::getUrl('view', ['record' => $account]) }}">
                            <div class="sf-ta-top">
                                <span class="sf-ta-type sf-ta-type--cash">Cash</span>
                                <span class="sf-ta-currency">{{ $account->currency ?: '-' }}</span>
                            </div>

                            <div class="sf-ta-name">{{ $account->account_name ?: 'Cash Account' }}</div>
                            <div class="sf-ta-institution">{{ $account->institution_name ?: 'Company Cash' }}</div>
                            <div class="sf-ta-balance">{{ number_format((float) ($account->current_balance ?? 0), 2) }}</div>

                            <div class="sf-ta-meta-grid">
                                <div class="sf-ta-meta">
                                    <div class="sf-ta-meta-label">Opening Balance</div>
                                    <div class="sf-ta-meta-value">{{ number_format((float) ($account->opening_balance ?? 0), 2) }}</div>
                                </div>
                                <div class="sf-ta-meta">
                                    <div class="sf-ta-meta-label">Default</div>
                                    <div class="sf-ta-meta-value">{{ $account->is_default ? 'Yes' : 'No' }}</div>
                                </div>
                            </div>

                            <div class="sf-ta-open">Open Account ↗</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sf-ta-empty">No active cash treasury accounts found.</div>
            @endif
        </section>

        <section class="sf-ta-section sf-ta-section--clearing">
            <div class="sf-ta-section-kicker">Clearing</div>
            <div class="sf-ta-section-title">Clearing Treasury Accounts</div>
            <div class="sf-ta-section-sub">Temporary and staged settlement accounts used for clearing and monitoring flows.</div>

            @if(($clearingAccounts->count() ?? 0) > 0)
                <div class="sf-ta-grid">
                    @foreach($clearingAccounts as $account)
                        <a class="sf-ta-card sf-ta-card--clearing" href="{{ \App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource::getUrl('view', ['record' => $account]) }}">
                            <div class="sf-ta-top">
                                <span class="sf-ta-type sf-ta-type--clearing">Clearing</span>
                                <span class="sf-ta-currency">{{ $account->currency ?: '-' }}</span>
                            </div>

                            <div class="sf-ta-name">{{ $account->account_name ?: 'Clearing Account' }}</div>
                            <div class="sf-ta-institution">{{ $account->institution_name ?: 'Treasury Clearing' }}</div>
                            <div class="sf-ta-balance">{{ number_format((float) ($account->current_balance ?? 0), 2) }}</div>

                            <div class="sf-ta-meta-grid">
                                <div class="sf-ta-meta">
                                    <div class="sf-ta-meta-label">Opening Balance</div>
                                    <div class="sf-ta-meta-value">{{ number_format((float) ($account->opening_balance ?? 0), 2) }}</div>
                                </div>
                                <div class="sf-ta-meta">
                                    <div class="sf-ta-meta-label">Default</div>
                                    <div class="sf-ta-meta-value">{{ $account->is_default ? 'Yes' : 'No' }}</div>
                                </div>
                            </div>

                            <div class="sf-ta-open">Open Account ↗</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sf-ta-empty">No active clearing treasury accounts found.</div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
