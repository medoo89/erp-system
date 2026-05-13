<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Restricted | Sada Fezzan ERP</title>

    <style>
        :root {
            --sf-primary: #234b74;
            --sf-teal: #4ca7a8;
            --sf-bg: #eef7f7;
            --sf-card: rgba(255,255,255,.90);
            --sf-text: #0f172a;
            --sf-muted: #64748b;
            --sf-border: rgba(15,23,42,.08);
            --sf-danger: #dc2626;
            --sf-warning: #f59e0b;
            --sf-blue: #2563eb;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--sf-text);
            background:
                radial-gradient(circle at top left, rgba(76,167,168,.20), transparent 34%),
                radial-gradient(circle at bottom right, rgba(35,75,116,.18), transparent 34%),
                linear-gradient(135deg, #f8fbfc 0%, var(--sf-bg) 100%);
        }

        body {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 28px;
        }

        .sf-page {
            width: min(100%, 960px);
        }

        .sf-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .sf-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .sf-logo-wrap {
            width: 92px;
            min-height: 58px;
            display: grid;
            place-items: center;
            padding: 8px 10px;
            border-radius: 22px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(76,167,168,.18);
            box-shadow: 0 12px 30px rgba(15,23,42,.06);
        }

        .sf-logo-wrap img {
            max-width: 100%;
            max-height: 48px;
            object-fit: contain;
        }

        .sf-brand-text {
            display: grid;
            gap: 2px;
        }

        .sf-system {
            font-size: 20px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
            color: var(--sf-primary);
        }

        .sf-sub {
            font-size: 12px;
            font-weight: 850;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--sf-teal);
        }

        .sf-code-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 12px 28px rgba(15,23,42,.05);
            font-size: 13px;
            font-weight: 950;
            color: var(--sf-primary);
        }

        .sf-card {
            position: relative;
            overflow: hidden;
            border-radius: 38px;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.16), transparent 36%),
                radial-gradient(circle at bottom left, rgba(37,99,235,.08), transparent 34%),
                var(--sf-card);
            border: 1px solid var(--sf-border);
            box-shadow:
                0 28px 80px rgba(15,23,42,.12),
                inset 0 1px 0 rgba(255,255,255,.70);
            backdrop-filter: blur(18px);
        }

        .sf-card-inner {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 30px;
            padding: clamp(28px, 5vw, 54px);
            align-items: center;
        }

        .sf-icon-shell {
            width: 82px;
            height: 82px;
            border-radius: 28px;
            display: grid;
            place-items: center;
            margin-bottom: 24px;
            background:
                linear-gradient(135deg, rgba(220,38,38,.12), rgba(245,158,11,.12));
            border: 1px solid rgba(220,38,38,.16);
            box-shadow: 0 18px 46px rgba(220,38,38,.10);
        }

        .sf-icon-shell svg {
            width: 38px;
            height: 38px;
            stroke: var(--sf-danger);
            stroke-width: 1.9;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sf-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 12px;
            margin-bottom: 16px;
            background: #fff7ed;
            border: 1px solid rgba(245,158,11,.20);
            color: #9a3412;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(44px, 7vw, 78px);
            line-height: .92;
            letter-spacing: -.075em;
            color: var(--sf-primary);
            font-weight: 1000;
        }

        .sf-message {
            margin: 20px 0 0;
            max-width: 660px;
            color: var(--sf-muted);
            font-size: 17px;
            line-height: 1.65;
            font-weight: 700;
        }

        .sf-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 28px;
        }

        .sf-btn {
            appearance: none;
            border: 0;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 950;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .sf-btn:hover {
            transform: translateY(-1px);
        }

        .sf-btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--sf-blue), var(--sf-primary));
            box-shadow: 0 16px 30px rgba(37,99,235,.18);
        }

        .sf-btn-soft {
            color: var(--sf-primary);
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 12px 24px rgba(15,23,42,.05);
        }

        .sf-side {
            min-height: 330px;
            border-radius: 30px;
            padding: 22px;
            background:
                linear-gradient(135deg, rgba(35,75,116,.96), rgba(16,85,103,.90));
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 24px 60px rgba(35,75,116,.20);
        }

        .sf-big-code {
            font-size: 86px;
            line-height: .9;
            letter-spacing: -.08em;
            font-weight: 1000;
            opacity: .96;
        }

        .sf-side-title {
            margin-top: 12px;
            color: rgba(255,255,255,.72);
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .sf-info-list {
            display: grid;
            gap: 10px;
        }

        .sf-info {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 18px;
            padding: 12px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.10);
            color: rgba(255,255,255,.82);
            font-size: 12px;
            line-height: 1.45;
            font-weight: 750;
        }

        .sf-dot {
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: #5eead4;
            flex-shrink: 0;
            box-shadow: 0 0 0 5px rgba(94,234,212,.10);
        }

        @media (prefers-color-scheme: dark) {
            html,
            body {
                background:
                    radial-gradient(circle at top left, rgba(76,167,168,.16), transparent 34%),
                    radial-gradient(circle at bottom right, rgba(37,99,235,.14), transparent 34%),
                    linear-gradient(135deg, #071427 0%, #0b1a31 100%);
                color: #e2e8f0;
            }

            .sf-card {
                background:
                    radial-gradient(circle at top right, rgba(76,167,168,.12), transparent 36%),
                    radial-gradient(circle at bottom left, rgba(37,99,235,.09), transparent 34%),
                    rgba(15,23,42,.82);
                border-color: rgba(148,163,184,.16);
            }

            .sf-system,
            h1 {
                color: #e8f2f4;
            }

            .sf-message {
                color: #94a3b8;
            }

            .sf-logo-wrap,
            .sf-code-pill,
            .sf-btn-soft {
                background: rgba(255,255,255,.08);
                border-color: rgba(255,255,255,.10);
                color: #e8f2f4;
            }
        }

        @media (max-width: 860px) {
            .sf-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .sf-card-inner {
                grid-template-columns: 1fr;
            }

            .sf-side {
                min-height: 240px;
            }
        }
    </style>
</head>

<body>
    <main class="sf-page">
        <div class="sf-top">
            <div class="sf-brand">
                <div class="sf-logo-wrap">
                    <img src="/images/sada-horizontal.png" alt="Sada Fezzan">
                </div>

                <div class="sf-brand-text">
                    <div class="sf-system">Sada Fezzan ERP</div>
                    <div class="sf-sub">Secure Access Control</div>
                </div>
            </div>

            <div class="sf-code-pill">
                <span>403</span>
                <span>Access Restricted</span>
            </div>
        </div>

        <section class="sf-card">
            <div class="sf-card-inner">
                <div>
                    <div class="sf-icon-shell">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3.75 19.25 7v5.25c0 4.55-3.02 7.78-7.25 9-4.23-1.22-7.25-4.45-7.25-9V7L12 3.75Z"/>
                            <path d="M9.25 12.25h5.5"/>
                            <path d="M12 9.5v5.5"/>
                        </svg>
                    </div>

                    <div class="sf-kicker">Permission Required</div>

                    <h1>You can’t access this page</h1>

                    <p class="sf-message">
                        Your ERP account does not currently have permission to view or perform this action.
                        Please contact your system administrator if you believe this access should be granted.
                    </p>

                    <div class="sf-actions">
                        <a class="sf-btn sf-btn-primary" href="/admin">
                            Back to Dashboard
                        </a>

                        <button class="sf-btn sf-btn-soft" type="button" onclick="window.history.back()">
                            Go Back
                        </button>
                    </div>
                </div>

                <aside class="sf-side">
                    <div>
                        <div class="sf-big-code">403</div>
                        <div class="sf-side-title">Protected ERP Area</div>
                    </div>

                    <div class="sf-info-list">
                        <div class="sf-info">
                            <span class="sf-dot"></span>
                            Access is controlled by ERP Page Rules.
                        </div>
                        <div class="sf-info">
                            <span class="sf-dot"></span>
                            Finance, HR, recruitment, and treasury pages can be restricted separately.
                        </div>
                        <div class="sf-info">
                            <span class="sf-dot"></span>
                            Contact a Super Admin to update your role or permissions.
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </main>
</body>
</html>
