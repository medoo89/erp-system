<x-filament-panels::page>
    <style>
        .fi-header {
            display: none !important;
        }

        .fi-page {
            gap: 1rem !important;
        }

        :root {
            --sf-navy: #1f4664;
            --sf-navy-2: #2b5c7e;
            --sf-teal: #4ca7a8;
            --sf-gold: #b89332;
            --sf-border: #d7e2e5;
            --sf-bg: #f7fafb;
            --sf-card: #ffffff;
            --sf-text: #0f172a;
            --sf-muted: #667085;
        }

        .sf-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .sf-hero {
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            padding: 34px 36px;
            border: 1px solid rgba(76, 167, 168, 0.18);
            background: linear-gradient(135deg, var(--sf-navy) 0%, var(--sf-navy-2) 55%, #2e6e72 100%);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.10);
        }

        .sf-hero::before {
            content: "";
            position: absolute;
            right: -60px;
            top: -70px;
            width: 230px;
            height: 230px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255,255,255,0.12), transparent 70%);
        }

        .sf-hero::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--sf-teal), var(--sf-gold));
        }

        .sf-kicker {
            position: relative;
            z-index: 1;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgba(255,255,255,.72);
        }

        .sf-title {
            position: relative;
            z-index: 1;
            margin-top: 10px;
            font-size: 50px;
            line-height: 1;
            font-weight: 900;
            color: #fff;
        }

        .sf-subtitle {
            position: relative;
            z-index: 1;
            margin-top: 14px;
            max-width: 920px;
            font-size: 17px;
            line-height: 1.7;
            color: rgba(255,255,255,.85);
        }

        .sf-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .sf-summary-card {
            background: var(--sf-card);
            border: 1px solid var(--sf-border);
            border-radius: 18px;
            padding: 22px 24px;
            box-shadow: 0 10px 22px rgba(15,23,42,.05);
            position: relative;
            overflow: hidden;
        }

        .sf-summary-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 4px;
        }

        .sf-summary-card--eur::before { background: var(--sf-navy); }
        .sf-summary-card--lyd::before { background: var(--sf-gold); }
        .sf-summary-card--usd::before { background: var(--sf-teal); }

        .sf-summary-label {
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #607085;
        }

        .sf-summary-value {
            margin-top: 18px;
            font-size: 48px;
            line-height: 1;
            font-weight: 900;
            color: var(--sf-text);
        }

        .sf-summary-note {
            margin-top: 14px;
            font-size: 14px;
            color: var(--sf-muted);
        }

        .sf-section {
            background: var(--sf-card);
            border: 1px solid var(--sf-border);
            border-radius: 22px;
            padding: 28px;
            box-shadow: 0 10px 24px rgba(15,23,42,.04);
        }

        .sf-section--banks {
            background: linear-gradient(180deg, #ffffff 0%, #f4f8fa 100%);
        }

        .sf-section--cash {
            background: linear-gradient(180deg, #ffffff 0%, #f4faf8 100%);
        }

        .sf-section--clearing {
            background: linear-gradient(180deg, #ffffff 0%, #fbf8f2 100%);
        }

        .sf-section--nav {
            background: linear-gradient(180deg, #ffffff 0%, #f5f8fa 100%);
        }

        .sf-section-kicker {
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .sf-section--banks .sf-section-kicker { color: var(--sf-navy); }
        .sf-section--cash .sf-section-kicker { color: var(--sf-teal); }
        .sf-section--clearing .sf-section-kicker { color: var(--sf-gold); }
        .sf-section--nav .sf-section-kicker { color: var(--sf-navy); }

        .sf-section-title {
            margin-top: 8px;
            font-size: 30px;
            line-height: 1.1;
            font-weight: 900;
            color: var(--sf-text);
        }

        .sf-section-subtitle {
            margin-top: 8px;
            font-size: 15px;
            line-height: 1.7;
            color: var(--sf-muted);
        }

        .sf-bank-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .sf-bank-card {
            background: rgba(255,255,255,.92);
            border: 1px solid var(--sf-border);
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 8px 18px rgba(15,23,42,.04);
            position: relative;
        }

        .sf-bank-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-top-left-radius: 18px;
            border-bottom-left-radius: 18px;
            background: linear-gradient(180deg, var(--sf-navy), var(--sf-teal));
        }

        .sf-bank-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .sf-bank-name {
            font-size: 20px;
            font-weight: 900;
            color: var(--sf-text);
            line-height: 1.2;
        }

        .sf-bank-meta {
            margin-top: 8px;
            font-size: 14px;
            color: var(--sf-muted);
            line-height: 1.6;
        }

        .sf-profile-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 108px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 1px solid var(--sf-border);
            background: #fff;
            color: var(--sf-navy);
            font-size: 12px;
            font-weight: 800;
            text-decoration: none !important;
            transition: .18s ease;
        }

        .sf-profile-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(15,23,42,.06);
        }

        .sf-currency-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .sf-currency-card,
        .sf-account-card,
        .sf-nav-card {
            border: 1px solid var(--sf-border);
            background: #fff;
            border-radius: 16px;
            text-decoration: none !important;
            color: inherit !important;
            transition: .18s ease;
        }

        .sf-currency-card {
            padding: 16px;
            position: relative;
        }

        .sf-currency-card:hover,
        .sf-account-card:hover,
        .sf-nav-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(15,23,42,.08);
            border-color: #b9cbd1;
        }

        .sf-currency-card::after,
        .sf-account-card::after {
            content: "Open Account ↗";
            display: block;
            margin-top: 10px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .10em;
            text-transform: uppercase;
            color: var(--sf-teal);
        }

        .sf-code {
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #607085;
        }

        .sf-number {
            margin-top: 14px;
            font-size: 22px;
            line-height: 1;
            font-weight: 900;
            color: var(--sf-text);
        }

        .sf-caption {
            margin-top: 10px;
            font-size: 13px;
            color: var(--sf-muted);
            line-height: 1.5;
        }

        .sf-card-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .sf-account-card {
            padding: 18px;
        }

        .sf-account-title {
            margin-top: 10px;
            font-size: 18px;
            line-height: 1.3;
            font-weight: 900;
            color: var(--sf-text);
        }

        .sf-account-balance {
            margin-top: 16px;
            font-size: 30px;
            line-height: 1;
            font-weight: 900;
            color: var(--sf-text);
        }

        .sf-nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 18px;
            margin-top: 22px;
        }

        .sf-nav-card {
            padding: 22px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 8px 18px rgba(15,23,42,.04);
        }

        .sf-nav-card--accounts { border-top: 4px solid var(--sf-navy); background: linear-gradient(180deg, #ffffff 0%, #f4f8fb 100%); }
        .sf-nav-card--transactions { border-top: 4px solid #607085; background: linear-gradient(180deg, #ffffff 0%, #f6f7fa 100%); }
        .sf-nav-card--operations { border-top: 4px solid var(--sf-teal); background: linear-gradient(180deg, #ffffff 0%, #f3faf8 100%); }
        .sf-nav-card--clearing { border-top: 4px solid var(--sf-gold); background: linear-gradient(180deg, #ffffff 0%, #fbf8f2 100%); }
        .sf-nav-card--banks { border-top: 4px solid var(--sf-navy-2); background: linear-gradient(180deg, #ffffff 0%, #f4f7fa 100%); }

        .sf-nav-title {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.15;
            color: var(--sf-text);
        }

        .sf-nav-subtitle {
            margin-top: 10px;
            font-size: 14px;
            line-height: 1.7;
            color: var(--sf-muted);
        }

        .sf-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .sf-stat-box {
            min-width: 96px;
            padding: 12px 13px;
            border-radius: 14px;
            border: 1px solid var(--sf-border);
            background: rgba(255,255,255,.96);
        }

        .sf-stat-value {
            font-size: 22px;
            line-height: 1;
            font-weight: 900;
            color: var(--sf-text);
        }

        .sf-stat-label {
            margin-top: 6px;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #607085;
        }

        .sf-empty {
            margin-top: 18px;
            padding: 16px 18px;
            border-radius: 16px;
            border: 1px dashed #c8d3de;
            background: rgba(255,255,255,.85);
            color: var(--sf-muted);
        }

        @media (max-width: 1200px) {
            .sf-summary-grid,
            .sf-currency-grid,
            .sf-card-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .sf-summary-grid,
            .sf-bank-grid,
            .sf-currency-grid,
            .sf-card-grid,
            .sf-nav-grid {
                grid-template-columns: 1fr;
            }

            .sf-hero {
                padding: 26px 24px;
            }

            .sf-title {
                font-size: 38px;
            }
        }
        .dark .sf-treasury-shell {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.03), transparent 18%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.05), transparent 20%),
                linear-gradient(180deg, rgba(7,20,39,.96) 0%, rgba(10,24,42,.96) 100%) !important;
        }

        .dark .sf-currency-card,
        .dark .sf-panel,
        .dark .sf-bank-card,
        .dark .sf-cash-card,
        .dark .sf-clearing-card,
        .dark .sf-quick-card,
        .dark .sf-mini-card,
        .dark .sf-account-card {
            background: rgba(12,23,38,.96) !important;
            border-color: rgba(76,167,168,.16) !important;
            box-shadow: 0 10px 24px rgba(0,0,0,.22) !important;
        }

        .dark .sf-panel-title,
        .dark .sf-bank-name,
        .dark .sf-cash-name,
        .dark .sf-clearing-name,
        .dark .sf-quick-title,
        .dark .sf-card-value,
        .dark .sf-mini-value,
        .dark .sf-account-title {
            color: #f6fbff !important;
        }

        .dark .sf-panel-subtitle,
        .dark .sf-bank-meta,
        .dark .sf-cash-meta,
        .dark .sf-clearing-meta,
        .dark .sf-quick-subtitle,
        .dark .sf-card-meta,
        .dark .sf-mini-label,
        .dark .sf-account-meta {
            color: #9fb2c3 !important;
        }

        .dark .sf-kicker,
        .dark .sf-section-kicker,
        .dark .sf-bank-currency,
        .dark .sf-cash-currency,
        .dark .sf-clearing-currency {
            color: #7fcfd0 !important;
        }

        .dark .sf-open-link,
        .dark .sf-open-account,
        .dark .sf-profile-link {
            color: #63d1cf !important;
        }

        .dark .sf-bank-card:hover,
        .dark .sf-cash-card:hover,
        .dark .sf-clearing-card:hover,
        .dark .sf-quick-card:hover,
        .dark .sf-account-card:hover {
            border-color: rgba(99,209,207,.28) !important;
            box-shadow: 0 16px 30px rgba(0,0,0,.28) !important;
        }

        .dark .sf-bank-inner,
        .dark .sf-cash-inner,
        .dark .sf-clearing-inner,
        .dark .sf-mini-inner,
        .dark .sf-account-inner {
            background: rgba(255,255,255,.02) !important;
            border-color: rgba(76,167,168,.12) !important;
        }
        .dark .sf-wrap {
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.03), transparent 18%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.05), transparent 20%),
                linear-gradient(180deg, rgba(7,20,39,.96) 0%, rgba(10,24,42,.96) 100%);
            border-radius: 28px;
            padding: 4px;
        }

        .dark .sf-summary-card,
        .dark .sf-section,
        .dark .sf-bank-card,
        .dark .sf-currency-card,
        .dark .sf-account-card,
        .dark .sf-nav-card {
            background: rgba(12,23,38,.96) !important;
            border-color: rgba(76,167,168,.16) !important;
            box-shadow: 0 10px 24px rgba(0,0,0,.22) !important;
        }

        .dark .sf-section--banks,
        .dark .sf-section--cash,
        .dark .sf-section--clearing,
        .dark .sf-section--nav {
            background: rgba(12,23,38,.96) !important;
        }

        .dark .sf-profile-btn {
            background: rgba(255,255,255,.04) !important;
            border-color: rgba(76,167,168,.16) !important;
            color: #d7eef0 !important;
        }

        .dark .sf-profile-btn:hover,
        .dark .sf-currency-card:hover,
        .dark .sf-account-card:hover,
        .dark .sf-nav-card:hover,
        .dark .sf-bank-card:hover,
        .dark .sf-summary-card:hover {
            border-color: rgba(99,209,207,.28) !important;
            box-shadow: 0 16px 30px rgba(0,0,0,.28) !important;
        }

        .dark .sf-title,
        .dark .sf-section-title,
        .dark .sf-bank-name,
        .dark .sf-number {
            color: #f6fbff !important;
        }

        .dark .sf-subtitle,
        .dark .sf-section-subtitle,
        .dark .sf-bank-meta,
        .dark .sf-summary-note {
            color: #9fb2c3 !important;
        }

        .dark .sf-summary-label,
        .dark .sf-code {
            color: #8ea8be !important;
        }

        .dark .sf-section--banks .sf-section-kicker,
        .dark .sf-section--cash .sf-section-kicker,
        .dark .sf-section--clearing .sf-section-kicker,
        .dark .sf-section--nav .sf-section-kicker,
        .dark .sf-currency-card::after,
        .dark .sf-account-card::after {
            color: #63d1cf !important;
        }


        .dark .sf-summary-value,
        .dark .sf-number,
        .dark .sf-card-value,
        .dark .sf-account-value,
        .dark .sf-balance-value {
            color: #f6fbff !important;
            text-shadow: none !important;
        }

        .dark .sf-stat-chip,
        .dark .sf-stat-box,
        .dark .sf-stat-card,
        .dark .sf-mini-stat,
        .dark .sf-nav-stat,
        .dark .sf-module-stat {
            background: rgba(255,255,255,.04) !important;
            border: 1px solid rgba(76,167,168,.16) !important;
            box-shadow: none !important;
        }

        .dark .sf-stat-value,
        .dark .sf-stat-box-value,
        .dark .sf-stat-card-value,
        .dark .sf-mini-stat-value,
        .dark .sf-nav-stat-value,
        .dark .sf-module-stat-value {
            color: #f6fbff !important;
        }

        .dark .sf-stat-label,
        .dark .sf-stat-box-label,
        .dark .sf-stat-card-label,
        .dark .sf-mini-stat-label,
        .dark .sf-nav-stat-label,
        .dark .sf-module-stat-label {
            color: #8ea8be !important;
        }

        .dark .sf-summary-card::before,
        .dark .sf-bank-card::before {
            opacity: 1 !important;
        }


        .dark .sf-wrap [style*="color:#0f172a"],
        .dark .sf-wrap [style*="color: #0f172a"] {
            color: #f6fbff !important;
        }

        .dark .sf-wrap [style*="color:#64748b"],
        .dark .sf-wrap [style*="color: #64748b"],
        .dark .sf-wrap [style*="color:#667085"],
        .dark .sf-wrap [style*="color: #667085"],
        .dark .sf-wrap [style*="color:#475569"],
        .dark .sf-wrap [style*="color: #475569"] {
            color: #9fb2c3 !important;
        }

        .dark .sf-wrap [style*="color:#94a3b8"],
        .dark .sf-wrap [style*="color: #94a3b8"],
        .dark .sf-wrap [style*="color:#607085"],
        .dark .sf-wrap [style*="color: #607085"] {
            color: #8ea8be !important;
        }

        .dark .sf-wrap [style*="background:#fff"],
        .dark .sf-wrap [style*="background: #fff"],
        .dark .sf-wrap [style*="background:#ffffff"],
        .dark .sf-wrap [style*="background: #ffffff"],
        .dark .sf-wrap [style*="background:#f8fafc"],
        .dark .sf-wrap [style*="background: #f8fafc"],
        .dark .sf-wrap [style*="background:#fcfdff"],
        .dark .sf-wrap [style*="background: #fcfdff"] {
            background: rgba(12,23,38,.96) !important;
        }

        .dark .sf-wrap [style*="border:1px solid #dbe7ef"],
        .dark .sf-wrap [style*="border: 1px solid #dbe7ef"],
        .dark .sf-wrap [style*="border:1px solid #e2e8f0"],
        .dark .sf-wrap [style*="border: 1px solid #e2e8f0"],
        .dark .sf-wrap [style*="border:1px solid #b9cbd1"],
        .dark .sf-wrap [style*="border: 1px solid #b9cbd1"] {
            border-color: rgba(76,167,168,.16) !important;
        }

        .dark .sf-wrap [style*="background:#f0fdf4"],
        .dark .sf-wrap [style*="background: #f0fdf4"],
        .dark .sf-wrap [style*="background:#eff6ff"],
        .dark .sf-wrap [style*="background: #eff6ff"],
        .dark .sf-wrap [style*="background:#fef2f2"],
        .dark .sf-wrap [style*="background: #fef2f2"],
        .dark .sf-wrap [style*="background:#eef2ff"],
        .dark .sf-wrap [style*="background: #eef2ff"],
        .dark .sf-wrap [style*="background:#faf5ff"],
        .dark .sf-wrap [style*="background: #faf5ff"] {
            background: rgba(255,255,255,.04) !important;
        }


        .dark .sf-currency-card,
        .dark .sf-account-card,
        .dark .sf-nav-card,
        .dark .sf-bank-card {
            color: #f6fbff !important;
        }

        .dark .sf-currency-card *,
        .dark .sf-account-card *,
        .dark .sf-nav-card *,
        .dark .sf-bank-card * {
            color: inherit;
        }

        .dark .sf-currency-card .sf-code,
        .dark .sf-account-card .sf-code,
        .dark .sf-nav-card .sf-code,
        .dark .sf-bank-card .sf-code,
        .dark .sf-nav-card p,
        .dark .sf-nav-card .sf-summary-note,
        .dark .sf-account-card .sf-summary-note,
        .dark .sf-currency-card .sf-summary-note,
        .dark .sf-bank-card .sf-bank-meta {
            color: #9fb2c3 !important;
        }

        .dark .sf-currency-card .sf-number,
        .dark .sf-account-card .sf-number,
        .dark .sf-nav-card .sf-number,
        .dark .sf-bank-card .sf-number,
        .dark .sf-nav-card strong,
        .dark .sf-nav-card b,
        .dark .sf-account-card strong,
        .dark .sf-currency-card strong {
            color: #f6fbff !important;
        }

        .dark .sf-nav-card [style*="background:#fff"],
        .dark .sf-nav-card [style*="background: #fff"],
        .dark .sf-nav-card [style*="background:#ffffff"],
        .dark .sf-nav-card [style*="background: #ffffff"],
        .dark .sf-nav-card [style*="background:#f8fafc"],
        .dark .sf-nav-card [style*="background: #f8fafc"],
        .dark .sf-nav-card [style*="background:#fcfdff"],
        .dark .sf-nav-card [style*="background: #fcfdff"],
        .dark .sf-nav-card [style*="background:#f1f5f9"],
        .dark .sf-nav-card [style*="background: #f1f5f9"] {
            background: rgba(255,255,255,.04) !important;
            border-color: rgba(76,167,168,.16) !important;
            color: #f6fbff !important;
        }

        .dark .sf-nav-card [style*="color:#0f172a"],
        .dark .sf-nav-card [style*="color: #0f172a"],
        .dark .sf-nav-card [style*="color:#111827"],
        .dark .sf-nav-card [style*="color: #111827"] {
            color: #f6fbff !important;
        }

        .dark .sf-nav-card [style*="color:#64748b"],
        .dark .sf-nav-card [style*="color: #64748b"],
        .dark .sf-nav-card [style*="color:#667085"],
        .dark .sf-nav-card [style*="color: #667085"],
        .dark .sf-nav-card [style*="color:#475569"],
        .dark .sf-nav-card [style*="color: #475569"],
        .dark .sf-nav-card [style*="color:#94a3b8"],
        .dark .sf-nav-card [style*="color: #94a3b8"] {
            color: #9fb2c3 !important;
        }

        .dark .sf-currency-card [style*="color:#0f172a"],
        .dark .sf-currency-card [style*="color: #0f172a"],
        .dark .sf-account-card [style*="color:#0f172a"],
        .dark .sf-account-card [style*="color: #0f172a"],
        .dark .sf-bank-card [style*="color:#0f172a"],
        .dark .sf-bank-card [style*="color: #0f172a"] {
            color: #f6fbff !important;
        }

        .dark .sf-currency-card [style*="color:#64748b"],
        .dark .sf-currency-card [style*="color: #64748b"],
        .dark .sf-account-card [style*="color:#64748b"],
        .dark .sf-account-card [style*="color: #64748b"],
        .dark .sf-bank-card [style*="color:#64748b"],
        .dark .sf-bank-card [style*="color: #64748b"],
        .dark .sf-bank-card [style*="color:#94a3b8"],
        .dark .sf-bank-card [style*="color: #94a3b8"] {
            color: #9fb2c3 !important;
        }

        .dark .sf-nav-card a,
        .dark .sf-currency-card a,
        .dark .sf-account-card a,
        .dark .sf-bank-card a {
            color: #63d1cf !important;
        }

    </style>

    <div class="sf-wrap">
        @php
            $eurTotal = (float) ($currencyTotals['EUR'] ?? 0);
            $lydTotal = (float) ($currencyTotals['LYD'] ?? 0);
            $usdTotal = (float) ($currencyTotals['USD'] ?? 0);
        @endphp

        <section class="sf-hero">
            <div class="sf-kicker">Corporate Finance · Treasury Control</div>
            <div class="sf-title">Treasury</div>
            <div class="sf-subtitle">
                Central treasury workspace for bank balances, cash positions, clearing accounts, and treasury operations.
            </div>
        </section>

        <section class="sf-summary-grid">
            <div class="sf-summary-card sf-summary-card--eur">
                <div class="sf-summary-label">EUR Position</div>
                <div class="sf-summary-value">{{ number_format($eurTotal, 2) }}</div>
                <div class="sf-summary-note">Active treasury accounts denominated in EUR</div>
            </div>

            <div class="sf-summary-card sf-summary-card--lyd">
                <div class="sf-summary-label">LYD Position</div>
                <div class="sf-summary-value">{{ number_format($lydTotal, 2) }}</div>
                <div class="sf-summary-note">Active treasury accounts denominated in LYD</div>
            </div>

            <div class="sf-summary-card sf-summary-card--usd">
                <div class="sf-summary-label">USD Position</div>
                <div class="sf-summary-value">{{ number_format($usdTotal, 2) }}</div>
                <div class="sf-summary-note">Active treasury accounts denominated in USD</div>
            </div>
        </section>

        <section class="sf-section sf-section--banks">
            <div class="sf-section-kicker">Banking Structure</div>
            <div class="sf-section-title">Bank Balances by Institution</div>
            <div class="sf-section-subtitle">Review treasury balances grouped by bank profile and linked currency accounts.</div>

            @if(($bankProfiles->count() ?? 0) > 0)
                <div class="sf-bank-grid">
                    @foreach($bankProfiles as $profile)
                        <div class="sf-bank-card">
                            <div class="sf-bank-head">
                                <div>
                                    <div class="sf-bank-name">{{ $profile['bank_name'] ?: ($profile['profile_name'] ?: 'Bank Profile') }}</div>
                                    <div class="sf-bank-meta">
                                        {{ $profile['profile_name'] ?: 'Profile' }}
                                        @if(!empty($profile['beneficiary_name']))
                                            · {{ $profile['beneficiary_name'] }}
                                        @endif
                                    </div>
                                </div>

                                <a class="sf-profile-btn" href="{{ $profile['resource_url'] }}">Open Profile</a>
                            </div>

                            <div class="sf-currency-grid">
                                @foreach($profile['currencies'] as $currencyRow)
                                    @if(!empty($currencyRow['treasury_account_id']))
                                        <a class="sf-currency-card" href="{{ \App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource::getUrl('view', ['record' => $currencyRow['treasury_account_id']]) }}">
                                            <div class="sf-code">{{ $currencyRow['currency'] ?: '-' }}</div>
                                            <div class="sf-number">{{ number_format((float) ($currencyRow['balance'] ?? 0), 2) }}</div>
                                            <div class="sf-caption">{{ $currencyRow['account_name'] ?: 'Treasury Account' }}</div>
                                        </a>
                                    @else
                                        <div class="sf-currency-card">
                                            <div class="sf-code">{{ $currencyRow['currency'] ?: '-' }}</div>
                                            <div class="sf-number">0.00</div>
                                            <div class="sf-caption">Treasury not linked</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="sf-empty">No active bank profiles found yet.</div>
            @endif
        </section>

        <section class="sf-section sf-section--cash">
            <div class="sf-section-kicker">Cash Position</div>
            <div class="sf-section-title">Main Cash Accounts</div>
            <div class="sf-section-subtitle">Review company cash balances and open each treasury account for movement detail.</div>

            @if(($cashAccounts->count() ?? 0) > 0)
                <div class="sf-card-grid">
                    @foreach($cashAccounts as $account)
                        <a class="sf-account-card" href="{{ $account['url'] }}">
                            <div class="sf-code">{{ $account['currency'] ?: '-' }}</div>
                            <div class="sf-account-title">{{ $account['account_name'] ?: 'Cash Account' }}</div>
                            <div class="sf-account-balance">{{ number_format((float) ($account['balance'] ?? 0), 2) }}</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sf-empty">No active cash accounts found yet.</div>
            @endif
        </section>

        <section class="sf-section sf-section--clearing">
            <div class="sf-section-kicker">Clearing Position</div>
            <div class="sf-section-title">Clearing Accounts</div>
            <div class="sf-section-subtitle">Monitor staged receipts, temporary holding balances, and pending treasury settlement flows.</div>

            @if(($clearingAccounts->count() ?? 0) > 0)
                <div class="sf-card-grid">
                    @foreach($clearingAccounts as $account)
                        <a class="sf-account-card" href="{{ $account['url'] }}">
                            <div class="sf-code">{{ $account['currency'] ?: '-' }}</div>
                            <div class="sf-account-title">{{ $account['account_name'] ?: 'Clearing Account' }}</div>
                            <div class="sf-account-balance">{{ number_format((float) ($account['balance'] ?? 0), 2) }}</div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="sf-empty">No active clearing accounts found yet.</div>
            @endif
        </section>

        <section class="sf-section sf-section--nav">
            <div class="sf-section-kicker">Treasury Navigation</div>
            <div class="sf-section-title">Treasury Modules</div>
            <div class="sf-section-subtitle">Open the main treasury areas and review the latest operational indicators.</div>

            <div class="sf-nav-grid">
                <a class="sf-nav-card sf-nav-card--accounts" href="{{ $treasuryAccountsUrl }}">
                    <div>
                        <div class="sf-nav-title">Treasury Accounts</div>
                        <div class="sf-nav-subtitle">Access all treasury accounts, balances, account types, and institutional structures.</div>
                    </div>
                    <div class="sf-stat-row">
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $accountsCount }}</div>
                            <div class="sf-stat-label">Accounts</div>
                        </div>
                    </div>
                </a>

                <a class="sf-nav-card sf-nav-card--transactions" href="{{ $treasuryTransactionsUrl }}">
                    <div>
                        <div class="sf-nav-title">Treasury Transactions</div>
                        <div class="sf-nav-subtitle">Review posted incoming and outgoing treasury entries across all treasury accounts.</div>
                    </div>
                    <div class="sf-stat-row">
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $transactionsCount }}</div>
                            <div class="sf-stat-label">Total</div>
                        </div>
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $incomingTransactionsCount }}</div>
                            <div class="sf-stat-label">Incoming</div>
                        </div>
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $outgoingTransactionsCount }}</div>
                            <div class="sf-stat-label">Outgoing</div>
                        </div>
                    </div>
                </a>

                <a class="sf-nav-card sf-nav-card--operations" href="{{ $treasuryOperationsUrl }}">
                    <div>
                        <div class="sf-nav-title">Treasury Operations</div>
                        <div class="sf-nav-subtitle">Track treasury workflow operations, internal transfers, and settlement movement status.</div>
                    </div>
                    <div class="sf-stat-row">
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $operationsCount }}</div>
                            <div class="sf-stat-label">Operations</div>
                        </div>
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $pendingOperationsCount }}</div>
                            <div class="sf-stat-label">Pending</div>
                        </div>
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $clearedOperationsCount }}</div>
                            <div class="sf-stat-label">Cleared</div>
                        </div>
                    </div>
                </a>

                <a class="sf-nav-card sf-nav-card--clearing" href="{{ $clearingMonitorUrl }}">
                    <div>
                        <div class="sf-nav-title">Clearing Monitor</div>
                        <div class="sf-nav-subtitle">Monitor pending clearing items and treasury accounts involved in staged settlement.</div>
                    </div>
                    <div class="sf-stat-row">
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $pendingClearingCount }}</div>
                            <div class="sf-stat-label">Pending</div>
                        </div>
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $clearingAccounts->count() }}</div>
                            <div class="sf-stat-label">Accounts</div>
                        </div>
                    </div>
                </a>

                <a class="sf-nav-card sf-nav-card--banks" href="{{ $bankProfilesUrl }}">
                    <div>
                        <div class="sf-nav-title">Bank Profiles</div>
                        <div class="sf-nav-subtitle">Manage institutional banking profiles and linked multi-currency treasury accounts.</div>
                    </div>
                    <div class="sf-stat-row">
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $bankProfilesCount }}</div>
                            <div class="sf-stat-label">Profiles</div>
                        </div>
                        <div class="sf-stat-box">
                            <div class="sf-stat-value">{{ $linkedBankCurrencyAccounts }}</div>
                            <div class="sf-stat-label">Currency Accounts</div>
                        </div>
                    </div>
                </a>
            </div>
        </section>
    </div>
</x-filament-panels::page>
