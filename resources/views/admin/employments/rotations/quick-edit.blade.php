<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Rotation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: #0f172a;
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.12), transparent 28%),
                radial-gradient(circle at bottom left, rgba(36,89,211,.10), transparent 24%),
                #eef3f8;
            padding: 32px;
        }

        .sf-wrap {
            max-width: 1180px;
            margin: 0 auto;
        }

        .sf-hero {
            border-radius: 34px;
            padding: 28px;
            background:
                radial-gradient(circle at 92% 8%, rgba(76,167,168,.16), transparent 34%),
                linear-gradient(135deg, #18344d 0%, #234d6f 54%, #2f8a8d 100%);
            color: #fff;
            box-shadow: 0 24px 70px rgba(15,23,42,.20);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .sf-kicker {
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .18em;
            text-transform: uppercase;
            opacity: .82;
            margin-bottom: 10px;
        }

        .sf-title {
            font-size: 42px;
            line-height: 1;
            font-weight: 950;
            letter-spacing: -.055em;
        }

        .sf-sub {
            margin-top: 12px;
            font-size: 14px;
            line-height: 1.6;
            opacity: .84;
            font-weight: 750;
        }

        .sf-back {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.22);
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 950;
        }

        .sf-card {
            margin-top: 22px;
            border-radius: 34px;
            background: rgba(255,255,255,.94);
            border: 1px solid rgba(215,226,229,.95);
            box-shadow: 0 18px 48px rgba(15,23,42,.08);
            padding: 26px;
        }

        .sf-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .sf-field label {
            display: block;
            margin-bottom: 8px;
            color: #334155;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .sf-input,
        .sf-select,
        .sf-textarea {
            width: 100%;
            border-radius: 18px;
            border: 1px solid #d7e2e5;
            background: #fff;
            min-height: 50px;
            padding: 0 14px;
            color: #0f172a;
            font-size: 15px;
            font-weight: 750;
            outline: none;
        }

        .sf-textarea {
            min-height: 120px;
            padding: 14px;
            resize: vertical;
        }

        .sf-full { grid-column: 1 / -1; }

        .sf-files {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .sf-file-box {
            border-radius: 24px;
            border: 1px solid rgba(15,23,42,.08);
            background: #f8fbff;
            padding: 16px;
        }

        .sf-file-title {
            color: #0f172a;
            font-size: 16px;
            font-weight: 950;
            margin-bottom: 8px;
        }

        .sf-file-current {
            color: #64748b;
            font-size: 13px;
            line-height: 1.5;
            font-weight: 750;
            margin-bottom: 12px;
            word-break: break-word;
        }

        .sf-file-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .sf-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid rgba(59,130,246,.20);
            font-size: 12px;
            font-weight: 950;
            text-decoration: none;
        }

        .sf-footer {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sf-btn {
            border: 0;
            cursor: pointer;
            min-height: 48px;
            padding: 0 20px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 950;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sf-btn-primary {
            background: linear-gradient(90deg, #2563eb, #4f8cff);
            color: #fff;
            box-shadow: 0 14px 34px rgba(37,99,235,.22);
        }

        .sf-btn-soft {
            background: #fff;
            color: #0f172a;
            border: 1px solid #d7e2e5;
        }

        .sf-error {
            margin-bottom: 18px;
            border-radius: 20px;
            padding: 14px 16px;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            font-weight: 800;
            line-height: 1.5;
        }

        @media (max-width: 760px) {
            body { padding: 18px; }
            .sf-grid, .sf-files { grid-template-columns: 1fr; }
            .sf-title { font-size: 32px; }
        }
    </style>
</head>
<body>
    <main class="sf-wrap">
        <section class="sf-hero">
            <div>
                <div class="sf-kicker">Employment Operations</div>
                <div class="sf-title">Edit Rotation</div>
                <div class="sf-sub">
                    Employee: {{ $employment->employee_name ?: '-' }} · Code: {{ $employment->employee_code ?: '-' }}
                </div>
            </div>

            <a href="{{ url('/admin/employments/' . $employment->id) }}" class="sf-back">← Back to Employment</a>
        </section>

        <section class="sf-card">
            @if($errors->any())
                <div class="sf-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.employments.rotations.quick-update', ['employment' => $employment->id, 'rotation' => $rotation->id]) }}" enctype="multipart/form-data">
                @csrf

                <div class="sf-grid">
                    <div class="sf-field">
                        <label>Rotation Label</label>
                        <input class="sf-input" name="rotation_label" value="{{ old('rotation_label', $rotation->rotation_label) }}" placeholder="Example: 06">
                    </div>

                    <div class="sf-field">
                        <label>Rotation Pattern</label>
                        <input class="sf-input" name="rotation_pattern" value="{{ old('rotation_pattern', $rotation->rotation_pattern) }}" placeholder="28/28, 35/35 ...">
                    </div>

                    <div class="sf-field">
                        <label>Status</label>
                        <select class="sf-select" name="status">
                            @foreach(['scheduled' => 'Scheduled', 'active' => 'Active', 'completed' => 'Completed', 'paused' => 'Paused', 'cancelled' => 'Cancelled'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $rotation->status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sf-field">
                        <label>Travel Status</label>
                        <select class="sf-select" name="travel_status">
                            @foreach(['pending_request' => 'Pending Request', 'request_received' => 'Request Received', 'ticket_booked' => 'Ticket Booked', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('travel_status', $rotation->travel_status) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sf-field">
                        <label>Work Start Date</label>
                        <input class="sf-input" type="date" name="from_date" value="{{ old('from_date', optional($rotation->from_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="sf-field">
                        <label>Work End Date</label>
                        <input class="sf-input" type="date" name="to_date" value="{{ old('to_date', optional($rotation->to_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="sf-field">
                        <label>Mobilization Date</label>
                        <input class="sf-input" type="date" name="mobilization_date" value="{{ old('mobilization_date', optional($rotation->mobilization_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="sf-field">
                        <label>Demobilization Date</label>
                        <input class="sf-input" type="date" name="demobilization_date" value="{{ old('demobilization_date', optional($rotation->demobilization_date)->format('Y-m-d')) }}">
                    </div>

                    <div class="sf-field sf-full">
                        <label>Notes</label>
                        <textarea class="sf-textarea" name="notes">{{ old('notes', $rotation->notes) }}</textarea>
                    </div>
                </div>

                <div class="sf-files">
                    <div class="sf-file-box">
                        <div class="sf-file-title">Travel Request File</div>
                        <div class="sf-file-current">
                            Current: {{ $rotation->travel_request_file_path ?: 'No file uploaded yet' }}
                        </div>

                        @if($rotation->travel_request_file_path)
                            <div class="sf-file-actions">
                                <a class="sf-chip" target="_blank" href="{{ route('admin.employments.rotations.file', ['employment' => $employment->id, 'rotation' => $rotation->id, 'type' => 'travel-request']) }}">Open current</a>
                            </div>
                        @endif

                        <input class="sf-input" type="file" name="travel_request_file">
                    </div>

                    <div class="sf-file-box">
                        <div class="sf-file-title">Ticket File</div>
                        <div class="sf-file-current">
                            Current: {{ $rotation->ticket_file_path ?: 'No file uploaded yet' }}
                        </div>

                        @if($rotation->ticket_file_path)
                            <div class="sf-file-actions">
                                <a class="sf-chip" target="_blank" href="{{ route('admin.employments.rotations.file', ['employment' => $employment->id, 'rotation' => $rotation->id, 'type' => 'ticket']) }}">Open current</a>
                            </div>
                        @endif

                        <input class="sf-input" type="file" name="ticket_file">
                    </div>
                </div>

                <div class="sf-footer">
                    <a href="{{ url('/admin/employments/' . $employment->id) }}" class="sf-btn sf-btn-soft">Cancel</a>
                    <button type="submit" class="sf-btn sf-btn-primary">Save Rotation</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
