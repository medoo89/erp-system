@php
    $jobApplication = $candidateRequest->jobApplication;
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:separate;border-spacing:0;">
        <tr>
            <td>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:linear-gradient(180deg,#fbfdfe 0%,#f8fbfc 100%);border:1px solid #e3edf1;border-radius:24px;">
                    <tr>
                        <td style="padding:22px;">
                            <p style="margin:0 0 14px 0;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#7a8b98;">
                                Request Summary
                            </p>

                            <p style="margin:0 0 10px 0;font-size:16px;line-height:1.8;color:#18212b;">
                                <strong>Applicant:</strong> {{ $jobApplication?->full_name ?: '-' }}
                            </p>

                            <p style="margin:0 0 10px 0;font-size:16px;line-height:1.8;color:#18212b;">
                                <strong>Position:</strong> {{ $jobTitle ?? '-' }}
                            </p>

                            <p style="margin:0 0 10px 0;font-size:16px;line-height:1.8;color:#18212b;">
                                <strong>Request Title:</strong> {{ $candidateRequest->title ?: '-' }}
                            </p>

                            <p style="margin:0 0 10px 0;font-size:16px;line-height:1.8;color:#18212b;">
                                <strong>Request Type:</strong> {{ ucfirst(str_replace('_', ' ', (string) $candidateRequest->type)) }}
                            </p>

                            <p style="margin:0;font-size:16px;line-height:1.8;color:#18212b;">
                                <strong>Due Date:</strong> {{ $candidateRequest->due_date?->format('M j, Y') ?: '-' }}
                            </p>
                        </td>
                    </tr>
                </table>

                @if (filled($candidateRequest->notes))
                    <div style="height:18px;"></div>

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff;border:1px solid #e3edf1;border-radius:24px;">
                        <tr>
                            <td style="padding:22px;">
                                <p style="margin:0 0 10px 0;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#7a8b98;">
                                    Instructions
                                </p>

                                <p style="margin:0;font-size:16px;line-height:1.95;color:#334155;white-space:pre-line;">{{ $candidateRequest->notes }}</p>
                            </td>
                        </tr>
                    </table>
                @endif

                @if ($candidateRequest->items->count())
                    <div style="height:18px;"></div>

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff;border:1px solid #e3edf1;border-radius:24px;">
                        <tr>
                            <td style="padding:22px;">
                                <p style="margin:0 0 10px 0;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#7a8b98;">
                                    Requested Files
                                </p>

                                @foreach ($candidateRequest->items as $item)
                                    <p style="margin:0 0 10px 0;font-size:15px;line-height:1.85;color:#334155;">
                                        - {{ $item->label }}
                                        @if ($item->file_format)
                                            ({{ ucfirst(str_replace('_', ' ', $item->file_format)) }})
                                        @endif
                                        {!! $item->is_required ? '<strong>[Required]</strong>' : '[Optional]' !!}
                                        @if ($item->allow_multiple)
                                            [Multiple Allowed]
                                        @endif
                                    </p>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                @endif

                <div style="height:18px;"></div>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fbfc;border:1px solid #dcebf0;border-radius:24px;">
                    <tr>
                        <td style="padding:22px;">
                            <p style="margin:0 0 10px 0;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#7a8b98;">
                                Portal Link
                            </p>

                            <p style="margin:0 0 12px 0;font-size:15px;line-height:1.9;color:#334155;">
                                Use the button above to open your request portal.
                            </p>

                            <p style="margin:0;font-size:14px;line-height:1.9;color:#2c5377;word-break:break-all;">
                                <strong>Direct Link:</strong><br>
                                <a href="{{ $portalUrl }}" style="color:#16999a;text-decoration:underline;">{{ $portalUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection