<style>
    :root {
        --md3-draft-bg: #eef2f6;
        --md3-draft-text: #344054;
        --md3-draft-border: #d0d5dd;

        --md3-approved-bg: #dbeafe;
        --md3-approved-text: #1d4ed8;
        --md3-approved-border: #bfdbfe;

        --md3-bank-bg: #fff4d6;
        --md3-bank-text: #b45309;
        --md3-bank-border: #fde68a;

        --md3-paid-bg: #dcfce7;
        --md3-paid-text: #047857;
        --md3-paid-border: #bbf7d0;

        --md3-rejected-bg: #ffe4e6;
        --md3-rejected-text: #be123c;
        --md3-rejected-border: #fecdd3;
    }

    .sf-portal-slip-status-badge,
    .sf-slip-status-badge,
    [data-salary-slip-status-badge] {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: fit-content !important;
        min-width: 96px !important;
        border-radius: 999px !important;
        padding: 9px 16px !important;
        font-size: 11px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase !important;
        box-shadow: none !important;
        white-space: nowrap !important;
        background-image: none !important;
    }

    .sf-status-draft,
    .sf-status-pending {
        background: var(--md3-draft-bg) !important;
        color: var(--md3-draft-text) !important;
        border: 1px solid var(--md3-draft-border) !important;
    }

    .sf-status-approved {
        background: var(--md3-approved-bg) !important;
        color: var(--md3-approved-text) !important;
        border: 1px solid var(--md3-approved-border) !important;
    }

    .sf-status-sent_to_bank,
    .sf-status-sent-to-bank {
        background: var(--md3-bank-bg) !important;
        color: var(--md3-bank-text) !important;
        border: 1px solid var(--md3-bank-border) !important;
    }

    .sf-status-paid,
    .sf-status-received {
        background: var(--md3-paid-bg) !important;
        color: var(--md3-paid-text) !important;
        border: 1px solid var(--md3-paid-border) !important;
    }

    .sf-status-bank_rejected,
    .sf-status-bank-rejected,
    .sf-status-rejected,
    .sf-status-cancelled,
    .sf-status-not_received,
    .sf-status-not-received {
        background: var(--md3-rejected-bg) !important;
        color: var(--md3-rejected-text) !important;
        border: 1px solid var(--md3-rejected-border) !important;
    }

    .sf-portal-confirm-box {
        margin: 18px auto 22px;
        border-radius: 28px;
        padding: 20px;
        background: rgba(255,255,255,.88);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 18px 45px rgba(15,23,42,.08);
    }

    .sf-portal-confirm-title {
        font-size: 18px;
        font-weight: 950;
        color: #0f172a;
        letter-spacing: -.03em;
        margin-bottom: 6px;
    }

    .sf-portal-confirm-text {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 16px;
        line-height: 1.6;
    }

    .sf-portal-confirm-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    @media (min-width: 768px) {
        .sf-portal-confirm-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .sf-confirm-panel {
        border-radius: 22px;
        padding: 14px;
        border: 1px solid rgba(15,23,42,.08);
        background: #f8fafc;
    }

    .sf-confirm-panel-received {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .sf-confirm-panel-not {
        background: #fff1f2;
        border-color: #fecdd3;
    }

    .sf-portal-confirm-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .sf-md3-note {
        flex: 1 1 220px;
        min-width: 0;
        border-radius: 999px;
        border: 1px solid rgba(15,23,42,.10);
        padding: 12px 15px;
        outline: none;
        font-size: 13px;
        background: #fff;
        color: #0f172a;
    }

    .sf-md3-note:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37,99,235,.10);
    }

    .sf-md3-btn {
        border: 0;
        cursor: pointer;
        border-radius: 999px;
        padding: 12px 18px;
        font-size: 13px;
        font-weight: 900;
        transition: transform .15s ease, box-shadow .15s ease;
        white-space: nowrap;
    }

    .sf-md3-btn:hover {
        transform: translateY(-1px);
    }

    .sf-md3-btn-received {
        background: #047857;
        color: #fff;
        box-shadow: 0 12px 26px rgba(4,120,87,.18);
    }

    .sf-md3-btn-not-received {
        background: #fff;
        color: #be123c;
        border: 1px solid rgba(190,18,60,.18);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function normalizeStatus(text) {
            text = (text || '').toLowerCase().trim();

            if (text.includes('not received') || text.includes('not_received')) return 'not_received';
            if (text.includes('sent to bank') || text.includes('sent_to_bank')) return 'sent_to_bank';
            if (text.includes('bank rejected') || text.includes('bank_rejected')) return 'bank_rejected';
            if (text.includes('approved')) return 'approved';
            if (text === 'paid' || text.includes('paid')) return 'paid';
            if (text.includes('received')) return 'received';
            if (text.includes('pending')) return 'pending';
            if (text.includes('cancelled') || text.includes('canceled')) return 'cancelled';
            if (text.includes('draft')) return 'draft';

            return null;
        }

        function applyStatusClass(element, status) {
            if (!element || !status) return;

            element.classList.remove(
                'sf-status-draft',
                'sf-status-pending',
                'sf-status-approved',
                'sf-status-sent_to_bank',
                'sf-status-bank_rejected',
                'sf-status-paid',
                'sf-status-received',
                'sf-status-cancelled',
                'sf-status-not_received'
            );

            element.classList.add('sf-status-' + status);
        }

        document.querySelectorAll('[data-status], [data-salary-status], [data-salary-slip-status], [data-salary-slip-status-badge]').forEach(function (el) {
            var status = normalizeStatus(
                el.getAttribute('data-status') ||
                el.getAttribute('data-salary-status') ||
                el.getAttribute('data-salary-slip-status') ||
                el.textContent
            );

            el.classList.add('sf-portal-slip-status-badge');
            applyStatusClass(el, status);
        });

        document.querySelectorAll('span, small, td.status-cell, .status, .badge').forEach(function (el) {
            var text = (el.textContent || '').trim();

            if (text.length > 30) return;

            var status = normalizeStatus(text);

            if (!status) return;

            el.classList.add('sf-portal-slip-status-badge');
            applyStatusClass(el, status);
        });
    });
