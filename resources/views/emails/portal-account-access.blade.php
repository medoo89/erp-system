@php
    $employeeName = $employment->employee_name ?: $portalAccount->full_name ?: 'Employee';
    $title = $mailType === 'reset' ? 'Set a new portal password' : 'Set up your employee portal password';

    $mailEyebrow = 'Portal Access';
    $mailTitle = $title;
    $mailIntro = 'Dear ' . $employeeName . ', please use the secure link below to create your own portal password.';
    $mailBadgeText = 'Secure Employee Portal';
    $mailButtonText = 'Set My Portal Password';
    $mailButtonUrl = $setupUrl ?? null;
    $mailFooter = 'Sada Fezzan ERP';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    Portal Access Details
                </div>

                <p style="margin:0 0 10px 0;font-size:16px;line-height:1.75;color:#0f172a;">
                    <strong>Portal Email:</strong> {{ $portalAccount->email }}
                </p>

                <p style="margin:0;font-size:16px;line-height:1.75;color:#0f172a;word-break:break-all;">
                    <strong>Portal Login:</strong><br>
                    <a href="{{ $portalLoginUrl }}" style="color:#0f766e;text-decoration:underline;">{{ $portalLoginUrl }}</a>
                </p>
            </td>
        </tr>
    </table>

    <div style="margin-top:18px;padding:16px;border-radius:20px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:13px;line-height:1.7;font-weight:700;">
        This password setup link is valid for 24 hours. If it expires, please request a new setup email from the company.
    </div>
@endsection
