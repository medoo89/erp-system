@php
    $candidateName = $preEmployment->candidate_name ?? optional($preEmployment->jobApplication)->full_name ?? 'Candidate';
    $jobTitle = optional($preEmployment->jobApplication?->job)->title ?: optional($preEmployment->job)->title;

    $mailEyebrow = 'Submission Received';
    $mailTitle = 'We Received Your Pre-Employment Submission';
    $mailIntro = 'Your pre-employment information has been submitted successfully and is now under review by our team.';
    $mailBadgeText = 'Submission Confirmed';
    $mailButtonText = 'View Portal';
    $mailButtonUrl = $portalUrl ?? null;
    $mailFooter = 'Sada Fezzan Pre-Employment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Submission Details
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Name:</strong> {{ $candidateName }}
                </p>

                @if($jobTitle)
                    <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                        <strong>Position:</strong> {{ $jobTitle }}
                    </p>
                @endif

                @if($preEmployment->status ?? false)
                    <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                        <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $preEmployment->status)) }}
                    </p>
                @endif
            </td>
        </tr>
    </table>

    <p style="margin:20px 0 0 0;font-size:15px;line-height:1.85;color:#334155;">
        We will contact you if any clarification, correction, or additional document is required.
    </p>
@endsection
