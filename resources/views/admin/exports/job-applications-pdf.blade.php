<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Applications Export</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --sf-primary: #2c5377;
            --sf-accent: #26b6b7;
            --sf-text: #1f2937;
            --sf-muted: #6b7280;
            --sf-border: #dbe4ea;
            --sf-bg: #f4f8fa;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--sf-bg);
            color: var(--sf-text);
        }

        .page {
            max-width: 1400px;
            margin: 0 auto;
            padding: 28px 20px 40px;
        }

        .topbar {
            background: #fff;
            border: 1px solid var(--sf-border);
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .brand-left img {
            height: 54px;
            object-fit: contain;
        }

        .title {
            margin: 14px 0 6px;
            font-size: 30px;
            font-weight: 800;
            color: var(--sf-primary);
        }

        .subtitle {
            margin: 0;
            color: var(--sf-muted);
            line-height: 1.7;
        }

        .meta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(38, 182, 183, 0.08);
            border: 1px solid rgba(38, 182, 183, 0.18);
            color: #0f766e;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-print {
            background: var(--sf-accent);
            color: #fff;
        }

        .btn-close {
            background: #fff;
            color: var(--sf-primary);
            border: 1px solid var(--sf-border);
        }

        .table-wrap {
            background: #fff;
            border: 1px solid var(--sf-border);
            border-radius: 18px;
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1600px;
        }

        thead th {
            background: #eef7f8;
            color: var(--sf-primary);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 12px 10px;
            border-bottom: 1px solid var(--sf-border);
            border-right: 1px solid var(--sf-border);
            text-align: left;
            vertical-align: top;
        }

        tbody td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
            border-right: 1px solid #edf2f7;
            font-size: 13px;
            line-height: 1.6;
            vertical-align: top;
        }

        tbody tr:nth-child(even) {
            background: #fcfeff;
        }

        a {
            color: #0f766e;
            text-decoration: none;
            font-weight: 700;
        }

        .muted {
            color: var(--sf-muted);
        }

        @media print {
            body {
                background: #fff;
            }

            .page {
                max-width: 100%;
                padding: 0;
            }

            .topbar {
                border: none;
                border-radius: 0;
                padding: 0 0 18px 0;
                margin-bottom: 12px;
            }

            .actions {
                display: none !important;
            }

            .table-wrap {
                border: none;
                border-radius: 0;
                overflow: visible;
            }

            table {
                min-width: 0;
                width: 100%;
            }

            thead th,
            tbody td {
                font-size: 10px;
                padding: 6px;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="topbar">
        <div class="brand">
            <div class="brand-left">
                <img src="{{ rtrim(config('app.public_app_url'), '/') }}/images/sada-horizontal.png" alt="Sada Fezzan">
                <h1 class="title">Job Applications Export</h1>
                <p class="subtitle">
                    Full applications report including applicant details, dynamic fields, notes, and CV links.
                </p>
            </div>

            <div class="meta">
                Total Records: {{ $applications->count() }}
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-print" onclick="window.print()">Print / Save as PDF</button>
            <button class="btn btn-close" onclick="window.close()">Close</button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>WhatsApp Number</th>
                <th>Position</th>
                <th>Nationality</th>
                <th>Project</th>
                <th>Client</th>
                <th>Status</th>
                <th>Years of Experience</th>
                <th>Applied At</th>
                <th>Notes</th>
                <th>Decline Reason</th>
                <th>Decline Notes</th>
                <th>CV Link</th>
                @foreach($dynamicFieldLabels as $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($applications as $application)
                @php
                    $cvValue = $application->values->first(function ($value) {
                        return ($value->field->field_key ?? null) === 'cv_file' && filled($value->value);
                    });

                    $cvLink = filled($application->cv_path)
                        ? route('job-applications.open-cv', $application)
                        : (filled($cvValue?->value) ? route('job-applications.open-cv', $application) : '');
                @endphp
                <tr>
                    <td>{{ $application->full_name }}</td>
                    <td>{{ $application->email }}</td>
                    <td>{{ $application->phone }}</td>
                    <td>{{ $application->whatsapp_number }}</td>
                    <td>{{ optional($application->job)->title }}</td>
                    <td>{{ $resolveNationality($application) }}</td>
                    <td>{{ optional($application->job?->project)->name }}</td>
                    <td>{{ optional($application->job?->project?->client)->name }}</td>
                    <td>{{ $application->status }}</td>
                    <td>{{ $resolveYearsOfExperience($application) }}</td>
                    <td>{{ optional($application->created_at)?->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $application->notes }}</td>
                    <td>{{ $application->decline_reason }}</td>
                    <td>{{ $application->decline_notes }}</td>
                    <td>
                        @if($cvLink)
                            <a href="{{ $cvLink }}" target="_blank">Open CV</a>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>

                    @foreach($dynamicFieldLabels as $label)
                        @php
                            $fieldValue = $application->values->first(function ($value) use ($label) {
                                return ($value->field->label ?? null) === $label;
                            });

                            $fieldType = $fieldValue->field->field_type ?? null;
                            $fieldKey = $fieldValue->field->field_key ?? null;
                            $rawValue = $fieldValue->value ?? '';
                        @endphp

                        <td>
                            @if($fieldType === 'file' && filled($rawValue))
                                @php
                                    $documentUrl = $fieldKey === 'cv_file'
                                        ? route('job-applications.open-cv', $application)
                                        : asset('storage/' . ltrim($rawValue, '/'));
                                @endphp
                                <a href="{{ $documentUrl }}" target="_blank">
                                    {{ $fieldKey === 'cv_file' ? 'Open CV' : 'Open File' }}
                                </a>
                            @else
                                {{ is_array($rawValue) ? implode(', ', $rawValue) : (string) $rawValue }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>