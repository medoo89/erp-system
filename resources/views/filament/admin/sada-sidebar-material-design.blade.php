{{-- SADA FEZZAN GLOBAL SIDEBAR - PREMIUM CLEAN STYLE --}}
<style id="sada-sidebar-premium-clean-final">
    /*
     | Sada Fezzan ERP Premium Sidebar
     | New custom style:
     | - smaller clean logo area
     | - premium white / dark sidebar
     | - compact navigation
     | - clear groups
     | - no green active icon
     | - blue/navy active pill
     */

    :root {
        --sf-sidebar-bg: #ffffff;
        --sf-sidebar-bg-soft: #f8fbfd;
        --sf-sidebar-border: rgba(15, 23, 42, .075);
        --sf-sidebar-text: #475569;
        --sf-sidebar-title: #0f2f4f;
        --sf-sidebar-muted: #8a98ab;

        --sf-sidebar-blue: #2563eb;
        --sf-sidebar-cyan: #22d3ee;
        --sf-sidebar-navy: #17324b;
        --sf-sidebar-active: linear-gradient(135deg, #17324b 0%, #1f4664 42%, #2563eb 125%);
        --sf-sidebar-active-shadow: 0 14px 34px rgba(37, 99, 235, .24);

        --sf-sidebar-icon-bg: #f1f5f9;
        --sf-sidebar-icon-border: rgba(15, 23, 42, .075);
        --sf-sidebar-hover: rgba(37, 99, 235, .055);

        --sf-sidebar-radius-lg: 22px;
        --sf-sidebar-radius-md: 17px;
    }

    .dark {
        --sf-sidebar-bg: #071525;
        --sf-sidebar-bg-soft: #0b1d31;
        --sf-sidebar-border: rgba(148, 163, 184, .13);
        --sf-sidebar-text: #cbd5e1;
        --sf-sidebar-title: #f8fafc;
        --sf-sidebar-muted: #94a3b8;

        --sf-sidebar-icon-bg: rgba(255,255,255,.07);
        --sf-sidebar-icon-border: rgba(255,255,255,.10);
        --sf-sidebar-hover: rgba(255,255,255,.055);
        --sf-sidebar-active-shadow: 0 16px 38px rgba(0, 0, 0, .34);
    }

    /* =========================
       Sidebar shell
       ========================= */

    .fi-sidebar,
    aside.fi-sidebar {
        width: 18rem !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 32%),
            var(--sf-sidebar-bg) !important;
        border-right: 1px solid var(--sf-sidebar-border) !important;
        box-shadow: 10px 0 38px rgba(15, 23, 42, .055) !important;
        font-family: "Inter", "Almarai", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        overflow: hidden !important;
    }

    .dark .fi-sidebar,
    .dark aside.fi-sidebar {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 32%),
            var(--sf-sidebar-bg) !important;
        box-shadow: 12px 0 42px rgba(0, 0, 0, .34) !important;
    }

    /* =========================
       Logo / header
       ========================= */

    .fi-sidebar-header {
        min-height: 104px !important;
        height: auto !important;
        padding: 16px 18px !important;
        border-bottom: 1px solid var(--sf-sidebar-border) !important;
        background: rgba(255, 255, 255, .72) !important;
        backdrop-filter: blur(18px) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .dark .fi-sidebar-header {
        background: rgba(7, 21, 37, .78) !important;
    }

    .fi-sidebar-header > *,
    .fi-sidebar-header a,
    .fi-sidebar-header .fi-logo,
    .fi-sidebar-header .fi-logo-link {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fi-sidebar-header img,
    .fi-sidebar-header svg,
    .fi-sidebar .fi-logo img,
    .fi-sidebar .fi-logo svg {
        max-width: 156px !important;
        max-height: 68px !important;
        width: auto !important;
        height: auto !important;
        object-fit: contain !important;
        margin: 0 auto !important;
        filter: none !important;
    }

    .dark .fi-sidebar-header img,
    .dark .fi-sidebar .fi-logo img {
        filter: brightness(1.08) contrast(1.04) !important;
    }

    /* =========================
       Navigation container
       ========================= */

    .fi-sidebar-nav {
        padding: 16px 13px 92px !important;
        background: transparent !important;
        gap: 0 !important;
        overflow-y: auto !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar {
        width: 5px !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, .25) !important;
        border-radius: 999px !important;
    }

    /* =========================
       Reset old green / teal active pollution
       ========================= */

    .fi-sidebar,
    .fi-sidebar *,
    .fi-sidebar *::before,
    .fi-sidebar *::after {
        --primary: 37 99 235 !important;
        --c-50: 239 246 255 !important;
        --c-100: 219 234 254 !important;
        --c-200: 191 219 254 !important;
        --c-300: 147 197 253 !important;
        --c-400: 96 165 250 !important;
        --c-500: 37 99 235 !important;
        --c-600: 37 99 235 !important;
        --c-700: 29 78 216 !important;
        --c-800: 30 64 175 !important;
        --c-900: 30 58 138 !important;

        --sada-sidebar-icon-active-bg: rgba(255,255,255,.16) !important;
        --sada-sidebar-active-bg: var(--sf-sidebar-active) !important;
        --sada-sidebar-active-text: #ffffff !important;
    }

    /* =========================
       Main item
       ========================= */

    .fi-sidebar-item {
        margin: 5px 0 !important;
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .fi-sidebar-item > a,
    .fi-sidebar-item > button,
    .fi-sidebar-item-button {
        min-height: 50px !important;
        width: 100% !important;
        border-radius: var(--sf-sidebar-radius-lg) !important;
        padding: 7px 10px !important;
        display: flex !important;
        align-items: center !important;
        gap: 11px !important;
        background: transparent !important;
        border: 1px solid transparent !important;
        box-shadow: none !important;
        color: var(--sf-sidebar-text) !important;
        text-decoration: none !important;
        transform: none !important;
        transition:
            background .18s ease,
            color .18s ease,
            border-color .18s ease,
            box-shadow .18s ease !important;
    }

    .fi-sidebar-item > a:hover,
    .fi-sidebar-item > button:hover,
    .fi-sidebar-item-button:hover {
        background: var(--sf-sidebar-hover) !important;
        border-color: rgba(37, 99, 235, .08) !important;
        color: var(--sf-sidebar-title) !important;
        transform: none !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .04) !important;
    }

    .fi-sidebar-item-label,
    .fi-sidebar-item > a span,
    .fi-sidebar-item > button span,
    .fi-sidebar-item-button span {
        color: inherit !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        font-weight: 820 !important;
        letter-spacing: -.015em !important;
        white-space: normal !important;
    }

    /* =========================
       Icons
       ========================= */

    .fi-sidebar-item > a > svg,
    .fi-sidebar-item > button > svg,
    .fi-sidebar-item-button > svg {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        max-width: 38px !important;
        border-radius: 15px !important;
        padding: 9px !important;
        background: var(--sf-sidebar-icon-bg) !important;
        color: #526174 !important;
        stroke: currentColor !important;
        fill: none !important;
        border: 1px solid var(--sf-sidebar-icon-border) !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .035) !important;
    }

    .fi-sidebar-item > a > svg *,
    .fi-sidebar-item > button > svg *,
    .fi-sidebar-item-button > svg * {
        stroke: currentColor !important;
    }

    .dark .fi-sidebar-item > a > svg,
    .dark .fi-sidebar-item > button > svg,
    .dark .fi-sidebar-item-button > svg {
        color: #cbd5e1 !important;
    }

    /* =========================
       Active item
       ========================= */

    .fi-sidebar-item.fi-active > a,
    .fi-sidebar-item-active > a,
    .fi-sidebar-item > a[aria-current="page"],
    .fi-sidebar-item.fi-active > button,
    .fi-sidebar-item-active > button,
    .fi-sidebar-item > button[aria-current="page"],
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button,
    .fi-sidebar-item-active > .fi-sidebar-item-button,
    .fi-sidebar-item-button[aria-current="page"] {
        background: var(--sf-sidebar-active) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, .16) !important;
        box-shadow: var(--sf-sidebar-active-shadow) !important;
    }

    .fi-sidebar-item.fi-active > a:hover,
    .fi-sidebar-item-active > a:hover,
    .fi-sidebar-item > a[aria-current="page"]:hover,
    .fi-sidebar-item.fi-active > button:hover,
    .fi-sidebar-item-active > button:hover,
    .fi-sidebar-item > button[aria-current="page"]:hover,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button:hover,
    .fi-sidebar-item-active > .fi-sidebar-item-button:hover,
    .fi-sidebar-item-button[aria-current="page"]:hover {
        background: var(--sf-sidebar-active) !important;
        color: #ffffff !important;
        box-shadow: var(--sf-sidebar-active-shadow) !important;
    }

    .fi-sidebar-item.fi-active > a > svg,
    .fi-sidebar-item-active > a > svg,
    .fi-sidebar-item > a[aria-current="page"] > svg,
    .fi-sidebar-item.fi-active > button > svg,
    .fi-sidebar-item-active > button > svg,
    .fi-sidebar-item > button[aria-current="page"] > svg,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg,
    .fi-sidebar-item-active > .fi-sidebar-item-button > svg,
    .fi-sidebar-item-button[aria-current="page"] > svg {
        background:
            radial-gradient(circle at 30% 20%, rgba(34, 211, 238, .52), transparent 45%),
            rgba(255, 255, 255, .15) !important;
        color: #ffffff !important;
        stroke: #ffffff !important;
        fill: none !important;
        border-color: rgba(255, 255, 255, .22) !important;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.14) !important;
    }

    .fi-sidebar-item.fi-active > a > svg *,
    .fi-sidebar-item-active > a > svg *,
    .fi-sidebar-item > a[aria-current="page"] > svg *,
    .fi-sidebar-item.fi-active > button > svg *,
    .fi-sidebar-item-active > button > svg *,
    .fi-sidebar-item > button[aria-current="page"] > svg *,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg *,
    .fi-sidebar-item-active > .fi-sidebar-item-button > svg *,
    .fi-sidebar-item-button[aria-current="page"] > svg * {
        stroke: #ffffff !important;
        color: #ffffff !important;
        fill: none !important;
    }

    .fi-sidebar-item.fi-active .fi-sidebar-item-label,
    .fi-sidebar-item-active .fi-sidebar-item-label,
    .fi-sidebar-item > a[aria-current="page"] span,
    .fi-sidebar-item > button[aria-current="page"] span,
    .fi-sidebar-item-button[aria-current="page"] span {
        color: #ffffff !important;
        font-weight: 930 !important;
    }

    .fi-sidebar-item.fi-active > a::before,
    .fi-sidebar-item-active > a::before,
    .fi-sidebar-item > a[aria-current="page"]::before,
    .fi-sidebar-item.fi-active > button::before,
    .fi-sidebar-item-active > button::before,
    .fi-sidebar-item > button[aria-current="page"]::before,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button::before,
    .fi-sidebar-item-active > .fi-sidebar-item-button::before,
    .fi-sidebar-item-button[aria-current="page"]::before {
        display: none !important;
        content: none !important;
    }

    /* =========================
       Groups
       ========================= */

    .fi-sidebar-group {
        margin: 20px 0 7px !important;
        padding: 16px 0 0 !important;
        border-top: 1px solid var(--sf-sidebar-border) !important;
        background: transparent !important;
        box-shadow: none !important;
        border-radius: 0 !important;
    }

    .fi-sidebar-group:first-child {
        margin-top: 8px !important;
        padding-top: 0 !important;
        border-top: 0 !important;
    }

    .fi-sidebar-group > div:first-child,
    .fi-sidebar-group-label-ctn,
    .fi-sidebar-group-button,
    .fi-sidebar-group > div:first-child button,
    .fi-sidebar-group > button {
        min-height: 30px !important;
        height: 30px !important;
        padding: 0 10px !important;
        margin: 0 0 9px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        color: var(--sf-sidebar-muted) !important;
        overflow: visible !important;
    }

    .fi-sidebar-group-label,
    .fi-sidebar-group-button span,
    .fi-sidebar-group > div:first-child span,
    .fi-sidebar-group > button span {
        position: relative !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 9px !important;
        padding: 0 !important;
        margin: 0 !important;
        color: var(--sf-sidebar-muted) !important;
        font-size: 10.5px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: .09em !important;
        text-transform: uppercase !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .fi-sidebar-group-label::before,
    .fi-sidebar-group-button span::before,
    .fi-sidebar-group > div:first-child span::before,
    .fi-sidebar-group > button span::before {
        content: "" !important;
        width: 8px !important;
        height: 8px !important;
        min-width: 8px !important;
        border-radius: 999px !important;
        background: linear-gradient(135deg, var(--sf-sidebar-cyan), var(--sf-sidebar-blue)) !important;
        box-shadow: 0 0 0 5px rgba(34, 211, 238, .10) !important;
    }

    .fi-sidebar-group-button svg,
    .fi-sidebar-group > div:first-child svg,
    .fi-sidebar-group > button svg {
        width: 14px !important;
        height: 14px !important;
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        color: #a2adbb !important;
        stroke: currentColor !important;
        fill: none !important;
    }

    .fi-sidebar-group-button:hover,
    .fi-sidebar-group > div:first-child button:hover,
    .fi-sidebar-group > button:hover {
        background: transparent !important;
        box-shadow: none !important;
    }

    /* subtle different group colors */
    .fi-sidebar-group:nth-of-type(1) .fi-sidebar-group-label,
    .fi-sidebar-group:nth-of-type(1) .fi-sidebar-group-button span {
        color: #64748b !important;
    }

    .fi-sidebar-group:nth-of-type(2) .fi-sidebar-group-label,
    .fi-sidebar-group:nth-of-type(2) .fi-sidebar-group-button span {
        color: #b7791f !important;
    }

    .fi-sidebar-group:nth-of-type(3) .fi-sidebar-group-label,
    .fi-sidebar-group:nth-of-type(3) .fi-sidebar-group-button span {
        color: #2563eb !important;
    }

    .fi-sidebar-group:nth-of-type(4) .fi-sidebar-group-label,
    .fi-sidebar-group:nth-of-type(4) .fi-sidebar-group-button span {
        color: #7c3aed !important;
    }

    .fi-sidebar-group:nth-of-type(5) .fi-sidebar-group-label,
    .fi-sidebar-group:nth-of-type(5) .fi-sidebar-group-button span {
        color: #0f766e !important;
    }

    /* =========================
       Child nav
       ========================= */

    .fi-sidebar .fi-sidebar-item-children {
        margin: 4px 0 8px 18px !important;
        padding: 5px 0 5px 10px !important;
        border-left: 1px solid rgba(148, 163, 184, .18) !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item {
        margin: 3px 0 !important;
    }

    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item > a,
    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item > button,
    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item-button {
        min-height: 42px !important;
        border-radius: 15px !important;
        padding: 6px 9px !important;
    }

    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item > a > svg,
    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item > button > svg,
    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item-button > svg {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        max-width: 32px !important;
        padding: 7px !important;
        border-radius: 13px !important;
    }

    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item-label,
    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item > a span,
    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item > button span,
    .fi-sidebar .fi-sidebar-item-children .fi-sidebar-item-button span {
        font-size: 13px !important;
        font-weight: 780 !important;
    }

    /* =========================
       Collapsed sidebar
       ========================= */

    .fi-sidebar.fi-sidebar-collapsed {
        width: 5.25rem !important;
    }

    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-header {
        min-height: 86px !important;
        padding: 13px 8px !important;
    }

    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-header img,
    .fi-sidebar.fi-sidebar-collapsed .fi-logo img {
        max-width: 50px !important;
        max-height: 50px !important;
    }

    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-group-button,
    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-group > div:first-child,
    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-group-label {
        display: none !important;
    }

    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-group {
        margin: 10px 0 !important;
        padding-top: 10px !important;
    }

    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item > a,
    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item > button,
    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item-button {
        justify-content: center !important;
        padding: 7px !important;
        gap: 0 !important;
    }

    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item > a > svg,
    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item > button > svg,
    .fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item-button > svg {
        margin: 0 !important;
    }
</style>

<style id="sada-sidebar-remove-logo-glass-active-icon-final">
    /*
     | Final sidebar polish:
     | - remove duplicated sidebar logo/header completely
     | - remove green active icon background
     | - active icon becomes clean white on transparent glass
     */

    .fi-sidebar-header,
    .fi-sidebar .fi-sidebar-header,
    aside.fi-sidebar .fi-sidebar-header,
    .fi-sidebar .fi-logo,
    .fi-sidebar .fi-logo-link,
    .fi-sidebar-header a,
    .fi-sidebar-header img,
    .fi-sidebar-header svg {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        min-height: 0 !important;
        max-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        overflow: hidden !important;
    }

    .fi-sidebar-nav {
        padding-top: 22px !important;
    }

    /*
     | Normal inactive icons stay soft and clean.
     */
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > a > svg,
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > button > svg,
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > .fi-sidebar-item-button > svg {
        background: rgba(241, 245, 249, .86) !important;
        color: #526174 !important;
        border: 1px solid rgba(148, 163, 184, .18) !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .035) !important;
        fill: none !important;
        stroke: currentColor !important;
    }

    /*
     | Active icon: NO green box.
     | Clean white icon with very soft transparent glass.
     */
    .fi-sidebar-item.fi-active > a > svg,
    .fi-sidebar-item-active > a > svg,
    .fi-sidebar-item > a[aria-current="page"] > svg,
    .fi-sidebar-item.fi-active > button > svg,
    .fi-sidebar-item-active > button > svg,
    .fi-sidebar-item > button[aria-current="page"] > svg,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg,
    .fi-sidebar-item-active > .fi-sidebar-item-button > svg,
    .fi-sidebar-item-button[aria-current="page"] > svg {
        background: rgba(255, 255, 255, .10) !important;
        color: #ffffff !important;
        stroke: #ffffff !important;
        fill: none !important;
        border: 1px solid rgba(255, 255, 255, .18) !important;
        box-shadow:
            inset 0 0 0 1px rgba(255, 255, 255, .10),
            0 8px 18px rgba(0, 0, 0, .08) !important;
        backdrop-filter: blur(12px) !important;
    }

    .fi-sidebar-item.fi-active > a > svg *,
    .fi-sidebar-item-active > a > svg *,
    .fi-sidebar-item > a[aria-current="page"] > svg *,
    .fi-sidebar-item.fi-active > button > svg *,
    .fi-sidebar-item-active > button > svg *,
    .fi-sidebar-item > button[aria-current="page"] > svg *,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg *,
    .fi-sidebar-item-active > .fi-sidebar-item-button > svg *,
    .fi-sidebar-item-button[aria-current="page"] > svg * {
        color: #ffffff !important;
        stroke: #ffffff !important;
        fill: none !important;
    }

    /*
     | Extra hard kill for any old green/teal active icon style.
     */
    .fi-sidebar .fi-active svg,
    .fi-sidebar .fi-sidebar-item-active svg,
    .fi-sidebar a[aria-current="page"] svg {
        background-color: rgba(255, 255, 255, .10) !important;
        background-image: none !important;
    }

    .fi-sidebar .fi-active svg *,
    .fi-sidebar .fi-sidebar-item-active svg *,
    .fi-sidebar a[aria-current="page"] svg * {
        stroke: #ffffff !important;
        fill: none !important;
    }
</style>

<style id="sada-sidebar-active-icon-no-teal-black-final">
    /*
     | Final icon behavior:
     | - every menu item uses one clean icon only
     | - active selected icon is NOT teal/green
     | - active selected icon is dark/black and visible on a transparent glass square
     | - inner svg paths never become white/hidden unless explicitly needed
     */

    .fi-sidebar-item a > svg,
    .fi-sidebar-item-button > svg,
    .fi-sidebar-item button > svg {
        flex: 0 0 43px !important;
        width: 43px !important;
        height: 43px !important;
        min-width: 43px !important;
        max-width: 43px !important;
        min-height: 43px !important;
        max-height: 43px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: visible !important;
    }

    /*
     | Normal icons.
     */
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > a > svg,
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > .fi-sidebar-item-button > svg,
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > button > svg {
        background: rgba(248, 250, 252, .88) !important;
        color: #475569 !important;
        stroke: #475569 !important;
        fill: none !important;
        border: 1px solid rgba(148, 163, 184, .18) !important;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .04) !important;
        opacity: 1 !important;
    }

    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > a > svg *,
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > .fi-sidebar-item-button > svg *,
    .fi-sidebar-item:not(.fi-active):not(.fi-sidebar-item-active) > button > svg * {
        stroke: currentColor !important;
        fill: none !important;
        opacity: 1 !important;
    }

    /*
     | Active icons.
     | No green / no teal / no white disappearing icon.
     */
    .fi-sidebar-item.fi-active > a > svg,
    .fi-sidebar-item-active > a > svg,
    .fi-sidebar-item > a[aria-current="page"] > svg,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg,
    .fi-sidebar-item-active > .fi-sidebar-item-button > svg,
    .fi-sidebar-item-button[aria-current="page"] > svg,
    .fi-sidebar-item.fi-active > button > svg,
    .fi-sidebar-item-active > button > svg,
    .fi-sidebar-item > button[aria-current="page"] > svg {
        background: rgba(255, 255, 255, .22) !important;
        background-image: none !important;
        color: #0f172a !important;
        stroke: #0f172a !important;
        fill: none !important;
        border: 1px solid rgba(255, 255, 255, .34) !important;
        box-shadow:
            inset 0 0 0 1px rgba(255, 255, 255, .16),
            0 10px 22px rgba(2, 6, 23, .12) !important;
        backdrop-filter: blur(14px) !important;
        opacity: 1 !important;
    }

    .fi-sidebar-item.fi-active > a > svg *,
    .fi-sidebar-item-active > a > svg *,
    .fi-sidebar-item > a[aria-current="page"] > svg *,
    .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg *,
    .fi-sidebar-item-active > .fi-sidebar-item-button > svg *,
    .fi-sidebar-item-button[aria-current="page"] > svg *,
    .fi-sidebar-item.fi-active > button > svg *,
    .fi-sidebar-item-active > button > svg *,
    .fi-sidebar-item > button[aria-current="page"] > svg * {
        color: #0f172a !important;
        stroke: #0f172a !important;
        fill: none !important;
        opacity: 1 !important;
    }

    /*
     | Hard kill any old teal/green classes/variables injected before.
     */
    .fi-sidebar,
    .fi-sidebar * {
        --sada-sidebar-icon-active-bg: rgba(255, 255, 255, .22) !important;
        --sf-sidebar-icon-active-bg: rgba(255, 255, 255, .22) !important;
    }

    .fi-sidebar .fi-active svg,
    .fi-sidebar .fi-sidebar-item-active svg,
    .fi-sidebar a[aria-current="page"] svg,
    .fi-sidebar button[aria-current="page"] svg {
        background-color: rgba(255, 255, 255, .22) !important;
        background-image: none !important;
    }

    /*
     | Dark mode: active icon still visible.
     */
    .dark .fi-sidebar-item.fi-active > a > svg,
    .dark .fi-sidebar-item-active > a > svg,
    .dark .fi-sidebar-item > a[aria-current="page"] > svg,
    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg,
    .dark .fi-sidebar-item-active > .fi-sidebar-item-button > svg,
    .dark .fi-sidebar-item-button[aria-current="page"] > svg {
        color: #ffffff !important;
        stroke: #ffffff !important;
        background: rgba(255, 255, 255, .14) !important;
        border-color: rgba(255, 255, 255, .22) !important;
    }

    .dark .fi-sidebar-item.fi-active > a > svg *,
    .dark .fi-sidebar-item-active > a > svg *,
    .dark .fi-sidebar-item > a[aria-current="page"] > svg *,
    .dark .fi-sidebar-item.fi-active > .fi-sidebar-item-button > svg *,
    .dark .fi-sidebar-item-active > .fi-sidebar-item-button > svg *,
    .dark .fi-sidebar-item-button[aria-current="page"] > svg * {
        stroke: #ffffff !important;
        fill: none !important;
    }
