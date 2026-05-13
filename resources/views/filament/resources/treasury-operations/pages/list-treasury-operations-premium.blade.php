<x-filament-panels::page>
    <style>
        .fi-header,
        .fi-breadcrumbs,
        nav[aria-label="Breadcrumb"],
        .fi-page-header {
            display: none !important;
        }

        .sf-top-list-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-top-hero {
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

        .sf-top-hero::after {
            content: "";
            position: absolute;
            inset: auto 0 0 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, #b38b2f);
        }

        .sf-top-hero-left,
        .sf-top-hero-right {
            position: relative;
            z-index: 1;
        }

        .sf-top-kicker {
            font-size: 14px;
            color: rgba(255,255,255,.78);
            margin-bottom: 8px;
        }

        .sf-top-title {
            font-size: 56px;
            line-height: .95;
            font-weight: 950;
            color: #fff;
            letter-spacing: -.04em;
        }

        .sf-top-subtitle {
            margin-top: 16px;
            max-width: 900px;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255,255,255,.84);
        }

        .sf-top-badges {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-top-badge {
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

        .sf-top-hero-right {
            flex-shrink: 0;
        }

        .sf-top-btn-primary {
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

        .sf-top-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 24px rgba(242,183,5,.26);
        }

        .sf-top-table-card {
            border: 1px solid #d7e2e5;
            border-radius: 22px;
            background: rgba(255,255,255,.82);
            box-shadow: 0 14px 30px rgba(15,23,42,.06);
            overflow: hidden;
        }

        .sf-top-table-card .fi-ta {
            border-radius: 0 !important;
            box-shadow: none !important;
            border: 0 !important;
        }

        .dark .sf-top-hero {
            border-color: rgba(255,255,255,.10);
            box-shadow: 0 18px 34px rgba(0,0,0,.22);
        }

        .dark .sf-top-table-card {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.16);
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }

        @media (max-width: 900px) {
            .sf-top-hero {
                flex-direction: column;
            }

            .sf-top-title {
                font-size: 40px;
            }

            .sf-top-hero-right {
                width: 100%;
            }

            .sf-top-btn-primary {
                width: 100%;
            }
        }
    </style>

    <div class="sf-top-list-wrap">
        <section class="sf-top-hero">
            <div class="sf-top-hero-left">
                <div class="sf-top-kicker">Treasury Operations › List</div>
                <div class="sf-top-title">Treasury Operations</div>
                <div class="sf-top-subtitle">
                    Review treasury movements between bank, cash, and clearing accounts. Open any operation to review its full account flow and posting details.
                </div>

                <div class="sf-top-badges">
                    <span class="sf-top-badge">{{ $operationsCount }} Total Operations</span>
                    <span class="sf-top-badge">{{ $postedCount }} Posted</span>
                    <span class="sf-top-badge">{{ $draftCount }} Draft / Not Posted</span>
                </div>
            </div>

            <div class="sf-top-hero-right">
                @if(auth()->user()?->canErp('treasury', 'create'))
                    <a href="{{ \App\Filament\Resources\TreasuryOperations\TreasuryOperationResource::getUrl('create') }}" class="sf-top-btn-primary">
                        New treasury operation
                    </a>
                @endif
            </div>
        </section>

        <section class="sf-top-table-card">
            {{ $this->table }}
        </section>
    </div>
</x-filament-panels::page>
