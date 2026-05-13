<x-filament-panels::page>
    <style>
        .fi-header { display: none !important; }

        .sf-bpv-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-bpv-page-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            padding: 24px 28px;
            border-radius: 22px;
            border: 1px solid #d7e2e5;
            background: linear-gradient(90deg, #f8fbfd 0%, #ffffff 55%, #eef6f6 100%);
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }

        .dark .sf-bpv-page-head {
            border-color: rgba(76,167,168,.16);
            background: linear-gradient(90deg, rgba(10,18,32,.96) 0%, rgba(8,25,44,.96) 55%, rgba(16,60,64,.92) 100%);
        }

        .sf-bpv-page-kicker {
            font-size: 14px;
            color: #667085;
        }

        .dark .sf-bpv-page-kicker {
            color: #9fb2c3;
        }

        .sf-bpv-page-title {
            margin-top: 8px;
            font-size: 52px;
            line-height: .95;
            font-weight: 900;
            color: #2f5b87;
        }

        .dark .sf-bpv-page-title {
            color: #f8fbff;
        }

        .sf-bpv-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sf-btn-primary,
        .sf-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 18px;
            border-radius: 999px;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 900;
            transition: all .18s ease;
        }

        .sf-btn-primary {
            background: #f2b705;
            color: #3b2a00 !important;
            box-shadow: 0 10px 20px rgba(242,183,5,.22);
        }

        .sf-btn-secondary {
            background: #eef4f7;
            color: #1f4664 !important;
            border: 1px solid #d7e2e5;
        }

        .dark .sf-btn-secondary {
            background: rgba(255,255,255,.06);
            color: #e8f2f4 !important;
            border-color: rgba(76,167,168,.14);
        }

        .sf-bpv-hero {
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

        .sf-bpv-hero-left,
        .sf-bpv-hero-actions {
            position: relative;
            z-index: 1;
        }

        .sf-bpv-hero-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
            align-items: flex-start;
        }

        .sf-bpv-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-bpv-kicker {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255,255,255,.72);
        }

        .sf-bpv-title {
            margin-top: 10px;
            font-size: 38px;
            line-height: 1;
            font-weight: 900;
            color: #fff;
        }

        .sf-bpv-sub {
            margin-top: 12px;
            max-width: 920px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.84);
        }

        .sf-bpv-badges {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-bpv-badge {
            display: inline-flex;
            align-items: center;
            padding: 9px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,.14);
            background: rgba(255,255,255,.10);
            color: #fff;
        }

        .sf-bpv-section {
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%);
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }

        .dark .sf-bpv-section {
            background: linear-gradient(180deg, rgba(11,22,38,.96) 0%, rgba(10,27,45,.95) 100%);
            border-color: rgba(76,167,168,.14);
        }

        .sf-bpv-section-kicker {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #1f4664;
        }

        .dark .sf-bpv-section-kicker {
            color: #7fcfd0;
        }

        .sf-bpv-section-title {
            margin-top: 8px;
            font-size: 28px;
            line-height: 1.1;
            font-weight: 900;
            color: #0f172a;
        }

        .dark .sf-bpv-section-title {
            color: #f6fbff;
        }

        .sf-bpv-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 20px;
        }

        .sf-bpv-card {
            border: 1px solid #e4ecef;
            background: #fff;
            border-radius: 16px;
            padding: 14px;
        }

        .dark .sf-bpv-card {
            border-color: rgba(76,167,168,.12);
            background: rgba(255,255,255,.02);
        }

        .sf-bpv-label {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #607085;
        }

        .sf-bpv-value {
            margin-top: 8px;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.5;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .dark .sf-bpv-value {
            color: #f6fbff;
        }

        .sf-bpv-currency-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .sf-bpv-currency-card {
            display: block;
            text-decoration: none !important;
            color: inherit !important;
            border: 1px solid #d7e2e5;
            background: #fff;
            border-radius: 18px;
            padding: 16px;
        }

        .dark .sf-bpv-currency-card {
            border-color: rgba(76,167,168,.14);
            background: rgba(255,255,255,.02);
        }

        .sf-bpv-open {
            margin-top: 12px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #4ca7a8;
        }

        @media (max-width: 768px) {
            .sf-bpv-grid {
                grid-template-columns: 1fr;
            }

            .sf-bpv-page-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .sf-bpv-page-title {
                font-size: 40px;
            }
        }
    </style>

    <div class="sf-bpv-wrap">
        <section class="sf-bpv-hero">
            <div class="sf-bpv-hero-left">
                <div class="sf-bpv-kicker">Bank Profiles › View</div>
                <div class="sf-bpv-title">{{ $profile->profile_name ?: ($profile->bank_name ?: 'Bank Profile') }}</div>
                <div class="sf-bpv-sub">
                    Review banking identifiers, invoice defaults, and linked treasury currency accounts before making any edits.
                </div>

                <div class="sf-bpv-badges">
                    <span class="sf-bpv-badge">{{ $profile->is_active ? 'Active' : 'Inactive' }}</span>
                    @if($profile->is_default_for_invoices)
                        <span class="sf-bpv-badge">Default for Invoices</span>
                    @endif
                </div>
            </div>

            <div class="sf-bpv-hero-actions">
                <a href="{{ \App\Filament\Resources\BankProfiles\BankProfileResource::getUrl('index') }}" class="sf-btn-secondary">Back</a>
                <a href="{{ \App\Filament\Resources\BankProfiles\BankProfileResource::getUrl('edit', ['record' => $profile]) }}" class="sf-btn-primary">Edit Profile</a>
            </div>
        </section>

        <section class="sf-bpv-section">
            <div class="sf-bpv-section-kicker">Profile Details</div>
            <div class="sf-bpv-section-title">Banking Identifiers & Structure</div>

            <div class="sf-bpv-grid">
                <div class="sf-bpv-card">
                    <div class="sf-bpv-label">Bank Name</div>
                    <div class="sf-bpv-value">{{ $profile->bank_name ?: '-' }}</div>
                </div>

                <div class="sf-bpv-card">
                    <div class="sf-bpv-label">Profile Name</div>
                    <div class="sf-bpv-value">{{ $profile->profile_name ?: '-' }}</div>
                </div>

                <div class="sf-bpv-card">
                    <div class="sf-bpv-label">Beneficiary</div>
                    <div class="sf-bpv-value">{{ $profile->beneficiary_name ?: '-' }}</div>
                </div>

                <div class="sf-bpv-card">
                    <div class="sf-bpv-label">Branch</div>
                    <div class="sf-bpv-value">{{ $profile->branch_name ?: '-' }}</div>
                </div>

                <div class="sf-bpv-card">
                    <div class="sf-bpv-label">SWIFT Code</div>
                    <div class="sf-bpv-value">{{ $profile->swift_code ?: '-' }}</div>
                </div>

                <div class="sf-bpv-card">
                    <div class="sf-bpv-label">Routing Code</div>
                    <div class="sf-bpv-value">{{ $profile->routing_code ?: '-' }}</div>
                </div>

                <div class="sf-bpv-card" style="grid-column: 1 / -1;">
                    <div class="sf-bpv-label">Bank Address</div>
                    <div class="sf-bpv-value">{{ $profile->bank_address ?: '-' }}</div>
                </div>
            </div>
        </section>

        <section class="sf-bpv-section">
            <div class="sf-bpv-section-kicker">Linked Currency Accounts</div>
            <div class="sf-bpv-section-title">Treasury Accounts</div>

            <div class="sf-bpv-currency-grid">
                @forelse($accounts as $account)
                    <a class="sf-bpv-currency-card" href="{{ $account->treasury_account_id ? \App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource::getUrl('view', ['record' => $account->treasury_account_id]) : '#' }}">
                        <div class="sf-bpv-label">Currency</div>
                        <div class="sf-bpv-value">{{ $account->currency ?: '-' }}</div>

                        <div class="sf-bpv-label" style="margin-top: 12px;">Account Number</div>
                        <div class="sf-bpv-value">{{ $account->account_number ?: '-' }}</div>

                        <div class="sf-bpv-label" style="margin-top: 12px;">IBAN</div>
                        <div class="sf-bpv-value">{{ $account->iban ?: '-' }}</div>

                        @if($account->treasuryAccount)
                            <div class="sf-bpv-open">Open Treasury Account ↗</div>
                        @endif
                    </a>
                @empty
                    <div class="sf-bpv-card">
                        <div class="sf-bpv-value">No linked currency accounts found.</div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-filament-panels::page>
