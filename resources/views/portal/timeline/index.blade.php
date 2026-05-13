@extends('portal.layouts.app')

@php
    $pageTitle = 'Portal Updates';

    $totalEvents = method_exists($events, 'total') ? $events->total() : $events->count();

    $updateIcon = function (?string $status, ?string $title = null) {
        $text = strtolower(trim(($status ?? '') . ' ' . ($title ?? '')));


        return 'update';
    };

    $renderSvgIcon = function (string $name, string $class = 'sf-timeline-svg') {
        $icons = [
            'update' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 6.25v6l4 2"/><path d="M20.25 12A8.25 8.25 0 1 1 12 3.75"/><path d="M17.25 3.75h3v3"/></svg>',
            'salary' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.75 7.25h14.5A1.75 1.75 0 0 1 21 9v6a1.75 1.75 0 0 1-1.75 1.75H4.75A1.75 1.75 0 0 1 3 15V9a1.75 1.75 0 0 1 1.75-1.75Z"/><circle cx="12" cy="12" r="2.25"/><path d="M6.25 9.75v4.5M17.75 9.75v4.5"/></svg>',
            'rotation' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 7.25A7 7 0 0 1 19 12.35"/><path d="M19.25 7.25v5.25H14"/><path d="M16.75 16.75A7 7 0 0 1 5 11.65"/><path d="M4.75 16.75V11.5H10"/></svg>',
            'travel' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M3.75 13.5 20.25 6.75l-6.75 16.5-3.25-7.5-6.5-2.25Z"/><path d="M10.25 15.75 20.25 6.75"/></svg>',
            'file' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 3.75h7.25L18.75 8v10.25A2 2 0 0 1 16.75 20.25h-9.5a2 2 0 0 1-2-2V5.75a2 2 0 0 1 2-2Z"/><path d="M14.5 3.75V8h4.25M8.25 12h7.5M8.25 15h7.5"/></svg>',
            'medical' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.25 6.75V5.5A1.75 1.75 0 0 1 10 3.75h4a1.75 1.75 0 0 1 1.75 1.75v1.25"/><path d="M5.75 6.75h12.5A2.25 2.25 0 0 1 20.5 9v8.25a2.25 2.25 0 0 1-2.25 2.25H5.75a2.25 2.25 0 0 1-2.25-2.25V9a2.25 2.25 0 0 1 2.25-2.25Z"/><path d="M12 10v6M9 13h6"/></svg>',
            'visa' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3.75 14.35 6l3.25-.25.25 3.25L20.25 12l-2.4 3 .25 3.25-3.25-.25L12 20.25 9.15 18l-3.25.25.25-3.25-2.4-3 2.4-3-.25-3.25L9.15 6 12 3.75Z"/><path d="m8.75 12.25 2.05 2.05 4.45-4.6"/></svg>',
            'contract' => '<svg class="'.$class.'" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.25 3.75h7.25L18.75 8v10.25A2 2 0 0 1 16.75 20.25h-9.5a2 2 0 0 1-2-2V5.75a2 2 0 0 1 2-2Z"/><path d="M14.5 3.75V8h4.25M8.25 11h7.5M8.25 14h7.5M8.25 17h4.5"/></svg>',
        ];

        return $icons[$name] ?? $icons['update'];
    };

    $badgeClass = function (?string $status) {
        $status = (string) $status;

        if ($status === 'paid') {
            return 'sf-timeline-badge sf-timeline-badge--success';
        }

        if (in_array($status, ['bank_rejected', 'cancelled', 'rejected'], true)) {
            return 'sf-timeline-badge sf-timeline-badge--danger';
        }

        if (in_array($status, ['sent_to_bank', 'approved', 'travel', 'rotation'], true)) {
            return 'sf-timeline-badge sf-timeline-badge--info';
        }

        return 'sf-timeline-badge';
    };
@endphp

@section('content')

<style id="sf-timeline-pagination-arrow-fix">
    .sf-timeline-pagination {
        margin-top: 24px !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
    }

    .sf-timeline-pagination nav {
        width: 100% !important;
    }

    .sf-timeline-pagination svg {
        width: 18px !important;
        height: 18px !important;
        max-width: 18px !important;
        max-height: 18px !important;
        display: inline-block !important;
    }

    .sf-timeline-pagination a,
    .sf-timeline-pagination span {
        font-size: 13px !important;
        line-height: 1.2 !important;
    }

    .sf-timeline-pagination [rel="prev"] svg,
    .sf-timeline-pagination [rel="next"] svg {
        width: 18px !important;
        height: 18px !important;
    }

    .sf-timeline-pagination .hidden {
        display: none !important;
    }
