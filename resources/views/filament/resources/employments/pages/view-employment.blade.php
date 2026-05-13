<x-filament-panels::page>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,600,0,0" />

<style id="sf-rotation-edit-button-style">
    .sf-rotation-row-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .sf-rotation-edit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8 !important;
        border: 1px solid rgba(59,130,246,.22);
        font-size: 12px;
        font-weight: 900;
        text-decoration: none !important;
        white-space: nowrap;
    }

    .sf-rotation-edit-btn:hover {
        background: #dbeafe;
    }
</style>

<style>
        .fi-header,
        .fi-page-header,
        header.fi-header,
        .fi-page-header-heading,
        .fi-page-header-breadcrumbs,
        .fi-page-header-actions,
        .fi-page-header-ctas {
            display: none !important;
        }

        .sf-employment-shell {
            width: min(100%, 1280px);
            margin: 0 auto 60px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .sf-employment-hero {
            border-radius: 32px;
            padding: 30px;
            color: #fff;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .16), transparent 34%),
                linear-gradient(135deg, #111827, #1f2937 62%, #234b74);
            border: 1px solid rgba(148, 163, 184, .22);
            box-shadow: 0 22px 60px rgba(15, 23, 42, .16);
        }

        .sf-employment-head {
            display: grid;
            grid-template-columns: minmax(260px, .9fr) minmax(420px, 1.1fr);
            gap: 22px;
            align-items: start;
        }

        .sf-kicker {
            font-size: 13px;
            font-weight: 850;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .sf-title {
            margin: 0;
            font-size: clamp(42px, 5vw, 72px);
            line-height: .94;
            letter-spacing: -.065em;
            font-weight: 950;
            color: #fff;
        }

        .sf-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .sf-btn {
            border: 0;
            cursor: pointer;
            border-radius: 999px;
            min-height: 42px;
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 900;
            color: #fff;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.14);
            white-space: nowrap;
        }

        .sf-btn-blue { background: #2563eb; }
        .sf-btn-yellow { background: #f59e0b; color: #111827; }
        .sf-btn-red { background: #dc2626; }
        .sf-btn-gray { background: rgba(255,255,255,.14); }

        .sf-summary-grid,
        .sf-finance-grid,
        .sf-block-grid {
            display: grid;
            gap: 16px;
        }

        .sf-summary-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-top: 24px;
        }

        .sf-finance-grid,
        .sf-block-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .sf-summary-card {
            border-radius: 22px;
            padding: 18px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.08);
        }

        .sf-label {
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 9px;
        }

        .sf-value {
            font-size: 24px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.04em;
            color: #fff;
        }

        .sf-meta {
            margin-top: 8px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 650;
        }

        .sf-finance-card,
        .sf-block,
        .sf-content-card {
            border-radius: 30px;
            padding: 22px;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
                rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 16px 42px rgba(15,23,42,.07);
        }

        .dark .sf-finance-card,
        .dark .sf-block,
        .dark .sf-content-card {
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
                rgba(15,23,42,.72);
            border-color: rgba(148,163,184,.18);
            box-shadow: 0 18px 46px rgba(0,0,0,.18);
        }

        .sf-finance-card {
            min-height: 160px;
        }

        .sf-finance-card::before {
            content: "";
            display: block;
            height: 5px;
            border-radius: 999px;
            margin-bottom: 18px;
            background: linear-gradient(90deg, #22d3ee, #2563eb);
        }

        .sf-finance-title,
        .sf-block-title {
            font-size: 18px;
            font-weight: 950;
            color: #234b74;
            letter-spacing: -.04em;
        }

        .dark .sf-finance-title,
        .dark .sf-block-title {
            color: #fff;
        }

        .sf-finance-number {
            margin-top: 14px;
            color: #234b74;
            font-size: clamp(30px, 3.4vw, 52px);
            line-height: .96;
            font-weight: 950;
            letter-spacing: -.07em;
        }

        .dark .sf-finance-number {
            color: #fff;
        }

        .sf-block-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .sf-block-subtitle {
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
            font-weight: 650;
        }

        .dark .sf-block-subtitle {
            color: #94a3b8;
        }

        .sf-mini-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .sf-mini-btn {
            border: 0;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            background: #e0f2fe;
            color: #0f172a;
        }

        .sf-list {
            display: grid;
            gap: 10px;
        }

        .sf-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border-radius: 18px;
            padding: 12px 14px;
            background: rgba(248,250,252,.88);
            border: 1px solid rgba(15,23,42,.08);
        }

        .dark .sf-row {
            background: rgba(15,23,42,.36);
            border-color: rgba(148,163,184,.16);
        }

        .sf-row strong {
            display: block;
            color: #0f172a;
            font-size: 14px;
            font-weight: 900;
        }

        .dark .sf-row strong {
            color: #fff;
        }

        .sf-row span {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 12px;
            font-weight: 650;
        }

        .dark .sf-row span {
            color: #94a3b8;
        }

        .sf-pill {
            border-radius: 999px;
            padding: 7px 10px;
            background: #e0f2fe;
            color: #0f172a;
            font-size: 11px;
            font-weight: 900;
            white-space: nowrap;
            text-decoration: none;
        }

        .sf-empty {
            border-radius: 18px;
            padding: 18px;
            border: 1px dashed rgba(148,163,184,.30);
            color: #64748b;
            font-size: 13px;
        }

        .dark .sf-empty {
            color: #94a3b8;
        }

        @media (max-width: 1450px) {
            .sf-employment-head {
                grid-template-columns: 1fr;
            }

            .sf-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 1100px) {
            .sf-summary-grid,
            .sf-finance-grid,
            .sf-block-grid {
                grid-template-columns: 1fr;
            }
        }

        .sf-tabs-card {
            border-radius: 30px;
            padding: 22px;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
                rgba(255,255,255,.94);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 16px 42px rgba(15,23,42,.07);
        }

        .dark .sf-tabs-card {
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
                rgba(15,23,42,.72);
            border-color: rgba(148,163,184,.18);
            box-shadow: 0 18px 46px rgba(0,0,0,.18);
        }

        .sf-tabs-title {
            margin: 0 0 16px;
            color: #234b74;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .dark .sf-tabs-title {
            color: #fff;
        }

        .sf-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
        }

        .sf-tabs input {
            display: none;
        }

        .sf-tabs label {
            cursor: pointer;
            border-radius: 999px;
            padding: 11px 16px;
            background: #eef6ff;
            color: #234b74;
            font-size: 13px;
            font-weight: 900;
            border: 1px solid rgba(35,75,116,.10);
        }

        .dark .sf-tabs label {
            background: rgba(255,255,255,.08);
            color: #e2e8f0;
            border-color: rgba(255,255,255,.10);
        }

        #sf-tab-rotations:checked ~ .sf-tabs label[for="sf-tab-rotations"],
        #sf-tab-expenses:checked ~ .sf-tabs label[for="sf-tab-expenses"],
        #sf-tab-salary:checked ~ .sf-tabs label[for="sf-tab-salary"],
        #sf-tab-files:checked ~ .sf-tabs label[for="sf-tab-files"] {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 12px 26px rgba(37,99,235,.18);
        }

        .sf-tab-panel {
            display: none;
        }

        #sf-tab-rotations:checked ~ .sf-tab-content .sf-panel-rotations,
        #sf-tab-expenses:checked ~ .sf-tab-content .sf-panel-expenses,
        #sf-tab-salary:checked ~ .sf-tab-content .sf-panel-salary,
        #sf-tab-files:checked ~ .sf-tab-content .sf-panel-files {
            display: block;
        }

        .sf-table-wrap {
            overflow-x: auto;
            border-radius: 22px;
            border: 1px solid rgba(15,23,42,.08);
        }

        .dark .sf-table-wrap {
            border-color: rgba(148,163,184,.16);
        }

        .sf-ops-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .sf-ops-table th {
            text-align: left;
            padding: 14px 16px;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: #64748b;
            background: rgba(248,250,252,.92);
            border-bottom: 1px solid rgba(15,23,42,.08);
        }

        .dark .sf-ops-table th {
            color: #94a3b8;
            background: rgba(15,23,42,.55);
            border-bottom-color: rgba(148,163,184,.16);
        }

        .sf-ops-table td {
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 1px solid rgba(15,23,42,.06);
            vertical-align: top;
        }

        .dark .sf-ops-table td {
            color: #e2e8f0;
            border-bottom-color: rgba(148,163,184,.12);
        }

        .sf-ops-table tr:last-child td {
            border-bottom: 0;
        }

        .sf-status-pill {
            display: inline-flex;
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 11px;
            font-weight: 950;
            background: #e0f2fe;
            color: #075985;
            white-space: nowrap;
        }

        .sf-empty-panel {
            border-radius: 22px;
            padding: 24px;
            color: #64748b;
            background: rgba(248,250,252,.75);
            border: 1px dashed rgba(15,23,42,.16);
            font-size: 14px;
            font-weight: 700;
        }

        .dark .sf-empty-panel {
            color: #94a3b8;
            background: rgba(15,23,42,.35);
            border-color: rgba(148,163,184,.24);
        }


        .sf-pager {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
        }

        .sf-pager button {
            border: 0;
            border-radius: 999px;
            padding: 9px 13px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            color: #0f172a;
            background: #e0f2fe;
        }

        .sf-pager button:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .sf-pager span {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
        }

        .dark .sf-pager span {
            color: #94a3b8;
        }

    </style>

    @php

    $financialSalarySlips = collect($record->salarySlips ?? [])
        ->filter(fn ($slip) => in_array($slip->status, ['sent_to_bank', 'paid'], true));

    $draftSalarySlips = collect($record->salarySlips ?? [])
        ->filter(fn ($slip) => $slip->status === 'draft');
        $record->loadMissing(['files', 'rotations.financeExpenses', 'financeExpenses', 'salarySlips']);

        $employmentStatus = (string) ($record->status ?? 'active');
        $currentWorkStatus = (string) ($record->current_work_status ?? 'pending_mobilization');
        $rotationStatus = (string) ($record->rotation_status ?? 'scheduled');

        $isOfficeEmployee = (string) ($record->employee_category ?? 'operational') === 'office';
        $erpLoginUser = null;

        if ($isOfficeEmployee && filled($record->employee_email)) {
            $erpLoginUser = \App\Models\User::query()
                ->where('email', strtolower(trim($record->employee_email)))
                ->first();
        }

        $costSnapshot = method_exists($record, 'employeeCostSnapshot') ? $record->employeeCostSnapshot() : [];
        $salaryCurrency = $record->salary_currency ?: 'EUR';
        $currencySnapshot = $costSnapshot[$salaryCurrency] ?? [];

        $financeProfile = $record->currentFinanceProfile ?? null;

        $dailyRate = $financeProfile?->daily_rate ?? $record->daily_rate ?? 0;
        $clientBillingRate = $financeProfile?->client_billing_rate ?? 0;
        $payoutCurrency = $financeProfile?->payout_currency ?? $record->salary_currency ?? 'EUR';
        $billingCurrency = $financeProfile?->client_billing_currency ?? $payoutCurrency;

        $salaryCost = $currencySnapshot['salary_cost'] ?? 0;
        $revenue = $currencySnapshot['revenue'] ?? 0;
        $otherExpenses = $currencySnapshot['other_employment_cost'] ?? ($record->financeExpenses ?? collect())->sum(fn ($expense) => (float) ($expense->amount ?? $expense->total_amount ?? 0));
        $netResult = $currencySnapshot['net'] ?? ((float) $revenue - (float) $salaryCost - (float) $otherExpenses);

        $clientName = $record->client_name ?: ($record->job?->project?->client?->name ?: '-');
        $projectName = $record->project_name ?: ($record->job?->project?->name ?: '-');

        $currentRotation = $record->currentRotation ?: $record->rotations->sortByDesc(function ($item) {
            return optional($item->from_date)->timestamp ?? 0;
        })->first();

        $allowedFileCategories = [
            'cv', 'candidate_upload', 'passport', 'visa', 'medical', 'personal_photo',
            'certificate', 'caf', 'gl', 'contract', 'rotation_document',
            'travel_request', 'ticket', 'internal_document',
        ];

        $hiddenFileKeywords = ['expense', 'invoice', 'receipt', 'payment', 'bank', 'treasury', 'salary', 'payroll', 'voucher', 'cost', 'finance'];

        $officialFiles = $record->files
            ->filter(fn ($file) => (bool) ($file->is_current ?? true))
            ->sortByDesc(function ($file) {
                return optional($file->updated_at)->timestamp
                    ?: optional($file->created_at)->timestamp
                    ?: (int) ($file->id ?? 0);
            })
            ->take(6);

        $expenses = $record->financeExpenses ?? collect();
        $expensesCount = $expenses->count();

        $rotationsList = $record->rotations
            ->sortByDesc('from_date')
            ->take(50);

        $salarySlipsList = ($record->salarySlips ?? collect())
            ->sortByDesc('id')
            ->take(50);

        $filesList = $record->files
            ->sortByDesc(function ($file) {
                return optional($file->updated_at)->timestamp
                    ?: optional($file->created_at)->timestamp
                    ?: (int) ($file->id ?? 0);
            })
            ->take(50);

        $expensesList = $expenses
            ->sortByDesc(function ($expense) {
                return optional($expense->expense_date ?? $expense->created_at)->timestamp ?? 0;
            })
            ->take(50);

        $erpUser = auth()->user();

        $canUploadEmploymentFile = (bool) $erpUser?->canErp('employments', 'upload_file');
        $canRequestEmploymentFile = (bool) $erpUser?->canErp('employments', 'request_file');
        $canViewOfficialFiles = (bool) ($erpUser?->canErp('employments', 'view') || $canUploadEmploymentFile || $canRequestEmploymentFile);

        $canAddRotation = (bool) $erpUser?->canErp('employments', 'rotation_add');
        $canEditRotation = (bool) $erpUser?->canErp('employments', 'rotation_edit');
        $canDeleteRotation = (bool) $erpUser?->canErp('employments', 'rotation_delete');
        $canPrintRotationHistory = (bool) $erpUser?->canErp('employments', 'rotation_print');
        $canViewTravelTickets = (bool) ($erpUser?->canErp('travel_tickets', 'view') || $erpUser?->canErp('travel_tickets', 'open_file'));
        $canOpenTravelTicket = (bool) ($erpUser?->canErp('travel_tickets', 'open_file') || $erpUser?->canErp('travel_tickets', 'view'));
        $canViewRotations = (bool) ($canAddRotation || $canEditRotation || $canDeleteRotation || $canPrintRotationHistory || $canViewTravelTickets);

        $canViewFinanceProfile = (bool) $erpUser?->canErp('employments', 'finance_profile_view');
        $canAddExpense = (bool) $erpUser?->canErp('employments', 'add_expense');
        $canViewExpenses = (bool) $erpUser?->canErp('finance_expenses', 'view');

        $canViewSalary = (bool) $erpUser?->canErp('salary_slips', 'view');
        $canGenerateSalarySlip = (bool) $erpUser?->canErp('employments', 'generate_salary_slip');
        $canPrintProfile = (bool) $erpUser?->canErp('employments', 'print_profile');
        $canEditEmployment = (bool) $erpUser?->canErp('employments', 'edit');

        $canViewFinanceNumbers = (bool) ($canViewFinanceProfile || $canViewSalary || $canViewExpenses);
        $canViewEmployeeCost = (bool) ($canViewSalary || $canViewExpenses);

        if ($isOfficeEmployee) {
            $canViewFinanceNumbers = false;
            $canViewEmployeeCost = false;
            $canViewRotations = false;
            $canViewExpenses = false;
            $canAddRotation = false;
            $canAddExpense = false;
            $canPrintRotationHistory = false;
        }

        $canViewPortalAccess = (bool) (
            $erpUser?->canErp('employments', 'portal_preview')
            || $erpUser?->canErp('employments', 'portal_send_password')
            || $erpUser?->canErp('employments', 'portal_reset_password')
            || $erpUser?->canErp('employments', 'portal_enable')
            || $erpUser?->canErp('employments', 'portal_disable')
        );

        $availableOpsTabs = collect([
            'rotations' => $canViewRotations,
            'expenses' => $canViewExpenses,
            'salary' => $canViewSalary,
            'files' => $canViewOfficialFiles,
        ])->filter()->keys();

        $defaultOpsTab = $availableOpsTabs->first();
    @endphp

    <div class="sf-employment-shell">
        <section class="sf-employment-hero">
            <div class="sf-employment-head">
                <div>
                    <div class="sf-kicker">Employment Profile</div>
                    <h1 class="sf-title">{{ $record->employee_name ?: 'Employee' }}</h1>
                </div>

                <div class="sf-actions">
                    @if($canAddRotation)
                        <button type="button" wire:click="mountAction('addRotation')" class="sf-btn sf-btn-blue"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 7h11l-3-3 1.4-1.4L21.8 8l-5.4 5.4L15 12l3-3H7V7Zm10 10H6l3 3-1.4 1.4L2.2 16l5.4-5.4L9 12l-3 3h11v2Z"/></svg></span><span>Add Rotation</span></button>
                    @endif
                    @if($canUploadEmploymentFile)
                        <button type="button" wire:click="mountAction('uploadEmploymentFile')" class="sf-btn sf-btn-blue"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 22q-2.5 0-4.25-1.75T6 16V6.5q0-1.875 1.313-3.188T10.5 2q1.875 0 3.188 1.313T15 6.5V16q0 1.25-.875 2.125T12 19q-1.25 0-2.125-.875T9 16V7h2v9q0 .425.288.713T12 17q.425 0 .713-.288T13 16V6.5q0-1.05-.725-1.775T10.5 4q-1.05 0-1.775.725T8 6.5V16q0 1.65 1.175 2.825T12 20q1.65 0 2.825-1.175T16 16V7h2v9q0 2.5-1.75 4.25T12 22Z"/></svg></span><span>Upload File</span></button>
                    @endif
                    @if($canRequestEmploymentFile)
                        <button type="button" wire:click="mountAction('requestCandidateFile')" class="sf-btn sf-btn-yellow"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 16 7 11l1.4-1.45 2.6 2.6V4h2v8.15l2.6-2.6L17 11l-5 5ZM5 20q-.825 0-1.413-.588T3 18v-3h2v3h14v-3h2v3q0 .825-.588 1.413T19 20H5Z"/></svg></span><span>Request File</span></button>
                    @endif
                    @if($canAddExpense)
                        <button type="button" wire:click="mountAction('addExpense')" class="sf-btn sf-btn-red"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 18V6h18v12H3Zm2-2h14V8H5v8Zm2-1q.825 0 1.413-.588T9 13q0-.825-.588-1.413T7 11q-.825 0-1.413.588T5 13q0 .825.588 1.413T7 15Zm10 0h1v-4h-6v1h5v3ZM5 8v8V8Z"/></svg></span><span>Add Expense</span></button>
                    @endif
                    @if($canViewFinanceProfile)
                        <button type="button" wire:click="mountAction('viewCurrentFinanceProfile')" class="sf-btn sf-btn-gray"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 20q-.825 0-1.413-.588T2 18V6q0-.825.588-1.413T4 4h16v4h-2V6H4v12h16v-4h2v4q0 .825-.588 1.413T20 20H4Zm10-4q-.825 0-1.413-.588T12 14v-4q0-.825.588-1.413T14 8h8v8h-8Zm0-2h6v-4h-6v4Zm3-1q.425 0 .713-.288T18 12q0-.425-.288-.713T17 11q-.425 0-.713.288T16 12q0 .425.288.713T17 13Z"/></svg></span><span>Finance Profile</span></button>
                    @endif
                    @if($canGenerateSalarySlip)
                        <button type="button" wire:click="mountAction('generateSalarySlip')" class="sf-btn sf-btn-yellow"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 22V2l1.5 1.5L9 2l1.5 1.5L12 2l1.5 1.5L15 2l1.5 1.5L18 2v20l-1.5-1.5L15 22l-1.5-1.5L12 22l-1.5-1.5L9 22l-1.5-1.5L6 22Zm3-6h6v-2H9v2Zm0-4h6v-2H9v2Zm0-4h6V6H9v2Z"/></svg></span><span>Generate Salary Slip</span></button>
                    @endif
                    @if($canPrintProfile)
                        <button type="button" wire:click="mountAction('printProfile')" class="sf-btn sf-btn-gray"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 19h12v-4H6v4Zm12-10V5H6v4H4V3h16v6h-2ZM4 17q-.825 0-1.413-.588T2 15v-4q0-.825.588-1.413T4 9h16q.825 0 1.413.588T22 11v4q0 .825-.588 1.413T20 17h-2v-2h2v-4H4v4h2v2H4Zm14-3.5q.425 0 .713-.288T19 12.5q0-.425-.288-.713T18 11.5q-.425 0-.713.288T17 12.5q0 .425.288.713T18 13.5Z"/></svg></span><span>Print Profile</span></button>
                    @endif
                    @if($canPrintRotationHistory)
                        <button type="button" wire:click="mountAction('printRotationHistory')" class="sf-btn sf-btn-gray"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 21q-3.45 0-6.012-2.287T3.05 13H5.1q.35 2.6 2.313 4.3T12 19q2.925 0 4.963-2.038T19 12q0-2.925-2.038-4.963T12 5q-1.725 0-3.225.8T6.25 8H10v2H3V3h2v3.35q1.275-1.6 3.113-2.475T12 3q3.75 0 6.375 2.625T21 12q0 3.75-2.625 6.375T12 21Zm2.8-4.8L11 12.4V7h2v4.6l3.2 3.2-1.4 1.4Z"/></svg></span><span>Print Rotation History</span></button>
                    @endif
                    @if($canEditEmployment)
                        <a href="{{ \App\Filament\Resources\Employments\EmploymentResource::getUrl('edit', ['record' => $record]) }}" class="sf-btn sf-btn-gray" style="text-decoration:none;"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 19h1.4l9.85-9.85-1.4-1.4L5 17.6V19Zm-2 2v-4.25L16.25 3.5q.3-.3.675-.45T17.7 2.9q.4 0 .775.15t.675.45l1.35 1.35q.3.3.45.675t.15.775q0 .4-.15.775t-.45.675L7.25 21H3Z"/></svg></span><span>Edit</span></a>
                    @endif
                </div>
            </div>

            <div class="sf-summary-grid">
                <div class="sf-summary-card">
                    <div class="sf-label">Status</div>
                    <div class="sf-value">{{ ucfirst(str_replace('_', ' ', $employmentStatus)) }}</div>
                    <div class="sf-meta">Client: {{ $clientName }}</div>
                </div>

                <div class="sf-summary-card">
                    <div class="sf-label">Work Status</div>
                    <div class="sf-value">{{ ucfirst(str_replace('_', ' ', $currentWorkStatus)) }}</div>
                    <div class="sf-meta">Project: {{ $projectName }}</div>
                </div>

                <div class="sf-summary-card">
                    <div class="sf-label">Rotation Status</div>
                    <div class="sf-value">{{ ucfirst(str_replace('_', ' ', $rotationStatus)) }}</div>
                    <div class="sf-meta">{{ $currentRotation?->rotation_label ?: 'No current rotation' }}</div>
                </div>

                @if($canViewEmployeeCost)
                    <div class="sf-summary-card">
                        <div class="sf-label">Total Employee Cost</div>
                        <div class="sf-value">{{ number_format((float) ($currencySnapshot['total_cost'] ?? 0), 2) }} {{ $salaryCurrency }}</div>
                        <div class="sf-meta">Snapshot in {{ $salaryCurrency }}</div>
                    </div>
                @endif
            </div>
        </section>

        @if($canViewFinanceNumbers)
        <section class="sf-finance-grid">
            @if($canViewFinanceProfile)
                <div class="sf-finance-card">
                    <div class="sf-finance-title">Daily Rate</div>
                    <div class="sf-finance-number">{{ number_format((float) $dailyRate, 2) }} {{ $payoutCurrency }}</div>
                    <div class="sf-meta">Employee payout rate from current finance profile.</div>
                </div>
            @endif

            @if($canViewFinanceProfile)
                <div class="sf-finance-card">
                    <div class="sf-finance-title">Client Billing Rate</div>
                    <div class="sf-finance-number">{{ number_format((float) $clientBillingRate, 2) }} {{ $billingCurrency }}</div>
                    <div class="sf-meta">Client-facing billing rate for invoice calculations.</div>
                </div>
            @endif

            @if($canViewSalary)
                <div class="sf-finance-card">
                    <div class="sf-finance-title">Salary Cost</div>
                    <div class="sf-finance-number">{{ number_format((float) $salaryCost, 2) }} {{ $payoutCurrency }}</div>
                    <div class="sf-meta">Calculated salary cost linked to salary slips.</div>
                </div>
            @endif

            <div class="sf-finance-card">
                <div class="sf-finance-title">Revenue</div>
                <div class="sf-finance-number">{{ number_format((float) $revenue, 2) }}</div>
                <div class="sf-meta">{{ number_format((float) $revenue, 2) }} {{ $billingCurrency }} · invoice revenue snapshot.</div>
            </div>

            @if($canViewExpenses)
                <div class="sf-finance-card">
                    <div class="sf-finance-title">Other Expenses</div>
                    <div class="sf-finance-number">{{ number_format((float) $otherExpenses, 2) }}</div>
                    <div class="sf-meta">Employment-linked internal extra costs.</div>
                </div>
            @endif

            <div class="sf-finance-card">
                <div class="sf-finance-title">Net Result</div>
                <div class="sf-finance-number">{{ number_format((float) $netResult, 2) }}</div>
                <div class="sf-meta">Revenue - Salary Cost - Other Expenses.</div>
            </div>
        </section>
        @endif

        @if($isOfficeEmployee)
        <section class="sf-finance-grid">
            <div class="sf-finance-card">
                <div class="sf-finance-title">Employee Type</div>
                <div class="sf-finance-number" style="font-size:42px;">Office</div>
                <div class="sf-meta">Internal Sada Fezzan employee profile.</div>
            </div>

            <div class="sf-finance-card">
                <div class="sf-finance-title">Department</div>
                <div class="sf-finance-number" style="font-size:36px;">
                    {{ ucfirst(str_replace('_', ' ', (string) ($record->office_department ?: 'Office'))) }}
                </div>
                <div class="sf-meta">Internal department classification.</div>
            </div>

            <div class="sf-finance-card">
                <div class="sf-finance-title">Contract</div>
                <div class="sf-finance-number" style="font-size:34px;">
                    {{ $record->is_open_ended_contract ? 'Open-ended' : ucfirst(str_replace('_', ' ', (string) ($record->contract_type ?: 'Contract'))) }}
                </div>
                <div class="sf-meta">
                    Start: {{ optional($record->contract_start_date)->format('d M Y') ?: 'Not set' }}
                </div>
            </div>
        </section>
        @endif

        <section class="sf-block-grid">
            @if($canViewOfficialFiles)
            <div class="sf-block">
                <div class="sf-block-head">
                    <div>
                        <div class="sf-block-title">Latest Official Files</div>
                        <div class="sf-block-subtitle">Latest employment files, newest upload first. Portal visibility is controlled per file.</div>
                        <div class="sf-mini-actions">
                            @if($canUploadEmploymentFile)
                                <button type="button" wire:click="mountAction('uploadEmploymentFile')" class="sf-mini-btn"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 22q-2.5 0-4.25-1.75T6 16V6.5q0-1.875 1.313-3.188T10.5 2q1.875 0 3.188 1.313T15 6.5V16q0 1.25-.875 2.125T12 19q-1.25 0-2.125-.875T9 16V7h2v9q0 .425.288.713T12 17q.425 0 .713-.288T13 16V6.5q0-1.05-.725-1.775T10.5 4q-1.05 0-1.775.725T8 6.5V16q0 1.65 1.175 2.825T12 20q1.65 0 2.825-1.175T16 16V7h2v9q0 2.5-1.75 4.25T12 22Z"/></svg></span><span>Upload File</span></button>
                            @endif
                            @if($canRequestEmploymentFile)
                                <button type="button" wire:click="mountAction('requestCandidateFile')" class="sf-mini-btn"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 16 7 11l1.4-1.45 2.6 2.6V4h2v8.15l2.6-2.6L17 11l-5 5ZM5 20q-.825 0-1.413-.588T3 18v-3h2v3h14v-3h2v3q0 .825-.588 1.413T19 20H5Z"/></svg></span><span>Request File</span></button>
                            @endif
                        </div>
                    </div>
                    <div class="sf-pill" style="padding:9px 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm-1 7V3.5L18.5 9H13ZM8 13h8v2H8v-2Zm0 4h8v2H8v-2Zm0-8h3v2H8V9Z"/></svg>
                    </div>
                </div>

                <div class="sf-list">
                    @forelse($officialFiles as $file)
                        @php
                            $path = $file->file_path ?? null;
                            $fileUrl = $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
                        @endphp

                        <div class="sf-row">
                            <div>
                                <strong>{{ $file->title ?: ucfirst(str_replace('_', ' ', $file->category)) }}</strong>
                                <span>
                                    {{ strtoupper(str_replace('_', ' ', $file->category)) }} · V{{ $file->version_no ?: 1 }}
                                    · {{ optional($file->updated_at ?: $file->created_at)->format('d M Y H:i') ?: 'No date' }}
                                    · {{ (bool) ($file->is_visible_to_employee_portal ?? true) ? 'Portal Visible' : 'Employment Only' }}
                                </span>
                            </div>

                            @if($fileUrl)
                                <a class="sf-pill" href="{{ $fileUrl }}" target="_blank">Open</a>
                            @else
                                <span class="sf-pill">No File</span>
                            @endif
                        </div>
                    @empty
                        <div class="sf-empty">No current official files uploaded yet.</div>
                    @endforelse
                </div>
            </div>
            @endif

            @if($canViewRotations)
            <div class="sf-block">
                <div class="sf-block-head">
                    <div>
                        <div class="sf-block-title">Current Rotation</div>
                        <div class="sf-block-subtitle">Travel date and real work period are separate</div>
                        <div class="sf-mini-actions">
                            @if($canAddRotation)
                                <button type="button" wire:click="mountAction('addRotation')" class="sf-mini-btn"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 7h11l-3-3 1.4-1.4L21.8 8l-5.4 5.4L15 12l3-3H7V7Zm10 10H6l3 3-1.4 1.4L2.2 16l5.4-5.4L9 12l-3 3h11v2Z"/></svg></span><span>Add Rotation</span></button>
                            @endif
                        </div>
                    </div>
                    <div class="sf-pill" style="padding:9px 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 4 5v6c0 5.55 3.84 10.74 8 12 4.16-1.26 8-6.45 8-12V5l-8-3Zm0 2.2L18 6.45V11c0 4.31-2.78 8.39-6 9.82C8.78 19.39 6 15.31 6 11V6.45l6-2.25Zm-1 4.8v5.17l4.24 2.52 1.02-1.71-3.26-1.93V9h-2Z"/></svg>
                    </div>
                </div>

                @if($currentRotation)
                    <div class="sf-list">
                        <div class="sf-row">
                            <div>
                                <strong>{{ $currentRotation->rotation_label ?: 'Current Rotation' }}</strong>
                                <span>{{ ucfirst(str_replace('_', ' ', (string) $currentRotation->status)) }}</span>
                            </div>
                            <span class="sf-pill">{{ $currentRotation->rotation_pattern ?: 'Rotation' }}</span>
                        </div>

                        <div class="sf-row">
                            <div>
                                <strong>Travel / Mobilization</strong>
                                <span>{{ optional($currentRotation->mobilization_date)->format('d M Y') ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="sf-row">
                            <div>
                                <strong>Real Work Period</strong>
                                <span>{{ optional($currentRotation->from_date)->format('d M Y') ?: '—' }} → {{ optional($currentRotation->to_date)->format('d M Y') ?: '—' }}</span>
                            </div>
                        </div>

                        <div class="sf-row">
                            <div>
                                <strong>Ticket</strong>
                                <span>{{ ucfirst(str_replace('_', ' ', (string) $currentRotation->travel_status)) ?: '—' }}</span>
                            </div>
                            @if($canOpenTravelTicket && $currentRotation->ticket_file_path)
                                <a class="sf-pill" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($currentRotation->ticket_file_path) }}" target="_blank">Open</a>
                            @else
                                <span class="sf-pill">{{ $currentRotation->ticket_file_path ? 'Restricted' : 'No Ticket' }}</span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="sf-empty">No rotation has been added yet.</div>
                @endif
            </div>
            @endif

            @if($canViewExpenses)
            <div class="sf-block">
                <div class="sf-block-head">
                    <div>
                        <div class="sf-block-title">Employee Expenses</div>
                        <div class="sf-block-subtitle">Internal finance only, hidden from portal files</div>
                        <div class="sf-mini-actions">
                            @if($canAddExpense)
                                <button type="button" wire:click="mountAction('addExpense')" class="sf-mini-btn"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M3 18V6h18v12H3Zm2-2h14V8H5v8Zm2-1q.825 0 1.413-.588T9 13q0-.825-.588-1.413T7 11q-.825 0-1.413.588T5 13q0 .825.588 1.413T7 15Zm10 0h1v-4h-6v1h5v3ZM5 8v8V8Z"/></svg></span><span>Add Expense</span></button>
                            @endif
                        </div>
                    </div>
                    <div class="sf-pill" style="padding:9px 10px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v12H3V6Zm2 2v2h14V8H5Zm0 4v4h14v-4H5Zm2 1h6v2H7v-2Z"/></svg>
                    </div>
                </div>

                <div class="sf-list">
                    <div class="sf-row">
                        <div>
                            <strong>{{ number_format((float) $otherExpenses, 2) }} {{ $salaryCurrency }}</strong>
                            <span>Total employment-linked expenses</span>
                        </div>
                        <span class="sf-pill">{{ $expensesCount }} Items</span>
                    </div>

                    <div class="sf-row">
                        <div>
                            <strong>Portal Visibility</strong>
                            <span>Expense invoices, receipts, and payment proofs are not shown to candidates.</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </section>


        @if($availableOpsTabs->count())
        <section class="sf-tabs-card">
            <h2 class="sf-tabs-title">Employment Operations</h2>

            @if($canViewRotations)
                <input type="radio" name="sf-tabs" id="sf-tab-rotations" @checked($defaultOpsTab === 'rotations')>
            @endif
            @if($canViewExpenses)
                <input type="radio" name="sf-tabs" id="sf-tab-expenses" @checked($defaultOpsTab === 'expenses')>
            @endif
            @if($canViewSalary)
                <input type="radio" name="sf-tabs" id="sf-tab-salary" @checked($defaultOpsTab === 'salary')>
            @endif
            @if($canViewOfficialFiles)
                <input type="radio" name="sf-tabs" id="sf-tab-files" @checked($defaultOpsTab === 'files')>
            @endif

            <div class="sf-tabs">
                @if($canViewRotations)
                    <label for="sf-tab-rotations">Rotations</label>
                @endif
                @if($canViewExpenses)
                    <label for="sf-tab-expenses">Expenses</label>
                @endif
                @if($canViewSalary)
                    <label for="sf-tab-salary">{{ $isOfficeEmployee ? 'Payroll Slips' : 'Salary Slips' }}</label>
                @endif
                @if($canViewOfficialFiles)
                    <label for="sf-tab-files">Files</label>
                @endif
            </div>

            <div class="sf-tab-content">
                @if($canViewRotations)
                <div class="sf-tab-panel sf-panel-rotations">
                    @if($rotationsList->count())
                        <div class="sf-table-wrap">
                            <table class="sf-ops-table">
                                <thead>
                                    <tr>
                                        <th>Rotation</th>
                                        <th>Status</th>
                                        <th>Travel</th>
                                        <th>Mobilization</th>
                                        <th>Real Work Period</th>
                                        <th>Ticket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rotationsList as $rotation)
                                        <tr>
                                            <td>{{ $rotation->rotation_label ?: ('Rotation #' . $rotation->id) }}<div style="margin-top:8px;">@if($canEditRotation)
                                                        <a href="{{ route('admin.employments.rotations.quick-edit', ['employment' => $this->record->id, 'rotation' => $rotation->id]) }}" class="sf-rotation-edit-btn"><span class="sf-svg-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 19h1.4l9.85-9.85-1.4-1.4L5 17.6V19Zm-2 2v-4.25L16.25 3.5q.3-.3.675-.45T17.7 2.9q.4 0 .775.15t.675.45l1.35 1.35q.3.3.45.675t.15.775q0 .4-.15.775t-.45.675L7.25 21H3Z"/></svg></span><span>Edit</span></a>
                                                    @endif</div></td>
                                            <td><span class="sf-status-pill">{{ ucfirst(str_replace('_', ' ', (string) $rotation->status)) }}</span></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', (string) $rotation->travel_status)) ?: '—' }}</td>
                                            <td>{{ optional($rotation->mobilization_date)->format('d M Y') ?: '—' }}</td>
                                            <td>
                                                {{ optional($rotation->from_date)->format('d M Y') ?: '—' }}
                                                →
                                                {{ optional($rotation->to_date)->format('d M Y') ?: '—' }}
                                            </td>
                                            <td>
                                                @if($canOpenTravelTicket && $rotation->ticket_file_path)
                                                    <a class="sf-pill" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($rotation->ticket_file_path) }}" target="_blank">Open Ticket</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="sf-empty-panel">No rotations added yet. Use Add Rotation to create the first one.</div>
                    @endif
                </div>
                @endif

                @if($canViewExpenses)
                <div class="sf-tab-panel sf-panel-expenses">
                    @if($expensesList->count())
                        <div class="sf-table-wrap">
                            <table class="sf-ops-table">
                                <thead>
                                    <tr>
                                        <th>Expense</th>
                                        <th>Type</th>
                                        <th>Linked Rotation</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Open</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expensesList as $expense)
                                        @php
                                            $rotationId = $expense->employment_rotation_id ?? $expense->rotation_id ?? null;
                                            $linkedRotation = $rotationId ? $record->rotations->firstWhere('id', $rotationId) : null;
                                            $amount = $expense->amount ?? $expense->total_amount ?? 0;
                                            $currency = $expense->currency ?? $salaryCurrency;
                                            $expenseDate = $expense->expense_date ?? $expense->created_at;
                                            $expensePath = $expense->attachment_path ?? $expense->file_path ?? $expense->receipt_path ?? $expense->document_path ?? null;
                                            $expenseUrl = $expensePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($expensePath) : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $expense->title ?? $expense->description ?? ('Expense #' . $expense->id) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', (string) ($expense->expense_type ?? $expense->category ?? 'other'))) }}</td>
                                            <td>{{ $linkedRotation?->rotation_label ?: ($rotationId ? ('Rotation #' . $rotationId) : '—') }}</td>
                                            <td>{{ number_format((float) $amount, 2) }} {{ $currency }}</td>
                                            <td>{{ optional($expenseDate)->format('d M Y') ?: '—' }}</td>
                                            <td><span class="sf-status-pill">{{ ucfirst(str_replace('_', ' ', (string) ($expense->status ?? 'draft'))) }}</span></td>
                                            <td>
                                                @if($expenseUrl)
                                                    <a class="sf-pill" href="{{ $expenseUrl }}" target="_blank">Open</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="sf-empty-panel">No employee expenses yet. Use Add Expense and link it to a rotation when needed.</div>
                    @endif
                </div>
                @endif

                @if($canViewSalary)
                <div class="sf-tab-panel sf-panel-salary">
                    @if($salarySlipsList->count())
                        <div class="sf-table-wrap">
                            <table class="sf-ops-table">
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Status</th>
                                        <th>Worked Days</th>
                                        <th>Net Amount</th>
                                        <th>Confirmation</th>
                                        <th>Open</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salarySlipsList as $slip)
                                        <tr>
                                            <td>{{ str_pad((string) ($slip->salary_month ?? 0), 2, '0', STR_PAD_LEFT) }} / {{ $slip->salary_year ?? '—' }}</td>
                                            <td><span class="sf-status-pill">{{ ucfirst(str_replace('_', ' ', (string) ($slip->status ?? 'draft'))) }}</span></td>
                                            <td>{{ $slip->days_worked ?? $slip->worked_days_total ?? '—' }}</td>
                                            <td>{{ number_format((float) ($slip->net_amount ?? 0), 2) }} {{ $slip->currency ?? $salaryCurrency }}</td>
                                            <td>{{ $slip->employee_confirmation_status ? ucfirst(str_replace('_', ' ', $slip->employee_confirmation_status)) : 'Pending' }}</td>
                                            <td>
                                                <a class="sf-pill" href="{{ \App\Filament\Resources\SalarySlips\SalarySlipResource::getUrl('view', ['record' => $slip]) }}">Open</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="sf-empty-panel">{{ $isOfficeEmployee ? 'No payroll slips generated yet.' : 'No salary slips generated yet.' }}</div>
                    @endif
                </div>
                @endif

                @if($canViewOfficialFiles)
                <div class="sf-tab-panel sf-panel-files">
                    @if($filesList->count())
                        <div class="sf-table-wrap">
                            <table class="sf-ops-table">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th>Category</th>
                                        <th>Version</th>
                                        <th>Current</th>
                                        <th>Expiry</th>
                                        <th>Open</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($filesList as $file)
                                        @php
                                            $fileUrl = $file->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($file->file_path) : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $file->title ?: ('File #' . $file->id) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', (string) $file->category)) }}</td>
                                            <td>V{{ $file->version_no ?: 1 }}</td>
                                            <td>
                                                <span class="sf-status-pill">{{ $file->is_current ? 'Current' : 'Old Version' }}</span>
                                                <span class="sf-status-pill" style="margin-left:6px; background: {{ (bool) ($file->is_visible_to_employee_portal ?? true) ? '#dcfce7' : '#fee2e2' }}; color: {{ (bool) ($file->is_visible_to_employee_portal ?? true) ? '#047857' : '#991b1b' }};">
                                                    {{ (bool) ($file->is_visible_to_employee_portal ?? true) ? 'Portal Visible' : 'Employment Only' }}
                                                </span>
                                            </td>
                                            <td>{{ optional($file->expiry_date)->format('d M Y') ?: '—' }}</td>
                                            <td>
                                                @if($fileUrl)
                                                    <a class="sf-pill" href="{{ $fileUrl }}" target="_blank">Open</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="sf-empty-panel">No files uploaded yet.</div>
                    @endif
                </div>
                @endif
            </div>
        </section>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pageSize = 5;

            document.querySelectorAll('.sf-tab-panel').forEach(function (panel) {
                const rows = Array.from(panel.querySelectorAll('tbody tr'));
                if (rows.length <= pageSize) return;

                let page = 0;
                const totalPages = Math.ceil(rows.length / pageSize);

                const pager = document.createElement('div');
                pager.className = 'sf-pager';

                const prev = document.createElement('button');
                prev.type = 'button';
                prev.textContent = 'Previous';

                const info = document.createElement('span');

                const next = document.createElement('button');
                next.type = 'button';
                next.textContent = 'Next';

                pager.appendChild(prev);
                pager.appendChild(info);
                pager.appendChild(next);

                const tableWrap = panel.querySelector('.sf-table-wrap');
                if (tableWrap) tableWrap.insertAdjacentElement('afterend', pager);

                function render() {
                    rows.forEach(function (row, index) {
                        row.style.display = (index >= page * pageSize && index < (page + 1) * pageSize) ? '' : 'none';
                    });

                    info.textContent = 'Page ' + (page + 1) + ' of ' + totalPages;
                    prev.disabled = page === 0;
                    next.disabled = page >= totalPages - 1;
                }

                prev.addEventListener('click', function () {
                    if (page > 0) {
                        page -= 1;
                        render();
                    }
                });

                next.addEventListener('click', function () {
                    if (page < totalPages - 1) {
                        page += 1;
                        render();
                    }
                });

                render();
            });
        });
    </script>


    {{-- SADA EMPLOYEE PORTAL PROFILE CARD --}}
    @php
        $portalUser = $this->record?->portalUser;

        $employmentEmail = strtolower(trim((string) ($this->record?->employee_email ?? '')));

        $portalAccount = null;
        $portalIdentity = null;

        if ($employmentEmail !== '' && class_exists(\App\Models\PortalAccount::class)) {
            $portalAccount = \App\Models\PortalAccount::query()
                ->where('email', $employmentEmail)
                ->latest('id')
                ->first();

            if ($portalAccount && class_exists(\App\Models\PortalIdentity::class)) {
                $portalIdentity = \App\Models\PortalIdentity::query()
                    ->where('portal_account_id', $portalAccount->id)
                    ->where('employment_id', $this->record?->id)
                    ->latest('id')
                    ->first();
            }
        }

        $portalStatus = $portalUser?->portal_status ?? 'not_created';
        $portalAccess = (bool) ($portalUser?->portal_access_enabled ?? false);
        $portalEmail = $portalAccount?->email ?? $portalUser?->email ?? $this->record?->employee_email ?? '-';
        $portalUserType = $portalUser?->user_type ?? '-';
        $portalDisabledReason = $portalUser?->portal_disabled_reason ?? '-';

        $portalAccountStatus = $portalAccount
            ? (($portalAccount->is_active ?? false) ? 'active' : 'inactive')
            : 'missing';

        $portalIdentityStatus = $portalIdentity
            ? (($portalIdentity->is_current ?? false) ? 'linked current' : 'linked not current')
            : 'missing';

        $portalPreviewReady = $portalAccount && $portalIdentity && ($portalAccount->is_active ?? false);

        $lastLogin = '-';

        if ($portalAccount) {
            $lastLogin = $portalAccount->last_login_at
                ? $portalAccount->last_login_at->format('Y-m-d H:i')
                : 'Not logged in yet';
        } elseif ($portalUser && \Illuminate\Support\Facades\Schema::hasColumn('users', 'last_login_at')) {
            $lastLogin = $portalUser->last_login_at ? $portalUser->last_login_at->format('Y-m-d H:i') : 'Not logged in yet';
        } else {
            $lastLogin = 'Not tracked yet';
        }

        $lastPasswordSetup = $portalUser?->password_setup_sent_at
            ? $portalUser->password_setup_sent_at->format('Y-m-d H:i')
            : 'Not sent yet';

        $portalDisabledAt = $portalUser?->portal_disabled_at
            ? $portalUser->portal_disabled_at->format('Y-m-d H:i')
            : '-';

        $portalLastAction = collect([
            $portalUser?->password_setup_sent_at,
            $portalUser?->portal_disabled_at,
            $portalUser?->updated_at,
            $portalAccount?->updated_at,
            $portalIdentity?->updated_at,
        ])->filter()->sortDesc()->first();

        $portalLastActionLabel = $portalLastAction
            ? $portalLastAction->format('Y-m-d H:i')
            : 'No portal action yet';
    @endphp

    <style>
        .sada-portal-profile-card {
            max-width: 1240px;
            margin: 24px auto;
            border-radius: 30px;
            border: 1px solid #d7e2e5;
            background:
                radial-gradient(circle at 92% 10%, rgba(76,167,168,.12), transparent 30%),
                linear-gradient(180deg,#ffffff 0%,#f8fbfc 100%);
            box-shadow: 0 14px 30px rgba(15,23,42,.07);
            overflow: hidden;
        }

        .sada-portal-profile-head {
            padding: 22px 24px;
            border-bottom: 1px solid #e4ecef;
            background:
                radial-gradient(circle at 90% 10%, rgba(76,167,168,.13), transparent 30%),
                linear-gradient(135deg,#f8fbfc 0%,#ffffff 100%);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            flex-wrap: wrap;
        }

        .sada-portal-profile-kicker {
            color: #1f4664;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .sada-portal-profile-title {
            color: #0f172a;
            font-size: 28px;
            line-height: 1.05;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .sada-portal-profile-subtitle {
            margin-top: 8px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
            font-weight: 700;
            max-width: 720px;
        }

        .sada-portal-profile-status {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            padding: 0 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 950;
            border: 1px solid;
        }

        .sada-portal-profile-status.is-on {
            background: #ecfdf5;
            border-color: #86efac;
            color: #047857;
        }

        .sada-portal-profile-status.is-off {
            background: #fff1f2;
            border-color: #fda4af;
            color: #be123c;
        }

        .sada-portal-profile-body {
            padding: 22px 24px 24px;
        }

        .sada-portal-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .sada-portal-stat {
            border-radius: 20px;
            border: 1px solid #e4ecef;
            background: rgba(255,255,255,.82);
            padding: 16px;
            box-shadow: 0 8px 18px rgba(15,23,42,.035);
        }

        .sada-portal-stat-label {
            color: #64748b;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .sada-portal-stat-value {
            color: #0f172a;
            font-size: 14px;
            line-height: 1.35;
            font-weight: 900;
            word-break: break-word;
        }

        .sada-portal-readiness-note {
            margin: 4px 0 18px;
            border-radius: 20px;
            padding: 15px 16px;
            border: 1px solid;
        }

        .sada-portal-readiness-note strong {
            display: block;
            font-size: 14px;
            font-weight: 950;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .sada-portal-readiness-note span {
            display: block;
            font-size: 13px;
            line-height: 1.55;
            font-weight: 700;
            color: #64748b;
        }

        .sada-portal-readiness-note.is-ready {
            background: #ecfdf5;
            border-color: #86efac;
        }

        .sada-portal-readiness-note.is-warning {
            background: #fff7ed;
            border-color: #fdba74;
        }

        .sada-portal-profile-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            border-top: 1px solid #e4ecef;
            padding-top: 18px;
        }

        .sada-portal-profile-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            border: 0;
            cursor: pointer;
            font-size: 13px;
            font-weight: 950;
            transition: .18s ease;
            text-decoration: none !important;
        }

        .sada-portal-profile-btn:hover {
            transform: translateY(-1px);
        }

        .sada-portal-profile-btn--green {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(16,185,129,.18);
        }

        .sada-portal-profile-btn--gold {
            background: #f2b705;
            color: #3b2a00;
            box-shadow: 0 10px 22px rgba(242,183,5,.20);
        }

        .sada-portal-profile-btn--red {
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 10px 22px rgba(239,68,68,.18);
        }

        .sada-portal-profile-btn--soft {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #d7e2e5;
        }

        .dark .sada-portal-profile-card {
            background:
                radial-gradient(circle at 92% 10%, rgba(76,167,168,.10), transparent 30%),
                linear-gradient(180deg,rgba(12,23,38,.98) 0%,rgba(15,23,42,.96) 100%);
            border-color: rgba(76,167,168,.18);
            box-shadow: 0 14px 30px rgba(0,0,0,.28);
        }

        .dark .sada-portal-profile-head {
            background: rgba(15,23,42,.92);
            border-bottom-color: rgba(76,167,168,.16);
        }

        .dark .sada-portal-profile-title,
        .dark .sada-portal-stat-value {
            color: #f8fafc;
        }

        .dark .sada-portal-profile-kicker {
            color: #8fd6d7;
        }

        .dark .sada-portal-profile-subtitle,
        .dark .sada-portal-stat-label {
            color: #aab8c6;
        }

        .dark .sada-portal-stat {
            background: rgba(15,23,42,.72);
            border-color: rgba(76,167,168,.16);
        }

        .dark .sada-portal-readiness-note {
            background: rgba(15,23,42,.72);
            border-color: rgba(76,167,168,.18);
        }

        .dark .sada-portal-readiness-note strong {
            color: #f8fafc;
        }

        .dark .sada-portal-readiness-note span {
            color: #aab8c6;
        }

        .dark .sada-portal-profile-actions {
            border-top-color: rgba(76,167,168,.16);
        }

        .dark .sada-portal-profile-btn--soft {
            background: rgba(15,23,42,.92);
            color: #f8fafc;
            border-color: rgba(76,167,168,.18);
        }

        @media (max-width: 1100px) {
            .sada-portal-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .sada-portal-stats {
                grid-template-columns: 1fr;
            }

            .sada-portal-profile-actions {
                justify-content: flex-start;
            }
        }
    </style>

    @if($canViewPortalAccess && ! $isOfficeEmployee)
    <section class="sada-portal-profile-card">
        <div class="sada-portal-profile-head">
            <div>
                <div class="sada-portal-profile-kicker">Employee Portal</div>
                <div class="sada-portal-profile-title">Portal Access & Activity</div>
                <div class="sada-portal-profile-subtitle">
                    Manage employee portal access, password setup/reset emails, real portal account link, and read-only preview readiness.
                </div>
            </div>

            <div class="sada-portal-profile-status {{ $portalPreviewReady ? 'is-on' : 'is-off' }}">
                {{ $portalPreviewReady ? 'Portal Ready' : 'Portal Needs Setup' }}
            </div>
        </div>

        <div class="sada-portal-profile-body">
            <div class="sada-portal-stats">
                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Portal Email</div>
                    <div class="sada-portal-stat-value">{{ $portalEmail }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Portal Status</div>
                    <div class="sada-portal-stat-value">{{ str_replace('_', ' ', $portalStatus) }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Portal Account</div>
                    <div class="sada-portal-stat-value">
                        {{ $portalAccount ? ('#' . $portalAccount->id . ' · ' . str_replace('_', ' ', $portalAccountStatus)) : 'Missing' }}
                    </div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Portal Identity</div>
                    <div class="sada-portal-stat-value">
                        {{ $portalIdentity ? ('#' . $portalIdentity->id . ' · ' . str_replace('_', ' ', $portalIdentityStatus)) : 'Missing' }}
                    </div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Preview Readiness</div>
                    <div class="sada-portal-stat-value">
                        {{ $portalPreviewReady ? 'Ready for read-only preview' : 'Will be prepared on preview open' }}
                    </div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Last Login</div>
                    <div class="sada-portal-stat-value">{{ $lastLogin }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Last Portal Action</div>
                    <div class="sada-portal-stat-value">{{ $portalLastActionLabel }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Password Setup Sent</div>
                    <div class="sada-portal-stat-value">{{ $lastPasswordSetup }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Disabled Reason</div>
                    <div class="sada-portal-stat-value">{{ $portalDisabledReason }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Disabled At</div>
                    <div class="sada-portal-stat-value">{{ $portalDisabledAt }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">User Type</div>
                    <div class="sada-portal-stat-value">{{ str_replace('_', ' ', $portalUserType) }}</div>
                </div>
            </div>

            <div class="sada-portal-readiness-note {{ $portalPreviewReady ? 'is-ready' : 'is-warning' }}">
                <strong>{{ $portalPreviewReady ? 'Real portal link is ready' : 'Real portal link will be created/updated automatically' }}</strong>
                <span>
                    {{ $portalPreviewReady
                        ? 'The employee has an active PortalAccount and a current PortalIdentity linked to this employment.'
                        : 'Opening Preview will create or repair the PortalAccount and PortalIdentity for this employment in read-only mode.'
                    }}
                </span>
            </div>

            <div class="sada-portal-profile-actions">
                <button type="button" wire:click="mountAction('sendPortalPasswordSetup')" class="sada-portal-profile-btn sada-portal-profile-btn--green">
                    Send Password Setup
                </button>

                <button type="button" wire:click="mountAction('resetPortalPassword')" class="sada-portal-profile-btn sada-portal-profile-btn--gold">
                    Reset Password
                </button>

                <button type="button" wire:click="mountAction('enablePortal')" class="sada-portal-profile-btn sada-portal-profile-btn--soft">
                    Enable
                </button>

                <button type="button" wire:click="mountAction('disablePortal')" class="sada-portal-profile-btn sada-portal-profile-btn--red">
                    Disable
                </button>

                <a href="{{ url('/admin/employments/' . $this->record->id . '/portal-preview') }}" target="_blank" class="sada-portal-profile-btn sada-portal-profile-btn--soft">
                    Preview
                </a>
            </div>
        </div>
    </section>

    @endif

    @if($isOfficeEmployee)
    <section class="sada-portal-profile-card">
        <div class="sada-portal-profile-head">
            <div>
                <div class="sada-portal-profile-kicker">ERP SYSTEM ACCESS</div>
                <div class="sada-portal-profile-title">ERP Access & Activity</div>
                <div class="sada-portal-profile-subtitle">
                    Manage this office employee's ERP login, role, department, access readiness, and internal system profile.
                </div>
            </div>

            <div class="sada-portal-profile-status {{ $erpLoginUser && (bool) $erpLoginUser->is_admin ? 'is-on' : 'is-off' }}">
                {{ $erpLoginUser ? ((bool) $erpLoginUser->is_admin ? 'ERP Enabled' : 'ERP Disabled') : 'ERP Needs Setup' }}
            </div>
        </div>

        <div class="sada-portal-profile-body">
            <div class="sada-portal-stats">
                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">ERP Email</div>
                    <div class="sada-portal-stat-value">{{ $erpLoginUser?->email ?: ($record->employee_email ?: '-') }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">ERP Status</div>
                    <div class="sada-portal-stat-value">{{ $erpLoginUser ? ((bool) $erpLoginUser->is_admin ? 'Enabled admin user' : 'Disabled admin user') : 'Not created yet' }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">ERP Role</div>
                    <div class="sada-portal-stat-value">{{ $erpLoginUser?->erp_role ? ucfirst(str_replace('_', ' ', $erpLoginUser->erp_role)) : '-' }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">ERP Department</div>
                    <div class="sada-portal-stat-value">{{ $erpLoginUser?->erp_department ?: ($record->office_department ?: '-') }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">User Type</div>
                    <div class="sada-portal-stat-value">{{ $erpLoginUser?->user_type ?: 'office employee' }}</div>
                </div>

                <div class="sada-portal-stat">
                    <div class="sada-portal-stat-label">Login Access</div>
                    <div class="sada-portal-stat-value">{{ $erpLoginUser ? ((bool) $erpLoginUser->is_admin ? 'Can access ERP' : 'ERP access disabled') : 'Not enabled' }}</div>
                </div>
            </div>

            <div class="sada-portal-readiness-note {{ $erpLoginUser && (bool) $erpLoginUser->is_admin ? 'is-ready' : 'is-warning' }}">
                <strong>{{ $erpLoginUser ? ((bool) $erpLoginUser->is_admin ? 'ERP login is enabled' : 'ERP login is currently disabled') : 'ERP login will be created from this employee profile' }}</strong>
                <span>
                    {{ $erpLoginUser
                        ? ((bool) $erpLoginUser->is_admin
                            ? 'This employee can access the ERP according to assigned Page Rules.'
                            : 'This employee has a saved ERP user, but admin login is disabled until you enable it.')
                        : 'Create ERP user login, then manage page rules from Page Rules.'
                    }}
                </span>
            </div>

            <div class="sada-portal-profile-actions">
                <button type="button" wire:click="mountAction('createErpUserLogin')" class="sada-portal-profile-btn sada-portal-profile-btn--green">
                    <span class="material-symbols-rounded">person_add</span>
                    <span>{{ $erpLoginUser ? 'Update ERP User' : 'Create ERP User Login' }}</span>
                </button>

                @if($erpLoginUser)
                    @if(! (bool) $erpLoginUser->is_admin)
                        <button type="button" wire:click="mountAction('enableErpUserLogin')" class="sada-portal-profile-btn sada-portal-profile-btn--green">
                            <span class="material-symbols-rounded">check_circle</span>
                            <span>Enable ERP</span>
                        </button>
                    @else
                        <button type="button" wire:click="mountAction('disableErpUserLogin')" class="sada-portal-profile-btn sada-portal-profile-btn--red">
                            <span class="material-symbols-rounded">block</span>
                            <span>Disable ERP</span>
                        </button>
                    @endif
                @endif

                @if($erpLoginUser && ((bool) auth()->user()?->isSuperAdmin() || (bool) auth()->user()?->canErp('access_control', 'view')))
                    <a href="{{ url('/admin/erp-access-control') }}" target="_blank" class="sada-portal-profile-btn sada-portal-profile-btn--soft">
                        <span class="material-symbols-rounded">admin_panel_settings</span>
                        <span>Open Page Rules</span>
                    </a>
                @endif
            </div>
        </div>
    </section>
    @endif

    <x-filament-actions::modals />
    {{-- /SADA EMPLOYEE PORTAL PROFILE CARD --}}

<style id="sf-office-erp-enabled-disabled-colors-final">
    .sada-portal-profile-status.is-on {
        background: rgba(220, 252, 231, .95) !important;
        color: #047857 !important;
        border: 1px solid rgba(16, 185, 129, .35) !important;
        box-shadow: 0 12px 28px rgba(16, 185, 129, .12) !important;
    }

    .sada-portal-profile-status.is-off {
        background: rgba(254, 226, 226, .95) !important;
        color: #b91c1c !important;
        border: 1px solid rgba(239, 68, 68, .35) !important;
        box-shadow: 0 12px 28px rgba(239, 68, 68, .12) !important;
    }

    .sada-portal-readiness-note.is-ready {
        background: rgba(236, 253, 245, .90) !important;
        border-color: rgba(16, 185, 129, .35) !important;
    }

    .sada-portal-readiness-note.is-warning {
        background: rgba(254, 242, 242, .90) !important;
        border-color: rgba(239, 68, 68, .35) !important;
    }

    .sada-portal-profile-btn--green {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        color: #fff !important;
        border-color: rgba(16,185,129,.35) !important;
        box-shadow: 0 14px 34px rgba(16, 185, 129, .20) !important;
    }

    .sada-portal-profile-btn--red {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #fff !important;
        border-color: rgba(239,68,68,.35) !important;
        box-shadow: 0 14px 34px rgba(239, 68, 68, .20) !important;
    }

    .sf-mini-btn:has(.material-symbols-rounded:first-child) {
        border-color: rgba(148,163,184,.22) !important;
    }

    .dark .sada-portal-profile-status.is-on {
        background: rgba(6, 78, 59, .72) !important;
        color: #bbf7d0 !important;
        border-color: rgba(52, 211, 153, .30) !important;
    }

    .dark .sada-portal-profile-status.is-off {
        background: rgba(127, 29, 29, .72) !important;
        color: #fecaca !important;
        border-color: rgba(248, 113, 113, .30) !important;
    }
</style>


<style id="sf-office-erp-disabled-note-red-final">
    .sada-portal-readiness-note.is-warning {
        background: rgba(254, 242, 242, .95) !important;
        border-color: rgba(239, 68, 68, .38) !important;
        color: #991b1b !important;
        box-shadow: 0 14px 34px rgba(239, 68, 68, .10) !important;
    }

    .sada-portal-readiness-note.is-warning strong {
        color: #991b1b !important;
    }

    .sada-portal-readiness-note.is-warning span {
        color: #7f1d1d !important;
    }

    .dark .sada-portal-readiness-note.is-warning {
        background: rgba(127, 29, 29, .55) !important;
        border-color: rgba(248, 113, 113, .35) !important;
        color: #fecaca !important;
    }

    .dark .sada-portal-readiness-note.is-warning strong,
    .dark .sada-portal-readiness-note.is-warning span {
        color: #fecaca !important;
    }
</style>




<style id="sf-employment-md3-action-icons">
    /*
        Sada Fezzan ERP Global Action Button Reference
        Compact header action layout: 3 columns x 3 rows.
    */

    .sf-employment-head {
        grid-template-columns: minmax(320px, .78fr) minmax(520px, 1fr) !important;
        gap: 24px !important;
    }

    .sf-actions {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 12px !important;
        justify-content: end !important;
        align-items: stretch !important;
        width: 100% !important;
        max-width: 760px !important;
        margin-left: auto !important;
    }

    .sf-btn,
    .sf-mini-btn {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        border: 0 !important;
        cursor: pointer !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        line-height: 1.05 !important;
        font-weight: 950 !important;
        letter-spacing: -.025em !important;
        box-shadow:
            0 12px 26px rgba(0,0,0,.16),
            inset 0 1px 0 rgba(255,255,255,.18) !important;
        transition: transform .15s ease, filter .15s ease, box-shadow .15s ease !important;
    }

    .sf-btn {
        min-height: 46px !important;
        height: 46px !important;
        padding: 9px 12px !important;
        border-radius: 999px !important;
        font-size: 13px !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .sf-btn:hover,
    .sf-mini-btn:hover {
        transform: translateY(-1px) !important;
        filter: saturate(1.06) brightness(1.03) !important;
    }

    .sf-svg-icon {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
    }

    .sf-svg-icon svg {
        width: 18px !important;
        height: 18px !important;
        display: block !important;
        fill: currentColor !important;
    }

    .sf-btn-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #fff !important;
    }

    .sf-btn-red {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #fff !important;
    }

    .sf-btn-gray {
        background: linear-gradient(135deg, rgba(71,85,105,.94), rgba(51,65,85,.92)) !important;
        color: #fff !important;
        border: 1px solid rgba(255,255,255,.14) !important;
    }

    .sf-btn-yellow {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #111827 !important;
    }

    .sf-btn-blue .sf-svg-icon,
    .sf-btn-blue .sf-svg-icon svg,
    .sf-btn-red .sf-svg-icon,
    .sf-btn-red .sf-svg-icon svg,
    .sf-btn-gray .sf-svg-icon,
    .sf-btn-gray .sf-svg-icon svg {
        color: #fff !important;
        fill: #fff !important;
    }

    .sf-btn-yellow .sf-svg-icon,
    .sf-btn-yellow .sf-svg-icon svg {
        color: #111827 !important;
        fill: #111827 !important;
    }

    .sf-btn span:not(.sf-svg-icon) {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    .sf-mini-btn {
        min-height: 38px !important;
        padding: 9px 14px !important;
        border-radius: 999px !important;
        font-size: 12px !important;
        background: #e0f2fe !important;
        color: #0f172a !important;
        box-shadow: 0 10px 22px rgba(15,23,42,.08) !important;
    }

    .dark .sf-mini-btn {
        background: rgba(51,65,85,.86) !important;
        color: #fff !important;
    }

    @media (max-width: 1180px) {
        .sf-employment-head {
            grid-template-columns: 1fr !important;
        }

        .sf-actions {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            max-width: 100% !important;
            margin-left: 0 !important;
        }
    }

    @media (max-width: 760px) {
        .sf-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 520px) {
        .sf-actions {
            grid-template-columns: 1fr !important;
        }
    }
</style>




<style id="sf-force-employment-actions-3-rows-final">
    /* FINAL EDIT: compact action buttons in exactly 3 visual rows */
    .sf-employment-hero .sf-employment-head {
        grid-template-columns: minmax(360px, .9fr) minmax(620px, 1.1fr) !important;
        gap: 24px !important;
        align-items: start !important;
    }

    .sf-employment-hero .sf-actions {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 12px !important;
        width: 100% !important;
        max-width: 760px !important;
        margin-left: auto !important;
        justify-content: stretch !important;
        align-items: stretch !important;
    }

    .sf-employment-hero .sf-actions > .sf-btn,
    .sf-employment-hero .sf-actions > a.sf-btn,
    .sf-employment-hero .sf-actions > button.sf-btn {
        grid-column: auto !important;
        grid-row: auto !important;
        width: auto !important;
        min-width: 0 !important;
        max-width: none !important;
        flex: initial !important;
        flex-basis: auto !important;
        height: 50px !important;
        min-height: 50px !important;
        padding: 8px 14px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        text-decoration: none !important;
        box-shadow: 0 14px 28px rgba(0,0,0,.16) !important;
    }

    .sf-employment-hero .sf-actions > .sf-btn span:last-child {
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    .sf-employment-hero .sf-actions .sf-btn-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #fff !important;
    }

    .sf-employment-hero .sf-actions .sf-btn-red {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #fff !important;
    }

    .sf-employment-hero .sf-actions .sf-btn-gray {
        background: linear-gradient(135deg, rgba(71,85,105,.94), rgba(51,65,85,.92)) !important;
        color: #fff !important;
        border: 1px solid rgba(255,255,255,.16) !important;
    }

    .sf-employment-hero .sf-actions .sf-btn-yellow {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #111827 !important;
    }

    .sf-employment-hero .sf-actions .sf-btn-blue .sf-svg-icon,
    .sf-employment-hero .sf-actions .sf-btn-blue svg,
    .sf-employment-hero .sf-actions .sf-btn-red .sf-svg-icon,
    .sf-employment-hero .sf-actions .sf-btn-red svg,
    .sf-employment-hero .sf-actions .sf-btn-gray .sf-svg-icon,
    .sf-employment-hero .sf-actions .sf-btn-gray svg {
        color: #fff !important;
        fill: #fff !important;
    }

    .sf-employment-hero .sf-actions .sf-btn-yellow .sf-svg-icon,
    .sf-employment-hero .sf-actions .sf-btn-yellow svg {
        color: #111827 !important;
        fill: #111827 !important;
    }

    .sf-employment-hero .sf-actions .sf-svg-icon {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .sf-employment-hero .sf-actions .sf-svg-icon svg {
        width: 18px !important;
        height: 18px !important;
        display: block !important;
        fill: currentColor !important;
    }

    @media (max-width: 1180px) {
        .sf-employment-hero .sf-employment-head {
            grid-template-columns: 1fr !important;
        }

        .sf-employment-hero .sf-actions {
            max-width: 100% !important;
            margin-left: 0 !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 760px) {
        .sf-employment-hero .sf-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 520px) {
        .sf-employment-hero .sf-actions {
            grid-template-columns: 1fr !important;
        }
    }
</style>


</x-filament-panels::page>
