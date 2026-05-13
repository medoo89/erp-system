@php
    $jobTitle = optional($jobApplication?->job)->title ?: 'Job Application';
    $mailEyebrow = 'Application Received';
    $mailTitle = 'Your Application Has Been Received';
    $mailIntro = 'Thank you for applying to Sada Fezzan. We have successfully received your job application and our recruitment team will review it shortly.';
    $mailBadgeText = 'Position: ' . $jobTitle;
    $mailFooter = 'Sada Fezzan Recruitment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Applicant Details
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Full Name:</strong> {{ $jobApplication->full_name ?? 'Applicant' }}
                </p>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Email:</strong> {{ $jobApplication->email ?? '-' }}
                </p>

                @if(!empty($jobApplication->phone))
                    <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                        <strong>Phone:</strong> {{ $jobApplication->phone }}
                    </p>
                @endif

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Position:</strong> {{ $jobTitle }}
                </p>
            </td>
        </tr>
    </table>

    @if(!empty($applicationAnswers))
        <div style="height:18px;"></div>

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:22px;">
            <tr>
                <td style="padding:22px;">
                    <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                        Submitted Information
                    </div>

                    @foreach($applicationAnswers as $answer)
                        <div style="padding:12px 0;border-bottom:1px solid #eef3f6;">
                            <div style="font-size:12px;color:#64748b;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">
                                {{ $answer['label'] ?? '-' }}
                            </div>
                            <div style="font-size:15px;color:#0f172a;line-height:1.7;font-weight:650;">
                                {{ $answer['value'] ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:20px 0 0 0;font-size:15px;line-height:1.85;color:#334155;">
        If your qualifications match the role requirements, our team will contact you with the next steps.
    </p>
@endsection
