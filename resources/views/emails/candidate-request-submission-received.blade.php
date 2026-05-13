@php
    $mailEyebrow = 'Submission Received';
    $mailTitle = 'We received your response';
    $mailIntro = 'Your response has been received successfully and will be reviewed by our recruitment team.';
    $mailBadgeText = 'Submission Confirmed';
    $mailButtonText = 'Open Request Portal';
    $mailButtonUrl = $portalUrl ?? null;
    $mailFooter = 'Sada Fezzan Recruitment Team';
@endphp

@extends('emails.partials.premium-layout')

@section('content')
    <p style="margin:0 0 16px 0;font-size:16px;line-height:1.85;color:#0f172a;">
        Your response for the request <strong>{{ $candidateRequest->title ?: 'Candidate Request' }}</strong> has been received successfully.
    </p>

    <p style="margin:0;font-size:16px;line-height:1.85;color:#334155;">
        Our recruitment team will review your submission and contact you if any further action is required.
    </p>
@endsection
