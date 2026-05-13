<x-filament-panels::page>
    <style>
        .fi-header { display: none !important; }

        .sf-bp-wrap {
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

        .sf-bp-hero {
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

        .dark .sf-bp-hero {
            border-color: rgba(255,255,255,.10);
            box-shadow: 0 18px 34px rgba(0,0,0,.22);
        }

        .sf-bp-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-bp-hero-left,
        .sf-bp-hero-right {
            position: relative;
            z-index: 1;
        }

        .sf-bp-hero-right {
            flex-shrink: 0;
        }

        .sf-bp-hero-kicker {
            font-size: 14px;
            color: rgba(255,255,255,.78);
        }

        .sf-bp-hero-title {
            margin-top: 8px;
            font-size: 56px;
            line-height: .95;
            font-weight: 900;
            color: #fff;
        }

        .sf-bp-hero-sub {
            margin-top: 16px;
            max-width: 920px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.84);
        }

        .sf-bp-summary {
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

        .sf-bp-section {
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%);
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }

        .dark .sf-bp-section {
            background: linear-gradient(180deg, rgba(11,22,38,.96) 0%, rgba(10,27,45,.95) 100%);
            border-color: rgba(76,167,168,.14);
            box-shadow: 0 10px 24px rgba(0,0,0,.18);
        }

        .sf-bp-section-kicker {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #1f4664;
        }

        .dark .sf-bp-section-kicker {
            color: #7fcfd0;
        }

        .sf-bp-section-title {
            margin-top: 8px;
            font-size: 28px;
            line-height: 1.1;
            font-weight: 900;
            color: #0f172a;
        }

        .dark .sf-bp-section-title {
            color: #f6fbff;
        }

        .sf-bp-section-sub {
            margin-top: 8px;
            font-size: 15px;
            line-height: 1.7;
            color: #667085;
        }

        .dark .sf-bp-section-sub {
            color: #9fb2c3;
        }

        .sf-bp-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .sf-bp-card {
            display: block;
            text-decoration: none !important;
            color: inherit !important;
            background: rgba(255,255,255,.96);
            border: 1px solid #d7e2e5;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 8px 18px rgba(15,23,42,.04);
            transition: all .18s ease;
            position: relative;
            overflow: hidden;
        }

        .dark .sf-bp-card {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.14);
            box-shadow: 0 8px 18px rgba(0,0,0,.18);
        }

        .sf-bp-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px rgba(15,23,42,.08);
            border-color: #b9cbd1;
        }

        .dark .sf-bp-card:hover {
            border-color: rgba(76,167,168,.24);
            box-shadow: 0 16px 28px rgba(0,0,0,.24);
        }

        .sf-bp-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #1f4664, #4ca7a8);
        }

        .sf-bp-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .sf-bp-name {
            font-size: 24px;
            line-height: 1.15;
            font-weight: 900;
            color: #0f172a;
        }

        .dark .sf-bp-name {
            color: #f6fbff;
        }

        .sf-bp-profile {
            margin-top: 8px;
            font-size: 14px;
            color: #667085;
            line-height: 1.6;
        }

        .dark .sf-bp-profile {
            color: #9fb2c3;
        }

        .sf-bp-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .sf-bp-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sf-bp-badge--active {
            background: #eaf7f5;
            color: #1d7a7b;
        }

        .sf-bp-badge--inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        .sf-bp-badge--default {
            background: #fcf5e8;
            color: #a67718;
        }

        .dark .sf-bp-badge--active {
            background: rgba(76,167,168,.16);
            color: #8de0dd;
        }

        .dark .sf-bp-badge--inactive {
            background: rgba(255,255,255,.06);
            color: #c0cad5;
        }

        .dark .sf-bp-badge--default {
            background: rgba(184,147,50,.16);
            color: #f0cf79;
        }

        .sf-bp-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .sf-bp-meta {
            border: 1px solid #e4ecef;
            background: #fff;
            border-radius: 14px;
            padding: 12px;
        }

        .dark .sf-bp-meta {
            border-color: rgba(76,167,168,.12);
            background: rgba(255,255,255,.02);
        }

        .sf-bp-meta-label {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #607085;
        }

        .dark .sf-bp-meta-label {
            color: #8ea8be;
        }

        .sf-bp-meta-value {
            margin-top: 8px;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.45;
            word-break: break-word;
            overflow-wrap: anywhere;
            white-space: normal;
        }

        .dark .sf-bp-meta-value {
            color: #f6fbff;
        }

        .sf-bp-currencies {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-bp-currency {
            display: inline-flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 999px;
            background: #f4f8fb;
            border: 1px solid #dbe7ef;
            color: #1f4664;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dark .sf-bp-currency {
            background: rgba(255,255,255,.03);
            border-color: rgba(76,167,168,.14);
            color: #b8d8db;
        }

        .sf-bp-open {
            margin-top: 18px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #4ca7a8;
        }

        .sf-bp-empty {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px dashed #c8d3de;
            background: rgba(255,255,255,.85);
            color: #667085;
        }

        .dark .sf-bp-empty {
            border-color: rgba(76,167,168,.14);
            background: rgba(255,255,255,.02);
            color: #9fb2c3;
        }

        @media (max-width: 768px) {
            .sf-bp-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .sf-bp-hero-title {
                font-size: 42px;
            }

            .sf-bp-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="sf-bp-wrap">
        <section class="sf-bp-hero">
            <div class="sf-bp-hero-left">
                <div class="sf-bp-hero-kicker">Bank Profiles &nbsp; › &nbsp; List</div>
                <div class="sf-bp-hero-title">Bank Profiles</div>
                <div class="sf-bp-hero-sub">
                    Box-based banking profile overview with linked currencies and account structure. Open any bank profile to manage identifiers, linked accounts, and invoice defaults.
                </div>
                <div class="sf-bp-summary">{{ $profilesCount }} Profiles</div>
            </div>

            <div class="sf-bp-hero-right">
                <a href="{{ \App\Filament\Resources\BankProfiles\BankProfileResource::getUrl('create') }}" class="sf-btn-primary">
                    New Bank Profile
                </a>
            </div>
        </section>

        <section class="sf-bp-section">
            <div class="sf-bp-section-kicker">Institutional Banking</div>
            <div class="sf-bp-section-title">Bank Profile Overview</div>
            <div class="sf-bp-section-sub">Review all bank profiles, invoice defaults, and linked currency accounts from one page.</div>

            @if(($profiles->count() ?? 0) > 0)
                <div class="sf-bp-grid">
                    @foreach($profiles as $profile)
                        <a class="sf-bp-card" href="{{ \App\Filament\Resources\BankProfiles\BankProfileResource::getUrl('view', ['record' => $profile]) }}">
                            <div class="sf-bp-top">
                                <div>
                                    <div class="sf-bp-name">{{ $profile->bank_name ?: 'Bank Profile' }}</div>
                                    <div class="sf-bp-profile">
                                        {{ $profile->profile_name ?: '-' }}
                                        @if($profile->beneficiary_name)
                                            · {{ $profile->beneficiary_name }}
                                        @endif
                                    </div>
                                </div>

                                <div class="sf-bp-badges">
                                    <span class="sf-bp-badge {{ $profile->is_active ? 'sf-bp-badge--active' : 'sf-bp-badge--inactive' }}">
                                        {{ $profile->is_active ? 'Active' : 'Inactive' }}
                                    </span>

                                    @if($profile->is_default_for_invoices)
                                        <span class="sf-bp-badge sf-bp-badge--default">Default</span>
                                    @endif
                                </div>
                            </div>

                            <div class="sf-bp-meta-grid">
                                <div class="sf-bp-meta">
                                    <div class="sf-bp-meta-label">Beneficiary</div>
                                    <div class="sf-bp-meta-value">{{ $profile->beneficiary_name ?: '-' }}</div>
                                </div>

                                <div class="sf-bp-meta">
                                    <div class="sf-bp-meta-label">SWIFT Code</div>
                                    <div class="sf-bp-meta-value">{{ $profile->swift_code ?: '-' }}</div>
                                </div>

                                <div class="sf-bp-meta">
                                    <div class="sf-bp-meta-label">Branch</div>
                                    <div class="sf-bp-meta-value">{{ $profile->branch_name ?: '-' }}</div>
                                </div>

                                <div class="sf-bp-meta">
                                    <div class="sf-bp-meta-label">Linked Accounts</div>
                                    <div class="sf-bp-meta-value">{{ $profile->accounts->count() }}</div>
                                </div>
                            </div>

                            <div class="sf-bp-currencies">
                                @forelse($profile->accounts as $account)
                                    <span class="sf-bp-currency">{{ $account->currency ?: '-' }}</span>
                                @empty
                                    <span class="sf-bp-currency">No Accounts</span>
                                @endforelse
                            </div>

                            <div class="sf-bp-open">Open Profile ↗</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sf-bp-empty">No bank profiles found.</div>
            @endif
        </section>
    </div>
</x-filament-panels::page>
