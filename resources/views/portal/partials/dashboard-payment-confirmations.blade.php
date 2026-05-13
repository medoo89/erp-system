@php
    use App\Models\SalarySlip;

    $pendingReceiptSlips = collect($pendingPaymentConfirmations ?? [])->filter(function ($slip) {
        return in_array($slip->employee_confirmation_status, [null, '', 'pending'], true);
    })->values();

    $methodLabel = function ($slip) {
        return $slip->payment_method === SalarySlip::PAYMENT_METHOD_CASH
            ? 'Cash Payment'
            : 'Bank Transfer';
    };

    $periodLabel = function ($slip) {
        $month = $slip->salary_month ? str_pad((string) $slip->salary_month, 2, '0', STR_PAD_LEFT) : '--';
        $year = $slip->salary_year ?: '----';

        return $month . ' / ' . $year;
    };
@endphp

@if($pendingReceiptSlips->isNotEmpty())
    <section class="sf-md3-payment-confirmations">
        <div class="sf-md3-payment-bg"></div>

        <div class="sf-md3-payment-head">
            <div>
                <div class="sf-md3-payment-kicker">Action Required</div>
                <h2>Salary Payment Confirmation</h2>
                <p>
                    You have salary payment receipts waiting for confirmation. Please confirm once the payment has been received.
                </p>
            </div>

            <div class="sf-md3-payment-count">
                <strong>{{ $pendingReceiptSlips->count() }}</strong>
                <span>Pending</span>
            </div>
        </div>

        <div class="sf-md3-payment-list">
            @foreach($pendingReceiptSlips as $receiptSlip)
                @php
                    $isCash = $receiptSlip->payment_method === SalarySlip::PAYMENT_METHOD_CASH;
                    $statusLabel = SalarySlip::statusLabels()[$receiptSlip->status] ?? ucfirst(str_replace('_', ' ', (string) $receiptSlip->status));
                    $confirmText = $isCash ? 'Confirm Cash Received' : 'Confirm Received';

                    $receiptCurrency = $receiptSlip->currency ?: 'EUR';
                    $receiptAmount = (float) (
                        $receiptSlip->payment_total_amount
                        ?? $receiptSlip->final_payable_amount
                        ?? $receiptSlip->net_amount
                        ?? 0
                    );
                @endphp

                <article class="sf-md3-payment-item {{ $isCash ? 'sf-md3-payment-item--cash' : 'sf-md3-payment-item--bank' }}">
                    <div class="sf-md3-payment-icon {{ $isCash ? 'sf-md3-payment-icon--cash' : 'sf-md3-payment-icon--bank' }}">
                        <span class="material-symbols-rounded">
                            {{ $isCash ? 'payments' : 'account_balance' }}
                        </span>
                    </div>

                    <div class="sf-md3-payment-main">
                        <div class="sf-md3-payment-title">
                            Salary Slip {{ $periodLabel($receiptSlip) }}
                        </div>

                        <div class="sf-md3-payment-meta">
                            <span class="sf-md3-payment-method">{{ $methodLabel($receiptSlip) }}</span>
                            <span class="sf-md3-payment-dot">•</span>
                            <span class="sf-md3-payment-status">{{ $statusLabel }}</span>
                        </div>

                        <div class="sf-md3-payment-amount">
                            {{ number_format($receiptAmount, 2) }} {{ $receiptCurrency }}
                        </div>

                        @if($receiptSlip->client?->name || $receiptSlip->project?->name)
                            <div class="sf-md3-payment-submeta">
                                {{ $receiptSlip->client?->name ?: '-' }} · {{ $receiptSlip->project?->name ?: '-' }}
                            </div>
                        @endif
                    </div>

                    <div class="sf-md3-payment-actions">
                        <form method="POST" action="{{ route('portal.salary-slips.confirm-received', $receiptSlip) }}">
                            @csrf
                            <button type="submit" class="sf-md3-payment-btn sf-md3-payment-btn--confirm">
                                {{ $confirmText }}
                            </button>
                        </form>

                        @if(! $isCash)
                            <form method="POST" action="{{ route('portal.salary-slips.not-received', $receiptSlip) }}" onsubmit="return confirm('Are you sure you want to report this salary payment as not received?');">
                                @csrf
                                <button type="submit" class="sf-md3-payment-btn sf-md3-payment-btn--danger">
                                    Not Received
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('portal.salary-slips.show', $receiptSlip) }}" class="sf-md3-payment-btn sf-md3-payment-btn--open">
                            Open
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif

