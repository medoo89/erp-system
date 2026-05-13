<style id="sada-admin-notifications-polish">
    /*
     |--------------------------------------------------------------------------
     | Sada Fezzan ERP — Filament Admin Notifications Compact Polish
     |--------------------------------------------------------------------------
     */

    @media (min-width: 900px) {
        body:has(.fi-notifications) .fi-modal-window,
        body:has(.fi-no-notifications) .fi-modal-window {
            position: fixed !important;
            top: 86px !important;
            right: 22px !important;
            left: auto !important;
            bottom: auto !important;
            width: 460px !important;
            max-width: calc(100vw - 44px) !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: calc(100vh - 112px) !important;
            border-radius: 28px !important;
            overflow: hidden !important;
            box-shadow: 0 28px 80px rgba(15, 23, 42, .28) !important;
            border: 1px solid rgba(215, 226, 229, .95) !important;
            transform: none !important;
        }

        body:has(.fi-notifications) .fi-modal,
        body:has(.fi-no-notifications) .fi-modal {
            align-items: flex-start !important;
            justify-content: flex-end !important;
            padding: 0 !important;
        }

        body:has(.fi-notifications) .fi-modal-close-overlay,
        body:has(.fi-no-notifications) .fi-modal-close-overlay {
            background: rgba(15, 23, 42, .35) !important;
            backdrop-filter: blur(4px) !important;
        }
    }

    body:has(.fi-notifications) .fi-modal-window,
    body:has(.fi-no-notifications) .fi-modal-window {
        background:
            radial-gradient(circle at 90% 0%, rgba(76,167,168,.12), transparent 34%),
            linear-gradient(180deg, #ffffff 0%, #f8fbff 100%) !important;
    }

    body:has(.fi-notifications) .fi-modal-header,
    body:has(.fi-no-notifications) .fi-modal-header {
        padding: 20px 22px 12px !important;
        border-bottom: 1px solid rgba(215,226,229,.80) !important;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.12), transparent 32%),
            linear-gradient(135deg, rgba(255,255,255,.98), rgba(248,251,255,.92)) !important;
    }

    body:has(.fi-notifications) .fi-modal-heading,
    body:has(.fi-no-notifications) .fi-modal-heading {
        font-size: 24px !important;
        line-height: 1.1 !important;
        font-weight: 950 !important;
        letter-spacing: -.045em !important;
        color: #0f172a !important;
    }

    body:has(.fi-notifications) .fi-modal-content,
    body:has(.fi-no-notifications) .fi-modal-content {
        padding: 12px !important;
        max-height: calc(100vh - 190px) !important;
        overflow-y: auto !important;
        background: transparent !important;
    }

    .sada-admin-notification-actions {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 0 2px 12px !important;
        margin: 0 !important;
        background: transparent !important;
    }

    .sada-admin-notification-actions form {
        margin: 0 !important;
    }

    .sada-admin-notification-actions button {
        min-height: 34px !important;
        border-radius: 999px !important;
        padding: 0 12px !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        cursor: pointer !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        font-family: Arial, Helvetica, sans-serif !important;
    }

    .sada-admin-notification-actions .read-btn {
        color: #b45309 !important;
        background: #fff7ed !important;
        border-color: rgba(245,158,11,.28) !important;
    }

    .sada-admin-notification-actions .clear-btn {
        color: #b91c1c !important;
        background: #fef2f2 !important;
        border-color: rgba(239,68,68,.20) !important;
    }

    /*
     * Important: reset huge notification card layout.
     */
    .fi-notifications {
        display: grid !important;
        gap: 10px !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .fi-notifications > *,
    .fi-notifications article,
    .fi-notifications li,
    .fi-notification {
        min-height: 0 !important;
        height: auto !important;
        max-height: none !important;
        padding: 14px 14px !important;
        margin: 0 !important;
        border-radius: 20px !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 35%),
            rgba(255,255,255,.96) !important;
        box-shadow: 0 12px 30px rgba(15,23,42,.06) !important;
        overflow: visible !important;
        display: block !important;
    }

    .fi-notifications > * + *,
    .fi-notifications article + article,
    .fi-notifications li + li {
        margin-top: 0 !important;
    }

    .fi-notification,
    .fi-notification > div,
    .fi-notifications article > div,
    .fi-notifications li > div {
        min-height: 0 !important;
        height: auto !important;
        display: block !important;
        align-items: initial !important;
        justify-content: initial !important;
    }

    .fi-notification-title,
    .fi-notifications h3,
    .fi-notifications [class*="title"] {
        margin: 0 !important;
        color: #0f172a !important;
        font-size: 14px !important;
        line-height: 1.35 !important;
        font-weight: 950 !important;
        letter-spacing: -.02em !important;
        text-align: left !important;
    }

    .fi-notification-body,
    .fi-notifications p,
    .fi-notifications [class*="body"] {
        margin-top: 5px !important;
        color: #64748b !important;
        font-size: 12px !important;
        line-height: 1.45 !important;
        font-weight: 700 !important;
        text-align: left !important;
    }

    .fi-notification-date,
    .fi-notifications time,
    .fi-notifications [class*="date"] {
        display: block !important;
        margin-top: 8px !important;
        color: #94a3b8 !important;
        font-size: 11px !important;
        line-height: 1.2 !important;
        font-weight: 750 !important;
        text-align: left !important;
    }

    .fi-notifications svg {
        width: 20px !important;
        height: 20px !important;
        flex-shrink: 0 !important;
    }

    .fi-no-notifications {
        min-height: 220px !important;
        display: grid !important;
        place-items: center !important;
        padding: 30px 18px !important;
        text-align: center !important;
    }

    .fi-no-notifications svg {
        width: 54px !important;
        height: 54px !important;
        color: #0f766e !important;
        background: #ecfdf5 !important;
        border: 1px solid rgba(16,185,129,.22) !important;
        border-radius: 20px !important;
        padding: 13px !important;
    }

    .fi-no-notifications h2,
    .fi-no-notifications h3 {
        margin-top: 14px !important;
        color: #0f172a !important;
        font-size: 18px !important;
        font-weight: 950 !important;
        letter-spacing: -.035em !important;
    }

    .fi-no-notifications p {
        margin-top: 6px !important;
        color: #64748b !important;
        font-size: 13px !important;
        font-weight: 700 !important;
    }

    body:has(.fi-notifications) .fi-modal-close-btn,
    body:has(.fi-no-notifications) .fi-modal-close-btn {
        width: 42px !important;
        height: 42px !important;
        border-radius: 999px !important;
        background: rgba(15,23,42,.05) !important;
        color: #64748b !important;
    }

    .dark body:has(.fi-notifications) .fi-modal-window,
    .dark body:has(.fi-no-notifications) .fi-modal-window {
        background:
            radial-gradient(circle at 90% 0%, rgba(76,167,168,.18), transparent 34%),
            linear-gradient(180deg, #071427 0%, #0f172a 100%) !important;
        border-color: rgba(76,167,168,.22) !important;
    }

    .dark body:has(.fi-notifications) .fi-modal-header,
    .dark body:has(.fi-no-notifications) .fi-modal-header {
        background: rgba(15,23,42,.86) !important;
        border-bottom-color: rgba(76,167,168,.20) !important;
    }

    .dark body:has(.fi-notifications) .fi-modal-heading,
    .dark body:has(.fi-no-notifications) .fi-modal-heading,
    .dark .fi-notification-title,
    .dark .fi-notifications h3,
    .dark .fi-notifications [class*="title"] {
        color: #ffffff !important;
    }

    .dark .fi-notification-body,
    .dark .fi-notifications p,
    .dark .fi-notifications [class*="body"] {
        color: rgba(226,232,240,.75) !important;
    }

    .dark .fi-notifications > *,
    .dark .fi-notifications article,
    .dark .fi-notifications li,
    .dark .fi-notification {
        background: rgba(15,23,42,.78) !important;
        border-color: rgba(255,255,255,.10) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        function injectNotificationActions() {
            const hasPanel = document.querySelector('.fi-no-notifications, .fi-notifications');
            if (!hasPanel) return;

            const content = hasPanel.closest('.fi-modal-content') || document.querySelector('.fi-modal-content');
            if (!content) return;

            if (content.querySelector('.sada-admin-notification-actions')) return;

            const wrap = document.createElement('div');
            wrap.className = 'sada-admin-notification-actions';
            wrap.innerHTML = `
                <form method="POST" action="/admin/sada-notifications/mark-all-read">
                    <input type="hidden" name="_token" value="${csrf}">
                    <button type="submit" class="read-btn">Mark all as read</button>
                </form>
                <form method="POST" action="/admin/sada-notifications/clear-all" onsubmit="return confirm('Clear all admin notifications?')">
                    <input type="hidden" name="_token" value="${csrf}">
                    <button type="submit" class="clear-btn">Clear</button>
                </form>
            `;

            content.prepend(wrap);
        }

        const observer = new MutationObserver(function () {
            injectNotificationActions();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });

        document.addEventListener('click', function () {
            setTimeout(injectNotificationActions, 100);
            setTimeout(injectNotificationActions, 350);
        });
    });
</script>
