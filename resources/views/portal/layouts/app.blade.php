<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Portal' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root{
            --bg:#eef3f8;
            --surface:#ffffff;
            --surface-soft:#f8fbff;
            --line:#d9e4ef;
            --text:#12243a;
            --muted:#6d7f97;

            --brand-navy:#17324b;
            --brand-blue:#2459d3;
            --brand-teal:#2f8a8d;
            --brand-cyan:#4ca7a8;
            --brand-gold:#c89a2f;

            --success:#16a34a;
            --warning:#d97706;
            --danger:#dc2626;
            --info:#2563eb;

            --shadow-soft:0 10px 30px rgba(15,23,42,.06);
            --shadow-strong:0 20px 55px rgba(15,23,42,.14);
            --radius-xl:30px;
            --radius-lg:22px;
            --radius-md:16px;
        }

        *{box-sizing:border-box}
        html,body{margin:0;padding:0}
        body{
            font-family:Arial,Helvetica,sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(36,89,211,.08), transparent 18%),
                radial-gradient(circle at bottom right, rgba(76,167,168,.08), transparent 20%),
                linear-gradient(180deg,#f7f9fc 0%, #edf2f8 100%);
        }

        a{text-decoration:none;color:inherit}
        button,input,select{font-family:inherit}

        .portal-shell{
            min-height:100vh;
            display:flex;
            flex-direction:column;
        }

        .portal-topbar{
            position:sticky;
            top:0;
            z-index:50;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,.08), transparent 18%),
                linear-gradient(90deg,var(--brand-navy) 0%, #234d6f 55%, var(--brand-teal) 100%);
            color:#fff;
            box-shadow:0 18px 36px rgba(15,23,42,.16);
        }

        .portal-topbar-inner{
            max-width:1450px;
            margin:0 auto;
            padding:20px 28px 10px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:24px;
            flex-wrap:wrap;
        }

        .portal-brand-side{
            display:flex;
            align-items:center;
            gap:18px;
            min-width:0;
        }

        .portal-logo-shell{
            width:110px;
            height:110px;
            border-radius:24px;
            background:rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.12);
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            padding:12px;
            flex-shrink:0;
            backdrop-filter:blur(8px);
            box-shadow:0 12px 30px rgba(0,0,0,.14);
        }

        .portal-logo-shell img{
            width:100%;
            height:100%;
            object-fit:contain;
            display:block;
        }

        .portal-logo-fallback{
            color:#fff;
            font-size:28px;
            font-weight:900;
            letter-spacing:.08em;
        }

        .portal-brand-copy{
            min-width:0;
        }

        .portal-brand-title{
            font-size:35px;
            line-height:1;
            font-weight:900;
            letter-spacing:-.04em;
            color:#fff;
        }

        .portal-brand-subtitle{
            margin-top:8px;
            font-size:15px;
            color:rgba(255,255,255,.84);
            line-height:1.5;
        }

        .portal-top-actions{
            display:flex;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            position:relative;
        }

        .portal-chip{
            display:inline-flex;
            align-items:center;
            gap:10px;
            min-height:48px;
            padding:0 16px;
            border-radius:999px;
            background:rgba(255,255,255,.10);
            border:1px solid rgba(255,255,255,.14);
            color:#fff;
            font-size:13px;
            font-weight:800;
            letter-spacing:.04em;
            backdrop-filter:blur(10px);
        }

        .portal-profile{
            display:flex;
            align-items:center;
            gap:10px;
            padding:8px 10px 8px 8px;
            border-radius:999px;
            background:rgba(255,255,255,.10);
            border:1px solid rgba(255,255,255,.14);
            color:#fff;
            min-height:52px;
            backdrop-filter:blur(10px);
        }

        .portal-avatar{
            width:38px;
            height:38px;
            border-radius:999px;
            background:rgba(255,255,255,.18);
            border:1px solid rgba(255,255,255,.14);
            display:flex;
            align-items:center;
            justify-content:center;
            overflow:hidden;
            flex-shrink:0;
        }

        .portal-avatar-text{
            font-size:14px;
            font-weight:900;
            color:#fff;
        }

        .portal-profile-name{
            max-width:190px;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
            font-size:13px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.05em;
        }

        .portal-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:44px;
            padding:0 16px;
            border-radius:999px;
            border:none;
            cursor:pointer;
            font-weight:800;
            font-size:14px;
        }

        .portal-btn--light{
            background:#fff;
            color:#17324b;
            box-shadow:0 10px 24px rgba(15,23,42,.12);
        }

        .portal-icon{
            width:20px;
            height:20px;
            display:inline-block;
            stroke:currentColor;
            stroke-width:1.9;
            fill:none;
            stroke-linecap:round;
            stroke-linejoin:round;
            flex-shrink:0;
        }

        .portal-icon--sm{
            width:18px;
            height:18px;
        }

        .portal-bell-wrap{
            position:relative;
        }

        .portal-bell-btn{
            width:48px;
            height:48px;
            border:none;
            border-radius:16px;
            background:rgba(255,255,255,.10);
            border:1px solid rgba(255,255,255,.14);
            color:#fff;
            cursor:pointer;
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:0;
            backdrop-filter:blur(10px);
        }

        .portal-bell-count{
            position:absolute;
            right:-3px;
            top:-4px;
            min-width:22px;
            height:22px;
            padding:0 6px;
            border-radius:999px;
            background:#ef4444;
            color:#fff;
            font-size:11px;
            font-weight:900;
            display:flex;
            align-items:center;
            justify-content:center;
            border:2px solid #295ddd;
        }

        .portal-bell-dropdown{
            position:absolute;
            right:0;
            top:58px;
            width:410px;
            max-width:min(94vw, 410px);
            background:#fff;
            color:var(--text);
            border:1px solid #dbe5ee;
            border-radius:24px;
            box-shadow:var(--shadow-strong);
            overflow:hidden;
            display:none;
            z-index:70;
        }

        .portal-bell-wrap:hover .portal-bell-dropdown,
        .portal-bell-wrap:focus-within .portal-bell-dropdown{
            display:block;
        }

        .portal-bell-head{
            padding:16px 18px;
            border-bottom:1px solid #e9eef5;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            background:#f8fbff;
        }

        .portal-bell-title{
            font-size:16px;
            font-weight:900;
            color:var(--text);
        }

        .portal-bell-actions{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        }

        .portal-bell-link,
        .portal-bell-link-btn{
            font-size:12px;
            font-weight:800;
            color:#2563eb;
            background:none;
            border:none;
            cursor:pointer;
            padding:0;
        }

        .portal-bell-link-btn.portal-bell-danger{
            color:#dc2626;
        }

        .portal-bell-list{
            max-height:360px;
            overflow:auto;
        }

        .portal-bell-item{
            display:block;
            padding:15px 18px;
            border-bottom:1px solid #f0f4f8;
            transition:background .18s ease;
        }

        .portal-bell-item:hover{
            background:#f8fbff;
        }

        .portal-bell-item:last-child{
            border-bottom:none;
        }

        .portal-bell-item-title{
            font-size:14px;
            font-weight:800;
            line-height:1.45;
        }

        .portal-bell-item-meta{
            margin-top:6px;
            font-size:12px;
            color:var(--muted);
            line-height:1.6;
        }

        .portal-bell-empty{
            padding:18px;
            color:var(--muted);
            font-size:14px;
        }

        .portal-nav{
            max-width:1450px;
            margin:0 auto;
            padding:0 28px 20px;
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }

        .portal-nav a{
            display:inline-flex;
            align-items:center;
            gap:10px;
            padding:12px 16px;
            border-radius:18px;
            background:rgba(255,255,255,.10);
            border:1px solid rgba(255,255,255,.12);
            color:#fff;
            font-weight:800;
            font-size:14px;
            backdrop-filter:blur(10px);
        }

        .portal-main{
            width:100%;
            max-width:1450px;
            margin:0 auto;
            padding:24px 28px 30px;
            display:flex;
            flex-direction:column;
            gap:22px;
        }

        .portal-card{
            background:var(--surface);
            border:1px solid var(--line);
            border-radius:30px;
            padding:24px;
            box-shadow:var(--shadow-soft);
        }

        .portal-card-soft{
            background:linear-gradient(180deg,#ffffff 0%, #f8fbff 100%);
        }

        .portal-title{
            font-size:34px;
            line-height:1.02;
            font-weight:900;
            letter-spacing:-.04em;
            color:var(--text);
        }

        .portal-title-md{
            font-size:25px;
            line-height:1.1;
            font-weight:900;
            letter-spacing:-.03em;
            color:var(--text);
        }

        .portal-muted{
            color:var(--muted);
            line-height:1.75;
        }

        .portal-grid-4{
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:16px;
        }

        .portal-grid-3{
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:18px;
        }

        .portal-grid-2{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:18px;
        }

        .portal-kpi{
            border:1px solid var(--line);
            border-radius:22px;
            background:#fff;
            padding:20px;
            box-shadow:0 8px 20px rgba(15,23,42,.03);
        }

        .portal-kpi-label{
            font-size:11px;
            font-weight:900;
            letter-spacing:.16em;
            text-transform:uppercase;
            color:var(--muted);
        }

        .portal-kpi-value{
            margin-top:12px;
            font-size:30px;
            line-height:1.08;
            font-weight:900;
            color:var(--text);
        }

        .portal-list{
            display:flex;
            flex-direction:column;
            gap:12px;
            margin-top:14px;
        }

        .portal-list-item{
            border:1px solid var(--line);
            border-radius:20px;
            background:#fcfdff;
            padding:16px;
        }

        .portal-list-title{
            font-weight:800;
            line-height:1.45;
        }

        .portal-list-meta{
            margin-top:6px;
            color:var(--muted);
            font-size:14px;
            line-height:1.65;
        }

        .portal-badge{
            display:inline-flex;
            align-items:center;
            padding:8px 11px;
            border-radius:999px;
            font-size:11px;
            font-weight:900;
            letter-spacing:.12em;
            text-transform:uppercase;
        }

        .portal-badge--info{background:#eff6ff;color:#1d4ed8}
        .portal-badge--success{background:#ecfdf5;color:#15803d}
        .portal-badge--warning{background:#fff7ed;color:#c2410c}
        .portal-badge--danger{background:#fef2f2;color:#b91c1c}
        .portal-badge--slate{background:#f1f5f9;color:#334155}

        .portal-empty{
            padding:18px;
            border:1px dashed var(--line);
            border-radius:18px;
            color:var(--muted);
            background:#fff;
        }

        .portal-section-head{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:12px;
            flex-wrap:wrap;
        }

        .portal-section-link{
            color:#2563eb;
            font-size:13px;
            font-weight:800;
        }

        .portal-hero{
            display:grid;
            grid-template-columns:1.2fr .8fr;
            gap:18px;
        }

        .portal-hero-main{
            min-height:180px;
            background:
                radial-gradient(circle at top right, rgba(37,99,235,.10), transparent 22%),
                linear-gradient(180deg,#ffffff 0%, #f7fbff 100%);
        }

        .portal-fast-stat{
            border:1px solid var(--line);
            border-radius:20px;
            padding:18px;
            background:#fff;
        }

        .portal-fast-stat-value{
            margin-top:10px;
            font-size:22px;
            font-weight:900;
            color:var(--text);
        }

        .portal-calendar-large{
            display:grid;
            grid-template-columns:repeat(7,minmax(0,1fr));
            gap:10px;
            margin-top:18px;
        }

        .portal-calendar-dayname{
            text-align:center;
            font-size:12px;
            font-weight:900;
            letter-spacing:.12em;
            text-transform:uppercase;
            color:var(--muted);
            padding:8px 0;
        }

        .portal-calendar-cell{
            min-height:82px;
            border-radius:20px;
            border:1px solid var(--line);
            background:#fff;
            padding:12px;
            font-weight:800;
            color:var(--text);
        }

        .portal-calendar-cell--muted{
            color:#a0aec0;
            background:#f8fafc;
        }

        .portal-calendar-cell--today{
            background:#eff6ff;
            border-color:#93c5fd;
            box-shadow:0 8px 18px rgba(37,99,235,.10);
        }

        @media (max-width: 1200px){
            .portal-grid-4{grid-template-columns:repeat(2,minmax(0,1fr))}
            .portal-grid-3{grid-template-columns:1fr}
            .portal-grid-2{grid-template-columns:1fr}
            .portal-hero{grid-template-columns:1fr}
        }

        @media (max-width: 760px){
            .portal-grid-4{grid-template-columns:1fr}
            .portal-main{padding:18px}
            .portal-topbar-inner,.portal-nav{padding-left:18px;padding-right:18px}
            .portal-title{font-size:28px}
            .portal-brand-title{font-size:28px}
            .portal-logo-shell{width:82px;height:82px}
            .portal-bell-dropdown{right:auto;left:0}
        }
        /* User Control Panel Calendar - Material minimal + day/night */
        .portal-calendar-large{
            display:grid;
            grid-template-columns:repeat(7,minmax(0,1fr));
            gap:10px;
            padding:14px;
            border-radius:28px;
            background:rgba(255,255,255,.72);
            border:1px solid rgba(15,23,42,.08);
            box-shadow:0 18px 46px rgba(15,23,42,.06);
        }

        .dark .portal-calendar-large{
            background:rgba(15,23,42,.55);
            border-color:rgba(148,163,184,.18);
            box-shadow:0 18px 46px rgba(0,0,0,.18);
        }

        .portal-calendar-dayname{
            min-height:36px;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius:16px;
            background:#eef6ff;
            color:#234b74;
            font-size:11px;
            font-weight:950;
            letter-spacing:.10em;
            text-transform:uppercase;
        }

        .dark .portal-calendar-dayname{
            background:rgba(255,255,255,.08);
            color:#cbd5e1;
        }

        .portal-calendar-cell{
            position:relative;
            min-height:78px;
            border-radius:20px;
            padding:10px;
            background:rgba(248,250,252,.92);
            border:1px solid rgba(15,23,42,.08);
            color:#0f172a;
            transition:.15s ease;
            overflow:hidden;
        }

        .portal-calendar-cell:hover{
            transform:translateY(-1px);
            box-shadow:0 12px 24px rgba(15,23,42,.08);
        }

        .dark .portal-calendar-cell{
            background:rgba(15,23,42,.45);
            border-color:rgba(148,163,184,.16);
            color:#e2e8f0;
        }

        .portal-calendar-cell--muted{
            opacity:.45;
        }

        .portal-calendar-cell--today{
            outline:2px solid #2563eb;
            outline-offset:2px;
        }

        .portal-calendar-cell .portal-calendar-date{
            font-size:13px;
            font-weight:950;
        }

        .portal-calendar-event-dot{
            position:absolute;
            right:10px;
            top:10px;
            width:10px;
            height:10px;
            border-radius:999px;
            background:#2563eb;
            box-shadow:0 0 0 5px rgba(37,99,235,.12);
        }

        .portal-calendar-event-count{
            position:absolute;
            left:10px;
            bottom:10px;
            border-radius:999px;
            padding:5px 8px;
            background:#e0f2fe;
            color:#075985;
            font-size:10px;
            font-weight:950;
        }

        .dark .portal-calendar-event-count{
            background:rgba(14,165,233,.18);
            color:#bae6fd;
        }


        /* Portal calendar interactive colored events */
        .portal-calendar-cell{
            cursor:pointer;
        }

        .portal-calendar-cell[data-events-count="0"]{
            cursor:default;
        }

        .portal-calendar-dots{
            position:absolute;
            right:10px;
            top:10px;
            display:flex;
            gap:4px;
            max-width:54px;
            flex-wrap:wrap;
            justify-content:flex-end;
        }

        .portal-calendar-event-dot{
            position:static !important;
            display:inline-flex;
            width:9px;
            height:9px;
            border-radius:999px;
            background:var(--event-color, #2563eb) !important;
            box-shadow:0 0 0 4px color-mix(in srgb, var(--event-color, #2563eb) 18%, transparent) !important;
        }

        .portal-calendar-event-count{
            background:color-mix(in srgb, var(--event-color, #2563eb) 15%, #ffffff) !important;
            color:color-mix(in srgb, var(--event-color, #2563eb) 70%, #0f172a) !important;
        }

        .dark .portal-calendar-event-count{
            background:color-mix(in srgb, var(--event-color, #2563eb) 24%, transparent) !important;
            color:#e2e8f0 !important;
        }

        .portal-day-popover{
            margin-top:14px;
            border-radius:24px;
            padding:16px;
            background:rgba(255,255,255,.94);
            border:1px solid rgba(15,23,42,.08);
            box-shadow:0 18px 42px rgba(15,23,42,.08);
        }

        .dark .portal-day-popover{
            background:rgba(15,23,42,.72);
            border-color:rgba(148,163,184,.18);
            box-shadow:0 18px 42px rgba(0,0,0,.18);
        }

        .portal-day-popover-title{
            display:flex;
            justify-content:space-between;
            gap:12px;
            align-items:center;
            color:#0f172a;
            font-size:16px;
            font-weight:950;
            letter-spacing:-.03em;
            margin-bottom:12px;
        }

        .dark .portal-day-popover-title{
            color:#ffffff;
        }

        .portal-day-close{
            border:0;
            border-radius:999px;
            width:32px;
            height:32px;
            cursor:pointer;
            background:#eef6ff;
            color:#234b74;
            font-weight:950;
        }

        .portal-day-event-list{
            display:grid;
            gap:10px;
        }

        .portal-day-event{
            display:flex;
            gap:10px;
            align-items:flex-start;
            border-radius:18px;
            padding:12px 14px;
            background:#f8fafc;
            border:1px solid rgba(15,23,42,.08);
        }

        .dark .portal-day-event{
            background:rgba(15,23,42,.42);
            border-color:rgba(148,163,184,.16);
        }

        .portal-day-event-dot{
            width:11px;
            height:11px;
            border-radius:999px;
            margin-top:5px;
            background:var(--event-color, #2563eb);
            box-shadow:0 0 0 5px color-mix(in srgb, var(--event-color, #2563eb) 15%, transparent);
            flex:0 0 auto;
        }

        .portal-day-event-main strong{
            display:block;
            color:#0f172a;
            font-size:13px;
            font-weight:950;
        }

        .dark .portal-day-event-main strong{
            color:#ffffff;
        }

        .portal-day-event-main span{
            display:block;
            margin-top:3px;
            color:#64748b;
            font-size:12px;
            font-weight:700;
        }

        .dark .portal-day-event-main span{
            color:#94a3b8;
        }

        .portal-next-event-card{
            border-radius:18px;
            padding:12px 14px;
            background:color-mix(in srgb, var(--event-color, #2563eb) 10%, #ffffff);
            border:1px solid color-mix(in srgb, var(--event-color, #2563eb) 22%, transparent);
        }

        .dark .portal-next-event-card{
            background:color-mix(in srgb, var(--event-color, #2563eb) 18%, rgba(15,23,42,.70));
            border-color:color-mix(in srgb, var(--event-color, #2563eb) 26%, transparent);
        }


        /* Portal calendar colored events + day popup */
        .portal-calendar-cell {
            cursor: pointer;
        }

        .portal-calendar-cell[data-events-count="0"] {
            cursor: default;
        }

        .portal-calendar-dots {
            position: absolute;
            right: 10px;
            top: 10px;
            display: flex;
            gap: 4px;
            max-width: 58px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .portal-calendar-event-dot {
            position: static !important;
            display: inline-flex !important;
            width: 9px !important;
            height: 9px !important;
            border-radius: 999px !important;
            background: var(--event-color, #2563eb) !important;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--event-color, #2563eb) 18%, transparent) !important;
        }

        .portal-calendar-event-count {
            background: color-mix(in srgb, var(--event-color, #2563eb) 15%, #ffffff) !important;
            color: color-mix(in srgb, var(--event-color, #2563eb) 72%, #0f172a) !important;
        }

        .dark .portal-calendar-event-count {
            background: color-mix(in srgb, var(--event-color, #2563eb) 26%, transparent) !important;
            color: #e2e8f0 !important;
        }

        .portal-day-popover {
            margin-top: 14px;
            border-radius: 24px;
            padding: 16px;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 18px 42px rgba(15,23,42,.08);
        }

        .dark .portal-day-popover {
            background: rgba(15,23,42,.72);
            border-color: rgba(148,163,184,.18);
            box-shadow: 0 18px 42px rgba(0,0,0,.18);
        }

        .portal-day-popover-title {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            color: #0f172a;
            font-size: 16px;
            font-weight: 950;
            letter-spacing: -.03em;
            margin-bottom: 12px;
        }

        .dark .portal-day-popover-title {
            color: #ffffff;
        }

        .portal-day-close {
            border: 0;
            border-radius: 999px;
            width: 32px;
            height: 32px;
            cursor: pointer;
            background: #eef6ff;
            color: #234b74;
            font-weight: 950;
        }

        .portal-day-event-list {
            display: grid;
            gap: 10px;
        }

        .portal-day-event {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            border-radius: 18px;
            padding: 12px 14px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.08);
        }

        .dark .portal-day-event {
            background: rgba(15,23,42,.42);
            border-color: rgba(148,163,184,.16);
        }

        .portal-day-event-dot {
            width: 11px;
            height: 11px;
            border-radius: 999px;
            margin-top: 5px;
            background: var(--event-color, #2563eb);
            box-shadow: 0 0 0 5px color-mix(in srgb, var(--event-color, #2563eb) 15%, transparent);
            flex: 0 0 auto;
        }

        .portal-day-event-main strong {
            display: block;
            color: #0f172a;
            font-size: 13px;
            font-weight: 950;
        }

        .dark .portal-day-event-main strong {
            color: #ffffff;
        }

        .portal-day-event-main span {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .dark .portal-day-event-main span {
            color: #94a3b8;
        }

        .portal-next-event-card {
            border-radius: 18px !important;
            padding: 12px 14px !important;
            background: color-mix(in srgb, var(--event-color, #2563eb) 10%, #ffffff) !important;
            border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 22%, transparent) !important;
        }

        .dark .portal-next-event-card {
            background: color-mix(in srgb, var(--event-color, #2563eb) 18%, rgba(15,23,42,.70)) !important;
            border-color: color-mix(in srgb, var(--event-color, #2563eb) 26%, transparent) !important;
        }


        /* FINAL portal calendar color force */
        .portal-calendar-dots {
            position: absolute !important;
            right: 10px !important;
            top: 10px !important;
            display: flex !important;
            gap: 4px !important;
            max-width: 60px !important;
            flex-wrap: wrap !important;
            justify-content: flex-end !important;
            z-index: 5 !important;
        }

        .portal-calendar-event-dot {
            position: static !important;
            display: inline-flex !important;
            width: 10px !important;
            height: 10px !important;
            min-width: 10px !important;
            min-height: 10px !important;
            border-radius: 999px !important;
            background: var(--event-color, #2563eb) !important;
            background-color: var(--event-color, #2563eb) !important;
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--event-color, #2563eb) 18%, transparent) !important;
        }

        .portal-calendar-event-count {
            position: absolute !important;
            left: 10px !important;
            bottom: 10px !important;
            border-radius: 999px !important;
            padding: 5px 8px !important;
            font-size: 10px !important;
            font-weight: 950 !important;
            background: color-mix(in srgb, var(--event-color, #2563eb) 14%, #ffffff) !important;
            color: color-mix(in srgb, var(--event-color, #2563eb) 75%, #0f172a) !important;
            border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 24%, transparent) !important;
        }

        .dark .portal-calendar-event-count {
            background: color-mix(in srgb, var(--event-color, #2563eb) 26%, transparent) !important;
            color: #e2e8f0 !important;
        }

        .portal-next-event-card {
            background: color-mix(in srgb, var(--event-color, #2563eb) 10%, #ffffff) !important;
            border-color: color-mix(in srgb, var(--event-color, #2563eb) 24%, transparent) !important;
        }

        .dark .portal-next-event-card {
            background: color-mix(in srgb, var(--event-color, #2563eb) 18%, rgba(15,23,42,.70)) !important;
            border-color: color-mix(in srgb, var(--event-color, #2563eb) 28%, transparent) !important;
        }

        .portal-day-popover {
            margin-top: 14px !important;
            border-radius: 24px !important;
            padding: 16px !important;
            background: rgba(255,255,255,.94) !important;
            border: 1px solid rgba(15,23,42,.08) !important;
            box-shadow: 0 18px 42px rgba(15,23,42,.08) !important;
        }

        .dark .portal-day-popover {
            background: rgba(15,23,42,.72) !important;
            border-color: rgba(148,163,184,.18) !important;
        }

        .portal-day-popover-title {
            display: flex !important;
            justify-content: space-between !important;
            gap: 12px !important;
            align-items: center !important;
            color: #0f172a !important;
            font-size: 16px !important;
            font-weight: 950 !important;
            letter-spacing: -.03em !important;
            margin-bottom: 12px !important;
        }

        .dark .portal-day-popover-title {
            color: #ffffff !important;
        }

        .portal-day-close {
            border: 0 !important;
            border-radius: 999px !important;
            width: 32px !important;
            height: 32px !important;
            cursor: pointer !important;
            background: #eef6ff !important;
            color: #234b74 !important;
            font-weight: 950 !important;
        }

        .portal-day-event-list {
            display: grid !important;
            gap: 10px !important;
        }

        .portal-day-event {
            display: flex !important;
            gap: 10px !important;
            align-items: flex-start !important;
            border-radius: 18px !important;
            padding: 12px 14px !important;
            background: #f8fafc !important;
            border: 1px solid rgba(15,23,42,.08) !important;
        }

        .dark .portal-day-event {
            background: rgba(15,23,42,.42) !important;
            border-color: rgba(148,163,184,.16) !important;
        }

        .portal-day-event-dot {
            width: 11px !important;
            height: 11px !important;
            border-radius: 999px !important;
            margin-top: 5px !important;
            background: var(--event-color, #2563eb) !important;
            flex: 0 0 auto !important;
        }

        .portal-day-event-main strong {
            display: block !important;
            color: #0f172a !important;
            font-size: 13px !important;
            font-weight: 950 !important;
        }

        .dark .portal-day-event-main strong {
            color: #ffffff !important;
        }

        .portal-day-event-main span {
            display: block !important;
            margin-top: 3px !important;
            color: #64748b !important;
            font-size: 12px !important;
            font-weight: 700 !important;
        }

        .dark .portal-day-event-main span {
            color: #94a3b8 !important;
        }

</style>

<style>
    /* Portal Header MD3 Minimal Premium */
    .portal-shell,
    .portal-header,
    .portal-topbar,
    .portal-hero,
    header {
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .portal-header,
    .portal-hero,
    .portal-topbar {
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.18), transparent 34%),
            linear-gradient(135deg, #0f172a 0%, #1e293b 52%, #0f766e 100%) !important;
        border-bottom: 1px solid rgba(255,255,255,.10) !important;
        box-shadow: 0 18px 42px rgba(15,23,42,.16) !important;
    }

    .portal-logo,
    .portal-brand-logo {
        border-radius: 24px !important;
        background: rgba(255,255,255,.10) !important;
        border: 1px solid rgba(255,255,255,.14) !important;
        box-shadow: 0 16px 34px rgba(0,0,0,.12) !important;
    }

    .portal-nav a,
    .portal-nav button,
    .portal-btn,
    .portal-header a,
    .portal-header button {
        border-radius: 999px !important;
        font-weight: 850 !important;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease !important;
    }

    .portal-nav a:hover,
    .portal-nav button:hover,
    .portal-btn:hover,
    .portal-header a:hover,
    .portal-header button:hover {
        transform: translateY(-1px) !important;
    }
</style>


<style>
    /* FINAL GLOBAL PORTAL MD3 HEADER */
    .portal-header,
    .portal-hero,
    .portal-topbar,
    header[class*="portal"] {
        background:
            radial-gradient(circle at 82% 18%, rgba(20,184,166,.26), transparent 32%),
            linear-gradient(135deg, #0f172a 0%, #1e3a5f 52%, #0f766e 100%) !important;
        border-bottom: 1px solid rgba(255,255,255,.10) !important;
        box-shadow: 0 18px 46px rgba(15,23,42,.18) !important;
    }

    .portal-logo,
    .portal-brand-logo,
    .portal-header img {
        border-radius: 24px !important;
    }

    .portal-nav a,
    .portal-nav button,
    .portal-header a,
    .portal-header button,
    .portal-btn {
        border-radius: 999px !important;
        font-weight: 850 !important;
        transition: transform .15s ease, box-shadow .15s ease, background .15s ease !important;
    }

    .portal-nav a:hover,
    .portal-nav button:hover,
    .portal-header a:hover,
    .portal-header button:hover,
    .portal-btn:hover {
        transform: translateY(-1px) !important;
    }

    .portal-card,
    .portal-panel,
    .portal-kpi,
    .portal-table-card,
    .portal-list-card {
        border-radius: 28px !important;
    }
</style>


<style>
    /* FINAL PORTAL HEADER MD3 — soft premium top bar + profile dropdown */
    .portal-header,
    .portal-hero,
    .portal-topbar,
    header[class*="portal"] {
        position: relative !important;
        overflow: visible !important;
        background:
            radial-gradient(circle at 78% 18%, rgba(45, 212, 191, .22), transparent 34%),
            radial-gradient(circle at 8% 12%, rgba(96, 165, 250, .14), transparent 30%),
            linear-gradient(135deg, #0f2f4f 0%, #164e63 52%, #0f766e 100%) !important;
        border-bottom: 1px solid rgba(255,255,255,.14) !important;
        box-shadow: 0 18px 50px rgba(15,23,42,.16) !important;
    }

    .portal-header::after,
    .portal-hero::after,
    .portal-topbar::after,
    header[class*="portal"]::after {
        content: "" !important;
        position: absolute !important;
        inset: 0 !important;
        pointer-events: none !important;
        background:
            linear-gradient(180deg, rgba(255,255,255,.10), transparent 55%),
            radial-gradient(circle at 50% 0%, rgba(255,255,255,.10), transparent 36%) !important;
        opacity: .9 !important;
    }

    .portal-header > *,
    .portal-hero > *,
    .portal-topbar > *,
    header[class*="portal"] > * {
        position: relative !important;
        z-index: 2 !important;
    }

    .portal-logo,
    .portal-brand-logo,
    .portal-header img {
        border-radius: 26px !important;
        background: rgba(255,255,255,.12) !important;
        border: 1px solid rgba(255,255,255,.16) !important;
        box-shadow: 0 14px 34px rgba(0,0,0,.16) !important;
    }

    .portal-nav,
    nav[class*="portal"] {
        gap: 10px !important;
    }

    .portal-nav a,
    .portal-nav button,
    .portal-header a,
    .portal-header button,
    .portal-btn {
        border-radius: 999px !important;
        min-height: 42px !important;
        padding: 0 17px !important;
        font-weight: 850 !important;
        letter-spacing: -.01em !important;
        background: rgba(255,255,255,.13) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255,255,255,.15) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.12), 0 8px 18px rgba(15,23,42,.12) !important;
        transition: transform .16s ease, background .16s ease, box-shadow .16s ease !important;
    }

    .portal-nav a:hover,
    .portal-nav button:hover,
    .portal-header a:hover,
    .portal-header button:hover,
    .portal-btn:hover {
        transform: translateY(-1px) !important;
        background: rgba(255,255,255,.20) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.18), 0 12px 24px rgba(15,23,42,.16) !important;
    }

    .sf-md3-profile-wrap {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        z-index: 99999 !important;
    }

    .sf-md3-profile-trigger {
        border: 1px solid rgba(255,255,255,.16) !important;
        border-radius: 999px !important;
        min-height: 46px !important;
        padding: 5px 10px 5px 5px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 9px !important;
        background: rgba(255,255,255,.14) !important;
        color: #fff !important;
        cursor: pointer !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.14), 0 10px 24px rgba(15,23,42,.14) !important;
        transition: transform .16s ease, background .16s ease, box-shadow .16s ease !important;
        font-family: inherit !important;
    }

    .sf-md3-profile-trigger:hover {
        transform: translateY(-1px) !important;
        background: rgba(255,255,255,.20) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.20), 0 14px 30px rgba(15,23,42,.18) !important;
    }

    .sf-md3-profile-avatar {
        width: 36px !important;
        height: 36px !important;
        border-radius: 999px !important;
        display: grid !important;
        place-items: center !important;
        background: rgba(255,255,255,.18) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255,255,255,.16) !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .04em !important;
    }

    .sf-md3-profile-name {
        font-size: 13px !important;
        font-weight: 900 !important;
        color: #fff !important;
        max-width: 160px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .sf-md3-profile-chevron {
        opacity: .82 !important;
        font-size: 12px !important;
        line-height: 1 !important;
    }

    .sf-md3-profile-menu {
        position: absolute !important;
        top: calc(100% + 12px) !important;
        right: 0 !important;
        width: 250px !important;
        display: none !important;
        border-radius: 26px !important;
        padding: 10px !important;
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.12), transparent 36%),
            rgba(255,255,255,.96) !important;
        border: 1px solid rgba(15,23,42,.10) !important;
        box-shadow: 0 24px 70px rgba(15,23,42,.22) !important;
        backdrop-filter: blur(18px) !important;
        z-index: 999999 !important;
    }

    .dark .sf-md3-profile-menu {
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.16), transparent 36%),
            rgba(15,23,42,.96) !important;
        border-color: rgba(148,163,184,.18) !important;
    }

    .sf-md3-profile-menu.is-open {
        display: block !important;
    }

    .sf-md3-profile-menu-head {
        padding: 12px 12px 10px !important;
        border-bottom: 1px solid rgba(15,23,42,.08) !important;
        margin-bottom: 8px !important;
    }

    .dark .sf-md3-profile-menu-head {
        border-color: rgba(148,163,184,.14) !important;
    }

    .sf-md3-profile-menu-title {
        color: #0f172a !important;
        font-size: 14px !important;
        font-weight: 950 !important;
        letter-spacing: -.03em !important;
        line-height: 1.25 !important;
    }

    .dark .sf-md3-profile-menu-title {
        color: #fff !important;
    }

    .sf-md3-profile-menu-sub {
        margin-top: 4px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 750 !important;
    }

    .dark .sf-md3-profile-menu-sub {
        color: #94a3b8 !important;
    }

    .sf-md3-profile-menu-link,
    .sf-md3-profile-menu-button {
        width: 100% !important;
        min-height: 44px !important;
        display: flex !important;
        align-items: center !important;
        gap: 11px !important;
        border-radius: 18px !important;
        padding: 0 12px !important;
        text-decoration: none !important;
        border: 0 !important;
        background: transparent !important;
        color: #0f172a !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        cursor: pointer !important;
        font-family: inherit !important;
        transition: background .15s ease, transform .15s ease !important;
    }

    .dark .sf-md3-profile-menu-link,
    .dark .sf-md3-profile-menu-button {
        color: #e2e8f0 !important;
    }

    .sf-md3-profile-menu-link:hover,
    .sf-md3-profile-menu-button:hover {
        background: #eef6ff !important;
        transform: translateY(-1px) !important;
    }

    .dark .sf-md3-profile-menu-link:hover,
    .dark .sf-md3-profile-menu-button:hover {
        background: rgba(255,255,255,.08) !important;
    }

    .sf-md3-profile-menu-button.danger {
        color: #dc2626 !important;
    }

    .sf-md3-profile-icon {
        width: 28px !important;
        height: 28px !important;
        border-radius: 999px !important;
        display: grid !important;
        place-items: center !important;
        background: #eef6ff !important;
        color: #234b74 !important;
        font-size: 13px !important;
        flex: 0 0 auto !important;
    }

    .sf-md3-profile-menu-button.danger .sf-md3-profile-icon {
        background: #fee2e2 !important;
        color: #dc2626 !important;
    }

    /* Header notification bubble polish */
    .portal-header [href*="notifications"],
    .portal-header button[class*="notification"],
    .portal-header a[class*="notification"],
    .portal-header [class*="bell"],
    .portal-header [class*="notif"] {
        border-radius: 999px !important;
        background: rgba(255,255,255,.13) !important;
        border: 1px solid rgba(255,255,255,.14) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.14), 0 10px 24px rgba(15,23,42,.12) !important;
    }

    @media (max-width: 760px) {
        .sf-md3-profile-name {
            max-width: 92px !important;
        }

        .sf-md3-profile-menu {
            right: -8px !important;
            width: min(250px, calc(100vw - 28px)) !important;
        }
    }
</style>

<style>
    /* FINAL HEADER TUNE: remove duplicate avatar, align profile, improve notification */
    .sf-md3-profile-wrap {
        align-self: center !important;
        transform: translateY(0) !important;
    }

    .sf-md3-profile-trigger {
        height: 46px !important;
        min-height: 46px !important;
        padding: 5px 13px 5px 6px !important;
        gap: 10px !important;
        align-items: center !important;
        transform: translateY(0) !important;
    }

    .sf-md3-profile-avatar {
        width: 34px !important;
        height: 34px !important;
        font-size: 11px !important;
        overflow: hidden !important;
        background-size: cover !important;
        background-position: center !important;
    }

    .sf-md3-profile-name {
        line-height: 1 !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    /*
      Hide the old standalone YS avatar next to the profile menu only.
      Keep the new profile avatar inside the dropdown trigger.
    */
    .sf-md3-profile-wrap > .sf-md3-profile-trigger + .sf-md3-profile-menu {
        z-index: 999999 !important;
    }

    .sf-md3-profile-wrap {
        margin-left: 0 !important;
    }

    .sf-md3-profile-wrap + .portal-user-avatar,
    .sf-md3-profile-wrap + .portal-avatar,
    .sf-md3-profile-wrap + [class*="avatar"],
    .sf-md3-profile-wrap + [class*="initial"],
    .sf-md3-profile-wrap + span,
    .sf-md3-profile-wrap + div {
        display: none !important;
    }

    /* Better notification icon button */
    .portal-header [href*="notifications"],
    .portal-header a[href*="notifications"],
    .portal-header button[class*="notification"],
    .portal-header a[class*="notification"],
    .portal-header [class*="bell"],
    .portal-header [class*="notif"] {
        width: 46px !important;
        height: 46px !important;
        min-width: 46px !important;
        min-height: 46px !important;
        padding: 0 !important;
        display: inline-grid !important;
        place-items: center !important;
        border-radius: 999px !important;
        background: rgba(255,255,255,.13) !important;
        border: 1px solid rgba(255,255,255,.18) !important;
        color: #ffffff !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.16),
            0 10px 24px rgba(15,23,42,.16) !important;
        transform: translateY(0) !important;
    }

    .portal-header [href*="notifications"]:hover,
    .portal-header a[href*="notifications"]:hover,
    .portal-header button[class*="notification"]:hover,
    .portal-header a[class*="notification"]:hover,
    .portal-header [class*="bell"]:hover,
    .portal-header [class*="notif"]:hover {
        background: rgba(255,255,255,.22) !important;
        transform: translateY(-1px) !important;
    }

    .portal-header [href*="notifications"] svg,
    .portal-header a[href*="notifications"] svg,
    .portal-header button[class*="notification"] svg,
    .portal-header a[class*="notification"] svg,
    .portal-header [class*="bell"] svg,
    .portal-header [class*="notif"] svg {
        width: 19px !important;
        height: 19px !important;
        stroke-width: 2.15 !important;
    }
</style>
<style id="portal-final-buttons-fix">
    /* ===============================
       FINAL PORTAL HEADER MATERIAL FIX
       =============================== */

    .portal-topbar,
    .portal-topbar-inner,
    .portal-top-actions,
    .portal-nav {
        overflow: visible !important;
    }

    .portal-topbar-inner {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 28px !important;
    }

    .portal-top-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 14px !important;
        white-space: nowrap !important;
    }

    .portal-chip {
        height: 48px !important;
        min-height: 48px !important;
        min-width: 232px !important;
        padding: 0 20px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        line-height: 1 !important;
        box-sizing: border-box !important;
        white-space: nowrap !important;
    }

    .portal-bell-wrap {
        position: relative !important;
        width: 48px !important;
        min-width: 48px !important;
        height: 48px !important;
        min-height: 48px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 48px !important;
        overflow: visible !important;
    }

    .portal-bell-btn {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        min-height: 48px !important;
        padding: 0 !important;
        margin: 0 !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255,255,255,.16) !important;
        border: 1px solid rgba(255,255,255,.30) !important;
        color: #ffffff !important;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .08) !important;
        cursor: pointer !important;
        line-height: 1 !important;
    }

    .portal-bell-btn:hover,
    .portal-bell-wrap.is-open .portal-bell-btn {
        background: rgba(255,255,255,.24) !important;
        border-color: rgba(255,255,255,.46) !important;
    }

    .portal-bell-btn svg,
    .portal-bell-btn .portal-icon,
    .portal-icon-material-bell {
        width: 22px !important;
        height: 22px !important;
        min-width: 22px !important;
        min-height: 22px !important;
        display: block !important;
        fill: #ffffff !important;
        stroke: none !important;
        color: #ffffff !important;
    }

    .portal-bell-dropdown {
        display: none !important;
        position: absolute !important;
        top: calc(100% + 12px) !important;
        right: 0 !important;
        width: 420px !important;
        max-width: calc(100vw - 28px) !important;
        z-index: 99999 !important;
        border-radius: 24px !important;
        background: #ffffff !important;
        border: 1px solid rgba(148, 163, 184, .30) !important;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .26) !important;
        overflow: hidden !important;
    }

    .portal-bell-wrap:hover .portal-bell-dropdown,
    .portal-bell-wrap:focus-within .portal-bell-dropdown,
    .portal-bell-wrap.is-open .portal-bell-dropdown {
        display: block !important;
    }

    .portal-bell-head {
        padding: 18px 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        border-bottom: 1px solid rgba(148, 163, 184, .22) !important;
    }

    .portal-bell-title {
        color: #0f172a !important;
        font-size: 18px !important;
        font-weight: 900 !important;
    }

    .portal-bell-actions {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-wrap: wrap !important;
    }

    .portal-bell-link,
    .portal-bell-link-btn {
        color: #0b57d0 !important;
        background: transparent !important;
        border: 0 !important;
        font-weight: 900 !important;
        font-size: 13px !important;
        cursor: pointer !important;
        text-decoration: none !important;
    }

    .portal-bell-danger {
        color: #dc2626 !important;
    }

    .portal-bell-list {
        padding: 14px 18px 18px !important;
    }

    /* ===============================
       PROFILE DROPDOWN CLICK + HOVER
       =============================== */

    .portal-profile-chip {
        height: 48px !important;
        min-height: 48px !important;
        min-width: 214px !important;
        padding: 0 16px 0 8px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        background: rgba(255,255,255,.16) !important;
        border: 1px solid rgba(255,255,255,.30) !important;
        color: #ffffff !important;
        cursor: pointer !important;
        line-height: 1 !important;
        box-sizing: border-box !important;
        white-space: nowrap !important;
        position: relative !important;
    }

    .portal-avatar {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        min-height: 38px !important;
        border-radius: 999px !important;
        overflow: hidden !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255,255,255,.22) !important;
        border: 1px solid rgba(255,255,255,.28) !important;
    }

    .portal-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }

    .portal-avatar-text {
        color: #ffffff !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
    }

    .portal-profile-name {
        max-width: 128px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        color: #ffffff !important;
        font-size: 14px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
    }

    .portal-profile-chip::after {
        content: "⌄" !important;
        color: #ffffff !important;
        font-size: 14px !important;
        font-weight: 900 !important;
        margin-left: 2px !important;
        opacity: .95 !important;
    }

    .portal-profile-dropdown-final {
        display: none;
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 270px;
        padding: 14px;
        border-radius: 24px;
        background: #ffffff;
        border: 1px solid rgba(148, 163, 184, .30);
        box-shadow: 0 24px 70px rgba(15, 23, 42, .26);
        z-index: 99999;
    }

    .portal-profile-chip:hover .portal-profile-dropdown-final,
    .portal-profile-chip:focus-within .portal-profile-dropdown-final,
    .portal-profile-chip.is-open .portal-profile-dropdown-final {
        display: block;
    }

    .portal-profile-dropdown-final-head {
        padding: 8px 10px 12px;
        margin-bottom: 8px;
        border-bottom: 1px solid rgba(148, 163, 184, .22);
    }

    .portal-profile-dropdown-final-name {
        color: #0f172a;
        font-size: 15px;
        font-weight: 900;
    }

    .portal-profile-dropdown-final-sub {
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    .portal-profile-dropdown-final-link,
    .portal-profile-dropdown-final button {
        width: 100%;
        min-height: 46px;
        border-radius: 16px;
        padding: 0 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #0f172a;
        background: transparent;
        border: none;
        text-decoration: none;
        font-size: 14px;
        font-weight: 900;
        cursor: pointer;
        text-align: left;
        box-sizing: border-box;
    }

    .portal-profile-dropdown-final-link:hover,
    .portal-profile-dropdown-final button:hover {
        background: #f1f5f9;
    }

    .portal-profile-dropdown-final .danger {
        color: #dc2626;
    }

    .portal-profile-dropdown-final svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* keep no standalone logout in top bar */
    .portal-top-actions > form[action*="logout"],
    .portal-top-actions > form .portal-btn--light {
        display: none !important;
    }

    /* ===============================
       NAVBAR SPACING FIX
       =============================== */

    .portal-nav {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .portal-nav a {
        height: 44px !important;
        min-height: 44px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        white-space: nowrap !important;
        line-height: 1 !important;
        flex: 0 0 auto !important;
        box-sizing: border-box !important;
    }

    .portal-nav a[href*="dashboard"] { min-width: 132px !important; }
    .portal-nav a[href*="salary"] { min-width: 154px !important; }
    .portal-nav a[href*="notifications"] { min-width: 168px !important; }
    .portal-nav a[href*="timeline"] { min-width: 126px !important; }
    .portal-nav a[href*="files"] { min-width: 112px !important; }

    .portal-nav a svg {
        width: 18px !important;
        min-width: 18px !important;
        height: 18px !important;
    }

    /* ===============================
       CALENDAR PREV/NEXT TEXT FIX
       =============================== */

    .portal-calendar-large a,
    .portal-calendar-large button,
    .portal-calendar-large .portal-btn,
    a[href*="month="],
    .portal-card a[href*="month="] {
        color: #0b57d0 !important;
        opacity: 1 !important;
        visibility: visible !important;
        text-shadow: none !important;
        font-weight: 900 !important;
    }

    .portal-calendar-large .portal-btn,
    .portal-card a[href*="month="] {
        background: #e8f0fe !important;
        border: 1px solid #c6dafc !important;
        box-shadow: 0 8px 20px rgba(11, 87, 208, .10) !important;
    }

    /* ===============================
       PORTAL ACTION BUTTON TEXT COLORS
       salary slips / updates / files / notifications
       =============================== */

    .portal-main button[type="submit"],
    .portal-main a.portal-btn,
    .portal-main .portal-btn,
    .portal-main a[href],
    .portal-main button {
        text-shadow: none !important;
    }

    .portal-main button[type="submit"]:not(.portal-profile-dropdown-final button),
    .portal-main .portal-btn:not(.portal-btn--danger),
    .portal-main a.portal-btn:not(.portal-btn--danger) {
        color: #0b57d0 !important;
        font-weight: 900 !important;
    }

    .portal-main button[type="submit"]:contains("Apply") {
        color: #0f5132 !important;
    }

    .portal-main a[href*="reset"],
    .portal-main a[href*="clear"],
    .portal-main button[name*="clear"],
    .portal-main .portal-btn--danger,
    .portal-main .danger {
        color: #dc2626 !important;
        font-weight: 900 !important;
    }

    .portal-main a[href*="open"],
    .portal-main a[href*="download"],
    .portal-main a[href*="files"],
    .portal-main a[href*="salary-slips"] {
        color: #0b57d0 !important;
        font-weight: 900 !important;
    }

    /* Safari-safe generic form button colors by order */
    .portal-main form button[type="submit"] {
        color: #0f766e !important;
        background: #ecfdf5 !important;
        border: 1px solid #99f6e4 !important;
    }

    .portal-main form a,
    .portal-main form button[type="reset"],
    .portal-main form .reset,
    .portal-main form [href*="reset"] {
        color: #dc2626 !important;
        background: #fef2f2 !important;
        border-color: #fecaca !important;
    }

    /* Make empty white buttons visible even if labels are blank */
    .portal-main .portal-card button,
    .portal-main .portal-card a.portal-btn {
        min-width: 92px !important;
        color: #0b57d0 !important;
    }

    @media (max-width: 900px) {
        .portal-topbar-inner {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .portal-top-actions {
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
        }

        .portal-chip,
        .portal-profile-chip {
            min-width: 0 !important;
        }

        .portal-bell-dropdown {
            right: auto !important;
            left: 0 !important;
            width: min(420px, calc(100vw - 28px)) !important;
        }
    }
</style>

</head>
<body>
@php
    $employeeCode = $portalEmployment->employee_code ?? null;
    $logoPath = public_path('portal-assets/sada-fezzan-logo-white.jpeg');
    $logoUrl = file_exists($logoPath) ? asset('portal-assets/sada-fezzan-logo-white.jpeg') : null;
    $profileImage = null;

    $portalJobTitle = 'Employee Self-Service Portal';

    try {
        $portalEmploymentForTitle = $employment ?? null;

        if (! $portalEmploymentForTitle && isset($currentIdentity) && $currentIdentity?->employment) {
            $portalEmploymentForTitle = $currentIdentity->employment;
        }

        if (! $portalEmploymentForTitle && isset($portalAccount) && $portalAccount?->currentIdentity?->employment) {
            $portalEmploymentForTitle = $portalAccount->currentIdentity->employment;
        }

        if ($portalEmploymentForTitle) {
            $portalJobTitle = $portalEmploymentForTitle->position_title
                ?: $portalEmploymentForTitle->job_title
                ?: $portalEmploymentForTitle->designation
                ?: 'Employee Self-Service Portal';
        }
    } catch (\Throwable $e) {
        $portalJobTitle = 'Employee Self-Service Portal';
    }

    /*
     * Portal profile photo source:
     * Show the latest/current Employment File titled/category "Personal Photo".
     * Employee cannot edit it from portal.
     */
    try {
        $portalEmployment = $employment ?? null;

        if (! $portalEmployment && isset($currentIdentity) && $currentIdentity?->employment) {
            $portalEmployment = $currentIdentity->employment;
        }

        if (! $portalEmployment && isset($portalAccount) && $portalAccount?->currentIdentity?->employment) {
            $portalEmployment = $portalAccount->currentIdentity->employment;
        }

        if ($portalEmployment && \Illuminate\Support\Facades\Schema::hasTable('employment_files')) {
            $fileQuery = \Illuminate\Support\Facades\DB::table('employment_files')
                ->where('employment_id', $portalEmployment->id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('employment_files', 'file_title')) {
                $fileQuery->where(function ($q) {
                    $q->where('file_title', 'like', '%Personal Photo%')
                      ->orWhere('file_title', 'like', '%Photo%')
                      ->orWhere('file_title', 'like', '%Image%');
                });
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('employment_files', 'title')) {
                $fileQuery->where(function ($q) {
                    $q->where('title', 'like', '%Personal Photo%')
                      ->orWhere('title', 'like', '%Photo%')
                      ->orWhere('title', 'like', '%Image%');
                });
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('employment_files', 'category')) {
                $fileQuery->orWhere(function ($q) use ($portalEmployment) {
                    $q->where('employment_id', $portalEmployment->id)
                      ->where('category', 'like', '%photo%');
                });
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('employment_files', 'is_current')) {
                $fileQuery->orderByDesc('is_current');
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('employment_files', 'created_at')) {
                $fileQuery->orderByDesc('created_at');
            } else {
                $fileQuery->orderByDesc('id');
            }

            $photoFile = $fileQuery->first();

            if ($photoFile) {
                $pathCandidates = [
                    $photoFile->file_path ?? null,
                    $photoFile->path ?? null,
                    $photoFile->attachment_path ?? null,
                    $photoFile->stored_path ?? null,
                ];

                foreach ($pathCandidates as $candidatePath) {
                    if (filled($candidatePath)) {
                        $candidatePath = ltrim((string) $candidatePath, '/');

                        if (str_starts_with($candidatePath, 'storage/')) {
                            $profileImage = asset($candidatePath);
                        } else {
                            $profileImage = \Illuminate\Support\Facades\Storage::disk('public')->url($candidatePath);
                        }

                        break;
                    }
                }
            }
        }

        if (! $profileImage && isset($portalAccount) && filled($portalAccount?->avatar_path)) {
            $avatarPath = ltrim((string) $portalAccount->avatar_path, '/');
            $profileImage = str_starts_with($avatarPath, 'storage/')
                ? asset($avatarPath)
                : \Illuminate\Support\Facades\Storage::disk('public')->url($avatarPath);
        }
    } catch (\Throwable $e) {
        $profileImage = null;
    }
    $initials = collect(explode(' ', $portalAccount->full_name ?? 'U'))
        ->filter()
        ->take(2)
        ->map(fn($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
@endphp

<div class="portal-shell">
    <header class="portal-topbar">
        <div class="portal-topbar-inner">
            <div class="portal-brand-side">
                <div class="portal-logo-shell">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Sada Fezzan Logo">
                    @else
                        <div class="portal-logo-fallback">SF</div>
                    @endif
                </div>

                <div class="portal-brand-copy">
                    <div class="portal-brand-title">Sada Fezzan Portal</div>
                    <div class="portal-brand-subtitle">Employee & Candidate Self Portal</div>
                </div>
            </div>

            @if(!empty($portalAccount))
                <div class="portal-top-actions">
                    @if($employeeCode)
                        <div class="portal-chip">
                            <svg class="portal-icon portal-icon--sm" viewBox="0 0 24 24">
                                <path d="M8 7h8"></path>
                                <path d="M8 12h8"></path>
                                <path d="M8 17h5"></path>
                                <rect x="4" y="4" width="16" height="16" rx="3"></rect>
                            </svg>
                            Code: {{ $employeeCode }}
                        </div>
                    @endif

                    <div class="portal-bell-wrap" tabindex="0">
                        <button type="button" class="portal-bell-btn" aria-label="Notifications">
                            <svg class="portal-icon portal-icon-material-bell" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Z"></path>
                                <path d="M18 16v-5a6 6 0 0 0-4.5-5.8V4a1.5 1.5 0 0 0-3 0v1.2A6 6 0 0 0 6 11v5l-2 2v1h16v-1l-2-2Z"></path>
                            </svg>

                            @if(($portalUnreadNotificationsCount ?? 0) > 0)
                                <span class="portal-bell-count">{{ $portalUnreadNotificationsCount > 99 ? '99+' : $portalUnreadNotificationsCount }}</span>
                            @endif
                        </button>

                        <div class="portal-bell-dropdown">
                            <div class="portal-bell-head">
                                <div class="portal-bell-title">Notifications</div>

                                <div class="portal-bell-actions">
                                    <form method="POST" action="{{ route('portal.notifications.mark-all-read') }}">
                                        @csrf
                                        <button type="submit" class="portal-bell-link-btn">Mark all read</button>
                                    </form>

                                    <form method="POST" action="{{ route('portal.notifications.clear-all') }}">
                                        @csrf
                                        <button type="submit" class="portal-bell-link-btn portal-bell-danger">Clear all</button>
                                    </form>

                                    <a class="portal-bell-link" href="{{ route('portal.notifications.index') }}">View all</a>
                                </div>
                            </div>

                            <div class="portal-bell-list">
                                @forelse(($portalHeaderNotifications ?? collect()) as $item)
                                    <a class="portal-bell-item" href="{{ route('portal.notifications.open', $item) }}">
                                        <div style="display:flex;justify-content:space-between;gap:10px;align-items:flex-start;flex-wrap:wrap;">
                                            <div class="portal-bell-item-title">{{ $item->title }}</div>

                                            @if(!$item->is_read)
                                                <span class="portal-badge portal-badge--warning">Unread</span>
                                            @endif
                                        </div>

                                        <div class="portal-bell-item-meta">
                                            {{ $item->message ?: 'No details available.' }}
                                        </div>

                                        <div class="portal-bell-item-meta">
                                            {{ $item->created_at?->format('Y-m-d H:i') ?: '-' }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="portal-bell-empty">No notifications yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="portal-profile">
                        <div class="portal-avatar">
                            @if($profileImage)
                                <img src="{{ $profileImage }}" alt="Profile">
                            @else
                                <div class="portal-avatar-text">{{ $initials ?: 'U' }}</div>
                            @endif
                        </div>

                        <div class="portal-profile-name">{{ $portalAccount->full_name }}</div>
                    </div>

                    <form method="POST" action="{{ route('portal.logout') }}">
                        @csrf
                        <button type="submit" class="portal-btn portal-btn--light">Logout</button>
                    </form>
                </div>
            @endif
        </div>

        @if(!empty($portalAccount))
            <nav class="portal-nav">
                <a href="{{ route('portal.dashboard') }}">
                    <svg class="portal-icon portal-icon--sm" viewBox="0 0 24 24">
                        <path d="M4 10.5 12 4l8 6.5"></path>
                        <path d="M6 9.5V20h12V9.5"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('portal.salary-slips.index') }}">
                    <svg class="portal-icon portal-icon--sm" viewBox="0 0 24 24">
                        <rect x="5" y="4" width="14" height="16" rx="2"></rect>
                        <path d="M8 8h8"></path>
                        <path d="M8 12h8"></path>
                        <path d="M8 16h5"></path>
                    </svg>
                    Salary Slips
                </a>

                <a href="{{ route('portal.notifications.index') }}">
                    <svg class="portal-icon portal-icon--sm" viewBox="0 0 24 24">
                        <path d="M18 16V11a6 6 0 1 0-12 0v5"></path>
                        <path d="M4 16h16"></path>
                        <path d="M10 20a2 2 0 0 0 4 0"></path>
                    </svg>
                    Notifications
                </a>

                <a href="{{ route('portal.timeline.index') }}">
                    <svg class="portal-icon portal-icon--sm" viewBox="0 0 24 24">
                        <path d="M6 7h12"></path>
                        <path d="M6 12h12"></path>
                        <path d="M6 17h8"></path>
                        <circle cx="4" cy="7" r="1"></circle>
                        <circle cx="4" cy="12" r="1"></circle>
                        <circle cx="4" cy="17" r="1"></circle>
                    </svg>
                    Updates
                </a>

                <a href="{{ route('portal.travel-tickets.index') }}" class="portal-nav-link {{ request()->routeIs('portal.travel-tickets.*') ? 'is-active' : '' }}">
                    <span>Travel & Tickets</span>
                </a>

                <a href="{{ route('portal.reimbursements.index') }}">
                <svg class="portal-icon portal-icon--sm" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4.75 7.25h14.5A1.75 1.75 0 0 1 21 9v6a1.75 1.75 0 0 1-1.75 1.75H4.75A1.75 1.75 0 0 1 3 15V9a1.75 1.75 0 0 1 1.75-1.75Z"/>
                    <circle cx="12" cy="12" r="2.25"/>
                    <path d="M6.25 9.75v4.5M17.75 9.75v4.5"/>
                </svg>
                Reimbursements
            </a>
            <a href="{{ route('portal.files.index') }}">
                    <svg class="portal-icon portal-icon--sm" viewBox="0 0 24 24">
                        <path d="M14 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V9z"></path>
                        <path d="M14 4v5h5"></path>
                    </svg>
                    Files
                </a>
            </nav>
        @endif
    </header>

    <main class="portal-main">
        @yield('content')
    </main>
</div>

<style>
    /* FORCE Portal calendar colors + clickable day events */
    .portal-calendar-cell {
        position: relative !important;
        cursor: pointer !important;
    }

    .portal-calendar-cell.sf-no-events {
        cursor: default !important;
    }

    .sf-force-portal-dots {
        position: absolute !important;
        top: 10px !important;
        right: 10px !important;
        display: flex !important;
        gap: 4px !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        max-width: 58px !important;
        z-index: 20 !important;
        pointer-events: none !important;
    }

    .sf-force-portal-dot {
        width: 10px !important;
        height: 10px !important;
        min-width: 10px !important;
        min-height: 10px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        background-color: var(--event-color, #2563eb) !important;
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--event-color, #2563eb) 18%, transparent) !important;
    }

    .portal-next-event-card,
    .sf-force-next-event {
        background: color-mix(in srgb, var(--event-color, #2563eb) 10%, #ffffff) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 28%, transparent) !important;
        box-shadow: 0 10px 24px color-mix(in srgb, var(--event-color, #2563eb) 10%, transparent) !important;
    }

    .dark .portal-next-event-card,
    .dark .sf-force-next-event {
        background: color-mix(in srgb, var(--event-color, #2563eb) 18%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 32%, transparent) !important;
    }

    .sf-force-next-badge {
        background: var(--event-color, #2563eb) !important;
        color: #fff !important;
        border-color: var(--event-color, #2563eb) !important;
    }

    .sf-portal-day-popover {
        margin-top: 14px !important;
        border-radius: 24px !important;
        padding: 16px !important;
        background: rgba(255,255,255,.96) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 18px 42px rgba(15,23,42,.08) !important;
    }

    .dark .sf-portal-day-popover {
        background: rgba(15,23,42,.78) !important;
        border-color: rgba(148,163,184,.18) !important;
    }

    .sf-portal-day-head {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 12px !important;
        margin-bottom: 12px !important;
    }

    .sf-portal-day-title {
        color: #0f172a !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        letter-spacing: -.03em !important;
    }

    .dark .sf-portal-day-title {
        color: #fff !important;
    }

    .sf-portal-day-close {
        border: 0 !important;
        border-radius: 999px !important;
        width: 32px !important;
        height: 32px !important;
        cursor: pointer !important;
        background: #eef6ff !important;
        color: #234b74 !important;
        font-weight: 950 !important;
    }

    .sf-portal-day-list {
        display: grid !important;
        gap: 10px !important;
    }

    .sf-portal-day-item {
        display: flex !important;
        gap: 10px !important;
        align-items: flex-start !important;
        border-radius: 18px !important;
        padding: 12px 14px !important;
        background: color-mix(in srgb, var(--event-color, #2563eb) 10%, #ffffff) !important;
        border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 24%, transparent) !important;
    }

    .dark .sf-portal-day-item {
        background: color-mix(in srgb, var(--event-color, #2563eb) 18%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 28%, transparent) !important;
    }

    .sf-portal-day-dot {
        width: 11px !important;
        height: 11px !important;
        margin-top: 5px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        flex: 0 0 auto !important;
    }

    .sf-portal-day-main strong {
        display: block !important;
        color: #0f172a !important;
        font-size: 13px !important;
        font-weight: 950 !important;
    }

    .dark .sf-portal-day-main strong {
        color: #fff !important;
    }

    .sf-portal-day-main span {
        display: block !important;
        margin-top: 3px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    .dark .sf-portal-day-main span {
        color: #94a3b8 !important;
    }
</style>

<script>
(function () {
    const colors = {
        ticket_travel: '#0ea5e9',
        travel: '#0ea5e9',
        mobilization: '#0ea5e9',
        rotation_start: '#10b981',
        rotation_end: '#14b8a6',
        demobilization: '#6366f1',
        visa_expiry: '#f97316',
        visa: '#f97316',
        medical_expiry: '#ef4444',
        medical: '#ef4444',
        contract_end: '#8b5cf6',
        passport_expiry: '#2563eb',
        certificate_expiry: '#7c3aed',
        desert_pass_expiry: '#d97706',
        file_expiry: '#475569',
        default: '#2563eb'
    };

    function normalizeType(text) {
        return String(text || '')
            .trim()
            .toLowerCase()
            .replace(/[\s\-\/]+/g, '_')
            .replace(/[^a-z0-9_]/g, '');
    }

    function colorFor(typeOrTitle) {
        const text = normalizeType(typeOrTitle);

        if (colors[text]) return colors[text];

        if (text.includes('ticket') || text.includes('travel') || text.includes('mobilization')) return colors.ticket_travel;
        if (text.includes('rotation_start')) return colors.rotation_start;
        if (text.includes('rotation_end')) return colors.rotation_end;
        if (text.includes('demobilization')) return colors.demobilization;
        if (text.includes('visa')) return colors.visa_expiry;
        if (text.includes('medical')) return colors.medical_expiry;
        if (text.includes('contract')) return colors.contract_end;
        if (text.includes('passport')) return colors.passport_expiry;
        if (text.includes('certificate')) return colors.certificate_expiry;
        if (text.includes('desert')) return colors.desert_pass_expiry;

        return colors.default;
    }

    function getCurrentCalendarYearMonth() {
        const label = document.querySelector('.portal-badge, .portal-calendar-month, [class*="calendar"][class*="badge"]');
        const text = label ? label.textContent.trim() : '';

        const match = text.match(/([A-Za-z]+)\s+(\d{4})/);
        if (!match) {
            const now = new Date();
            return { year: now.getFullYear(), month: now.getMonth() + 1 };
        }

        const months = {
            jan: 1, january: 1,
            feb: 2, february: 2,
            mar: 3, march: 3,
            apr: 4, april: 4,
            may: 5,
            jun: 6, june: 6,
            jul: 7, july: 7,
            aug: 8, august: 8,
            sep: 9, september: 9,
            oct: 10, october: 10,
            nov: 11, november: 11,
            dec: 12, december: 12,
        };

        return {
            year: parseInt(match[2], 10),
            month: months[match[1].toLowerCase()] || (new Date().getMonth() + 1),
        };
    }

    function dateKey(year, month, day) {
        return String(year).padStart(4, '0') + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
    }

    function collectNextEvents() {
        const eventMap = {};
        const cards = Array.from(document.querySelectorAll('.portal-update-card, .portal-next-event-card, [class*="next"] .portal-card, [class*="event"]'));

        cards.forEach(card => {
            const text = card.textContent || '';
            const dateMatch = text.match(/20\d{2}-\d{2}-\d{2}/);
            if (!dateMatch) return;

            const date = dateMatch[0];

            let title = '';
            const titleEl = card.querySelector('.portal-update-title, [class*="title"], strong, h3, h4');
            title = titleEl ? titleEl.textContent.trim() : text.split('\n').map(v => v.trim()).filter(Boolean)[0];

            let type = '';
            const badgeEl = card.querySelector('.portal-badge, [class*="badge"]');
            type = badgeEl ? badgeEl.textContent.trim() : title;

            const color = colorFor(type + ' ' + title);

            card.classList.add('sf-force-next-event');
            card.style.setProperty('--event-color', color);

            if (badgeEl) {
                badgeEl.classList.add('sf-force-next-badge');
                badgeEl.style.setProperty('--event-color', color);
            }

            if (!eventMap[date]) eventMap[date] = [];
            eventMap[date].push({
                title: title || 'Event',
                type: type || title || 'Event',
                date,
                color,
            });
        });

        return eventMap;
    }

    function removeOldGrayDots(cell) {
        Array.from(cell.querySelectorAll('.portal-calendar-event-dot, .sf-force-portal-dots')).forEach(el => el.remove());
    }

    function applyCalendarDots(eventMap) {
        const { year, month } = getCurrentCalendarYearMonth();
        const cells = Array.from(document.querySelectorAll('.portal-calendar-cell'));

        cells.forEach(cell => {
            const dayText = (cell.querySelector('.portal-calendar-date') || cell).textContent.trim().match(/\d{1,2}/);
            if (!dayText) return;

            const day = parseInt(dayText[0], 10);
            const isMuted = cell.classList.contains('portal-calendar-cell--muted');

            if (isMuted || !day || day < 1 || day > 31) {
                cell.classList.add('sf-no-events');
                return;
            }

            const key = dateKey(year, month, day);
            const events = eventMap[key] || [];

            removeOldGrayDots(cell);

            if (!events.length) {
                cell.classList.add('sf-no-events');
                cell.onclick = null;
                return;
            }

            cell.classList.remove('sf-no-events');

            const wrap = document.createElement('span');
            wrap.className = 'sf-force-portal-dots';

            events.slice(0, 4).forEach(event => {
                const dot = document.createElement('span');
                dot.className = 'sf-force-portal-dot';
                dot.style.setProperty('--event-color', event.color || colors.default);
                wrap.appendChild(dot);
            });

            cell.appendChild(wrap);

            cell.onclick = function () {
                showDayPopover(cell, key, events);
            };
        });
    }

    function ensurePopover(anchor) {
        let popover = document.getElementById('sfPortalDayPopover');

        if (!popover) {
            popover = document.createElement('div');
            popover.id = 'sfPortalDayPopover';
            popover.className = 'sf-portal-day-popover';
            popover.style.display = 'none';
            popover.innerHTML = `
                <div class="sf-portal-day-head">
                    <div class="sf-portal-day-title" id="sfPortalDayTitle">Selected Day</div>
                    <button type="button" class="sf-portal-day-close" id="sfPortalDayClose">×</button>
                </div>
                <div class="sf-portal-day-list" id="sfPortalDayList"></div>
            `;

            const calendar = document.querySelector('.portal-calendar-large') || anchor?.closest('.portal-card') || document.body;
            calendar.insertAdjacentElement('afterend', popover);

            document.getElementById('sfPortalDayClose')?.addEventListener('click', () => {
                popover.style.display = 'none';
            });
        }

        return popover;
    }

    function showDayPopover(cell, date, events) {
        const popover = ensurePopover(cell);
        const title = document.getElementById('sfPortalDayTitle');
        const list = document.getElementById('sfPortalDayList');

        if (!popover || !title || !list) return;

        title.textContent = 'Events on ' + date;
        list.innerHTML = '';

        events.forEach(event => {
            const item = document.createElement('div');
            item.className = 'sf-portal-day-item';
            item.style.setProperty('--event-color', event.color || colors.default);

            item.innerHTML = `
                <div class="sf-portal-day-dot"></div>
                <div class="sf-portal-day-main">
                    <strong>${event.title || 'Event'}</strong>
                    <span>${String(event.type || '').replaceAll('_', ' ')}</span>
                </div>
            `;

            list.appendChild(item);
        });

        popover.style.display = 'block';
        popover.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function run() {
        const eventMap = collectNextEvents();
        applyCalendarDots(eventMap);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(run, 200);
        setTimeout(run, 800);
        setTimeout(run, 1600);
    });

    window.addEventListener('load', function () {
        setTimeout(run, 300);
    });

    document.addEventListener('click', function (event) {
        const target = event.target.closest('a, button');
        if (!target) return;

        const text = target.textContent || '';
        if (text.includes('Prev') || text.includes('Next')) {
            setTimeout(run, 500);
            setTimeout(run, 1200);
        }
    });
})();
</script>


<style>
    /* FINAL PORTAL FORCE — colored calendar dots + colored next events */
    .portal-calendar-cell {
        position: relative !important;
        cursor: pointer !important;
    }

    .portal-calendar-cell.sf-no-events {
        cursor: default !important;
    }

    .sf-force-portal-dots {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        display: flex !important;
        gap: 5px !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        max-width: 62px !important;
        z-index: 50 !important;
        pointer-events: none !important;
    }

    .sf-force-portal-dot {
        width: 11px !important;
        height: 11px !important;
        min-width: 11px !important;
        min-height: 11px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        background-color: var(--event-color, #2563eb) !important;
        box-shadow: 0 0 0 5px color-mix(in srgb, var(--event-color, #2563eb) 18%, transparent) !important;
    }

    .sf-force-next-event {
        background: color-mix(in srgb, var(--event-color, #2563eb) 11%, #ffffff) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 28%, transparent) !important;
        box-shadow: 0 12px 28px color-mix(in srgb, var(--event-color, #2563eb) 10%, transparent) !important;
    }

    .sf-force-next-event::before {
        content: "" !important;
        display: block !important;
        width: 6px !important;
        align-self: stretch !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        margin-right: 12px !important;
    }

    .dark .sf-force-next-event {
        background: color-mix(in srgb, var(--event-color, #2563eb) 20%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 34%, transparent) !important;
    }

    .sf-force-next-badge {
        background: var(--event-color, #2563eb) !important;
        color: #fff !important;
        border-color: var(--event-color, #2563eb) !important;
    }

    .sf-portal-day-popover {
        margin-top: 14px !important;
        border-radius: 24px !important;
        padding: 16px !important;
        background: rgba(255,255,255,.96) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 18px 42px rgba(15,23,42,.08) !important;
    }

    .dark .sf-portal-day-popover {
        background: rgba(15,23,42,.78) !important;
        border-color: rgba(148,163,184,.18) !important;
    }

    .sf-portal-day-head {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 12px !important;
        margin-bottom: 12px !important;
    }

    .sf-portal-day-title {
        color: #0f172a !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        letter-spacing: -.03em !important;
    }

    .dark .sf-portal-day-title {
        color: #fff !important;
    }

    .sf-portal-day-close {
        border: 0 !important;
        border-radius: 999px !important;
        width: 32px !important;
        height: 32px !important;
        cursor: pointer !important;
        background: #eef6ff !important;
        color: #234b74 !important;
        font-weight: 950 !important;
    }

    .sf-portal-day-list {
        display: grid !important;
        gap: 10px !important;
    }

    .sf-portal-day-item {
        display: flex !important;
        gap: 10px !important;
        align-items: flex-start !important;
        border-radius: 18px !important;
        padding: 12px 14px !important;
        background: color-mix(in srgb, var(--event-color, #2563eb) 10%, #ffffff) !important;
        border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 24%, transparent) !important;
    }

    .dark .sf-portal-day-item {
        background: color-mix(in srgb, var(--event-color, #2563eb) 18%, rgba(15,23,42,.72)) !important;
        border-color: color-mix(in srgb, var(--event-color, #2563eb) 28%, transparent) !important;
    }

    .sf-portal-day-dot {
        width: 11px !important;
        height: 11px !important;
        margin-top: 5px !important;
        border-radius: 999px !important;
        background: var(--event-color, #2563eb) !important;
        flex: 0 0 auto !important;
    }

    .sf-portal-day-main strong {
        display: block !important;
        color: #0f172a !important;
        font-size: 13px !important;
        font-weight: 950 !important;
    }

    .dark .sf-portal-day-main strong {
        color: #fff !important;
    }

    .sf-portal-day-main span {
        display: block !important;
        margin-top: 3px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }

    .dark .sf-portal-day-main span {
        color: #94a3b8 !important;
    }
</style>

<script>
(function () {
    const colors = {
        ticket_travel: '#0ea5e9',
        travel: '#0ea5e9',
        mobilization: '#0ea5e9',
        rotation_start: '#10b981',
        rotation_end: '#14b8a6',
        demobilization: '#6366f1',
        visa_expiry: '#f97316',
        medical_expiry: '#ef4444',
        contract_end: '#8b5cf6',
        passport_expiry: '#2563eb',
        certificate_expiry: '#7c3aed',
        desert_pass_expiry: '#d97706',
        default: '#2563eb'
    };

    function normalize(text) {
        return String(text || '')
            .trim()
            .toLowerCase()
            .replace(/[\s\-\/]+/g, '_')
            .replace(/[^a-z0-9_]/g, '');
    }

    function colorFor(text) {
        const key = normalize(text);
        if (colors[key]) return colors[key];

        if (key.includes('ticket') || key.includes('travel') || key.includes('mobilization')) return colors.ticket_travel;
        if (key.includes('rotation_start')) return colors.rotation_start;
        if (key.includes('rotation_end')) return colors.rotation_end;
        if (key.includes('demobilization')) return colors.demobilization;
        if (key.includes('visa')) return colors.visa_expiry;
        if (key.includes('medical')) return colors.medical_expiry;
        if (key.includes('contract')) return colors.contract_end;
        if (key.includes('passport')) return colors.passport_expiry;
        if (key.includes('certificate')) return colors.certificate_expiry;
        if (key.includes('desert')) return colors.desert_pass_expiry;

        return colors.default;
    }

    function currentMonthYear() {
        const bodyText = document.body.innerText || '';
        const match = bodyText.match(/\b(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER|JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\s+(\d{4})\b/i);

        const now = new Date();

        if (!match) return { year: now.getFullYear(), month: now.getMonth() + 1 };

        const months = {
            jan:1,january:1,
            feb:2,february:2,
            mar:3,march:3,
            apr:4,april:4,
            may:5,
            jun:6,june:6,
            jul:7,july:7,
            aug:8,august:8,
            sep:9,september:9,
            oct:10,october:10,
            nov:11,november:11,
            dec:12,december:12,
        };

        return {
            month: months[match[1].toLowerCase()] || now.getMonth() + 1,
            year: parseInt(match[2], 10) || now.getFullYear()
        };
    }

    function keyDate(year, month, day) {
        return String(year).padStart(4, '0') + '-' + String(month).padStart(2, '0') + '-' + String(day).padStart(2, '0');
    }

    function findNextEventsCards() {
        const titleNodes = Array.from(document.querySelectorAll('h1,h2,h3,h4,.portal-title-md,.portal-card-title,.portal-title'));
        const nextTitle = titleNodes.find(el => /next events/i.test(el.textContent || ''));
        if (!nextTitle) return [];

        const container = nextTitle.closest('.portal-card, section, div') || nextTitle.parentElement;
        if (!container) return [];

        return Array.from(container.querySelectorAll('div, article, li'))
            .filter(el => {
                const text = el.textContent || '';
                return /20\d{2}-\d{2}-\d{2}/.test(text) && text.length < 300;
            })
            .filter((el, idx, arr) => {
                return !arr.some(other => other !== el && other.contains(el));
            });
    }

    function collectEvents() {
        const map = {};
        const cards = findNextEventsCards();

        cards.forEach(card => {
            const text = card.textContent || '';
            const dateMatch = text.match(/20\d{2}-\d{2}-\d{2}/);
            if (!dateMatch) return;

            const date = dateMatch[0];

            const lines = text.split('\n').map(v => v.trim()).filter(Boolean);
            const title = lines.find(v => !/20\d{2}-\d{2}-\d{2}/.test(v) && !/important/i.test(v)) || 'Event';
            const type = lines.find(v => /^[A-Z0-9_]{3,}$/.test(v)) || title;
            const color = colorFor(type + ' ' + title);

            card.classList.add('sf-force-next-event');
            card.style.setProperty('--event-color', color);

            const badge = Array.from(card.querySelectorAll('span,div')).find(el => {
                const t = (el.textContent || '').trim();
                return /^[A-Z0-9_]{3,}$/.test(t);
            });

            if (badge) {
                badge.classList.add('sf-force-next-badge');
                badge.style.setProperty('--event-color', color);
            }

            if (!map[date]) map[date] = [];
            map[date].push({ date, title, type, color });
        });

        return map;
    }

    function calendarCells() {
        return Array.from(document.querySelectorAll('.portal-calendar-cell'));
    }

    function cleanCell(cell) {
        cell.querySelectorAll('.sf-force-portal-dots, .portal-calendar-event-dot').forEach(el => el.remove());
    }

    function applyDots(eventMap) {
        const { year, month } = currentMonthYear();

        calendarCells().forEach(cell => {
            cleanCell(cell);

            const text = cell.textContent || '';
            const match = text.match(/\b\d{1,2}\b/);
            if (!match) return;

            const day = parseInt(match[0], 10);
            if (!day || cell.classList.contains('portal-calendar-cell--muted')) {
                cell.classList.add('sf-no-events');
                cell.onclick = null;
                return;
            }

            const date = keyDate(year, month, day);
            const events = eventMap[date] || [];

            if (!events.length) {
                cell.classList.add('sf-no-events');
                cell.onclick = null;
                return;
            }

            cell.classList.remove('sf-no-events');

            const dots = document.createElement('span');
            dots.className = 'sf-force-portal-dots';

            events.slice(0, 4).forEach(event => {
                const dot = document.createElement('span');
                dot.className = 'sf-force-portal-dot';
                dot.style.setProperty('--event-color', event.color || colors.default);
                dots.appendChild(dot);
            });

            cell.appendChild(dots);

            cell.onclick = function () {
                showDayDetails(date, events, cell);
            };
        });
    }

    function ensurePopover(anchor) {
        let popover = document.getElementById('sfPortalDayPopover');

        if (!popover) {
            popover = document.createElement('div');
            popover.id = 'sfPortalDayPopover';
            popover.className = 'sf-portal-day-popover';
            popover.style.display = 'none';
            popover.innerHTML = `
                <div class="sf-portal-day-head">
                    <div class="sf-portal-day-title" id="sfPortalDayTitle">Selected Day</div>
                    <button type="button" class="sf-portal-day-close" onclick="document.getElementById('sfPortalDayPopover').style.display='none'">×</button>
                </div>
                <div class="sf-portal-day-list" id="sfPortalDayList"></div>
            `;

            const calendarBox = document.querySelector('.portal-calendar-large') || anchor.closest('.portal-card') || anchor.parentElement;
            calendarBox.insertAdjacentElement('afterend', popover);
        }

        return popover;
    }

    function showDayDetails(date, events, cell) {
        const popover = ensurePopover(cell);
        const title = document.getElementById('sfPortalDayTitle');
        const list = document.getElementById('sfPortalDayList');

        title.textContent = 'Events on ' + date;
        list.innerHTML = '';

        events.forEach(event => {
            const row = document.createElement('div');
            row.className = 'sf-portal-day-item';
            row.style.setProperty('--event-color', event.color || colors.default);
            row.innerHTML = `
                <div class="sf-portal-day-dot"></div>
                <div class="sf-portal-day-main">
                    <strong>${event.title || 'Event'}</strong>
                    <span>${String(event.type || '').replaceAll('_', ' ')}</span>
                </div>
            `;
            list.appendChild(row);
        });

        popover.style.display = 'block';
        popover.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function run() {
        const events = collectEvents();
        applyDots(events);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(run, 300);
        setTimeout(run, 900);
        setTimeout(run, 1600);
    });

    window.addEventListener('load', function () {
        setTimeout(run, 300);
        setTimeout(run, 1200);
    });

    document.addEventListener('click', function (e) {
        const t = e.target.closest('a,button');
        if (!t) return;

        const label = t.textContent || '';
        if (/prev|next|april|may|june|july|august|september|october|november|december|january|february|march/i.test(label)) {
            setTimeout(run, 500);
            setTimeout(run, 1200);
        }
    });
})();
</script>
<script id="portal-final-click-fix">
document.addEventListener('DOMContentLoaded', function () {
    // Notification dropdown works by click on mobile/desktop
    document.querySelectorAll('.portal-bell-wrap').forEach(function (wrap) {
        const btn = wrap.querySelector('.portal-bell-btn');
        if (!btn) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            document.querySelectorAll('.portal-bell-wrap.is-open').forEach(function (other) {
                if (other !== wrap) other.classList.remove('is-open');
            });

            document.querySelectorAll('.portal-profile-chip.is-open').forEach(function (other) {
                other.classList.remove('is-open');
            });

            wrap.classList.toggle('is-open');
        });

        const dropdown = wrap.querySelector('.portal-bell-dropdown');
        if (dropdown) {
            dropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }
    });

    // Profile chip dropdown: add Dashboard / Profile / Logout inside the name pill
    document.querySelectorAll('.portal-profile-chip').forEach(function (chip) {
        if (chip.querySelector('.portal-profile-dropdown-final')) return;

        const name = (chip.querySelector('.portal-profile-name')?.textContent || 'Portal User').trim();

        const dropdown = document.createElement('div');
        dropdown.className = 'portal-profile-dropdown-final';
        dropdown.innerHTML = `
            <div class="portal-profile-dropdown-final-head">
                <div class="portal-profile-dropdown-final-name">${name}</div>
                <div class="portal-profile-dropdown-final-sub">User Control Panel</div>
            </div>

            <a href="/portal" class="portal-profile-dropdown-final-link">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 10.5 12 4l8 6.5"></path>
                    <path d="M6 9.5V20h12V9.5"></path>
                </svg>
                Dashboard
            </a>

            <a href="/portal" class="portal-profile-dropdown-final-link">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"></circle>
                    <path d="M4 20c1.8-4 14.2-4 16 0"></path>
                </svg>
                Profile
            </a>

            <button type="button" class="danger" data-final-logout-btn>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M10 17l5-5-5-5"></path>
                    <path d="M15 12H3"></path>
                    <path d="M21 4v16"></path>
                </svg>
                Logout
            </button>
        `;

        chip.appendChild(dropdown);
        chip.setAttribute('tabindex', '0');

        chip.addEventListener('click', function (e) {
            e.stopPropagation();

            document.querySelectorAll('.portal-profile-chip.is-open').forEach(function (other) {
                if (other !== chip) other.classList.remove('is-open');
            });

            document.querySelectorAll('.portal-bell-wrap.is-open').forEach(function (other) {
                other.classList.remove('is-open');
            });

            chip.classList.toggle('is-open');
        });

        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        dropdown.querySelector('[data-final-logout-btn]')?.addEventListener('click', function () {
            const logoutForm = Array.from(document.querySelectorAll('form')).find(function (form) {
                return (form.getAttribute('action') || '').includes('logout');
            });

            if (logoutForm) {
                logoutForm.submit();
                return;
            }

            window.location.href = '/portal/logout';
        });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.portal-bell-wrap.is-open, .portal-profile-chip.is-open').forEach(function (el) {
            el.classList.remove('is-open');
        });
    });
});
</script>


@if(!empty($portalPreviewReadonly))
    <div class="sf-portal-readonly-banner">
        <div>
            <strong>Read-only Portal Preview</strong>
            <span>You are viewing the employee portal as an administrator. Actions are disabled in preview mode.</span>
        </div>
        <a href="{{ $portalPreviewBackUrl ?? url('/admin') }}" class="sf-portal-readonly-back">Back to ERP</a>
    </div>

    <style>
        .sf-portal-readonly-banner {
            position: sticky;
            top: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 13px 22px;
            background: linear-gradient(135deg, #111827, #334155);
            color: #ffffff;
            box-shadow: 0 12px 34px rgba(15,23,42,.22);
        }

        .sf-portal-readonly-banner strong {
            display: block;
            font-size: 14px;
            font-weight: 950;
            letter-spacing: -.01em;
        }

        .sf-portal-readonly-banner span {
            display: block;
            margin-top: 3px;
            font-size: 12px;
            color: rgba(255,255,255,.78);
            font-weight: 650;
        }

        .sf-portal-readonly-back {
            border-radius: 999px;
            padding: 9px 14px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            color: #ffffff;
            text-decoration: none;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        body.sf-portal-readonly-preview form button[type="submit"],
        body.sf-portal-readonly-preview form input[type="submit"],
        body.sf-portal-readonly-preview .sf-payment-btn,
        body.sf-portal-readonly-preview .sf-md3-payment-btn--confirm,
        body.sf-portal-readonly-preview .sf-md3-payment-btn--danger,
        body.sf-portal-readonly-preview button[data-action],
        body.sf-portal-readonly-preview button[type="submit"] {
            opacity: .45 !important;
            cursor: not-allowed !important;
            filter: grayscale(.2);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.add('sf-portal-readonly-preview');

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    alert('This is a read-only portal preview. Actions are disabled.');
                    return false;
                }, true);
            });

            document.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (button) {
                button.setAttribute('disabled', 'disabled');
                button.setAttribute('title', 'Disabled in read-only preview mode');
            });
        });
    </script>
@endif


<style id="sf-cancello-portal-fixed-footer-final">
    :root {
        --sf-portal-footer-height: 66px;
    }

    body {
        padding-bottom: var(--sf-portal-footer-height) !important;
    }

    .sf-cancello-portal-fixed-footer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 80;
        height: var(--sf-portal-footer-height);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 7px 14px;
        background: linear-gradient(180deg, rgba(248,250,252,0), rgba(248,250,252,.90) 34%, rgba(248,250,252,.97));
        backdrop-filter: blur(18px);
        pointer-events: none;
    }

    .sf-cancello-portal-fixed-footer-inner {
        pointer-events: auto;
        width: fit-content;
        max-width: calc(100vw - 28px);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        border-radius: 999px;
        padding: 6px 18px;
        background: rgba(255,255,255,.88);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 12px 34px rgba(15,23,42,.10);
        color: #64748b;
        font-size: 12px;
        font-weight: 850;
        line-height: 1;
    }

    .sf-cancello-portal-powered {
        color: #94a3b8;
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sf-cancello-portal-logo {
        width: 76px;
        height: 42px;
        object-fit: contain;
        border-radius: 0;
        background: transparent;
        border: 0;
        padding: 0;
    }

    .sf-cancello-portal-logo-fallback {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #020617;
        color: #ffffff;
        font-size: 10px;
        font-weight: 950;
        border: 1px solid rgba(15,23,42,.10);
    }

    .sf-cancello-portal-dot {
        color: #cbd5e1;
    }

    .dark .sf-cancello-portal-fixed-footer {
        background: linear-gradient(180deg, rgba(2,6,23,0), rgba(2,6,23,.82) 34%, rgba(2,6,23,.95));
    }

    .dark .sf-cancello-portal-fixed-footer-inner {
        background: rgba(15,23,42,.86);
        border-color: rgba(148,163,184,.18);
        color: #94a3b8;
        box-shadow: 0 12px 34px rgba(0,0,0,.24);
    }

    @media (max-width: 640px) {
        .sf-cancello-portal-fixed-footer-inner {
            gap: 7px;
            padding-inline: 11px;
            font-size: 11px;
        }
    }

    @media print {
        body {
            padding-bottom: 0 !important;
        }

        .sf-cancello-portal-fixed-footer {
            display: none !important;
        }
    }
</style>

<footer class="sf-cancello-portal-fixed-footer">
    <div class="sf-cancello-portal-fixed-footer-inner">
        <span class="sf-cancello-portal-powered">Powered by</span>
        <img
            src="{{ asset('images/cancello-studio-logo.png') }}"
            alt="Cancello Studio"
            class="sf-cancello-portal-logo"
            onerror="this.outerHTML='<span class=&quot;sf-cancello-portal-logo-fallback&quot;>CS</span>'"
        >
        <span class="sf-cancello-portal-dot">•</span>
        <span>© 2026</span>
        <span class="sf-cancello-portal-dot">•</span>
        <span>ERP Version 1.2</span>
    </div>
</footer>

{{-- SAFE PORTAL UI FIX: notification/profile dropdown stacking only --}}

<style id="sf-real-portal-bell-dropdown-final">
    /*
     * REAL FIX:
     * Actual notification dropdown class is .portal-bell-dropdown.
     * We move it to fixed positioning when opened so it cannot sit behind nav pills.
     */

    .portal-topbar,
    .portal-topbar-inner {
        overflow: visible !important;
        position: relative !important;
        z-index: 1000 !important;
    }

    .portal-bell-wrap {
        position: relative !important;
        overflow: visible !important;
        z-index: 1000000 !important;
    }

    .portal-bell-dropdown {
        width: 460px !important;
        max-width: calc(100vw - 28px) !important;
        max-height: 520px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        box-sizing: border-box !important;
        z-index: 2147483000 !important;
        isolation: isolate !important;
        pointer-events: auto !important;
        white-space: normal !important;
    }

    body.sf-bell-fixed-open .portal-bell-dropdown {
        position: fixed !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    body.sf-bell-fixed-open .portal-nav {
        pointer-events: none !important;
        z-index: 1 !important;
    }

    .portal-bell-head {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        width: 100% !important;
        padding: 18px 20px !important;
        box-sizing: border-box !important;
    }

    .portal-bell-title {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        margin: 0 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
    }

    .portal-bell-actions {
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 8px !important;
        margin: 0 !important;
        padding: 0 !important;
        white-space: nowrap !important;
    }

    .portal-bell-actions form {
        display: inline-flex !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .portal-bell-link,
    .portal-bell-link-btn {
        min-height: 34px !important;
        height: 34px !important;
        padding: 0 10px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        pointer-events: auto !important;
        position: relative !important;
        z-index: 2147483001 !important;
        border: 0 !important;
        background: transparent !important;
        font-size: 13px !important;
        font-weight: 900 !important;
        text-decoration: none !important;
    }

    .portal-bell-link:hover,
    .portal-bell-link-btn:hover {
        background: rgba(226, 232, 240, .7) !important;
    }

    .portal-bell-list {
        max-height: 420px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    .portal-bell-item {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        white-space: normal !important;
    }

    .portal-bell-item-title,
    .portal-bell-item-desc,
    .portal-bell-item p,
    .portal-bell-item div,
    .portal-bell-item span {
        max-width: 100% !important;
        white-space: normal !important;
        overflow-wrap: break-word !important;
        word-break: normal !important;
    }

    .portal-bell-item-meta {
        display: block !important;
        white-space: nowrap !important;
        overflow-wrap: normal !important;
        word-break: keep-all !important;
        line-height: 1.2 !important;
        margin-top: 8px !important;
    }

    @media (max-width: 760px) {
        .portal-bell-dropdown {
            width: calc(100vw - 24px) !important;
            max-height: 70vh !important;
        }

        .portal-bell-head {
            align-items: flex-start !important;
            flex-direction: column !important;
        }

        .portal-bell-actions {
            width: 100% !important;
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
        }
    }
</style>

<script id="sf-real-portal-bell-dropdown-final-js">
(function () {
    function positionBellDropdown() {
        const wrap = document.querySelector('.portal-bell-wrap.is-open, .portal-bell-wrap:focus-within');
        const dropdown = wrap ? wrap.querySelector('.portal-bell-dropdown') : null;

        if (!wrap || !dropdown) {
            document.body.classList.remove('sf-bell-fixed-open');
            document.querySelectorAll('.portal-bell-dropdown').forEach(function (el) {
                el.style.removeProperty('top');
                el.style.removeProperty('left');
                el.style.removeProperty('right');
            });
            return;
        }

        const rect = wrap.getBoundingClientRect();
        const width = Math.min(460, window.innerWidth - 28);
        let left = rect.right - width;

        if (left < 14) left = 14;
        if (left + width > window.innerWidth - 14) left = window.innerWidth - width - 14;

        dropdown.style.setProperty('top', (rect.bottom + 12) + 'px', 'important');
        dropdown.style.setProperty('left', left + 'px', 'important');
        dropdown.style.setProperty('right', 'auto', 'important');

        document.body.classList.add('sf-bell-fixed-open');
    }

    document.addEventListener('click', function () {
        setTimeout(positionBellDropdown, 10);
        setTimeout(positionBellDropdown, 100);
    }, true);

    document.addEventListener('focusin', function () {
        setTimeout(positionBellDropdown, 10);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.body.classList.remove('sf-bell-fixed-open');
        }
    });

    window.addEventListener('resize', positionBellDropdown);
    window.addEventListener('scroll', positionBellDropdown, true);
    document.addEventListener('DOMContentLoaded', positionBellDropdown);
})();
</script>


<style id="sf-click-only-notification-restore-profile">
    /*
     * FINAL PORTAL HEADER RULE:
     * Notification dropdown opens ONLY by click, not hover.
     * Profile dropdown also opens ONLY by click.
     */

    .portal-topbar,
    .portal-topbar-inner,
    .portal-header {
        overflow: visible !important;
        position: relative !important;
        z-index: 1000 !important;
    }

    /* Kill hover/focus dropdown opening completely. */
    .portal-bell-wrap:hover .portal-bell-dropdown,
    .portal-bell-wrap:focus-within .portal-bell-dropdown,
    .portal-bell-wrap:hover .portal-bell-menu,
    .portal-bell-wrap:focus-within .portal-bell-menu {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* Show notification ONLY when JS adds is-open. */
    .portal-bell-wrap.is-open .portal-bell-dropdown {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        position: fixed !important;
        z-index: 2147483000 !important;
        width: 460px !important;
        max-width: calc(100vw - 28px) !important;
        max-height: 520px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        box-sizing: border-box !important;
    }

    .portal-bell-head {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 14px !important;
        padding: 18px 20px !important;
    }

    .portal-bell-title {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        white-space: nowrap !important;
        line-height: 1 !important;
    }

    .portal-bell-actions {
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 8px !important;
        white-space: nowrap !important;
    }

    .portal-bell-actions form {
        display: inline-flex !important;
        margin: 0 !important;
    }

    .portal-bell-link,
    .portal-bell-link-btn {
        min-height: 34px !important;
        height: 34px !important;
        padding: 0 11px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        pointer-events: auto !important;
        cursor: pointer !important;
        position: relative !important;
        z-index: 2147483001 !important;
    }

    .portal-bell-list {
        max-height: 420px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    .portal-bell-item {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
        white-space: normal !important;
    }

    .portal-bell-item-title,
    .portal-bell-item-desc,
    .portal-bell-item p,
    .portal-bell-item div,
    .portal-bell-item span {
        max-width: 100% !important;
        white-space: normal !important;
        overflow-wrap: break-word !important;
        word-break: normal !important;
    }

    .portal-bell-item-meta {
        white-space: nowrap !important;
        word-break: keep-all !important;
        overflow-wrap: normal !important;
    }

    /* Profile dropdown: click only, clean and visible. */
    .portal-profile,
    .portal-profile-chip {
        position: relative !important;
        overflow: visible !important;
        z-index: 1000001 !important;
        cursor: pointer !important;
    }

    .portal-profile:hover .portal-profile-dropdown-final,
    .portal-profile:focus-within .portal-profile-dropdown-final,
    .portal-profile-chip:hover .portal-profile-dropdown-final,
    .portal-profile-chip:focus-within .portal-profile-dropdown-final,
    .portal-profile:hover .portal-profile-dropdown-clean,
    .portal-profile:focus-within .portal-profile-dropdown-clean,
    .portal-profile-chip:hover .portal-profile-dropdown-clean,
    .portal-profile-chip:focus-within .portal-profile-dropdown-clean {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    .portal-profile.is-open .portal-profile-dropdown-final,
    .portal-profile-chip.is-open .portal-profile-dropdown-final,
    .portal-profile.is-open .portal-profile-dropdown-clean,
    .portal-profile-chip.is-open .portal-profile-dropdown-clean {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        position: absolute !important;
        top: calc(100% + 14px) !important;
        right: 0 !important;
        width: 320px !important;
        max-width: calc(100vw - 28px) !important;
        z-index: 2147482000 !important;
    }

    .portal-profile-dropdown-final,
    .portal-profile-dropdown-clean {
        overflow: hidden !important;
        white-space: normal !important;
    }

    .portal-profile-dropdown-final *,
    .portal-profile-dropdown-clean * {
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
        max-width: 100% !important;
    }
</style>

<script id="sf-click-only-notification-restore-profile-js">
(function () {
    function closeAllExcept(except) {
        document.querySelectorAll('.portal-bell-wrap.is-open, .portal-profile.is-open, .portal-profile-chip.is-open').forEach(function (el) {
            if (except && el === except) return;
            el.classList.remove('is-open');
        });
        if (!except || !except.classList.contains('portal-bell-wrap')) {
            document.body.classList.remove('sf-bell-fixed-open');
        }
    }

    function positionBell(wrap) {
        const dropdown = wrap ? wrap.querySelector('.portal-bell-dropdown') : null;
        if (!wrap || !dropdown || !wrap.classList.contains('is-open')) {
            document.body.classList.remove('sf-bell-fixed-open');
            return;
        }

        const rect = wrap.getBoundingClientRect();
        const width = Math.min(460, window.innerWidth - 28);
        let left = rect.right - width;

        if (left < 14) left = 14;
        if (left + width > window.innerWidth - 14) left = window.innerWidth - width - 14;

        dropdown.style.setProperty('top', (rect.bottom + 12) + 'px', 'important');
        dropdown.style.setProperty('left', left + 'px', 'important');
        dropdown.style.setProperty('right', 'auto', 'important');

        document.body.classList.add('sf-bell-fixed-open');
    }

    document.addEventListener('click', function (event) {
        const bellButton = event.target.closest('.portal-bell-btn');
        const bellWrap = event.target.closest('.portal-bell-wrap');
        const profileChip = event.target.closest('.portal-profile, .portal-profile-chip');

        if (bellButton && bellWrap) {
            event.preventDefault();
            event.stopPropagation();

            const willOpen = !bellWrap.classList.contains('is-open');
            closeAllExcept(willOpen ? bellWrap : null);

            if (willOpen) {
                bellWrap.classList.add('is-open');
                setTimeout(function () { positionBell(bellWrap); }, 0);
                setTimeout(function () { positionBell(bellWrap); }, 80);
            } else {
                bellWrap.classList.remove('is-open');
                document.body.classList.remove('sf-bell-fixed-open');
            }

            return;
        }

        if (profileChip && !event.target.closest('.portal-profile-dropdown-final, .portal-profile-dropdown-clean')) {
            event.preventDefault();
            event.stopPropagation();

            const willOpen = !profileChip.classList.contains('is-open');
            closeAllExcept(willOpen ? profileChip : null);

            if (willOpen) {
                profileChip.classList.add('is-open');
            } else {
                profileChip.classList.remove('is-open');
            }

            return;
        }

        if (event.target.closest('.portal-bell-dropdown, .portal-profile-dropdown-final, .portal-profile-dropdown-clean')) {
            return;
        }

        closeAllExcept(null);
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllExcept(null);
        }
    });

    window.addEventListener('resize', function () {
        const openBell = document.querySelector('.portal-bell-wrap.is-open');
        if (openBell) positionBell(openBell);
    });

    window.addEventListener('scroll', function () {
        const openBell = document.querySelector('.portal-bell-wrap.is-open');
        if (openBell) positionBell(openBell);
    }, true);
})();
</script>


<style id="sf-portal-profile-final-click-fix">
    /*
     * FINAL PROFILE DROPDOWN FIX
     * This overrides old duplicated dropdown CSS safely.
     */

    .portal-profile,
    .portal-profile-chip {
        position: relative !important;
        overflow: visible !important;
        cursor: pointer !important;
        z-index: 2147481000 !important;
        isolation: isolate !important;
    }

    .portal-profile::after,
    .portal-profile-chip::after {
        content: "⌄" !important;
        width: 42px !important;
        height: 42px !important;
        margin-left: 12px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 auto !important;
        background: rgba(255,255,255,.14) !important;
        color: #ffffff !important;
        font-size: 22px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
    }

    .portal-profile.is-open::after,
    .portal-profile-chip.is-open::after {
        content: "⌃" !important;
        background: rgba(255,255,255,.22) !important;
    }

    /* Hide every old broken profile menu by default. */
    .portal-profile-dropdown-final,
    .portal-profile-dropdown-clean,
    .sf-md3-profile-menu,
    .sf-portal-profile-menu {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    /* Show only our active menu. */
    .portal-profile.is-open .portal-profile-dropdown-final,
    .portal-profile-chip.is-open .portal-profile-dropdown-final {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;

        position: absolute !important;
        top: calc(100% + 14px) !important;
        right: 0 !important;
        width: 320px !important;
        max-width: calc(100vw - 24px) !important;
        padding: 14px !important;

        border-radius: 26px !important;
        background: rgba(255,255,255,.98) !important;
        border: 1px solid rgba(148,163,184,.28) !important;
        box-shadow: 0 28px 80px rgba(15,23,42,.24) !important;
        z-index: 2147482000 !important;

        overflow: hidden !important;
        box-sizing: border-box !important;
        text-align: left !important;
    }

    .portal-profile-dropdown-final *,
    .portal-profile-dropdown-final *::before,
    .portal-profile-dropdown-final *::after {
        box-sizing: border-box !important;
        max-width: 100% !important;
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
        line-height: normal !important;
        letter-spacing: normal !important;
        text-transform: none !important;
    }

    .portal-profile-dropdown-final-head {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 10px 10px 14px !important;
        margin: 0 0 10px !important;
        border-bottom: 1px solid rgba(148,163,184,.22) !important;
    }

    .portal-profile-dropdown-final-head::before {
        content: attr(data-initials) !important;
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: linear-gradient(135deg, #e0f2fe, #ccfbf1) !important;
        border: 1px solid rgba(15,118,110,.18) !important;
        color: #0f766e !important;
        font-size: 16px !important;
        font-weight: 950 !important;
    }

    .portal-profile-dropdown-final-name {
        display: block !important;
        color: #0f172a !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        margin: 0 0 4px !important;
        padding: 0 !important;
    }

    .portal-profile-dropdown-final-sub {
        display: block !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 750 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .portal-profile-dropdown-final-link,
    .portal-profile-dropdown-final button {
        width: 100% !important;
        min-height: 46px !important;
        padding: 0 13px !important;
        margin: 4px 0 !important;
        border: 0 !important;
        border-radius: 16px !important;
        background: transparent !important;
        color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 10px !important;
        text-decoration: none !important;
        font-size: 14px !important;
        font-weight: 900 !important;
        cursor: pointer !important;
        text-align: left !important;
        font-family: inherit !important;
    }

    .portal-profile-dropdown-final-link:hover,
    .portal-profile-dropdown-final button:hover {
        background: #eef6ff !important;
        color: #0b57d0 !important;
    }

    .portal-profile-dropdown-final .danger,
    .portal-profile-dropdown-final button.danger {
        color: #dc2626 !important;
    }

    .portal-profile-dropdown-final .danger:hover,
    .portal-profile-dropdown-final button.danger:hover {
        background: #fef2f2 !important;
        color: #b91c1c !important;
    }

    .portal-profile-dropdown-final svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        stroke: currentColor !important;
        fill: none !important;
        stroke-width: 2.2 !important;
    }
</style>

<script id="sf-portal-profile-final-click-fix-js">
(function () {
    function initialsFromName(name) {
        return String(name || 'User')
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map(function (part) { return part.charAt(0).toUpperCase(); })
            .join('') || 'U';
    }

    function closeProfiles(except) {
        document.querySelectorAll('.portal-profile.is-open, .portal-profile-chip.is-open').forEach(function (el) {
            if (except && el === except) return;
            el.classList.remove('is-open');
        });
    }

    function ensureProfileMenu(chip) {
        if (!chip) return;

        let menu = chip.querySelector('.portal-profile-dropdown-final');
        const name = (chip.querySelector('.portal-profile-name')?.textContent || 'Portal User').trim();
        const initials = (chip.querySelector('.portal-avatar-text')?.textContent || initialsFromName(name)).trim();

        if (menu) {
            const head = menu.querySelector('.portal-profile-dropdown-final-head');
            if (head) head.setAttribute('data-initials', initials);
            return;
        }

        menu = document.createElement('div');
        menu.className = 'portal-profile-dropdown-final';
        menu.setAttribute('data-portal-profile-menu', '1');

        menu.innerHTML = `
            <div class="portal-profile-dropdown-final-head" data-initials="${initials}">
                <div>
                    <div class="portal-profile-dropdown-final-name">${name}</div>
                    <div class="portal-profile-dropdown-final-sub">Employee Self-Service Portal</div>
                </div>
            </div>

            <a href="/portal" class="portal-profile-dropdown-final-link">
                <svg viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/><path d="M9.5 20v-6h5v6"/></svg>
                Dashboard
            </a>

            <a href="/portal/files" class="portal-profile-dropdown-final-link">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                Files
            </a>

            <a href="/portal/notifications" class="portal-profile-dropdown-final-link">
                <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notifications
            </a>

            <form method="POST" action="/portal/logout" class="portal-profile-dropdown-final-form">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                <button type="submit" class="danger">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                    Logout
                </button>
            </form>
        `;

        chip.appendChild(menu);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.portal-profile, .portal-profile-chip').forEach(ensureProfileMenu);
    });

    document.addEventListener('click', function (event) {
        const chip = event.target.closest('.portal-profile, .portal-profile-chip');

        if (chip && !event.target.closest('.portal-profile-dropdown-final')) {
            event.preventDefault();
            event.stopPropagation();

            ensureProfileMenu(chip);

            const willOpen = !chip.classList.contains('is-open');
            closeProfiles(willOpen ? chip : null);

            if (willOpen) {
                chip.classList.add('is-open');
                document.querySelectorAll('.portal-bell-wrap.is-open').forEach(function (bell) {
                    bell.classList.remove('is-open');
                });
                document.body.classList.remove('sf-bell-fixed-open');
            } else {
                chip.classList.remove('is-open');
            }

            return;
        }

        if (event.target.closest('.portal-profile-dropdown-final')) {
            return;
        }

        closeProfiles(null);
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeProfiles(null);
    });
})();
</script>


<style id="sf-portal-body-profile-dropdown-final">
    /*
     * Body-level profile dropdown.
     * This avoids all header/pill overflow conflicts.
     */

    .portal-profile,
    .portal-profile-chip {
        cursor: pointer !important;
        overflow: visible !important;
        position: relative !important;
    }

    /* Make only YS/photo circle bigger inside same pill. */
    .portal-profile .portal-avatar,
    .portal-profile-chip .portal-avatar {
        width: 64px !important;
        height: 64px !important;
        min-width: 64px !important;
        border-radius: 999px !important;
        border: 3px solid rgba(255,255,255,.75) !important;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.22), 0 8px 18px rgba(15,23,42,.12) !important;
        overflow: hidden !important;
    }

    .portal-profile .portal-avatar img,
    .portal-profile-chip .portal-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 999px !important;
        display: block !important;
    }

    .portal-profile-chip .portal-avatar-text {
        font-size: 25px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
    }

    /* Synchronize arrow inside its own circle. */
    .portal-profile::after,
    .portal-profile-chip::after {
        content: "⌄" !important;
        width: 44px !important;
        height: 44px !important;
        min-width: 44px !important;
        margin-left: 12px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(255,255,255,.16) !important;
        color: #ffffff !important;
        font-size: 22px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
        transform: translateY(0) !important;
    }

    .portal-profile.is-open::after,
    .portal-profile-chip.is-open::after {
        content: "⌃" !important;
        background: rgba(255,255,255,.25) !important;
    }

    /* Kill old internal dropdowns so they never create garbage text. */
    .portal-profile-dropdown-final,
    .portal-profile-dropdown-clean,
    .sf-md3-profile-menu,
    .sf-portal-profile-menu {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    #sfPortalBodyProfileMenu {
        position: fixed !important;
        width: 320px !important;
        max-width: calc(100vw - 24px) !important;
        padding: 14px !important;
        border-radius: 26px !important;
        background: rgba(255,255,255,.98) !important;
        border: 1px solid rgba(148,163,184,.30) !important;
        box-shadow: 0 28px 80px rgba(15,23,42,.28) !important;
        z-index: 2147483647 !important;
        display: none !important;
        overflow: hidden !important;
        box-sizing: border-box !important;
    }

    #sfPortalBodyProfileMenu.is-open {
        display: block !important;
    }

    #sfPortalBodyProfileMenu,
    #sfPortalBodyProfileMenu * {
        box-sizing: border-box !important;
        font-family: inherit !important;
        line-height: normal !important;
        letter-spacing: normal !important;
        text-transform: none !important;
        white-space: normal !important;
        word-break: normal !important;
        overflow-wrap: break-word !important;
    }

    .sf-body-profile-head {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        padding: 10px 10px 14px !important;
        margin-bottom: 10px !important;
        border-bottom: 1px solid rgba(148,163,184,.24) !important;
    }

    .sf-body-profile-avatar {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        border-radius: 999px !important;
        overflow: hidden !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: linear-gradient(135deg, #e0f2fe, #ccfbf1) !important;
        border: 1px solid rgba(15,118,110,.18) !important;
        color: #0f766e !important;
        font-size: 17px !important;
        font-weight: 950 !important;
    }

    .sf-body-profile-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }

    .sf-body-profile-name {
        color: #0f172a !important;
        font-size: 16px !important;
        font-weight: 950 !important;
        margin-bottom: 4px !important;
    }

    .sf-body-profile-email {
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 750 !important;
    }

    .sf-body-profile-link,
    .sf-body-profile-button {
        width: 100% !important;
        min-height: 46px !important;
        padding: 0 13px !important;
        margin: 4px 0 !important;
        border: 0 !important;
        border-radius: 16px !important;
        background: transparent !important;
        color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 10px !important;
        text-decoration: none !important;
        font-size: 14px !important;
        font-weight: 900 !important;
        cursor: pointer !important;
        text-align: left !important;
    }

    .sf-body-profile-link:hover,
    .sf-body-profile-button:hover {
        background: #eef6ff !important;
        color: #0b57d0 !important;
    }

    .sf-body-profile-button.danger {
        color: #dc2626 !important;
    }

    .sf-body-profile-button.danger:hover {
        background: #fef2f2 !important;
        color: #b91c1c !important;
    }

    .sf-body-profile-link svg,
    .sf-body-profile-button svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        stroke: currentColor !important;
        fill: none !important;
        stroke-width: 2.2 !important;
        stroke-linecap: round !important;
        stroke-linejoin: round !important;
    }
</style>

<script id="sf-portal-body-profile-dropdown-final-js">
(function () {
    function initialsFromName(name) {
        return String(name || 'User')
            .trim()
            .split(/\s+/)
            .filter(Boolean)
            .slice(0, 2)
            .map(function (part) { return part.charAt(0).toUpperCase(); })
            .join('') || 'U';
    }

    function findProfileChip() {
        return document.querySelector('.portal-profile-chip') ||
               document.querySelector('.portal-profile') ||
               document.querySelector('[data-portal-profile-chip]');
    }

    function getProfileData(chip) {
        const name = (chip?.querySelector('.portal-profile-name')?.textContent || 'Portal User').trim();
        const email =
            (chip?.getAttribute('data-email') ||
             document.getElementById('sfPortalAccountEmailSource')?.textContent ||
             document.querySelector('.portal-profile-dropdown-final-sub')?.textContent ||
             document.querySelector('.portal-profile-dropdown-clean-email')?.textContent ||
             '').trim();

        const img = chip?.getAttribute('data-profile-image') || chip?.querySelector('.portal-avatar img')?.getAttribute('src') || '';
        const initials = (chip?.querySelector('.portal-avatar-text')?.textContent || initialsFromName(name)).trim();
        const jobTitle = (chip?.getAttribute('data-job-title') || 'Employee Self-Service Portal').trim();

        return { name, email, img, initials, jobTitle };
    }

    function ensureMenu(chip) {
        let menu = document.getElementById('sfPortalBodyProfileMenu');
        const data = getProfileData(chip);

        if (!menu) {
            menu = document.createElement('div');
            menu.id = 'sfPortalBodyProfileMenu';
            document.body.appendChild(menu);
        }

        const avatarHtml = data.img
            ? `<img src="${data.img}" alt="Profile">`
            : data.initials;

        menu.innerHTML = `
            <div class="sf-body-profile-head">
                <div class="sf-body-profile-avatar">${avatarHtml}</div>
                <div>
                    <div class="sf-body-profile-name">${data.name}</div>
                    <div class="sf-body-profile-email">${data.email || 'No email registered'}</div>
                </div>
            </div>

            <a class="sf-body-profile-link" href="/portal">
                <svg viewBox="0 0 24 24"><path d="M3 11.5 12 4l9 7.5"/><path d="M5 10.5V20h14v-9.5"/><path d="M9.5 20v-6h5v6"/></svg>
                Dashboard
            </a>
<a class="sf-body-profile-link" href="/portal/notifications">
                <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                Notifications
            </a>

            <form method="POST" action="/portal/logout">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                <button class="sf-body-profile-button danger" type="submit">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                    Logout
                </button>
            </form>
        `;

        return menu;
    }

    function positionMenu(chip, menu) {
        const rect = chip.getBoundingClientRect();
        const menuWidth = 320;
        const gap = 12;

        let left = rect.right - menuWidth;
        left = Math.max(12, Math.min(left, window.innerWidth - menuWidth - 12));

        let top = rect.bottom + gap;
        if (top + 260 > window.innerHeight) {
            top = Math.max(12, rect.top - 270);
        }

        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
    }

    function closeMenu() {
        const menu = document.getElementById('sfPortalBodyProfileMenu');
        if (menu) menu.classList.remove('is-open');

        document.querySelectorAll('.portal-profile.is-open, .portal-profile-chip.is-open').forEach(function (el) {
            el.classList.remove('is-open');
        });
    }

    function openMenu(chip) {
        const menu = ensureMenu(chip);
        positionMenu(chip, menu);
        menu.classList.add('is-open');
        chip.classList.add('is-open');

        document.querySelectorAll('.portal-bell-wrap.is-open').forEach(function (bell) {
            bell.classList.remove('is-open');
        });
        document.body.classList.remove('sf-bell-fixed-open');
    }

    document.addEventListener('click', function (event) {
        const menu = document.getElementById('sfPortalBodyProfileMenu');
        const chip = event.target.closest('.portal-profile, .portal-profile-chip, [data-portal-profile-chip]');

        if (chip) {
            event.preventDefault();
            event.stopPropagation();

            const isOpen = menu && menu.classList.contains('is-open');

            if (isOpen) {
                closeMenu();
            } else {
                openMenu(chip);
            }

            return;
        }

        if (menu && event.target.closest('#sfPortalBodyProfileMenu')) {
            return;
        }

        closeMenu();
    }, true);

    window.addEventListener('resize', function () {
        const menu = document.getElementById('sfPortalBodyProfileMenu');
        const chip = findProfileChip();

        if (menu && menu.classList.contains('is-open') && chip) {
            positionMenu(chip, menu);
        }
    });

    window.addEventListener('scroll', function () {
        const menu = document.getElementById('sfPortalBodyProfileMenu');
        const chip = findProfileChip();

        if (menu && menu.classList.contains('is-open') && chip) {
            positionMenu(chip, menu);
        }
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeMenu();
    });
})();
</script>


<style id="sf-force-hide-old-portal-profile-dropdowns-final">
    /*
     * Keep only the body-level dropdown #sfPortalBodyProfileMenu.
     * Hide every old internal profile dropdown that was causing the ghost/back menu.
     */
    .portal-profile > .portal-profile-dropdown-final,
    .portal-profile-chip > .portal-profile-dropdown-final,
    .portal-profile > .portal-profile-dropdown-clean,
    .portal-profile-chip > .portal-profile-dropdown-clean,
    .portal-profile > .sf-md3-profile-menu,
    .portal-profile-chip > .sf-md3-profile-menu,
    .portal-profile > .sf-portal-profile-menu,
    .portal-profile-chip > .sf-portal-profile-menu,
    [data-portal-profile-chip] > .portal-profile-dropdown-final,
    [data-portal-profile-chip] > .portal-profile-dropdown-clean {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        width: 0 !important;
        height: 0 !important;
        max-width: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        inset: auto !important;
        transform: scale(0) !important;
    }

    #sfPortalBodyProfileMenu {
        display: none !important;
    }

    #sfPortalBodyProfileMenu.is-open {
        display: block !important;
    }

    .sf-body-profile-title {
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 850 !important;
        margin-top: 3px !important;
    }
</style>


<style id="sf-profile-dropdown-email-only-final">
    #sfPortalBodyProfileMenu .sf-body-profile-title {
        display: none !important;
    }

    #sfPortalBodyProfileMenu .sf-body-profile-email {
        display: block !important;
        margin-top: 4px !important;
        color: #64748b !important;
        font-size: 14px !important;
        font-weight: 850 !important;
        line-height: 1.25 !important;
        word-break: break-word !important;
    }
</style>

<style id="sf-force-profile-email-visible-final">
    #sfPortalBodyProfileMenu .sf-body-profile-email {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        margin-top: 5px !important;
        color: #64748b !important;
        font-size: 14px !important;
        font-weight: 850 !important;
        line-height: 1.25 !important;
        max-width: 210px !important;
        white-space: normal !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
    }

    #sfPortalBodyProfileMenu .sf-body-profile-title {
        display: none !important;
    }
</style>


<span id="sfPortalAccountEmailSource" style="display:none !important;">{{ $portalAccount->email ?? '' }}</span>

</body>
</html>
