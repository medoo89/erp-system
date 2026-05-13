@extends('portal.layouts.app')

@php

    $salaryDisplayAmount = function ($slip) {
        return number_format((float) ($slip->payment_total_amount ?? $slip->net_amount ?? 0), 2);
    };

    $pageTitle = 'Portal Salary Slip';

    $salarySlip->loadMissing(['days', 'employment', 'client', 'project', 'treasuryAccount']);

    $status = \App\Models\SalarySlip::statusLabels()[$salarySlip->status] ?? ucfirst(str_replace('_', ' ', (string) $salarySlip->status));
    $currency = strtoupper((string) ($salarySlip->currency ?: 'EUR'));

    $salaryNet = (float) ($salarySlip->net_amount ?? 0);
    $paymentTotal = (float) ($salarySlip->payment_total_amount ?: $salaryNet);
    $sameCurrencyTotal = (float) ($salarySlip->reimbursement_same_currency_total ?? 0);
    $convertedTotal = (float) ($salarySlip->reimbursement_converted_total ?? 0);
    $allConvertedReimbursement = round($sameCurrencyTotal + $convertedTotal, 2);

    $linkedReimbursements = \App\Models\FinanceExpense::query()
        ->where('reimbursed_salary_slip_id', $salarySlip->id)
        ->get();

    $reimbursementByCurrency = $linkedReimbursements
        ->groupBy(fn ($item) => strtoupper((string) ($item->reimbursement_currency ?: $item->currency ?: $currency)))
        ->map(function ($items, $cur) {
            return [
                'currency' => strtoupper((string) $cur),
                'amount' => (float) $items->sum(fn ($item) => (float) ($item->reimbursement_amount ?: $item->amount ?: 0)),
                'items' => $items,
            ];
        })
        ->values();

    $breakdown = collect((array) ($salarySlip->reimbursement_breakdown ?? []));

    if ($paymentTotal <= $salaryNet && $allConvertedReimbursement > 0) {
        $paymentTotal = round($salaryNet + $allConvertedReimbursement, 2);
    }

    $days = collect($salarySlip->days ?? [])->sortBy(fn ($day) => $day->work_date ?? $day->date ?? $day->id);

    $paidDays = $days->filter(function ($day) {
        $status = $day->attendance_status ?: $day->status ?: 'present';

        if (in_array($status, ['absent', 'unpaid_leave'], true)) {
            return false;
        }

        return (bool) ($day->is_paid_day ?? $day->is_paid ?? $day->paid ?? true);
    })->count();

    $attendanceCounts = [
        'present' => $days->where('attendance_status', 'present')->count(),
        'absent' => $days->where('attendance_status', 'absent')->count(),
        'sick' => $days->where('attendance_status', 'sick')->count(),
        'leave' => $days->where('attendance_status', 'leave')->count(),
        'unpaid_leave' => $days->where('attendance_status', 'unpaid_leave')->count(),
        'holiday' => $days->where('attendance_status', 'holiday')->count(),
        'travel' => $days->where('attendance_status', 'travel')->count(),
        'other' => $days->where('attendance_status', 'other')->count(),
    ];

    $employeeName = $salarySlip->employment?->employee_name
        ?: $salarySlip->employee_name
        ?: 'Employee';

    $jobTitle = $salarySlip->employment?->position_title
        ?: $salarySlip->employment?->job_title
        ?: $salarySlip->job_title
        ?: '-';

    $employeeCode = $salarySlip->employment?->employee_code
        ?: $salarySlip->employee_code
        ?: '-';

    $periodLabel = ($salarySlip->salary_year ?: '-') . '-' . str_pad((string) ($salarySlip->salary_month ?: 0), 2, '0', STR_PAD_LEFT);

    $paymentMethodLabel = match ((string) $salarySlip->payment_method) {
        'bank' => 'Bank Transfer',
        'cash' => 'Cash Payment',
        default => ucfirst((string) ($salarySlip->payment_method ?: 'Pending')),
    };

    $receiptStatus = $salarySlip->employee_confirmation_status;
    $needsReceiptConfirmation = in_array($salarySlip->status, ['sent_to_bank', 'paid'], true)
        && in_array($receiptStatus, [null, '', 'pending'], true);

    $receiptConfirmed = $receiptStatus === 'received';
    $receiptNotReceived = $receiptStatus === 'not_received' || $salarySlip->status === 'bank_rejected';
@endphp

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,600,0,0" rel="stylesheet">