</style>

<style id="sf-global-archive-bulk-actions-clean-final">
    /*
     | GLOBAL ARCHIVE BULK ACTION CLEANUP
     | Applies to all /admin/archived-* pages.
     | - Hide Restore Selected / Permanent Delete until at least one row is selected.
     | - Hide Filament Select all / Deselect all helper buttons.
     | - Keep row checkboxes working normally.
     */

    body.sf-archive-page:not(.sf-archive-has-selection) .fi-ta-bulk-actions,
    body.sf-archive-page:not(.sf-archive-has-selection) .fi-ta-bulk-actions-toolbar,
    body.sf-archive-page:not(.sf-archive-has-selection) [class*="bulk-actions"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions,
    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions-toolbar {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 12px !important;
    }

    /*
     | Hide Select all / Deselect all helper buttons.
     | Header checkbox is enough.
     */
    body.sf-archive-page .fi-ta-selection-indicator button,
    body.sf-archive-page .fi-ta-selection-indicator a,
    body.sf-archive-page [wire\:click*="selectAllTableRecords"],
    body.sf-archive-page [wire\:click*="deselectAllTableRecords"],
    body.sf-archive-page [x-on\:click*="selectAll"],
    body.sf-archive-page [x-on\:click*="deselect"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    /*
     | Keep selection text only, without helper buttons.
     */
    body.sf-archive-page .fi-ta-selection-indicator {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0 !important;
        color: #334155 !important;
        font-size: 13px !important;
        font-weight: 750 !important;
    }

    /*
     | Bulk buttons style — same archive premium style.
     */
    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions .fi-btn,
    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions-toolbar .fi-btn {
        width: auto !important;
        height: 44px !important;
        min-height: 44px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        border: 0 !important;
        box-shadow: 0 14px 28px rgba(15, 23, 42, .12) !important;
    }

    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions .fi-btn:first-child,
    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions-toolbar .fi-btn:first-child {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #ffffff !important;
    }

    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions .fi-btn:nth-child(2),
    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions-toolbar .fi-btn:nth-child(2) {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
    }

    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions .fi-btn svg,
    body.sf-archive-page.sf-archive-has-selection .fi-ta-bulk-actions-toolbar .fi-btn svg {
        width: 17px !important;
        height: 17px !important;
        min-width: 17px !important;
        min-height: 17px !important;
        color: currentColor !important;
        stroke: currentColor !important;
    }

    body.sf-archive-page .fi-ta-bulk-actions .animate-spin,
    body.sf-archive-page .fi-ta-bulk-actions-toolbar .animate-spin,
    body.sf-archive-page .fi-ta-bulk-actions [class*="spinner"],
    body.sf-archive-page .fi-ta-bulk-actions-toolbar [class*="spinner"],
    body.sf-archive-page .fi-ta-bulk-actions [class*="loading"],
    body.sf-archive-page .fi-ta-bulk-actions-toolbar [class*="loading"] {
        display: none !important;
    }
</style>

<script id="sf-global-archive-bulk-actions-clean-final-script">
    (() => {
        const applyArchiveBulkClean = () => {
            const path = window.location.pathname || '';
            const isArchivePage = path.includes('/admin/archived-') || path.includes('/admin/archive');

            document.body.classList.toggle('sf-archive-page', isArchivePage);

            if (!isArchivePage) {
                document.body.classList.remove('sf-archive-has-selection');
                return;
            }

            const hasSelection = document.querySelectorAll('.fi-ta-table tbody input[type="checkbox"]:checked, table tbody input[type="checkbox"]:checked').length > 0;
            document.body.classList.toggle('sf-archive-has-selection', hasSelection);

            /*
             | Remove Select all / Deselect all helper buttons by text.
             | The table header checkbox remains active.
             */
            document.querySelectorAll('button, a').forEach((el) => {
                const text = (el.textContent || '').trim().toLowerCase();

                if (
                    text === 'select all' ||
                    text === 'deselect all' ||
                    text.includes('select all') ||
                    text.includes('deselect all')
                ) {
                    const tableArea = el.closest('.fi-ta, .fi-ta-ctn, .fi-ta-content, .fi-ta-selection-indicator');

                    if (tableArea) {
                        el.style.display = 'none';
                        el.style.visibility = 'hidden';
                        el.style.opacity = '0';
                        el.style.pointerEvents = 'none';
                    }
                }
            });

            /*
             | Ensure labels on bulk buttons are correct.
             */
            document.querySelectorAll('.fi-ta-bulk-actions .fi-btn, .fi-ta-bulk-actions-toolbar .fi-btn').forEach((button, index) => {
                button.querySelectorAll('.animate-spin, [class*="spinner"], [class*="loading"]').forEach((el) => el.remove());

                let label = button.querySelector('.fi-btn-label');

                if (!label) {
                    label = document.createElement('span');
                    label.className = 'fi-btn-label';
                    button.appendChild(label);
                }

                if (index === 0) {
                    label.textContent = 'Restore Selected';
                    button.setAttribute('title', 'Restore Selected');
                    button.setAttribute('aria-label', 'Restore Selected');
                }

                if (index === 1) {
                    label.textContent = 'Permanent Delete';
                    button.setAttribute('title', 'Permanent Delete');
                    button.setAttribute('aria-label', 'Permanent Delete');
                }
            });
        };

        document.addEventListener('DOMContentLoaded', applyArchiveBulkClean);

        document.addEventListener('change', (event) => {
            if (event.target && event.target.matches('input[type="checkbox"]')) {
                setTimeout(applyArchiveBulkClean, 40);
            }
        });

        new MutationObserver(applyArchiveBulkClean).observe(document.body, {
            childList: true,
            subtree: true,
        });

        setInterval(applyArchiveBulkClean, 700);
    })();
</script>

<style id="sf-force-hide-archive-bulk-until-selected-final">
    /*
     | HARD FIX:
     | On all archive pages, hide Restore Selected / Permanent Delete
     | unless at least one BODY ROW checkbox is selected.
     */

    body:has([data-current-route*="archived"]) .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions,
    body:has([data-current-route*="archived"]) .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions-toolbar,
    body.sf-archive-page .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions,
    body.sf-archive-page .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions-toolbar,
    body[class*="archived"] .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions,
    body[class*="archived"] .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions-toolbar {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        height: 0 !important;
        min-height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
    }

    /*
     | URL/path fallback controlled by JS.
     */
    body.sf-force-archive-page .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions,
    body.sf-force-archive-page .fi-ta:not(:has(tbody input[type="checkbox"]:checked)) .fi-ta-bulk-actions-toolbar {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        height: 0 !important;
        min-height: 0 !important;
        max-height: 0 !important;
        overflow: hidden !important;
    }

    /*
     | Show only when a real table row is selected.
     */
    body.sf-force-archive-page .fi-ta:has(tbody input[type="checkbox"]:checked) .fi-ta-bulk-actions,
    body.sf-force-archive-page .fi-ta:has(tbody input[type="checkbox"]:checked) .fi-ta-bulk-actions-toolbar {
        display: inline-flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        height: auto !important;
        min-height: 44px !important;
        max-height: none !important;
        overflow: visible !important;
    }

    /*
     | Hide Select all / Deselect all text buttons.
     */
    body.sf-force-archive-page .fi-ta-selection-indicator button,
    body.sf-force-archive-page .fi-ta-selection-indicator a {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
</style>

<script id="sf-force-hide-archive-bulk-until-selected-final-script">
    (() => {
        const apply = () => {
            const path = window.location.pathname || '';
            const isArchive = path.includes('/admin/archived-') || path.includes('/admin/archive');

            document.body.classList.toggle('sf-force-archive-page', isArchive);

            if (!isArchive) return;

            document.querySelectorAll('.fi-ta').forEach((table) => {
                const hasCheckedBodyRow = table.querySelectorAll('tbody input[type="checkbox"]:checked').length > 0;
                const bulkAreas = table.querySelectorAll('.fi-ta-bulk-actions, .fi-ta-bulk-actions-toolbar');

                bulkAreas.forEach((area) => {
                    if (!hasCheckedBodyRow) {
                        area.style.setProperty('display', 'none', 'important');
                        area.style.setProperty('visibility', 'hidden', 'important');
                        area.style.setProperty('opacity', '0', 'important');
                        area.style.setProperty('pointer-events', 'none', 'important');
                        area.style.setProperty('height', '0', 'important');
                        area.style.setProperty('min-height', '0', 'important');
                        area.style.setProperty('overflow', 'hidden', 'important');
                    } else {
                        area.style.setProperty('display', 'inline-flex', 'important');
                        area.style.setProperty('visibility', 'visible', 'important');
                        area.style.setProperty('opacity', '1', 'important');
                        area.style.setProperty('pointer-events', 'auto', 'important');
                        area.style.setProperty('height', 'auto', 'important');
                        area.style.setProperty('min-height', '44px', 'important');
                        area.style.setProperty('overflow', 'visible', 'important');
                    }
                });
            });

            document.querySelectorAll('.fi-ta-selection-indicator button, .fi-ta-selection-indicator a').forEach((el) => {
                const text = (el.textContent || '').trim().toLowerCase();

                if (text.includes('select all') || text.includes('deselect all')) {
                    el.style.setProperty('display', 'none', 'important');
                    el.style.setProperty('visibility', 'hidden', 'important');
                    el.style.setProperty('opacity', '0', 'important');
                    el.style.setProperty('pointer-events', 'none', 'important');
                }
            });
        };

        document.addEventListener('DOMContentLoaded', apply);

        document.addEventListener('change', (event) => {
            if (event.target && event.target.matches('input[type="checkbox"]')) {
                setTimeout(apply, 20);
                setTimeout(apply, 120);
                setTimeout(apply, 300);
            }
        });

        new MutationObserver(apply).observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
        });

        setInterval(apply, 500);
    })();
</script>
