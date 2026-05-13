<style>
    /*
        Sada Fezzan MD3 Topbar Notification Polish
        Makes the notification bell visually consistent with the custom profile chip.
    */

    .fi-topbar {
        overflow: visible !important;
    }

    .fi-topbar .fi-icon-btn,
    .fi-topbar button[aria-label*="notification" i],
    .fi-topbar button[aria-label*="notifications" i],
    .fi-topbar [title*="notification" i],
    .fi-topbar [title*="notifications" i] {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        border-radius: 999px !important;
        background: rgba(255, 255, 255, .92) !important;
        border: 1px solid rgba(15, 23, 42, .10) !important;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .09) !important;
        color: #0f172a !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        backdrop-filter: blur(14px) !important;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background .18s ease,
            border-color .18s ease !important;
    }

    .fi-topbar .fi-icon-btn:hover,
    .fi-topbar button[aria-label*="notification" i]:hover,
    .fi-topbar button[aria-label*="notifications" i]:hover,
    .fi-topbar [title*="notification" i]:hover,
    .fi-topbar [title*="notifications" i]:hover {
        transform: translateY(-1px) !important;
        background: rgba(255, 255, 255, .98) !important;
        border-color: rgba(37, 99, 235, .22) !important;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .12) !important;
    }

    .fi-topbar .fi-icon-btn svg,
    .fi-topbar button[aria-label*="notification" i] svg,
    .fi-topbar button[aria-label*="notifications" i] svg,
    .fi-topbar [title*="notification" i] svg,
    .fi-topbar [title*="notifications" i] svg {
        width: 25px !important;
        height: 25px !important;
        color: #64748b !important;
        stroke-width: 2 !important;
    }

    .fi-topbar .fi-icon-btn:hover svg,
    .fi-topbar button[aria-label*="notification" i]:hover svg,
    .fi-topbar button[aria-label*="notifications" i]:hover svg,
    .fi-topbar [title*="notification" i]:hover svg,
    .fi-topbar [title*="notifications" i]:hover svg {
        color: #2563eb !important;
    }

    /*
        Notification badge/counter polish.
    */
    .fi-topbar .fi-badge,
    .fi-topbar [class*="badge"],
    .fi-topbar [class*="Badge"] {
        border-radius: 999px !important;
        box-shadow: 0 6px 14px rgba(239, 68, 68, .28) !important;
        border: 2px solid #fff !important;
        font-weight: 950 !important;
    }

    /*
        Dark mode alignment.
    */
    .dark .fi-topbar .fi-icon-btn,
    .dark .fi-topbar button[aria-label*="notification" i],
    .dark .fi-topbar button[aria-label*="notifications" i],
    .dark .fi-topbar [title*="notification" i],
    .dark .fi-topbar [title*="notifications" i] {
        background: rgba(15, 23, 42, .84) !important;
        border-color: rgba(148, 163, 184, .18) !important;
        color: #fff !important;
        box-shadow: 0 10px 28px rgba(0, 0, 0, .18) !important;
    }

    .dark .fi-topbar .fi-icon-btn svg,
    .dark .fi-topbar button[aria-label*="notification" i] svg,
    .dark .fi-topbar button[aria-label*="notifications" i] svg,
    .dark .fi-topbar [title*="notification" i] svg,
    .dark .fi-topbar [title*="notifications" i] svg {
        color: #cbd5e1 !important;
    }

    .dark .fi-topbar .fi-icon-btn:hover svg,
    .dark .fi-topbar button[aria-label*="notification" i]:hover svg,
    .dark .fi-topbar button[aria-label*="notifications" i]:hover svg,
    .dark .fi-topbar [title*="notification" i]:hover svg,
    .dark .fi-topbar [title*="notifications" i]:hover svg {
        color: #60a5fa !important;
    }
</style>
