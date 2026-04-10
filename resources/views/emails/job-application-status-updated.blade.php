@php
    $logoUrl = rtrim(config('app.url'), '/') . '/images/sada-horizontal.png';
    $jobTitle = optional($jobApplication->job)->title ?? '-';

    $statusConfig = match ($statusLabel) {
        'Under Review' => [
            'bg' => '#eff6ff',
            'border' => '#bfdbfe',
            'text' => '#1d4ed8',
            'title' => 'Application Under Review',
        ],
        'Client Submitted' => [
            'bg' => '#eef2ff',
            'border' => '#c7d2fe',
            'text' => '#4338ca',
            'title' => 'Submitted to Client',
        ],
        'Qualified' => [
            'bg' => '#f3f4f6',
            'border' => '#d1d5db',
            'text' => '#374151',
            'title' => 'Application Qualified',
        ],
        'Hired' => [
            'bg' => '#ecfdf5',
            'border' => '#a7f3d0',
            'text' => '#047857',
            'title' => 'Congratulations',
        ],
        default => [
            'bg' => '#f8fafc',
            'border' => '#e2e8f0',
            'text' => '#334155',
            'title' => 'Application Update',
        ],
    };
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f7fb; font-family: Arial, Helvetica, sans-serif; color:#1f2937;">
    <div style="max-width:680px; margin:0 auto; padding:32px 20px;">
        <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:18px; overflow:hidden; box-shadow:0 8px 24px rgba(15, 23, 42, 0.06);">
            <div style="padding:24px 28px; background:#f9fafb; border-bottom:1px solid #e5e7eb;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <img src="{{ $logoUrl }}" alt="Sada Fezzan" style="max-height:48px; display:block;">
                    <div>
                        <h2 style="margin:0; font-size:26px; color:#0f172a;">Sada Fezzan Recruitment</h2>
                        <p style="margin:6px 0 0 0; font-size:14px; color:#64748b;">{{ $statusConfig['title'] }}</p>
                    </div>
                </div>
            </div>

            <div style="padding:30px 28px;">
                <p style="margin:0 0 16px 0; font-size:17px; line-height:1.7;">
                    Dear {{ $jobApplication->full_name ?? 'Applicant' }},
                </p>

                <p style="margin:0 0 18px 0; font-size:16px; line-height:1.8;">
                    {{ $messageBody }}
                </p>

                <div style="margin:24px 0; padding:18px 20px; background:{{ $statusConfig['bg'] }}; border:1px solid {{ $statusConfig['border'] }}; border-radius:14px;">
                    <p style="margin:0 0 10px 0; font-size:14px; color:{{ $statusConfig['text'] }}; font-weight:700;">Status Summary</p>
                    <p style="margin:0 0 8px 0; font-size:15px;"><strong>Applicant:</strong> {{ $jobApplication->full_name ?? '-' }}</p>
                    <p style="margin:0 0 8px 0; font-size:15px;"><strong>Job:</strong> {{ $jobTitle }}</p>
                    <p style="margin:0; font-size:15px;"><strong>Status:</strong> {{ $statusLabel }}</p>
                </div>

                <div style="margin:18px 0 24px 0; padding:18px 20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px;">
                    <p style="margin:0 0 10px 0; font-size:14px; color:#64748b; font-weight:700;">Application Details</p>
                    <p style="margin:0 0 8px 0; font-size:15px;"><strong>Email:</strong> {{ $jobApplication->email ?? '-' }}</p>
                    <p style="margin:0; font-size:15px;"><strong>Reference:</strong> #{{ $jobApplication->id }}</p>
                </div>

                <p style="margin:0 0 16px 0; font-size:16px; line-height:1.8;">
                    Our recruitment team will continue to keep you informed of any next steps where applicable.
                </p>

                <p style="margin:28px 0 0 0; font-size:16px; line-height:1.8;">
                    Best regards,<br>
                    <strong>Sada Fezzan Recruitment Team</strong>
                </p>
            </div>
        </div>
    </div>
</body>
</html>