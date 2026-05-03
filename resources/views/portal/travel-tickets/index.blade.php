@extends('portal.layouts.app')

@php
    $pageTitle = 'Travel & Tickets';

    $statusColor = function (?string $status) {
        return match ($status) {
            'active', 'completed', 'ticket_booked' => 'success',
            'scheduled', 'pending_request' => 'warning',
            'request_received' => 'info',
            'cancelled' => 'danger',
            default => 'slate',
        };
    };

    $formatDate = function ($date) {
        return $date ? $date->format('Y-m-d') : '-';
    };
@endphp

@section('content')
<style>
    .sf-travel-hero {
        position: relative;
        overflow: hidden;
        border-radius: 34px;
        padding: 28px;
        background:
            radial-gradient(circle at 92% 4%, rgba(76,167,168,.16), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f8fbff 54%, #eefafb 100%);
        border: 1px solid rgba(215,226,229,.95);
        box-shadow: 0 18px 46px rgba(15,23,42,.07);
    }

    .sf-travel-kicker {
        color: #2459d3;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .16em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .sf-travel-title {
        color: #0f172a;
        font-size: 38px;
        line-height: 1.05;
        font-weight: 950;
        letter-spacing: -.05em;
    }

    .sf-travel-subtitle {
        margin-top: 12px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
        font-weight: 750;
        max-width: 920px;
    }

    .sf-travel-count {
        position: absolute;
        right: 28px;
        top: 28px;
        min-width: 94px;
        min-height: 82px;
        border-radius: 28px;
        background: rgba(255,255,255,.82);
        border: 1px solid rgba(15,23,42,.08);
        display: grid;
        place-items: center;
        text-align: center;
        box-shadow: 0 14px 34px rgba(15,23,42,.07);
    }

    .sf-travel-count strong {
        display: block;
        color: #0f172a;
        font-size: 28px;
        line-height: 1;
        font-weight: 950;
    }

    .sf-travel-count span {
        display: block;
        margin-top: 7px;
        color: #64748b;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .sf-travel-grid {
        margin-top: 22px;
        display: grid;
        gap: 16px;
    }

    .sf-travel-card {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 36%),
            rgba(255,255,255,.94);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 16px 38px rgba(15,23,42,.055);
        padding: 20px;
    }

    .sf-travel-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .sf-travel-card-title {
        color: #0f172a;
        font-size: 22px;
        line-height: 1.15;
        font-weight: 950;
        letter-spacing: -.035em;
    }

    .sf-travel-card-meta {
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
        font-weight: 750;
    }

    .sf-travel-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sf-travel-badge {
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        border-radius: 999px;
        padding: 0 11px;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .08em;
        text-transform: uppercase;
        border: 1px solid rgba(15,23,42,.08);
        background: #f8fafc;
        color: #334155;
    }

    .sf-travel-badge--success { background:#ecfdf5; color:#047857; border-color:rgba(16,185,129,.20); }
    .sf-travel-badge--warning { background:#fff7ed; color:#c2410c; border-color:rgba(251,146,60,.25); }
    .sf-travel-badge--info { background:#eff6ff; color:#1d4ed8; border-color:rgba(59,130,246,.20); }
    .sf-travel-badge--danger { background:#fef2f2; color:#b91c1c; border-color:rgba(239,68,68,.20); }
    .sf-travel-badge--slate { background:#f8fafc; color:#334155; border-color:rgba(15,23,42,.08); }

    .sf-travel-details {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 14px;
    }

    .sf-travel-detail {
        border-radius: 18px;
        border: 1px solid rgba(15,23,42,.07);
        background: rgba(248,250,252,.82);
        padding: 13px;
    }

    .sf-travel-detail-label {
        color: #64748b;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .11em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .sf-travel-detail-value {
        color: #0f172a;
        font-size: 13px;
        line-height: 1.35;
        font-weight: 900;
    }

    .sf-travel-files {
        margin-top: 16px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .sf-travel-file {
        border-radius: 22px;
        padding: 15px;
        border: 1px solid rgba(15,23,42,.08);
        background: rgba(255,255,255,.86);
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
    }

    .sf-travel-file-title {
        color: #0f172a;
        font-size: 14px;
        font-weight: 950;
        line-height: 1.3;
    }

    .sf-travel-file-sub {
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
        font-weight: 750;
    }

    .sf-travel-file-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sf-travel-btn {
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0 13px;
        font-size: 12px;
        font-weight: 950;
        text-decoration: none !important;
    }

    .sf-travel-btn--open {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid rgba(59,130,246,.20);
    }

    .sf-travel-btn--download {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid rgba(16,185,129,.20);
    }

    .sf-travel-empty-file {
        color: #94a3b8;
        font-size: 12px;
        font-weight: 800;
    }

    @media (max-width: 980px) {
        .sf-travel-details,
        .sf-travel-files {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sf-travel-count {
            position: static;
            margin-top: 18px;
            width: 120px;
        }
    }

    @media (max-width: 680px) {
        .sf-travel-details,
        .sf-travel-files {
            grid-template-columns: 1fr;
        }

        .sf-travel-title {
            font-size: 30px;
        }
    }
</style>

<section class="sf-travel-hero">
    <div class="sf-travel-kicker">Travel & Tickets</div>
    <div class="sf-travel-title">Travel Requests, Tickets & Rotation Travel</div>
    <div class="sf-travel-subtitle">
        This page keeps your travel request files and monthly/rotation ticket files in one dedicated place, without mixing them with general employee documents.
    </div>

    <div class="sf-travel-count">
        <div>
            <strong>{{ $travelTickets->count() }}</strong>
            <span>Rotations</span>
        </div>
    </div>
</section>

@if($travelTickets->count())
    <section class="sf-travel-grid">
        @foreach($travelTickets as $item)
            @php
                $rotationColor = $statusColor($item['status']);
                $travelColor = $statusColor($item['travel_status']);
            @endphp

            <article class="sf-travel-card">
                <div class="sf-travel-card-head">
                    <div>
                        <div class="sf-travel-card-title">{{ $item['label'] }}</div>
                        <div class="sf-travel-card-meta">
                            {{ $item['rotation_pattern'] ?: 'Rotation pattern not set' }}
                        </div>
                    </div>

                    <div class="sf-travel-badges">
                        <span class="sf-travel-badge sf-travel-badge--{{ $rotationColor }}">
                            {{ $item['status'] ? ucfirst(str_replace('_', ' ', $item['status'])) : 'Rotation' }}
                        </span>

                        <span class="sf-travel-badge sf-travel-badge--{{ $travelColor }}">
                            {{ $item['travel_status'] ? ucfirst(str_replace('_', ' ', $item['travel_status'])) : 'Travel' }}
                        </span>
                    </div>
                </div>

                <div class="sf-travel-details">
                    <div class="sf-travel-detail">
                        <div class="sf-travel-detail-label">From</div>
                        <div class="sf-travel-detail-value">{{ $formatDate($item['from_date']) }}</div>
                    </div>

                    <div class="sf-travel-detail">
                        <div class="sf-travel-detail-label">To</div>
                        <div class="sf-travel-detail-value">{{ $formatDate($item['to_date']) }}</div>
                    </div>

                    <div class="sf-travel-detail">
                        <div class="sf-travel-detail-label">Mobilization</div>
                        <div class="sf-travel-detail-value">{{ $formatDate($item['mobilization_date']) }}</div>
                    </div>

                    <div class="sf-travel-detail">
                        <div class="sf-travel-detail-label">Demobilization</div>
                        <div class="sf-travel-detail-value">{{ $formatDate($item['demobilization_date']) }}</div>
                    </div>
                </div>

                <div class="sf-travel-files">
                    <div class="sf-travel-file">
                        <div>
                            <div class="sf-travel-file-title">Travel Request</div>
                            <div class="sf-travel-file-sub">
                                {{ $item['travel_request_file_path'] ? 'File available' : 'No travel request file yet' }}
                            </div>
                        </div>

                        @if($item['travel_request_file_path'])
                            <div class="sf-travel-file-actions">
                                <a class="sf-travel-btn sf-travel-btn--open" href="{{ $item['travel_request_url'] }}" target="_blank">Open</a>
                                <a class="sf-travel-btn sf-travel-btn--download" href="{{ route('portal.travel-tickets.download', ['rotation' => $item['id'], 'type' => 'travel-request']) }}">Download</a>
                            </div>
                        @else
                            <div class="sf-travel-empty-file">Pending</div>
                        @endif
                    </div>

                    <div class="sf-travel-file">
                        <div>
                            <div class="sf-travel-file-title">Ticket</div>
                            <div class="sf-travel-file-sub">
                                {{ $item['ticket_file_path'] ? 'File available' : 'No ticket file yet' }}
                            </div>
                        </div>

                        @if($item['ticket_file_path'])
                            <div class="sf-travel-file-actions">
                                <a class="sf-travel-btn sf-travel-btn--open" href="{{ $item['ticket_url'] }}" target="_blank">Open</a>
                                <a class="sf-travel-btn sf-travel-btn--download" href="{{ route('portal.travel-tickets.download', ['rotation' => $item['id'], 'type' => 'ticket']) }}">Download</a>
                            </div>
                        @else
                            <div class="sf-travel-empty-file">Pending</div>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </section>
@else
    <section class="portal-card portal-card-soft" style="margin-top:22px;">
        <div class="portal-empty">
            No travel or ticket records are available yet.
        </div>
    </section>
@endif
@endsection
