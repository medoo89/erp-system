<div class="ci-stats-shell">
    <style>
        .ci-stats-shell {
            width: 100%;
        }

        .ci-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .ci-stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 22px 24px;
            border: 1px solid var(--ci-stat-border);
            background: var(--ci-stat-bg);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
        }

        .ci-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
        }

        .ci-stat-card::before {
            content: "";
            position: absolute;
            inset-inline: 0;
            top: 0;
            height: 4px;
            background: var(--ci-stat-accent);
        }

        .ci-stat-title {
            font-size: 14px;
            line-height: 1.2;
            font-weight: 800;
            color: var(--ci-stat-title);
            margin-bottom: 18px;
        }

        .ci-stat-value {
            font-size: 58px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -.04em;
            color: var(--ci-stat-value);
        }

        .ci-stat-note {
            margin-top: 18px;
            font-size: 15px;
            line-height: 1.6;
            color: var(--ci-stat-note);
        }

        .ci-stat-card--slate {
            --ci-stat-accent: #94a3b8;
        }

        .ci-stat-card--blue {
            --ci-stat-accent: #60a5fa;
        }

        .ci-stat-card--amber {
            --ci-stat-accent: #f59e0b;
        }

        .ci-stat-card--purple {
            --ci-stat-accent: #a855f7;
        }

        .ci-stat-card--green {
            --ci-stat-accent: #22c55e;
        }

        .ci-stat-card--teal {
            --ci-stat-accent: #14b8a6;
        }

        html:not(.dark) .ci-stat-card {
            --ci-stat-bg: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            --ci-stat-border: #dbe7ef;
            --ci-stat-title: #1f2937;
            --ci-stat-value: #0f172a;
            --ci-stat-note: #64748b;
        }

        .dark .ci-stat-card {
            --ci-stat-bg:
                radial-gradient(circle at top right, rgba(255,255,255,.03), transparent 22%),
                linear-gradient(180deg, rgba(12,23,38,.98) 0%, rgba(10,21,36,.98) 100%);
            --ci-stat-border: rgba(76,167,168,.18);
            --ci-stat-title: #f8fafc;
            --ci-stat-value: #ffffff;
            --ci-stat-note: #9fb0c3;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.24);
        }

        @media (max-width: 1100px) {
            .ci-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .ci-stats-grid {
                grid-template-columns: 1fr;
            }

            .ci-stat-value {
                font-size: 44px;
            }
        }
    </style>

    <div class="ci-stats-grid">
        @foreach($cards as $card)
            <div class="ci-stat-card ci-stat-card--{{ $card['accent'] ?? 'slate' }}">
                <div class="ci-stat-title">{{ $card['title'] }}</div>
                <div class="ci-stat-value">{{ $card['value'] }}</div>
                <div class="ci-stat-note">{{ $card['note'] }}</div>
            </div>
        @endforeach
    </div>
</div>