<style>
    .sf-slip-wrap{display:grid;gap:18px;}
    .sf-slip-hero,.sf-slip-card{border-radius:30px;background:rgba(255,255,255,.92);border:1px solid rgba(15,23,42,.08);box-shadow:0 18px 48px rgba(15,23,42,.07);padding:26px;overflow:hidden;}
    .sf-slip-hero{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap;}
    .sf-slip-title{font-size:26px;font-weight:950;color:#0f172a;letter-spacing:-.04em;}
    .sf-slip-muted{color:#64748b;font-size:13px;font-weight:750;margin-top:6px;}
    .sf-slip-actions{display:flex;gap:10px;flex-wrap:wrap;}
    .sf-slip-btn,.sf-slip-badge{height:38px;display:inline-flex;align-items:center;justify-content:center;padding:0 14px;border-radius:999px;text-decoration:none;font-size:12px;font-weight:950;letter-spacing:.05em;}
    .sf-slip-btn-blue{background:#eff6ff;color:#1d4ed8;}
    .sf-slip-btn-green{background:#ecfdf5;color:#15803d;}
    .sf-slip-badge{background:#fff7ed;color:#b45309;border:1px solid rgba(245,158,11,.24);}
    .sf-confirm-box{border-radius:30px;padding:24px;background:linear-gradient(135deg,#fffbeb,#ecfeff);border:1px solid rgba(245,158,11,.24);box-shadow:0 18px 44px rgba(15,23,42,.06);}
    .sf-confirm-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;flex-wrap:wrap;}
    .sf-kicker{font-size:11px;font-weight:950;letter-spacing:.22em;text-transform:uppercase;color:#64748b;}
    .sf-confirm-title{font-size:22px;font-weight:950;color:#0f172a;margin-top:6px;}
    .sf-confirm-text{font-size:13px;font-weight:750;color:#64748b;margin-top:8px;}
    .sf-confirm-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:18px;}
    .sf-confirm-panel{border-radius:24px;padding:16px;border:1px solid rgba(15,23,42,.08);background:rgba(255,255,255,.70);}
    .sf-confirm-panel-received{background:rgba(236,253,245,.78);border-color:rgba(16,185,129,.20);}
    .sf-confirm-panel-not{background:rgba(254,242,242,.78);border-color:rgba(239,68,68,.18);}
    .sf-md3-note{width:100%;height:48px;border-radius:16px;border:1px solid rgba(15,23,42,.10);padding:0 14px;font-weight:750;background:white;}
    .sf-md3-btn{width:100%;height:44px;border:0;border-radius:999px;margin-top:10px;font-weight:950;cursor:pointer;}
    .sf-md3-btn-received{background:#ecfdf5;color:#047857;border:1px solid rgba(16,185,129,.25);}
    .sf-md3-btn-not-received{background:#fff1f2;color:#be123c;border:1px solid rgba(244,63,94,.22);}
    .sf-kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;}
    .sf-kpi{border-radius:24px;padding:18px;background:rgba(248,250,252,.90);border:1px solid rgba(15,23,42,.07);}
    .sf-label{font-size:11px;font-weight:950;letter-spacing:.18em;text-transform:uppercase;color:#64748b;}
    .sf-value{margin-top:9px;font-size:22px;font-weight:950;color:#0f172a;letter-spacing:-.035em;}
    .sf-finance{background:radial-gradient(circle at 92% 8%,rgba(34,211,238,.13),transparent 30%),rgba(255,255,255,.96);}
    .sf-finance-head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap;margin-bottom:18px;}
    .sf-section-title{font-size:24px;font-weight:950;color:#0f172a;letter-spacing:-.04em;margin-top:5px;}
    .sf-final-card{min-width:280px;border-radius:26px;padding:18px;background:rgba(248,250,252,.92);border:1px solid rgba(15,23,42,.08);}
    .sf-final-card strong{display:block;font-size:29px;font-weight:950;color:#0f172a;letter-spacing:-.045em;margin-top:8px;}
    .sf-money-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;}
    .sf-money-card{position:relative;border-radius:26px;padding:20px;background:linear-gradient(135deg,rgba(255,255,255,.98),rgba(248,250,252,.92));border:1px solid rgba(15,23,42,.075);box-shadow:0 12px 34px rgba(15,23,42,.045);overflow:hidden;}
    .sf-money-card::before{content:"";position:absolute;inset:0 0 auto 0;height:4px;background:linear-gradient(90deg,#22d3ee,#2563eb);}
    .sf-money-card-final::before{background:linear-gradient(90deg,#10b981,#0f766e);}
    .sf-money-big{margin-top:14px;font-size:28px;font-weight:950;color:#0f172a;line-height:1.05;letter-spacing:-.045em;overflow-wrap:anywhere;}
    .sf-money-card p{font-size:12px;font-weight:750;color:#64748b;line-height:1.45;margin:10px 0 0;}
    .sf-breakdown{margin-top:16px;border-radius:24px;padding:16px;background:rgba(236,253,245,.55);border:1px solid rgba(15,118,110,.14);}
    .sf-breakdown-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:14px;}
    .sf-row-card{border-radius:18px;padding:14px;background:rgba(255,255,255,.75);border:1px solid rgba(15,23,42,.07);}
    .sf-row-card span{display:block;color:#0f766e;font-size:11px;font-weight:950;letter-spacing:.14em;text-transform:uppercase;}
    .sf-row-card strong{display:block;margin-top:6px;color:#0f172a;font-size:20px;font-weight:950;}
    .sf-row-card small{display:block;margin-top:5px;color:#64748b;font-size:11px;font-weight:750;}
    .sf-attendance-table-wrap{overflow:auto;border-radius:24px;border:1px solid rgba(15,23,42,.08);margin-top:14px;}
    .sf-attendance-table{width:100%;border-collapse:collapse;background:white;}
    .sf-attendance-table th{background:#f8fafc;color:#64748b;font-size:11px;font-weight:950;letter-spacing:.12em;text-transform:uppercase;text-align:left;padding:13px;}
    .sf-attendance-table td{padding:13px;border-top:1px solid rgba(15,23,42,.06);font-size:13px;font-weight:800;color:#0f172a;}
    .sf-pill{display:inline-flex;align-items:center;border-radius:999px;padding:6px 10px;font-size:11px;font-weight:950;text-transform:uppercase;}
    .sf-pill-paid{background:#ecfdf5;color:#047857;}
    .sf-pill-unpaid{background:#fff1f2;color:#be123c;}
    @media(max-width:1100px){.sf-kpi-grid,.sf-money-grid,.sf-breakdown-grid{grid-template-columns:1fr 1fr;}.sf-confirm-grid{grid-template-columns:1fr;}}
    @media(max-width:700px){.sf-kpi-grid,.sf-money-grid,.sf-breakdown-grid{grid-template-columns:1fr;}}

.sf-paid-pill-travel {
    background: #dbeafe !important;
    color: #1d4ed8 !important;
    border-color: #bfdbfe !important;
}

</style>

<div class="sf-slip-wrap">
    <section class="sf-slip-hero">
        <div>
            <div class="sf-slip-title">Salary Slip #{{ $salarySlip->id }}</div>
            <div class="sf-slip-muted">Detailed view of your salary, attendance, reimbursements, and payment confirmation.</div>
        </div>

        <div class="sf-slip-actions">
            <a href="{{ route('portal.salary-slips.print', $salarySlip) }}" target="_blank" class="sf-slip-btn sf-slip-btn-blue">Print</a>
            <a href="{{ route('portal.salary-slips.print', $salarySlip) }}" target="_blank" class="sf-slip-btn sf-slip-btn-green">Download</a>
            <span class="sf-slip-badge">{{ strtoupper($status) }}</span>
        </div>
    </section>

    @if($needsReceiptConfirmation)
        <section class="sf-confirm-box">
            <div class="sf-confirm-head">
                <div>
                    <div class="sf-kicker">Employee Receipt Confirmation</div>
                    <div class="sf-confirm-title">
                        {{ $salarySlip->payment_method === 'cash' ? 'Please confirm receipt of this cash salary payment' : 'Please confirm receipt of this bank salary payment' }}
                    </div>
                    <div class="sf-confirm-text">Payment method: {{ $paymentMethodLabel }}. Confirming here will update the ERP record directly.</div>
                </div>
                <span class="sf-slip-badge">Action Required</span>
            </div>

            <div class="sf-confirm-grid">
                <form method="POST" action="{{ route('portal.salary-slips.confirm-received', $salarySlip) }}" class="sf-confirm-panel sf-confirm-panel-received">
                    @csrf
                    <div style="font-weight:950;color:#0f172a;margin-bottom:8px;">✓ Confirm Payment Received</div>
                    <input class="sf-md3-note" name="employee_confirmation_notes" placeholder="Optional note..." />
                    <button class="sf-md3-btn sf-md3-btn-received" type="submit">Confirm Received</button>
                </form>

                <form method="POST" action="{{ route('portal.salary-slips.not-received', $salarySlip) }}" class="sf-confirm-panel sf-confirm-panel-not">
                    @csrf
                    <div style="font-weight:950;color:#0f172a;margin-bottom:8px;">! Not Received</div>
                    <input class="sf-md3-note" name="employee_confirmation_notes" placeholder="Tell us what happened..." />
                    <button class="sf-md3-btn sf-md3-btn-not-received" type="submit">Not Received</button>
                </form>
            </div>
        </section>
    @elseif($receiptConfirmed || $receiptNotReceived || $receiptStatus)
        <section class="sf-confirm-box">
            <div class="sf-kicker">Payment Confirmation Submitted</div>
            <div class="sf-confirm-title">
                {{ strtoupper(str_replace('_', ' ', (string) ($receiptStatus ?: 'pending'))) }}
            </div>
            <div class="sf-confirm-text">
                @if($salarySlip->employee_confirmed_at)
                    Submitted at {{ \Carbon\Carbon::parse($salarySlip->employee_confirmed_at)->format('d M Y H:i') }}.
                @endif
                @if($salarySlip->employee_confirmation_notes)
                    Note: {{ $salarySlip->employee_confirmation_notes }}
                @endif
            </div>
        </section>
    @endif

    <section class="sf-kpi-grid">
        <div class="sf-kpi">
            <div class="sf-label">Period</div>
            <div class="sf-value">{{ $periodLabel }}</div>
        </div>
        <div class="sf-kpi">
            <div class="sf-label">Final Payable</div>
            <div class="sf-value">{{ number_format($paymentTotal, 2) }} {{ $currency }}</div>
        </div>
        <div class="sf-kpi">
            <div class="sf-label">Currency</div>
            <div class="sf-value">{{ $currency }}</div>
        </div>
        <div class="sf-kpi">
            <div class="sf-label">Payment Method</div>
            <div class="sf-value">{{ $paymentMethodLabel }}</div>
        </div>
    </section>

    <section class="sf-slip-card">
        <div class="sf-kicker">Employee</div>
        <div class="sf-section-title">{{ $employeeName }}</div>
        <div class="sf-breakdown-grid">
            <div class="sf-row-card"><span>Job Title</span><strong>{{ $jobTitle }}</strong></div>
            <div class="sf-row-card"><span>Employee Code</span><strong>{{ $employeeCode }}</strong></div>
            <div class="sf-row-card"><span>Client / Project</span><strong>{{ $salarySlip->client?->name ?? '-' }}</strong><small>{{ $salarySlip->project?->name ?? '-' }}</small></div>
        </div>
    </section>

    <section class="sf-slip-card sf-finance">
        <div class="sf-finance-head">
            <div>
                <div class="sf-kicker">Salary Calculation</div>
                <div class="sf-section-title">Financial Overview</div>
                <div class="sf-slip-muted">Salary net, linked reimbursements, exchange-rate conversion, and final payable amount.</div>
            </div>
            <div class="sf-final-card">
                <div class="sf-label">Final Net Amount</div>
                <strong>{{ number_format($paymentTotal, 2) }} {{ $currency }}</strong>
                <small>Salary {{ number_format($salaryNet, 2) }} · Reimb. {{ number_format($allConvertedReimbursement, 2) }}</small>
            </div>
        </div>

        <div class="sf-money-grid">
            <div class="sf-money-card">
                <div class="sf-label">Salary Net</div>
                <div class="sf-money-big">{{ number_format($salaryNet, 2) }} {{ $currency }}</div>
                <p>Original salary slip net amount.</p>
            </div>

            <div class="sf-money-card">
                <div class="sf-label">Linked Reimbursement</div>
                <div class="sf-money-big">
                    @if($reimbursementByCurrency->count())
                        @foreach($reimbursementByCurrency as $row)
                            {{ number_format((float) $row['amount'], 2) }} {{ $row['currency'] }}@if(!$loop->last)<br>+ @endif
                        @endforeach
                    @else
                        0.00 {{ $currency }}
                    @endif
                </div>
                <p>Original reimbursement currencies.</p>
            </div>

            <div class="sf-money-card">
                <div class="sf-label">Converted Reimb.</div>
                <div class="sf-money-big">{{ number_format($allConvertedReimbursement, 2) }} {{ $currency }}</div>
                <p>All reimbursements converted into payment currency.</p>
            </div>

            <div class="sf-money-card sf-money-card-final">
                <div class="sf-label">Final Payable</div>
                <div class="sf-money-big">{{ number_format($paymentTotal, 2) }} {{ $currency }}</div>
                <p>Salary net plus converted reimbursements.</p>
            </div>
        </div>

        <div class="sf-breakdown">
            <div class="sf-kicker">Currency Breakdown</div>
            <div class="sf-slip-muted">Original currencies are shown clearly. Converted totals appear in {{ $currency }} after finance enters exchange rates.</div>

            <div class="sf-breakdown-grid">
                <div class="sf-row-card">
                    <span>{{ $currency }}</span>
                    <strong>{{ number_format($salaryNet, 2) }}</strong>
                    <small>Salary Net</small>
                </div>

                @foreach($reimbursementByCurrency as $row)
                    <div class="sf-row-card">
                        <span>{{ $row['currency'] }}</span>
                        <strong>{{ number_format((float) $row['amount'], 2) }}</strong>
                        <small>Original reimbursement amount</small>
                    </div>
                @endforeach

                @foreach($breakdown as $row)
                    <div class="sf-row-card">
                        <span>{{ $row['currency'] ?? '-' }} → {{ $row['payment_currency'] ?? $currency }}</span>
                        <strong>{{ number_format((float) ($row['converted_amount'] ?? 0), 2) }}</strong>
                        <small>
                            Original {{ number_format((float) ($row['original_amount'] ?? 0), 2) }}
                            · Rate {{ $row['exchange_rate'] ?? 1 }}
                        </small>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="sf-breakdown-grid" style="margin-top:16px;">
            <div class="sf-row-card"><span>Gross Amount</span><strong>{{ number_format((float) ($salarySlip->base_amount ?? 0), 2) }} {{ $currency }}</strong></div>
            <div class="sf-row-card"><span>Additions</span><strong>{{ number_format((float) ($salarySlip->adjustments_amount ?? 0), 2) }} {{ $currency }}</strong></div>
            <div class="sf-row-card"><span>Deductions</span><strong>{{ number_format((float) ($salarySlip->deductions_amount ?? 0), 2) }} {{ $currency }}</strong></div>
        </div>
    </section>

    <section class="sf-slip-card">
        <div class="sf-kicker">Attendance Summary</div>
        <div class="sf-section-title">Attendance Days</div>

        <div class="sf-breakdown-grid">
            <div class="sf-row-card"><span>Paid Days</span><strong>{{ $paidDays }}</strong></div>
            <div class="sf-row-card"><span>Absent</span><strong>{{ $attendanceCounts['absent'] }}</strong></div>
            <div class="sf-row-card"><span>Unpaid Leave</span><strong>{{ $attendanceCounts['unpaid_leave'] }}</strong></div>
            <div class="sf-row-card"><span>Sick</span><strong>{{ $attendanceCounts['sick'] }}</strong></div>
            <div class="sf-row-card"><span>Leave</span><strong>{{ $attendanceCounts['leave'] }}</strong></div>
            <div class="sf-row-card"><span>Travel</span><strong>{{ $attendanceCounts['travel'] }}</strong></div>
        </div>

        <div class="sf-attendance-table-wrap">
            <table class="sf-attendance-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Status</th>
                        <th>Paid</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($days as $day)
                        @php
                            $dateValue = $day->work_date ?? $day->date ?? null;
                            $dayStatus = $day->attendance_status ?: $day->status ?: 'present';
                            $isPaid = !in_array($dayStatus, ['absent', 'unpaid_leave'], true)
                                && (bool) ($day->is_paid_day ?? $day->is_paid ?? $day->paid ?? true);
                        @endphp
                        <tr>
                            <td>{{ $dateValue ? \Carbon\Carbon::parse($dateValue)->format('Y-m-d') : '-' }}</td>
                            <td>{{ $day->day_name ?: ($dateValue ? \Carbon\Carbon::parse($dateValue)->format('l') : '-') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $dayStatus)) }}</td>
                            <td>
                                <span class="sf-pill {{ $isPaid ? ($dayStatus === 'travel' ? 'sf-pill-paid-travel' : 'sf-pill-paid') : 'sf-pill-unpaid' }}">
                                    {{ $isPaid ? 'Paid' : 'Not Paid' }}
                                </span>
                            </td>
                            <td>{{ $day->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">No attendance days were generated for this salary slip.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
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


<style id="sf-portal-attendance-table-scroll-six-days-final">
    .sf-attendance-table-wrap {
        max-height: 430px !important;
        overflow-y: auto !important;
        overflow-x: auto !important;
        scrollbar-width: thin;
    }

    .sf-attendance-table-wrap::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .sf-attendance-table-wrap::-webkit-scrollbar-thumb {
        background: rgba(37, 99, 235, .28);
        border-radius: 999px;
    }

    .sf-attendance-table {
        display: table !important;
        width: 100% !important;
    }

    .sf-attendance-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }
</style>


<style id="sf-portal-travel-paid-pill-final">
    .sf-pill.sf-pill-paid-travel {
        background: #dbeafe !important;
        color: #1d4ed8 !important;
        border: 1px solid rgba(37, 99, 235, .22) !important;
    }
</style>

