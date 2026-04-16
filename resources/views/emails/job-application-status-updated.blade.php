<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#edf3f6;font-family:Arial,Helvetica,sans-serif;color:#18212b;">
    @php
        $logoUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/images/sada-horizontal.png';
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0;padding:28px 0;background:
        radial-gradient(circle at top left, rgba(38,182,183,0.08), transparent 24%),
        radial-gradient(circle at top right, rgba(44,83,119,0.08), transparent 30%),
        linear-gradient(180deg,#f4f8fa 0%,#edf3f6 100%);
    ">
        <tr>
            <td align="center" style="padding:0 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:720px;border-collapse:separate;border-spacing:0;background:#ffffff;border:1px solid #dfe8ed;border-radius:32px;overflow:hidden;box-shadow:0 22px 55px rgba(25,41,61,0.10);">
                    
                    <tr>
                        <td style="padding:0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:
                                radial-gradient(circle at top right, rgba(38,182,183,0.10), transparent 28%),
                                linear-gradient(135deg,#ffffff 0%,#f7fbfc 52%,#eaf6f7 100%);
                            ">
                                <tr>
                                    <td style="padding:34px 34px 28px 34px;">

                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td align="center" style="padding:0 0 18px 0;">
                                                    <div style="display:inline-block;padding:14px 20px;border-radius:22px;background:rgba(255,255,255,0.82);border:1px solid rgba(44,83,119,0.08);box-shadow:0 12px 28px rgba(25,41,61,0.06);">
                                                        <img src="{{ $logoUrl }}" alt="Sada Fezzan" style="display:block;height:58px;max-width:240px;width:auto;object-fit:contain;">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="display:inline-block;padding:10px 16px;border-radius:999px;border:1px solid rgba(38,182,183,0.20);background:#eefafa;color:#16999a;font-size:12px;font-weight:700;letter-spacing:1.7px;text-transform:uppercase;">
                                            Application Status Update
                                        </div>

                                        <h1 style="margin:18px 0 12px 0;font-size:37px;line-height:1.05;font-weight:800;letter-spacing:-0.02em;color:#2c5377;">
                                            {{ $subjectLine }}
                                        </h1>

                                        <p style="margin:0;max-width:560px;font-size:17px;line-height:1.85;color:#648095;">
                                            Your application status has been updated by the Sada Fezzan recruitment team.
                                        </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 34px;">
                            <div style="display:inline-block;margin-top:2px;background:#f3fbfb;color:#19a5a6;border:1px solid #d7efef;border-radius:999px;padding:10px 16px;font-size:12px;font-weight:800;letter-spacing:0.9px;text-transform:uppercase;">
                                Current Status: {{ $statusLabel }}
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px 34px 10px 34px;">

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:linear-gradient(180deg,#fbfdfe 0%,#f8fbfc 100%);border:1px solid #e3edf1;border-radius:24px;">
                                <tr>
                                    <td style="padding:22px 22px 18px 22px;">
                                        <p style="margin:0 0 14px 0;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#7a8b98;">
                                            Applicant Summary
                                        </p>

                                        <p style="margin:0 0 12px 0;font-size:16px;line-height:1.8;color:#18212b;">
                                            <strong>Applicant:</strong> {{ $jobApplication->full_name }}
                                        </p>

                                        @if(optional($jobApplication->job)->title)
                                            <p style="margin:0 0 12px 0;font-size:16px;line-height:1.8;color:#18212b;">
                                                <strong>Position:</strong> {{ optional($jobApplication->job)->title }}
                                            </p>
                                        @endif

                                        <p style="margin:0;font-size:16px;line-height:1.8;color:#18212b;">
                                            <strong>Status:</strong> {{ $statusLabel }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <div style="height:18px;"></div>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#ffffff;border:1px solid #e3edf1;border-radius:24px;">
                                <tr>
                                    <td style="padding:22px;">
                                        <p style="margin:0 0 10px 0;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#7a8b98;">
                                            Message
                                        </p>

                                        <p style="margin:0;font-size:16px;line-height:1.95;color:#334155;white-space:pre-line;">{{ $messageBody }}</p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 34px 36px 34px;">
                            <div style="height:1px;background:#e7eef2;margin-bottom:18px;"></div>

                            <p style="margin:0 0 8px 0;font-size:13px;line-height:1.8;color:#6b7f90;">
                                This is an automated message from <strong>Sada Fezzan Recruitment Team</strong>.
                            </p>

                            <p style="margin:0;font-size:12px;line-height:1.8;color:#91a1ae;">
                                Please do not reply to this email unless instructed otherwise.
                            </p>
                        </td>
                    </tr>
                </table>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:720px;">
                    <tr>
                        <td align="center" style="padding:14px 10px 0 10px;">
                            <p style="margin:0;font-size:12px;color:#8da0ae;line-height:1.7;">
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