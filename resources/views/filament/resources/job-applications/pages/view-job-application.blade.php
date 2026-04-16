<x-filament-panels::page>
    <div style="display:flex; flex-direction:column; gap:24px;">
        {{ $this->infolist }}

        @if ($this->candidateRequests->count())
            <section style="
                background:#ffffff;
                border:1px solid #dbe4ea;
                border-radius:28px;
                overflow:hidden;
                box-shadow:0 18px 45px rgba(15,23,42,0.06);
            ">
                <div style="
                    padding:22px 28px;
                    border-bottom:1px solid #e5edf3;
                    background:linear-gradient(135deg,#ffffff 0%,#f8fbfd 55%,#eefaf8 100%);
                    font-size:20px;
                    font-weight:800;
                    color:#0f172a;
                    letter-spacing:-0.02em;
                ">
                    Candidate Requests
                </div>

                <div style="padding:28px; display:flex; flex-direction:column; gap:24px;">
                    @foreach ($this->candidateRequests as $request)
                        @php
                            $decoded = json_decode((string) $request->candidate_response, true);

                            if (! is_array($decoded)) {
                                $decoded = [];
                            }

                            $uploadedFiles = is_array($decoded['uploaded_files'] ?? null)
                                ? $decoded['uploaded_files']
                                : [];

                            $noteResponses = is_array($decoded['note_responses'] ?? null)
                                ? $decoded['note_responses']
                                : [];

                            $thread = is_array($decoded['thread'] ?? null)
                                ? $decoded['thread']
                                : [];

                            $candidateMessage = $decoded['message'] ?? null;
                            $candidateDecision = $decoded['decision'] ?? null;
                            $candidateCounterOffer = $decoded['counter_offer'] ?? null;
                            $candidateNegotiationNotes = $decoded['negotiation_notes'] ?? null;

                            $typeLabel = ucfirst(str_replace('_', ' ', (string) $request->type));
                            $statusLabel = ucfirst(str_replace('_', ' ', (string) $request->request_status));
                            $portalUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $request->public_token;

                            $statusStyles = match (strtolower((string) $request->request_status)) {
                                'pending' => 'background:#fff7ed;color:#c2410c;border:1px solid #fdba74;',
                                'submitted' => 'background:#eff6ff;color:#1d4ed8;border:1px solid #93c5fd;',
                                'accepted' => 'background:#ecfdf5;color:#047857;border:1px solid #86efac;',
                                'declined' => 'background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;',
                                'reconsidered' => 'background:#fff7ed;color:#c2410c;border:1px solid #fdba74;',
                                'closed' => 'background:#f8fafc;color:#475569;border:1px solid #cbd5e1;',
                                default => 'background:#f8fafc;color:#475569;border:1px solid #cbd5e1;',
                            };

                            $requestedItems = $request->items->map(function ($item) {
                                $parts = [];
                                $parts[] = $item->label ?: '-';

                                if (($item->item_type ?? 'file') === 'note') {
                                    $parts[] = '(Note)';
                                } else {
                                    $parts[] = $item->file_format
                                        ? '(' . ucfirst(str_replace('_', ' ', $item->file_format)) . ')'
                                        : '(File)';
                                }

                                $parts[] = $item->is_required ? '[Required]' : '[Optional]';
                                $parts[] = $item->allow_multiple ? '[Multiple]' : null;

                                return implode(' ', array_filter($parts));
                            });

                            $isNegotiation = $request->type === 'salary_negotiation';

                            $historyItems = $thread;

                            if (empty($historyItems)) {
                                $historyItems[] = [
                                    'sender' => 'hr',
                                    'event' => 'request_created',
                                    'message' => $request->notes,
                                    'salary' => $request->proposed_salary,
                                    'currency' => $request->currency,
                                    'created_at' => optional($request->created_at)?->toDateTimeString(),
                                    'title' => $request->title,
                                ];

                                if (
                                    filled($candidateMessage)
                                    || filled($candidateDecision)
                                    || filled($candidateCounterOffer)
                                    || filled($candidateNegotiationNotes)
                                    || count($noteResponses)
                                ) {
                                    $historyItems[] = [
                                        'sender' => 'candidate',
                                        'event' => $candidateDecision ?: 'reply',
                                        'message' => $candidateMessage,
                                        'salary' => $candidateCounterOffer,
                                        'currency' => $request->currency,
                                        'notes' => $candidateNegotiationNotes,
                                        'note_responses' => $noteResponses,
                                        'created_at' => optional($request->responded_at)?->toDateTimeString(),
                                    ];
                                }
                            }
                        @endphp

                        <div
                            x-data="{ confirmDelete: false }"
                            style="
                                border:1px solid #dbe4ea;
                                border-radius:26px;
                                overflow:hidden;
                                background:#ffffff;
                                box-shadow:0 10px 30px rgba(15,23,42,0.05);
                            "
                        >
                            <div style="
                                padding:24px 26px;
                                border-bottom:1px solid #e7eef3;
                                background:linear-gradient(135deg,#ffffff 0%,#f8fafc 60%,#eefbf7 100%);
                            ">
                                <div style="display:flex; flex-wrap:wrap; justify-content:space-between; gap:18px;">
                                    <div style="min-width:280px; flex:1;">
                                        <div style="
                                            display:inline-flex;
                                            align-items:center;
                                            padding:7px 12px;
                                            border-radius:999px;
                                            background:#ecfeff;
                                            color:#0f766e;
                                            border:1px solid #99f6e4;
                                            font-size:11px;
                                            font-weight:800;
                                            letter-spacing:0.14em;
                                            text-transform:uppercase;
                                        ">
                                            Candidate Request
                                        </div>

                                        <div style="
                                            margin-top:14px;
                                            font-size:26px;
                                            line-height:1.05;
                                            font-weight:900;
                                            color:#0f172a;
                                            letter-spacing:-0.03em;
                                        ">
                                            {{ $request->title ?: '-' }}
                                        </div>

                                        <div style="margin-top:16px; display:flex; flex-wrap:wrap; gap:10px;">
                                            <span style="
                                                display:inline-flex;
                                                align-items:center;
                                                padding:8px 14px;
                                                border-radius:999px;
                                                background:#eff6ff;
                                                color:#1d4ed8;
                                                border:1px solid #bfdbfe;
                                                font-size:13px;
                                                font-weight:700;
                                            ">
                                                {{ $typeLabel }}
                                            </span>

                                            <span style="
                                                display:inline-flex;
                                                align-items:center;
                                                padding:8px 14px;
                                                border-radius:999px;
                                                font-size:13px;
                                                font-weight:700;
                                                {!! $statusStyles !!}
                                            ">
                                                {{ $statusLabel }}
                                            </span>

                                            @if ($request->due_date)
                                                <span style="
                                                    display:inline-flex;
                                                    align-items:center;
                                                    padding:8px 14px;
                                                    border-radius:999px;
                                                    background:#ffffff;
                                                    color:#475569;
                                                    border:1px solid #cbd5e1;
                                                    font-size:13px;
                                                    font-weight:700;
                                                ">
                                                    Due {{ optional($request->due_date)?->format('M j, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div style="min-width:280px; display:flex; align-items:flex-start;">
                                        <div x-show="!confirmDelete" x-cloak style="display:flex; flex-wrap:wrap; gap:10px; justify-content:flex-end; width:100%;">
                                            <a
                                                href="{{ $portalUrl }}"
                                                target="_blank"
                                                style="
                                                    text-decoration:none;
                                                    padding:12px 18px;
                                                    border-radius:14px;
                                                    background:#ffffff;
                                                    color:#0f172a;
                                                    border:1px solid #cbd5e1;
                                                    font-size:15px;
                                                    font-weight:800;
                                                    box-shadow:0 4px 12px rgba(15,23,42,0.04);
                                                "
                                            >
                                                Open Portal
                                            </a>

                                            <button
                                                type="button"
                                                wire:click="resendCandidateRequestEmail({{ $request->id }})"
                                                style="
                                                    padding:12px 18px;
                                                    border:none;
                                                    border-radius:14px;
                                                    background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 100%);
                                                    color:#ffffff;
                                                    font-size:15px;
                                                    font-weight:800;
                                                    box-shadow:0 8px 20px rgba(37,99,235,0.25);
                                                    cursor:pointer;
                                                "
                                            >
                                                Resend Email
                                            </button>

                                            @if ($isNegotiation && in_array($request->request_status, ['reconsidered', 'submitted', 'pending'], true))
                                                <button
                                                    type="button"
                                                    wire:click="approveNegotiationRequest({{ $request->id }})"
                                                    style="
                                                        padding:12px 18px;
                                                        border:none;
                                                        border-radius:14px;
                                                        background:linear-gradient(135deg,#16a34a 0%,#15803d 100%);
                                                        color:#ffffff;
                                                        font-size:15px;
                                                        font-weight:800;
                                                        cursor:pointer;
                                                    "
                                                >
                                                    Approve
                                                </button>

                                                <button
                                                    type="button"
                                                    wire:click="declineNegotiationRequest({{ $request->id }})"
                                                    style="
                                                        padding:12px 18px;
                                                        border:none;
                                                        border-radius:14px;
                                                        background:linear-gradient(135deg,#f97316 0%,#ea580c 100%);
                                                        color:#ffffff;
                                                        font-size:15px;
                                                        font-weight:800;
                                                        cursor:pointer;
                                                    "
                                                >
                                                    Reject
                                                </button>

                                                <button
                                                    type="button"
                                                    wire:click="startNewOffer({{ $request->id }})"
                                                    style="
                                                        padding:12px 18px;
                                                        border:none;
                                                        border-radius:14px;
                                                        background:linear-gradient(135deg,#0f766e 0%,#0d9488 100%);
                                                        color:#ffffff;
                                                        font-size:15px;
                                                        font-weight:800;
                                                        cursor:pointer;
                                                    "
                                                >
                                                    New Offer
                                                </button>
                                            @endif

                                            <button
                                                type="button"
                                                x-on:click="confirmDelete = true"
                                                style="
                                                    padding:12px 18px;
                                                    border:none;
                                                    border-radius:14px;
                                                    background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%);
                                                    color:#ffffff;
                                                    font-size:15px;
                                                    font-weight:800;
                                                    box-shadow:0 8px 20px rgba(239,68,68,0.22);
                                                    cursor:pointer;
                                                "
                                            >
                                                Delete Request
                                            </button>
                                        </div>

                                        <div
                                            x-show="confirmDelete"
                                            x-cloak
                                            style="
                                                width:100%;
                                                border:1px solid #fecaca;
                                                background:#fef2f2;
                                                border-radius:18px;
                                                padding:16px;
                                            "
                                        >
                                            <div style="font-size:15px; font-weight:900; color:#b91c1c;">
                                                Confirm deletion
                                            </div>

                                            <div style="margin-top:6px; font-size:14px; line-height:1.6; color:#dc2626;">
                                                This will permanently remove only this request and its related items.
                                            </div>

                                            <div style="margin-top:14px; display:flex; flex-wrap:wrap; gap:10px;">
                                                <button
                                                    type="button"
                                                    x-on:click="confirmDelete = false"
                                                    style="
                                                        padding:11px 16px;
                                                        border-radius:12px;
                                                        border:1px solid #cbd5e1;
                                                        background:#ffffff;
                                                        color:#334155;
                                                        font-size:14px;
                                                        font-weight:800;
                                                        cursor:pointer;
                                                    "
                                                >
                                                    Cancel
                                                </button>

                                                <button
                                                    type="button"
                                                    wire:click="deleteCandidateRequest({{ $request->id }})"
                                                    style="
                                                        padding:11px 16px;
                                                        border:none;
                                                        border-radius:12px;
                                                        background:#dc2626;
                                                        color:#ffffff;
                                                        font-size:14px;
                                                        font-weight:800;
                                                        cursor:pointer;
                                                    "
                                                >
                                                    Yes, Delete Permanently
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="padding:24px 26px;">
                                <div style="
                                    display:grid;
                                    grid-template-columns:minmax(0, 1.35fr) minmax(320px, 0.85fr);
                                    gap:20px;
                                ">
                                    <div style="display:flex; flex-direction:column; gap:18px;">
                                        @if ($isNegotiation)
                                            <div style="
                                                border:1px solid #dbe4ea;
                                                background:#f8fafc;
                                                border-radius:22px;
                                                padding:22px;
                                            ">
                                                <div style="
                                                    font-size:13px;
                                                    font-weight:900;
                                                    color:#64748b;
                                                    text-transform:uppercase;
                                                    letter-spacing:0.16em;
                                                    margin-bottom:18px;
                                                ">
                                                    Negotiation History
                                                </div>

                                                <div style="position:relative; padding-left:40px; display:flex; flex-direction:column; gap:18px;">
                                                    <div style="
                                                        position:absolute;
                                                        left:15px;
                                                        top:6px;
                                                        bottom:6px;
                                                        width:2px;
                                                        background:#dbe4ea;
                                                    "></div>

                                                    @foreach ($historyItems as $historyIndex => $entry)
                                                        @php
                                                            $sender = $entry['sender'] ?? 'system';
                                                            $event = strtolower((string) ($entry['event'] ?? 'message'));
                                                            $entryDate = $entry['created_at'] ?? null;
                                                            $entryTitle = $entry['title'] ?? null;
                                                            $entryMessage = $entry['message'] ?? null;
                                                            $entrySalary = $entry['salary'] ?? null;
                                                            $entryCurrency = $entry['currency'] ?? ($request->currency ?: 'USD');
                                                            $entryNotes = $entry['notes'] ?? null;
                                                            $entryNoteResponses = is_array($entry['note_responses'] ?? null) ? $entry['note_responses'] : [];

                                                            $isHr = $sender === 'hr';
                                                            $isCandidate = $sender === 'candidate';

                                                            $dotColor = $isHr ? '#14b8a6' : '#3b82f6';
                                                            $cardBg = $isHr ? 'linear-gradient(135deg,#f0fdfa 0%,#ecfeff 100%)' : 'linear-gradient(135deg,#f8fbff 0%,#ffffff 100%)';
                                                            $cardBorder = $isHr ? '#99f6e4' : '#bfdbfe';
                                                            $headingColor = $isHr ? '#0f766e' : '#1d4ed8';

                                                            $eventLabel = match ($event) {
                                                                'approved' => 'Approved',
                                                                'accepted' => 'Accepted',
                                                                'declined' => 'Declined',
                                                                'reconsidered' => 'Reconsidered',
                                                                'new_offer' => 'New Offer',
                                                                'final_offer' => 'Final Offer',
                                                                'request_created' => 'Request Created',
                                                                default => ucfirst(str_replace('_', ' ', $event)),
                                                            };
                                                        @endphp

                                                        <div style="position:relative;">
                                                            <div style="
                                                                position:absolute;
                                                                left:-31px;
                                                                top:24px;
                                                                width:16px;
                                                                height:16px;
                                                                border-radius:999px;
                                                                background:{{ $dotColor }};
                                                                box-shadow:0 0 0 5px {{ $isHr ? 'rgba(20,184,166,0.15)' : 'rgba(59,130,246,0.15)' }};
                                                            "></div>

                                                            <div style="
                                                                border:1px solid {{ $cardBorder }};
                                                                background:{{ $cardBg }};
                                                                border-radius:22px;
                                                                padding:20px;
                                                                box-shadow:0 8px 24px rgba(15,23,42,0.04);
                                                            ">
                                                                <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
                                                                    <div style="
                                                                        font-size:15px;
                                                                        font-weight:900;
                                                                        color:{{ $headingColor }};
                                                                        text-transform:uppercase;
                                                                        letter-spacing:0.12em;
                                                                    ">
                                                                        {{ $isHr ? 'HR Request' : ($isCandidate ? 'Candidate Reply' : 'System Update') }}
                                                                    </div>

                                                                    <div style="
                                                                        font-size:13px;
                                                                        font-weight:800;
                                                                        color:#64748b;
                                                                        white-space:nowrap;
                                                                    ">
                                                                        {{ $entryDate ? \Illuminate\Support\Carbon::parse($entryDate)->format('M j, Y - H:i') : '-' }}
                                                                    </div>
                                                                </div>

                                                                @if ($entryTitle)
                                                                    <div style="
                                                                        margin-top:14px;
                                                                        font-size:24px;
                                                                        line-height:1.15;
                                                                        font-weight:900;
                                                                        color:#0f172a;
                                                                        letter-spacing:-0.03em;
                                                                    ">
                                                                        {{ $entryTitle }}
                                                                    </div>
                                                                @endif

                                                                @if ($entryMessage)
                                                                    <div style="
                                                                        margin-top:14px;
                                                                        font-size:15px;
                                                                        line-height:1.85;
                                                                        color:#334155;
                                                                        font-weight:600;
                                                                        white-space:pre-line;
                                                                    ">
                                                                        {{ $entryMessage }}
                                                                    </div>
                                                                @endif

                                                                <div style="margin-top:16px; display:flex; flex-wrap:wrap; gap:10px;">
                                                                    @if ($isCandidate && $event !== 'reply')
                                                                        <span style="
                                                                            display:inline-flex;
                                                                            align-items:center;
                                                                            padding:9px 14px;
                                                                            border-radius:999px;
                                                                            font-size:13px;
                                                                            font-weight:800;
                                                                            {{ match ($event) {
                                                                                'approved', 'accepted' => 'background:#ecfdf5;color:#047857;border:1px solid #86efac;',
                                                                                'declined' => 'background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;',
                                                                                'reconsidered' => 'background:#fff7ed;color:#c2410c;border:1px solid #fdba74;',
                                                                                default => 'background:#f8fafc;color:#475569;border:1px solid #cbd5e1;',
                                                                            } }}
                                                                        ">
                                                                            Decision: {{ $eventLabel }}
                                                                        </span>
                                                                    @endif

                                                                    @if ($entrySalary !== null && $entrySalary !== '')
                                                                        <span style="
                                                                            display:inline-flex;
                                                                            align-items:center;
                                                                            padding:10px 16px;
                                                                            border-radius:999px;
                                                                            background:#ffffff;
                                                                            border:1px solid #cbd5e1;
                                                                            color:#0f172a;
                                                                            font-size:14px;
                                                                            font-weight:900;
                                                                            box-shadow:0 4px 14px rgba(15,23,42,0.04);
                                                                        ">
                                                                            {{ $isCandidate ? 'Counter Offer:' : (($event === 'final_offer') ? 'Final Offer:' : 'Proposed Salary:') }}
                                                                            &nbsp;{{ number_format((float) $entrySalary, 2) }} {{ $entryCurrency }}
                                                                        </span>
                                                                    @endif
                                                                </div>

                                                                @if ($entryNotes)
                                                                    <div style="
                                                                        margin-top:16px;
                                                                        border:1px solid #dbe4ea;
                                                                        background:#ffffff;
                                                                        border-radius:18px;
                                                                        padding:16px;
                                                                    ">
                                                                        <div style="
                                                                            font-size:13px;
                                                                            font-weight:900;
                                                                            color:#64748b;
                                                                            text-transform:uppercase;
                                                                            letter-spacing:0.12em;
                                                                            margin-bottom:8px;
                                                                        ">
                                                                            Negotiation Notes
                                                                        </div>

                                                                        <div style="
                                                                            font-size:15px;
                                                                            line-height:1.8;
                                                                            color:#334155;
                                                                            font-weight:600;
                                                                            white-space:pre-line;
                                                                        ">
                                                                            {{ $entryNotes }}
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if ($isCandidate && filled($candidateMessage) && $historyIndex === count($historyItems) - 1 && $event === 'reply')
                                                                    <div style="
                                                                        margin-top:16px;
                                                                        border:1px solid #dbe4ea;
                                                                        background:#ffffff;
                                                                        border-radius:18px;
                                                                        padding:16px;
                                                                    ">
                                                                        <div style="
                                                                            font-size:13px;
                                                                            font-weight:900;
                                                                            color:#64748b;
                                                                            text-transform:uppercase;
                                                                            letter-spacing:0.12em;
                                                                            margin-bottom:8px;
                                                                        ">
                                                                            General Message
                                                                        </div>

                                                                        <div style="
                                                                            font-size:15px;
                                                                            line-height:1.8;
                                                                            color:#334155;
                                                                            font-weight:600;
                                                                            white-space:pre-line;
                                                                        ">
                                                                            {{ $candidateMessage }}
                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                @if (! empty($entryNoteResponses))
                                                                    <div style="
                                                                        margin-top:16px;
                                                                        border:1px solid #dbe4ea;
                                                                        background:#ffffff;
                                                                        border-radius:18px;
                                                                        padding:16px;
                                                                    ">
                                                                        <div style="
                                                                            font-size:13px;
                                                                            font-weight:900;
                                                                            color:#64748b;
                                                                            text-transform:uppercase;
                                                                            letter-spacing:0.12em;
                                                                            margin-bottom:12px;
                                                                        ">
                                                                            Note Responses
                                                                        </div>

                                                                        <div style="display:flex; flex-direction:column; gap:10px;">
                                                                            @foreach ($entryNoteResponses as $noteItemLabel => $noteItemValue)
                                                                                <div style="
                                                                                    padding:12px 14px;
                                                                                    border:1px solid #e2e8f0;
                                                                                    border-radius:14px;
                                                                                    background:#f8fafc;
                                                                                ">
                                                                                    <div style="font-size:13px; font-weight:900; color:#64748b; margin-bottom:4px;">
                                                                                        {{ $noteItemLabel }}
                                                                                    </div>
                                                                                    <div style="font-size:15px; line-height:1.75; color:#334155; font-weight:600; white-space:pre-line;">
                                                                                        {{ $noteItemValue }}
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                                                                        @if (empty($historyItems) || count($historyItems) === 1)
                                                        <div style="
                                                            border:1px dashed #cbd5e1;
                                                            background:#ffffff;
                                                            border-radius:18px;
                                                            padding:18px 20px;
                                                            color:#64748b;
                                                            font-size:15px;
                                                            font-weight:700;
                                                        ">
                                                            No candidate reply yet.
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            @if ($this->activeNegotiationRequestId === $request->id || $this->activeFinalOfferRequestId === $request->id)
                                                <div style="
                                                    border:1px solid {{ $this->activeFinalOfferRequestId === $request->id ? '#fde68a' : '#99f6e4' }};
                                                    background:{{ $this->activeFinalOfferRequestId === $request->id ? 'linear-gradient(135deg,#fffbeb 0%,#ffffff 100%)' : 'linear-gradient(135deg,#f0fdfa 0%,#ffffff 100%)' }};
                                                    border-radius:22px;
                                                    padding:20px;
                                                ">
                                                    <div style="
                                                        font-size:13px;
                                                        font-weight:900;
                                                        color:{{ $this->activeFinalOfferRequestId === $request->id ? '#a16207' : '#0f766e' }};
                                                        text-transform:uppercase;
                                                        letter-spacing:0.12em;
                                                        margin-bottom:14px;
                                                    ">
                                                        {{ $this->activeFinalOfferRequestId === $request->id ? 'HR Final Offer' : 'HR New Offer' }}
                                                    </div>

                                                    <div style="display:grid; grid-template-columns:minmax(0,1fr) 180px; gap:12px;">
                                                        <div>
                                                            <label style="display:block; font-size:13px; font-weight:800; color:#475569; margin-bottom:6px;">Salary</label>
                                                            <input
                                                                type="text"
                                                                wire:model.defer="newOfferSalary"
                                                                style="
                                                                    width:100%;
                                                                    border:1px solid #cbd5e1;
                                                                    border-radius:12px;
                                                                    padding:12px 14px;
                                                                    font-size:14px;
                                                                    font-weight:700;
                                                                    color:#0f172a;
                                                                    background:#ffffff;
                                                                "
                                                            >
                                                            @error('newOfferSalary')
                                                                <div style="margin-top:6px; font-size:12px; color:#dc2626; font-weight:700;">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div>
                                                            <label style="display:block; font-size:13px; font-weight:800; color:#475569; margin-bottom:6px;">Currency</label>
                                                            <input
                                                                type="text"
                                                                wire:model.defer="newOfferCurrency"
                                                                style="
                                                                    width:100%;
                                                                    border:1px solid #cbd5e1;
                                                                    border-radius:12px;
                                                                    padding:12px 14px;
                                                                    font-size:14px;
                                                                    font-weight:700;
                                                                    color:#0f172a;
                                                                    background:#ffffff;
                                                                "
                                                            >
                                                            @error('newOfferCurrency')
                                                                <div style="margin-top:6px; font-size:12px; color:#dc2626; font-weight:700;">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div style="margin-top:12px;">
                                                        <label style="display:block; font-size:13px; font-weight:800; color:#475569; margin-bottom:6px;">Notes</label>
                                                        <textarea
                                                            wire:model.defer="newOfferNotes"
                                                            rows="4"
                                                            style="
                                                                width:100%;
                                                                border:1px solid #cbd5e1;
                                                                border-radius:12px;
                                                                padding:12px 14px;
                                                                font-size:14px;
                                                                font-weight:600;
                                                                color:#0f172a;
                                                                background:#ffffff;
                                                            "
                                                        ></textarea>
                                                        @error('newOfferNotes')
                                                            <div style="margin-top:6px; font-size:12px; color:#dc2626; font-weight:700;">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                                                        <button
                                                            type="button"
                                                            wire:click="sendNewNegotiationOffer({{ $request->id }})"
                                                            style="
                                                                padding:12px 18px;
                                                                border:none;
                                                                border-radius:14px;
                                                                background:linear-gradient(135deg,#0f766e 0%,#0d9488 100%);
                                                                color:#ffffff;
                                                                font-size:14px;
                                                                font-weight:800;
                                                                cursor:pointer;
                                                            "
                                                        >
                                                            Send New Offer
                                                        </button>

                                                        <button
                                                            type="button"
                                                            wire:click="sendFinalNegotiationOffer({{ $request->id }})"
                                                            style="
                                                                padding:12px 18px;
                                                                border:none;
                                                                border-radius:14px;
                                                                background:linear-gradient(135deg,#ca8a04 0%,#eab308 100%);
                                                                color:#ffffff;
                                                                font-size:14px;
                                                                font-weight:800;
                                                                cursor:pointer;
                                                            "
                                                        >
                                                            Send Final Offer
                                                        </button>

                                                        <button
                                                            type="button"
                                                            wire:click="cancelNewOffer"
                                                            style="
                                                                padding:12px 18px;
                                                                border:1px solid #cbd5e1;
                                                                border-radius:14px;
                                                                background:#ffffff;
                                                                color:#334155;
                                                                font-size:14px;
                                                                font-weight:800;
                                                                cursor:pointer;
                                                            "
                                                        >
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div style="
                                                border:1px solid #dbe4ea;
                                                background:#f8fafc;
                                                border-radius:22px;
                                                padding:20px;
                                            ">
                                                <div style="
                                                    font-size:13px;
                                                    font-weight:900;
                                                    color:#64748b;
                                                    text-transform:uppercase;
                                                    letter-spacing:0.16em;
                                                    margin-bottom:12px;
                                                ">
                                                    Notes / Instructions
                                                </div>

                                                <div style="
                                                    font-size:15px;
                                                    line-height:1.85;
                                                    color:#334155;
                                                    font-weight:600;
                                                    white-space:pre-line;
                                                ">
                                                    {{ $request->notes ?: '-' }}
                                                </div>
                                            </div>

                                            <div style="
                                                border:1px solid #dbe4ea;
                                                background:#f8fafc;
                                                border-radius:22px;
                                                padding:20px;
                                            ">
                                                <div style="
                                                    font-size:13px;
                                                    font-weight:900;
                                                    color:#64748b;
                                                    text-transform:uppercase;
                                                    letter-spacing:0.16em;
                                                    margin-bottom:12px;
                                                ">
                                                    Requested Items
                                                </div>

                                                @if ($requestedItems->count())
                                                    <div style="display:flex; flex-wrap:wrap; gap:10px;">
                                                        @foreach ($requestedItems as $requestedItem)
                                                            <span style="
                                                                display:inline-flex;
                                                                align-items:center;
                                                                padding:10px 14px;
                                                                border-radius:999px;
                                                                background:#ffffff;
                                                                border:1px solid #cbd5e1;
                                                                color:#334155;
                                                                font-size:14px;
                                                                font-weight:700;
                                                            ">
                                                                {{ $requestedItem }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div style="
                                                        font-size:15px;
                                                        color:#64748b;
                                                        font-weight:700;
                                                    ">
                                                        -
                                                    </div>
                                                @endif
                                            </div>

                                            <div style="
                                                border:1px solid #dbe4ea;
                                                background:#f8fafc;
                                                border-radius:22px;
                                                padding:20px;
                                            ">
                                                <div style="
                                                    font-size:13px;
                                                    font-weight:900;
                                                    color:#64748b;
                                                    text-transform:uppercase;
                                                    letter-spacing:0.16em;
                                                    margin-bottom:12px;
                                                ">
                                                    Candidate Response
                                                </div>

                                                <div style="
                                                    border:1px solid #dbe4ea;
                                                    background:#ffffff;
                                                    border-radius:18px;
                                                    padding:16px;
                                                ">
                                                    <div style="
                                                        font-size:14px;
                                                        font-weight:800;
                                                        color:#64748b;
                                                        margin-bottom:8px;
                                                    ">
                                                        General Message
                                                    </div>

                                                    <div style="
                                                        font-size:15px;
                                                        line-height:1.8;
                                                        color:#334155;
                                                        font-weight:600;
                                                        white-space:pre-line;
                                                    ">
                                                        {{ filled($candidateMessage) ? $candidateMessage : '-' }}
                                                    </div>
                                                </div>

                                                @if (! empty($noteResponses))
                                                    <div style="margin-top:14px; display:flex; flex-direction:column; gap:10px;">
                                                        @foreach ($noteResponses as $noteItemLabel => $noteItemValue)
                                                            <div style="
                                                                border:1px solid #dbe4ea;
                                                                background:#ffffff;
                                                                border-radius:18px;
                                                                padding:16px;
                                                            ">
                                                                <div style="
                                                                    font-size:13px;
                                                                    font-weight:900;
                                                                    color:#64748b;
                                                                    text-transform:uppercase;
                                                                    letter-spacing:0.12em;
                                                                    margin-bottom:8px;
                                                                ">
                                                                    {{ $noteItemLabel }}
                                                                </div>

                                                                <div style="
                                                                    font-size:15px;
                                                                    line-height:1.8;
                                                                    color:#334155;
                                                                    font-weight:600;
                                                                    white-space:pre-line;
                                                                ">
                                                                    {{ $noteItemValue }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div style="display:flex; flex-direction:column; gap:18px;">
                                        <div style="
                                            border:1px solid #dbe4ea;
                                            background:#f8fafc;
                                            border-radius:22px;
                                            padding:20px;
                                        ">
                                            <div style="
                                                font-size:13px;
                                                font-weight:900;
                                                color:#64748b;
                                                text-transform:uppercase;
                                                letter-spacing:0.16em;
                                                margin-bottom:14px;
                                            ">
                                                Request Summary
                                            </div>

                                            <div style="display:flex; flex-direction:column; gap:0;">
                                                <div style="display:flex; justify-content:space-between; gap:16px; padding:12px 0; border-bottom:1px solid #e2e8f0;">
                                                    <span style="font-size:15px; font-weight:800; color:#64748b;">Status</span>
                                                    <span style="font-size:15px; font-weight:900; color:#0f172a; text-align:right;">{{ $statusLabel }}</span>
                                                </div>

                                                <div style="display:flex; justify-content:space-between; gap:16px; padding:12px 0; border-bottom:1px solid #e2e8f0;">
                                                    <span style="font-size:15px; font-weight:800; color:#64748b;">Type</span>
                                                    <span style="font-size:15px; font-weight:900; color:#0f172a; text-align:right;">{{ $typeLabel }}</span>
                                                </div>

                                                <div style="display:flex; justify-content:space-between; gap:16px; padding:12px 0; border-bottom:1px solid #e2e8f0;">
                                                    <span style="font-size:15px; font-weight:800; color:#64748b;">Due Date</span>
                                                    <span style="font-size:15px; font-weight:900; color:#0f172a; text-align:right;">{{ $request->due_date ? optional($request->due_date)->format('M j, Y') : '-' }}</span>
                                                </div>

                                                <div style="display:flex; justify-content:space-between; gap:16px; padding:12px 0; border-bottom:1px solid #e2e8f0;">
                                                    <span style="font-size:15px; font-weight:800; color:#64748b;">Created At</span>
                                                    <span style="font-size:15px; font-weight:900; color:#0f172a; text-align:right;">{{ optional($request->created_at)?->format('M j, Y - H:i') ?: '-' }}</span>
                                                </div>

                                                <div style="display:flex; justify-content:space-between; gap:16px; padding:12px 0;">
                                                    <span style="font-size:15px; font-weight:800; color:#64748b;">Files Count</span>
                                                    <span style="font-size:15px; font-weight:900; color:#0f172a; text-align:right;">{{ count($uploadedFiles) }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="
                                            border:1px solid #dbe4ea;
                                            background:#f8fafc;
                                            border-radius:22px;
                                            padding:20px;
                                        ">
                                            <div style="
                                                display:flex;
                                                justify-content:space-between;
                                                align-items:center;
                                                gap:12px;
                                                margin-bottom:14px;
                                            ">
                                                <div style="
                                                    font-size:13px;
                                                    font-weight:900;
                                                    color:#64748b;
                                                    text-transform:uppercase;
                                                    letter-spacing:0.16em;
                                                ">
                                                    Uploaded Files
                                                </div>

                                                <div style="
                                                    font-size:13px;
                                                    font-weight:800;
                                                    color:#94a3b8;
                                                ">
                                                    {{ count($uploadedFiles) }} file(s)
                                                </div>
                                            </div>

                                            @if (count($uploadedFiles))
                                                <div style="display:flex; flex-direction:column; gap:12px;">
                                                    @foreach ($uploadedFiles as $uploaded)
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
                                                            <a
                                                                href="{{ asset('storage/' . ltrim($filePath, '/')) }}"
                                                                target="_blank"
                                                                style="
                                                                    display:flex;
                                                                    align-items:center;
                                                                    justify-content:space-between;
                                                                    gap:14px;
                                                                    text-decoration:none;
                                                                    border:1px solid #bfdbfe;
                                                                    background:#f8fbff;
                                                                    border-radius:18px;
                                                                    padding:14px;
                                                                    box-shadow:0 4px 12px rgba(37,99,235,0.05);
                                                                "
                                                            >
                                                                <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                                                                    <div style="
                                                                        width:46px;
                                                                        height:46px;
                                                                        border-radius:14px;
                                                                        background:#eaf2ff;
                                                                        color:#1d4ed8;
                                                                        display:flex;
                                                                        align-items:center;
                                                                        justify-content:center;
                                                                        font-size:12px;
                                                                        font-weight:900;
                                                                        flex-shrink:0;
                                                                    ">
                                                                        {{ $badge }}
                                                                    </div>

                                                                    <div style="min-width:0;">
                                                                        <div style="
                                                                            font-size:15px;
                                                                            font-weight:900;
                                                                            color:#0f172a;
                                                                            white-space:nowrap;
                                                                            overflow:hidden;
                                                                            text-overflow:ellipsis;
                                                                        ">
                                                                            {{ $fileName }}
                                                                        </div>

                                                                        <div style="
                                                                            margin-top:4px;
                                                                            font-size:13px;
                                                                            font-weight:700;
                                                                            color:#64748b;
                                                                        ">
                                                                            Click to open
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div style="
                                                                    font-size:14px;
                                                                    font-weight:900;
                                                                    color:#1d4ed8;
                                                                    flex-shrink:0;
                                                                ">
                                                                    Open
                                                                </div>
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <div style="
                                                    border:1px dashed #cbd5e1;
                                                    background:#ffffff;
                                                    border-radius:18px;
                                                    padding:18px 20px;
                                                    color:#64748b;
                                                    font-size:15px;
                                                    line-height:1.7;
                                                    font-weight:700;
                                                ">
                                                    No uploaded files available for this request.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-filament-panels::page>