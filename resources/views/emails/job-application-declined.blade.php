<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update on Your Job Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f4f8fa;font-family:Arial,Helvetica,sans-serif;color:#18212b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:linear-gradient(180deg,#f4f8fa 0%,#eef4f7 100%);margin:0;padding:24px 0;">
        <tr>
            <td align="center" style="padding:0 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:680px;background:#ffffff;border-radius:28px;overflow:hidden;border:1px solid #e6edf1;box-shadow:0 18px 50px rgba(25,41,61,0.08);">
                    <tr>
                        <td style="padding:0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:linear-gradient(135deg,#ffffff 0%,#fdf7f7 58%,#fff1f1 100%);">
                                <tr>
                                    <td style="padding:34px 34px 26px 34px;">
                                        <div style="text-align:center;margin-bottom:18px;">
                                            <img src="{{ rtrim(config('app.public_app_url'), '/') }}/images/sada-horizontal.png" alt="Sada Fezzan" style="height:56px;max-width:220px;object-fit:contain;">
                                        </div>

                                        <div style="display:inline-block;padding:8px 14px;border-radius:999px;border:1px solid #f3d2d2;background:#fff7f7;color:#c24141;font-size:12px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;">
                                            Application Update
                                        </div>

                                        <h1 style="margin:16px 0 10px 0;font-size:34px;line-height:1.08;font-weight:800;color:#2c5377;">
                                            Update on Your Job Application
                                        </h1>

                                        <p style="margin:0;font-size:16px;line-height:1.8;color:#6b7f90;">
                                            Thank you for your interest in Sada Fezzan. After reviewing your application, we would like to share the latest update with you.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 34px 0 34px;">
                            <div style="display:inline-block;background:#fff7f7;color:#c24141;border:1px solid #f3d2d2;border-radius:999px;padding:8px 14px;font-size:12px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;">
                                Application Status: Declined
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:26px 34px 10px 34px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fff7f7;border:1px solid #f3d2d2;border-radius:20px;">
                                <tr>
                                    <td style="padding:20px;">
                                        <p style="margin:0 0 10px 0;font-size:15px;line-height:1.8;color:#18212b;">
                                            <strong>Applicant:</strong> {{ $jobApplication->full_name }}
                                        </p>

                                        @if(optional($jobApplication->job)->title)
                                            <p style="margin:0 0 10px 0;font-size:15px;line-height:1.8;color:#18212b;">
                                                <strong>Position:</strong> {{ optional($jobApplication->job)->title }}
                                            </p>
                                        @endif

                                        <p style="margin:0;font-size:15px;line-height:1.8;color:#18212b;">
                                            <strong>Reason:</strong> {{ $declineReasonLabel }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            @if($declineNotes)
                                <div style="height:18px;"></div>

                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff;border:1px solid #e6edf1;border-radius:20px;">
                                    <tr>
                                        <td style="padding:20px;">
                                            <p style="margin:0 0 8px 0;font-size:13px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#6b7f90;">
                                                Additional Notes
                                            </p>
                                            <p style="margin:0;font-size:15px;line-height:1.95;color:#334155;white-space:pre-line;">{{ $declineNotes }}</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <div style="height:18px;"></div>

                            <p style="margin:0;font-size:15px;line-height:1.9;color:#334155;">
                                We sincerely appreciate the time and effort you invested in your application, and we wish you success in your future career opportunities.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 34px 34px 34px;">
                            <div style="height:1px;background:#e8eef2;margin-bottom:18px;"></div>
                            <p style="margin:0 0 8px 0;font-size:13px;line-height:1.7;color:#6b7f90;">
                                This is an automated message from Sada Fezzan Recruitment Team.
                            </p>
                            <p style="margin:0;font-size:12px;line-height:1.7;color:#91a1ae;">
                                Please do not reply to this email unless instructed otherwise.
                            </p>
                        </td>
                    </tr>
                </table>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:680px;">
                    <tr>
                        <td align="center" style="padding:14px 10px 0 10px;">
                            <p style="margin:0;font-size:12px;color:#94a3b0;line-height:1.7;">
                                © {{ now()->year }} Sada Fezzan for Oil Services. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>