</script>


<style>
    .sf-payment-confirm-shell {
        width: min(100% - 32px, 1180px) !important;
        margin: 18px auto 22px !important;
        padding: 22px !important;
        border-radius: 32px !important;
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, .07), transparent 32%),
            rgba(255,255,255,.92) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 18px 50px rgba(15, 23, 42, .08) !important;
        box-sizing: border-box !important;
    }

    .sf-payment-confirm-head {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 18px !important;
        margin-bottom: 18px !important;
    }

    .sf-payment-confirm-kicker {
        font-size: 11px !important;
        font-weight: 900 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        color: #2563eb !important;
        margin-bottom: 6px !important;
    }

    .sf-payment-confirm-title {
        margin: 0 !important;
        font-size: 24px !important;
        line-height: 1.1 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        color: #0f172a !important;
    }

    .sf-payment-confirm-text {
        margin: 8px 0 0 !important;
        max-width: 640px !important;
        color: #64748b !important;
        font-size: 14px !important;
        line-height: 1.65 !important;
    }

    .sf-payment-confirm-status {
        flex: 0 0 auto !important;
        border-radius: 999px !important;
        padding: 9px 14px !important;
        background: #fff4d6 !important;
        color: #b45309 !important;
        border: 1px solid #fde68a !important;
        font-size: 11px !important;
        font-weight: 900 !important;
        letter-spacing: .07em !important;
        text-transform: uppercase !important;
    }

    .sf-payment-confirm-grid {
        display: grid !important;
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }

    @media (min-width: 900px) {
        .sf-payment-confirm-grid {
            grid-template-columns: 1fr 1fr !important;
        }
    }

    .sf-payment-choice {
        display: grid !important;
        grid-template-columns: auto 1fr !important;
        gap: 14px !important;
        align-items: start !important;
        padding: 18px !important;
        border-radius: 26px !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-sizing: border-box !important;
    }

    .sf-payment-choice-received {
        background: linear-gradient(135deg, #f0fdf4, #ffffff) !important;
        border-color: #bbf7d0 !important;
    }

    .sf-payment-choice-not {
        background: linear-gradient(135deg, #fff1f2, #ffffff) !important;
        border-color: #fecdd3 !important;
    }

    .sf-payment-choice-icon {
        width: 42px !important;
        height: 42px !important;
        border-radius: 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 18px !important;
        font-weight: 950 !important;
        background: #fff !important;
        color: #0f172a !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 8px 22px rgba(15,23,42,.06) !important;
    }

    .sf-payment-choice-body h3 {
        margin: 0 !important;
        font-size: 18px !important;
        font-weight: 950 !important;
        color: #0f172a !important;
        letter-spacing: -.03em !important;
    }

    .sf-payment-choice-body p {
        margin: 5px 0 0 !important;
        color: #64748b !important;
        font-size: 13px !important;
        line-height: 1.55 !important;
    }

    .sf-payment-note {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        min-height: 54px !important;
        resize: vertical !important;
        border-radius: 18px !important;
        border: 1px solid rgba(15,23,42,.10) !important;
        background: rgba(255,255,255,.86) !important;
        color: #0f172a !important;
        padding: 13px 15px !important;
        font-size: 13px !important;
        outline: none !important;
        box-sizing: border-box !important;
    }

    .sf-payment-note:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 4px rgba(37,99,235,.10) !important;
    }

    .sf-payment-btn {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        border: 0 !important;
        cursor: pointer !important;
        border-radius: 999px !important;
        padding: 14px 18px !important;
        font-size: 14px !important;
        font-weight: 950 !important;
        transition: transform .15s ease, box-shadow .15s ease !important;
    }

    .sf-payment-btn:hover {
        transform: translateY(-1px) !important;
    }

    .sf-payment-btn-received {
        background: #047857 !important;
        color: #fff !important;
        box-shadow: 0 14px 30px rgba(4,120,87,.18) !important;
    }

    .sf-payment-btn-not {
        background: #fff !important;
        color: #be123c !important;
        border: 1px solid rgba(190,18,60,.18) !important;
        box-shadow: 0 14px 30px rgba(190,18,60,.08) !important;
    }

    .sf-payment-confirm-summary {
        display: grid !important;
        gap: 12px !important;
        margin-top: 16px !important;
    }

    .sf-payment-confirm-summary > div {
        border-radius: 20px !important;
        background: #f8fafc !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        padding: 14px 16px !important;
    }

    .sf-payment-confirm-summary strong {
        display: block !important;
        margin-bottom: 5px !important;
        color: #0f172a !important;
        font-size: 13px !important;
    }

    .sf-payment-confirm-summary span {
        color: #64748b !important;
        font-size: 13px !important;
    }

    @media (max-width: 640px) {
        .sf-payment-confirm-shell {
            width: min(100% - 20px, 1180px) !important;
            padding: 16px !important;
            border-radius: 24px !important;
        }

        .sf-payment-confirm-head {
            flex-direction: column !important;
        }

        .sf-payment-confirm-title {
            font-size: 21px !important;
        }
    }
</style>
