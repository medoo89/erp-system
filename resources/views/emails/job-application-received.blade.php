@php
    $candidateName = $application->full_name ?? $application->name ?? $application->candidate_name ?? 'Candidate';
    $candidateEmail = $application->email ?? $application->candidate_email ?? null;
    $candidatePhone = $application->phone ?? $application->phone_number ?? $application->mobile ?? null;

    $mailEyebrow = 'Application Received';
    $mailTitle = 'Thank you, ' . $candidateName;
    $mailIntro = 'We have received your application for ' . ($jobTitle ?? 'Job Application') . '. Our team will review your application and contact you shortly.';
    $mailBadgeText = 'Position: ' . ($jobTitle ?? 'Job Application');
    $mailFooter = 'Sada Fezzan Recruitment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Submitted Information
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Name:</strong> {{ $candidateName }}
                </p>

                @if($candidateEmail)
                    <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                        <strong>Email:</strong> {{ $candidateEmail }}
                    </p>
                @endif

                @if($candidatePhone)
                    <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                        <strong>Phone:</strong> {{ $candidatePhone }}
                    </p>
                @endif

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Position:</strong> {{ $jobTitle ?? 'Job Application' }}
                </p>
            </td>
        </tr>
    </table>

    @if(!empty($answers))
        <div style="height:18px;"></div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:22px;">
            <tr>
                <td style="padding:22px;">
                    <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                        Application Answers
                    </div>

                    @foreach($answers as $label => $value)
                        @continue(blank($value))
                        <div style="padding:12px 0;border-bottom:1px solid #eef3f6;">
                            <div style="font-size:12px;color:#64748b;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">
                                {{ $label }}
                            </div>
                            <div style="font-size:15px;color:#0f172a;line-height:1.7;font-weight:650;">
                                @if(is_array($value))
                                    {{ implode(', ', array_filter($value)) }}
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif
@endsection
