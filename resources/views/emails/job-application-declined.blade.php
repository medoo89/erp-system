@php
    $jobTitle = optional($jobApplication?->job)->title ?: 'your application';
    $mailEyebrow = 'Application Update';
    $mailTitle = 'Update on Your Job Application';
    $mailIntro = 'Thank you for your interest in Sada Fezzan. After reviewing your application, we would like to share the latest update with you.';
    $mailBadgeText = 'Application Status: Declined';
    $mailFooter = 'Sada Fezzan Recruitment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fff7f7;border:1px solid #f3d2d2;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#b91c1c;margin-bottom:14px;">
                    Application Details
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Applicant:</strong> {{ $jobApplication->full_name ?? 'Applicant' }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Position:</strong> {{ $jobTitle }}
                </p>

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Reason:</strong> {{ $declineReasonLabel ?? 'Declined' }}
                </p>
            </td>
        </tr>
    </table>

    @if(!empty($declineNotes))
        <div style="height:18px;"></div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:22px;">
            <tr>
                <td style="padding:22px;">
                    <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:12px;">
                        Additional Notes
                    </div>

                    <p style="margin:0;font-size:15px;line-height:1.9;color:#334155;white-space:pre-line;">{{ $declineNotes }}</p>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:20px 0 0 0;font-size:15px;line-height:1.85;color:#334155;">
        We sincerely appreciate the time and effort you invested in your application, and we wish you success in your future career opportunities.
    </p>
@endsection
