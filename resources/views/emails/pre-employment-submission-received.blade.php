@include('emails.partials.premium-layout', [
    'mailEyebrow' => 'Submission Received',
    'mailTitle' => 'We Received Your Pre-Employment Submission',
    'mailIntro' => 'Your pre-employment information has been submitted successfully and is now under review by our team.',
    'mailBadgeText' => 'Submission Confirmed',
    'mailButtonText' => 'View Portal',
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

                            @if($preEmployment->status ?? false)
                                <p style="margin:0;font-size:15px;line-height:1.8;color:#18212b;">
                                    <strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $preEmployment->status)) }}
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>

                <div style="height:18px;"></div>

                <p style="margin:0;font-size:15px;line-height:1.9;color:#334155;">
                    We will contact you if any clarification, correction, or additional document is required.
                </p>
            </td>
        </tr>
    </table>
@endinclude