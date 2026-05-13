@extends('portal.layouts.app')

@php
    $pageTitle = 'Reimbursement Claims';

    $claimStatusLabel = function (?string $status) {
        return match ((string) $status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
            'not_applicable' => 'Not Applicable',
            default => $status ? ucfirst(str_replace('_', ' ', $status)) : 'Pending Review',
        };
    };

    $claimStatusClass = function (?string $status) {
        return match ((string) $status) {
            'approved' => 'sf-rmb-badge sf-rmb-badge--info',
            'paid' => 'sf-rmb-badge sf-rmb-badge--success',
            'rejected' => 'sf-rmb-badge sf-rmb-badge--danger',
            default => 'sf-rmb-badge sf-rmb-badge--warning',
        };
    };

    $categoryOptions = [
        'ticket' => 'Ticket',
        'visa' => 'Visa',
        'hotel' => 'Hotel',
        'medical' => 'Medical',
        'health_certificate' => 'Health Certificate',
        'training' => 'Training Certificate',
        'transport' => 'Transport',
        'food' => 'Food',
        'accommodation' => 'Accommodation',
        'other' => 'Other',
    ];
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
    .sf-rmb-hero {
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

    .sf-rmb-hero-inner {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        gap: 22px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .sf-rmb-kicker {
        color: #2459d3;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .sf-rmb-title {
        margin: 0;
        color: #0f172a;
        font-size: 38px;
        line-height: 1.05;
        font-weight: 950;
        letter-spacing: -.05em;
    }

    .sf-rmb-subtitle {
        margin-top: 12px;
        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
        font-weight: 700;
        max-width: 880px;
    }

    .sf-rmb-count {
        min-width: 132px;
        border-radius: 26px;
        padding: 16px;
        text-align: center;
        background: rgba(255,255,255,.86);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 16px 40px rgba(15,23,42,.06);
    }

    .sf-rmb-count strong {
        display: block;
        color: #0f172a;
        font-size: 30px;
        line-height: 1;
        font-weight: 950;
    }

    .sf-rmb-count span {
        display: block;
        margin-top: 7px;
        color: #64748b;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .sf-rmb-card {
        margin-top: 22px;
        border-radius: 34px;
        background: rgba(255,255,255,.96);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 18px 48px rgba(15,23,42,.06);
        overflow: hidden;
    }

    .sf-rmb-card-head {
        padding: 26px 28px;
        border-bottom: 1px solid rgba(15,23,42,.08);
    }

    .sf-rmb-card-title {
        color: #0f172a;
        font-size: 28px;
        line-height: 1.05;
        font-weight: 950;
        letter-spacing: -.045em;
    }

    .sf-rmb-card-subtitle {
        margin-top: 8px;
        color: #64748b;
        font-size: 15px;
        line-height: 1.65;
        font-weight: 750;
    }

    .sf-rmb-body {
        padding: 28px;
    }

    .sf-rmb-list {
        display: grid;
        gap: 14px;
    }

    .sf-rmb-claim {
        border-radius: 26px;
        padding: 18px;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 34%),
            rgba(248,250,252,.96);
        border: 1px solid rgba(15,23,42,.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .sf-rmb-claim-title {
        color: #0f172a;
        font-size: 18px;
        line-height: 1.25;
        font-weight: 950;
        letter-spacing: -.03em;
    }

    .sf-rmb-claim-meta {
        margin-top: 6px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
        font-weight: 850;
    }

    .sf-rmb-claim-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .sf-rmb-amount {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 999px;
        background: #ffffff;
        color: #0f172a;
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 10px 22px rgba(15,23,42,.04);
        font-size: 15px;
        font-weight: 950;
        white-space: nowrap;
    }

    .sf-rmb-badge {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 13px;
        font-weight: 950;
        white-space: nowrap;
    }

    .sf-rmb-badge--warning {
        background: #fff7ed;
        color: #9a3412;
        border-color: #fed7aa;
    }

    .sf-rmb-badge--info {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .sf-rmb-badge--success {
        background: #ecfdf5;
        color: #047857;
        border-color: #bbf7d0;
    }

    .sf-rmb-badge--danger {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }

    .sf-rmb-receipt {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 999px;
        background: #fbbf24;
        color: #111827;
        border: 0;
        font-size: 13px;
        font-weight: 950;
        text-decoration: none;
        white-space: nowrap;
    }

    .sf-rmb-form-shell {
        border-radius: 30px;
        padding: 26px;
        background:
            radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 34%),
            rgba(255,255,255,.94);
        border: 1px solid rgba(15,23,42,.08);
    }

    .sf-rmb-form-top {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .sf-rmb-form-title {
        color: #0f172a;
        font-size: 22px;
        font-weight: 950;
        letter-spacing: -.04em;
    }

    .sf-rmb-form-note {
        margin-top: 8px;
        color: #475569;
        font-size: 14px;
        line-height: 1.6;
        font-weight: 800;
    }

    .sf-rmb-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .sf-rmb-field-full {
        grid-column: 1 / -1;
    }

    .sf-rmb-label {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
    }

    .sf-rmb-label b {
        color: #dc2626;
    }

    .sf-rmb-control,
    .sf-rmb-select,
    .sf-rmb-textarea {
        width: 100%;
        border-radius: 22px;
        border: 1px solid rgba(15,23,42,.12);
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 12px 28px rgba(15,23,42,.035);
        outline: none;
        font-size: 15px;
        font-weight: 850;
    }

    .sf-rmb-control,
    .sf-rmb-select {
        min-height: 58px;
        padding: 0 18px;
    }

    .sf-rmb-textarea {
        min-height: 132px;
        padding: 18px;
        resize: vertical;
        line-height: 1.55;
    }

    .sf-rmb-file {
        position: relative;
        min-height: 58px;
        border-radius: 22px;
        border: 1px solid rgba(15,23,42,.12);
        background: #f8fafc;
        display: flex;
        align-items: center;
        padding: 9px 14px;
        box-shadow: 0 12px 28px rgba(15,23,42,.035);
    }

    .sf-rmb-file input[type="file"] {
        width: 100%;
        font-size: 14px;
        font-weight: 850;
        color: #0f172a;
    }

    .sf-rmb-file input[type="file"]::file-selector-button {
        border: 0;
        border-radius: 999px;
        min-height: 40px;
        padding: 0 17px;
        margin-right: 12px;
        background: #0f172a;
        color: #ffffff;
        cursor: pointer;
        font-weight: 950;
    }

    .sf-rmb-submit-row {
        margin-top: 24px;
        display: flex;
        justify-content: flex-end;
    }

    .sf-rmb-submit {
        border: 0;
        cursor: pointer;
        min-height: 56px;
        padding: 0 28px;
        border-radius: 999px;
        background: #0f172a;
        color: #ffffff;
        font-size: 15px;
        font-weight: 950;
        box-shadow: 0 18px 42px rgba(15,23,42,.16);
    }

    .sf-rmb-alert {
        margin-bottom: 18px;
        border-radius: 22px;
        padding: 16px 18px;
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid rgba(16,185,129,.22);
        font-weight: 900;
    }

    .sf-rmb-empty {
        border-radius: 24px;
        padding: 22px;
        color: #64748b;
        border: 1px dashed rgba(15,23,42,.16);
        background: #ffffff;
        font-weight: 800;
        line-height: 1.65;
    }

    @media (max-width: 860px) {
        .sf-rmb-form-grid {
            grid-template-columns: 1fr;
        }

        .sf-rmb-claim {
            align-items: flex-start;
        }

        .sf-rmb-claim-actions,
        .sf-rmb-submit-row {
            justify-content: flex-start;
        }

        .sf-rmb-amount,
        .sf-rmb-badge,
        .sf-rmb-receipt,
        .sf-rmb-submit {
            width: 100%;
        }
    }
</style>

<section class="sf-rmb-hero">
    <div class="sf-rmb-hero-inner">
        <div>
            <div class="sf-rmb-kicker">Employee Portal</div>
            <h1 class="sf-rmb-title">Reimbursement Claims</h1>
            <div class="sf-rmb-subtitle">
                Submit only expenses you paid from your own pocket. These claims stay pending until Sada Fezzan reviews, approves, and pays them.
            </div>
        </div>

        <div class="sf-rmb-count">
            <strong>{{ $claims->count() }}</strong>
            <span>Claims</span>
        </div>
    </div>
</section>

<section class="sf-rmb-card">
    <div class="sf-rmb-card-head">
        <div class="sf-rmb-card-title">Submitted Claims</div>
        <div class="sf-rmb-card-subtitle">Current reimbursement claims linked to your employment profile.</div>
    </div>

    <div class="sf-rmb-body">
        @if(session('success'))
            <div class="sf-rmb-alert">{{ session('success') }}</div>
        @endif

        @if($claims->isNotEmpty())
            <div class="sf-rmb-list">
                @foreach($claims as $claim)
                    <div class="sf-rmb-claim">
                        <div>
                            <div class="sf-rmb-claim-title">{{ $claim->title ?: 'Reimbursement Claim' }}</div>
                            <div class="sf-rmb-claim-meta">
                                {{ ucfirst(str_replace('_', ' ', $claim->category ?: 'Other')) }}
                                · {{ $claim->expense_date?->format('M d, Y') ?: 'No date' }}
                                · {{ $claim->has_attachment ? 'Receipt uploaded' : 'No receipt' }}
                            </div>
                        </div>

                        <div class="sf-rmb-claim-actions">
                            <span class="sf-rmb-amount">
                                {{ number_format((float) ($claim->reimbursement_amount ?? $claim->amount), 2) }}
                                {{ $claim->reimbursement_currency ?: $claim->currency ?: 'EUR' }}
                            </span>

                            <span class="{{ $claimStatusClass($claim->reimbursement_status) }}">
                                {{ $claimStatusLabel($claim->reimbursement_status) }}
                            </span>

                            @if($claim->receipt_file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($claim->receipt_file_path))
                                <a class="sf-rmb-receipt" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($claim->receipt_file_path) }}" target="_blank" rel="noopener">
                                    Receipt
                                </a>
                            @elseif($claim->attachment_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($claim->attachment_path))
                                <a class="sf-rmb-receipt" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($claim->attachment_path) }}" target="_blank" rel="noopener">
                                    Receipt
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sf-rmb-empty">
                No reimbursement claims submitted yet. Use the form below only when you paid an employment-related expense from your own pocket.
            </div>
        @endif
    </div>
