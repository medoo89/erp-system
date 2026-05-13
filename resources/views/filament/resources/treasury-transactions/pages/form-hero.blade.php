@php
    $routeRecord = request()->route('record');
    $transaction = null;

    if ($routeRecord) {
        $transaction = \App\Models\TreasuryTransaction::query()->find($routeRecord);
    }

    $isEdit = filled($routeRecord);
    $title = $isEdit ? 'Edit Treasury Transaction' : 'Create Treasury Transaction';
    $kicker = $isEdit ? 'Treasury Transactions › Edit' : 'Treasury Transactions › Create';

    $subtitle = $isEdit
        : 'Record an incoming or outgoing treasury transaction linked to an account, client, project, employment, or manual reference.';

    $badge = $isEdit
        ? ($transaction?->transaction_no ?: ('Transaction #' . $routeRecord))
        : 'New Treasury Transaction';

    $statusBadge = $isEdit
        ? (($transaction?->is_posted ?? false) ? 'Posted' : 'Draft / Not Posted')
        : 'Ready for Entry';
@endphp

<style>
    .fi-header,
    .fi-breadcrumbs,
    nav[aria-label="Breadcrumb"],
    .fi-page-header {
        display: none !important;
    }

    .sf-tt-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        border: 1px solid #d7e2e5;
        border-radius: 22px;
        padding: 26px 28px;
        background: linear-gradient(135deg, #18344d 0%, #234d6f 50%, #2f6f73 100%);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.10);
        position: relative;
        overflow: hidden;
        margin-bottom: 22px;
    }

    .sf-tt-hero::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 4px;
        background: linear-gradient(90deg, #4ca7a8, #b38b2f);
    }

    .sf-tt-hero-left,
    .sf-tt-hero-right {
        position: relative;
        z-index: 1;
    }

    .sf-tt-kicker {
        font-size: 14px;
        color: rgba(255,255,255,.78);
        margin-bottom: 8px;
    }

    .sf-tt-title {
        font-size: 52px;
        line-height: .95;
        font-weight: 950;
        color: #fff;
        letter-spacing: -.04em;
    }

    .sf-tt-subtitle {
        margin-top: 16px;
        max-width: 860px;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255,255,255,.84);
    }

    .sf-tt-badges {
        margin-top: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sf-tt-badge {
        display: inline-flex;
        align-items: center;
        padding: 9px 14px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
        border: 1px solid rgba(255,255,255,.16);
        background: rgba(255,255,255,.12);
        color: #fff;
    }

    .sf-tt-hero-right {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        flex-shrink: 0;
    }

    .sf-tt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 18px;
        border-radius: 999px;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 900;
        transition: all .18s ease;
        white-space: nowrap;
    }

    .sf-tt-btn-secondary {
        background: rgba(255,255,255,.12);
        color: #fff !important;
        border: 1px solid rgba(255,255,255,.16);
    }

    .sf-tt-btn-primary {
        background: #f2b705;
        color: #3b2a00 !important;
        box-shadow: 0 10px 20px rgba(242,183,5,.22);
    }

    .sf-tt-btn:hover { transform: translateY(-1px); }

    @media (max-width: 900px) {
        .sf-tt-hero { flex-direction: column; }
        .sf-tt-title { font-size: 40px; }
        .sf-tt-hero-right { width: 100%; flex-wrap: wrap; }
    }
</style>

<section class="sf-tt-hero">
    <div class="sf-tt-hero-left">
        <div class="sf-tt-kicker">{{ $kicker }}</div>
        <div class="sf-tt-title">{{ $title }}</div>
        <div class="sf-tt-subtitle">{{ $subtitle }}</div>

        <div class="sf-tt-badges">
            <span class="sf-tt-badge">{{ $badge }}</span>
            <span class="sf-tt-badge">{{ $statusBadge }}</span>
        </div>
    </div>

    <div class="sf-tt-hero-right">
        <a href="{{ \App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource::getUrl('index') }}" class="sf-tt-btn sf-tt-btn-secondary">
            Back to Transactions
        </a>

        @if($isEdit && $transaction)
            <a href="{{ \App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource::getUrl('view', ['record' => $transaction]) }}" class="sf-tt-btn sf-tt-btn-primary">
                View Transaction
            </a>
        @endif
    </div>
</section>
