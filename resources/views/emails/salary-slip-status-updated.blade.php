@php
    $employeeName = $salarySlip->employment?->employee_name ?: 'Employee';
    $clientName = $salarySlip->client?->name ?: '-';
    $projectName = $salarySlip->project?->name ?: '-';
    $currency = $salarySlip->currency ?: 'USD';
    $period = ($salarySlip->salary_year ?: '-') . '-' . str_pad((string) ($salarySlip->salary_month ?: 0), 2, '0', STR_PAD_LEFT);
    $netAmount = number_format((float) ($salarySlip->net_amount ?? 0), 2);
    $paymentMethod = $salarySlip->payment_method
        ? (\App\Models\SalarySlip::paymentMethodLabels()[$salarySlip->payment_method] ?? $salarySlip->payment_method)
        : '-';

    $mailEyebrow = 'Salary Slip';
    $mailTitle = 'Salary Slip Status Update';
    $mailIntro = 'Dear ' . $employeeName . ', your salary slip status has been updated in the ERP system.';
    $mailBadgeText = $oldStatusLabel . ' → ' . $newStatusLabel;
    $mailButtonText = 'Open Salary Slip';
    $mailButtonUrl = $printUrl ?? null;
    $mailFooter = 'Sada Fezzan Finance';
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
                    <strong>Status:</strong> {{ $oldStatusLabel }} → {{ $newStatusLabel }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Period:</strong> {{ $period }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Net Amount:</strong> {{ $netAmount }} {{ $currency }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Payment Method:</strong> {{ $paymentMethod }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Client:</strong> {{ $clientName }}
                </p>

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Project:</strong> {{ $projectName }}
                </p>
            </td>
        </tr>
    </table>

    @if(!empty($confirmUrl) || !empty($reportNotReceivedUrl))
        <div style="height:18px;"></div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:22px;">
            <tr>
                <td style="padding:22px;">
                    <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                        Confirmation Actions
                    </div>

                    @if(!empty($confirmUrl))
                        <p style="margin:0 0 12px 0;">
                            <a href="{{ $confirmUrl }}" style="display:inline-block;background:#059669;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:999px;font-weight:900;font-size:13px;">
                                Confirm Salary Received
                            </a>
                        </p>
                    @endif

                    @if(!empty($reportNotReceivedUrl))
                        <p style="margin:0;">
                            <a href="{{ $reportNotReceivedUrl }}" style="display:inline-block;background:#dc2626;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:999px;font-weight:900;font-size:13px;">
                                I Did Not Receive It
                            </a>
                        </p>
                    @endif
                </td>
            </tr>
        </table>
    @endif
@endsection