</style>

    <style>
        .sf-timeline-hero {
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

        .sf-timeline-hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            gap: 22px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .sf-timeline-kicker {
            color: #2459d3;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .sf-timeline-title {
            margin: 0;
            color: #0f172a;
            font-size: 38px;
            line-height: 1.05;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .sf-timeline-subtitle {
            margin-top: 12px;
            color: #64748b;
            font-size: 15px;
            line-height: 1.7;
            font-weight: 650;
            max-width: 860px;
        }

        .sf-timeline-count {
            min-width: 132px;
            border-radius: 26px;
            padding: 16px;
            text-align: center;
            background: rgba(255,255,255,.86);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 16px 40px rgba(15,23,42,.06);
        }

        .sf-timeline-count strong {
            display: block;
            color: #0f172a;
            font-size: 30px;
            line-height: 1;
            font-weight: 950;
        }

        .sf-timeline-count span {
            display: block;
            margin-top: 7px;
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .sf-timeline-filter-card {
            margin-top: 22px;
            border-radius: 30px;
            padding: 20px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 18px 48px rgba(15,23,42,.055);
        }

        .sf-timeline-filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            align-items: end;
        }

        .sf-timeline-label {
            margin-bottom: 8px;
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sf-timeline-control {
            width: 100%;
            min-height: 48px;
            border-radius: 18px;
            border: 1px solid rgba(15,23,42,.10);
            background: #ffffff;
            color: #0f172a;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 750;
            outline: none;
        }

        .sf-timeline-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sf-timeline-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 18px;
            border-radius: 999px;
            border: 0;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 950;
            white-space: nowrap;
        }

        .sf-timeline-btn-primary {
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb, #4f8cff);
            box-shadow: 0 12px 28px rgba(37,99,235,.20);
        }

        .sf-timeline-btn-soft {
            color: #2459d3;
            background: #eff6ff;
            border: 1px solid rgba(36,89,211,.16);
        }

        .sf-timeline-list {
            position: relative;
            display: grid;
            gap: 16px;
            margin-top: 24px;
        }

        .sf-timeline-event {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 16px;
            border-radius: 30px;
            padding: 18px;
            background:
                radial-gradient(circle at top right, rgba(36,89,211,.08), transparent 34%),
                rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 18px 48px rgba(15,23,42,.06);
        }

        .sf-timeline-event::before {
            content: "";
            position: absolute;
            left: 43px;
            top: 78px;
            bottom: -20px;
            width: 2px;
            background: linear-gradient(180deg, rgba(36,89,211,.22), transparent);
        }

        .sf-timeline-icon-box {
            position: relative;
            z-index: 2;
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 20px;
            background: #eff6ff;
            border: 1px solid rgba(36,89,211,.14);
            flex-shrink: 0;
        }

        .sf-timeline-svg {
            width: 27px;
            height: 27px;
            display: block;
            stroke: #2459d3;
            stroke-width: 1.9;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .sf-timeline-event-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sf-timeline-event-title {
            color: #0f172a;
            font-size: 20px;
            line-height: 1.2;
            font-weight: 950;
            letter-spacing: -.035em;
        }

        .sf-timeline-description {
            margin-top: 9px;
            color: #64748b;
            font-size: 14px;
            line-height: 1.65;
            font-weight: 700;
        }

        .sf-timeline-date {
            margin-top: 13px;
            color: #94a3b8;
            font-size: 12px;
            line-height: 1.3;
            font-weight: 850;
        }

        .sf-timeline-badge {
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

        .sf-timeline-badge--success {
            background: #ecfdf5;
            color: #047857;
            border-color: rgba(16,185,129,.20);
        }

        .sf-timeline-badge--danger {
            background: #fef2f2;
            color: #b91c1c;
            border-color: rgba(239,68,68,.18);
        }

        .sf-timeline-badge--info {
            background: #eff6ff;
            color: #2459d3;
            border-color: rgba(36,89,211,.16);
        }

        .sf-timeline-empty {
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

        .sf-timeline-empty strong {
            display: block;
            color: #0f172a;
            font-size: 24px;
            font-weight: 950;
            margin-bottom: 8px;
        }

        .sf-timeline-pagination {
            margin-top: 22px;
        }

        .dark .sf-timeline-hero,
        .dark .sf-timeline-filter-card,
        .dark .sf-timeline-event,
        .dark .sf-timeline-count,
        .dark .sf-timeline-empty {
            background: rgba(15,23,42,.86);
            border-color: rgba(255,255,255,.10);
        }

        .dark .sf-timeline-title,
        .dark .sf-timeline-count strong,
        .dark .sf-timeline-event-title,
        .dark .sf-timeline-empty strong {
            color: #ffffff;
        }

        .dark .sf-timeline-subtitle,
        .dark .sf-timeline-count span,
        .dark .sf-timeline-description,
        .dark .sf-timeline-empty {
            color: rgba(226,232,240,.76);
        }

        .dark .sf-timeline-control {
            background: rgba(15,23,42,.92);
            border-color: rgba(255,255,255,.12);
            color: #ffffff;
        }

        @media (max-width: 980px) {
            .sf-timeline-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .sf-timeline-filter-grid {
                grid-template-columns: 1fr;
            }

            .sf-timeline-title {
                font-size: 32px;
            }

            .sf-timeline-event {
                grid-template-columns: 1fr;
            }

            .sf-timeline-event::before {
                display: none;
            }
        }
    </style>

    <section class="sf-timeline-hero">
        <div class="sf-timeline-hero-inner">
            <div>
                <div class="sf-timeline-kicker">Employee Timeline</div>
                <h1 class="sf-timeline-title">Updates & Activity</h1>
                <div class="sf-timeline-subtitle">
                    Follow your latest employee-visible updates across salary slips, files, rotations, travel, medical,
                    visa, and contract activity.
                </div>
            </div>

            <div class="sf-timeline-count">
                <strong>{{ number_format((int) $totalEvents) }}</strong>
                <span>Updates</span>
            </div>
        </div>
    </section>

    <section class="sf-timeline-filter-card">
        <form method="GET" action="{{ route('portal.timeline.index') }}">
            <div class="sf-timeline-filter-grid">
                <div>
                    <div class="sf-timeline-label">Type</div>
                    <select name="status" class="sf-timeline-control">
                        <option value="">All Types</option>
                        @foreach($statusOptions as $value)
                            <option value="{{ $value }}" @selected($statusFilter === $value)>
                                {{ ucfirst(str_replace('_', ' ', $value)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="sf-timeline-label">Month</div>
                    <select name="month" class="sf-timeline-control">
                        <option value="">All Months</option>
                        @foreach($monthOptions as $value => $label)
                            <option value="{{ $value }}" @selected($monthFilter === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="sf-timeline-label">Year</div>
                    <select name="year" class="sf-timeline-control">
                        <option value="">All Years</option>
                        @foreach($yearOptions as $value)
                            <option value="{{ $value }}" @selected($yearFilter === $value)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sf-timeline-actions">
                    <button type="submit" class="sf-timeline-btn sf-timeline-btn-primary">Apply Filters</button>
                    <a href="{{ route('portal.timeline.index') }}" class="sf-timeline-btn sf-timeline-btn-soft">Reset</a>
                </div>
            </div>
        </form>
    </section>

    @if($events->count())
        <section class="sf-timeline-list">
            @foreach($events as $item)
                @php
                    $title = $item->title ?? 'Portal update';
                    $description = $item->description ?? null;
                    $status = $item->badge_status ?? null;
                    $date = $item->event_date ? $item->event_date->format('Y-m-d H:i') : '-';
                    $iconKey = $updateIcon($status, $title . ' ' . $description);
                @endphp

                <article class="sf-timeline-event">
                    <div class="sf-timeline-icon-box">
                        {!! $renderSvgIcon($iconKey) !!}
                    </div>

                    <div>
                        <div class="sf-timeline-event-head">
                            <div class="sf-timeline-event-title">{{ $title }}</div>

                            @if($status)
                                <span class="{{ $badgeClass($status) }}">
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            @endif
                        </div>

                        @if($description)
                            <div class="sf-timeline-description">{{ $description }}</div>
                        @endif

                        <div class="sf-timeline-date">{{ $date }}</div>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="sf-timeline-pagination">
            {{ $events->links() }}
        </div>
    @else
        <section class="sf-timeline-empty">
            <strong>No updates found</strong>
            No timeline items match the selected filters.
        </section>
    @endif


<style id="sf-timeline-icon-hard-limit">
    .sf-timeline-icon-box {
        width: 54px !important;
        height: 54px !important;
        min-width: 54px !important;
        max-width: 54px !important;
        overflow: hidden !important;
    }

    .sf-timeline-icon-box svg,
    .sf-timeline-svg {
        width: 27px !important;
        height: 27px !important;
        min-width: 27px !important;
        max-width: 27px !important;
        display: block !important;
    }

    .sf-timeline-event {
        overflow: hidden !important;
    }
</style>

@endsection
