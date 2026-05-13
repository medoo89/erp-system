@php
    $employeeName = $portalAccount->full_name ?: 'Employee';

    $mailEyebrow = ucfirst(str_replace('_', ' ', $notification->category ?: 'Portal'));
    $mailTitle = $notification->title ?: 'Portal Notification';
    $mailIntro = 'Dear ' . $employeeName . ', you have a new update in your employee portal.';
    $mailBadgeText = 'Portal Update';
    $mailButtonText = $notification->action_label ?: 'Open Portal';
    $mailButtonUrl = $actionUrl ?? url('/portal/notifications');
    $mailFooter = 'Sada Fezzan ERP';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fbfdfe;border:1px solid #e2e8f0;border-radius:22px;">
        <tr>
            <td style="padding:22px;">
                <div style="font-size:12px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:#64748b;margin-bottom:14px;">
                    {{ ucfirst(str_replace('_', ' ', $notification->category ?: 'portal')) }}
                </div>

                <p style="margin:0;font-size:15px;line-height:1.85;color:#334155;font-weight:650;">
                    {{ $notification->message }}
                </p>
            </td>
        </tr>
    </table>
@endsection
