@include('emails.partials.premium-layout', [
    'mailEyebrow' => 'Submission Review',
    'mailTitle' => 'Your Pre-Employment Submission Is Ready for Review',
    'mailIntro' => 'Your pre-employment submission has progressed to the review stage. Please keep your portal accessible in case additional updates are requested.',
    'mailBadgeText' => 'Review Stage',
    'mailButtonText' => 'Open Portal',
    'mailButtonUrl' => $portalUrl,
    'mailFooter' => 'Sada Fezzan Pre-Employment Team',
])
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fbfdfe;border:1px solid #e6edf1;border-radius:20px;">
                    <tr>
                        <td style="padding:20px;">
                            <p style="margin:0 0 10px 0;font-size:15px;line-height:1.8;color:#18212b;">
                                <strong>Name:</strong> {{ $preEmployment->candidate_name ?? optional($preEmployment->jobApplication)->full_name ?? 'Candidate' }}
                            </p>

                            @if(optional($preEmployment->jobApplication?->job)->title)
                                <p style="margin:0 0 10px 0;font-size:15px;line-height:1.8;color:#18212b;">
                                    <strong>Position:</strong> {{ optional($preEmployment->jobApplication?->job)->title }}
                                </p>
                            @endif

                            <p style="margin:0;font-size:15px;line-height:1.8;color:#18212b;">
                                <strong>Portal Link:</strong><br>
                                <a href="{{ $portalUrl }}" style="color:#26b6b7;word-break:break-all;">{{ $portalUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>

                <div style="height:18px;"></div>

                <p style="margin:0;font-size:15px;line-height:1.9;color:#334155;">
                    Our team is currently reviewing the submitted details. Please monitor your email for any follow-up request or next-step notification.
                </p>
            </td>
        </tr>
    </table>
@endinclude