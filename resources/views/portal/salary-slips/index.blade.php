@include('portal.partials.salary-slip-status-theme')
@extends('portal.layouts.app')

@php
    $pageTitle = 'Portal Salary Slips';

    $statusBadge = function (?string $status): array {
        return match ((string) $status) {
            'draft' => ['Draft', 'portal-badge portal-badge--slate'],
            'approved' => ['Approved', 'portal-badge portal-badge--info'],
            'sent_to_bank' => ['Sent to Bank', 'portal-badge portal-badge--warning'],
            'paid' => ['Paid', 'portal-badge portal-badge--success'],
            'bank_rejected' => ['Bank Rejected', 'portal-badge portal-badge--danger'],
            default => [ucfirst((string) $status), 'portal-badge portal-badge--slate'],
        };
    };
@endphp

@section('content')

<style>
    .sf-dashboard-receipt-inline {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .04em;
        text-transform: uppercase;
        background: #fffbeb;
        border: 1px solid #facc15;
        color: #b45309;
        margin-inline-start: 8px;
    }

    .sf-dashboard-receipt-inline.ok {
        background: #ecfdf5;
        border-color: #86efac;
        color: #047857;
    }

    .sf-dashboard-receipt-inline.issue {
        background: #fff1f2;
        border-color: #fda4af;
        color: #be123c;
    }
</style>

    <section class="portal-card">
        <div class="portal-section-head">
            <div>
                <div class="portal-title">Salary Slips</div>
                <div class="portal-muted" style="margin-top:12px;">
                    View your salary slips in a structured table with filtering by status, month, and year.
                </div>
            </div>
        </div>
    </section>

    <section class="portal-card">
        <form method="GET" action="{{ route('portal.salary-slips.index') }}">
            <div class="portal-grid-4">
                <div>
                    <div class="portal-kpi-label" style="margin-bottom:8px;">Status</div>
                    <select name="status" style="width:100%;min-height:46px;border-radius:14px;border:1px solid #dbe5ee;background:#fff;padding:0 14px;">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected($statusFilter === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="portal-kpi-label" style="margin-bottom:8px;">Month</div>
                    <select name="month" style="width:100%;min-height:46px;border-radius:14px;border:1px solid #dbe5ee;background:#fff;padding:0 14px;">
                        <option value="">All Months</option>
                        @foreach($monthOptions as $key => $label)
                            <option value="{{ $key }}" @selected((string) $monthFilter === (string) $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="portal-kpi-label" style="margin-bottom:8px;">Year</div>
                    <select name="year" style="width:100%;min-height:46px;border-radius:14px;border:1px solid #dbe5ee;background:#fff;padding:0 14px;">
                        <option value="">All Years</option>
                        @foreach($yearOptions as $value)
                            <option value="{{ $value }}" @selected((string) $yearFilter === (string) $value)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;align-items:end;gap:10px;">
                    <button type="submit" class="portal-btn portal-btn--light">Apply Filters</button>
                    <a href="{{ route('portal.salary-slips.index') }}" class="portal-btn" style="background:#eff6ff;color:#1d4ed8;">Reset</a>
                </div>
            </div>
        </form>
    </section>

    <section class="portal-card">
        @if($salarySlips->count())
            <div style="overflow:auto;">
                <table style="width:100%;border-collapse:separate;border-spacing:0 10px;">
                    <thead>
                        <tr>
                            <th style="text-align:left;padding:10px 12px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Period</th>
                            <th style="text-align:left;padding:10px 12px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Client</th>
                            <th style="text-align:left;padding:10px 12px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Project</th>
                            <th style="text-align:left;padding:10px 12px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Net Amount</th>
                            <th style="text-align:left;padding:10px 12px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Method</th>
                            <th style="text-align:left;padding:10px 12px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Status</th>
                            <th style="text-align:left;padding:10px 12px;color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.12em;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salarySlips as $item)
                            @php([$badgeText, $badgeClass] = $statusBadge($item->status))
                            <tr>
                                <td style="background:#f8fbff;padding:16px 12px;border-top-left-radius:16px;border-bottom-left-radius:16px;">
                                    <div style="font-weight:800;color:#0f172a;">
                                        {{ sprintf('%02d / %04d', (int) ($item->salary_month ?? 0), (int) ($item->salary_year ?? 0)) }}
                                    </div>
                                </td>
                                <td style="background:#f8fbff;padding:16px 12px;">{{ $item->client?->name ?: '-' }}</td>
                                <td style="background:#f8fbff;padding:16px 12px;">{{ $item->project?->name ?: '-' }}</td>
                                <td style="background:#f8fbff;padding:16px 12px;font-weight:900;color:#0f172a;">
                                    {{ number_format((float) ($item->net_amount ?? 0), 2) }} {{ $item->currency ?: '' }}
                                </td>
                                <td style="background:#f8fbff;padding:16px 12px;">
                                    {{ \App\Models\SalarySlip::paymentMethodLabels()[$item->payment_method] ?? '-' }}
                                </td>
                                <td style="background:#f8fbff;padding:16px 12px;">
                                    <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                                </td>
                                <td style="background:#f8fbff;padding:16px 12px;border-top-right-radius:16px;border-bottom-right-radius:16px;">
                                    <a href="{{ route('portal.salary-slips.show', $item) }}" style="font-weight:800;color:#2563eb;">Open</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:18px;">
                {{ $salarySlips->links() }}
            </div>
        @else
            <div class="portal-empty">
                No salary slips found for the current filters.
            </div>
        @endif
    </section>
@endsection
