@php
    $candidateName = $preEmployment->candidate_name ?? optional($preEmployment->jobApplication)->full_name ?? 'Candidate';
    $jobTitle = optional($preEmployment->jobApplication?->job)->title ?: optional($preEmployment->job)->title;

    $mailEyebrow = 'Portal Access';
    $mailTitle = $isUpdateRequest ? 'Additional Pre-Employment Requirements' : 'Access Your Pre-Employment Portal';
    $mailIntro = $isUpdateRequest
        ? 'Please log in to your pre-employment portal and provide the additional requested information or documents.'
        : 'Your pre-employment portal is now ready. Please use the secure link below to review and submit the required information.';
    $mailBadgeText = 'Secure Candidate Portal';
    $mailButtonText = 'Open Portal';
    $mailButtonUrl = $portalUrl ?? null;
    $mailFooter = 'Sada Fezzan Pre-Employment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Pre-Employment Details
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Name:</strong> {{ $candidateName }}
                </p>

                @if($jobTitle)
                    <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                        <strong>Position:</strong> {{ $jobTitle }}
                    </p>
                @endif

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;word-break:break-all;">
                    <strong>Portal Link:</strong><br>
                    <a href="{{ $portalUrl }}" style="color:#0f766e;text-decoration:underline;">{{ $portalUrl }}</a>
                </p>
            </td>
        </tr>
    </table>
@endsection
