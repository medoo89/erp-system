@php
    $jobApplication = $candidateRequest->jobApplication;
    $mailEyebrow = $mailEyebrow ?? 'Candidate Request';
    $mailTitle = $mailTitle ?? 'New Request for Your Job Application';
    $mailIntro = $mailIntro ?? 'Please review the request details below and use the portal link to respond or upload the requested files.';
    $mailBadgeText = $mailBadgeText ?? 'Portal Access Required';
    $mailButtonText = $mailButtonText ?? 'Open Request Portal';
    $mailButtonUrl = $mailButtonUrl ?? $portalUrl ?? null;
    $mailFooter = $mailFooter ?? 'Sada Fezzan Recruitment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Request Summary
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Applicant:</strong> {{ $jobApplication?->full_name ?: '-' }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Position:</strong> {{ $jobTitle ?? '-' }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Request Title:</strong> {{ $candidateRequest->title ?: '-' }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Request Type:</strong> {{ ucfirst(str_replace('_', ' ', (string) $candidateRequest->type)) }}
                </p>

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Due Date:</strong> {{ $candidateRequest->due_date?->format('M j, Y') ?: '-' }}
                </p>
            </td>
        </tr>
    </table>

    @if(filled($candidateRequest->notes))
        <div style="height:18px;"></div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:22px;">
            <tr>
                <td style="padding:22px;">
                    <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:12px;">
                        Instructions
                    </div>
                    <p style="margin:0;font-size:15px;line-height:1.9;color:#334155;white-space:pre-line;">{{ $candidateRequest->notes }}</p>
                </td>
            </tr>
        </table>
    @endif

    @if($candidateRequest->items->count())
        <div style="height:18px;"></div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:22px;">
            <tr>
                <td style="padding:22px;">
                    <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:12px;">
                        Requested Files
                    </div>

                    @foreach($candidateRequest->items as $item)
                        <p style="margin:0 0 10px 0;font-size:15px;line-height:1.85;color:#334155;">
                            - {{ $item->label }}
                            @if($item->file_format)
                                ({{ ucfirst(str_replace('_', ' ', $item->file_format)) }})
                            @endif
                            {!! $item->is_required ? '<strong>[Required]</strong>' : '[Optional]' !!}
                            @if($item->allow_multiple)
                                [Multiple Allowed]
                            @endif
                        </p>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif
@endsection
