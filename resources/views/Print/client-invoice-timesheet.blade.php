<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Timesheet - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #111827;
            background: #ffffff;
            font-size: 10px;
        }

        .print-actions {
            position: fixed;
            top: 12px;
            right: 12px;
            z-index: 10;
        }

        .print-actions button {
            border: none;
            border-radius: 999px;
            padding: 9px 14px;
            font-weight: 900;
            color: #fff;
            background: #2563eb;
            cursor: pointer;
        }

        .sheet {
            width: 100%;
            border: 2px solid #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #111827;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .header-table td {
            height: 34px;
        }

        .logo-cell {
            width: 22%;
            text-align: center;
            font-weight: 900;
            color: #234b74;
        }

        .logo-title {
            font-size: 13px;
            font-weight: 900;
        }

        .main-title {
            text-align: center;
            font-size: 16px;
            font-weight: 900;
            line-height: 1.25;
        }

        .small {
            font-size: 8px;
            color: #374151;
        }

        .days-head {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            width: 22px;
            text-align: center;
            font-size: 8px;
            padding: 3px 1px;
        }

        .no-col {
            width: 28px;
            text-align: center;
        }

        .qty-col {
            width: 46px;
            text-align: center;
        }

        .position-col {
            width: 210px;
        }

        .name-col {
            width: 220px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: 900;
        }

        .day-cell {
            text-align: center;
            width: 22px;
            height: 24px;
            font-size: 9px;
            font-weight: 900;
        }

        .daily-total-label {
            text-align: right;
            font-weight: 900;
            letter-spacing: .03em;
        }

        .remarks {
            height: 32px;
        }

        .signature-table td {
            height: 30px;
        }

        .signature-title {
            font-weight: 900;
            background: #f3f4f6;
        }

        .stamp-placeholder {
            height: 68px;
            color: #2563eb;
            font-size: 14px;
            font-weight: 900;
            text-align: center;
            border: 2px solid #2563eb;
            transform: rotate(-6deg);
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 260px;
            margin: 8px auto;
        }

        @media print {
            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-actions">
        <button onclick="window.print()">Print Timesheet</button>
    </div>

    @php
        $monthTitle = $periodStart->format('F-y');
        $workDaysByLine = $invoice->workDays->groupBy('client_invoice_line_id');
        $dailyTotals = [];

        foreach ($days as $day) {
            $key = $day->format('Y-m-d');
            $dailyTotals[$key] = (float) $invoice->workDays
                ->where('work_date', $key)
                ->where('day_status', \App\Models\ClientInvoiceWorkDay::STATUS_PAID)
                ->sum('billable_units');
        }
    @endphp

    <div class="sheet">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <div class="logo-title">Dietsmann Libya</div>
                    <div class="small">Client / Project</div>
                </td>
                <td class="logo-cell">
                    <div class="logo-title">SADA FEZZAN</div>
                    <div class="small">Contractor</div>
                </td>
                <td class="main-title">
                    Summary Monthly Sheet<br>
                    {{ $monthTitle }}
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th class="no-col">#</th>
                    <th class="qty-col">Q.t</th>
                    <th class="position-col">Position</th>
                    <th class="name-col">Name</th>
                    @foreach($days as $day)
                        <th class="days-head">{{ $day->format('D d') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($lines as $index => $line)
                    @php
                        $lineDays = $workDaysByLine->get($line->id, collect());
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">1</td>
                        <td>{{ $line->position_title ?: $line->service_title ?: '-' }}</td>
                        <td>{{ $line->candidate_name ?: ($line->salarySlip?->employment?->full_name ?? '-') }}</td>

                        @foreach($days as $day)
                            @php
                                $dateKey = $day->format('Y-m-d');
                                $workDay = $lineDays->first(function ($item) use ($dateKey) {
                                    return optional($item->work_date)->format('Y-m-d') === $dateKey;
                                });

                                $symbol = '';
                                if ($workDay) {
                                    if ($workDay->day_status === \App\Models\ClientInvoiceWorkDay::STATUS_PAID) {
                                        $symbol = (float) $workDay->billable_units > 0 ? '1' : '';
                                    } elseif ($workDay->day_status === \App\Models\ClientInvoiceWorkDay::STATUS_ABSENT) {
                                        $symbol = 'A';
                                    } else {
                                        $symbol = '0';
                                    }
                                }
                            @endphp
                            <td class="day-cell">{{ $symbol }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="center">1</td>
                        <td class="center"></td>
                        <td></td>
                        <td></td>
                        @foreach($days as $day)
                            <td class="day-cell"></td>
                        @endforeach
                    </tr>
                @endforelse

                @for($i = $lines->count() + 1; $i <= 5; $i++)
                    <tr>
                        <td class="center">{{ $i }}</td>
                        <td class="center"></td>
                        <td></td>
                        <td></td>
                        @foreach($days as $day)
                            <td class="day-cell"></td>
                        @endforeach
                    </tr>
                @endfor

                <tr>
                    <td colspan="4" class="daily-total-label">DAILY TOTAL:</td>
                    @foreach($days as $day)
                        <td class="day-cell">{{ number_format((float) ($dailyTotals[$day->format('Y-m-d')] ?? 0), 0) }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        <table>
            <tr>
                <td style="width:85px;" class="bold">Remarks:</td>
                <td class="remarks">{{ $invoice->notes ?: '' }}</td>
            </tr>
        </table>

        <table class="signature-table">
            <tr>
                <td colspan="2" class="signature-title">Dietsmann Team Leader</td>
                <td colspan="2" class="signature-title">Contractor</td>
            </tr>
            <tr>
                <td style="width:80px;">Name:</td>
                <td></td>
                <td style="width:80px;">Name:</td>
                <td></td>
            </tr>
            <tr>
                <td>Signature:</td>
                <td rowspan="3">
                    <div class="stamp-placeholder">Dietsmann Libya<br>Team Leader</div>
                </td>
                <td>Signature:</td>
                <td rowspan="3"></td>
            </tr>
            <tr>
                <td>Position:</td>
                <td>Position:</td>
            </tr>
            <tr>
                <td>Date:</td>
                <td>Date:</td>
            </tr>
        </table>
    </div>
</body>
</html>
