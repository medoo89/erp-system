@props([
    'kicker' => '',
    'title' => '',
    'subtitle' => '',
    'badge' => null,
    'actionLabel' => null,
    'actionUrl' => null,
    'actionType' => 'link',
    'actionWireClick' => null,
    'actionColor' => 'gold',
])

<style>
    /*
    | Hide Filament default top breadcrumb/header on pages that use this unified hero.
    | The breadcrumb remains inside the colored hero only.
    */
    .fi-header,
    .fi-breadcrumbs,
    nav[aria-label="Breadcrumb"],
    .fi-page-header {
        display: none !important;
    }

    .sf-finance-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        border: 1px solid #d7e2e5;
        border-radius: 22px;
        padding: 26px 28px;
        margin-bottom: 24px;
        background: linear-gradient(135deg, #18344d 0%, #234d6f 50%, #2f6f73 100%);
        box-shadow: 0 18px 34px rgba(15, 23, 42, 0.10);
        position: relative;
        overflow: hidden;
    }

    .dark .sf-finance-hero {
        border-color: rgba(255,255,255,.10);
        box-shadow: 0 18px 34px rgba(0,0,0,.22);
    }

    .sf-finance-hero::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 4px;
        background: linear-gradient(90deg, #4ca7a8, #b38b2f);
    }

    .sf-finance-hero-left,
    .sf-finance-hero-right {
        position: relative;
        z-index: 1;
    }

    .sf-finance-hero-right {
        flex-shrink: 0;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .sf-finance-hero-kicker {
        font-size: 14px;
        color: rgba(255,255,255,.78);
    }

    .sf-finance-hero-title {
        margin-top: 8px;
        font-size: 56px;
        line-height: .95;
        font-weight: 900;
        color: #fff;
        letter-spacing: -.045em;
    }

    .sf-finance-hero-sub {
        margin-top: 16px;
        max-width: 920px;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255,255,255,.84);
    }

    .sf-finance-hero-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 18px;
        min-height: 38px;
        padding: 0 16px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        color: #ffffff;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .sf-finance-hero-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 96px;
        padding: 0 18px;
        border-radius: 999px;
        border: 0;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 900;
        transition: all .18s ease;
        cursor: pointer;
    }

    .sf-finance-hero-btn:hover {
        transform: translateY(-1px);
    }

    .sf-finance-hero-btn--gold {
        background: #f2b705;
        color: #3b2a00 !important;
        box-shadow: 0 10px 20px rgba(242,183,5,.22);
    }

    .sf-finance-hero-btn--gold:hover {
        box-shadow: 0 14px 24px rgba(242,183,5,.26);
    }

    .sf-finance-hero-btn--danger {
        background: #ef4444;
        color: #ffffff !important;
        box-shadow: 0 10px 20px rgba(239,68,68,.20);
    }

    .sf-finance-hero-btn--danger:hover {
        box-shadow: 0 14px 24px rgba(239,68,68,.26);
    }

    .sf-finance-hero-btn--teal {
        background: #14b8a6;
        color: #042f2e !important;
        box-shadow: 0 10px 20px rgba(20,184,166,.20);
    }

    @media (max-width: 900px) {
        .sf-finance-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .sf-finance-hero-title {
            font-size: 42px;
        }

        .sf-finance-hero-right {
            width: 100%;
        }

        .sf-finance-hero-btn {
            width: 100%;
        }
    }
</style>

<section class="sf-finance-hero">
    <div class="sf-finance-hero-left">
        @if($kicker)
            <div class="sf-finance-hero-kicker">{{ $kicker }}</div>
        @endif

        <div class="sf-finance-hero-title">{{ $title }}</div>

        @if($subtitle)
            <div class="sf-finance-hero-sub">{{ $subtitle }}</div>
        @endif

        @if($badge)
            <div class="sf-finance-hero-badge">{{ $badge }}</div>
        @endif
    </div>

    @if($actionLabel)
        <div class="sf-finance-hero-right">
            @if($actionType === 'button')
                <button
                    type="button"
                    class="sf-finance-hero-btn sf-finance-hero-btn--{{ $actionColor }}"
                    @if($actionWireClick) wire:click="{{ $actionWireClick }}" @endif
                >
                    {{ $actionLabel }}
                </button>
            @else
                <a href="{{ $actionUrl }}" class="sf-finance-hero-btn sf-finance-hero-btn--{{ $actionColor }}">
                    {{ $actionLabel }}
                </a>
            @endif
        </div>
    @endif
</section>
