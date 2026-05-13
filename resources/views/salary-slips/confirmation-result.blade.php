@php
    $status = $status ?? 'info';

    $theme = match ($status) {
        'success' => [
            'bg' => 'linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%)',
            'border' => '#86efac',
            'title' => '#166534',
            'badgeBg' => '#dcfce7',
            'badgeText' => '#166534',
        ],
        'warning' => [
            'bg' => 'linear-gradient(135deg, #fff7ed 0%, #fffbeb 100%)',
            'border' => '#fdba74',
            'title' => '#9a3412',
            'badgeBg' => '#fed7aa',
            'badgeText' => '#9a3412',
        ],
        default => [
            'bg' => 'linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%)',
            'border' => '#93c5fd',
            'title' => '#1d4ed8',
            'badgeBg' => '#dbeafe',
            'badgeText' => '#1d4ed8',
        ],
    };

    $employee = $salarySlip->employment?->employee_name ?: 'Employee';
    $period = ($salarySlip->salary_year ?: '-') . '-' . str_pad((string) ($salarySlip->salary_month ?: 0), 2, '0', STR_PAD_LEFT);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:720px;margin:0 auto;padding:40px 20px;">
        <div style="border-radius:24px;padding:32px;background:{{ $theme['bg'] }};border:1px solid {{ $theme['border'] }};box-shadow:0 18px 40px rgba(15,23,42,.08);">
            <div style="display:inline-flex;align-items:center;padding:8px 14px;border-radius:999px;background:{{ $theme['badgeBg'] }};color:{{ $theme['badgeText'] }};font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;">
                Salary Slip Confirmation
            </div>

            <div style="margin-top:18px;font-size:38px;line-height:1.05;font-weight:900;color:{{ $theme['title'] }};">
                {{ $title }}
            </div>

            <div style="margin-top:14px;font-size:16px;line-height:1.8;color:{{ $theme['text'] }};">
                {{ $message }}
            </div>

            <div style="margin-top:24px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;">
                <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:14px;">
                    <div style="font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:#64748b;">Salary Slip</div>
                    <div style="margin-top:8px;font-size:18px;font-weight:900;color:#0f172a;">#{{ $salarySlip->id }}</div>
                </div>

                <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:14px;">
                    <div style="font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:#64748b;">Period</div>
                    <div style="margin-top:8px;font-size:18px;font-weight:900;color:#0f172a;">{{ $period }}</div>
                </div>

                <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:14px;">
                    <div style="font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:#64748b;">Employee</div>
                    <div style="margin-top:8px;font-size:16px;font-weight:800;color:#0f172a;">{{ $employee }}</div>
                </div>

                <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:14px;">
                    <div style="font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:#64748b;">Current Status</div>
                    <div style="margin-top:8px;font-size:16px;font-weight:800;color:#0f172a;">
                        {{ \App\Models\SalarySlip::statusLabels()[$salarySlip->status] ?? $salarySlip->status }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
