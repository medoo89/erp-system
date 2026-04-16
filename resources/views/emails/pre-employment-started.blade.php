@include('emails.partials.premium-layout', [
    'mailEyebrow' => 'Pre-Employment',
    'mailTitle' => 'Your Pre-Employment Process Has Started',
    'mailIntro' => 'Your application has moved to the pre-employment stage. Please review the details below and wait for the next instructions from our team.',
    'mailBadgeText' => optional($preEmployment->jobApplication?->job)->title ? 'Position: ' . optional($preEmployment->jobApplication?->job)->title : 'Pre-Employment Stage',
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
                    Our team will contact you if documents, confirmations, or additional actions are needed from your side.
                </p>
            </td>
        </tr>
    </table>
@endinclude