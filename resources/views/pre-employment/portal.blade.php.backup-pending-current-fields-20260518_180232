<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pre-Employment Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --sf-navy: #0f172a;
            --sf-blue: #234b74;
            --sf-teal: #14b8a6;
            --sf-green: #22c55e;
            --sf-yellow: #fbbf24;
            --sf-bg: #eef7f8;
            --sf-card: rgba(255,255,255,.94);
            --sf-border: rgba(15, 23, 42, .10);
            --sf-muted: #64748b;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top right, rgba(20, 184, 166, .16), transparent 34%),
                linear-gradient(135deg, #f8fafc, var(--sf-bg));
            color: var(--sf-navy);
        }

        .sf-portal-shell {
            width: min(100%, 1040px);
            margin: 48px auto;
            padding: 0 18px;
        }

        .sf-hero {
            border-radius: 34px;
            padding: 34px;
            color: #fff;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .16), transparent 34%),
                linear-gradient(135deg, #111827, #1f2937 62%, #234b74);
            box-shadow: 0 22px 60px rgba(15, 23, 42, .16);
            border: 1px solid rgba(255,255,255,.12);
            overflow: hidden;
            position: relative;
        }

        .sf-hero::after {
            content: "";
            position: absolute;
            inset-inline: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--sf-teal), var(--sf-yellow));
        }

        .sf-kicker {
            color: #94a3b8;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .sf-title {
            margin: 0;
            font-size: clamp(38px, 6vw, 72px);
            line-height: .94;
            letter-spacing: -.065em;
            font-weight: 950;
        }

        .sf-subtitle {
            margin: 16px 0 0;
            max-width: 760px;
            color: #cbd5e1;
            font-size: 15px;
            line-height: 1.7;
            font-weight: 650;
        }

        .sf-card {
            margin-top: 22px;
            border-radius: 30px;
            background: var(--sf-card);
            border: 1px solid var(--sf-border);
            box-shadow: 0 18px 46px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .sf-card-head {
            padding: 22px 26px;
            border-bottom: 1px solid rgba(15,23,42,.08);
        }

        .sf-card-title {
            margin: 0;
            font-size: 22px;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .sf-card-subtitle {
            margin-top: 6px;
            color: var(--sf-muted);
            font-weight: 650;
            font-size: 14px;
        }

        .sf-card-body { padding: 26px; }

        .sf-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .sf-info-box {
            border-radius: 22px;
            padding: 18px;
            border: 1px solid rgba(15,23,42,.08);
            background: rgba(248,250,252,.88);
        }

        .sf-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
            font-weight: 950;
            color: #64748b;
            margin-bottom: 8px;
        }

        .sf-value {
            font-size: 17px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .sf-alert-success {
            border-radius: 20px;
            padding: 16px 18px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid rgba(16,185,129,.22);
            font-weight: 800;
            margin-bottom: 18px;
        }

        .sf-alert-error {
            border-radius: 20px;
            padding: 16px 18px;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid rgba(244,63,94,.22);
            font-weight: 800;
            margin-bottom: 18px;
        }

        .sf-field-card {
            border-radius: 24px;
            padding: 20px;
            border: 1px solid rgba(15,23,42,.09);
            background:
                radial-gradient(circle at top right, rgba(20,184,166,.08), transparent 30%),
                rgba(255,255,255,.92);
            margin-bottom: 16px;
        }

        .sf-field-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .sf-field-title {
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .sf-pill {
            border-radius: 999px;
            padding: 8px 12px;
            background: #e0f2fe;
            color: #075985;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .sf-instructions {
            margin: 10px 0 14px;
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            font-weight: 650;
        }

        .sf-download-box {
            border-radius: 20px;
            padding: 16px;
            background: #f8fafc;
            border: 1px dashed rgba(15,23,42,.16);
            margin: 14px 0;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
        }

        .sf-download-box strong {
            display: block;
            font-size: 14px;
            font-weight: 950;
        }

        .sf-download-box span {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .sf-btn {
            border: 0;
            cursor: pointer;
            border-radius: 999px;
            min-height: 42px;
            padding: 11px 18px;
            font-size: 13px;
            font-weight: 950;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .sf-btn-primary { background: var(--sf-navy); color: #fff; }
        .sf-btn-yellow { background: var(--sf-yellow); color: #111827; }

        input[type="file"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            border-radius: 18px;
            border: 1px solid rgba(15,23,42,.14);
            background: #fff;
            padding: 14px;
            font-size: 14px;
            font-weight: 650;
            outline: none;
        }

        input[type="file"] {
            padding: 12px;
            background: #f8fafc;
        }

        .sf-existing-files { display: grid; gap: 10px; }

        .sf-file-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            border-radius: 18px;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.08);
        }

        .sf-file-row strong {
            display: block;
            font-size: 14px;
            font-weight: 950;
        }

        .sf-file-row span {
            display: block;
            color: #64748b;
            margin-top: 3px;
            font-size: 12px;
            font-weight: 700;
        }

        .sf-submit-row {
            display: flex;
            justify-content: flex-end;
            margin-top: 22px;
        }

        .sf-required { color: #dc2626; font-weight: 950; }


        .sf-salary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .sf-salary-card {
            border-radius: 22px;
            padding: 18px;
            background: #f8fafc;
            border: 1px solid rgba(15,23,42,.08);
        }

        .sf-file-scroll {
            max-height: 430px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .sf-file-actions {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .sf-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 950;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid rgba(16,185,129,.22);
            white-space: nowrap;
        }

        .sf-status-badge.waiting {
            background: #fffbeb;
            color: #92400e;
            border-color: rgba(245,158,11,.28);
        }

        .sf-status-badge.uploaded {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: rgba(59,130,246,.22);
        }

        @media (max-width: 860px) {
            .sf-grid { grid-template-columns: 1fr; }

            .sf-download-box,
            .sf-file-row,
            .sf-field-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .sf-btn { width: 100%; }
        }
    </style>
</head>

<body>
    @php
        use Illuminate\Support\Facades\Storage;
        use App\Models\CandidateRequest;

        $existingFiles = $preEmployment->files()
            ->where('is_active', true)
            ->latest()
            ->get();

        $salaryRequest = $preEmployment->job_application_id
            ? CandidateRequest::query()
                ->where('job_application_id', $preEmployment->job_application_id)
                ->where('type', 'salary_negotiation')
                ->latest('responded_at')
                ->latest('id')
                ->first()
            : null;

        $salaryResponse = [];
        if ($salaryRequest && filled($salaryRequest->candidate_response)) {
            $salaryResponse = json_decode((string) $salaryRequest->candidate_response, true) ?: [];
        }

        $salaryDecision = $salaryResponse['decision'] ?? $salaryRequest?->negotiation_result ?? null;
        $salaryAmount = $salaryRequest?->accepted_salary ?: $salaryRequest?->proposed_salary;
        $salaryCurrency = $salaryRequest?->accepted_currency ?: $salaryRequest?->currency;

        $valuesByField = $values ?? collect();

        $alreadySubmittedFieldIds = $valuesByField
            ->filter(fn ($value) => filled($value?->value))
            ->keys()
            ->values();

        $visibleFields = $preEmployment->portalFields ?? collect();

        $pendingFields = $visibleFields->filter(function ($field) use ($alreadySubmittedFieldIds) {
            return ! $alreadySubmittedFieldIds->contains($field->id);
        });

        $submittedReimbursementClaims = collect();

        try {
            if (class_exists(\App\Models\FinanceExpense::class)) {
                $submittedReimbursementClaims = \App\Models\FinanceExpense::query()
                    ->where(function ($query) use ($preEmployment) {
                        $query->where('pre_employment_id', $preEmployment->id);

                        if ($preEmployment->job_application_id) {
                            $query->orWhere('job_application_id', $preEmployment->job_application_id);
                        }
                    })
                    ->where('paid_by', \App\Models\FinanceExpense::PAID_BY_CANDIDATE)
                    ->orderByDesc('expense_date')
                    ->orderByDesc('id')
                    ->get();
            }
        } catch (\Throwable $e) {
            $submittedReimbursementClaims = collect();
        }


        $reimbursementClaims = collect();

        if (class_exists(\App\Models\FinanceExpense::class)) {
            $reimbursementClaims = \App\Models\FinanceExpense::query()
                ->where(function ($query) use ($preEmployment) {
                    $query->where('pre_employment_id', $preEmployment->id);

                    if ($preEmployment->job_application_id) {
                        $query->orWhere('job_application_id', $preEmployment->job_application_id);
                    }
                })
                ->where('paid_by', \App\Models\FinanceExpense::PAID_BY_CANDIDATE)
                ->latest('expense_date')
                ->latest('id')
                ->get();
        }
    @endphp

    <main class="sf-portal-shell">
        <section class="sf-hero">
            <div class="sf-kicker">Sada Fezzan Pre-Employment Portal</div>
            <h1 class="sf-title">Pre-Employment Portal</h1>
            <p class="sf-subtitle">
                Please review your pre-employment information. Download any document that requires your signature, then upload only the pending requested files below.
            </p>
        </section>

        <section class="sf-card">
            <div class="sf-card-head">
                <h2 class="sf-card-title">Candidate Overview</h2>
                <div class="sf-card-subtitle">Main candidate and job context.</div>
            </div>

            <div class="sf-card-body">
                @if(session('success'))
                    <div class="sf-alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="sf-alert-error">Please review the highlighted fields and try again.</div>
                @endif
                <div class="sf-grid sfpe-salary-agreement-grid">
                    <div class="sfpe-portal-info-card sfpe-salary-agreement-card">
                        <div class="sf-label">Pay Basis</div>
                        <div class="sf-value">Daily Rate</div>
                    </div>

                    <div class="sfpe-portal-info-card sfpe-salary-agreement-card sfpe-salary-rate-card">
                        <div class="sf-label">Daily Rate</div>
                        <div class="sf-value">{{ $salaryAmount ? number_format((float) $salaryAmount, 2) : '—' }}</div>
                    </div>

                    <div class="sfpe-portal-info-card sfpe-salary-agreement-card">
                        <div class="sf-label">Decision</div>
                        <div class="sf-value">{{ $salaryDecision ? ucfirst(str_replace('_', ' ', $salaryDecision)) : '—' }}</div>
                    </div>
                </div>
</div>

                    <div class="sf-info-box">
                        <div class="sf-label">Position</div>
                        <div class="sf-value">{{ $preEmployment->job?->title ?: '—' }}</div>
                    </div>

                    <div class="sf-info-box">
                        <div class="sf-label">Project</div>
                        <div class="sf-value">{{ $preEmployment->job?->project?->name ?: '—' }}</div>
                    </div>

                    <div class="sf-info-box">
                        <div class="sf-label">Client</div>
                        <div class="sf-value">{{ $preEmployment->job?->project?->client?->name ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </section>


        @if($salaryRequest)
{{-- Salary Agreement block removed intentionally. --}}

<section class="sf-card">
                <div class="sf-card-head">
                    <h2 class="sf-card-title">Uploaded Files</h2>
                    <div class="sf-card-subtitle">Files already received by Sada Fezzan. You do not need to upload these again.</div>
                </div>

                <div class="sf-card-body">
                    <div class="sf-existing-files sf-file-scroll">
                        @foreach($existingFiles as $file)
                            <div class="sf-file-row">
                                <div>
                                    <strong>{{ $file->title ?: 'Document' }}</strong>
                                    <span>{{ ucfirst(str_replace('_', ' ', $file->category ?: 'document')) }} · {{ $file->created_at?->format('M d, Y H:i') }}</span>
                                </div>

                                <div class="sf-file-actions">
                                    @php
                                        $fileText = strtolower(($file->title ?? '') . ' ' . ($file->category ?? '') . ' ' . ($file->notes ?? ''));
                                        $isSignedFile = str_contains($fileText, 'signed') || str_contains($fileText, 'signature');
                                    @endphp

                                    <span class="sf-status-badge {{ $isSignedFile ? '' : 'uploaded' }}">
                                        {{ $isSignedFile ? 'Signed' : 'Uploaded' }}
                                    </span>

                                    @if($file->file_path && Storage::disk('public')->exists($file->file_path))
                                        <a class="sf-btn sf-btn-yellow" href="{{ Storage::disk('public')->url($file->file_path) }}" target="_blank" rel="noopener">Open</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="sf-card">
            <div class="sf-card-head">
                <h2 class="sf-card-title">Pending Requests</h2>
                <div class="sf-card-subtitle">Upload only the new pending documents requested from you.</div>
            </div>

            <div class="sf-card-body">
                @if($pendingFields->isNotEmpty())
                    <form method="POST" action="{{ route('pre-employment.portal.submit', $preEmployment->portal_token) }}" enctype="multipart/form-data">
                        @csrf

                        @foreach($pendingFields as $field)
                            <div class="sf-field-card">
                                <div class="sf-field-top">
                                    <div>
                                        <div class="sf-field-title">
                                            {{ $field->label }}
                                            @if($field->is_required)
                                                <span class="sf-required">*</span>
                                            @endif
                                        </div>

                                        @if($field->instructions)
                                            <div class="sf-instructions">{{ $field->instructions }}</div>
                                        @endif
                                    </div>

                                    @if(($field->request_type ?? 'upload_only') === 'download_sign_upload')
                                        <span class="sf-status-badge waiting">Waiting for Signature</span>
                                    @else
                                        <span class="sf-pill">Upload Required</span>
                                    @endif
                                </div>

                                @if(($field->request_type ?? 'upload_only') === 'download_sign_upload' && $field->document_to_sign_path)
                                    <div class="sf-download-box">
                                        <div>
                                            <strong>Document to download and sign</strong>
                                            <span>{{ $field->document_to_sign_original_name ?: basename($field->document_to_sign_path) }}</span>
                                        </div>

                                        <a class="sf-btn sf-btn-yellow" href="{{ Storage::disk('public')->url($field->document_to_sign_path) }}" target="_blank" rel="noopener">
                                            Download Document
                                        </a>
                                    </div>
                                @endif

                                @if($field->field_type === 'file')
                                    <input type="file" name="field_{{ $field->id }}" @if($field->is_required) required @endif>
                                @elseif($field->field_type === 'date')
                                    <input type="date" name="field_{{ $field->id }}" @if($field->is_required) required @endif>
                                @elseif($field->field_type === 'email')
                                    <input type="email" name="field_{{ $field->id }}" @if($field->is_required) required @endif>
                                @elseif($field->field_type === 'number')
                                    <input type="number" step="any" name="field_{{ $field->id }}" @if($field->is_required) required @endif>
                                @else
                                    <textarea rows="4" name="field_{{ $field->id }}" @if($field->is_required) required @endif></textarea>
                                @endif

                                @error('field_' . $field->id)
                                    <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach

                        <div class="sf-submit-row">
<button type="submit" class="sf-btn sf-btn-primary">Submit Documents</button>
                        </div>
                    </form>
                @else
                    <div class="sf-field-card">
                        <div class="sf-field-title">No pending file requests</div>
                        <div class="sf-instructions">All currently requested documents have already been submitted.</div>
                    </div>
                @endif
            </div>
        </section>
<section class="sfpe-reimbursement-card sf-card">
            <div class="sf-card-head">
                <h2 class="sf-card-title">Submit Reimbursement Claim</h2>
                <div class="sf-card-subtitle">Use this only for expenses you paid from your own pocket during the pre-employment stage.</div>
            </div>

            <div class="sf-card-body">
                <form method="POST" action="{{ route('pre-employment.portal.reimbursement', $preEmployment->portal_token) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="sf-field-card">
                        <div class="sf-field-top">
                            <div>
                                <div class="sf-field-title">Expense Details</div>
                                <div class="sf-instructions">Upload a receipt or proof if available. The claim will be reviewed by Sada Fezzan before approval or payment.</div>
                            </div>
                            <span class="sf-status-badge waiting">Pending Review</span>
                        </div>

                        <div class="sf-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr));">
                            <div>
                                <div class="sf-label">Expense Title <span class="sf-required">*</span></div>
                                <input type="text" name="expense_title" value="{{ old('expense_title') }}" placeholder="Ticket, visa, medical, hotel..." required>
                                @error('expense_title')
                                    <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <div class="sf-label">Category <span class="sf-required">*</span></div>
                                <select name="expense_category" required>
                                    @php($selectedCategory = old('expense_category', 'other'))
                                    <option value="ticket" @selected($selectedCategory === 'ticket')>Ticket</option>
                                    <option value="visa" @selected($selectedCategory === 'visa')>Visa</option>
                                    <option value="hotel" @selected($selectedCategory === 'hotel')>Hotel</option>
                                    <option value="medical" @selected($selectedCategory === 'medical')>Medical / Health Certificate</option>
                                    <option value="training" @selected($selectedCategory === 'training')>Training Certificate</option>
                                    <option value="transport" @selected($selectedCategory === 'transport')>Transport</option>
                                    <option value="other" @selected($selectedCategory === 'other')>Other</option>
                                </select>
                                @error('expense_category')
                                    <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <div class="sf-label">Amount <span class="sf-required">*</span></div>
                                <input type="number" step="0.01" min="0.01" name="expense_amount" value="{{ old('expense_amount') }}" required>
                                @error('expense_amount')
                                    <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <div class="sf-label">Currency <span class="sf-required">*</span></div>
                                @php($selectedCurrency = old('expense_currency', 'EUR'))
                                <select name="expense_currency" required>
                                    <option value="EUR" @selected($selectedCurrency === 'EUR')>EUR</option>
                                    <option value="USD" @selected($selectedCurrency === 'USD')>USD</option>
                                    <option value="LYD" @selected($selectedCurrency === 'LYD')>LYD</option>
                                    <option value="GBP" @selected($selectedCurrency === 'GBP')>GBP</option>
                                </select>
                                @error('expense_currency')
                                    <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <div class="sf-label">Expense Date <span class="sf-required">*</span></div>
                                <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required>
                                @error('expense_date')
                                    <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <div class="sf-label">Receipt / Proof</div>
                                <input type="file" name="receipt_file">
                                @error('receipt_file')
                                    <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div style="margin-top: 16px;">
                            <div class="sf-label">Notes</div>
                            <textarea rows="4" name="expense_notes" placeholder="Add any explanation needed for this claim...">{{ old('expense_notes') }}</textarea>
                            @error('expense_notes')
                                <div style="margin-top: 8px; color: #dc2626; font-weight: 800;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sf-submit-row">
                            <button type="submit" class="sf-btn sf-btn-primary">Submit Reimbursement Claim</button>
                        </div>
                    </div>
                </form>

                @if($reimbursementClaims->isNotEmpty())
                    <div class="sf-field-card" style="margin-top: 18px;">
                        <div class="sf-field-title">Submitted Claims</div>
                        <div class="sf-instructions">Current status of reimbursement claims submitted during pre-employment.</div>

                        <div class="sf-existing-files">
                            @foreach($reimbursementClaims as $claim)
                                <div class="sf-file-row">
                                    <div>
                                        <strong>{{ $claim->title ?: 'Reimbursement Claim' }}</strong>
                                        <span>
                                            {{ ucfirst(str_replace('_', ' ', $claim->category ?: 'expense')) }}
                                            · {{ number_format((float) $claim->amount, 2) }} {{ $claim->currency ?: 'EUR' }}
                                            · {{ $claim->expense_date?->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <div class="sf-file-actions">
                                        <span class="sf-status-badge {{ ($claim->reimbursement_status ?? '') === 'paid' ? '' : 'waiting' }}">
                                            {{ ucfirst(str_replace('_', ' ', $claim->reimbursement_status ?: 'pending')) }}
                                        </span>

                                        @if($claim->attachment_path && Storage::disk('public')->exists($claim->attachment_path))
                                            <a class="sf-btn sf-btn-yellow" href="{{ Storage::disk('public')->url($claim->attachment_path) }}" target="_blank" rel="noopener">Receipt</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

    </main>

<style>
/* SADA FINAL PORTAL DYNAMIC REIMBURSEMENT FIELDS */
.sf-rmb-dynamic-fields {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-top: 4px;
}

.sf-rmb-dynamic-field label {
    display: block;
    margin-bottom: 8px;
    color: #64748b;
    font-size: 12px;
    font-weight: 950;
    letter-spacing: .18em;
    text-transform: uppercase;
}

.sf-rmb-dynamic-field input {
    width: 100%;
    min-height: 54px;
    border-radius: 18px;
    border: 1px solid rgba(15, 23, 42, .10);
    background: rgba(255, 255, 255, .92);
    padding: 14px 16px;
    color: #0f172a;
    font-weight: 850;
    outline: none;
}

.sf-rmb-dynamic-help {
    margin-top: 8px;
    color: #64748b;
    font-size: 12px;
    font-weight: 750;
}

@media (max-width: 800px) {
    .sf-rmb-dynamic-fields {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
/* SADA FINAL PORTAL DYNAMIC REIMBURSEMENT FIELDS */
(function () {
    function normalize(value) {
        return String(value || '').toLowerCase().trim();
    }

    function applyDynamicFields(form) {
        var category = form.querySelector('select[name="expense_category"], select[name="category"]');
        var wrap = form.querySelector('[data-rmb-dynamic-fields]');

        if (!category || !wrap) {
            return;
        }

        var fromLabel = form.querySelector('[data-rmb-from-label]');
        var toLabel = form.querySelector('[data-rmb-to-label]');
        var help = form.querySelector('[data-rmb-help]');

        function update() {
            var value = normalize(category.value);
            var text = normalize(category.options && category.selectedIndex >= 0 ? category.options[category.selectedIndex].text : '');
            var key = value + ' ' + text;

            var show = false;
            var from = 'Incurred From';
            var to = 'Incurred To';
            var note = '';

            if (key.indexOf('hotel') !== -1) {
                show = true;
                from = 'Check-in Date';
                to = 'Check-out Date';
                note = 'Used for hotel stay period and calendar tracking.';
            } else if (key.indexOf('ticket') !== -1 || key.indexOf('travel') !== -1 || key.indexOf('flight') !== -1) {
                show = true;
                from = 'Departure Date';
                to = 'Return Date';
                note = 'Leave return date empty for one-way ticket.';
            } else if (key.indexOf('visa') !== -1) {
                show = true;
                from = 'Submission Date';
                to = 'Expiry / Follow-up Date';
                note = 'Use expiry or follow-up date if available.';
            } else if (key.indexOf('medical') !== -1 || key.indexOf('clinic') !== -1) {
                show = true;
                from = 'Medical Visit Date';
                to = 'Expiry / Follow-up Date';
                note = 'Use follow-up or expiry date if available.';
            }

            wrap.style.display = show ? 'grid' : 'none';

            if (fromLabel) fromLabel.textContent = from;
            if (toLabel) toLabel.textContent = to;
            if (help) help.textContent = note;
        }

        category.removeEventListener('change', update);
        category.addEventListener('change', update);
        update();
    }

    function init() {
        document.querySelectorAll('form').forEach(applyDynamicFields);
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('livewire:navigated', init);
    setTimeout(init, 300);
})();
</script>


<style>
/* SADA HOTFIX 2026-05-07: dynamic reimbursement fields inside claim box */
.sf-rmb-dynamic-fields {
    grid-column: 1 / -1 !important;
    width: 100% !important;
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.sf-rmb-dynamic-inner {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 16px !important;
    width: 100% !important;
}

.sf-rmb-dynamic-field {
    min-width: 0 !important;
}

.sf-rmb-dynamic-field .sf-rmb-input {
    width: 100% !important;
}

.sf-rmb-help {
    margin-top: 8px !important;
    color: #64748b !important;
    font-size: 12px !important;
    font-weight: 800 !important;
}

@media (max-width: 760px) {
    .sf-rmb-dynamic-inner {
        grid-template-columns: 1fr !important;
    }
}
</style>




</body>
</html>

<style id="sfpe-public-salary-agreement-polish">
    .sfpe-salary-agreement-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 18px !important;
        align-items: stretch !important;
    }

    .sfpe-salary-agreement-card {
        position: relative !important;
        overflow: hidden !important;
        min-height: 128px !important;
        border-radius: 26px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 38%),
            rgba(248, 250, 252, .94) !important;
        border: 1px solid rgba(15, 23, 42, .09) !important;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .045) !important;
    }

    .sfpe-salary-agreement-card::before {
        content: "" !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 4px !important;
        background: linear-gradient(90deg, #14b8a6, #2563eb) !important;
        opacity: .75 !important;
    }

    .sfpe-salary-rate-card::before {
        background: linear-gradient(90deg, #fbbf24, #14b8a6) !important;
    }

    @media (max-width: 900px) {
        .sfpe-salary-agreement-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<style id="sfpe-public-salary-agreement-final-fix">
    .sfpe-salary-agreement-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 18px !important;
        align-items: stretch !important;
    }

    .sfpe-salary-agreement-card {
        min-height: 112px !important;
        border-radius: 24px !important;
        padding: 22px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 38%),
            rgba(248, 250, 252, .96) !important;
        border: 1px solid rgba(15, 23, 42, .09) !important;
        box-shadow: 0 14px 30px rgba(15, 23, 42, .04) !important;
    }

    .sfpe-salary-agreement-card > div:first-child {
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
        margin-bottom: 10px !important;
    }

    .sfpe-salary-agreement-card > div:last-child {
        color: #0f172a !important;
        font-size: 24px !important;
        font-weight: 950 !important;
        line-height: 1.1 !important;
    }

    @media (max-width: 900px) {
        .sfpe-salary-agreement-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>


<style id="sfpe-reimbursement-form-field-polish">
    /*
     * STYLE ONLY — Pre-Employment public reimbursement form.
     * Makes title, amount, date, selects, textarea, and receipt upload visually consistent.
     */
    .sfpe-reimbursement-card input[type="text"],
    .sfpe-reimbursement-card input[type="number"],
    .sfpe-reimbursement-card input[type="date"],
    .sfpe-reimbursement-card input[type="file"],
    .sfpe-reimbursement-card select,
    .sfpe-reimbursement-card textarea {
        width: 100% !important;
        min-height: 58px !important;
        border-radius: 22px !important;
        border: 1px solid rgba(15, 23, 42, .14) !important;
        background: rgba(255, 255, 255, .94) !important;
        color: #0f172a !important;
        padding: 0 20px !important;
        font-size: 16px !important;
        font-weight: 800 !important;
        line-height: 1.2 !important;
        outline: none !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.75), 0 8px 18px rgba(15,23,42,.035) !important;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease !important;
    }

    .sfpe-reimbursement-card textarea {
        min-height: 128px !important;
        padding: 18px 20px !important;
        resize: vertical !important;
        line-height: 1.55 !important;
    }

    .sfpe-reimbursement-card input[type="file"] {
        display: flex !important;
        align-items: center !important;
        padding: 12px 14px !important;
        background: rgba(248, 250, 252, .96) !important;
        cursor: pointer !important;
    }

    .sfpe-reimbursement-card input[type="file"]::file-selector-button {
        min-height: 36px !important;
        margin-right: 12px !important;
        border: 0 !important;
        border-radius: 999px !important;
        background: #0f172a !important;
        color: #ffffff !important;
        padding: 0 16px !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        cursor: pointer !important;
    }

    .sfpe-reimbursement-card input[type="text"]:focus,
    .sfpe-reimbursement-card input[type="number"]:focus,
    .sfpe-reimbursement-card input[type="date"]:focus,
    .sfpe-reimbursement-card input[type="file"]:focus,
    .sfpe-reimbursement-card select:focus,
    .sfpe-reimbursement-card textarea:focus {
        border-color: rgba(20, 184, 166, .70) !important;
        box-shadow: 0 0 0 4px rgba(20, 184, 166, .12), 0 12px 24px rgba(15,23,42,.06) !important;
        background: #ffffff !important;
    }

    .sfpe-reimbursement-card input::placeholder,
    .sfpe-reimbursement-card textarea::placeholder {
        color: #94a3b8 !important;
        font-weight: 750 !important;
    }

    .sfpe-reimbursement-card select {
        appearance: auto !important;
        cursor: pointer !important;
    }

    .sfpe-reimbursement-card .sf-label {
        margin-bottom: 10px !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .18em !important;
        text-transform: uppercase !important;
    }

    .sfpe-reimbursement-card .sf-btn,
    .sfpe-reimbursement-card button[type="submit"] {
        min-height: 54px !important;
        border-radius: 999px !important;
        padding: 0 24px !important;
        font-size: 15px !important;
        font-weight: 950 !important;
        box-shadow: 0 16px 32px rgba(15, 23, 42, .14) !important;
    }

    @media (max-width: 860px) {
        .sfpe-reimbursement-card input[type="text"],
        .sfpe-reimbursement-card input[type="number"],
        .sfpe-reimbursement-card input[type="date"],
        .sfpe-reimbursement-card input[type="file"],
        .sfpe-reimbursement-card select,
        .sfpe-reimbursement-card textarea {
            min-height: 56px !important;
            border-radius: 20px !important;
            font-size: 15px !important;
        }
    }
</style>


<style id="sfpe-public-reimbursement-claims-list-style">
    .sfpe-public-claims-card {
        margin-top: 22px !important;
    }

    .sfpe-public-claims-list {
        display: grid !important;
        gap: 14px !important;
    }

    .sfpe-public-claim-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 18px !important;
        padding: 18px 20px !important;
        border-radius: 24px !important;
        border: 1px solid rgba(15, 23, 42, .09) !important;
        background:
            radial-gradient(circle at top right, rgba(20, 184, 166, .08), transparent 36%),
            rgba(248, 250, 252, .94) !important;
        box-shadow: 0 12px 26px rgba(15, 23, 42, .045) !important;
    }

    .sfpe-public-claim-main {
        min-width: 0 !important;
    }

    .sfpe-public-claim-title {
        color: #0f172a !important;
        font-size: 16px !important;
        line-height: 1.25 !important;
        font-weight: 950 !important;
        letter-spacing: -.025em !important;
        word-break: break-word !important;
    }

    .sfpe-public-claim-meta {
        margin-top: 6px !important;
        color: #64748b !important;
        font-size: 12px !important;
        line-height: 1.45 !important;
        font-weight: 750 !important;
    }

    .sfpe-public-claim-side {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        flex: 0 0 auto !important;
    }

    .sfpe-public-claim-amount {
        min-height: 38px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 14px !important;
        border-radius: 999px !important;
        background: #ffffff !important;
        border: 1px solid rgba(15, 23, 42, .09) !important;
        color: #0f172a !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        white-space: nowrap !important;
    }

    .sfpe-public-claim-status {
        min-height: 38px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 14px !important;
        border-radius: 999px !important;
        border: 1px solid transparent !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        white-space: nowrap !important;
    }

    .sfpe-public-claim-status.is-pending {
        background: #fff7ed !important;
        color: #9a3412 !important;
        border-color: rgba(249, 115, 22, .28) !important;
    }

    .sfpe-public-claim-status.is-approved {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
        border-color: rgba(37, 99, 235, .22) !important;
    }

    .sfpe-public-claim-status.is-paid {
        background: #ecfdf5 !important;
        color: #047857 !important;
        border-color: rgba(16, 185, 129, .26) !important;
    }

    .sfpe-public-claim-status.is-rejected {
        background: #fff1f2 !important;
        color: #be123c !important;
        border-color: rgba(244, 63, 94, .24) !important;
    }

    @media (max-width: 760px) {
        .sfpe-public-claim-row {
            align-items: flex-start !important;
            flex-direction: column !important;
        }

        .sfpe-public-claim-side {
            width: 100% !important;
            justify-content: flex-start !important;
        }
    }
</style>



<style>
/* SADA FINAL 2026-05-07: reimbursement dynamic fields inside form box */
.sf-rmb-dynamic-fields {
    grid-column: 1 / -1 !important;
    width: 100% !important;
    margin: 0 !important;
}

.sf-rmb-dynamic-inner {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 16px !important;
    width: 100% !important;
}

.sf-rmb-dynamic-field {
    min-width: 0 !important;
}

.sf-rmb-dynamic-field[style*="display: none"] {
    display: none !important;
}

.sf-rmb-dynamic-field .sf-rmb-input {
    width: 100% !important;
}

.sf-rmb-help {
    margin-top: 8px !important;
    color: #64748b !important;
    font-size: 12px !important;
    font-weight: 800 !important;
}

@media (max-width: 760px) {
    .sf-rmb-dynamic-inner {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
(function () {
    function norm(v) {
        return String(v || '').toLowerCase().trim();
    }

    function hasAny(text, value, words) {
        text = norm(text);
        value = norm(value);

        return words.some(function (word) {
            word = norm(word);
            return text.includes(word) || value.includes(word);
        });
    }

    function setVisible(field, visible, required) {
        if (!field) return;

        field.style.display = visible ? '' : 'none';

        var input = field.querySelector('input, select, textarea');
        if (!input) return;

        input.required = !!(visible && required);
    }

    function initDynamicReimbursementFields() {
        document.querySelectorAll('form').forEach(function (form) {
            var wrapper = form.querySelector('[data-rmb-dynamic-fields]');
            if (!wrapper) return;

            var category = form.querySelector('select[name="expense_category"]') || form.querySelector('select[name="category"]');
            if (!category) return;

            var fields = {
                departure: wrapper.querySelector('[data-rmb-field="departure"]'),
                returnDate: wrapper.querySelector('[data-rmb-field="return"]'),
                checkin: wrapper.querySelector('[data-rmb-field="checkin"]'),
                checkout: wrapper.querySelector('[data-rmb-field="checkout"]'),
                visaIssue: wrapper.querySelector('[data-rmb-field="visa_issue"]'),
                visaExpiry: wrapper.querySelector('[data-rmb-field="visa_expiry"]'),
                medical: wrapper.querySelector('[data-rmb-field="medical"]')
            };

            function update() {
                var selectedText = category.options && category.selectedIndex >= 0
                    ? category.options[category.selectedIndex].text
                    : '';

                var selectedValue = category.value || '';

                var ticket = hasAny(selectedText, selectedValue, ['ticket', 'flight', 'travel']);
                var hotel = hasAny(selectedText, selectedValue, ['hotel', 'accommodation']);
                var visa = hasAny(selectedText, selectedValue, ['visa']);
                var medical = hasAny(selectedText, selectedValue, ['medical', 'clinic', 'hospital']);

                wrapper.style.display = (ticket || hotel || visa || medical) ? '' : 'none';

                setVisible(fields.departure, ticket, true);
                setVisible(fields.returnDate, ticket, false);

                setVisible(fields.checkin, hotel, true);
                setVisible(fields.checkout, hotel, true);

                setVisible(fields.visaIssue, visa, true);
                setVisible(fields.visaExpiry, visa, true);

                setVisible(fields.medical, medical, false);
            }

            category.addEventListener('change', update);
            update();
        });
    }

    document.addEventListener('DOMContentLoaded', initDynamicReimbursementFields);
    document.addEventListener('livewire:navigated', initDynamicReimbursementFields);
    setTimeout(initDynamicReimbursementFields, 250);
    setTimeout(initDynamicReimbursementFields, 750);
})();
</script>

