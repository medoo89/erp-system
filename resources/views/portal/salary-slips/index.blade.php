@include('portal.partials.salary-slip-status-theme')
@extends('portal.layouts.app')

@php

    $salaryDisplayAmount = function ($slip) {
        return number_format((float) ($slip->payment_total_amount ?? $slip->net_amount ?? 0), 2);
    };

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

<style id="sf-candidate-request-decision-colors">
    /*
     * Colored decision buttons — visual only.
     */

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]) {
        overflow: hidden !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
        border-color: rgba(34,197,94,.42) !important;
        color: #047857 !important;
        box-shadow: 0 12px 28px rgba(34,197,94,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: linear-gradient(135deg, #fef2f2, #fee2e2) !important;
        border-color: rgba(239,68,68,.38) !important;
        color: #b91c1c !important;
        box-shadow: 0 12px 28px rgba(239,68,68,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
        border-color: rgba(249,115,22,.38) !important;
        color: #c2410c !important;
        box-shadow: 0 12px 28px rgba(249,115,22,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]:checked) {
        transform: translateY(-1px) !important;
        filter: saturate(1.12) !important;
        box-shadow: 0 0 0 5px rgba(37,99,235,.10), 0 18px 38px rgba(15,23,42,.12) !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: rgba(6,78,59,.55) !important;
        border-color: rgba(52,211,153,.34) !important;
        color: #a7f3d0 !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: rgba(127,29,29,.48) !important;
        border-color: rgba(248,113,113,.34) !important;
        color: #fecaca !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: rgba(124,45,18,.48) !important;
        border-color: rgba(251,146,60,.34) !important;
        color: #fed7aa !important;
    }
</style>


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
                            
                        @php
                            $badge = match ((string) ($item->status ?? 'draft')) {
                                'approved' => ['Approved', 'portal-badge portal-badge--info'],
                                'sent_to_bank' => ['Sent to Bank', 'portal-badge portal-badge--warning'],
                                'paid' => ['Paid', 'portal-badge portal-badge--success'],
                                'bank_rejected' => ['Bank Rejected', 'portal-badge portal-badge--danger'],
                                'cancelled' => ['Cancelled', 'portal-badge portal-badge--danger'],
                                default => ['Draft', 'portal-badge portal-badge--slate'],
                            };

                            $badgeText = $badge[0];
                            $badgeClass = $badge[1];
                        
                            $badgeText = $badge[0];
                            $badgeClass = $badge[1];
                        
                            $badgeText = $badge[0];
                            $badgeClass = $badge[1];
                        @endphp
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
                                    <span class="{{ $badgeClass ?? 'portal-badge portal-badge--slate' }}">{{ $badgeText ?? ucfirst(str_replace('_', ' ', (string) ($item->status ?? 'draft'))) }}</span>
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

<style id="sf-portal-salary-scroll-final">
    .sf-attendance-table,
    .sf-attendance-days,
    .salary-attendance-table,
    .portal-attendance-table,
    [data-attendance-table],
    [data-salary-attendance],
    .sf-salary-attendance,
    .sf-slip-attendance {
        max-height: 430px !important;
        overflow-y: auto !important;
        display: block !important;
        scrollbar-width: thin;
    }

    .sf-attendance-table::-webkit-scrollbar,
    .sf-attendance-days::-webkit-scrollbar,
    .salary-attendance-table::-webkit-scrollbar,
    .portal-attendance-table::-webkit-scrollbar,
    [data-attendance-table]::-webkit-scrollbar,
    [data-salary-attendance]::-webkit-scrollbar,
    .sf-salary-attendance::-webkit-scrollbar,
    .sf-slip-attendance::-webkit-scrollbar {
        width: 8px;
    }

    .sf-attendance-table::-webkit-scrollbar-thumb,
    .sf-attendance-days::-webkit-scrollbar-thumb,
    .salary-attendance-table::-webkit-scrollbar-thumb,
    .portal-attendance-table::-webkit-scrollbar-thumb,
    [data-attendance-table]::-webkit-scrollbar-thumb,
    [data-salary-attendance]::-webkit-scrollbar-thumb,
    .sf-salary-attendance::-webkit-scrollbar-thumb,
    .sf-slip-attendance::-webkit-scrollbar-thumb {
        background: rgba(37,99,235,.24);
        border-radius: 999px;
    }

    table.sf-attendance-table,
    table.salary-attendance-table,
    table.portal-attendance-table {
        display: table !important;
        width: 100% !important;
    }

    .sf-portal-attendance-scroll {
        max-height: 430px !important;
        overflow-y: auto !important;
    }
</style>
