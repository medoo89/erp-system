<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Request Portal</title>
    <style>
        * { box-sizing: border-box; }

        :root {
            --bg-1: #f3f7fb;
            --bg-2: #eef5f8;
            --card: #ffffff;
            --line: #dbe4ea;
            --line-soft: #e6eef3;
            --text: #0f172a;
            --muted: #64748b;
            --label: #7a8b98;
            --teal: #16999a;
            --teal-soft: #eefafa;
            --blue: #2563eb;
            --blue-soft: #eff6ff;
            --green: #16a34a;
            --green-soft: #ecfdf5;
            --red: #dc2626;
            --red-soft: #fef2f2;
            --amber: #d97706;
            --amber-soft: #fff7ed;
            --yellow-soft: #fff8db;
            --yellow-line: #f3d36b;
            --shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(180deg, var(--bg-1) 0%, var(--bg-2) 100%);
            color: var(--text);
        }

        .wrap {
            max-width: 1140px;
            margin: 36px auto;
            padding: 0 18px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .header {
            padding: 30px 32px;
            background: linear-gradient(135deg, #ffffff 0%, #f7fbfc 58%, #edf8f8 100%);
            border-bottom: 1px solid var(--line);
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 18px;
        }

        .logo-wrap img {
            max-width: 220px;
            max-height: 74px;
            object-fit: contain;
            display: inline-block;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 9px 15px;
            border-radius: 999px;
            background: var(--teal-soft);
            color: var(--teal);
            border: 1px solid rgba(38, 182, 183, 0.25);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        h1 {
            margin: 16px 0 0 0;
            font-size: 42px;
            line-height: 1.04;
            color: #2c5377;
            letter-spacing: -0.03em;
        }

        .body {
            padding: 28px 32px 34px;
        }

        .success {
            background: var(--green-soft);
            border: 1px solid #a7f3d0;
            color: #047857;
            padding: 14px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
            font-weight: 700;
        }

        .error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            padding: 10px 12px;
            border-radius: 12px;
            margin-top: 8px;
            font-size: 14px;
            font-weight: 700;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(300px, 0.8fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .panel {
            background: #f8fbfc;
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 20px;
        }

        .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            color: var(--label);
            font-weight: 800;
            margin-bottom: 8px;
        }

        .value {
            font-size: 16px;
            line-height: 1.8;
            color: #334155;
            font-weight: 700;
        }

        .status-hero {
            margin-bottom: 20px;
            border-radius: 24px;
            padding: 22px 24px;
            border: 1px solid #cbd5e1;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbfc 100%);
        }

        .status-hero.closed-approved {
            border-color: #86efac;
            background: linear-gradient(135deg, #ecfdf5 0%, #ffffff 100%);
        }

        .status-hero.closed-declined {
            border-color: #fca5a5;
            background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
        }

        .status-hero-title {
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            margin-bottom: 10px;
            color: #64748b;
        }

        .status-hero-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .status-hero-heading {
            font-size: 28px;
            line-height: 1.1;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.03em;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 900;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
        }

        .status-pill.accepted {
            background: #ecfdf5;
            border-color: #86efac;
            color: #047857;
        }

        .status-pill.declined {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .status-pill.closed {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #475569;
        }

        .hero-offer {
            margin-top: 18px;
            border: 1px solid var(--yellow-line);
            background: linear-gradient(135deg, #fffdf3 0%, #fff6cf 100%);
            border-radius: 24px;
            padding: 22px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.5);
        }

        .hero-offer-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: #a16207;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .hero-offer-amount {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 20px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #fcd34d;
            color: #92400e;
            font-size: 24px;
            font-weight: 900;
            box-shadow: 0 8px 20px rgba(250, 204, 21, 0.12);
        }

        .hero-offer-note {
            margin-top: 12px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.8;
            font-weight: 600;
            white-space: pre-line;
        }

        .timeline-card {
            background: #f8fbfc;
            border: 1px solid var(--line);
            border-radius: 24px;
            padding: 22px;
            margin-bottom: 22px;
        }

        .timeline-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.16em;
            margin-bottom: 18px;
        }

        .timeline {
            position: relative;
            padding-left: 42px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .timeline-line {
            position: absolute;
            left: 15px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: var(--line);
        }

        .timeline-item {
            position: relative;
        }

        .timeline-dot {
            position: absolute;
            left: -31px;
            top: 24px;
            width: 16px;
            height: 16px;
            border-radius: 999px;
        }

        .timeline-dot.hr {
            background: #14b8a6;
            box-shadow: 0 0 0 5px rgba(20,184,166,0.15);
        }

        .timeline-dot.candidate {
            background: #3b82f6;
            box-shadow: 0 0 0 5px rgba(59,130,246,0.15);
        }

        .bubble {
            border-radius: 22px;
            padding: 20px;
            box-shadow: 0 8px 24px rgba(15,23,42,0.04);
        }

        .bubble.hr {
            border: 1px solid #99f6e4;
            background: linear-gradient(135deg, #f0fdfa 0%, #ecfeff 100%);
        }

        .bubble.candidate {
            border: 1px solid #bfdbfe;
            background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        }

        .bubble-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .bubble-role {
            font-size: 15px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .bubble.hr .bubble-role { color: #0f766e; }
        .bubble.candidate .bubble-role { color: #1d4ed8; }

        .bubble-date {
            font-size: 13px;
            font-weight: 800;
            color: var(--muted);
            white-space: nowrap;
        }

        .bubble-title {
            margin-top: 14px;
            font-size: 24px;
            line-height: 1.15;
            font-weight: 900;
            color: var(--text);
            letter-spacing: -0.03em;
        }

        .bubble-message {
            margin-top: 14px;
            font-size: 15px;
            line-height: 1.85;
            color: #334155;
            font-weight: 600;
            white-space: pre-line;
        }

        .tag-row {
            margin-top: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            padding: 10px 16px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: var(--text);
            font-size: 14px;
            font-weight: 900;
            box-shadow: 0 4px 14px rgba(15,23,42,0.04);
        }

        .tag.decision-approve {
            background: var(--green-soft);
            color: #047857;
            border-color: #86efac;
        }

        .tag.decision-decline {
            background: var(--red-soft);
            color: var(--red);
            border-color: #fca5a5;
        }

        .tag.decision-reconsider {
            background: var(--amber-soft);
            color: #c2410c;
            border-color: #fdba74;
        }
                .request-items-wrap {
            margin-top: 20px;
        }

        .request-item {
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #ffffff;
            margin-bottom: 14px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        .request-item-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }

        .request-item-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
        }

        .request-item-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            background: var(--blue-soft);
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            white-space: nowrap;
        }

        .hint {
            font-size: 14px;
            color: var(--muted);
            margin-top: 6px;
            line-height: 1.75;
            font-weight: 600;
        }

        input[type="file"],
        textarea,
        input[type="number"],
        input[type="text"],
        select {
            width: 100%;
            margin-top: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 13px 14px;
            font: inherit;
            background: #fff;
            color: var(--text);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .uploaded-files {
            margin-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .uploaded-file {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            text-decoration: none;
            border: 1px solid #cfe0ff;
            background: #f8fbff;
            border-radius: 16px;
            padding: 12px 14px;
        }

        .uploaded-file-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .file-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #eaf2ff;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            flex-shrink: 0;
        }

        .file-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-open {
            font-size: 13px;
            font-weight: 900;
            color: #1d4ed8;
            flex-shrink: 0;
        }

        .answers-card {
            border: 1px solid #dbe4ea;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            border-radius: 22px;
            padding: 22px;
            margin-top: 22px;
        }

        .answers-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: var(--muted);
            font-weight: 900;
            margin-bottom: 14px;
        }

        .answer-box {
            border: 1px solid #dbe4ea;
            background: #ffffff;
            border-radius: 18px;
            padding: 16px;
            margin-top: 14px;
        }

        .answer-box-label {
            font-size: 13px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 8px;
        }

        .answer-box-value {
            font-size: 15px;
            line-height: 1.8;
            color: #334155;
            font-weight: 600;
            white-space: pre-line;
        }

        .negotiation-panel {
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            border: 1px solid #d7e6ff;
            border-radius: 22px;
            padding: 22px;
            margin-top: 22px;
        }

        .negotiation-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: var(--muted);
            font-weight: 900;
            margin-bottom: 14px;
        }

        .decision-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .decision-option input[type="radio"] { display: none; }

        .decision-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 58px;
            border-radius: 16px;
            border: 2px solid var(--line);
            background: #ffffff;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all .18s ease;
            text-align: center;
            padding: 10px 12px;
        }

        .decision-option.approve label { color: #047857; }
        .decision-option.decline label { color: #b91c1c; }
        .decision-option.reconsider label { color: #c2410c; }

        .decision-option input[type="radio"]:checked + label {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .decision-option.approve input[type="radio"]:checked + label {
            border-color: #86efac;
            background: var(--green-soft);
        }

        .decision-option.decline input[type="radio"]:checked + label {
            border-color: #fca5a5;
            background: var(--red-soft);
        }

        .decision-option.reconsider input[type="radio"]:checked + label {
            border-color: #fdba74;
            background: var(--amber-soft);
        }

        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .muted {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
            font-weight: 600;
        }

        .submit-wrap {
            margin-top: 24px;
            display: flex;
            justify-content: flex-start;
        }

        button.submit-btn {
            background: linear-gradient(135deg, #26b6b7 0%, #39c7c8 100%);
            color: #fff;
            border: 0;
            border-radius: 16px;
            padding: 15px 24px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(38, 182, 183, 0.18);
        }

        .locked-note {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
            font-size: 14px;
            line-height: 1.7;
            font-weight: 700;
        }

        @media (max-width: 860px) {
            h1 { font-size: 34px; }
            .grid { grid-template-columns: 1fr; }
            .decision-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 700px) {
            .header, .body { padding: 20px; }
        }
    </style>
</head>
<body>
@php
    $decodedResponse = json_decode((string) ($candidateRequest->candidate_response ?? ''), true);

    if (! is_array($decodedResponse)) {
        $decodedResponse = [];
    }

    $thread = $thread ?? (is_array($decodedResponse['thread'] ?? null) ? $decodedResponse['thread'] : []);

    if (empty($thread)) {
        $thread[] = [
            'sender' => 'hr',
            'event' => 'request_created',
            'title' => $candidateRequest->title,
            'message' => $candidateRequest->notes,
            'salary' => $candidateRequest->proposed_salary,
            'currency' => $candidateRequest->currency,
            'created_at' => optional($candidateRequest->created_at)?->toDateTimeString(),
        ];
    }

    $isFinalOffer = (bool) ($candidateRequest->is_final_offer ?? false);
    $isClosedRequest = $isClosedPortal ?? in_array($candidateRequest->request_status, ['accepted', 'declined', 'closed'], true);
    $canReconsider = ! $isFinalOffer && ! $isClosedRequest;
    $currentDecision = old('decision');
    $prefillCounterOffer = old('counter_offer');

    if ($prefillCounterOffer === null) {
        $prefillCounterOffer = '';
    }

    $closedStatusClass = match ((string) $candidateRequest->request_status) {
        'accepted' => 'accepted',
        'declined' => 'declined',
        default => 'closed',
    };

    $closedStatusLabel = match ((string) $candidateRequest->request_status) {
        'accepted' => 'Approved',
        'declined' => 'Declined',
        default => 'Closed',
    };

    $candidateMessageValue = filled($candidateMessage ?? null) ? $candidateMessage : '-';
@endphp

    <div class="wrap">
        <div class="card">
            <div class="header">
                <div class="logo-wrap">
                    <img src="{{ asset('images/sada-horizontal.png') }}" alt="Sada Fezzan">
                </div>

                <span class="eyebrow">Candidate Request</span>
                <h1>{{ $candidateRequest->title ?: 'Request Portal' }}</h1>
            </div>

            <div class="body">
                @if (session('success'))
                    <div class="success">{{ session('success') }}</div>
                @endif

                @if ($isClosedRequest)
                    <div class="status-hero closed-{{ $closedStatusClass }}">
                        <div class="status-hero-title">Request Status</div>
                        <div class="status-hero-main">
                            <div class="status-hero-heading">This request is now closed.</div>
                            <span class="status-pill {{ $closedStatusClass }}">{{ $closedStatusLabel }}</span>
                        </div>
                        <div class="hero-offer-note" style="margin-top:14px;">
                            This portal is now in read-only mode. You can review the request details, your answers, uploaded files, and final decision below.
                        </div>
                    </div>
                @endif
                                <div class="grid">
                    <div>
                        <div class="panel">
                            <div class="label">Request Type</div>
                            <div class="value">{{ ucfirst(str_replace('_', ' ', (string) $candidateRequest->type)) }}</div>
                        </div>

                        <div class="panel">
                            <div class="label">Applicant</div>
                            <div class="value">{{ $candidateRequest->jobApplication?->full_name ?: '-' }}</div>
                        </div>

                        <div class="panel">
                            <div class="label">Position</div>
                            <div class="value">{{ $candidateRequest->jobApplication?->job?->title ?: '-' }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="panel">
                            <div class="label">Request Status</div>
                            <div class="value">{{ ucfirst(str_replace('_', ' ', (string) $candidateRequest->request_status)) }}</div>
                        </div>

                        <div class="panel">
                            <div class="label">Due Date</div>
                            <div class="value">{{ optional($candidateRequest->due_date)?->format('M j, Y') ?: '-' }}</div>
                        </div>

                        <div class="panel">
                            <div class="label">Created At</div>
                            <div class="value">{{ optional($candidateRequest->created_at)?->format('M j, Y - H:i') ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="label">Instructions</div>
                    <div class="value">{{ $candidateRequest->notes ?: '-' }}</div>

                    @if ($candidateRequest->type === 'salary_negotiation' && filled($candidateRequest->proposed_salary))
                        <div class="hero-offer">
                            <div class="hero-offer-title">
                                {{ $isFinalOffer ? 'Final HR Offer' : 'HR Counter Offer' }}
                            </div>

                            <div class="hero-offer-amount">
                                {{ number_format((float) $candidateRequest->proposed_salary, 2) }}
                                {{ $candidateRequest->currency ?: 'USD' }}
                            </div>

                            @if ($isFinalOffer)
                                <div class="hero-offer-note">This is the final offer from HR. You may approve or decline this offer only.</div>
                            @elseif (filled($candidateRequest->notes))
                                <div class="hero-offer-note">{{ $candidateRequest->notes }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="timeline-card">
                    <div class="timeline-title">Negotiation History</div>

                    <div class="timeline">
                        <div class="timeline-line"></div>

                        @foreach ($thread as $entry)
                            @php
                                $sender = $entry['sender'] ?? 'system';
                                $event = strtolower((string) ($entry['event'] ?? 'message'));
                                $entryDate = $entry['created_at'] ?? null;
                                $entryTitle = $entry['title'] ?? null;
                                $entryMessage = $entry['message'] ?? null;
                                $entrySalary = $entry['salary'] ?? null;
                                $entryCurrency = $entry['currency'] ?? ($candidateRequest->currency ?: 'USD');
                                $entryNotes = $entry['notes'] ?? null;
                                $entryNoteResponses = is_array($entry['note_responses'] ?? null) ? $entry['note_responses'] : [];

                                $isHr = $sender === 'hr';
                                $isCandidate = $sender === 'candidate';

                                $eventLabel = match ($event) {
                                    'approved', 'accepted' => 'Approved',
                                    'declined' => 'Declined',
                                    'reconsidered' => 'Reconsidered',
                                    'new_offer' => 'New Offer',
                                    'final_offer' => 'Final Offer',
                                    'request_created' => 'Request Created',
                                    default => ucfirst(str_replace('_', ' ', $event)),
                                };
                            @endphp

                            <div class="timeline-item">
                                <div class="timeline-dot {{ $isHr ? 'hr' : 'candidate' }}"></div>

                                <div class="bubble {{ $isHr ? 'hr' : 'candidate' }}">
                                    <div class="bubble-head">
                                        <div class="bubble-role">
                                            {{ $isHr ? 'HR Request' : ($isCandidate ? 'Candidate Reply' : 'System Update') }}
                                        </div>

                                        <div class="bubble-date">
                                            {{ $entryDate ? \Illuminate\Support\Carbon::parse($entryDate)->format('M j, Y - H:i') : '-' }}
                                        </div>
                                    </div>

                                    @if ($entryTitle)
                                        <div class="bubble-title">{{ $entryTitle }}</div>
                                    @endif

                                    @if ($entryMessage)
                                        <div class="bubble-message">{{ $entryMessage }}</div>
                                    @endif

                                    <div class="tag-row">
                                        @if ($isCandidate && $event !== 'reply')
                                            <span class="tag {{
                                                match ($event) {
                                                    'approved', 'accepted' => 'decision-approve',
                                                    'declined' => 'decision-decline',
                                                    'reconsidered' => 'decision-reconsider',
                                                    default => '',
                                                }
                                            }}">
                                                Decision: {{ $eventLabel }}
                                            </span>
                                        @endif

                                        @if ($entrySalary !== null && $entrySalary !== '')
                                            <span class="tag">
                                                {{ $isCandidate ? 'Counter Offer:' : (($event === 'final_offer') ? 'Final Offer:' : 'Offer:') }}
                                                &nbsp;{{ number_format((float) $entrySalary, 2) }} {{ $entryCurrency }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($entryNotes)
                                        <div class="panel" style="margin-top:16px; background:#ffffff;">
                                            <div class="label">Negotiation Notes</div>
                                            <div class="value" style="white-space:pre-line;">{{ $entryNotes }}</div>
                                        </div>
                                    @endif

                                    @if (! empty($entryNoteResponses))
                                        <div style="margin-top:16px; display:flex; flex-direction:column; gap:10px;">
                                            @foreach ($entryNoteResponses as $noteItemLabel => $noteItemValue)
                                                <div class="panel" style="margin-bottom:0; background:#ffffff;">
                                                    <div class="label">{{ $noteItemLabel }}</div>
                                                    <div class="value" style="white-space:pre-line;">{{ $noteItemValue }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($isClosedRequest)
                    <div class="answers-card">
                        <div class="answers-title">Submitted Answers</div>

                        <div class="answer-box">
                            <div class="answer-box-label">Candidate Message</div>
                            <div class="answer-box-value">{{ $candidateMessageValue }}</div>
                        </div>

                        @if ($candidateRequest->type === 'salary_negotiation')
                            <div class="answer-box">
                                <div class="answer-box-label">Decision</div>
                                <div class="answer-box-value">
                                    {{ filled($candidateDecision ?? null) ? ucfirst(str_replace('_', ' ', (string) $candidateDecision)) : '-' }}
                                </div>
                            </div>

                            <div class="answer-box">
                                <div class="answer-box-label">Counter Offer</div>
                                <div class="answer-box-value">
                                    @if (filled($candidateCounterOffer ?? null))
                                        {{ number_format((float) $candidateCounterOffer, 2) }} {{ $candidateRequest->currency ?: 'USD' }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="answer-box">
                                <div class="answer-box-label">Negotiation Notes</div>
                                <div class="answer-box-value">{{ filled($candidateNegotiationNotes ?? null) ? $candidateNegotiationNotes : '-' }}</div>
                            </div>
                        @endif

                        @if (! empty($noteResponsesByItem ?? []))
                            @foreach ($noteResponsesByItem as $noteResponse)
                                <div class="answer-box">
                                    <div class="answer-box-label">{{ $noteResponse['item_label'] ?? 'Note Response' }}</div>
                                    <div class="answer-box-value">{{ $noteResponse['response'] ?? '-' }}</div>
                                </div>
                            @endforeach
                        @endif

                        @if (! empty($uploadedFilesByItem ?? []))
                            <div class="answer-box">
                                <div class="answer-box-label">Uploaded Files</div>
                                <div class="uploaded-files" style="margin-top:10px;">
                                    @foreach ($uploadedFilesByItem as $itemFiles)
                                        @foreach ($itemFiles as $uploaded)
                                            @php
                                                $fileName = $uploaded['original_name'] ?? 'Open file';
                                                $filePath = $uploaded['stored_path'] ?? null;
                                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                $badge = match ($ext) {
                                                    'pdf' => 'PDF',
                                                    'doc', 'docx' => 'DOC',
                                                    'jpg', 'jpeg', 'png', 'webp' => 'IMG',
                                                    default => strtoupper($ext ?: 'FILE'),
                                                };
                                            @endphp

                                            @if ($filePath)
                                                <a class="uploaded-file" href="{{ asset('storage/' . ltrim($filePath, '/')) }}" target="_blank">
                                                    <div class="uploaded-file-left">
                                                        <div class="file-icon">{{ $badge }}</div>
                                                        <div class="file-name">{{ $fileName }}</div>
                                                    </div>
                                                    <div class="file-open">Open</div>
                                                </a>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('candidate-request.submit', $candidateRequest->public_token) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="request-items-wrap">
                            @forelse ($candidateRequest->items as $item)
                                <div class="request-item">
                                    <div class="request-item-head">
                                        <div class="request-item-title">{{ $item->label }}</div>

                                        <div class="request-item-badge">
                                            @if (($item->item_type ?? 'file') === 'note')
                                                Note
                                            @else
                                                {{ $item->file_format ? ucfirst(str_replace('_', ' ', $item->file_format)) : 'File' }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="hint">
                                        {{ $item->is_required ? 'Required' : 'Optional' }}
                                        @if (($item->item_type ?? 'file') !== 'note' && $item->allow_multiple)
                                            • Multiple files allowed
                                        @endif
                                        @if (!empty($item->notes))
                                            <br>{{ $item->notes }}
                                        @endif
                                    </div>

                                    @if (($item->item_type ?? 'file') === 'note')
                                        <textarea name="request_item_{{ $item->id }}" placeholder="Write your response here...">{{ old('request_item_' . $item->id, $noteResponsesByItem[$item->id]['response'] ?? '') }}</textarea>
                                    @else
                                        @if ($item->allow_multiple)
                                            <input type="file" name="request_item_{{ $item->id }}[]" multiple>
                                        @else
                                            <input type="file" name="request_item_{{ $item->id }}">
                                        @endif
                                                                                @if (!empty($uploadedFilesByItem[$item->id] ?? []))
                                            <div class="uploaded-files">
                                                @foreach (($uploadedFilesByItem[$item->id] ?? []) as $uploaded)
                                                    @php
                                                        $fileName = $uploaded['original_name'] ?? 'Open file';
                                                        $filePath = $uploaded['stored_path'] ?? null;
                                                        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                        $badge = match ($ext) {
                                                            'pdf' => 'PDF',
                                                            'doc', 'docx' => 'DOC',
                                                            'jpg', 'jpeg', 'png', 'webp' => 'IMG',
                                                            default => strtoupper($ext ?: 'FILE'),
                                                        };
                                                    @endphp

                                                    @if ($filePath)
                                                        <a class="uploaded-file" href="{{ asset('storage/' . ltrim($filePath, '/')) }}" target="_blank">
                                                            <div class="uploaded-file-left">
                                                                <div class="file-icon">{{ $badge }}</div>
                                                                <div class="file-name">{{ $fileName }}</div>
                                                            </div>
                                                            <div class="file-open">Open</div>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif

                                    @error('request_item_' . $item->id)
                                        <div class="error">{{ $message }}</div>
                                    @enderror

                                    @error('request_item_' . $item->id . '.*')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                            @empty
                                <div class="panel">
                                    <div class="muted">No request items are attached to this request.</div>
                                </div>
                            @endforelse
                        </div>

                        @if ($candidateRequest->type === 'salary_negotiation')
                            <div class="negotiation-panel">
                                <div class="negotiation-title">Negotiation Response</div>

                                <div class="section-title">Choose your decision</div>

                                <div class="decision-grid" style="grid-template-columns: repeat({{ $canReconsider ? 3 : 2 }}, minmax(0, 1fr));">
                                    <div class="decision-option approve">
                                        <input type="radio" id="decision_approved" name="decision" value="approved" {{ $currentDecision === 'approved' ? 'checked' : '' }}>
                                        <label for="decision_approved">Approve</label>
                                    </div>

                                    <div class="decision-option decline">
                                        <input type="radio" id="decision_declined" name="decision" value="declined" {{ $currentDecision === 'declined' ? 'checked' : '' }}>
                                        <label for="decision_declined">Decline</label>
                                    </div>

                                    @if ($canReconsider)
                                        <div class="decision-option reconsider">
                                            <input type="radio" id="decision_reconsidered" name="decision" value="reconsidered" {{ $currentDecision === 'reconsidered' ? 'checked' : '' }}>
                                            <label for="decision_reconsidered">Reconsider</label>
                                        </div>
                                    @endif
                                </div>

                                @error('decision')
                                    <div class="error">{{ $message }}</div>
                                @enderror

                                <div style="margin-top:18px; display:none;" id="counter-offer-block">
                                    <div class="section-title">Your Counter Offer</div>

                                    <div style="
                                        border:1px solid #fdba74;
                                        background:linear-gradient(135deg,#fff7ed 0%,#ffffff 100%);
                                        border-radius:18px;
                                        padding:16px;
                                    ">
                                        <div style="
                                            font-size:12px;
                                            text-transform:uppercase;
                                            letter-spacing:0.12em;
                                            color:#c2410c;
                                            font-weight:900;
                                            margin-bottom:10px;
                                        ">
                                            Latest Counter Offer
                                        </div>

                                        <input
                                            type="number"
                                            step="0.01"
                                            name="counter_offer"
                                            value="{{ $prefillCounterOffer }}"
                                            placeholder="Enter your expected amount"
                                            style="
                                                margin-top:0;
                                                font-size:18px;
                                                font-weight:800;
                                                border:1px solid #fdba74;
                                                background:#ffffff;
                                            "
                                        >
                                    </div>

                                    @error('counter_offer')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div style="margin-top:16px;">
                                    <div class="section-title">Negotiation Notes</div>
                                    <textarea name="negotiation_notes" placeholder="Write your negotiation notes here...">{{ old('negotiation_notes', $candidateNegotiationNotes) }}</textarea>
                                    @error('negotiation_notes')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div class="panel" style="margin-top:22px;">
                            <div class="label">Candidate Response</div>
                            <textarea name="candidate_response_text" placeholder="Write your response here...">{{ old('candidate_response_text', $candidateMessage) }}</textarea>

                            @error('candidate_response_text')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="submit-wrap">
                            <button class="submit-btn" type="submit">Submit Response</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        (function () {
            const approved = document.getElementById('decision_approved');
            const declined = document.getElementById('decision_declined');
            const reconsidered = document.getElementById('decision_reconsidered');
            const counterOfferBlock = document.getElementById('counter-offer-block');

            if (!counterOfferBlock) {
                return;
            }

            function toggleCounterOffer() {
                const show = reconsidered && reconsidered.checked;
                counterOfferBlock.style.display = show ? 'block' : 'none';
            }

            if (approved) approved.addEventListener('change', toggleCounterOffer);
            if (declined) declined.addEventListener('change', toggleCounterOffer);
            if (reconsidered) reconsidered.addEventListener('change', toggleCounterOffer);

            toggleCounterOffer();
        })();
    </script>
</body>
</html>