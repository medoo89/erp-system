<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mailTitle ?? 'Sada Fezzan' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f4f8fa;font-family:Arial,Helvetica,sans-serif;color:#18212b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:linear-gradient(180deg,#f4f8fa 0%,#eef4f7 100%);margin:0;padding:24px 0;">
        <tr>
            <td align="center" style="padding:0 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:680px;background:#ffffff;border-radius:28px;overflow:hidden;border:1px solid #e6edf1;box-shadow:0 18px 50px rgba(25,41,61,0.08);">
                    <tr>
                        <td style="padding:0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:linear-gradient(135deg,#ffffff 0%,#f7fbfc 58%,#ecf7f7 100%);">
                                <tr>
                                    <td style="padding:34px 34px 26px 34px;">
                                        <div style="text-align:center;margin-bottom:18px;">
                                            @if(!empty($embeddedLogoCid))
                                                <img src="{{ $embeddedLogoCid }}" alt="Sada Fezzan" style="height:56px;max-width:220px;object-fit:contain;display:block;margin:0 auto 8px auto;">
                                            @endif

                                            <div style="font-size:13px;line-height:1.7;color:#6b7f90;font-weight:700;">
                                                Sada Fezzan for Oil Services
                                            </div>
                                        </div>

                                        <div style="display:inline-block;padding:8px 14px;border-radius:999px;border:1px solid rgba(38,182,183,0.20);background:#eefafa;color:#16999a;font-size:12px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;">
                                            {{ $mailEyebrow ?? 'Notification' }}
                                        </div>

                                        <h1 style="margin:16px 0 10px 0;font-size:34px;line-height:1.08;font-weight:800;color:#2c5377;">
                                            {{ $mailTitle ?? 'Sada Fezzan' }}
                                        </h1>

                                        @if (! empty($mailIntro))
                                            <p style="margin:0;font-size:16px;line-height:1.8;color:#6b7f90;">
                                                {{ $mailIntro }}
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if (! empty($mailBadgeText))
                        <tr>
                            <td style="padding:0 34px 0 34px;">
                                <div style="display:inline-block;background:#f4fbfb;color:{{ $mailAccent ?? '#26b6b7' }};border:1px solid #d7efef;border-radius:999px;padding:8px 14px;font-size:12px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;">
                                    {{ $mailBadgeText }}
                                </div>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:26px 34px 10px 34px;">
                            @yield('content')
                        </td>
                    </tr>

                    @if (! empty($mailButtonText) && ! empty($mailButtonUrl))
                        <tr>
                            <td style="padding:10px 34px 0 34px;">
                                <a href="{{ $mailButtonUrl }}" style="display:inline-block;background:linear-gradient(135deg,#26b6b7 0%,#39c7c8 100%);color:#ffffff;text-decoration:none;padding:14px 22px;border-radius:14px;font-size:14px;font-weight:700;">
                                    {{ $mailButtonText }}
                                </a>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:28px 34px 34px 34px;">
                            <div style="height:1px;background:#e8eef2;margin-bottom:18px;"></div>
                            <p style="margin:0 0 8px 0;font-size:13px;line-height:1.7;color:#6b7f90;">
                                This is an automated message from {{ $mailFooter ?? 'Sada Fezzan for Oil Services' }}.
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