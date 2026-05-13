<x-filament-widgets::widget>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400..700,0..1,-50..200" rel="stylesheet">
    <div class="sf-rec-ops-dashboard">
        <style>
            .sf-rec-ops-dashboard {
                display: grid;
                gap: 18px;
            }

            .sf-rec-ops-hero {
                border-radius: 34px;
                padding: 28px;
                color: #fff;
                background:
                    radial-gradient(circle at top right, rgba(45, 212, 191, .22), transparent 34%),
                    radial-gradient(circle at bottom left, rgba(37, 99, 235, .20), transparent 32%),
                    linear-gradient(135deg, #0f172a, #1f4664 62%, #0f766e);
                box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
                border: 1px solid rgba(255, 255, 255, .12);
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 18px;
                overflow: hidden;
                position: relative;
            }

            .sf-rec-ops-hero::after {
                content: "";
                position: absolute;
                width: 360px;
                height: 360px;
                right: -120px;
                top: -160px;
                border-radius: 999px;
                background: rgba(255, 255, 255, .10);
                filter: blur(2px);
            }

            .sf-rec-ops-brand-row {
                display: flex;
                align-items: center;
                gap: 14px;
                margin-bottom: 18px;
                position: relative;
                z-index: 1;
            }

            .sf-rec-ops-logo {
                width: 96px;
                height: 52px;
                object-fit: contain;
                border-radius: 18px;
                padding: 8px 10px;
                background: rgba(255, 255, 255, .92);
                border: 1px solid rgba(255, 255, 255, .28);
                box-shadow: 0 14px 34px rgba(15, 23, 42, .16);
            }

            .sf-rec-ops-mini {
                margin-top: 4px;
                color: rgba(226, 232, 240, .78);
                font-size: 12px;
                font-weight: 850;
                letter-spacing: .12em;
                text-transform: uppercase;
            }

            .sf-rec-ops-kicker {
                font-size: 12px;
                font-weight: 950;
                letter-spacing: .16em;
                text-transform: uppercase;
                color: #99f6e4;
                margin-bottom: 0;
            }

            .sf-rec-ops-title {
                margin: 0;
                font-size: clamp(38px, 4vw, 68px);
                line-height: .92;
                font-weight: 950;
                letter-spacing: -.07em;
                color: #fff;
            }

            .sf-rec-ops-subtitle {
                margin-top: 12px;
                max-width: 760px;
                color: rgba(226, 232, 240, .88);
                font-size: 14px;
                font-weight: 650;
                line-height: 1.6;
            }

            .sf-rec-ops-date {
                position: relative;
                z-index: 1;
                border-radius: 999px;
                padding: 12px 16px;
                background: rgba(255, 255, 255, .12);
                border: 1px solid rgba(255, 255, 255, .16);
                color: #fff;
                font-weight: 900;
                white-space: nowrap;
                backdrop-filter: blur(12px);
            }

            .sf-rec-ops-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 16px;
            }

            .sf-rec-ops-card {
                min-height: 178px;
                border-radius: 30px;
                padding: 20px;
                background:
                    radial-gradient(circle at top right, rgba(20, 184, 166, .11), transparent 34%),
                    rgba(255, 255, 255, .96);
                border: 1px solid rgba(15, 23, 42, .08);
                box-shadow: 0 18px 48px rgba(15, 23, 42, .07);
                text-decoration: none;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                overflow: hidden;
                position: relative;
            }

            .dark .sf-rec-ops-card {
                background:
                    radial-gradient(circle at top right, rgba(20, 184, 166, .13), transparent 34%),
                    rgba(15, 23, 42, .72);
                border-color: rgba(148, 163, 184, .18);
                box-shadow: 0 18px 48px rgba(0, 0, 0, .18);
            }

            .sf-rec-ops-card::before {
                content: "";
                display: block;
                height: 5px;
                border-radius: 999px;
                background: linear-gradient(90deg, var(--tone-a), var(--tone-b));
                margin-bottom: 16px;
            }

            .sf-rec-ops-card-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .sf-rec-ops-label {
                color: #234b74;
                font-size: 15px;
                font-weight: 950;
                letter-spacing: -.03em;
                line-height: 1.25;
            }

            .dark .sf-rec-ops-label {
                color: #fff;
            }

            .sf-rec-ops-icon {
                width: 42px;
                height: 42px;
                border-radius: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: color-mix(in srgb, var(--tone-a) 16%, #ffffff);
                font-size: 20px;
                flex: 0 0 auto;
            }

            .sf-rec-ops-icon .material-symbols-rounded {
                font-size: 26px;
                font-variation-settings:
                    'FILL' 0,
                    'wght' 650,
                    'GRAD' 0,
                    'opsz' 28;
                color: var(--tone-b);
                line-height: 1;
            }

            .dark .sf-rec-ops-icon {
                background: color-mix(in srgb, var(--tone-a) 22%, rgba(15, 23, 42, .8));
            }

            .sf-rec-ops-value {
                margin-top: 18px;
                color: #0f172a;
                font-size: clamp(36px, 4vw, 58px);
                line-height: .95;
                font-weight: 950;
                letter-spacing: -.07em;
            }

            .dark .sf-rec-ops-value {
                color: #fff;
            }

            .sf-rec-ops-caption {
                margin-top: 10px;
                color: #64748b;
                font-size: 12px;
                font-weight: 750;
                line-height: 1.45;
            }

            .dark .sf-rec-ops-caption {
                color: #94a3b8;
            }

            .sf-rec-ops-link {
                margin-top: 12px;
                color: var(--tone-b);
                font-size: 12px;
                font-weight: 950;
            }

            .sf-rec-ops-muted {
                opacity: .78;
            }

            .tone-blue { --tone-a: #38bdf8; --tone-b: #2563eb; }
            .tone-cyan { --tone-a: #22d3ee; --tone-b: #0891b2; }
            .tone-teal { --tone-a: #2dd4bf; --tone-b: #0f766e; }
            .tone-amber { --tone-a: #fbbf24; --tone-b: #d97706; }
            .tone-violet { --tone-a: #a78bfa; --tone-b: #7c3aed; }
            .tone-green { --tone-a: #4ade80; --tone-b: #16a34a; }
            .tone-indigo { --tone-a: #818cf8; --tone-b: #4f46e5; }
            .tone-orange { --tone-a: #fb923c; --tone-b: #ea580c; }

            @media (max-width: 1300px) {
                .sf-rec-ops-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 760px) {
                .sf-rec-ops-hero {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .sf-rec-ops-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <section class="sf-rec-ops-hero">
            <div>
                <div class="sf-rec-ops-brand-row"><img src="/images/sada-horizontal.png" alt="Sada Fezzan" class="sf-rec-ops-logo"><div><div class="sf-rec-ops-kicker">Sada Fezzan RFO Platform</div><div class="sf-rec-ops-mini">Recruitment • Finance • Operations</div></div></div>
                <h1 class="sf-rec-ops-title">Operations Dashboard</h1>
                <div class="sf-rec-ops-subtitle">
                    A focused command center for Sada Fezzan recruitment flow, pre-employment processing, active employees, rotations, and upcoming mobilizations.
                </div>
            </div>

            <div class="sf-rec-ops-date">
                {{ $todayLabel }}
            </div>
        </section>

        <section class="sf-rec-ops-grid">
            @foreach($stats as $stat)
                @php
                    $cardClass = 'sf-rec-ops-card tone-' . ($stat['tone'] ?? 'blue');
                @endphp

                @if(! empty($stat['url']))
                    <a class="{{ $cardClass }}" href="{{ $stat['url'] }}">
                        <div>
                            <div class="sf-rec-ops-card-top">
                                <div class="sf-rec-ops-label">{{ $stat['label'] }}</div>
                                <div class="sf-rec-ops-icon"><span class="material-symbols-rounded">{{ $stat['icon'] }}</span></div>
                            </div>

                            <div class="sf-rec-ops-value">{{ number_format((int) $stat['value']) }}</div>
                            <div class="sf-rec-ops-caption">{{ $stat['caption'] }}</div>
                        </div>

                        <div class="sf-rec-ops-link">Open section →</div>
                    </a>
                @else
                    <div class="{{ $cardClass }} sf-rec-ops-muted">
                        <div>
                            <div class="sf-rec-ops-card-top">
                                <div class="sf-rec-ops-label">{{ $stat['label'] }}</div>
                                <div class="sf-rec-ops-icon"><span class="material-symbols-rounded">{{ $stat['icon'] }}</span></div>
                            </div>

                            <div class="sf-rec-ops-value">{{ number_format((int) $stat['value']) }}</div>
                            <div class="sf-rec-ops-caption">{{ $stat['caption'] }}</div>
                        </div>

                        <div class="sf-rec-ops-caption">View only · no page access</div>
                    </div>
                @endif
            @endforeach
        </section>
    </div>
</x-filament-widgets::widget>
