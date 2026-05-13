@php
    $mailEyebrow = 'Application Update';
    $mailTitle = $subjectLine ?? 'Your application status has been updated';
    $mailIntro = 'Please review the status details below. Our recruitment team will contact you if any further action is required.';
    $mailBadgeText = 'Current Status: ' . ($statusLabel ?? 'Updated');
    $mailButtonText = !empty($portalUrl) ? 'Open Portal' : null;
    $mailButtonUrl = $portalUrl ?? null;
    $mailFooter = 'Sada Fezzan Recruitment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Status Summary
                </div>

                <p style="margin:0 0 12px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    Dear <strong>{{ $applicantName ?? 'Applicant' }}</strong>,
                </p>

                <p style="margin:0 0 16px 0;font-size:16px;line-height:1.75;color:#334155;">
                    Your application for <strong>{{ $jobTitle ?? 'your application' }}</strong> has been updated to:
                </p>

                <div style="display:inline-block;padding:11px 16px;border-radius:999px;background:#e6f6f6;border:1px solid #bfeaea;color:#078a8a;font-size:13px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;">
                    {{ $statusLabel ?? 'Updated' }}
                </div>
            </td>
        </tr>
    </table>
@endsection
