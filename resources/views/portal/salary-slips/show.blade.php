@extends('portal.layouts.app')

@section('content')
@include('portal.partials.salary-slip-status-theme')

@php
    $slip = $salarySlip ?? $slip ?? $record ?? null;

    $status = $slip?->status ?? 'draft';

    $statusLabel = match($status) {
        'draft' => 'Draft',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'sent_to_bank' => 'Sent To Bank',
        'paid' => 'Paid',
        'bank_rejected' => 'Bank Rejected',
        default => ucfirst(str_replace('_', ' ', (string) $status)),
    };

    $period = ($slip?->salary_year ?? $slip?->year ?? '—') . '-' . str_pad((string) ($slip?->salary_month ?? $slip?->month ?? '—'), 2, '0', STR_PAD_LEFT);

    $currency = $slip?->currency ?? 'EUR';
    $netAmount = $slip?->net_amount ?? $slip?->net_salary ?? $slip?->total_amount ?? $slip?->amount ?? 0;
    $baseAmount = $slip?->base_amount ?? $slip?->gross_amount ?? $slip?->gross_salary ?? $netAmount;
    $adjustments = $slip?->adjustments ?? $slip?->additions_total ?? $slip?->total_additions ?? 0;
    $deductions = $slip?->deductions ?? $slip?->deductions_total ?? $slip?->total_deductions ?? 0;
    $workedDays = $slip?->worked_days_total ?? $slip?->worked_days ?? $slip?->days_worked ?? 0;

    $client = $slip?->client_name ?? data_get($slip, 'client.name') ?? data_get($slip, 'employment.client_name') ?? '—';
    $project = $slip?->project_name ?? data_get($slip, 'project.name') ?? data_get($slip, 'employment.project_name') ?? '—';
    $paymentMethod = $slip?->payment_route ?? $slip?->payment_method ?? '—';
    $treasury = data_get($slip, 'treasuryAccount.name')
        ?? data_get($slip, 'treasury_account.name')
        ?? $slip?->treasury_account_name
        ?? '—';

    $notes = $slip?->notes ?? 'Generated automatically for selected month using Current Finance Profile.';

    try {
        $attachments = method_exists($slip, 'attachments') ? $slip->attachments : collect();
    } catch (\Throwable $e) {
        $attachments = collect();
    }

    $printUrl = Route::has('portal.salary-slips.print') && $slip
        ? route('portal.salary-slips.print', $slip)
        : '#';
@endphp

<style>
    .sf-slip-detail-page {
        width: min(100% - 32px, 1180px);
        margin: 22px auto 56px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .sf-slip-title-card {
        border-radius: 30px;
        background: rgba(255,255,255,.90);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 18px 48px rgba(15,23,42,.07);
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .sf-slip-title-card h1 {
        margin: 0;
        font-size: 30px;
        line-height: 1.05;
        font-weight: 950;
        letter-spacing: -.05em;
        color: #0f172a;
    }

    .sf-slip-title-card p {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 14px;
        font-weight: 650;
    }

    .sf-slip-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .sf-slip-pill-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 10px 15px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        text-decoration: none;
        border: 1px solid rgba(15,23,42,.08);
    }

    .sf-slip-pill-print {
        background: #eff6ff;
        color: #2563eb;
    }

    .sf-slip-pill-download {
        background: #ecfdf5;
        color: #047857;
    }

    .sf-slip-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .sf-slip-kpi {
        border-radius: 24px;
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 14px 36px rgba(15,23,42,.05);
        padding: 18px;
        min-height: 96px;
    }

    .sf-slip-kpi-label {
        font-size: 11px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 12px;
    }

    .sf-slip-kpi-value {
        font-size: 22px;
        line-height: 1.1;
        font-weight: 950;
        color: #0f172a;
        letter-spacing: -.04em;
    }

    .sf-slip-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 18px;
    }

    .sf-slip-panel {
        border-radius: 28px;
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 16px 44px rgba(15,23,42,.06);
        padding: 20px;
    }

    .sf-slip-panel-title {
        margin: 0 0 16px;
        font-size: 20px;
        font-weight: 950;
        letter-spacing: -.04em;
        color: #0f172a;
    }

    .sf-slip-info-stack {
        display: grid;
        gap: 10px;
    }

    .sf-slip-info-item {
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid rgba(15,23,42,.07);
        padding: 14px;
    }

    .sf-slip-info-item strong {
        display: block;
        margin-bottom: 8px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 900;
    }

    .sf-slip-info-item span {
        color: #64748b;
        font-size: 13px;
        font-weight: 650;
    }

    .sf-slip-attachments {
        border-radius: 28px;
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 16px 44px rgba(15,23,42,.06);
        padding: 20px;
    }

    .sf-slip-attachment-grid {
        display: grid;
        gap: 12px;
    }

    .sf-slip-attachment-card {
        border-radius: 20px;
        background: #f8fafc;
        border: 1px solid rgba(15,23,42,.07);
        padding: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .sf-slip-attachment-card strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        font-weight: 900;
    }

    .sf-slip-attachment-card span {
        display: block;
        margin-top: 4px;
        color: #64748b;
        font-size: 12px;
        font-weight: 650;
    }

    .sf-slip-attachment-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .sf-slip-attachment-actions a {
        border-radius: 999px;
        padding: 9px 13px;
        font-size: 12px;
        font-weight: 900;
        text-decoration: none;
        background: #eff6ff;
        color: #2563eb;
    }

    .sf-slip-empty {
        border-radius: 20px;
        padding: 22px;
        text-align: center;
        background: #f8fafc;
        color: #64748b;
        font-size: 13px;
        border: 1px dashed rgba(15,23,42,.12);
    }

    .sf-payment-confirm-shell {
        width: 100% !important;
        margin: 0 !important;
        padding: 22px !important;
        border-radius: 30px !important;
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, .07), transparent 34%),
            rgba(255,255,255,.92) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 18px 48px rgba(15,23,42,.07) !important;
        box-sizing: border-box !important;
    }

    @media (max-width: 900px) {
        .sf-slip-kpi-grid,
        .sf-slip-main-grid {
            grid-template-columns: 1fr;
        }

        .sf-slip-title-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .sf-slip-actions {
            justify-content: flex-start;
        }
    }
