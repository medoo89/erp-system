<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Update Attendance Days</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #eef9fa, #f8fbff);
            color: #0f172a;
        }

        .page {
            width: min(100% - 48px, 1280px);
            margin: 34px auto;
        }

        .hero {
            border-radius: 30px;
            padding: 32px;
            background: linear-gradient(135deg, #0b1f3a, #0f766e);
            color: #fff;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .18);
            margin-bottom: 22px;
        }

        .hero small {
            display: block;
            text-transform: uppercase;
            letter-spacing: .18em;
            font-weight: 900;
            opacity: .75;
            margin-bottom: 10px;
        }

        .hero h1 {
            margin: 0;
            font-size: 42px;
            line-height: 1;
            letter-spacing: -.06em;
        }

        .hero p {
            margin: 12px 0 0;
            opacity: .82;
            font-weight: 650;
        }

        .notice {
            border-radius: 20px;
            padding: 16px 18px;
            background: #ecfdf5;
            color: #065f46;
            font-weight: 850;
            margin-bottom: 18px;
            border: 1px solid #bbf7d0;
        }

        .debug {
            margin-top: 10px;
            color: #064e3b;
            font-size: 13px;
            font-weight: 750;
            line-height: 1.8;
        }

        .card {
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            border-radius: 28px;
            box-shadow: 0 18px 50px rgba(15,23,42,.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
            padding: 18px;
            border-bottom: 1px solid #e5edf0;
        }

        td {
            padding: 16px 18px;
            border-bottom: 1px solid #edf2f4;
            font-weight: 750;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        input[type="checkbox"] {
            width: 24px;
            height: 24px;
            accent-color: #2563eb;
        }

        select,
        input[type="text"] {
            width: 100%;
            min-height: 44px;
            border-radius: 14px;
            border: 1px solid #dbe4e8;
            background: #fff;
            padding: 0 14px;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            outline: none;
        }

        .actions {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 22px;
            background: #fff;
            border-top: 1px solid #edf2f4;
            position: sticky;
            bottom: 0;
        }

        .btn {
            border: 0;
            border-radius: 999px;
            min-height: 46px;
            padding: 0 22px;
            font-size: 14px;
            font-weight: 950;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #f5b800;
            color: #0f172a;
            box-shadow: 0 14px 30px rgba(245,184,0,.24);
        }

        .btn-secondary {
            background: #f8fafc;
            color: #0f172a;
            border: 1px solid #e5edf0;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .summary-box {
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(15,23,42,.08);
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 12px 32px rgba(15,23,42,.05);
        }

        .summary-box span {
            display: block;
            color: #64748b;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .summary-box strong {
            font-size: 24px;
            font-weight: 950;
        }
    </style>
</head>
<body>
@php
    $currency = $salarySlip->currency ?: 'EUR';
@endphp

<div class="page">
    <div class="hero">
        <small>Salary Slip Attendance</small>
        <h1>Update Attendance Days</h1>
        <p>
            {{ $salarySlip->employment?->employee_name ?? 'Employee' }}
            · Salary Slip #{{ $salarySlip->id }}
            · {{ str_pad((string) $salarySlip->salary_month, 2, '0', STR_PAD_LEFT) }}/{{ $salarySlip->salary_year }}
        </p>
    </div>

    @if(session('success'))
        <div class="notice">
            {{ session('success') }}

            @if(session('debug'))
                <div class="debug">
                    Updated rows: {{ session('debug.updated_rows') }}<br>
                    Paid days: {{ session('debug.paid_days') }}<br>
                    Not paid days: {{ session('debug.not_paid_days') }}<br>
                    Base amount: {{ number_format((float) session('debug.base_amount'), 2) }} {{ $currency }}<br>
                    Net amount: {{ number_format((float) session('debug.net_amount'), 2) }} {{ $currency }}
                </div>
            @endif
        </div>
    @endif

    <div class="summary">
        <div class="summary-box">
            <span>Daily Rate</span>
            <strong>{{ number_format((float) $salarySlip->daily_rate, 2) }} {{ $currency }}</strong>
        </div>
        <div class="summary-box">
            <span>Current Paid Days</span>
            <strong>{{ $salarySlip->days_worked ?? 0 }}</strong>
        </div>
        <div class="summary-box">
            <span>Current Net</span>
            <strong>{{ number_format((float) $salarySlip->net_amount, 2) }} {{ $currency }}</strong>
        </div>
        <div class="summary-box">
            <span>Total Days</span>
            <strong>{{ $days->count() }}</strong>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.salary-slips.attendance.direct.update', $salarySlip->id) }}">
        @csrf

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th style="width: 150px;">Date</th>
                        <th style="width: 150px;">Day</th>
                        <th style="width: 90px;">Paid</th>
                        <th style="width: 240px;">Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($days as $day)
                        @php
                            $status = $day->attendance_status ?: 'present';

                            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                                $isPaid = false;
                            } elseif (property_exists($day, 'is_paid_day')) {
                                $isPaid = (bool) $day->is_paid_day;
                            } elseif (false) {
                                $isPaid = false;
                            } else {
                                $isPaid = true;
                            }

                            $dateValue = $day->work_date ?? $day->date ?? null;
                            $dateText = '-';
                            $dayName = $day->day_name ?? '-';

                            if ($dateValue) {
                                try {
                                    $carbon = \Carbon\Carbon::parse($dateValue);
                                    $dateText = $carbon->format('Y-m-d');
                                    $dayName = $day->day_name ?: $carbon->format('l');
                                } catch (\Throwable $e) {
                                    $dateText = (string) $dateValue;
                                }
                            }
                        @endphp

                        <tr>
                            <td>{{ $dateText }}</td>
                            <td>{{ $dayName }}</td>
                            <td>
                                <input
                                    type="checkbox"
                                    name="paid[{{ $day->id }}]"
                                    value="1"
                                    @checked($isPaid)
                                >
                            </td>
                            <td>
                                <select name="status[{{ $day->id }}]">
                                    <option value="present" @selected($status === 'present')>Present</option>
                                    <option value="absent" @selected($status === 'absent')>Absent</option>
                                    <option value="sick" @selected($status === 'sick')>Sick</option>
                                    <option value="leave" @selected($status === 'leave')>Leave</option>
                                    <option value="unpaid_leave" @selected($status === 'unpaid_leave')>Unpaid Leave</option>
                                    <option value="holiday" @selected($status === 'holiday')>Holiday</option>
                                    <option value="travel" @selected($status === 'travel')>Travel</option>
                                    <option value="other" @selected($status === 'other')>Other</option>
                                </select>
                            </td>
                            <td>
                                <input
                                    type="text"
                                    name="notes[{{ $day->id }}]"
                                    value="{{ $day->notes }}"
                                    placeholder="Optional notes..."
                                >
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Save Attendance</button>
                <a href="{{ route('filament.admin.resources.salary-slips.view', ['record' => $salarySlip->id]) }}" class="btn btn-secondary">Back to Salary Slip</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
