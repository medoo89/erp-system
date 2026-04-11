<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Received</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="padding:32px 20px;">
        <div style="max-width:760px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:20px;overflow:hidden;">
            <div style="padding:28px 32px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                <h1 style="margin:0;font-size:30px;color:#0f172a;">Submission Received</h1>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0;font-size:16px;line-height:1.8;">
                    Dear {{ $preEmployment->candidate_name ?: 'Candidate' }},
                </p>

                <p style="margin:0 0 20px 0;font-size:16px;line-height:1.8;color:#334155;">
                    Thank you for submitting your required documents and information.
                    We have received your submission and our team will review it.
                </p>

                <p style="margin:0 0 20px 0;font-size:16px;line-height:1.8;color:#334155;">
                    If additional updates are required, we will contact you.
                </p>

                <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:14px;padding:16px 18px;margin-bottom:22px;word-break:break-word;">
                    {{ $portalUrl }}
                </div>

                <p style="margin:24px 0 0 0;font-size:15px;line-height:1.8;color:#334155;">
                    Best regards,<br>
                    <strong>Sada Fezzan Recruitment Team</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>