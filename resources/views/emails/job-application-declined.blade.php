@php
    $logoUrl = rtrim(config('app.url'), '/') . '/images/sada-horizontal.png';
    $jobTitle = optional($jobApplication->job)->title ?? '-';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update on Your Job Application</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7fb; font-family: Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:18px; overflow:hidden; box-shadow:0 8px 24px rgba(15, 23, 42, 0.06);">
            <div style="padding:24px 28px; background:#f9fafb; border-bottom:1px solid #e5e7eb;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <img src="{{ $logoUrl }}" alt="Sada Fezzan" style="max-height:48px; display:block;">
                    <div>
                        <h2 style="margin:0; font-size:26px; color:#0f172a;">Sada Fezzan Recruitment</h2>
                        <p style="margin:6px 0 0 0; font-size:14px; color:#64748b;">Application Update</p>
                    </div>
                </div>
            </div>

            <div style="padding:30px 28px;">
                <p style="margin:0 0 16px 0; font-size:17px; line-height:1.7;">
                    Dear {{ $jobApplication->full_name ?? 'Applicant' }},
                </p>

                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.8;">
                    Thank you for your interest in the position of <strong>{{ $jobTitle }}</strong>.
                </p>

                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.8;">
                    After review, we regret to inform you that your application has not been moved forward at this stage.
                </p>

                <div style="margin:24px 0; padding:18px 20px; background:#fef2f2; border:1px solid #fecaca; border-radius:14px;">
                    <p style="margin:0 0 10px 0; font-size:14px; color:#b91c1c; font-weight:700;">Decline Summary</p>
                    <p style="margin:0 0 8px 0; font-size:15px;"><strong>Job:</strong> {{ $jobTitle }}</p>
                    <p style="margin:0 0 8px 0; font-size:15px;"><strong>Status:</strong> Declined</p>
                    <p style="margin:0; font-size:15px;"><strong>Reason:</strong> {{ $declineReasonLabel }}</p>
                </div>

                @if (filled($declineNotes))
                    <div style="margin:18px 0 24px 0; padding:18px 20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px;">
                        <p style="margin:0 0 10px 0; font-size:14px; color:#64748b; font-weight:700;">Additional Notes</p>
                        <p style="margin:0; font-size:15px; line-height:1.8;">{{ $declineNotes }}</p>
                    </div>
                @endif

                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.8;">
                    We appreciate the time and effort you invested in your application and wish you success in your future opportunities.
                </p>

                <p style="margin:28px 0 0 0; font-size:16px; line-height:1.8;">
                    Best regards,<br>
                    <strong>Sada Fezzan Recruitment Team</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>