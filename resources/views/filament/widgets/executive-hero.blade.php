<div class="sf-dashboard-shell">
    <style>
        /*
         * Executive Dashboard — Material minimal, centered, day/night compatible.
         */
        .sf-dashboard-shell {
            display: grid;
            gap: 18px;
        }

        .sada-executive-hero {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            padding: clamp(28px, 4vw, 44px);
            text-align: center;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .18), transparent 34%),
                radial-gradient(circle at bottom left, rgba(59, 130, 246, .16), transparent 32%),
                linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #234b74 100%);
            border: 1px solid rgba(148, 163, 184, .20);
            box-shadow: 0 22px 60px rgba(15, 23, 42, .16);
            color: #ffffff;
        }

        .sada-executive-badge {
            width: fit-content;
            margin: 0 auto 16px;
            border-radius: 999px;
            padding: 9px 13px;
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .14);
            color: #bae6fd;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sada-executive-title {
            margin: 0 auto;
            color: #ffffff;
            font-size: clamp(42px, 5vw, 76px);
            line-height: .92;
            letter-spacing: -.07em;
            font-weight: 950;
            text-align: center;
        }

        .sada-executive-subtitle {
            max-width: 850px;
            margin: 18px auto 0;
            color: #cbd5e1;
            font-size: clamp(15px, 1.2vw, 18px);
            line-height: 1.65;
            font-weight: 650;
            text-align: center;
        }

        .sada-executive-pill {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 24px auto 0;
            border-radius: 999px;
            padding: 10px 14px;
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .14);
            box-shadow: 0 12px 28px rgba(0, 0, 0, .10);
        }

        .sada-executive-pill-label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sada-executive-pill-value {
            color: #ffffff;
            font-size: 13px;
            font-weight: 900;
        }

        /*
         * Broad Filament stats cards refinement.
         */
        .fi-wi-stats-overview,
        .fi-wi-stats-overview > div,
        .fi-wi-stats-overview .grid {
            gap: 14px !important;
        }

        .fi-wi-stats-overview-stat,
        .fi-wi-stats-overview-stat-card,
        .fi-wi-stats-overview [class*="stat"] {
            border-radius: 26px !important;
        }

        .fi-wi-stats-overview-stat,
        .fi-wi-stats-overview-stat-card,
        .fi-wi-stats-overview section,
        .fi-wi-stats-overview article {
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 34%),
                rgba(255, 255, 255, .96) !important;
            border: 1px solid rgba(15, 23, 42, .08) !important;
            box-shadow: 0 16px 42px rgba(15, 23, 42, .06) !important;
            overflow: hidden !important;
        }

        .dark .fi-wi-stats-overview-stat,
        .dark .fi-wi-stats-overview-stat-card,
        .dark .fi-wi-stats-overview section,
        .dark .fi-wi-stats-overview article {
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
                rgba(15, 23, 42, .72) !important;
            border-color: rgba(148, 163, 184, .18) !important;
            box-shadow: 0 18px 46px rgba(0, 0, 0, .18) !important;
        }

        .fi-wi-stats-overview-stat-value,
        .fi-wi-stats-overview [class*="value"] {
            font-size: clamp(30px, 3vw, 46px) !important;
            letter-spacing: -.07em !important;
            font-weight: 950 !important;
        }

        .fi-wi-stats-overview-stat-label,
        .fi-wi-stats-overview [class*="label"] {
            font-weight: 900 !important;
            letter-spacing: -.02em !important;
        }

        .fi-wi-stats-overview-stat-description,
        .fi-wi-stats-overview [class*="description"] {
            font-weight: 650 !important;
        }
    </style>

    <div class="sada-executive-hero">
        <div class="sada-executive-badge">SADA FEZZAN ERP</div>

        <h1 class="sada-executive-title">Executive Dashboard</h1>

        <p class="sada-executive-subtitle">
            A focused operational overview for recruitment, employment, rotations, mobilization, salary slips, and expiring employee documents.
        </p>

        <div class="sada-executive-pill">
            <span class="sada-executive-pill-label">Today</span>
            <span class="sada-executive-pill-value">{{ $todayLabel }}</span>
        </div>
    </div>
</div>
