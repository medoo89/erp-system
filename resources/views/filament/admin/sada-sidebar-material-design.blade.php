<style id="sf-sada-sidebar-minimal-material-v4">
/*
|--------------------------------------------------------------------------
| Sada Fezzan ERP - Minimal Material Sidebar
|--------------------------------------------------------------------------
| CSS only. No JavaScript.
| Goal:
| - No icon boxes
| - No box-inside-box group headers
| - Very light active state
| - Clean Material Design feeling
*/

/* Sidebar */
.fi-sidebar {
    background:
        radial-gradient(circle at top left, rgba(34,211,238,.045), transparent 34%),
        linear-gradient(180deg, #ffffff 0%, #f9fcfd 100%) !important;
    border-right: 1px solid rgba(148,163,184,.10) !important;
    box-shadow: 14px 0 44px rgba(15,23,42,.035) !important;
}

.dark .fi-sidebar {
    background:
        radial-gradient(circle at top left, rgba(34,211,238,.07), transparent 34%),
        linear-gradient(180deg, #0f172a 0%, #020617 100%) !important;
    border-right-color: rgba(148,163,184,.12) !important;
}

/* Navigation spacing */
.fi-sidebar-nav {
    padding: 16px 12px 88px !important;
}

/* Header/logo area */
.fi-sidebar-header {
    min-height: 78px !important;
    padding: 14px 16px 12px !important;
    margin-bottom: 12px !important;
    border-bottom: 1px solid rgba(148,163,184,.10) !important;
}

.fi-sidebar-header img,
.fi-logo {
    max-height: 58px !important;
    object-fit: contain !important;
}

/* ==========================================================
   GROUP HEADERS - minimal, no pill, no internal box
   ========================================================== */

.fi-sidebar-group {
    margin-top: 20px !important;
    padding-top: 18px !important;
    border-top: 1px solid rgba(148,163,184,.12) !important;
}

/* Remove box/pill from group label completely */
.fi-sidebar-group-label {
    position: relative !important;
    min-height: auto !important;
    display: flex !important;
    align-items: center !important;
    margin: 0 10px 12px !important;
    padding: 0 28px !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    color: #a16207 !important;
    font-size: 12px !important;
    line-height: 1.2 !important;
    letter-spacing: .10em !important;
    font-weight: 1000 !important;
    text-transform: uppercase !important;
}

/* Small material dot only */
.fi-sidebar-group-label::before {
    content: "";
    position: absolute;
    left: 4px;
    top: 50%;
    width: 10px;
    height: 10px;
    border-radius: 999px;
    transform: translateY(-50%);
    background: #22d3ee;
    box-shadow: 0 0 0 7px rgba(34,211,238,.11);
}

/* Make collapse arrow simple */
.fi-sidebar-group-collapse-button {
    width: 34px !important;
    height: 34px !important;
    border-radius: 999px !important;
    color: #94a3b8 !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

.fi-sidebar-group-collapse-button:hover {
    background: rgba(15,23,42,.045) !important;
    color: #64748b !important;
}

/* ==========================================================
   ITEMS - simple material rows
   ========================================================== */

.fi-sidebar-item {
    margin: 4px 0 !important;
}

.fi-sidebar-item-button,
.fi-sidebar-item a,
.fi-sidebar-item button {
    min-height: 54px !important;
    border-radius: 20px !important;
    padding: 0 16px !important;
    gap: 14px !important;
    background: transparent !important;
    border: 1px solid transparent !important;
    box-shadow: none !important;
    color: #3f4755 !important;
    font-weight: 900 !important;
    transition: background .15s ease, color .15s ease, box-shadow .15s ease !important;
}

/* Soft hover */
.fi-sidebar-item-button:hover,
.fi-sidebar-item a:hover,
.fi-sidebar-item button:hover {
    background: rgba(15,23,42,.045) !important;
    color: #0f766e !important;
}

/* Remove all icon containers/boxes */
.fi-sidebar-item-icon {
    width: 30px !important;
    min-width: 30px !important;
    height: 30px !important;
    border-radius: 0 !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    color: #475569 !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

/* SVG icon size */
.fi-sidebar-item-icon svg,
.fi-sidebar-item svg {
    width: 25px !important;
    height: 25px !important;
    color: currentColor !important;
    stroke-width: 1.85 !important;
}

/* Label */
.fi-sidebar-item-label {
    color: inherit !important;
    font-size: 15.5px !important;
    font-weight: 920 !important;
    line-height: 1.15 !important;
    letter-spacing: -.015em !important;
}

/* ==========================================================
   ACTIVE - material soft state, not heavy gradient
   ========================================================== */

.fi-sidebar-item.fi-active > .fi-sidebar-item-button,
.fi-sidebar-item.fi-active a,
.fi-sidebar-item.fi-active button,
.fi-sidebar-item:has(.fi-sidebar-item-button[aria-current="page"]) > .fi-sidebar-item-button,
.fi-sidebar-item:has(a[aria-current="page"]) a,
.fi-sidebar-item:has(button[aria-current="page"]) button,
.fi-sidebar-item:has(.fi-sidebar-item-button.fi-active) > .fi-sidebar-item-button {
    min-height: 58px !important;
    border-radius: 999px !important;
    background:
        linear-gradient(90deg, #3ebfc0 0 6px, rgba(236,253,253,.96) 6px 100%) !important;
    color: #087a7b !important;
    border: 1px solid rgba(62,191,192,.13) !important;
    box-shadow: 0 10px 24px rgba(15,23,42,.035) !important;
}

/* Active icon is still plain */
.fi-sidebar-item.fi-active .fi-sidebar-item-icon,
.fi-sidebar-item:has(.fi-sidebar-item-button[aria-current="page"]) .fi-sidebar-item-icon,
.fi-sidebar-item:has(a[aria-current="page"]) .fi-sidebar-item-icon,
.fi-sidebar-item:has(button[aria-current="page"]) .fi-sidebar-item-icon,
.fi-sidebar-item:has(.fi-sidebar-item-button.fi-active) .fi-sidebar-item-icon {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    color: #087a7b !important;
}

/* Badges */
.fi-sidebar-item-badge {
    border-radius: 999px !important;
    padding: 3px 8px !important;
    background: rgba(62,191,192,.11) !important;
    color: #087a7b !important;
    font-weight: 900 !important;
}

/* Dark mode */
.dark .fi-sidebar-item-button,
.dark .fi-sidebar-item a,
.dark .fi-sidebar-item button {
    color: #cbd5e1 !important;
}

.dark .fi-sidebar-item-button:hover,
.dark .fi-sidebar-item a:hover,
.dark .fi-sidebar-item button:hover {
    background: rgba(148,163,184,.08) !important;
    color: #67e8f9 !important;
}

.dark .fi-sidebar-item-icon {
    color: #cbd5e1 !important;
}

.dark .fi-sidebar-group {
    border-top-color: rgba(148,163,184,.12) !important;
}

.dark .fi-sidebar-group-label {
    color: #60a5fa !important;
}

.dark .fi-sidebar-group-collapse-button:hover {
    background: rgba(148,163,184,.08) !important;
}

.dark .fi-sidebar-item.fi-active > .fi-sidebar-item-button,
.dark .fi-sidebar-item.fi-active a,
.dark .fi-sidebar-item.fi-active button,
.dark .fi-sidebar-item:has(.fi-sidebar-item-button[aria-current="page"]) > .fi-sidebar-item-button,
.dark .fi-sidebar-item:has(a[aria-current="page"]) a,
.dark .fi-sidebar-item:has(button[aria-current="page"]) button {
    background:
        linear-gradient(90deg, #22d3ee 0 6px, rgba(34,211,238,.11) 6px 100%) !important;
    color: #67e8f9 !important;
    border-color: rgba(34,211,238,.14) !important;
}

/* Collapsed safety */
.fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item-button,
.fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item a,
.fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item button {
    justify-content: center !important;
    padding-inline: 10px !important;
}

.fi-sidebar.fi-sidebar-collapsed .fi-sidebar-item-label,
.fi-sidebar.fi-sidebar-collapsed .fi-sidebar-group-label {
    display: none !important;
}

/* Mobile */
@media (max-width: 1024px) {
    .fi-sidebar-nav {
        padding-bottom: 32px !important;
    }

    .fi-sidebar-item-button,
    .fi-sidebar-item a,
    .fi-sidebar-item button {
        min-height: 52px !important;
        border-radius: 18px !important;
    }

    .fi-sidebar-item-label {
        font-size: 14.5px !important;
    }

    .fi-sidebar-item-icon svg,
    .fi-sidebar-item svg {
        width: 23px !important;
        height: 23px !important;
    }
}
</style>