<style>
    .sf-md3-payment-confirmations {
        position: relative;
        overflow: hidden;
        width: min(100% - 32px, 1180px);
        margin: 22px auto;
        padding: 24px;
        border-radius: 34px;
        background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(240,253,250,.92));
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 24px 64px rgba(15,23,42,.10);
    }

    .sf-md3-payment-bg {
        position: absolute;
        inset: auto -80px -120px auto;
        width: 320px;
        height: 320px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(16,185,129,.22), transparent 65%);
        pointer-events: none;
    }

    .sf-md3-payment-head {
        position: relative;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 22px;
        margin-bottom: 18px;
    }

    .sf-md3-payment-kicker {
        color: #d97706;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .sf-md3-payment-head h2 {
        margin: 0;
        color: #0f172a;
        font-size: 30px;
        line-height: 1.05;
        font-weight: 950;
        letter-spacing: -.05em;
    }

    .sf-md3-payment-head p {
        margin: 10px 0 0;
        max-width: 820px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.65;
        font-weight: 700;
    }

    .sf-md3-payment-count {
        min-width: 104px;
        padding: 14px 16px;
        border-radius: 24px;
        text-align: center;
        background: rgba(255,255,255,.82);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 14px 34px rgba(15,23,42,.06);
    }

    .sf-md3-payment-count strong {
        display: block;
        color: #0f172a;
        font-size: 26px;
        font-weight: 950;
        line-height: 1;
    }

    .sf-md3-payment-count span {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sf-md3-payment-list {
        position: relative;
        display: grid;
        gap: 12px;
    }

    .sf-md3-payment-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border-radius: 26px;
        background: rgba(255,255,255,.88);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 16px 42px rgba(15,23,42,.055);
    }

    .sf-md3-payment-item--cash {
        border-color: rgba(16,185,129,.24);
        background: linear-gradient(135deg, rgba(236,253,245,.98), rgba(255,255,255,.92));
    }

    .sf-md3-payment-item--bank {
        border-color: rgba(37,99,235,.22);
        background: linear-gradient(135deg, rgba(239,246,255,.98), rgba(255,255,255,.92));
    }

    .sf-md3-payment-icon {
        width: 58px;
        height: 58px;
        display: grid;
        place-items: center;
        border-radius: 20px;
        background: rgba(37,99,235,.10);
        border: 1px solid rgba(37,99,235,.16);
        box-shadow: 0 14px 28px rgba(37,99,235,.10);
        color: #1d4ed8;
        flex-shrink: 0;
    }

    .sf-md3-payment-icon span {
        font-size: 30px;
        line-height: 1;
        font-variation-settings: 'FILL' 0, 'wght' 650, 'GRAD' 0, 'opsz' 24;
    }

    .sf-md3-payment-icon--cash {
        background: rgba(16,185,129,.10);
        border-color: rgba(16,185,129,.18);
        color: #047857;
        box-shadow: 0 14px 28px rgba(16,185,129,.10);
    }

    .sf-md3-payment-icon--bank {
        background: rgba(37,99,235,.10);
        border-color: rgba(37,99,235,.18);
        color: #1d4ed8;
        box-shadow: 0 14px 28px rgba(37,99,235,.10);
    }

    .sf-md3-payment-title {
        color: #0f172a;
        font-size: 17px;
        font-weight: 950;
        letter-spacing: -.02em;
    }

    .sf-md3-payment-meta,
    .sf-md3-payment-submeta {
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: 13px;
        font-weight: 800;
    }

    .sf-md3-payment-method,
    .sf-md3-payment-status {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 11px;
        border-radius: 999px;
        background: rgba(241,245,249,.95);
        border: 1px solid rgba(15,23,42,.07);
        color: #475569;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .sf-md3-payment-dot {
        color: #cbd5e1;
        font-weight: 950;
    }

    .sf-md3-payment-amount {
        margin-top: 8px;
        color: #0f172a;
        font-size: 20px;
        line-height: 1.05;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .sf-md3-payment-submeta {
        color: #64748b;
        font-size: 12px;
        font-weight: 850;
    }

    .sf-md3-payment-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sf-md3-payment-btn {
        border: 0;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 15px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 950;
        white-space: nowrap;
    }

    .sf-md3-payment-btn--confirm {
        color: white;
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 12px 28px rgba(16,185,129,.24);
    }

    .sf-md3-payment-btn--danger {
        color: white;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        box-shadow: 0 12px 28px rgba(239,68,68,.20);
    }

    .sf-md3-payment-btn--open {
        color: #2563eb;
        background: #ffffff;
        border: 1px solid rgba(37,99,235,.18);
    }

    .dark .sf-md3-payment-confirmations {
        background: linear-gradient(135deg, rgba(15,23,42,.96), rgba(20,83,45,.70));
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-md3-payment-head h2,
    .dark .sf-md3-payment-title,
    .dark .sf-md3-payment-count strong {
        color: #ffffff;
    }

    .dark .sf-md3-payment-head p,
    .dark .sf-md3-payment-meta,
    .dark .sf-md3-payment-submeta,
    .dark .sf-md3-payment-count span {
        color: rgba(226,232,240,.78);
    }

    .dark .sf-md3-payment-count,
    .dark .sf-md3-payment-item {
        background: rgba(15,23,42,.72);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-md3-payment-icon {
        background: rgba(255,255,255,.08);
    }

    @media (max-width: 900px) {
        .sf-md3-payment-head,
        .sf-md3-payment-item {
            grid-template-columns: 1fr;
            flex-direction: column;
            align-items: stretch;
        }

        .sf-md3-payment-item {
            display: flex;
            align-items: stretch;
        }

        .sf-md3-payment-actions {
            justify-content: flex-start;
        }
    }
</style>


<style id="sf-md3-payment-confirmations-polish-final">
    .sf-md3-payment-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        flex-wrap: nowrap !important;
    }

    .sf-md3-payment-actions form {
        margin: 0 !important;
    }

    .sf-md3-payment-btn {
        min-height: 42px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: .02em !important;
        border: 1px solid transparent !important;
        box-shadow: 0 12px 26px rgba(15,23,42,.06) !important;
        transition: transform .16s ease, box-shadow .16s ease, filter .16s ease !important;
    }

    .sf-md3-payment-btn:hover {
        transform: translateY(-1px) !important;
        filter: saturate(1.08) !important;
    }

    .sf-md3-payment-btn--confirm {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
        border-color: rgba(16,185,129,.22) !important;
        color: #047857 !important;
    }

    .sf-md3-payment-btn--danger {
        background: linear-gradient(135deg, #fff1f2, #fee2e2) !important;
        border-color: rgba(239,68,68,.22) !important;
        color: #b91c1c !important;
    }

    .sf-md3-payment-btn--open {
        background: rgba(255,255,255,.92) !important;
        border-color: rgba(37,99,235,.16) !important;
        color: #1d4ed8 !important;
        text-decoration: none !important;
    }

    .sf-md3-payment-item {
        grid-template-columns: auto minmax(0, 1fr) auto !important;
    }

    .dark .sf-md3-payment-method,
    .dark .sf-md3-payment-status {
        background: rgba(15,23,42,.62) !important;
        border-color: rgba(255,255,255,.10) !important;
        color: rgba(226,232,240,.86) !important;
    }

    .dark .sf-md3-payment-amount {
        color: #f8fafc !important;
    }

    .dark .sf-md3-payment-icon--bank {
        background: rgba(37,99,235,.18) !important;
        border-color: rgba(147,197,253,.20) !important;
        color: #bfdbfe !important;
    }

    .dark .sf-md3-payment-icon--cash {
        background: rgba(16,185,129,.18) !important;
        border-color: rgba(110,231,183,.20) !important;
        color: #a7f3d0 !important;
    }

    @media (max-width: 900px) {
        .sf-md3-payment-item {
            grid-template-columns: auto minmax(0, 1fr) !important;
        }

        .sf-md3-payment-actions {
            grid-column: 1 / -1 !important;
            justify-content: flex-start !important;
            flex-wrap: wrap !important;
        }
    }
</style>

<style id="sf-md3-payment-confirmation-strong-colors-final">
    /* Status pill: Sent to Bank = orange */
    .sf-md3-payment-status {
        background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
        border-color: rgba(249,115,22,.34) !important;
        color: #c2410c !important;
        box-shadow: 0 8px 18px rgba(249,115,22,.10) !important;
    }

    /* Confirm Received = stronger green */
    .sf-md3-payment-btn--confirm {
        background: linear-gradient(135deg, #16a34a, #059669) !important;
        border-color: rgba(5,150,105,.55) !important;
        color: #ffffff !important;
        box-shadow: 0 14px 30px rgba(5,150,105,.24) !important;
    }

    .sf-md3-payment-btn--confirm:hover {
        box-shadow: 0 18px 38px rgba(5,150,105,.32) !important;
    }

    /* Not Received = clear red */
    .sf-md3-payment-btn--danger {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        border-color: rgba(220,38,38,.55) !important;
        color: #ffffff !important;
        box-shadow: 0 14px 30px rgba(220,38,38,.22) !important;
    }

    .sf-md3-payment-btn--danger:hover {
        box-shadow: 0 18px 38px rgba(220,38,38,.30) !important;
    }

    .dark .sf-md3-payment-status {
        background: rgba(124,45,18,.72) !important;
        border-color: rgba(251,146,60,.38) !important;
        color: #fed7aa !important;
    }
</style>

<style id="sf-md3-payment-confirm-vs-not-received-final-strong">
    /* Confirm Received = GREEN */
    .sf-md3-payment-confirmations .sf-md3-payment-actions form button.sf-md3-payment-btn--confirm,
    .sf-md3-payment-confirmations button.sf-md3-payment-btn--confirm {
        background: linear-gradient(135deg, #16a34a, #059669) !important;
        border: 1px solid rgba(5,150,105,.55) !important;
        color: #ffffff !important;
        box-shadow: 0 14px 30px rgba(5,150,105,.26) !important;
    }

    .sf-md3-payment-confirmations .sf-md3-payment-actions form button.sf-md3-payment-btn--confirm:hover,
    .sf-md3-payment-confirmations button.sf-md3-payment-btn--confirm:hover {
        background: linear-gradient(135deg, #15803d, #047857) !important;
        color: #ffffff !important;
        box-shadow: 0 18px 38px rgba(5,150,105,.34) !important;
    }

    /* Not Received = RED */
    .sf-md3-payment-confirmations .sf-md3-payment-actions form button.sf-md3-payment-btn--danger,
    .sf-md3-payment-confirmations button.sf-md3-payment-btn--danger {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        border: 1px solid rgba(220,38,38,.58) !important;
        color: #ffffff !important;
        box-shadow: 0 14px 30px rgba(220,38,38,.28) !important;
    }

    .sf-md3-payment-confirmations .sf-md3-payment-actions form button.sf-md3-payment-btn--danger:hover,
    .sf-md3-payment-confirmations button.sf-md3-payment-btn--danger:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        color: #ffffff !important;
        box-shadow: 0 18px 38px rgba(220,38,38,.36) !important;
    }

    /* Open stays clean white / blue */
    .sf-md3-payment-confirmations .sf-md3-payment-btn--open {
        background: rgba(255,255,255,.94) !important;
        border: 1px solid rgba(37,99,235,.16) !important;
        color: #2563eb !important;
        box-shadow: 0 10px 24px rgba(37,99,235,.08) !important;
    }
</style>
