<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Employment Portal</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="padding:32px 20px;">
        <div style="max-width:760px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:20px;overflow:hidden;">
            <div style="padding:28px 32px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                <h1 style="margin:0;font-size:30px;color:#0f172a;">Pre-Employment Portal</h1>
                <p style="margin:10px 0 0 0;color:#64748b;line-height:1.7;">
                    {{ $isUpdateRequest ? 'Additional information or documents are required.' : 'Please complete your pre-employment requirements.' }}
                </p>
            </div>

            <div style="padding:32px;">
                <p style="margin:0 0 18px 0;font-size:16px;line-height:1.8;">
                    Dear {{ $preEmployment->candidate_name ?: 'Candidate' }},
                </p>

                <p style="margin:0 0 20px 0;font-size:16px;line-height:1.8;color:#334155;">
                    @if($isUpdateRequest)
                        We have added or updated required information/documents for your profile. Please review the portal and upload the requested items.
                    @else
                        Please use the portal below to review and submit the required information and documents for your pre-employment process.
                    @endif
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

                    <div style="margin-bottom:10px;font-size:14px;color:#64748b;">Project / Client</div>
                    <div style="font-size:16px;font-weight:700;color:#0f172a;">
                        {{ $preEmployment->job?->project?->client?->name ?: '-' }}
                        @if($preEmployment->job?->project?->name)
                            / {{ $preEmployment->job->project->name }}
                        @endif
                    </div>
                </div>

                <div style="margin-bottom:26px;">
                    <a href="{{ $portalUrl }}" target="_blank" style="display:inline-block;background:#0f172a;color:#ffffff;text-decoration:none;padding:14px 22px;border-radius:12px;font-size:15px;font-weight:700;">
                        Open Pre-Employment Portal
                    </a>
                </div>

                <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:14px;padding:16px 18px;margin-bottom:22px;word-break:break-word;">
                    {{ $portalUrl }}
                </div>

                <p style="margin:0 0 12px 0;font-size:15px;line-height:1.8;color:#334155;">
                    If you have any questions, please contact our recruitment/operations team.
                </p>

                <p style="margin:24px 0 0 0;font-size:15px;line-height:1.8;color:#334155;">
                    Best regards,<br>
                    <strong>Sada Fezzan Recruitment Team</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>