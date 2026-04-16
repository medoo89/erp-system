<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submission Received</title>
</head>
<body style="margin:0; padding:0; background:#f4f8fa; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <div style="max-width:700px; margin:30px auto; background:#ffffff; border:1px solid #dbe4ea; border-radius:20px; overflow:hidden;">
        <div style="padding:28px 30px; background:linear-gradient(135deg,#ffffff 0%,#f7fbfc 60%,#edf8f8 100%); border-bottom:1px solid #dbe4ea;">
            <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#16999a;">Submission Received</div>
            <h1 style="margin:14px 0 0 0; font-size:30px; line-height:1.1; color:#2c5377;">
                We received your response
            </h1>
        </div>

        <div style="padding:28px 30px;">
            <p style="font-size:16px; line-height:1.8; margin:0 0 18px 0;">
                Your response for the request <strong>{{ $candidateRequest->title ?: 'Candidate Request' }}</strong> has been received successfully.
            </p>

            <p style="font-size:16px; line-height:1.8; margin:0 0 22px 0;">
                Our recruitment team will review your submission and contact you if any further action is required.
            </p>

            <a href="{{ $portalUrl }}"
               style="display:inline-block; text-decoration:none; background:#26b6b7; color:#ffffff; padding:14px 22px; border-radius:14px; font-size:15px; font-weight:700;">
                Open Request Portal
            </a>
        </div>
    </div>
</body>
</html>