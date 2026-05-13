<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sada Fezzan Oil Services Company')</title>

    <meta name="description" content="Sada Fezzan Oil Services Company provides oil and gas manpower, mobilization, logistics, maintenance, HSE, and field support services in Libya.">

    <link rel="icon" href="https://sfco.ly/sada-logo-full.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,300..700,0..1,-50..200" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Gilroy';
            src: url('/fonts/Gilroy-Light.otf') format('opentype');
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'Gilroy';
            src: url('/fonts/Gilroy-ExtraBold.otf') format('opentype');
            font-weight: 800 950;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --bg: #050b12;
            --bg-2: #071522;
            --card: rgba(255, 255, 255, 0.065);
            --card-2: rgba(255, 255, 255, 0.095);
            --border: rgba(255, 255, 255, 0.12);
            --text: #ffffff;
            --muted: #aab6c5;
            --muted-2: #7f8fa3;
            --cyan: #67e8f9;
            --cyan-2: #22d3ee;
            --gold: #d6a650;
            --gold-2: #b88735;
            --dark: #020617;
            --shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --container: 1180px;
        }

        html[data-theme="light"] {
            --bg: #f4f7fb;
            --bg-2: #ffffff;
            --card: rgba(255, 255, 255, 0.78);
            --card-2: rgba(255, 255, 255, 0.95);
            --border: rgba(15, 23, 42, 0.12);
            --text: #08111f;
            --muted: #475569;
            --muted-2: #64748b;
            --dark: #ffffff;
            --shadow: 0 24px 70px rgba(15, 23, 42, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Gilroy', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(214, 166, 80, 0.16), transparent 30%),
                radial-gradient(circle at 85% 10%, rgba(103, 232, 249, 0.10), transparent 26%),
                linear-gradient(180deg, #06111d 0%, #03070c 60%, #020617 100%);
            min-height: 100vh;
        }

        html[dir="rtl"] body {
            font-family: 'Gilroy', Tahoma, Arial, sans-serif;
        }

        html[data-theme="light"] body {
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(214, 166, 80, 0.18), transparent 30%),
                radial-gradient(circle at 88% 8%, rgba(34, 211, 238, 0.14), transparent 26%),
                linear-gradient(180deg, #f7fafc 0%, #eef4f8 58%, #ffffff 100%);
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.045;
            background-image:
                linear-gradient(rgba(255,255,255,.7) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.7) 1px, transparent 1px);
            background-size: 92px 92px;
            z-index: -1;
        }

        html[data-theme="light"] body::before {
            opacity: 0.032;
            background-image:
                linear-gradient(rgba(15,23,42,.5) 1px, transparent 1px),
                linear-gradient(90deg, rgba(15,23,42,.5) 1px, transparent 1px);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(var(--container), calc(100% - 36px));
            margin: 0 auto;
        }

        .material-symbols-rounded {
            font-family: 'Material Symbols Rounded';
            font-weight: normal;
            font-style: normal;
            font-size: 22px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-flex;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            font-feature-settings: 'liga';
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid var(--border);
            background: rgba(5, 11, 18, 0.78);
            backdrop-filter: blur(22px);
        }

        html[data-theme="light"] .site-header {
            background: rgba(255, 255, 255, 0.84);
        }

        .header-inner {
            min-height: 82px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 235px;
        }

        .brand-logo {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: #ffffff;
            padding: 8px;
            object-fit: contain;
            box-shadow: var(--shadow);
        }

        .brand-title {
            font-size: 14px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            font-weight: 950;
        }

        .brand-subtitle {
            margin-top: 3px;
            color: var(--muted-2);
            font-size: 12px;
            font-weight: 300;
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 26px;
        }

        .nav a {
            font-size: 14px;
            color: var(--muted);
            font-weight: 800;
            transition: 0.2s ease;
        }

        .nav a:hover {
            color: var(--cyan);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 0;
            cursor: pointer;
            border-radius: 999px;
            padding: 13px 18px;
            font-weight: 950;
            font-size: 14px;
            transition: 0.2s ease;
            white-space: nowrap;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--cyan);
            color: #021018;
            box-shadow: 0 0 38px rgba(103, 232, 249, 0.22);
        }

        .btn-primary:hover {
            background: #ffffff;
            transform: translateY(-1px);
        }

        .btn-outline {
            color: var(--text);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
        }

        html[data-theme="light"] .btn-outline {
            background: rgba(255, 255, 255, 0.74);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-1px);
        }

        .btn-jobs {
            background: linear-gradient(135deg, var(--gold), var(--gold-2));
            color: #08111f;
            box-shadow: 0 0 38px rgba(214, 166, 80, 0.24);
        }

        .btn-jobs:hover {
            background: #ffffff;
            transform: translateY(-1px);
        }

        .theme-toggle {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.06);
            color: var(--text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s ease;
        }

        html[data-theme="light"] .theme-toggle {
            background: rgba(255,255,255,0.74);
        }

        .theme-toggle:hover {
            transform: translateY(-1px);
            background: rgba(255,255,255,0.12);
        }

        .mobile-toggle {
            display: none;
            width: 46px;
            height: 46px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text);
            font-size: 24px;
        }

        .mobile-menu {
            display: none;
            border-top: 1px solid var(--border);
            padding: 18px;
            background: rgba(5, 11, 18, 0.98);
        }

        html[data-theme="light"] .mobile-menu {
            background: rgba(255,255,255,0.98);
        }

        .mobile-menu a,
        .mobile-menu button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px;
            border-radius: 18px;
            background: rgba(255,255,255,0.055);
            margin-bottom: 10px;
            font-weight: 900;
            color: var(--text);
            border: 0;
            font-family: inherit;
            font-size: 16px;
            text-align: start;
            cursor: pointer;
        }

        html[data-theme="light"] .mobile-menu a,
        html[data-theme="light"] .mobile-menu button {
            background: rgba(15,23,42,0.055);
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 88px 0 76px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            align-items: center;
            gap: 52px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #cffafe;
            background: rgba(103, 232, 249, 0.11);
            border: 1px solid rgba(103, 232, 249, 0.2);
            border-radius: 999px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 950;
            margin-bottom: 24px;
        }

        html[data-theme="light"] .eyebrow {
            color: #155e75;
            background: rgba(34, 211, 238, 0.10);
        }

        .eyebrow-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 18px var(--cyan);
        }

        .hero h1 {
            margin: 0;
            max-width: 850px;
            font-size: clamp(46px, 7vw, 92px);
            line-height: 0.95;
            letter-spacing: -0.055em;
            font-weight: 950;
        }

        html[dir="rtl"] .hero h1,
        html[dir="rtl"] .section-title {
            letter-spacing: -0.025em;
        }

        .hero p {
            max-width: 680px;
            margin: 28px 0 0;
            color: var(--muted);
            line-height: 1.85;
            font-size: 19px;
            font-weight: 300;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 36px;
        }

        .jobs-hero-card {
            margin-top: 24px;
            border-radius: 26px;
            border: 1px solid rgba(214, 166, 80, 0.32);
            background: linear-gradient(135deg, rgba(214, 166, 80, 0.14), rgba(255,255,255,0.045));
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .jobs-hero-card strong {
            display: block;
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: 950;
        }

        .jobs-hero-card span {
            color: var(--muted);
            line-height: 1.6;
            font-weight: 300;
        }

        .success-alert {
            margin-top: 24px;
            border-radius: 22px;
            border: 1px solid rgba(103, 232, 249, 0.32);
            background: rgba(103, 232, 249, 0.11);
            color: var(--text);
            padding: 18px 20px;
            font-weight: 900;
        }

        .error-alert {
            margin-top: 24px;
            border-radius: 22px;
            border: 1px solid rgba(248, 113, 113, 0.32);
            background: rgba(248, 113, 113, 0.11);
            color: var(--text);
            padding: 18px 20px;
            font-weight: 900;
        }

        .stats-grid {
            margin-top: 42px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .stat-card {
            padding: 22px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            background: var(--card);
            backdrop-filter: blur(18px);
            box-shadow: var(--shadow);
        }

        .stat-value {
            font-size: 34px;
            font-weight: 950;
        }

        .stat-label {
            margin-top: 5px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.5;
            font-weight: 300;
        }

        .hero-visual {
            position: relative;
        }

        .hero-visual::before {
            content: "";
            position: absolute;
            inset: -38px;
            border-radius: 56px;
            background: rgba(214, 166, 80, 0.11);
            filter: blur(42px);
            z-index: -1;
        }

        .visual-card {
            position: relative;
            min-height: 560px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 42px;
            border: 1px solid var(--border);
            background:
                linear-gradient(145deg, rgba(255,255,255,0.09), rgba(255,255,255,0.035)),
                radial-gradient(circle at top, rgba(214,166,80,0.13), transparent 42%);
            box-shadow: var(--shadow);
            overflow: hidden;
            padding: 22px;
        }

        .visual-card::after {
            content: "";
            position: absolute;
            inset: auto -20% -25% -20%;
            height: 45%;
            background: linear-gradient(180deg, transparent, rgba(2, 6, 23, 0.92));
            pointer-events: none;
        }

        html[data-theme="light"] .visual-card::after {
            background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.72));
        }

        .visual-card img {
            width: 100%;
            max-height: 500px;
            object-fit: contain;
            filter: drop-shadow(0 35px 45px rgba(0,0,0,0.42));
        }

        .floating-note {
            position: absolute;
            left: 24px;
            right: 24px;
            bottom: 24px;
            z-index: 2;
            border-radius: 28px;
            border: 1px solid var(--border);
            background: rgba(2, 6, 23, 0.76);
            backdrop-filter: blur(22px);
            padding: 22px;
        }

        html[data-theme="light"] .floating-note {
            background: rgba(255, 255, 255, 0.82);
        }

        .floating-note-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gold);
            font-size: 13px;
            font-weight: 950;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .floating-note p {
            margin: 12px 0 0;
            font-size: 14px;
            line-height: 1.7;
            color: var(--muted);
        }

        .section {
            padding: 96px 0;
        }

        .section-soft {
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.032);
        }

        html[data-theme="light"] .section-soft {
            background: rgba(255,255,255,0.36);
        }

        .split {
            display: grid;
            grid-template-columns: 0.88fr 1.12fr;
            gap: 50px;
            align-items: start;
        }

        .section-label {
            color: var(--cyan);
            text-transform: uppercase;
            letter-spacing: 0.24em;
            font-size: 13px;
            font-weight: 950;
        }

        .section-title {
            margin: 16px 0 0;
            font-size: clamp(34px, 4.8vw, 58px);
            line-height: 1.05;
            letter-spacing: -0.04em;
            font-weight: 950;
        }

        .large-text {
            color: var(--muted);
            font-size: 18px;
            line-height: 1.95;
            font-weight: 300;
        }

        .center-title {
            max-width: 820px;
            margin: 0 auto;
            text-align: center;
        }

        .center-title .section-title {
            margin-left: auto;
            margin-right: auto;
        }

        .center-title .large-text {
            margin: 22px auto 0;
        }

        .services-grid {
            margin-top: 56px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .service-card {
            min-height: 300px;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: var(--card);
            padding: 30px;
            transition: 0.22s ease;
            box-shadow: 0 20px 60px rgba(0,0,0,0.24);
        }

        .service-card:hover {
            transform: translateY(-5px);
            border-color: rgba(214, 166, 80, 0.45);
            background: var(--card-2);
        }

        .service-icon {
            width: 58px;
            height: 58px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(214, 166, 80, 0.12);
            color: var(--gold);
            margin-bottom: 24px;
            border: 1px solid rgba(214, 166, 80, 0.25);
        }

        .service-icon .material-symbols-rounded {
            font-size: 30px;
        }

        .service-card h3 {
            margin: 0;
            font-size: 21px;
            line-height: 1.25;
            font-weight: 950;
        }

        .service-card p {
            margin: 15px 0 0;
            color: var(--muted);
            line-height: 1.75;
            font-weight: 300;
        }

        .learn-link {
            margin-top: 22px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--cyan);
            font-size: 14px;
            font-weight: 950;
        }

        .premium-panel {
            border-radius: 42px;
            border: 1px solid var(--border);
            background:
                linear-gradient(145deg, rgba(255,255,255,0.09), rgba(255,255,255,0.035));
            box-shadow: var(--shadow);
            padding: 44px;
        }

        .hse-grid {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 46px;
            align-items: center;
        }

        .check-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .check-card {
            border-radius: 22px;
            border: 1px solid var(--border);
            background: rgba(2, 6, 23, 0.42);
            padding: 22px;
            color: var(--text);
            font-weight: 800;
        }

        html[data-theme="light"] .check-card {
            background: rgba(255,255,255,0.72);
        }

        .check-card span {
            display: block;
            color: var(--cyan);
            font-size: 24px;
            margin-bottom: 12px;
        }

        .process-grid {
            margin-top: 52px;
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 12px;
        }

        .process-step {
            position: relative;
            min-height: 130px;
            text-align: center;
            border-radius: 24px;
            border: 1px solid var(--border);
            background: var(--card);
            padding: 20px 12px;
        }

        .process-number {
            width: 42px;
            height: 42px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: var(--cyan);
            color: #021018;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950;
        }

        .process-label {
            font-weight: 850;
            font-size: 13px;
            line-height: 1.5;
        }

        .three-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .feature-card {
            border-radius: 30px;
            border: 1px solid var(--border);
            background: var(--card);
            padding: 34px;
        }

        .feature-icon {
            color: var(--gold);
            margin-bottom: 18px;
        }

        .feature-icon .material-symbols-rounded {
            font-size: 36px;
        }

        .feature-card h3 {
            margin: 0;
            font-size: 25px;
            font-weight: 950;
        }

        .feature-card p {
            color: var(--muted);
            line-height: 1.75;
            font-weight: 300;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 0.85fr 1.15fr;
            gap: 48px;
            align-items: start;
        }

        .form-card {
            border-radius: 34px;
            border: 1px solid var(--border);
            background: var(--card);
            backdrop-filter: blur(20px);
            padding: 28px;
            box-shadow: var(--shadow);
        }

        .fields {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid var(--border);
            background: rgba(2, 6, 23, 0.72);
            color: white;
            border-radius: 18px;
            padding: 16px 16px;
            outline: none;
            font: inherit;
            font-weight: 300;
        }

        html[data-theme="light"] input,
        html[data-theme="light"] select,
        html[data-theme="light"] textarea {
            background: rgba(255,255,255,0.92);
            color: #08111f;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--cyan);
        }

        label {
            display: grid;
            gap: 8px;
            color: var(--muted);
            font-weight: 800;
            font-size: 13px;
        }

        .site-footer {
            border-top: 1px solid var(--border);
            background: rgba(2, 6, 23, 0.9);
            padding: 48px 0;
        }

        html[data-theme="light"] .site-footer {
            background: rgba(255,255,255,0.82);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr 0.8fr;
            gap: 34px;
        }

        .footer-logo {
            width: 150px;
            border-radius: 18px;
            background: white;
            padding: 12px;
        }

        .footer-text {
            max-width: 440px;
            color: var(--muted);
            line-height: 1.75;
            font-weight: 300;
        }

        .footer-list {
            display: grid;
            gap: 12px;
            color: var(--muted);
        }

        .footer-list strong {
            color: var(--text);
            font-weight: 950;
        }

        [data-en],
        [data-ar] {
            transition: opacity .15s ease;
        }

        html[lang="en"] [data-ar] {
            display: none !important;
        }

        html[lang="ar"] [data-en] {
            display: none !important;
        }

        @media (max-width: 1120px) {
            .nav {
                gap: 18px;
            }

            .header-actions .btn-primary {
                display: none;
            }
        }

        @media (max-width: 1020px) {
            .nav,
            .header-actions {
                display: none;
            }

            .mobile-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .mobile-menu.is-open {
                display: block;
            }

            .hero-grid,
            .split,
            .hse-grid,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .services-grid,
            .three-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .process-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .visual-card {
                min-height: 430px;
            }
        }

        @media (max-width: 680px) {
            .container {
                width: min(100% - 26px, var(--container));
            }

            .header-inner {
                min-height: 74px;
            }

            .brand {
                min-width: auto;
            }

            .brand-title {
                font-size: 12px;
            }

            .brand-subtitle {
                display: none;
            }

            .hero {
                padding-top: 58px;
            }

            .hero h1 {
                font-size: clamp(42px, 12vw, 58px);
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .jobs-hero-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .jobs-hero-card .btn {
                width: 100%;
            }

            .fields {
                grid-template-columns: 1fr;
            }

            .services-grid,
            .three-grid,
            .check-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .premium-panel {
                padding: 26px;
                border-radius: 30px;
            }

            .service-card,
            .feature-card {
                padding: 24px;
            }

            .visual-card {
                min-height: 360px;
                border-radius: 30px;
            }

            .floating-note {
                left: 14px;
                right: 14px;
                bottom: 14px;
                padding: 18px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

@php
    $jobsUrl = url('/jobs');
@endphp

<header class="site-header">
    <div class="container header-inner">
        <a href="{{ route('website.home') }}" class="brand">
            <img src="https://sfco.ly/sada-logo-full.png" alt="Sada Fezzan Logo" class="brand-logo">
            <div>
                <div class="brand-title">Sada Fezzan</div>
                <div class="brand-subtitle">
                    <span data-en>Oil Services Company</span>
                    <span data-ar>شركة خدمات نفطية</span>
                </div>
            </div>
        </a>

        <nav class="nav">
            <a href="{{ route('website.home') }}#about"><span data-en>About</span><span data-ar>من نحن</span></a>
            <a href="{{ route('website.home') }}#services"><span data-en>Services</span><span data-ar>الخدمات</span></a>
            <a href="{{ route('website.home') }}#hse"><span data-en>HSE</span><span data-ar>السلامة</span></a>
            <a href="{{ $jobsUrl }}"><span data-en>Jobs</span><span data-ar>الوظائف</span></a>
            <a href="{{ route('website.home') }}#vendors"><span data-en>Vendors</span><span data-ar>الموردون</span></a>
            <a href="{{ route('website.home') }}#contact"><span data-en>Contact</span><span data-ar>تواصل معنا</span></a>
        </nav>

        <div class="header-actions">
            <button type="button" class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle day and night mode">
                <span id="themeIcon" class="material-symbols-rounded">dark_mode</span>
            </button>

            <a href="{{ $jobsUrl }}" class="btn btn-jobs">
                <span class="material-symbols-rounded">work</span>
                <span data-en>Job Opportunities</span>
                <span data-ar>فرص العمل</span>
            </a>

            <button type="button" class="btn btn-outline" onclick="toggleLanguage()">
                <span class="material-symbols-rounded">translate</span>
                <span data-en>العربية</span>
                <span data-ar>English</span>
            </button>

            <a href="{{ route('website.home') }}#inquiry" class="btn btn-primary">
                <span class="material-symbols-rounded">send</span>
                <span data-en>Submit Inquiry</span>
                <span data-ar>إرسال استفسار</span>
            </a>
        </div>

        <button class="mobile-toggle" onclick="toggleMobileMenu()" aria-label="Open menu">
            <span class="material-symbols-rounded">menu</span>
        </button>
    </div>

    <div id="mobileMenu" class="mobile-menu">
        <a href="{{ $jobsUrl }}" onclick="toggleMobileMenu()">
            <span class="material-symbols-rounded">work</span>
            <span data-en>Job Opportunities</span>
            <span data-ar>فرص العمل</span>
        </a>

        <a href="{{ route('website.home') }}#about" onclick="toggleMobileMenu()">
            <span class="material-symbols-rounded">apartment</span>
            <span data-en>About</span>
            <span data-ar>من نحن</span>
        </a>

        <a href="{{ route('website.home') }}#services" onclick="toggleMobileMenu()">
            <span class="material-symbols-rounded">engineering</span>
            <span data-en>Services</span>
            <span data-ar>الخدمات</span>
        </a>

        <a href="{{ route('website.home') }}#hse" onclick="toggleMobileMenu()">
            <span class="material-symbols-rounded">health_and_safety</span>
            <span data-en>HSE</span>
            <span data-ar>السلامة</span>
        </a>

        <a href="{{ route('website.home') }}#vendors" onclick="toggleMobileMenu()">
            <span class="material-symbols-rounded">business_center</span>
            <span data-en>Vendors</span>
            <span data-ar>الموردون</span>
        </a>

        <a href="{{ route('website.home') }}#contact" onclick="toggleMobileMenu()">
            <span class="material-symbols-rounded">contact_mail</span>
            <span data-en>Contact</span>
            <span data-ar>تواصل معنا</span>
        </a>

        <button type="button" onclick="toggleLanguage(); toggleMobileMenu();">
            <span class="material-symbols-rounded">translate</span>
            <span data-en>العربية</span>
            <span data-ar>English</span>
        </button>

        <button type="button" onclick="toggleTheme(); toggleMobileMenu();">
            <span class="material-symbols-rounded">routine</span>
            <span data-en>Day / Night Mode</span>
            <span data-ar>الوضع النهاري / الليلي</span>
        </button>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer id="contact" class="site-footer">
    <div class="container footer-grid">
        <div>
            <img src="https://sfco.ly/sada-logo-full.png" alt="Sada Fezzan Logo" class="footer-logo">
            <p class="footer-text">
                <span data-en>Sada Fezzan Oil Services Company provides professional support solutions for Libya’s oil and gas sector, including manpower, mobilization, logistics, maintenance, HSE, and field coordination.</span>
                <span data-ar>شركة صدى فزان للخدمات النفطية تقدم حلول دعم احترافية لقطاع النفط والغاز في ليبيا، تشمل القوى العاملة، التعبئة، اللوجستيات، الصيانة، السلامة، والتنسيق الميداني.</span>
            </p>
        </div>

        <div class="footer-list">
            <strong><span data-en>Contact</span><span data-ar>التواصل</span></strong>
            <span><span data-en>Phone numbers will be updated</span><span data-ar>سيتم تحديث أرقام الهاتف</span></span>
            <span>info@sfco.ly</span>
        </div>

        <div class="footer-list">
            <strong><span data-en>Location</span><span data-ar>الموقع</span></strong>
            <span><span data-en>Tripoli, Libya</span><span data-ar>طرابلس، ليبيا</span></span>
            <span>sfco.ly</span>
        </div>
    </div>
</footer>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('is-open');
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);

        const icon = document.getElementById('themeIcon');

        if (icon) {
            icon.textContent = theme === 'light' ? 'light_mode' : 'dark_mode';
        }

        localStorage.setItem('sfco_theme', theme);
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
        const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
    }

    function applyLanguage(language) {
        document.documentElement.setAttribute('lang', language);

        if (language === 'ar') {
            document.documentElement.setAttribute('dir', 'rtl');
        } else {
            document.documentElement.setAttribute('dir', 'ltr');
        }

        localStorage.setItem('sfco_language', language);
    }

    function toggleLanguage() {
        const currentLanguage = document.documentElement.getAttribute('lang') || 'en';
        const nextLanguage = currentLanguage === 'en' ? 'ar' : 'en';
        applyLanguage(nextLanguage);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const savedTheme = localStorage.getItem('sfco_theme') || 'dark';
        const savedLanguage = localStorage.getItem('sfco_language') || 'en';

        applyTheme(savedTheme);
        applyLanguage(savedLanguage);
    });
</script>

@stack('scripts')
</body>
</html>
