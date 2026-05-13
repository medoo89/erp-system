@php
    $jobTitle = optional($jobApplication?->job)->title ?: 'your application';
    $mailEyebrow = 'Application Update';
    $mailTitle = $subjectLine ?? 'Your application status has been updated';
    $mailIntro = 'Your application status has been updated by the Sada Fezzan recruitment team.';
    $mailBadgeText = 'Current Status: ' . ($statusLabel ?? 'Updated');
    $mailFooter = 'Sada Fezzan Recruitment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Applicant Summary
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Applicant:</strong> {{ $jobApplication->full_name ?? 'Applicant' }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Position:</strong> {{ $jobTitle }}
                </p>

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Status:</strong> {{ $statusLabel ?? 'Updated' }}
                </p>
            </td>
        </tr>
    </table>

    @if(!empty($messageBody))
        <div style="height:18px;"></div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:22px;">
            <tr>
                <td style="padding:22px;">
                    <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:12px;">
                        Message
                    </div>

                    <p style="margin:0;font-size:15px;line-height:1.9;color:#334155;white-space:pre-line;">{{ $messageBody }}</p>
                </td>
            </tr>
        </table>
    @endif
@endsection
