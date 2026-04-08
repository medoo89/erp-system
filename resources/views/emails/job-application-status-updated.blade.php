<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f8fb; font-family: Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:640px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden;">
            <div style="padding:24px 28px; background:#f9fafb; border-bottom:1px solid #e5e7eb;">
                <h2 style="margin:0; font-size:24px; color:#111827;">Sada Fezzan Recruitment</h2>
            </div>

            <div style="padding:28px;">
                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.7;">
                    Dear {{ $jobApplication->full_name ?? 'Applicant' }},
                </p>

                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.7;">
                    {{ $messageBody }}
                </p>

                <div style="margin:24px 0; padding:18px 20px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px;">
                    <p style="margin:0 0 10px 0; font-size:14px; color:#6b7280;">Application Details</p>
                    <p style="margin:0 0 8px 0; font-size:15px;"><strong>Applicant:</strong> {{ $jobApplication->full_name ?? '-' }}</p>
                    <p style="margin:0 0 8px 0; font-size:15px;"><strong>Job:</strong> {{ optional($jobApplication->job)->title ?? '-' }}</p>
                    <p style="margin:0; font-size:15px;"><strong>Status:</strong> {{ $statusLabel }}</p>
                </div>

                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.7;">
                    Our team will keep you informed of the next steps where applicable.
                </p>

                <p style="margin:24px 0 0 0; font-size:16px; line-height:1.7;">
                    Best regards,<br>
                    <strong>Sada Fezzan Recruitment Team</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>