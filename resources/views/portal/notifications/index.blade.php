@extends('portal.layouts.app')

@php
    $pageTitle = 'Portal Notifications';

    $totalNotifications = method_exists($notifications, 'total') ? $notifications->total() : $notifications->count();

    $notificationIcon = function (?string $category, ?string $title = null) {
        $text = strtolower(trim(($category ?? '') . ' ' . ($title ?? '')));

        if (str_contains($text, 'salary') || str_contains($text, 'payment') || str_contains($text, 'bank')) return 'salary';
        if (str_contains($text, 'file') || str_contains($text, 'document')) return 'file';
        if (str_contains($text, 'rotation')) return 'rotation';
        if (str_contains($text, 'travel') || str_contains($text, 'ticket')) return 'travel';
        if (str_contains($text, 'medical')) return 'medical';
        if (str_contains($text, 'visa')) return 'visa';
        if (str_contains($text, 'contract')) return 'contract';
        if (str_contains($text, 'test')) return 'bell';

        return 'bell';
    };

    $renderNotificationSvg = function (string $name, string $class = 'sf-notification-svg') {
        $icons = [
            'bell' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M6.75 10.75a5.25 5.25 0 0 1 10.5 0v3.4l1.5 2.35H5.25l1.5-2.35v-3.4Z"/><path d="M9.75 18.25a2.25 2.25 0 0 0 4.5 0"/></svg>',
            'salary' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.75 7.25h14.5A1.75 1.75 0 0 1 21 9v6a1.75 1.75 0 0 1-1.75 1.75H4.75A1.75 1.75 0 0 1 3 15V9a1.75 1.75 0 0 1 1.75-1.75Z"/><circle cx="12" cy="12" r="2.25"/><path d="M6.25 9.75v4.5M17.75 9.75v4.5"/></svg>',
            'file' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 3.75h7.25L18.75 8v10.25A2 2 0 0 1 16.75 20.25h-9.5a2 2 0 0 1-2-2V5.75a2 2 0 0 1 2-2Z"/><path d="M14.5 3.75V8h4.25M8.25 12h7.5M8.25 15h7.5"/></svg>',
            'rotation' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 7.25A7 7 0 0 1 19 12.35"/><path d="M19.25 7.25v5.25H14"/><path d="M16.75 16.75A7 7 0 0 1 5 11.65"/><path d="M4.75 16.75V11.5H10"/></svg>',
            'travel' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.75 13.5 20.25 6.75l-6.75 16.5-3.25-7.5-6.5-2.25Z"/><path d="M10.25 15.75 20.25 6.75"/></svg>',
            'medical' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.25 6.75V5.5A1.75 1.75 0 0 1 10 3.75h4a1.75 1.75 0 0 1 1.75 1.75v1.25"/><path d="M5.75 6.75h12.5A2.25 2.25 0 0 1 20.5 9v8.25a2.25 2.25 0 0 1-2.25 2.25H5.75a2.25 2.25 0 0 1-2.25-2.25V9a2.25 2.25 0 0 1 2.25-2.25Z"/><path d="M12 10v6M9 13h6"/></svg>',
            'visa' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.75 14.35 6l3.25-.25.25 3.25L20.25 12l-2.4 3 .25 3.25-3.25-.25L12 20.25 9.15 18l-3.25.25.25-3.25-2.4-3 2.4-3-.25-3.25L9.15 6 12 3.75Z"/><path d="m8.75 12.25 2.05 2.05 4.45-4.6"/></svg>',
            'contract' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 3.75h7.25L18.75 8v10.25A2 2 0 0 1 16.75 20.25h-9.5a2 2 0 0 1-2-2V5.75a2 2 0 0 1 2-2Z"/><path d="M14.5 3.75V8h4.25M8.25 11h7.5M8.25 14h7.5M8.25 17h4.5"/></svg>',
            'open' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 4.75h5.25V10"/><path d="M19.25 4.75 11.5 12.5"/><path d="M10 6.25H6.75a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V14"/></svg>',
            'check' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="m5.25 12.5 4.25 4.25 9.25-9.5"/></svg>',
            'trash' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M5.25 7.25h13.5"/><path d="M9.25 7.25V5.75a1.5 1.5 0 0 1 1.5-1.5h2.5a1.5 1.5 0 0 1 1.5 1.5v1.5"/><path d="M7.25 7.25 8 19.25a2 2 0 0 0 2 1.75h4a2 2 0 0 0 2-1.75l.75-12"/><path d="M10.25 10.75v6M13.75 10.75v6"/></svg>',
        ];

        return $icons[$name] ?? $icons['bell'];
    };

    $smallSvg = fn (string $name) => $renderNotificationSvg($name, 'sf-notification-svg-sm');
