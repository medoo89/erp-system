<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Review</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="padding:32px 20px;">
        <div style="max-width:760px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:20px;overflow:hidden;">
            <div style="padding:28px 32px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                <h1 style="margin:0;font-size:30px;color:#0f172a;">Candidate Submission Received</h1>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0;font-size:16px;line-height:1.8;">
                    A candidate has submitted or updated their portal requirements.
                </p>

                <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:18px 20px;margin-bottom:22px;">
                    <div style="margin-bottom:10px;font-size:14px;color:#64748b;">Candidate</div>
                    <div style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:14px;">
                        {{ $preEmployment->candidate_name ?: '-' }}
                    </div>

                    <div style="margin-bottom:10px;font-size:14px;color:#64748b;">Position</div>
                    <div style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:14px;">
                        {{ $preEmployment->job?->title ?: '-' }}
                    </div>

                    <div style="margin-bottom:10px;font-size:14px;color:#64748b;">Portal Link</div>
                    <div style="font-size:15px;color:#1d4ed8;word-break:break-word;">
                        {{ $portalUrl }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>