</section>

<section class="sf-rmb-card">
    <div class="sf-rmb-card-head">
        <div class="sf-rmb-card-title">Submit Reimbursement Claim</div>
        <div class="sf-rmb-card-subtitle">Use this for employment-stage expenses you paid personally.</div>
    </div>

    <div class="sf-rmb-body">
        <form method="POST" action="{{ route('portal.reimbursements.store') }}" enctype="multipart/form-data" class="sf-rmb-form-shell">
            @csrf

            <div class="sf-rmb-form-top">
                <div>
                    <div class="sf-rmb-form-title">Expense Details</div>
                    <div class="sf-rmb-form-note">Upload a receipt or proof when available. Finance will review the claim before approval or payment.</div>
                </div>
                <span class="sf-rmb-badge sf-rmb-badge--warning">Pending Review</span>
            </div>

            <div class="sf-rmb-form-grid">
                <div>
                    <div class="sf-rmb-label">Expense Title <b>*</b></div>
                    <input class="sf-rmb-control" type="text" name="expense_title" value="{{ old('expense_title') }}" placeholder="Ticket, visa, medical, hotel..." required>
                    @error('expense_title')<div style="margin-top:8px;color:#dc2626;font-weight:800;">{{ $message }}</div>@enderror
                </div>

                <div>
                    <div class="sf-rmb-label">Category <b>*</b></div>
                    <select class="sf-rmb-select" name="expense_category" required>
                        @foreach($categoryOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('expense_category', 'other') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('expense_category')<div style="margin-top:8px;color:#dc2626;font-weight:800;">{{ $message }}</div>@enderror
                </div>

                <div>
                    <div class="sf-rmb-label">Amount <b>*</b></div>
                    <input class="sf-rmb-control" type="number" step="0.01" min="0.01" name="expense_amount" value="{{ old('expense_amount') }}" required>
                    @error('expense_amount')<div style="margin-top:8px;color:#dc2626;font-weight:800;">{{ $message }}</div>@enderror
                </div>

                <div>
                    <div class="sf-rmb-label">Currency <b>*</b></div>
                    <select class="sf-rmb-select" name="expense_currency" required>
                        @foreach(['EUR', 'USD', 'LYD', 'GBP'] as $currency)
                            <option value="{{ $currency }}" @selected(old('expense_currency', $employment->salary_currency ?: 'EUR') === $currency)>{{ $currency }}</option>
                        @endforeach
                    </select>
                    @error('expense_currency')<div style="margin-top:8px;color:#dc2626;font-weight:800;">{{ $message }}</div>@enderror
                </div>

                <div>
                    <div class="sf-rmb-label">Expense Date <b>*</b></div>
                    <input class="sf-rmb-control" type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required>
                    @error('expense_date')<div style="margin-top:8px;color:#dc2626;font-weight:800;">{{ $message }}</div>@enderror
                </div>

                <div>
                    <div class="sf-rmb-label">Receipt / Proof</div>
                    <div class="sf-rmb-file">
                        <input type="file" name="receipt_file">
                    </div>
                    @error('receipt_file')<div style="margin-top:8px;color:#dc2626;font-weight:800;">{{ $message }}</div>@enderror
                </div>

                <div class="sf-rmb-field-full">
                    {{-- SADA_FINAL_OPERATIONAL_DATES_BLOCK --}}
                    <div class="sf-operational-dates-shell" data-operational-dates-shell style="display:none;">
                        <div class="sf-rmb-form-title" style="margin-bottom:8px;">Operational Dates for Calendar</div>
                        <div class="sf-rmb-form-note" style="margin-bottom:14px;">
                            These dates appear in ERP Calendar / Portal Calendar. Expense Date is only the claim/receipt date and will not be used in the calendar.
                        </div>

                        <div class="sf-rmb-form-grid">
                            <div class="sf-op-date-field" data-op-date-field="ticket">
                                <div class="sf-rmb-label">Ticket Departure Date <b>*</b></div>
                                <input class="sf-rmb-control" type="date" name="operational_departure_date" value="{{ old('operational_departure_date') }}">
                            </div>

                            <div class="sf-op-date-field" data-op-date-field="ticket">
                                <div class="sf-rmb-label">Ticket Return Date</div>
                                <input class="sf-rmb-control" type="date" name="operational_return_date" value="{{ old('operational_return_date') }}">
                                <div class="sf-rmb-help">Leave empty only for one-way ticket.</div>
                            </div>

                            <div class="sf-op-date-field" data-op-date-field="hotel">
                                <div class="sf-rmb-label">Hotel Check-in Date <b>*</b></div>
                                <input class="sf-rmb-control" type="date" name="operational_check_in_date" value="{{ old('operational_check_in_date') }}">
                            </div>

                            <div class="sf-op-date-field" data-op-date-field="hotel">
                                <div class="sf-rmb-label">Hotel Check-out Date <b>*</b></div>
                                <input class="sf-rmb-control" type="date" name="operational_check_out_date" value="{{ old('operational_check_out_date') }}">
                            </div>

                            <div class="sf-op-date-field" data-op-date-field="visa">
                                <div class="sf-rmb-label">Visa Issue / Submission Date <b>*</b></div>
                                <input class="sf-rmb-control" type="date" name="operational_visa_issue_date" value="{{ old('operational_visa_issue_date') }}">
                            </div>

                            <div class="sf-op-date-field" data-op-date-field="visa">
                                <div class="sf-rmb-label">Visa Expiry Date <b>*</b></div>
                                <input class="sf-rmb-control" type="date" name="operational_visa_expiry_date" value="{{ old('operational_visa_expiry_date') }}">
                            </div>

                            <div class="sf-op-date-field" data-op-date-field="medical">
                                <div class="sf-rmb-label">Medical Visit / Follow-up Date</div>
                                <input class="sf-rmb-control" type="date" name="operational_medical_date" value="{{ old('operational_medical_date') }}">
                            </div>
                        </div>
                    </div>

                    <div class="sf-rmb-label">Notes</div>
                    <textarea class="sf-rmb-textarea" name="expense_notes" placeholder="Add any explanation needed for this claim...">{{ old('expense_notes') }}</textarea>
                    @error('expense_notes')<div style="margin-top:8px;color:#dc2626;font-weight:800;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="sf-rmb-submit-row">
<button type="submit" class="sf-rmb-submit">Submit Reimbursement Claim</button>
            </div>
        </form>
    </div>
</section>
@endsection


<style>
/* SADA FINAL PORTAL DYNAMIC REIMBURSEMENT FIELDS */
.sf-rmb-dynamic-fields {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-top: 4px;
}

.sf-rmb-dynamic-field label {
    display: block;
    margin-bottom: 8px;
    color: #64748b;
    font-size: 12px;
    font-weight: 950;
    letter-spacing: .18em;
    text-transform: uppercase;
}

.sf-rmb-dynamic-field input {
    width: 100%;
    min-height: 54px;
    border-radius: 18px;
    border: 1px solid rgba(15, 23, 42, .10);
    background: rgba(255, 255, 255, .92);
    padding: 14px 16px;
    color: #0f172a;
    font-weight: 850;
    outline: none;
}

.sf-rmb-dynamic-help {
    margin-top: 8px;
    color: #64748b;
    font-size: 12px;
    font-weight: 750;
}

@media (max-width: 800px) {
    .sf-rmb-dynamic-fields {
        grid-template-columns: 1fr;
    }
}
</style>




<style>
/* SADA HOTFIX 2026-05-07: dynamic reimbursement fields inside claim box */
.sf-rmb-dynamic-fields {
    grid-column: 1 / -1 !important;
    width: 100% !important;
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.sf-rmb-dynamic-inner {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 16px !important;
    width: 100% !important;
}

.sf-rmb-dynamic-field {
    min-width: 0 !important;
}

.sf-rmb-dynamic-field .sf-rmb-input {
    width: 100% !important;
}

.sf-rmb-help {
    margin-top: 8px !important;
    color: #64748b !important;
    font-size: 12px !important;
    font-weight: 800 !important;
}

@media (max-width: 760px) {
    .sf-rmb-dynamic-inner {
        grid-template-columns: 1fr !important;
    }
}
</style>






<style>
/* SADA FINAL 2026-05-07: reimbursement dynamic fields inside form box */
.sf-rmb-dynamic-fields {
    grid-column: 1 / -1 !important;
    width: 100% !important;
    margin: 0 !important;
}

.sf-rmb-dynamic-inner {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 16px !important;
    width: 100% !important;
}

.sf-rmb-dynamic-field {
    min-width: 0 !important;
}

.sf-rmb-dynamic-field[style*="display: none"] {
    display: none !important;
}

.sf-rmb-dynamic-field .sf-rmb-input {
    width: 100% !important;
}

.sf-rmb-help {
    margin-top: 8px !important;
    color: #64748b !important;
    font-size: 12px !important;
    font-weight: 800 !important;
}

@media (max-width: 760px) {
    .sf-rmb-dynamic-inner {
        grid-template-columns: 1fr !important;
    }
}
</style>




<script>
/* SADA_FINAL_OPERATIONAL_DATES_JS */
(function () {
    function syncOperationalFields() {
        const form = document.querySelector('form[action*="/reimbursements"]');
        if (!form) return;

        const categorySelect = form.querySelector('select[name="expense_category"]');
        const shell = form.querySelector('[data-operational-dates-shell]');
        const fields = Array.from(form.querySelectorAll('[data-op-date-field]'));

        if (!categorySelect || !shell || !fields.length) return;

        const category = String(categorySelect.value || '').toLowerCase().trim();

        const groups = {
            ticket: ['ticket'],
            hotel: ['hotel'],
            accommodation: ['hotel'],
            visa: ['visa'],
            medical: ['medical']
        };

        const requiredNames = {
            ticket: ['operational_departure_date'],
            hotel: ['operational_check_in_date', 'operational_check_out_date'],
            accommodation: ['operational_check_in_date', 'operational_check_out_date'],
            visa: ['operational_visa_issue_date', 'operational_visa_expiry_date'],
            medical: []
        };

        const activeGroup = groups[category] ? groups[category][0] : null;
        shell.style.display = activeGroup ? '' : 'none';

        fields.forEach(function (field) {
            const group = String(field.getAttribute('data-op-date-field') || '').toLowerCase().trim();
            const input = field.querySelector('input, select, textarea');
            const visible = !!activeGroup && group === activeGroup;

            field.style.display = visible ? '' : 'none';

            if (input) {
                input.required = visible && (requiredNames[category] || []).includes(input.name);

                if (!visible) {
                    input.required = false;
                    input.value = '';
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', syncOperationalFields);
    document.addEventListener('change', function (event) {
        if (event.target && event.target.matches('select[name="expense_category"]')) {
            syncOperationalFields();
        }
    });
    document.addEventListener('livewire:navigated', syncOperationalFields);
    setTimeout(syncOperationalFields, 250);
    setTimeout(syncOperationalFields, 750);
})();
</script>

