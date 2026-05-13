@php
    $routeRecord = request()->route('record');
    $operation = null;

    if ($routeRecord) {
        $operation = \App\Models\TreasuryOperation::query()->find($routeRecord);
    }

    $isEdit = filled($routeRecord);
    $title = $isEdit
        ? 'Edit Treasury Operation'
        : 'Create Treasury Operation';

    $kicker = $isEdit
        ? 'Treasury Operations › Edit'
        : 'Treasury Operations › Create';

    $subtitle = $isEdit
        ? 'Update movement details, accounts, amounts, currencies, fees, and posting status.'
        : 'Create an internal treasury movement between accounts, banks, cash, or clearing balances.';

    $badge = $isEdit
        ? ($operation?->operation_no ?: ('Operation #' . $routeRecord))
        : 'New Treasury Operation';

    $statusBadge = $isEdit
        ? (($operation?->is_posted ?? false) ? 'Posted' : 'Draft / Not Posted')
        : 'Ready for Entry';
@endphp

<style>
    .fi-header,
    .fi-breadcrumbs,
    nav[aria-label="Breadcrumb"],
    .fi-page-header {
        display: none !important;
    }

    .sf-to-hero {
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

    .sf-to-hero::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 4px;
        background: linear-gradient(90deg, #4ca7a8, #b38b2f);
    }

    .sf-to-hero-left,
    .sf-to-hero-right {
        position: relative;
        z-index: 1;
    }

    .sf-to-kicker {
        font-size: 14px;
        color: rgba(255,255,255,.78);
        margin-bottom: 8px;
    }

    .sf-to-title {
        font-size: 52px;
        line-height: .95;
        font-weight: 950;
        color: #fff;
        letter-spacing: -.04em;
    }

    .sf-to-subtitle {
        margin-top: 16px;
        max-width: 860px;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255,255,255,.84);
    }

    .sf-to-badges {
        margin-top: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sf-to-badge {
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

    .sf-to-hero-right {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        flex-shrink: 0;
    }

    .sf-to-btn {
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

    .sf-to-btn-secondary {
        background: rgba(255,255,255,.12);
        color: #fff !important;
        border: 1px solid rgba(255,255,255,.16);
    }

    .sf-to-btn-primary {
        background: #f2b705;
        color: #3b2a00 !important;
        box-shadow: 0 10px 20px rgba(242,183,5,.22);
    }

    .sf-to-btn:hover {
        transform: translateY(-1px);
    }

    .dark .sf-to-hero {
        border-color: rgba(255,255,255,.10);
        box-shadow: 0 18px 34px rgba(0,0,0,.22);
    }

    @media (max-width: 900px) {
        .sf-to-hero {
            flex-direction: column;
        }

        .sf-to-title {
            font-size: 40px;
        }

        .sf-to-hero-right {
            width: 100%;
            flex-wrap: wrap;
        }
    }
</style>

<section class="sf-to-hero">
    <div class="sf-to-hero-left">
        <div class="sf-to-kicker">{{ $kicker }}</div>
        <div class="sf-to-title">{{ $title }}</div>
        <div class="sf-to-subtitle">{{ $subtitle }}</div>

        <div class="sf-to-badges">
            <span class="sf-to-badge">{{ $badge }}</span>
            <span class="sf-to-badge">{{ $statusBadge }}</span>
        </div>
    </div>

    <div class="sf-to-hero-right">
        <a href="{{ \App\Filament\Resources\TreasuryOperations\TreasuryOperationResource::getUrl('index') }}" class="sf-to-btn sf-to-btn-secondary">
            Back to Operations
        </a>

        @if($isEdit && $operation)
            <a href="{{ \App\Filament\Resources\TreasuryOperations\TreasuryOperationResource::getUrl('view', ['record' => $operation]) }}" class="sf-to-btn sf-to-btn-primary">
                View Operation
            </a>
        @endif
    </div>
</section>
