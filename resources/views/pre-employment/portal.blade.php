<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Employment Portal</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .page {
            min-height: 100vh;
            padding: 32px 20px;
        }

        .container {
            max-width: 980px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .header {
            padding: 28px 32px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .header h1 {
            margin: 0;
            font-size: 2rem;
            color: #0f172a;
        }

        .header p {
            margin: 10px 0 0 0;
            color: #64748b;
            line-height: 1.7;
        }

        .body {
            padding: 32px;
        }

        .overview {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .overview-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px 18px;
        }

        .overview-item .label {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 8px;
        }

        .overview-item .value {
            font-size: 1rem;
            color: #0f172a;
            font-weight: 700;
            line-height: 1.6;
        }

        .success-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            border-radius: 14px;
            padding: 16px 18px;
            margin-bottom: 22px;
        }

        .field-block {
            margin-bottom: 22px;
            padding: 18px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #ffffff;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .required {
            color: #dc2626;
            font-weight: 800;
        }

        .instructions {
            margin-bottom: 12px;
            color: #64748b;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            background: #fff;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .existing-file {
            margin-top: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: 0.92rem;
        }

        .actions {
            margin-top: 30px;
        }

        .button {
            border: none;
            background: #0f172a;
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
        }

        .errors {
            margin-bottom: 22px;
            padding: 16px 18px;
            border-radius: 14px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .errors ul {
            margin: 0;
            padding-left: 18px;
        }

        @media (max-width: 768px) {
            .overview {
                grid-template-columns: 1fr;
            }

            .body,
            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="container">
            <div class="card">
                <div class="header">
                    <h1>Pre-Employment Portal</h1>
                    <p>
                        Please review the requested information and upload the required documents below.
                    </p>
                </div>

                <div class="body">
                    @if (session('success'))
                        <div class="success-box">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="errors">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="overview">
                        <div class="overview-item">
                            <div class="label">Candidate</div>
                            <div class="value">{{ $preEmployment->candidate_name ?: '-' }}</div>
                        </div>

                        <div class="overview-item">
                            <div class="label">Position</div>
                            <div class="value">{{ $preEmployment->job?->title ?: '-' }}</div>
                        </div>

                        <div class="overview-item">
                            <div class="label">Project</div>
                            <div class="value">{{ $preEmployment->job?->project?->name ?: '-' }}</div>
                        </div>

                        <div class="overview-item">
                            <div class="label">Client</div>
                            <div class="value">{{ $preEmployment->job?->project?->client?->name ?: '-' }}</div>
                        </div>
                    </div>

                    <form action="{{ route('pre-employment.portal.submit', $preEmployment->portal_token) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @forelse ($preEmployment->portalFields as $field)
                            @php
                                $existingValue = $values->get($field->id)?->value;
                                $inputName = 'field_' . $field->id;
                            @endphp

                            <div class="field-block">
                                <div class="field-label">
                                    <span>{{ $field->label }}</span>
                                    @if ($field->is_required)
                                        <span class="required">*</span>
                                    @endif
                                </div>

                                @if (filled($field->instructions))
                                    <div class="instructions">
                                        {{ $field->instructions }}
                                    </div>
                                @endif

                                @if ($field->field_type === 'textarea')
                                    <textarea name="{{ $inputName }}">{{ old($inputName, $existingValue) }}</textarea>

                                @elseif ($field->field_type === 'date')
                                    <input
                                        type="date"
                                        name="{{ $inputName }}"
                                        value="{{ old($inputName, $existingValue) }}"
                                    >

                                @elseif ($field->field_type === 'email')
                                    <input
                                        type="email"
                                        name="{{ $inputName }}"
                                        value="{{ old($inputName, $existingValue) }}"
                                    >

                                @elseif ($field->field_type === 'number')
                                    <input
                                        type="number"
                                        step="any"
                                        name="{{ $inputName }}"
                                        value="{{ old($inputName, $existingValue) }}"
                                    >

                                @elseif ($field->field_type === 'file')
                                    <input type="file" name="{{ $inputName }}">

                                    @if (filled($existingValue))
                                        <div class="existing-file">
                                            File already uploaded.
                                        </div>
                                    @endif

                                @else
                                    <input
                                        type="text"
                                        name="{{ $inputName }}"
                                        value="{{ old($inputName, $existingValue) }}"
                                    >
                                @endif
                            </div>
                        @empty
                            <div class="field-block">
                                No document requests or fields are currently available for this profile.
                            </div>
                        @endforelse

                        @if ($preEmployment->portalFields->count())
                            <div class="actions">
                                <button type="submit" class="button">Submit</button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>