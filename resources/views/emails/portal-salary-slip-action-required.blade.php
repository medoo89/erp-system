@php
    $employeeName = $salarySlip->employment?->employee_name ?: 'Employee';
    $period = ($salarySlip->salary_year ?: '-') . '-' . str_pad((string) ($salarySlip->salary_month ?: 0), 2, '0', STR_PAD_LEFT);
    $method = $salarySlip->payment_method === \App\Models\SalarySlip::PAYMENT_METHOD_CASH ? 'Cash Payment' : 'Bank Transfer';
    $amount = number_format((float) $salarySlip->net_amount, 2) . ' ' . ($salarySlip->currency ?: '');
    $client = $salarySlip->client?->name ?: '-';
    $project = $salarySlip->project?->name ?: '-';
    $isApproved = $mailType === 'approved';

    $mailEyebrow = 'Salary Slip';
    $mailTitle = $isApproved ? 'Salary Slip Approved' : 'Salary Payment Confirmation Required';
    $mailIntro = $isApproved
        ? 'Your salary slip has been approved. You can open it through the employee portal.'
        : 'A salary slip requires your confirmation through the employee portal.';
    $mailBadgeText = 'Period: ' . $period;
    $mailButtonText = $isApproved ? 'Open Salary Slip' : 'Confirm in Portal';
    $mailButtonUrl = $portalUrl ?? null;
    $mailFooter = 'Sada Fezzan ERP';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Salary Slip Details
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Employee:</strong> {{ $employeeName }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Period:</strong> {{ $period }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Amount:</strong> {{ $amount }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Payment Method:</strong> {{ $method }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Client:</strong> {{ $client }}
                </p>

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Project:</strong> {{ $project }}
                </p>
            </td>
        </tr>
    </table>
@endsection
