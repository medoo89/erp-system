@php
    /*
     | Unified Sada Fezzan premium email layout.
     | All recruitment, pre-employment, employee portal, salary, and system emails
     | should extend this layout so the visual style stays consistent.
     */

    $mailTitle = $mailTitle ?? 'Sada Fezzan Notification';
    $mailEyebrow = $mailEyebrow ?? 'Notification';
    $mailIntro = $mailIntro ?? null;
    $mailBadgeText = $mailBadgeText ?? null;
    $mailButtonText = $mailButtonText ?? null;
    $mailButtonUrl = $mailButtonUrl ?? null;
    $mailFooter = $mailFooter ?? 'Sada Fezzan for Oil Services';
    $mailBrandLine = $mailBrandLine ?? 'Employee & Candidate Self Portal';

    $sfLogoPath = public_path('images/sada-fezzan-logo.png');
    $sfLogoSrc = url('/images/sada-fezzan-logo.png');

    if (isset($message) && file_exists($sfLogoPath)) {
        try {
            $sfLogoSrc = $message->embed($sfLogoPath);
        } catch (\Throwable $e) {
            $sfLogoSrc = url('/images/sada-fezzan-logo.png');
        }
    }
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $mailTitle }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0;padding:0;background:#f3f7fb;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f3f7fb;margin:0;padding:34px 14px;">
        <tr>
            <td align="center" style="padding:0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:760px;width:100%;border-collapse:separate;border-spacing:0;">
                    <tr>
                        <td style="padding:0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;border-collapse:separate;border-spacing:0;">
                                <tr>
                                    <td style="border-radius:30px;overflow:hidden;background:linear-gradient(135deg,#18344d 0%,#234d6f 52%,#2f6f73 100%);padding:30px 32px;color:#ffffff;box-shadow:0 22px 55px rgba(15,23,42,.16);">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="104" valign="top" style="width:104px;padding:0 20px 0 0;">
                                                    <div style="width:82px;height:82px;border-radius:22px;background:#ffffff;text-align:center;box-shadow:0 14px 34px rgba(15,23,42,.18);overflow:hidden;">
                                                        <img src="{{ $sfLogoSrc }}" alt="Sada Fezzan" style="width:66px;height:66px;object-fit:contain;display:block;margin:8px auto 0;">
                                                    </div>
                                                </td>

                                                <td valign="middle" style="padding:0;vertical-align:middle;">
                                                    <div style="font-size:18px;line-height:1.1;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:#ffffff;">
                                                        Sada Fezzan
                                                    </div>
                                                    <div style="margin-top:8px;font-size:14px;line-height:1.35;font-weight:700;color:rgba(255,255,255,.78);">
                                                        {{ $mailBrandLine }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="margin-top:30px;display:inline-block;padding:8px 15px;border-radius:999px;border:1px solid rgba(255,255,255,.20);background:rgba(255,255,255,.10);font-size:12px;font-weight:900;letter-spacing:.16em;text-transform:uppercase;color:#dff7fb;">
                                            {{ $mailEyebrow }}
                                        </div>

                                        <h1 style="margin:18px 0 0 0;font-size:34px;line-height:1.1;font-weight:900;letter-spacing:-.045em;color:#ffffff;">
                                            {{ $mailTitle }}
                                        </h1>

                                        @if(!empty($mailIntro))
                                            <p style="margin:14px 0 0 0;font-size:15px;line-height:1.75;color:rgba(255,255,255,.90);font-weight:600;">
                                                {{ $mailIntro }}
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if(!empty($mailBadgeText))
                        <tr>
                            <td style="padding:18px 0 0 0;">
                                <span style="display:inline-block;border-radius:999px;background:#e6f6f6;border:1px solid #bfeaea;color:#078a8a;padding:9px 15px;font-size:12px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;">
                                    {{ $mailBadgeText }}
                                </span>
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td style="padding:18px 0 0 0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#ffffff;border:1px solid #dbe7eb;border-radius:26px;box-shadow:0 18px 45px rgba(15,23,42,.07);overflow:hidden;">
                                <tr>
                                    <td style="padding:28px 30px;">
                                        @yield('content')

                                        @if(!empty($mailButtonText) && !empty($mailButtonUrl))
                                            <div style="margin-top:26px;text-align:center;">
                                                <a href="{{ $mailButtonUrl }}" style="display:inline-block;background:linear-gradient(135deg,#0f766e 0%,#14b8a6 100%);color:#ffffff !important;text-decoration:none;padding:14px 24px;border-radius:999px;font-size:14px;font-weight:900;box-shadow:0 14px 28px rgba(20,184,166,.18);">
                                                    {{ $mailButtonText }}
                                                </a>
                                            </div>

                                            <div style="margin-top:14px;text-align:center;font-size:12px;line-height:1.65;color:#64748b;word-break:break-all;">
                                                {{ $mailButtonUrl }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:18px 10px 0 10px;">
                            <p style="margin:0;font-size:12px;line-height:1.8;color:#64748b;">
                                This is an automated notification from {{ $mailFooter }}.
                            </p>
                            <p style="margin:4px 0 0 0;font-size:12px;line-height:1.8;color:#94a3b8;">
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