@endphp

@section('content')
    <style>
        .sf-notifications-hero {
            position: relative;
            overflow: hidden;
            border-radius: 34px;
            padding: 28px;
            background:
                radial-gradient(circle at 88% 12%, rgba(76,167,168,.18), transparent 30%),
                linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,251,255,.92));
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 22px 58px rgba(15,23,42,.08);
        }

        .sf-notifications-hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            gap: 22px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .sf-notifications-kicker {
            color: #2459d3;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .sf-notifications-title {
            margin: 0;
            color: #0f172a;
            font-size: 38px;
            line-height: 1.05;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .sf-notifications-subtitle {
            margin-top: 12px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            font-weight: 650;
            max-width: 850px;
        }

        .sf-notifications-stats {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .sf-notification-stat {
            min-width: 118px;
            border-radius: 26px;
            padding: 16px;
            text-align: center;
            background: rgba(255,255,255,.86);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 16px 40px rgba(15,23,42,.06);
        }

        .sf-notification-stat strong {
            display: block;
            color: #0f172a;
            font-size: 30px;
            line-height: 1;
            font-weight: 950;
        }

        .sf-notification-stat span {
            display: block;
            margin-top: 7px;
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sf-notifications-actions {
            margin-top: 22px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sf-notification-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 999px;
            border: 0;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: 950;
            white-space: nowrap;
        }

        .sf-notification-btn-read {
            color: #047857;
            background: #ecfdf5;
            border: 1px solid rgba(16,185,129,.20);
        }

        .sf-notification-btn-clear {
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid rgba(239,68,68,.18);
        }

        .sf-notifications-list {
            display: grid;
            gap: 16px;
            margin-top: 24px;
        }

        .sf-notification-card {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 16px;
            border-radius: 30px;
            padding: 18px;
            background:
                radial-gradient(circle at top right, rgba(36,89,211,.08), transparent 34%),
                rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 18px 48px rgba(15,23,42,.06);
            transition: .18s ease;
        }

        .sf-notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 60px rgba(15,23,42,.10);
        }

        .sf-notification-card.is-unread {
            border-color: rgba(37,99,235,.24);
            background:
                radial-gradient(circle at top right, rgba(37,99,235,.12), transparent 34%),
                linear-gradient(135deg, rgba(239,246,255,.98), rgba(255,255,255,.94));
        }

        .sf-notification-icon-box {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 20px;
            background: #eff6ff;
            border: 1px solid rgba(36,89,211,.14);
            flex-shrink: 0;
        }

        .sf-notification-svg {
            width: 27px;
            height: 27px;
            display: block;
            stroke: #2459d3;
            stroke-width: 1.9;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sf-notification-svg-sm {
            width: 17px;
            height: 17px;
            display: inline-block;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sf-notification-title-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sf-notification-card-title {
            color: #0f172a;
            font-size: 20px;
            line-height: 1.2;
            font-weight: 950;
            letter-spacing: -.035em;
        }

        .sf-notification-message {
            margin-top: 9px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.65;
            font-weight: 700;
        }

        .sf-notification-meta {
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 850;
        }

        .sf-notification-badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 8px 11px;
            background: #f8fafc;
            color: #475569;
            border: 1px solid rgba(15,23,42,.10);
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .sf-notification-badge-unread {
            background: #eff6ff;
            color: #2459d3;
            border-color: rgba(36,89,211,.16);
        }

        .sf-notification-badge-read {
            background: #f8fafc;
            color: #64748b;
            border-color: rgba(15,23,42,.10);
        }

        .sf-notification-open {
            align-self: center;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 42px;
            padding: 0 15px;
            border-radius: 999px;
            text-decoration: none;
            background: #eff6ff;
            color: #2459d3;
            border: 1px solid rgba(36,89,211,.16);
            font-size: 12px;
            font-weight: 950;
            white-space: nowrap;
        }

        .sf-notification-empty {
            margin-top: 24px;
            border-radius: 30px;
            padding: 42px 24px;
            background: rgba(255,255,255,.88);
            border: 1px dashed rgba(15,23,42,.18);
            text-align: center;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            font-weight: 700;
        }

        .sf-notification-empty strong {
            display: block;
            color: #0f172a;
            font-size: 24px;
            font-weight: 950;
            margin-bottom: 8px;
        }

        .sf-notifications-pagination {
            margin-top: 22px;
        }

        .dark .sf-notifications-hero,
        .dark .sf-notification-stat,
        .dark .sf-notification-card,
        .dark .sf-notification-empty {
            background: rgba(15,23,42,.86);
            border-color: rgba(255,255,255,.10);
        }

        .dark .sf-notifications-title,
        .dark .sf-notification-stat strong,
        .dark .sf-notification-card-title,
        .dark .sf-notification-empty strong {
            color: #ffffff;
        }

        .dark .sf-notifications-subtitle,
        .dark .sf-notification-stat span,
        .dark .sf-notification-message,
        .dark .sf-notification-empty {
            color: rgba(226,232,240,.76);
        }

        @media (max-width: 850px) {
            .sf-notification-card {
                grid-template-columns: 1fr;
            }

            .sf-notification-open {
                justify-content: center;
            }

            .sf-notifications-title {
                font-size: 32px;
            }
        }
    </style>

    <section class="sf-notifications-hero">
        <div class="sf-notifications-hero-inner">
            <div>
                <div class="sf-notifications-kicker">Employee Notifications</div>
                <h1 class="sf-notifications-title">Notifications Center</h1>
                <div class="sf-notifications-subtitle">
                    All employee-visible alerts and updates related to your salary slips, files, rotations, travel,
                    ticket, medical, visa, and contract activity will appear here.
                </div>
            </div>

            <div class="sf-notifications-stats">
                <div class="sf-notification-stat">
                    <strong>{{ number_format((int) $totalNotifications) }}</strong>
                    <span>Total</span>
                </div>

                <div class="sf-notification-stat">
                    <strong>{{ number_format((int) ($portalUnreadNotificationsCount ?? 0)) }}</strong>
                    <span>Unread</span>
                </div>
            </div>
        </div>

        <div class="sf-notifications-actions">
            <form method="POST" action="{{ route('portal.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="sf-notification-btn sf-notification-btn-read">
                    {!! $smallSvg('check') !!}
                    Mark All Read
                </button>
            </form>

            <form method="POST" action="{{ route('portal.notifications.clear-all') }}" onsubmit="return confirm('Clear all notifications from your portal?');">
                @csrf
                <button type="submit" class="sf-notification-btn sf-notification-btn-clear">
                    {!! $smallSvg('trash') !!}
                    Clear All
                </button>
            </form>
        </div>
    </section>

    @if($notifications->count())
        <section class="sf-notifications-list">
            @foreach($notifications as $item)
                @php
                    $iconKey = $notificationIcon($item->category, $item->title . ' ' . $item->message);
                    $categoryLabel = ucfirst(str_replace('_', ' ', (string) ($item->category ?: 'portal')));
                    $createdLabel = $item->created_at?->format('Y-m-d H:i') ?: '-';
                @endphp

                <article class="sf-notification-card {{ ! $item->is_read ? 'is-unread' : '' }}">
                    <div class="sf-notification-icon-box">
                        {!! $renderNotificationSvg($iconKey) !!}
                    </div>

                    <div>
                        <div class="sf-notification-title-row">
                            <div class="sf-notification-card-title">{{ $item->title }}</div>

                            @if($item->is_read)
                                <span class="sf-notification-badge sf-notification-badge-read">Read</span>
                            @else
                                <span class="sf-notification-badge sf-notification-badge-unread">Unread</span>
                            @endif
                        </div>

                        @if($item->message)
                            <div class="sf-notification-message">{{ $item->message }}</div>
                        @endif

                        <div class="sf-notification-meta">
                            <span>{{ $categoryLabel }}</span>
                            <span>•</span>
                            <span>{{ $createdLabel }}</span>

                            @if($item->emailed_at)
                                <span>•</span>
                                <span>Email sent</span>
                            @endif
                        </div>
                    </div>

                    <a class="sf-notification-open" href="{{ route('portal.notifications.open', $item) }}">
                        {!! $smallSvg('open') !!}
                        Open
                    </a>
                </article>
            @endforeach
        </section>

        <div class="sf-notifications-pagination">
            {{ $notifications->links() }}
        </div>
    @else
        <section class="sf-notification-empty">
            <strong>No notifications found</strong>
            New portal alerts will appear here when your ERP record is updated.
        </section>
    @endif
@endsection