</style>

<div class="sf-slip-detail-page">
    <section class="sf-slip-title-card">
        <div>
            <h1>Salary Slip #{{ $slip?->getKey() }}</h1>
            <p>Detailed view of your salary slip.</p>
        </div>

        <div class="sf-slip-actions">
            <a href="{{ $printUrl }}" target="_blank" class="sf-slip-pill-btn sf-slip-pill-print">Print</a>
            <a href="{{ $printUrl }}" target="_blank" class="sf-slip-pill-btn sf-slip-pill-download">Download</a>
            <span class="sf-portal-slip-status-badge sf-status-{{ $status }}">
                {{ strtoupper($statusLabel) }}
            </span>
        </div>
    </section>

    @include('portal.salary-slips.partials.payment-confirmation')

    <section class="sf-slip-kpi-grid">
        <div class="sf-slip-kpi">
            <div class="sf-slip-kpi-label">Period</div>
            <div class="sf-slip-kpi-value">{{ $period }}</div>
        </div>

        <div class="sf-slip-kpi">
            <div class="sf-slip-kpi-label">Net Amount</div>
            <div class="sf-slip-kpi-value">{{ number_format((float) $netAmount, 2) }}</div>
        </div>

        <div class="sf-slip-kpi">
            <div class="sf-slip-kpi-label">Currency</div>
            <div class="sf-slip-kpi-value">{{ $currency }}</div>
        </div>

        <div class="sf-slip-kpi">
            <div class="sf-slip-kpi-label">Payment Method</div>
            <div class="sf-slip-kpi-value">{{ ucfirst(str_replace('_', ' ', (string) $paymentMethod)) }}</div>
        </div>
    </section>

    <section class="sf-slip-main-grid">
        <div class="sf-slip-panel">
            <h2 class="sf-slip-panel-title">Payroll Details</h2>

            <div class="sf-slip-info-stack">
                <div class="sf-slip-info-item">
                    <strong>Worked Days</strong>
                    <span>{{ number_format((float) $workedDays, 2) }}</span>
                </div>

                <div class="sf-slip-info-item">
                    <strong>Base Amount</strong>
                    <span>{{ number_format((float) $baseAmount, 2) }}</span>
                </div>

                <div class="sf-slip-info-item">
                    <strong>Adjustments</strong>
                    <span>{{ number_format((float) $adjustments, 2) }}</span>
                </div>

                <div class="sf-slip-info-item">
                    <strong>Deductions</strong>
                    <span>{{ number_format((float) $deductions, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="sf-slip-panel">
            <h2 class="sf-slip-panel-title">Additional Info</h2>

            <div class="sf-slip-info-stack">
                <div class="sf-slip-info-item">
                    <strong>Client</strong>
                    <span>{{ $client }}</span>
                </div>

                <div class="sf-slip-info-item">
                    <strong>Project</strong>
                    <span>{{ $project }}</span>
                </div>

                <div class="sf-slip-info-item">
                    <strong>Treasury Account</strong>
                    <span>{{ $treasury }}</span>
                </div>

                <div class="sf-slip-info-item">
                    <strong>Notes</strong>
                    <span>{{ $notes }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="sf-slip-attachments">
        <h2 class="sf-slip-panel-title">Attachments</h2>

        @if($attachments && $attachments->count())
            <div class="sf-slip-attachment-grid">
                @foreach($attachments as $attachment)
                    @php
                        $path = $attachment->file_path ?? $attachment->path ?? $attachment->attachment ?? null;
                        $url = $path ? asset('storage/' . ltrim($path, '/')) : '#';
                        $type = $attachment->type ?? $attachment->attachment_type ?? 'Attachment';
                        $name = $attachment->file_name ?? $attachment->name ?? basename((string) $path) ?: 'Attachment';
                    @endphp

                    <div class="sf-slip-attachment-card">
                        <div>
                            <strong>{{ ucfirst(str_replace('_', ' ', $type)) }}</strong>
                            <span>{{ $name }}</span>
                        </div>

                        <div class="sf-slip-attachment-actions">
                            @if($path)
                                <a href="{{ $url }}" target="_blank">Open</a>
                                <a href="{{ $url }}" download>Download</a>
                            @else
                                <span>Unavailable</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="sf-slip-empty">No attachments uploaded for this salary slip.</div>
        @endif
    </section>
</div>
@endsection
