<div class="sf-finance-expense-livewire-root">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,600,0,0" />

{{-- SF Finance Expense MD3 Premium Header --}}
@php

    /* SADA TICKET DATE FALLBACK HELPERS 2026-05-07 */
    $sfTravelDetail = $expense?->travelDetail ?? null;

    $sfTicketBookingDate = $expense?->expense_date ?? null;
    $sfTicketDepartureDate = $sfTravelDetail?->departure_date ?? $expense?->incurred_from ?? null;
    $sfTicketReturnDate = $sfTravelDetail?->return_date ?? $expense?->incurred_to ?? null;

    $sfTicketBookingDateText = $sfTicketBookingDate ? \Illuminate\Support\Carbon::parse($sfTicketBookingDate)->format('Y-m-d') : '-';
    $sfTicketDepartureDateText = $sfTicketDepartureDate ? \Illuminate\Support\Carbon::parse($sfTicketDepartureDate)->format('Y-m-d') : '-';
    $sfTicketReturnDateText = $sfTicketReturnDate ? \Illuminate\Support\Carbon::parse($sfTicketReturnDate)->format('Y-m-d') : 'One Way / No Return';

    $sfExpenseHeader = method_exists($this, 'getExpenseHeaderData') ? $this->getExpenseHeaderData() : [];

    $expense = $record ?? $this->record;
    $expense = $expense?->fresh();

    $title = $expense?->title ?: ($sfExpenseHeader['title'] ?? ('Finance Expense #' . ($expense?->id ?? '')));
    $owner = method_exists($expense, 'ownerName') ? ($expense->ownerName() ?: '-') : ($sfExpenseHeader['owner'] ?? '-');

    $scopeLabel = \App\Models\FinanceExpense::scopeLabels()[$expense?->expense_scope] ?? ($expense?->expense_scope ?: '-');
    $statusLabel = \App\Models\FinanceExpense::statusLabels()[$expense?->status] ?? ($expense?->status ?: '-');
    $reimbursementLabel = \App\Models\FinanceExpense::reimbursementLabels()[$expense?->reimbursement_status] ?? ($expense?->reimbursement_status ?: '-');
    $paidByLabel = \App\Models\FinanceExpense::paidByLabels()[$expense?->paid_by] ?? ($expense?->paid_by ?: '-');

    $amount = number_format((float) ($expense?->amount ?? 0), 2) . ' ' . ($expense?->currency ?: 'USD');
    $claimAmount = number_format((float) ($expense?->reimbursement_amount ?? $expense?->amount ?? 0), 2) . ' ' . ($expense?->reimbursement_currency ?: $expense?->currency ?: 'USD');
    $expenseDate = $expense?->expense_date ? $expense->expense_date->format('Y-m-d') : '-';

    $isDraft = (string) ($expense?->status ?? '') === \App\Models\FinanceExpense::STATUS_DRAFT;
    $isApproved = (string) ($expense?->status ?? '') === \App\Models\FinanceExpense::STATUS_APPROVED;

    $expenseStatus = (string) ($expense?->status ?? '');
    $isDraft = $expenseStatus === \App\Models\FinanceExpense::STATUS_DRAFT;
    $isApproved = $expenseStatus === \App\Models\FinanceExpense::STATUS_APPROVED;
    $isPaid = $expenseStatus === \App\Models\FinanceExpense::STATUS_PAID;
    $isCancelled = $expenseStatus === \App\Models\FinanceExpense::STATUS_CANCELLED;
    $reimbursementPending = (string) ($expense?->reimbursement_status ?? '') === \App\Models\FinanceExpense::REIMBURSEMENT_PENDING;
    $reimbursementCanGoBack = in_array((string) ($expense?->reimbursement_status ?? ''), [
        \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED,
        \App\Models\FinanceExpense::REIMBURSEMENT_REJECTED,
    ], true) && in_array((string) ($expense?->status ?? ''), [
        \App\Models\FinanceExpense::STATUS_DRAFT,
        \App\Models\FinanceExpense::STATUS_APPROVED,
    ], true);

    $canEditExpense = (bool) auth()->user()?->canErp('finance_expenses', 'edit');
    $canApproveExpense = (bool) auth()->user()?->canErp('finance_expenses', 'approve');
    $canCancelExpense = (bool) (auth()->user()?->canErp('finance_expenses', 'cancel') || auth()->user()?->canErp('finance_expenses', 'approve'));
    $canPayExpense = (bool) (auth()->user()?->canErp('finance_expenses', 'mark_paid') || auth()->user()?->canErp('finance_expenses', 'process_payment'));
    $canBackToPayment = (bool) (
        $expense?->paid_by === \App\Models\FinanceExpense::PAID_BY_CANDIDATE
        && in_array((string) ($expense?->reimbursement_status ?? ''), [
            \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED,
            \App\Models\FinanceExpense::REIMBURSEMENT_REJECTED,
            \App\Models\FinanceExpense::REIMBURSEMENT_PAID,
        ], true)
        && (bool) auth()->user()?->canErp('finance_expenses', 'approve')
    );
    $canBackExpenseToApproved = (
        $isPaid
        && (
            (bool) auth()->user()?->canErp('finance_expenses', 'approve')
            || (bool) auth()->user()?->canErp('finance_expenses', 'reopen')
            || (bool) auth()->user()?->canErp('finance_expenses', 'mark_paid')
            || (bool) auth()->user()?->canErp('finance_expenses', 'process_payment')
            || (bool) auth()->user()?->canErp('finance_expenses', 'back_to_draft')
        )
    );

    $reimbursementApproved = (string) ($expense?->reimbursement_status ?? '') === \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED;
    $reimbursementRejected = (string) ($expense?->reimbursement_status ?? '') === \App\Models\FinanceExpense::REIMBURSEMENT_REJECTED;
    $reimbursementPaid = (string) ($expense?->reimbursement_status ?? '') === \App\Models\FinanceExpense::REIMBURSEMENT_PAID;
    $linkedSalarySlipId = $expense?->reimbursed_salary_slip_id;
    $reimbursementLinked = $reimbursementApproved && filled($linkedSalarySlipId);
    $canLinkSalarySlip = (bool) (
        auth()->user()?->canErp('salary_slips', 'edit')
        || auth()->user()?->canErp('salary_slips', 'create')
        || auth()->user()?->canErp('finance_expenses', 'approve')
    );


    $sfIsCandidateReimbursement = (string) ($expense?->paid_by ?? '') === \App\Models\FinanceExpense::PAID_BY_CANDIDATE;

    $sfDisplayWorkflowStatus = $statusLabel;

    if ($sfIsCandidateReimbursement) {
        $sfReimbursementState = (string) ($expense?->reimbursement_status ?? '');

        $sfDisplayWorkflowStatus = match ($sfReimbursementState) {
            \App\Models\FinanceExpense::REIMBURSEMENT_PENDING => 'Pending',
            \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED => filled($expense?->reimbursed_salary_slip_id) ? 'Linked to Salary Slip' : 'Approved',
            \App\Models\FinanceExpense::REIMBURSEMENT_REJECTED => 'Rejected',
            \App\Models\FinanceExpense::REIMBURSEMENT_PAID => 'Paid',
            default => $statusLabel,
        };
    }

    $sfCreatedByName = $expense?->creator?->name
        ?: $expense?->createdBy?->name
        ?: $owner
        ?: '-';

    $sfApprovedByName = '-';

    $sfDecisionById = null;

    if (\Illuminate\Support\Facades\Schema::hasColumn('finance_expenses', 'reimbursement_decision_by')) {
        $sfDecisionById = $expense?->reimbursement_decision_by;
    }

    if ($sfDecisionById) {
        $sfApprovedByName = \App\Models\User::query()->whereKey($sfDecisionById)->value('name') ?: '-';
    } elseif ($expense?->approver?->name) {
        $sfApprovedByName = $expense->approver->name;
    } elseif ($expense?->approved_by) {
        $sfApprovedByName = \App\Models\User::query()->whereKey($expense->approved_by)->value('name') ?: '-';
    }

    $sfAttachmentPath = $expense?->attachment_file_path
        ?? $expense?->attachment_path
        ?? $expense?->receipt_file_path
        ?? null;

    $sfHasAttachment = filled($sfAttachmentPath) || (bool) ($expense?->has_attachment ?? false);

@endphp

<section class="sf-md3-expense-hero">
    <div class="sf-md3-hero-main">
        <div class="sf-md3-hero-copy">
            <div class="sf-md3-kicker">
                <span>Finance Expense</span>
                <span>•</span>
                <span>Reimbursement Claim</span>
                @if((bool) ($expense?->candidate_submitted ?? false))
                    <span>•</span>
                    <span>Portal Submitted</span>
                @endif
            </div>


<h1>{{ $title }}</h1>

            <p class="sf-md3-subtitle">
                {{ $owner }} · {{ $scopeLabel }}
            </p>

            <div class="sf-md3-chips">
                <span>Status: <strong>{{ $sfDisplayWorkflowStatus }}</strong></span>
                <span>Reimbursement: <strong>{{ $reimbursementLabel }}</strong></span>
                <span>Paid By: <strong>{{ $paidByLabel }}</strong></span>
            </div>
        </div>

        <div class="sf-md3-hero-actions">

                <a class="sf-md3-action sf-md3-action-blue"
                   href="{{ route('admin.finance-expenses.print', $expense) }}"
                   target="_blank" rel="noopener">
                    <span class="sf-md3-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M6 19h12v-5H6v5Zm12-9h1q.425 0 .713-.288T20 9V7q0-.425-.288-.713T19 6H5q-.425 0-.713.288T4 7v2q0 .425.288.713T5 10h1V8h12v2ZM8 4h8V2H8v2Zm-2 17q-.825 0-1.413-.588T4 19v-5H3q-.825 0-1.413-.588T1 12V7q0-1.25.875-2.125T4 4h2V0h12v4h2q1.25 0 2.125.875T23 7v5q0 .825-.588 1.413T21 14h-1v5q0 .825-.588 1.413T18 21H6Z"/></svg>
                    </span>
                    <strong>Print</strong>
                </a>
@if($canEditExpense)
                <a class="sf-md3-action sf-md3-action-warning"
                   href="{{ static::getResource()::getUrl('edit', ['record' => $expense]) }}">
                    <span class="sf-md3-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M5 19h1.4l9.85-9.85-1.4-1.4L5 17.6V19Zm-2 2v-4.25L16.25 3.5q.3-.3.675-.45T17.7 2.9q.4 0 .775.15t.675.45l1.35 1.35q.3.3.45.675t.15.775q0 .4-.15.775t-.45.675L7.25 21H3Zm12.55-12.55-1.4-1.4 1.4 1.4Z"/></svg>
                    </span>
                    <strong>Edit</strong>
                </a>
            @endif

            {{-- Stage 1: Pending reimbursement request --}}
            @if($reimbursementPending && $canApproveExpense)
                <button type="button" class="sf-md3-action sf-md3-action-purple" wire:click="approveReimbursementDirect">
                    <span class="sf-md3-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 22q-3.475-.875-5.738-3.988T4 11.1V5l8-3 8 3v6.1q0 3.8-2.263 6.913T12 22Zm-1.05-6.45 5.65-5.65-1.4-1.45-4.25 4.25-2.15-2.15-1.4 1.4 3.55 3.6Z"/></svg>
                    </span>
                    <strong>Approve</strong>
                </button>

                <button type="button" class="sf-md3-action sf-md3-action-red" wire:click="rejectReimbursementDirect">
                    <span class="sf-md3-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M8.4 17 12 13.4 15.6 17 17 15.6 13.4 12 17 8.4 15.6 7 12 10.6 8.4 7 7 8.4 10.6 12 7 15.6 8.4 17ZM12 22q-3.475-.875-5.738-3.988T4 11.1V5l8-3 8 3v6.1q0 3.8-2.263 6.913T12 22Z"/></svg>
                    </span>
                    <strong>Decline</strong>
                </button>
            @endif

            {{-- Stage 2: Approved reimbursement, not linked yet --}}
            @if($reimbursementApproved && blank($linkedSalarySlipId))
                @if($canApproveExpense)
                    <button type="button" class="sf-md3-action sf-md3-action-gray" wire:click="backReimbursementToPendingDirect">
                        <span class="sf-md3-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M12 20q-3.35 0-5.675-2.325T4 12q0-3.35 2.325-5.675T12 4h.15l-1.6-1.6L11.95 1 16 5.05 11.95 9.1l-1.4-1.4 1.7-1.7H12q-2.5 0-4.25 1.75T6 12q0 2.5 1.75 4.25T12 18q1.7 0 3.075-.85T17.2 14.8l1.85.75q-.95 2-2.837 3.225T12 20Z"/></svg>
                        </span>
                        <strong>Back</strong>
                    </button>
                @endif

                @if($canLinkSalarySlip)
                    <button type="button" class="sf-md3-action sf-md3-action-green" wire:click="mountAction('linkReimbursementToSalarySlip')">
                        <span class="sf-md3-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M7 22q-.825 0-1.413-.588T5 20V4q0-.825.588-1.413T7 2h10q.825 0 1.413.588T19 4v16q0 .825-.588 1.413T17 22H7Zm0-2h10V4H7v16Zm2-3h6v-2H9v2Zm0-4h6v-2H9v2Zm0-4h6V7H9v2Z"/></svg>
                        </span>
                        <strong>Link to Salary Slip</strong>
                    </button>
                @endif
            @endif

            {{-- Stage 3: Linked to salary slip --}}
            @if($reimbursementLinked)
                @if($canApproveExpense)
                    <button type="button" class="sf-md3-action sf-md3-action-gray" wire:click="backReimbursementToPendingDirect">
                        <span class="sf-md3-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M12 20q-3.35 0-5.675-2.325T4 12q0-3.35 2.325-5.675T12 4h.15l-1.6-1.6L11.95 1 16 5.05 11.95 9.1l-1.4-1.4 1.7-1.7H12q-2.5 0-4.25 1.75T6 12q0 2.5 1.75 4.25T12 18q1.7 0 3.075-.85T17.2 14.8l1.85.75q-.95 2-2.837 3.225T12 20Z"/></svg>
                        </span>
                        <strong>Unlink / Back</strong>
                    </button>
                @endif

                <a class="sf-md3-action sf-md3-action-blue"
                   href="{{ \App\Filament\Resources\SalarySlips\SalarySlipResource::getUrl('view', ['record' => $linkedSalarySlipId]) }}">
                    <span class="sf-md3-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M14 3v2h3.6l-9.85 9.85 1.4 1.4L19 6.4V10h2V3h-7ZM5 5h6V3H5q-.825 0-1.413.588T3 5v14q0 .825.588 1.413T5 21h14q.825 0 1.413-.588T21 19v-6h-2v6H5V5Z"/></svg>
                    </span>
                    <strong>Open Salary Slip</strong>
                </a>
            @endif
        </div>
    </div>

    <div class="sf-md3-hero-stats">
        <div>
            <small>Amount</small>
            <strong>{{ $amount }}</strong>
        </div>
        <div>
            <small>Claim Amount</small>
            <strong>{{ $claimAmount }}</strong>
        </div>
        <div>
            <small>Expense Date</small>
            <strong>{{ $expenseDate }}</strong>
        </div>
    </div>
</section>
<x-filament-panels::page>
    
@php
    /*
     * Safe display variables for Finance Expense premium view.
     * Prevents undefined variable errors when the view is opened directly
     * after creating an expense from Pre-Employment / Employment / Finance.
     */
    $record = $this->record ?? $record ?? null;

    $expenseScope = (string) ($record?->expense_scope ?? 'ad_hoc');

    $scopeLabel = match ($expenseScope) {
        'pre_hire' => 'Pre-Hire',
        'employment' => 'Employment',
        'rotation' => 'Rotation',
        'ad_hoc' => 'Ad Hoc',
        default => filled($expenseScope) ? ucwords(str_replace('_', ' ', $expenseScope)) : 'Ad Hoc',
    };

    [$scopeBg, $scopeText, $scopeBorder] = match ($expenseScope) {
        'pre_hire' => ['#fffbeb', '#92400e', '#fde68a'],
        'employment' => ['#ecfdf5', '#047857', '#86efac'],
        'rotation' => ['#eff6ff', '#1d4ed8', '#bfdbfe'],
        'ad_hoc' => ['#f8fafc', '#475569', '#e2e8f0'],
        default => ['#f8fafc', '#475569', '#e2e8f0'],
    };

    $paidBy = (string) ($record?->paid_by ?? 'company');

    $paidByLabel = match ($paidBy) {
        'company' => 'Company',
        'candidate' => 'Candidate / Employee',
        'client' => 'Client',
        'third_party' => 'Third Party',
        default => filled($paidBy) ? ucwords(str_replace('_', ' ', $paidBy)) : 'Company',
    };

    [$paidByBg, $paidByText, $paidByBorder] = match ($paidBy) {
        'company' => ['#ecfdf5', '#047857', '#86efac'],
        'candidate' => ['#fff7ed', '#c2410c', '#fdba74'],
        'client' => ['#eff6ff', '#1d4ed8', '#bfdbfe'],
        'third_party' => ['#f8fafc', '#475569', '#e2e8f0'],
        default => ['#f8fafc', '#475569', '#e2e8f0'],
    };

    $reimbursementStatus = (string) ($record?->reimbursement_status ?? 'not_applicable');

    $reimbursementLabel = match ($reimbursementStatus) {
        'not_applicable' => 'Not Applicable',
        'pending' => 'Pending',
        'approved' => 'Approved',
        'paid' => 'Paid',
        'rejected' => 'Rejected',
        default => filled($reimbursementStatus) ? ucwords(str_replace('_', ' ', $reimbursementStatus)) : 'Not Applicable',
    };

    [$reimbursementBg, $reimbursementText, $reimbursementBorder] = match ($reimbursementStatus) {
        'pending' => ['#fffbeb', '#92400e', '#fde68a'],
        'approved' => ['#eff6ff', '#1d4ed8', '#bfdbfe'],
        'paid' => ['#ecfdf5', '#047857', '#86efac'],
        'rejected' => ['#fef2f2', '#b91c1c', '#fecaca'],
        'not_applicable' => ['#f8fafc', '#475569', '#e2e8f0'],
        default => ['#f8fafc', '#475569', '#e2e8f0'],
    };

    $status = (string) ($record?->status ?? 'draft');

    $statusLabel = match ($status) {
        'draft' => 'Draft',
        'approved' => 'Approved',
        'paid' => 'Paid',
        'cancelled' => 'Cancelled',
        default => filled($status) ? ucwords(str_replace('_', ' ', $status)) : 'Draft',
    };

    [$statusBg, $statusText, $statusBorder] = match ($status) {
        'draft' => ['#f8fafc', '#475569', '#e2e8f0'],
        'approved' => ['#eff6ff', '#1d4ed8', '#bfdbfe'],
        'paid' => ['#ecfdf5', '#047857', '#86efac'],
        'cancelled' => ['#fef2f2', '#b91c1c', '#fecaca'],
        default => ['#f8fafc', '#475569', '#e2e8f0'],
    };
@endphp


@php
        $expense = $this->record;

        $scope = \App\Models\FinanceExpense::scopeLabels()[$expense->expense_scope] ?? ($expense->expense_scope ?: '-');
        $category = \App\Models\FinanceExpense::categoryLabels()[$expense->category] ?? '-';
        $status = \App\Models\FinanceExpense::statusLabels()[$expense->status] ?? '-';
        $paidBy = \App\Models\FinanceExpense::paidByLabels()[$expense->paid_by] ?? '-';
        $reimbursement = \App\Models\FinanceExpense::reimbursementLabels()[$expense->reimbursement_status] ?? '-';

        $owner = $expense->ownerName() ?: '-';
        $title = $expense->title ?: ('Finance Expense #' . $expense->id);
        $amount = filled($expense->amount)
            ? number_format((float) $expense->amount, 2) . ' ' . ($expense->currency ?: '')
            : '-';

        $expenseDate = $expense->expense_date?->format('Y-m-d') ?: '-';
        $incurredFrom = $expense->incurred_from?->format('Y-m-d') ?: '-';
        $incurredTo = $expense->incurred_to?->format('Y-m-d') ?: '-';

        $rotationLabel = '-';
        if ($expense->employmentRotation) {
            $rotationLabel = $expense->employmentRotation->rotation_label ?: ('Rotation #' . $expense->employmentRotation->id);
        }

        $theme = match ((string) $expense->status) {
            \App\Models\FinanceExpense::STATUS_DRAFT => [
                'hero' => 'linear-gradient(135deg, #081a34 0%, #0b2a56 52%, #334155 100%)',
                'heroGlow' => 'rgba(148,163,184,.28)',
                'heroBorder' => 'rgba(148,163,184,.24)',
                'heroLine' => 'linear-gradient(90deg, #94a3b8, #cbd5e1)',
                'pageGlow' => 'rgba(148,163,184,.13)',
            ],
            \App\Models\FinanceExpense::STATUS_APPROVED => [
                'hero' => 'linear-gradient(135deg, #081a34 0%, #0b2a56 45%, #1d4ed8 100%)',
                'heroGlow' => 'rgba(59,130,246,.30)',
                'heroBorder' => 'rgba(96,165,250,.28)',
                'heroLine' => 'linear-gradient(90deg, #38bdf8, #3b82f6)',
                'pageGlow' => 'rgba(59,130,246,.14)',
            ],
            \App\Models\FinanceExpense::STATUS_PAID => [
                'hero' => 'linear-gradient(135deg, #06281d 0%, #064e3b 45%, #10b981 100%)',
                'heroGlow' => 'rgba(16,185,129,.30)',
                'heroBorder' => 'rgba(52,211,153,.26)',
                'heroLine' => 'linear-gradient(90deg, #34d399, #6ee7b7)',
                'pageGlow' => 'rgba(16,185,129,.16)',
            ],
            \App\Models\FinanceExpense::STATUS_CANCELLED => [
                'hero' => 'linear-gradient(135deg, #2a0b13 0%, #450a0a 45%, #dc2626 100%)',
                'heroGlow' => 'rgba(239,68,68,.28)',
                'heroBorder' => 'rgba(248,113,113,.26)',
                'heroLine' => 'linear-gradient(90deg, #fb7185, #ef4444)',
                'pageGlow' => 'rgba(239,68,68,.16)',
            ],
            default => [
                'hero' => 'linear-gradient(135deg, #081a34 0%, #0b2a56 52%, #334155 100%)',
                'heroGlow' => 'rgba(148,163,184,.28)',
                'heroBorder' => 'rgba(148,163,184,.24)',
                'heroLine' => 'linear-gradient(90deg, #94a3b8, #cbd5e1)',
                'pageGlow' => 'rgba(148,163,184,.13)',
            ],
        };

        $badgeScopeColor = match ($expense->expense_scope) {
            \App\Models\FinanceExpense::SCOPE_PRE_HIRE => ['#fff7ed', '#c2410c', '#fdba74'],
            \App\Models\FinanceExpense::SCOPE_EMPLOYMENT => ['#ecfdf5', '#047857', '#86efac'],
            \App\Models\FinanceExpense::SCOPE_ROTATION => ['#eff6ff', '#1d4ed8', '#93c5fd'],
            \App\Models\FinanceExpense::SCOPE_AD_HOC => ['#f8fafc', '#475569', '#cbd5e1'],
            default => ['#f8fafc', '#475569', '#cbd5e1'],
        };

        $badgeStatusColor = match ($expense->status) {
            \App\Models\FinanceExpense::STATUS_DRAFT => ['#f8fafc', '#475569', '#cbd5e1'],
            \App\Models\FinanceExpense::STATUS_APPROVED => ['#eff6ff', '#1d4ed8', '#93c5fd'],
            \App\Models\FinanceExpense::STATUS_PAID => ['#ecfdf5', '#047857', '#86efac'],
            \App\Models\FinanceExpense::STATUS_CANCELLED => ['#fff1f2', '#be123c', '#fda4af'],
            default => ['#f8fafc', '#475569', '#cbd5e1'],
        };

        $badgePaidByColor = match ($expense->paid_by) {
            \App\Models\FinanceExpense::PAID_BY_COMPANY => ['#ecfdf5', '#047857', '#86efac'],
            \App\Models\FinanceExpense::PAID_BY_CANDIDATE => ['#fff7ed', '#c2410c', '#fdba74'],
            \App\Models\FinanceExpense::PAID_BY_CLIENT => ['#eff6ff', '#1d4ed8', '#93c5fd'],
            \App\Models\FinanceExpense::PAID_BY_THIRD_PARTY => ['#f8fafc', '#475569', '#cbd5e1'],
            default => ['#f8fafc', '#475569', '#cbd5e1'],
        };

        $badgeReimbursementColor = match ($expense->reimbursement_status) {
            \App\Models\FinanceExpense::REIMBURSEMENT_PENDING => ['#fff7ed', '#c2410c', '#fdba74'],
            \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED => ['#eff6ff', '#1d4ed8', '#93c5fd'],
            \App\Models\FinanceExpense::REIMBURSEMENT_PAID => ['#ecfdf5', '#047857', '#86efac'],
            \App\Models\FinanceExpense::REIMBURSEMENT_REJECTED => ['#fff1f2', '#be123c', '#fda4af'],
            \App\Models\FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE => ['#f8fafc', '#475569', '#cbd5e1'],
            default => ['#f8fafc', '#475569', '#cbd5e1'],
        };

    @endphp

    <style>
        .fi-page {
            gap: 1rem !important;
        }

        .fe-view-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 6px;
            border-radius: 34px;
            background:
                radial-gradient(circle at top right, {{ $theme['pageGlow'] }}, transparent 22%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.05), transparent 20%),
                linear-gradient(180deg, rgba(7,20,39,.96) 0%, rgba(10,24,42,.96) 100%);
        }

        .fi-header {
            position: relative;
            overflow: hidden;
            border-radius: 30px !important;
            border: 1px solid {{ $theme['heroBorder'] }} !important;
            background: {{ $theme['hero'] }} !important;
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12) !important;
            padding: 22px 24px 20px 24px !important;
            margin-bottom: 2px !important;
        }

        .fi-header::before {
            content: "";
            position: absolute;
            right: -70px;
            top: -70px;
            width: 240px;
            height: 240px;
            border-radius: 999px;
            background: {{ $theme['heroGlow'] }};
            filter: blur(42px);
            opacity: .95;
        }

        .fi-header::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: {{ $theme['heroLine'] }};
        }

        .fi-header > * {
            position: relative;
            z-index: 1;
        }

        .fi-breadcrumbs,
        .fi-breadcrumbs li,
        .fi-breadcrumbs a,
        .fi-breadcrumbs span {
            color: rgba(255,255,255,.68) !important;
            font-weight: 500 !important;
        }

        .fi-header-heading {
            color: #ffffff !important;
            font-size: 44px !important;
            line-height: .95 !important;
            font-weight: 900 !important;
            letter-spacing: -.05em !important;
            max-width: 760px !important;
            white-space: normal !important;
            overflow-wrap: anywhere !important;
        }

        .fi-header-subheading {
            color: rgba(255,255,255,.84) !important;
            font-size: 16px !important;
            line-height: 1.7 !important;
            font-weight: 500 !important;
            max-width: 100% !important;
            margin-top: 12px !important;
        }

        .sf-fe-btn {
            border-radius: 999px !important;
            min-height: 44px;
            padding-inline: 16px !important;
            font-weight: 800 !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .sf-fe-btn--edit {
            background: linear-gradient(135deg, #f59e0b 0%, #ea7a00 100%) !important;
            color: #1f1400 !important;
        }

        .sf-fe-btn--approve {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            color: #ffffff !important;
        }

        .sf-fe-btn--paid {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
        }

        .sf-fe-btn--bank {
            background: linear-gradient(135deg, #06b6d4 0%, #0f766e 100%) !important;
            color: #ffffff !important;
        }

        .sf-fe-btn--partial {
            background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%) !important;
            color: #ffffff !important;
        }

        .sf-fe-btn--danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: #ffffff !important;
        }

        .sf-fe-btn--back {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
            color: #ffffff !important;
        }

        .fe-card,
        .fe-panel {
            background: rgba(12,23,38,.96);
            border: 1px solid rgba(76,167,168,.16);
            border-radius: 26px;
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }

        .fe-card { padding: 22px; }
        .fe-panel { padding: 28px; }

        .fe-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
            gap: 18px;
        }

        .fe-two-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .fe-three-grid {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .fe-label,
        .fe-field-label {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #8ea8be;
        }

        .fe-value,
        .fe-field-value {
            margin-top: 12px;
            font-size: 34px;
            font-weight: 900;
            color: #f6fbff;
            line-height: 1.08;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .fe-field-value {
            font-size: 18px;
            line-height: 1.6;
        }

        .fe-sub,
        .fe-field-value--soft {
            margin-top: 8px;
            font-size: 14px;
            color: #9fb2c3;
            line-height: 1.7;
        }

        .fe-pill {
            display: inline-block;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .fe-pill--blue { background: rgba(3,105,161,.20); color: #7dd3fc; }
        .fe-pill--amber { background: rgba(146,64,14,.22); color: #fdba74; }
        .fe-pill--purple { background: rgba(126,34,206,.20); color: #d8b4fe; }
        .fe-pill--green { background: rgba(22,101,52,.22); color: #86efac; }
        .fe-pill--slate { background: rgba(71,85,105,.22); color: #cbd5e1; }

        .fe-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 12px;
            border: 1px solid transparent;
        }

        .fe-meta-grid {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .fe-box {
            border: 1px solid rgba(76,167,168,.12);
            border-radius: 20px;
            padding: 18px;
            background: rgba(255,255,255,.03);
        }

        @media (max-width: 1100px) {
            .fe-two-grid,
            .fe-meta-grid,
            .fe-three-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .fi-header-heading {
                font-size: 34px !important;
            }

            .fi-header {
                padding: 18px 18px 16px 18px !important;
            }
        }
        /*
        | Light / Dark mode correction for Finance Expense View
        | Default is light. Dark style applies only when html/body has .dark.
        */
        html:not(.dark) .fe-view-shell,
        body:not(.dark) .fe-view-shell {
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 24%),
                radial-gradient(circle at bottom left, rgba(179,139,47,.08), transparent 26%),
                linear-gradient(180deg, #f8fcfd 0%, #eef8f8 100%) !important;
            border: 1px solid rgba(215,226,229,.9) !important;
            box-shadow: 0 18px 34px rgba(15,23,42,.06) !important;
        }
        html:not(.dark) .fe-view-shell .fe-card,
        html:not(.dark) .fe-view-shell .fe-section,
        html:not(.dark) .fe-view-shell .fe-panel,
        html:not(.dark) .fe-view-shell [class*="fe-card"],
        html:not(.dark) .fe-view-shell [class*="fe-section"],
        html:not(.dark) .fe-view-shell [class*="fe-panel"],
        body:not(.dark) .fe-view-shell .fe-card,
        body:not(.dark) .fe-view-shell .fe-section,
        body:not(.dark) .fe-view-shell .fe-panel,
        body:not(.dark) .fe-view-shell [class*="fe-card"],
        body:not(.dark) .fe-view-shell [class*="fe-section"],
        body:not(.dark) .fe-view-shell [class*="fe-panel"] {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
            border-color: #d7e2e5 !important;
            color: #0f172a !important;
            box-shadow: 0 12px 26px rgba(15,23,42,.055) !important;
        }
        html:not(.dark) .fe-view-shell .fe-label,
        html:not(.dark) .fe-view-shell [class*="label"],
        body:not(.dark) .fe-view-shell .fe-label,
        body:not(.dark) .fe-view-shell [class*="label"] {
            color: #8090a6 !important;
        }
        html:not(.dark) .fe-view-shell .fe-value,
        html:not(.dark) .fe-view-shell [class*="value"],
        html:not(.dark) .fe-view-shell .fe-card strong,
        html:not(.dark) .fe-view-shell .fe-section strong,
        html:not(.dark) .fe-view-shell h2,
        html:not(.dark) .fe-view-shell h3,
        html:not(.dark) .fe-view-shell p,
        body:not(.dark) .fe-view-shell .fe-value,
        body:not(.dark) .fe-view-shell [class*="value"],
        body:not(.dark) .fe-view-shell .fe-card strong,
        body:not(.dark) .fe-view-shell .fe-section strong,
        body:not(.dark) .fe-view-shell h2,
        body:not(.dark) .fe-view-shell h3,
        body:not(.dark) .fe-view-shell p {
            color: #0f172a !important;
        }
        html:not(.dark) .fe-view-shell .fe-muted,
        html:not(.dark) .fe-view-shell small,
        body:not(.dark) .fe-view-shell .fe-muted,
        body:not(.dark) .fe-view-shell small {
            color: #64748b !important;
        }
        /*
        | Improve Finance Expense section pill titles in light mode
        */
        html:not(.dark) .fe-view-shell .fe-section-pill,
        html:not(.dark) .fe-view-shell .fe-kicker,
        html:not(.dark) .fe-view-shell .fe-chip,
        html:not(.dark) .fe-view-shell [class*="pill"],
        html:not(.dark) .fe-view-shell [class*="kicker"],
        html:not(.dark) .fe-view-shell [class*="chip"],
        body:not(.dark) .fe-view-shell .fe-section-pill,
        body:not(.dark) .fe-view-shell .fe-kicker,
        body:not(.dark) .fe-view-shell .fe-chip,
        body:not(.dark) .fe-view-shell [class*="pill"],
        body:not(.dark) .fe-view-shell [class*="kicker"],
        body:not(.dark) .fe-view-shell [class*="chip"] {
            color: #1f4664 !important;
            font-weight: 950 !important;
            letter-spacing: .14em !important;
            text-shadow: none !important;
        }
        html:not(.dark) .fe-view-shell [class*="overview"],
        body:not(.dark) .fe-view-shell [class*="overview"] {
            color: #0f6f8f !important;
        }
        html:not(.dark) .fe-view-shell [class*="payment"],
        body:not(.dark) .fe-view-shell [class*="payment"] {
            color: #b45309 !important;
        }
        html:not(.dark) .fe-view-shell [class*="ownership"],
        body:not(.dark) .fe-view-shell [class*="ownership"] {
            color: #6d28d9 !important;
        }
        html:not(.dark) .fe-view-shell [class*="expense"],
        body:not(.dark) .fe-view-shell [class*="expense"] {
            color: #047857 !important;
        }
        html:not(.dark) .fe-view-shell [class*="internal"],
        body:not(.dark) .fe-view-shell [class*="internal"] {
            color: #475569 !important;
        }
        /*
        | Finance Expense section pill titles in dark mode
        */
        .dark .fe-view-shell .fe-section-pill,
        .dark .fe-view-shell .fe-kicker,
        .dark .fe-view-shell .fe-chip,
        .dark .fe-view-shell [class*="pill"],
        .dark .fe-view-shell [class*="kicker"],
        .dark .fe-view-shell [class*="chip"],
        html.dark .fe-view-shell .fe-section-pill,
        html.dark .fe-view-shell .fe-kicker,
        html.dark .fe-view-shell .fe-chip,
        html.dark .fe-view-shell [class*="pill"],
        html.dark .fe-view-shell [class*="kicker"],
        html.dark .fe-view-shell [class*="chip"],
        body.dark .fe-view-shell .fe-section-pill,
        body.dark .fe-view-shell .fe-kicker,
        body.dark .fe-view-shell .fe-chip,
        body.dark .fe-view-shell [class*="pill"],
        body.dark .fe-view-shell [class*="kicker"],
        body.dark .fe-view-shell [class*="chip"] {
            font-weight: 950 !important;
            letter-spacing: .14em !important;
            text-shadow: none !important;
        }

        .dark .fe-view-shell [class*="overview"],
        html.dark .fe-view-shell [class*="overview"],
        body.dark .fe-view-shell [class*="overview"] {
            color: #7dd3fc !important;
            background: rgba(14,165,233,.18) !important;
        }

        .dark .fe-view-shell [class*="payment"],
        html.dark .fe-view-shell [class*="payment"],
        body.dark .fe-view-shell [class*="payment"] {
            color: #fdba74 !important;
            background: rgba(249,115,22,.18) !important;
        }

        .dark .fe-view-shell [class*="ownership"],
        html.dark .fe-view-shell [class*="ownership"],
        body.dark .fe-view-shell [class*="ownership"] {
            color: #d8b4fe !important;
            background: rgba(168,85,247,.18) !important;
        }

        .dark .fe-view-shell [class*="expense"],
        html.dark .fe-view-shell [class*="expense"],
        body.dark .fe-view-shell [class*="expense"] {
            color: #86efac !important;
            background: rgba(34,197,94,.18) !important;
        }

        .dark .fe-view-shell [class*="internal"],
        html.dark .fe-view-shell [class*="internal"],
        body.dark .fe-view-shell [class*="internal"] {
            color: #cbd5e1 !important;
            background: rgba(148,163,184,.18) !important;
        }
        /*
        | Force Finance Expense detail cards to follow dark mode
        */
        .dark .fe-view-shell,
        html.dark .fe-view-shell,
        body.dark .fe-view-shell {
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 24%),
                radial-gradient(circle at bottom left, rgba(179,139,47,.08), transparent 26%),
                linear-gradient(180deg, rgba(4,13,28,.98) 0%, rgba(7,20,39,.98) 100%) !important;
        }

        .dark .fe-view-shell .fe-stat-card,
        .dark .fe-view-shell .fe-panel,
        .dark .fe-view-shell .fe-section,
        .dark .fe-view-shell .fe-card,
        .dark .fe-view-shell .fe-info-card,
        .dark .fe-view-shell [class*="card"],
        .dark .fe-view-shell [class*="panel"],
        .dark .fe-view-shell [class*="section"] {
            background: linear-gradient(180deg, rgba(11,22,38,.96) 0%, rgba(13,27,45,.94) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
            color: #f8fbff !important;
            box-shadow: 0 18px 34px rgba(0,0,0,.22) !important;
        }

        .dark .fe-view-shell .fe-label,
        .dark .fe-view-shell [class*="label"] {
            color: #9fb2c3 !important;
        }

        .dark .fe-view-shell .fe-value,
        .dark .fe-view-shell [class*="value"],
        .dark .fe-view-shell strong,
        .dark .fe-view-shell b {
            color: #ffffff !important;
        }

        .dark .fe-view-shell .fe-sub,
        .dark .fe-view-shell .fe-muted,
        .dark .fe-view-shell [class*="sub"],
        .dark .fe-view-shell [class*="muted"] {
            color: #a8bbcc !important;
        }

        .dark .fe-view-shell .fi-badge,
        .dark .fe-view-shell [class*="badge"] {
            border-color: rgba(255,255,255,.16) !important;
        }

        .dark .fe-view-shell .fe-section-title,
        .dark .fe-view-shell .fe-panel-title,
        .dark .fe-view-shell .fe-card-title,
        .dark .fe-view-shell h2,
        .dark .fe-view-shell h3,
        .dark .fe-view-shell h4 {
            color: #ffffff !important;
        }

        .dark .fe-view-shell .fe-section-kicker,
        .dark .fe-view-shell .fe-kicker,
        .dark .fe-view-shell .fe-pill,
        .dark .fe-view-shell [class*="kicker"],
        .dark .fe-view-shell [class*="pill"] {
            color: #e8f7ff !important;
            border-color: rgba(255,255,255,.14) !important;
        }

        .dark .fe-view-shell input,
        .dark .fe-view-shell textarea,
        .dark .fe-view-shell select,
        .dark .fe-view-shell .fi-input,
        .dark .fe-view-shell .fi-select-input,
        .dark .fe-view-shell .fi-input-wrp {
            background: rgba(255,255,255,.04) !important;
            color: #ffffff !important;
            border-color: rgba(76,167,168,.18) !important;
        }
        /*
        | FINAL dark mode force for Finance Expense View cards
        | This targets the actual white blocks inside the page shell.
        */
        html.dark .fe-view-shell > div:not(.fi-header),
        html.dark .fe-view-shell > section,
        html.dark .fe-view-shell .grid > div,
        html.dark .fe-view-shell [class*="grid"] > div,
        html.dark .fe-view-shell [class*="overview"],
        html.dark .fe-view-shell [class*="payment"],
        html.dark .fe-view-shell [class*="ownership"],
        html.dark .fe-view-shell [class*="details"],
        html.dark .fe-view-shell [class*="notes"],
        body.dark .fe-view-shell > div:not(.fi-header),
        body.dark .fe-view-shell > section,
        body.dark .fe-view-shell .grid > div,
        body.dark .fe-view-shell [class*="grid"] > div,
        body.dark .fe-view-shell [class*="overview"],
        body.dark .fe-view-shell [class*="payment"],
        body.dark .fe-view-shell [class*="ownership"],
        body.dark .fe-view-shell [class*="details"],
        body.dark .fe-view-shell [class*="notes"],
        .dark .fe-view-shell > div:not(.fi-header),
        .dark .fe-view-shell > section,
        .dark .fe-view-shell .grid > div,
        .dark .fe-view-shell [class*="grid"] > div,
        .dark .fe-view-shell [class*="overview"],
        .dark .fe-view-shell [class*="payment"],
        .dark .fe-view-shell [class*="ownership"],
        .dark .fe-view-shell [class*="details"],
        .dark .fe-view-shell [class*="notes"] {
            background: linear-gradient(180deg, rgba(11,22,38,.98) 0%, rgba(13,27,45,.96) 100%) !important;
            border-color: rgba(76,167,168,.20) !important;
            color: #f8fbff !important;
            box-shadow: 0 18px 34px rgba(0,0,0,.28) !important;
        }

        html.dark .fe-view-shell > div:not(.fi-header) *,
        html.dark .fe-view-shell > section *,
        html.dark .fe-view-shell .grid > div *,
        html.dark .fe-view-shell [class*="grid"] > div *,
        body.dark .fe-view-shell > div:not(.fi-header) *,
        body.dark .fe-view-shell > section *,
        body.dark .fe-view-shell .grid > div *,
        body.dark .fe-view-shell [class*="grid"] > div *,
        .dark .fe-view-shell > div:not(.fi-header) *,
        .dark .fe-view-shell > section *,
        .dark .fe-view-shell .grid > div *,
        .dark .fe-view-shell [class*="grid"] > div * {
            color: inherit;
        }

        html.dark .fe-view-shell [class*="label"],
        html.dark .fe-view-shell [class*="Label"],
        html.dark .fe-view-shell .text-slate-400,
        html.dark .fe-view-shell .text-slate-500,
        html.dark .fe-view-shell .text-gray-400,
        html.dark .fe-view-shell .text-gray-500,
        body.dark .fe-view-shell [class*="label"],
        body.dark .fe-view-shell [class*="Label"],
        body.dark .fe-view-shell .text-slate-400,
        body.dark .fe-view-shell .text-slate-500,
        body.dark .fe-view-shell .text-gray-400,
        body.dark .fe-view-shell .text-gray-500,
        .dark .fe-view-shell [class*="label"],
        .dark .fe-view-shell [class*="Label"],
        .dark .fe-view-shell .text-slate-400,
        .dark .fe-view-shell .text-slate-500,
        .dark .fe-view-shell .text-gray-400,
        .dark .fe-view-shell .text-gray-500 {
            color: #9fb2c3 !important;
        }

        html.dark .fe-view-shell [class*="value"],
        html.dark .fe-view-shell [class*="Value"],
        html.dark .fe-view-shell strong,
        html.dark .fe-view-shell b,
        html.dark .fe-view-shell .text-slate-900,
        html.dark .fe-view-shell .text-gray-900,
        html.dark .fe-view-shell .text-black,
        body.dark .fe-view-shell [class*="value"],
        body.dark .fe-view-shell [class*="Value"],
        body.dark .fe-view-shell strong,
        body.dark .fe-view-shell b,
        body.dark .fe-view-shell .text-slate-900,
        body.dark .fe-view-shell .text-gray-900,
        body.dark .fe-view-shell .text-black,
        .dark .fe-view-shell [class*="value"],
        .dark .fe-view-shell [class*="Value"],
        .dark .fe-view-shell strong,
        .dark .fe-view-shell b,
        .dark .fe-view-shell .text-slate-900,
        .dark .fe-view-shell .text-gray-900,
        .dark .fe-view-shell .text-black {
            color: #ffffff !important;
        }

        html.dark .fe-view-shell [class*="sub"],
        html.dark .fe-view-shell [class*="muted"],
        html.dark .fe-view-shell small,
        body.dark .fe-view-shell [class*="sub"],
        body.dark .fe-view-shell [class*="muted"],
        body.dark .fe-view-shell small,
        .dark .fe-view-shell [class*="sub"],
        .dark .fe-view-shell [class*="muted"],
        .dark .fe-view-shell small {
            color: #a8bbcc !important;
        }

        html.dark .fe-view-shell [class*="pill"],
        html.dark .fe-view-shell [class*="kicker"],
        html.dark .fe-view-shell [class*="chip"],
        body.dark .fe-view-shell [class*="pill"],
        body.dark .fe-view-shell [class*="kicker"],
        body.dark .fe-view-shell [class*="chip"],
        .dark .fe-view-shell [class*="pill"],
        .dark .fe-view-shell [class*="kicker"],
        .dark .fe-view-shell [class*="chip"] {
            background: rgba(76,167,168,.16) !important;
            color: #dffcff !important;
            border-color: rgba(76,167,168,.22) !important;
        }

        html.dark .fe-view-shell .fi-badge,
        body.dark .fe-view-shell .fi-badge,
        .dark .fe-view-shell .fi-badge {
            background: rgba(255,255,255,.08) !important;
            border-color: rgba(255,255,255,.16) !important;
            color: #f8fbff !important;
        }
        /*
        | Absolute dark mode override for Finance Expense View
        | Fixes remaining white parent panels in dark mode.
        */
        html.dark .fe-view-shell,
        body.dark .fe-view-shell,
        .dark .fe-view-shell {
            background:
                radial-gradient(circle at top right, rgba(76,167,168,.10), transparent 24%),
                radial-gradient(circle at bottom left, rgba(179,139,47,.08), transparent 26%),
                linear-gradient(180deg, rgba(4,13,28,.98) 0%, rgba(7,20,39,.98) 100%) !important;
            border-color: rgba(76,167,168,.18) !important;
        }

        html.dark .fe-view-shell > *,
        html.dark .fe-view-shell section,
        html.dark .fe-view-shell article,
        html.dark .fe-view-shell div[class^="fe-"],
        html.dark .fe-view-shell div[class*=" fe-"],
        body.dark .fe-view-shell > *,
        body.dark .fe-view-shell section,
        body.dark .fe-view-shell article,
        body.dark .fe-view-shell div[class^="fe-"],
        body.dark .fe-view-shell div[class*=" fe-"],
        .dark .fe-view-shell > *,
        .dark .fe-view-shell section,
        .dark .fe-view-shell article,
        .dark .fe-view-shell div[class^="fe-"],
        .dark .fe-view-shell div[class*=" fe-"] {
            background-color: rgba(11,22,38,.96) !important;
            background-image: linear-gradient(180deg, rgba(11,22,38,.98) 0%, rgba(13,27,45,.96) 100%) !important;
            border-color: rgba(76,167,168,.20) !important;
            color: #f8fbff !important;
        }

        html.dark .fe-view-shell .fi-header,
        body.dark .fe-view-shell .fi-header,
        .dark .fe-view-shell .fi-header {
            background: inherit !important;
        }

        html.dark .fe-view-shell [class*="label"],
        html.dark .fe-view-shell [class*="Label"],
        body.dark .fe-view-shell [class*="label"],
        body.dark .fe-view-shell [class*="Label"],
        .dark .fe-view-shell [class*="label"],
        .dark .fe-view-shell [class*="Label"] {
            color: #9fb2c3 !important;
        }

        html.dark .fe-view-shell [class*="value"],
        html.dark .fe-view-shell [class*="Value"],
        html.dark .fe-view-shell strong,
        html.dark .fe-view-shell b,
        body.dark .fe-view-shell [class*="value"],
        body.dark .fe-view-shell [class*="Value"],
        body.dark .fe-view-shell strong,
        body.dark .fe-view-shell b,
        .dark .fe-view-shell [class*="value"],
        .dark .fe-view-shell [class*="Value"],
        .dark .fe-view-shell strong,
        .dark .fe-view-shell b {
            color: #ffffff !important;
        }

        html.dark .fe-view-shell [class*="pill"],
        html.dark .fe-view-shell [class*="kicker"],
        html.dark .fe-view-shell [class*="chip"],
        body.dark .fe-view-shell [class*="pill"],
        body.dark .fe-view-shell [class*="kicker"],
        body.dark .fe-view-shell [class*="chip"],
        .dark .fe-view-shell [class*="pill"],
        .dark .fe-view-shell [class*="kicker"],
        .dark .fe-view-shell [class*="chip"] {
            background: rgba(76,167,168,.18) !important;
            color: #e8fdff !important;
            border-color: rgba(76,167,168,.24) !important;
        }
        /*
        | Fix remaining white parent sections in dark mode
        | Targets the large wrapper blocks that contain section pills.
        */
        html.dark .fe-view-shell section:has(.fe-section-pill),
        html.dark .fe-view-shell div:has(> .fe-section-pill),
        html.dark .fe-view-shell div:has(> [class*="pill"]),
        body.dark .fe-view-shell section:has(.fe-section-pill),
        body.dark .fe-view-shell div:has(> .fe-section-pill),
        body.dark .fe-view-shell div:has(> [class*="pill"]),
        .dark .fe-view-shell section:has(.fe-section-pill),
        .dark .fe-view-shell div:has(> .fe-section-pill),
        .dark .fe-view-shell div:has(> [class*="pill"]) {
            background: linear-gradient(180deg, rgba(7,20,39,.98) 0%, rgba(10,24,42,.98) 100%) !important;
            border-color: rgba(76,167,168,.22) !important;
            color: #f8fbff !important;
            box-shadow: 0 18px 34px rgba(0,0,0,.28) !important;
        }

        html.dark .fe-view-shell section:has(.fe-section-pill) > *,
        html.dark .fe-view-shell div:has(> .fe-section-pill) > *,
        html.dark .fe-view-shell div:has(> [class*="pill"]) > *,
        body.dark .fe-view-shell section:has(.fe-section-pill) > *,
        body.dark .fe-view-shell div:has(> .fe-section-pill) > *,
        body.dark .fe-view-shell div:has(> [class*="pill"]) > *,
        .dark .fe-view-shell section:has(.fe-section-pill) > *,
        .dark .fe-view-shell div:has(> .fe-section-pill) > *,
        .dark .fe-view-shell div:has(> [class*="pill"]) > * {
            color: inherit !important;
        }

        html.dark .fe-view-shell section:has(.fe-section-pill) .fe-section-pill,
        html.dark .fe-view-shell div:has(> .fe-section-pill) .fe-section-pill,
        body.dark .fe-view-shell section:has(.fe-section-pill) .fe-section-pill,
        body.dark .fe-view-shell div:has(> .fe-section-pill) .fe-section-pill,
        .dark .fe-view-shell section:has(.fe-section-pill) .fe-section-pill,
        .dark .fe-view-shell div:has(> .fe-section-pill) .fe-section-pill {
            background: rgba(76,167,168,.18) !important;
            color: #e8fdff !important;
            border-color: rgba(76,167,168,.28) !important;
        }
        /*
        | Remove leftover white wrapper backgrounds in dark mode
        | Keeps inner cards dark and removes the parent white container.
        */
        html.dark .fe-view-shell *[style*="background"],
        body.dark .fe-view-shell *[style*="background"],
        .dark .fe-view-shell *[style*="background"] {
            background-color: transparent !important;
        }

        html.dark .fe-view-shell .fe-section,
        html.dark .fe-view-shell .fe-panel,
        html.dark .fe-view-shell .fe-card,
        html.dark .fe-view-shell [class*="section"],
        html.dark .fe-view-shell [class*="panel"],
        html.dark .fe-view-shell [class*="card"],
        body.dark .fe-view-shell .fe-section,
        body.dark .fe-view-shell .fe-panel,
        body.dark .fe-view-shell .fe-card,
        body.dark .fe-view-shell [class*="section"],
        body.dark .fe-view-shell [class*="panel"],
        body.dark .fe-view-shell [class*="card"],
        .dark .fe-view-shell .fe-section,
        .dark .fe-view-shell .fe-panel,
        .dark .fe-view-shell .fe-card,
        .dark .fe-view-shell [class*="section"],
        .dark .fe-view-shell [class*="panel"],
        .dark .fe-view-shell [class*="card"] {
            background: linear-gradient(180deg, rgba(9,22,39,.98) 0%, rgba(11,26,45,.96) 100%) !important;
            border-color: rgba(76,167,168,.22) !important;
        }

        html.dark .fe-view-shell > div,
        html.dark .fe-view-shell > section,
        body.dark .fe-view-shell > div,
        body.dark .fe-view-shell > section,
        .dark .fe-view-shell > div,
        .dark .fe-view-shell > section {
            background-color: transparent !important;
        }

        html.dark .fe-view-shell > div > div,
        html.dark .fe-view-shell > section > div,
        body.dark .fe-view-shell > div > div,
        body.dark .fe-view-shell > section > div,
        .dark .fe-view-shell > div > div,
        .dark .fe-view-shell > section > div {
            background-color: transparent !important;
        }

        html.dark .fe-view-shell .fe-section:has(.fe-section-pill),
        html.dark .fe-view-shell .fe-panel:has(.fe-section-pill),
        html.dark .fe-view-shell .fe-card:has(.fe-section-pill),
        body.dark .fe-view-shell .fe-section:has(.fe-section-pill),
        body.dark .fe-view-shell .fe-panel:has(.fe-section-pill),
        body.dark .fe-view-shell .fe-card:has(.fe-section-pill),
        .dark .fe-view-shell .fe-section:has(.fe-section-pill),
        .dark .fe-view-shell .fe-panel:has(.fe-section-pill),
        .dark .fe-view-shell .fe-card:has(.fe-section-pill) {
            background: linear-gradient(180deg, rgba(9,22,39,.98) 0%, rgba(11,26,45,.96) 100%) !important;
            border-color: rgba(76,167,168,.22) !important;
        }

    </style>

    <div class="fe-view-shell">
        
<section class="sf-fe-body">
    <section class="sf-finance-grid sf-fe-summary-grid">
        <div class="sf-finance-card">
            <span class="material-symbols-rounded sf-card-icon">payments</span>
            <div class="sf-finance-title">Amount</div>
            <div class="sf-finance-number">{{ $amount }}</div>
            <div class="sf-meta">Recorded expense value.</div>
        </div>

        <div class="sf-finance-card">
            <span class="material-symbols-rounded sf-card-icon">event</span>
            <div class="sf-finance-title">Expense Date</div>
            <div class="sf-finance-number">{{ $expenseDate }}</div>
            <div class="sf-meta">Primary expense date.</div>
        </div>

        <div class="sf-finance-card">
            <span class="material-symbols-rounded sf-card-icon">person</span>
            <div class="sf-finance-title">Owner</div>
            <div class="sf-finance-number">{{ $owner }}</div>
            <div class="sf-meta">Primary linked person.</div>
        </div>

        <div class="sf-finance-card">
            <span class="material-symbols-rounded sf-card-icon">category</span>
            <div class="sf-finance-title">Category</div>
            <div class="sf-finance-number">{{ $expense?->category ?: '-' }}</div>
            <div class="sf-meta">Expense classification.</div>
        </div>
    </section>

    <section class="sf-block-grid">
        <div class="sf-block">
            <div class="sf-block-head">
                <div>
                    <div class="sf-block-title">Overview</div>
                    <div class="sf-block-subtitle">Core expense information and workflow status.</div>
                </div>
                <div class="sf-pill sf-block-head-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3 11V3h8v8H3Zm2-2h4V5H5v4Zm8 2V3h8v8h-8Zm2-2h4V5h-4v4ZM3 21v-8h8v8H3Zm2-2h4v-4H5v4Zm8 2v-8h8v8h-8Zm2-2h4v-4h-4v4Z"/></svg>
                </div>
            </div>

            <div class="sf-list">
                <div class="sf-row">
                    <span>Scope</span>
                    <strong>{{ $scopeLabel }}</strong>
                </div>

                <div class="sf-row">
                    <span>Status</span>
                    <strong>{{ $statusLabel }}</strong>
                </div>

                <div class="sf-row">
                    <span>Category</span>
                    <strong>{{ $expense?->category ?: '-' }}</strong>
                </div>

                <div class="sf-row">
                    <span>Title</span>
                    <strong>{{ $title }}</strong>
                </div>

                <div class="sf-row">
                    <span>Expense Date</span>
                    <strong>{{ $expenseDate }}</strong>
                </div>

                <div class="sf-row">
                    <span>Amount</span>
                    <strong>{{ $amount }}</strong>
                </div>
            </div>
        </div>

        <div class="sf-block">
            <div class="sf-block-head">
                <div>
                    <div class="sf-block-title">Payment & Approval</div>
                    <div class="sf-block-subtitle">Payment responsibility, reimbursement, and approval tracking.</div>
                </div>
                <div class="sf-pill sf-block-head-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22q-3.475-.875-5.738-3.988T4 11.1V5l8-3 8 3v6.1q0 3.8-2.263 6.913T12 22Zm-1.05-6.45 5.65-5.65-1.4-1.45-4.25 4.25-2.15-2.15-1.4 1.4 3.55 3.6Z"/></svg>
                </div>
            </div>

            <div class="sf-list">
                <div class="sf-row">
                    <span>Paid By</span>
                    <strong>{{ $paidByLabel }}</strong>
                </div>

                <div class="sf-row">
                    <span>Reimbursement</span>
                    <strong>{{ $reimbursementLabel }}</strong>
                </div>

                <div class="sf-row">
                    <span>First Mobilization</span>
                    <strong>{{ (bool) ($expense?->is_first_mobilization ?? false) ? 'Yes' : 'No' }}</strong>
                </div>

                <div class="sf-row">
                    <span>Created By</span>
                    <strong>{{ $sfCreatedByName }}</strong>
                </div>

                <div class="sf-row">
                    <span>Approved By</span>
                    <strong>{{ $sfApprovedByName }}</strong>
                </div>

                <div class="sf-row">
                    <span>Has Attachment</span>
                    <strong>{{ $sfHasAttachment ? 'Yes' : 'No' }}</strong>
                </div>
            </div>
        </div>
    </section>

    @php
        /*
         |--------------------------------------------------------------------------
         | Dynamic ownership view
         |--------------------------------------------------------------------------
         | Show the real stage only, instead of repeating the same person under
         | Job Application / Pre-Employment / Employment.
         */
        $sfExpenseScope = (string) ($expense?->expense_scope ?? '');

        $sfStageLabel = match ($sfExpenseScope) {
            \App\Models\FinanceExpense::SCOPE_PRE_HIRE => 'Pre-Employment',
            \App\Models\FinanceExpense::SCOPE_EMPLOYMENT => 'Employment',
            \App\Models\FinanceExpense::SCOPE_ROTATION => 'Rotation',
            \App\Models\FinanceExpense::SCOPE_AD_HOC => 'Ad Hoc',
            default => filled($sfExpenseScope) ? ucwords(str_replace('_', ' ', $sfExpenseScope)) : 'Ad Hoc',
        };

        $sfStageValue = match ($sfExpenseScope) {
            \App\Models\FinanceExpense::SCOPE_PRE_HIRE => $expense?->preEmployment?->candidate_name
                ?? $expense?->preEmployment?->full_name
                ?? $expense?->jobApplication?->full_name
                ?? $expense?->jobApplication?->applicant_name
                ?? $owner
                ?? '-',

            \App\Models\FinanceExpense::SCOPE_EMPLOYMENT => $expense?->employment?->employee_name
                ?? $owner
                ?? '-',

            \App\Models\FinanceExpense::SCOPE_ROTATION => $expense?->employmentRotation?->rotation_label
                ?? $expense?->rotation?->rotation_label
                ?? ('Rotation #' . ($expense?->employment_rotation_id ?: '-')),

            default => $owner ?: '-',
        };

        $sfJobLabel = $expense?->jobOpening?->title ?? $expense?->job?->title ?? null;
        $sfClientLabel = $expense?->client?->name ?? $expense?->employment?->client?->name ?? null;
        $sfProjectLabel = $expense?->project?->name ?? $expense?->employment?->project?->name ?? null;
    @endphp

    <section class="sf-block">
        <div class="sf-block-head">
            <div>
                <div class="sf-block-title">Ownership & Links</div>
                <div class="sf-block-subtitle">Clean ownership stage, job, client, and project links.</div>
            </div>
            <div class="sf-pill sf-block-head-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M3.9 12q0-1.71 1.195-2.905Q6.29 7.9 8 7.9h4v2H8q-.875 0-1.488.613Q5.9 11.125 5.9 12t.612 1.488Q7.125 14.1 8 14.1h4v2H8q-1.71 0-2.905-1.195Q3.9 13.71 3.9 12Zm5.1 1v-2h6v2H9Zm3-3.1v-2h4q1.71 0 2.905 1.195Q20.1 10.29 20.1 12t-1.195 2.905Q17.71 16.1 16 16.1h-4v-2h4q.875 0 1.488-.612Q18.1 12.875 18.1 12t-.612-1.487Q16.875 9.9 16 9.9h-4Z"/></svg>
            </div>
        </div>

        <div class="sf-list sf-list-compact">
            <div class="sf-row">
                <span>Owner</span>
                <strong>{{ $owner ?: '-' }}</strong>
            </div>

            <div class="sf-row">
                <span>Expense Stage</span>
                <strong>{{ $sfStageLabel }}</strong>
            </div>

            <div class="sf-row">
                <span>Stage Record</span>
                <strong>{{ $sfStageValue }}</strong>
            </div>

            @if(filled($sfJobLabel))
                <div class="sf-row">
                    <span>Job</span>
                    <strong>{{ $sfJobLabel }}</strong>
                </div>
            @endif

            @if(filled($sfClientLabel))
                <div class="sf-row">
                    <span>Client</span>
                    <strong>{{ $sfClientLabel }}</strong>
                </div>
            @endif

            @if(filled($sfProjectLabel))
                <div class="sf-row">
                    <span>Project</span>
                    <strong>{{ $sfProjectLabel }}</strong>
                </div>
            @endif
        </div>
    </section>

    <section class="sf-block-grid">
        <div class="sf-block">
            @php
                /*
                 |--------------------------------------------------------------------------
                 | Dynamic expense details rows
                 |--------------------------------------------------------------------------
                 | Use the same generic DB columns:
                 | expense_date = main date
                 | incurred_from / incurred_to = category-specific period dates
                 */
                $sfCategory = strtolower((string) ($expense?->category ?? $expense?->expense_category ?? 'other'));

                $sfExpenseDateLabel = match ($sfCategory) {
                    'ticket' => 'Booking / Ticket Date',
                    'hotel' => 'Booking Date',
                    'visa' => 'Visa Submission Date',
                    'medical' => 'Medical Date',
                    default => 'Expense Date',
                };

                $sfFromLabel = match ($sfCategory) {
                    'ticket' => 'Departure Date',
                    'hotel' => 'Check-in Date',
                    'visa' => 'Submission Date',
                    'medical' => 'Medical Date',
                    default => 'Start Date',
                };

                $sfToLabel = match ($sfCategory) {
                    'ticket' => 'Return Date',
                    'hotel' => 'Check-out Date',
                    'visa' => 'Visa Expiry Date',
                    'medical' => 'Follow-up Date',
                    default => 'End Date',
                };

                $sfDescriptionValue = $expense?->description ?: $expense?->title ?: '-';

                $sfMainExpenseDate = $expense?->expense_date
                    ? \Illuminate\Support\Carbon::parse($expense->expense_date)->format('Y-m-d')
                    : '-';

                $sfIncurredFrom = $expense?->incurred_from
                    ? \Illuminate\Support\Carbon::parse($expense->incurred_from)->format('Y-m-d')
                    : null;

                $sfIncurredTo = $expense?->incurred_to
                    ? \Illuminate\Support\Carbon::parse($expense->incurred_to)->format('Y-m-d')
                    : null;

                $sfShowFromTo = in_array($sfCategory, ['ticket', 'hotel', 'visa', 'medical'], true)
                    || filled($sfIncurredFrom)
                    || filled($sfIncurredTo);

                $sfTripType = null;
                if ($sfCategory === 'ticket') {
                    $sfTripType = filled($sfIncurredTo) ? 'Round Trip' : 'One Way';
                }

                $sfVendorName = $expense?->vendor_name ?? $expense?->vendor_supplier ?? null;
                $sfTreasuryName = $expense?->treasuryAccount?->account_name
                    ?? $expense?->treasuryAccount?->name
                    ?? $expense?->treasuryAccount?->title
                    ?? null;
            @endphp

            <div class="sf-block-head">
                <div>
                    <div class="sf-block-title">Expense Details</div>
                    <div class="sf-block-subtitle">
                        Dynamic details based on the expense category.
                    </div>
                    <div class="sf-expense-field-hint">
                        {{ $sfExpenseDateLabel }} = main date
                        @if($sfShowFromTo)
                            · {{ $sfFromLabel }} / {{ $sfToLabel }} = covered period or follow-up date
                        @endif
                    </div>
                </div>
                <div class="sf-pill sf-block-head-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M7 21q-.825 0-1.413-.588T5 19V5q0-.825.588-1.413T7 3h10q.825 0 1.413.588T19 5v14q0 .825-.588 1.413T17 21H7Zm0-2h10V5H7v14Zm2-10h6V7H9v2Zm0 4h6v-2H9v2Zm0 4h4v-2H9v2Z"/></svg>
                </div>
            </div>

            <div class="sf-list">
                <div class="sf-row">
                    <span>Description</span>
                    <strong>{{ $sfDescriptionValue }}</strong>
                </div>

                <div class="sf-row">
                    <span>{{ $sfExpenseDateLabel }}</span>
                    <strong>{{ $sfMainExpenseDate }}</strong>
                </div>

                @if($sfCategory === 'ticket')
                    <div class="sf-row">
                        <span>Trip Type</span>
                        <strong>{{ $sfTripType }}</strong>
                    </div>
                @endif

                @if($sfShowFromTo)
                    <div class="sf-row">
                        <span>{{ $sfFromLabel }}</span>
                        <strong>{{ $sfIncurredFrom ?: '-' }}</strong>
                    </div>

                    <div class="sf-row">
                        <span>{{ $sfToLabel }}</span>
                        <strong>{{ $sfIncurredTo ?: ($sfCategory === 'ticket' ? 'One Way / No Return' : '-') }}</strong>
                    </div>
                @endif

                @if(filled($sfVendorName))
                    <div class="sf-row">
                        <span>Vendor / Supplier</span>
                        <strong>{{ $sfVendorName }}</strong>
                    </div>
                @endif

                @if(filled($sfTreasuryName))
                    <div class="sf-row">
                        <span>Treasury Account</span>
                        <strong>{{ $sfTreasuryName }}</strong>
                    </div>
                @endif

                <div class="sf-row">
                    <span>Attachment File</span>
                    <strong>
                        @if($sfAttachmentPath)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($sfAttachmentPath) }}" target="_blank" class="sf-pill">Open Attachment</a>
                        @else
                            -
                        @endif
                    </strong>
                </div>
            </div>
        </div>

        <div class="sf-block">
            <div class="sf-block-head">
                <div>
                    <div class="sf-block-title">Internal Notes</div>
                    <div class="sf-block-subtitle">Administrative notes and record timestamps.</div>
                </div>
                <div class="sf-pill sf-block-head-icon" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M4 17.5v-2h10v2H4Zm0-5v-2h16v2H4Zm0-5v-2h16v2H4Z"/></svg>
                </div>
            </div>

            <div class="sf-list">
                <div class="sf-row">
                    <span>Notes</span>
                    <strong>{{ $expense?->internal_notes ?: '-' }}</strong>
                </div>

                <div class="sf-row">
                    <span>Created At</span>
                    <strong>{{ $expense?->created_at ? $expense->created_at->format('Y-m-d H:i') : '-' }}</strong>
                </div>

                <div class="sf-row">
                    <span>Last Updated</span>
                    <strong>{{ $expense?->updated_at ? $expense->updated_at->format('Y-m-d H:i') : '-' }}</strong>
                </div>
            </div>
        </div>
    </section>
</section>

<style>
    /*
     * Finance Expenses - Material Design v3 polish
     * Visual-only: no data structure changes, no logic changes.
     */

    .fi-page {
        gap: 1rem !important;
    }

    .fi-main,
    .fi-page,
    .fi-page > section,
    .fi-page > div {
        max-width: 1240px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .fi-page > header,
    .fi-header,
    header.fi-header {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 30px !important;
        padding: 32px !important;
        border: 1px solid rgba(76, 167, 168, .22) !important;
        background:
            radial-gradient(circle at 90% 18%, rgba(76, 167, 168, .30), transparent 24%),
            radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .18), transparent 28%),
            linear-gradient(135deg, #081a34 0%, #12385d 55%, #2f6f73 100%) !important;
        box-shadow: 0 18px 36px rgba(15, 23, 42, .12) !important;
        margin-bottom: 24px !important;
        color: #ffffff !important;
    }

    .fi-page > header::after,
    .fi-header::after,
    header.fi-header::after {
        content: "" !important;
        position: absolute !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        height: 4px !important;
        background: linear-gradient(90deg, #4ca7a8, #b38b2f) !important;
    }

    .fi-page > header *,
    .fi-header *,
    header.fi-header * {
        position: relative !important;
        z-index: 1 !important;
    }

    .fi-header-heading,
    .fi-header-heading h1,
    .fi-page h1 {
        color: #ffffff !important;
        font-size: clamp(42px, 4vw, 62px) !important;
        line-height: .95 !important;
        font-weight: 950 !important;
        letter-spacing: -.05em !important;
    }

    .fi-header-subheading,
    .fi-header-subheading *,
    .fi-breadcrumbs,
    .fi-breadcrumbs * {
        color: rgba(255, 255, 255, .78) !important;
        font-weight: 650 !important;
    }

    .fi-header-actions,
    .fi-ac {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .fi-header-actions .fi-btn,
    .fi-ac .fi-btn,
    .fi-page > header .fi-btn,
    .fi-header .fi-btn {
        min-height: 46px !important;
        border-radius: 999px !important;
        padding-inline: 18px !important;
        font-weight: 900 !important;
        border: 0 !important;
        box-shadow: 0 12px 24px rgba(15, 23, 42, .14) !important;
    }

    .fi-header-actions .fi-btn-color-primary,
    .fi-header-actions .fi-btn-color-warning,
    .fi-ac .fi-btn-color-primary,
    .fi-ac .fi-btn-color-warning {
        background: #f2b705 !important;
        color: #3b2a00 !important;
    }

    .fi-header-actions .fi-btn-color-success,
    .fi-ac .fi-btn-color-success {
        background: #10b981 !important;
        color: #ffffff !important;
    }

    .fi-header-actions .fi-btn-color-danger,
    .fi-ac .fi-btn-color-danger {
        background: #ef4444 !important;
        color: #ffffff !important;
    }

    /*
     * Filter section / table shell
     */
    .fi-section,
    .fi-ta-ctn,
    .fi-ta,
    .fi-ta-outer,
    .fi-ta-content,
    .fi-ta-table,
    .fi-ta-header,
    .fi-ta-toolbar,
    .fi-ta-filters,
    .fi-pagination {
        border-radius: 24px !important;
    }

    .fi-section,
    .fi-ta-ctn,
    .fi-ta-outer {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%) !important;
        border: 1px solid #d7e2e5 !important;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06) !important;
        overflow: hidden !important;
    }

    .fi-section-header {
        background: #f4f8fa !important;
        border-bottom: 1px solid #d7e2e5 !important;
        padding: 18px 22px !important;
    }

    .fi-section-header-heading,
    .fi-section h2,
    .fi-section h3 {
        color: #1f4664 !important;
        font-weight: 950 !important;
        letter-spacing: -.02em !important;
    }

    .fi-section-content {
        background: transparent !important;
        padding: 22px !important;
    }

    .fi-fo-field-wrp-label,
    .fi-fo-field-wrp-label span,
    .fi-section label {
        color: #334155 !important;
        font-weight: 850 !important;
    }

    .fi-input-wrp,
    .fi-select,
    .fi-input,
    .fi-select-input,
    input,
    select,
    textarea {
        border-radius: 14px !important;
        background: #ffffff !important;
        color: #0f172a !important;
        border-color: #d7e2e5 !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .035) !important;
    }

    input::placeholder,
    textarea::placeholder {
        color: #94a3b8 !important;
    }

    /*
     * Reset / Apply buttons inside filters
     */
    .fi-section .fi-btn,
    .fi-ta-filters .fi-btn,
    form .fi-btn {
        border-radius: 999px !important;
        font-weight: 900 !important;
        min-height: 40px !important;
    }

    .fi-section .fi-btn-color-warning,
    .fi-section .fi-btn-color-primary,
    .fi-ta-filters .fi-btn-color-warning,
    .fi-ta-filters .fi-btn-color-primary {
        background: #f2b705 !important;
        color: #3b2a00 !important;
        border-color: #f2b705 !important;
    }

    .fi-section .fi-btn-color-danger,
    .fi-ta-filters .fi-btn-color-danger,
    a[href*="reset"],
    button[type="reset"] {
        color: #ef4444 !important;
        font-weight: 950 !important;
    }

    /*
     * Table Material Design
     */
    .fi-ta {
        border: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    .fi-ta-header,
    .fi-ta-toolbar {
        background: #ffffff !important;
        border-bottom: 1px solid #e4ecef !important;
    }

    .fi-ta-search-field .fi-input-wrp,
    .fi-ta-search-field input {
        border-radius: 999px !important;
        min-height: 42px !important;
    }

    .fi-ta-table thead th,
    table thead th {
        background: #eef5f8 !important;
        color: #1f4664 !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .10em !important;
        text-transform: uppercase !important;
        border-bottom: 1px solid #d7e2e5 !important;
    }

    .fi-ta-table tbody tr,
    table tbody tr {
        transition: background .18s ease, transform .18s ease !important;
    }

    .fi-ta-table tbody td,
    table tbody td {
        background: #ffffff !important;
        color: #0f172a !important;
        border-color: #eef2f7 !important;
        font-weight: 650 !important;
    }

    .fi-ta-table tbody tr:hover td,
    table tbody tr:hover td {
        background: #f8fcfd !important;
    }

    .fi-badge {
        border-radius: 999px !important;
        font-weight: 900 !important;
        letter-spacing: .02em !important;
        min-height: 24px !important;
        padding-inline: 9px !important;
    }

    .fi-checkbox-input {
        border-radius: 8px !important;
    }

    .fi-pagination {
        border-top: 1px solid #e4ecef !important;
        background: #ffffff !important;
    }

    /*
     * Dropdown clipping safety
     */
    .fi-section,
    .fi-section-content,
    .fi-ta-ctn,
    .fi-ta-outer,
    .fi-ta,
    .fi-ta-content,
    .fi-ta-filters,
    .fi-fo,
    .fi-fo-component-ctn {
        overflow: visible !important;
    }

    .fi-section,
    .fi-ta-ctn {
        position: relative !important;
        z-index: 5 !important;
    }

    .choices__list--dropdown,
    .choices__list[aria-expanded],
    [role="listbox"] {
        z-index: 99999 !important;
        border-radius: 16px !important;
        box-shadow: 0 20px 42px rgba(15, 23, 42, .18) !important;
    }

    /*
     * Dark mode
     */
    .dark .fi-page > header,
    .dark .fi-header,
    .dark header.fi-header {
        background:
            radial-gradient(circle at 90% 18%, rgba(76, 167, 168, .20), transparent 24%),
            radial-gradient(circle at 12% 110%, rgba(179, 139, 47, .12), transparent 28%),
            linear-gradient(135deg, #071427 0%, #0b1a31 58%, #12385d 100%) !important;
        border-color: rgba(76, 167, 168, .18) !important;
    }

    .dark .fi-section,
    .dark .fi-ta-ctn,
    .dark .fi-ta-outer {
        background: linear-gradient(180deg, rgba(12, 23, 38, .98) 0%, rgba(15, 23, 42, .96) 100%) !important;
        border-color: rgba(76, 167, 168, .18) !important;
        box-shadow: 0 14px 30px rgba(0, 0, 0, .28) !important;
    }

    .dark .fi-section-header,
    .dark .fi-ta-header,
    .dark .fi-ta-toolbar,
    .dark .fi-pagination {
        background: rgba(15, 23, 42, .92) !important;
        border-color: rgba(76, 167, 168, .16) !important;
    }

    .dark .fi-section-header-heading,
    .dark .fi-section h2,
    .dark .fi-section h3,
    .dark .fi-fo-field-wrp-label,
    .dark .fi-fo-field-wrp-label span,
    .dark .fi-section label {
        color: #8fd6d7 !important;
    }

    .dark .fi-input-wrp,
    .dark .fi-select,
    .dark .fi-input,
    .dark .fi-select-input,
    .dark input,
    .dark select,
    .dark textarea {
        background: rgba(15, 23, 42, .92) !important;
        color: #f8fafc !important;
        border-color: rgba(76, 167, 168, .18) !important;
    }

    .dark .fi-ta-table thead th,
    .dark table thead th {
        background: rgba(15, 23, 42, .92) !important;
        color: #8fd6d7 !important;
        border-color: rgba(76, 167, 168, .16) !important;
    }

    .dark .fi-ta-table tbody td,
    .dark table tbody td {
        background: rgba(12, 23, 38, .96) !important;
        color: #f8fafc !important;
        border-color: rgba(76, 167, 168, .10) !important;
    }

    .dark .fi-ta-table tbody tr:hover td,
    .dark table tbody tr:hover td {
        background: rgba(20, 35, 56, .96) !important;
    }

    @media (max-width: 1200px) {
        .fi-page > header,
        .fi-header,
        header.fi-header {
            padding: 24px !important;
        }

        .fi-header-heading,
        .fi-header-heading h1,
        .fi-page h1 {
            font-size: 42px !important;
        }
    }
</style>


<style id="sf-finance-expense-action-buttons-employment-style-final">
    /*
     * Finance Expense action buttons — Employment-style final.
     * CSS only. Does not change workflow/actions/HTML.
     */
    .sf-finance-expense-livewire-root .sf-md3-hero-actions {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: flex-end !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        max-width: 560px !important;
        margin-left: auto !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions a.sf-md3-action,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions button.sf-md3-action {
        width: auto !important;
        min-width: 0 !important;
        height: 46px !important;
        min-height: 46px !important;
        max-height: 46px !important;
        padding: 0 18px !important;
        border-radius: 999px !important;
        border: 1px solid rgba(255,255,255,.16) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;
        font-size: 13px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: -.01em !important;
        text-decoration: none !important;
        box-shadow: 0 12px 22px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.20) !important;
        transform: none !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        cursor: pointer !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action strong,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action span:not(.sf-md3-icon) {
        font-size: 13px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        white-space: nowrap !important;
        color: inherit !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-icon {
        width: 17px !important;
        height: 17px !important;
        min-width: 17px !important;
        min-height: 17px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: inherit !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-icon svg,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action svg {
        width: 17px !important;
        height: 17px !important;
        min-width: 17px !important;
        min-height: 17px !important;
        fill: currentColor !important;
        color: inherit !important;
        stroke: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
        color: #07111f !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-warning *,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-warning svg {
        color: #07111f !important;
        fill: #07111f !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-green {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-purple {
        background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-gray {
        background: linear-gradient(135deg, rgba(71,85,105,.94) 0%, rgba(51,65,85,.92) 100%) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-blue *,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-green *,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-purple *,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-red *,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-gray * {
        color: #ffffff !important;
        fill: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 16px 28px rgba(0,0,0,.22), inset 0 1px 0 rgba(255,255,255,.22) !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action:active {
        transform: translateY(0) scale(.99) !important;
    }

    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .sf-md3-hero-actions {
            max-width: 100% !important;
            justify-content: flex-start !important;
            margin-left: 0 !important;
        }

        .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action,
        .sf-finance-expense-livewire-root .sf-md3-hero-actions a.sf-md3-action,
        .sf-finance-expense-livewire-root .sf-md3-hero-actions button.sf-md3-action {
            height: 44px !important;
            min-height: 44px !important;
            max-height: 44px !important;
            padding: 0 15px !important;
            font-size: 12px !important;
        }

        .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action strong {
            font-size: 12px !important;
        }
    }
</style>


<style id="sf-expense-display-corrections">
    .sf-expense-field-hint {
        margin-top: 7px;
        max-width: 760px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.55;
        font-weight: 750;
    }

    .dark .sf-expense-field-hint {
        color: #94a3b8;
    }
</style>

</x-filament-panels::page>


<style id="sf-candidate-request-decision-colors">
    /*
     * Colored decision buttons — visual only.
     */

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]) {
        overflow: hidden !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5) !important;
        border-color: rgba(34,197,94,.42) !important;
        color: #047857 !important;
        box-shadow: 0 12px 28px rgba(34,197,94,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: linear-gradient(135deg, #fef2f2, #fee2e2) !important;
        border-color: rgba(239,68,68,.38) !important;
        color: #b91c1c !important;
        box-shadow: 0 12px 28px rgba(239,68,68,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
        border-color: rgba(249,115,22,.38) !important;
        color: #c2410c !important;
        box-shadow: 0 12px 28px rgba(249,115,22,.10) !important;
    }

    form:has(textarea[name*="negotiation"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="candidate"]) label:has(input[type="radio"]:checked),
    form:has(textarea[name*="response"]) label:has(input[type="radio"]:checked) {
        transform: translateY(-1px) !important;
        filter: saturate(1.12) !important;
        box-shadow: 0 0 0 5px rgba(37,99,235,.10), 0 18px 38px rgba(15,23,42,.12) !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="approve"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="approve"]) {
        background: rgba(6,78,59,.55) !important;
        border-color: rgba(52,211,153,.34) !important;
        color: #a7f3d0 !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="decline"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="decline"]) {
        background: rgba(127,29,29,.48) !important;
        border-color: rgba(248,113,113,.34) !important;
        color: #fecaca !important;
    }

    .dark form:has(textarea[name*="negotiation"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="candidate"]) label:has(input[type="radio"][value*="reconsider"]),
    .dark form:has(textarea[name*="response"]) label:has(input[type="radio"][value*="reconsider"]) {
        background: rgba(124,45,18,.48) !important;
        border-color: rgba(251,146,60,.34) !important;
        color: #fed7aa !important;
    }
</style>


<style id="sf-finance-expense-employment-body-clean">
    .sf-finance-expense-livewire-root {
        width: min(100%, 1280px) !important;
        margin: 0 auto 60px !important;
    }

    .sf-fe-body {
        display: flex;
        flex-direction: column;
        gap: 22px;
        margin-top: 22px;
    }

    .sf-fe-body .sf-finance-grid,
    .sf-fe-body .sf-block-grid {
        display: grid;
        gap: 16px;
    }

    .sf-fe-body .sf-finance-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .sf-fe-body .sf-block-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sf-fe-body .sf-finance-card,
    .sf-fe-body .sf-block {
        border-radius: 30px;
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255,255,255,.94);
        border: 1px solid rgba(15,23,42,.08);
        box-shadow: 0 16px 42px rgba(15,23,42,.07);
    }

    .dark .sf-fe-body .sf-finance-card,
    .dark .sf-fe-body .sf-block {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
            rgba(15,23,42,.72);
        border-color: rgba(148,163,184,.18);
        box-shadow: 0 18px 46px rgba(0,0,0,.18);
    }

    .sf-fe-body .sf-finance-card {
        min-height: 160px;
        position: relative;
        overflow: hidden;
    }

    .sf-fe-body .sf-finance-card::before {
        content: "";
        display: block;
        height: 5px;
        border-radius: 999px;
        margin-bottom: 18px;
        background: linear-gradient(90deg, #22d3ee, #2563eb);
    }

    .sf-fe-body .sf-card-icon {
        position: absolute;
        top: 18px;
        right: 20px;
        font-family: 'Material Symbols Rounded' !important;
        font-size: 24px;
        color: rgba(37,99,235,.30);
        font-variation-settings: 'FILL' 0, 'wght' 600, 'GRAD' 0, 'opsz' 24;
    }

    .sf-fe-body .sf-finance-title,
    .sf-fe-body .sf-block-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 18px;
        font-weight: 950;
        color: #234b74;
        letter-spacing: -.04em;
    }

    .sf-fe-body .sf-block-title .material-symbols-rounded {
        font-family: 'Material Symbols Rounded' !important;
        font-size: 22px;
        font-variation-settings: 'FILL' 0, 'wght' 600, 'GRAD' 0, 'opsz' 24;
    }

    .dark .sf-fe-body .sf-finance-title,
    .dark .sf-fe-body .sf-block-title {
        color: #fff;
    }

    .sf-fe-body .sf-finance-number {
        margin-top: 14px;
        color: #234b74;
        font-size: clamp(28px, 2.5vw, 42px);
        line-height: .96;
        font-weight: 950;
        letter-spacing: -.07em;
        word-break: break-word;
    }

    .dark .sf-fe-body .sf-finance-number {
        color: #fff;
    }

    .sf-fe-body .sf-meta,
    .sf-fe-body .sf-block-subtitle {
        margin-top: 8px;
        font-size: 13px;
        color: #64748b;
        font-weight: 650;
        line-height: 1.45;
    }

    .dark .sf-fe-body .sf-meta,
    .dark .sf-fe-body .sf-block-subtitle {
        color: #94a3b8;
    }

    .sf-fe-body .sf-block-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .sf-fe-body .sf-list {
        display: grid;
        gap: 10px;
    }

    .sf-fe-body .sf-list-compact {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .sf-fe-body .sf-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        border-radius: 18px;
        padding: 13px 14px;
        background: rgba(15,23,42,.035);
        border: 1px solid rgba(15,23,42,.06);
        min-height: 58px;
    }

    .dark .sf-fe-body .sf-row {
        background: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.08);
    }

    .sf-fe-body .sf-row span {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        line-height: 1.35;
    }

    .dark .sf-fe-body .sf-row span {
        color: #94a3b8;
    }

    .sf-fe-body .sf-row strong {
        color: #0f172a;
        font-size: 15px;
        line-height: 1.35;
        font-weight: 950;
        text-align: right;
        letter-spacing: -.025em;
        max-width: 58%;
        overflow-wrap: anywhere;
    }

    .dark .sf-fe-body .sf-row strong {
        color: #fff;
    }

    .sf-fe-body .sf-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0f172a !important;
        font-size: 12px;
        font-weight: 900;
        text-decoration: none !important;
        white-space: nowrap;
    }

    .sf-fe-body .sf-pill:hover {
        background: #bae6fd;
    }

    @media (max-width: 1100px) {
        .sf-fe-body .sf-finance-grid,
        .sf-fe-body .sf-block-grid,
        .sf-fe-body .sf-list-compact {
            grid-template-columns: 1fr;
        }
    }
</style>



<style id="sf-finance-expense-header-restore-final">
    .sf-finance-expense-livewire-root .sf-md3-expense-hero {
        position: relative !important;
        overflow: hidden !important;
        margin: 10px auto 28px !important;
        padding: 30px !important;
        border-radius: 32px !important;
        color: #fff !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .16), transparent 34%),
            linear-gradient(135deg, #111827, #1f2937 62%, #234b74) !important;
        border: 1px solid rgba(148, 163, 184, .22) !important;
        box-shadow: 0 22px 60px rgba(15, 23, 42, .16) !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-main {
        display: grid !important;
        grid-template-columns: minmax(260px, .9fr) minmax(420px, 1.1fr) !important;
        gap: 22px !important;
        align-items: start !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-kicker {
        font-size: 13px !important;
        font-weight: 850 !important;
        color: #94a3b8 !important;
        margin-bottom: 8px !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero h1 {
        margin: 0 !important;
        font-size: clamp(42px, 5vw, 72px) !important;
        line-height: .94 !important;
        letter-spacing: -.065em !important;
        font-weight: 950 !important;
        color: #fff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-subtitle {
        margin: 12px 0 0 !important;
        color: #cbd5e1 !important;
        font-size: 16px !important;
        font-weight: 750 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-chips {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
        margin-top: 18px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-chips span {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        min-height: 34px !important;
        padding: 7px 13px !important;
        border-radius: 999px !important;
        color: #e5e7eb !important;
        background: rgba(255,255,255,.12) !important;
        border: 1px solid rgba(255,255,255,.16) !important;
        font-size: 13px !important;
        font-weight: 800 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-chips strong {
        color: #fff !important;
        font-size: 13px !important;
        font-weight: 950 !important;
        letter-spacing: 0 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
        align-items: start !important;
        justify-content: end !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        min-height: 42px !important;
        padding: 11px 16px !important;
        border-radius: 999px !important;
        border: 1px solid rgba(255,255,255,.14) !important;
        box-shadow: 0 14px 28px rgba(15,23,42,.18) !important;
        text-decoration: none !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        overflow: hidden !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action strong {
        display: inline-flex !important;
        align-items: center !important;
        color: inherit !important;
        font-size: 13px !important;
        line-height: 1 !important;
        font-weight: 900 !important;
        letter-spacing: 0 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 auto !important;
        width: 18px !important;
        height: 18px !important;
        color: inherit !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon svg {
        display: block !important;
        width: 18px !important;
        height: 18px !important;
        max-width: 18px !important;
        max-height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
        fill: currentColor !important;
        color: inherit !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-warning {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #111827 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon,
    .sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon svg {
        color: #fff !important;
        fill: #fff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #fff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-green {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #fff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-purple {
        background: linear-gradient(135deg, #a855f7, #7c3aed) !important;
        color: #fff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-red {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #fff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 16px !important;
        margin-top: 24px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats > div {
        border-radius: 22px !important;
        padding: 18px !important;
        border: 1px solid rgba(255,255,255,.12) !important;
        background: rgba(255,255,255,.08) !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats small {
        display: block !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #94a3b8 !important;
        margin-bottom: 9px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats strong {
        display: block !important;
        font-size: 24px !important;
        line-height: 1.1 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        color: #fff !important;
    }

    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .sf-md3-hero-main,
        .sf-finance-expense-livewire-root .sf-md3-hero-stats {
            grid-template-columns: 1fr !important;
        }

        .sf-finance-expense-livewire-root .sf-md3-hero-actions {
            grid-template-columns: 1fr !important;
        }
    }
</style>



<style id="sf-finance-view-employment-alignment-final">
    /*
    |--------------------------------------------------------------------------
    | Finance Expense View - Employment-style final alignment
    | Keeps the current good body style, restores header/body same width,
    | hides Cancel, fixes KPI icon overlap, and gives body icons circular badges.
    |--------------------------------------------------------------------------
    */

    .sf-finance-expense-livewire-root {
        width: min(100%, 1280px) !important;
        max-width: 1280px !important;
        margin: 0 auto 60px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 22px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero,
    .sf-finance-expense-livewire-root .fe-view-shell,
    .sf-finance-expense-livewire-root .sf-finance-expense-page,
    .sf-finance-expense-livewire-root .sf-premium-expense-page,
    .sf-finance-expense-livewire-root .sf-expense-page {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: auto !important;
        margin-right: auto !important;
        box-sizing: border-box !important;
    }

    /*
    | Keep header compact like Employment. Do not enlarge it.
    */
    .sf-finance-expense-livewire-root .sf-md3-expense-hero {
        margin-top: 0 !important;
        margin-bottom: 22px !important;
        padding: 30px !important;
        border-radius: 32px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-main {
        grid-template-columns: minmax(260px, .9fr) minmax(420px, 1.1fr) !important;
        gap: 22px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero h1 {
        font-size: clamp(42px, 5vw, 72px) !important;
        line-height: .94 !important;
        letter-spacing: -.065em !important;
    }

    /*
    | Finance action buttons: same compact style, remove Cancel.
    */
    .sf-finance-expense-livewire-root .sf-md3-hero-actions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
        align-items: start !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions button[wire\:click="mountAction('cancel')"],
    .sf-finance-expense-livewire-root .sf-md3-hero-actions button[wire\:click*="cancel"] {
        display: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action {
        min-height: 42px !important;
        padding: 11px 16px !important;
        border-radius: 999px !important;
        gap: 8px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action strong {
        font-size: 13px !important;
        line-height: 1 !important;
        font-weight: 900 !important;
        letter-spacing: 0 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon,
    .sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
        max-width: 18px !important;
        max-height: 18px !important;
        display: inline-flex !important;
        color: inherit !important;
        fill: currentColor !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon,
    .sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon svg {
        color: #fff !important;
        fill: #fff !important;
    }

    /*
    | Header stat cards should match Employment summary blocks.
    */
    .sf-finance-expense-livewire-root .sf-md3-hero-stats {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 16px !important;
        margin-top: 24px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats > div {
        min-height: 118px !important;
        border-radius: 22px !important;
        padding: 18px !important;
        border: 1px solid rgba(255,255,255,.12) !important;
        background: rgba(255,255,255,.08) !important;
        box-shadow: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats small {
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #94a3b8 !important;
        margin-bottom: 9px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats strong {
        font-size: 24px !important;
        line-height: 1.1 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        color: #fff !important;
    }

    /*
    | Body shell same width as header, no extra outside box feeling.
    */
    .sf-finance-expense-livewire-root .fe-view-shell {
        padding-left: 0 !important;
        padding-right: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    /*
    | First KPI row: fix icon overlap with top gradient line.
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid {
        width: 100% !important;
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 30px !important;
        padding: 46px 24px 24px !important;
        min-height: 230px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255,255,255,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 16px 42px rgba(15,23,42,.07) !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::before,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::before {
        content: "" !important;
        position: absolute !important;
        top: 24px !important;
        left: 24px !important;
        right: 64px !important;
        height: 5px !important;
        border-radius: 999px !important;
        background: linear-gradient(90deg, #22d3ee, #2563eb) !important;
        z-index: 1 !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::after,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::after {
        top: 16px !important;
        right: 20px !important;
        width: 44px !important;
        height: 44px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(224, 242, 254, .90) !important;
        color: rgba(37, 99, 235, .34) !important;
        font-size: 25px !important;
        line-height: 1 !important;
        z-index: 2 !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-label,
    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-field-label,
    .sf-finance-expense-livewire-root .fe-kpi-grid [class*="label"] {
        font-size: 18px !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        text-transform: none !important;
        color: #234b74 !important;
        margin-top: 0 !important;
        margin-bottom: 22px !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-value,
    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-field-value,
    .sf-finance-expense-livewire-root .fe-kpi-grid strong,
    .sf-finance-expense-livewire-root .fe-kpi-grid [class*="value"] {
        font-size: clamp(30px, 3.4vw, 52px) !important;
        line-height: .96 !important;
        font-weight: 950 !important;
        letter-spacing: -.07em !important;
        color: #234b74 !important;
        margin-top: 0 !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-sub,
    .sf-finance-expense-livewire-root .fe-kpi-grid small,
    .sf-finance-expense-livewire-root .fe-kpi-grid p {
        margin-top: 14px !important;
        font-size: 13px !important;
        line-height: 1.45 !important;
        color: #64748b !important;
        font-weight: 650 !important;
    }

    /*
    | Body large panels: Employment style with right circular icon badge.
    */
    .sf-finance-expense-livewire-root .fe-panel,
    .sf-finance-expense-livewire-root .fe-two-grid > .fe-card,
    .sf-finance-expense-livewire-root .fe-two-grid > div,
    .sf-finance-expense-livewire-root .fe-meta-grid > div,
    .sf-finance-expense-livewire-root .fe-box {
        border-radius: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255,255,255,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 16px 42px rgba(15,23,42,.07) !important;
    }

    .sf-finance-expense-livewire-root .fe-panel,
    .sf-finance-expense-livewire-root .fe-two-grid > .fe-card,
    .sf-finance-expense-livewire-root .fe-two-grid > div {
        padding: 24px !important;
    }

    .sf-finance-expense-livewire-root .fe-pill {
        border-radius: 999px !important;
        padding: 9px 16px !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #234b74 !important;
        background: #e0f2fe !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--amber {
        background: #f1dfd6 !important;
        color: #234b74 !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--purple {
        background: #ead7ff !important;
        color: #234b74 !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--green {
        background: #dff3e7 !important;
        color: #234b74 !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--slate {
        background: #e2e8f0 !important;
        color: #234b74 !important;
    }

    /*
    | Inner small fields remain readable and not oversized.
    */
    .sf-finance-expense-livewire-root .fe-three-grid .fe-box,
    .sf-finance-expense-livewire-root .fe-meta-grid .fe-box {
        padding: 18px !important;
        border-radius: 22px !important;
        background: rgba(248,250,252,.72) !important;
        box-shadow: none !important;
    }

    .sf-finance-expense-livewire-root .fe-three-grid .fe-field-label,
    .sf-finance-expense-livewire-root .fe-meta-grid .fe-field-label {
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #8090a6 !important;
    }

    .sf-finance-expense-livewire-root .fe-three-grid .fe-field-value,
    .sf-finance-expense-livewire-root .fe-meta-grid .fe-field-value {
        margin-top: 12px !important;
        color: #0f172a !important;
        font-size: 18px !important;
        line-height: 1.35 !important;
        font-weight: 950 !important;
        letter-spacing: -.035em !important;
    }

    /*
    | Responsive
    */
    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .sf-md3-hero-main,
        .sf-finance-expense-livewire-root .sf-md3-hero-stats,
        .sf-finance-expense-livewire-root .fe-kpi-grid,
        .sf-finance-expense-livewire-root .fe-two-grid {
            grid-template-columns: 1fr !important;
        }

        .sf-finance-expense-livewire-root .sf-md3-hero-actions {
            grid-template-columns: 1fr !important;
        }
    }

    /*
    | Dark mode
    */
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid > div,
    .dark .sf-finance-expense-livewire-root .fe-panel,
    .dark .sf-finance-expense-livewire-root .fe-two-grid > .fe-card,
    .dark .sf-finance-expense-livewire-root .fe-two-grid > div,
    .dark .sf-finance-expense-livewire-root .fe-meta-grid > div,
    .dark .sf-finance-expense-livewire-root .fe-box {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
            rgba(15,23,42,.72) !important;
        border-color: rgba(148,163,184,.18) !important;
        box-shadow: 0 18px 46px rgba(0,0,0,.18) !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-label,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-field-label,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid [class*="label"],
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-value,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-field-value,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid strong,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid [class*="value"] {
        color: #fff !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-three-grid .fe-box,
    .dark .sf-finance-expense-livewire-root .fe-meta-grid .fe-box {
        background: rgba(15,23,42,.52) !important;
        border-color: rgba(148,163,184,.14) !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-three-grid .fe-field-value,
    .dark .sf-finance-expense-livewire-root .fe-meta-grid .fe-field-value {
        color: #fff !important;
    }
</style>



<style id="sf-finance-view-final-compact-header-body-fix">
    /*
    |--------------------------------------------------------------------------
    | Finance Expense View - final compact header + aligned Employment-style body
    |--------------------------------------------------------------------------
    */

    .sf-finance-expense-livewire-root {
        width: min(100%, 1280px) !important;
        max-width: 1280px !important;
        margin: 0 auto 60px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 22px !important;
    }

    /*
    | Same outer width for header and body
    */
    .sf-finance-expense-livewire-root .sf-md3-expense-hero,
    .sf-finance-expense-livewire-root .fe-view-shell,
    .sf-finance-expense-livewire-root .fe-kpi-grid,
    .sf-finance-expense-livewire-root .fe-two-grid,
    .sf-finance-expense-livewire-root .fe-panel,
    .sf-finance-expense-livewire-root .sf-finance-body-status-strip {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    /*
    | Compact header: no status chips inside header
    */
    .sf-finance-expense-livewire-root .sf-md3-chips {
        display: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero {
        padding: 26px 30px !important;
        margin: 0 0 22px !important;
        border-radius: 32px !important;
        min-height: auto !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-main {
        grid-template-columns: minmax(260px, .95fr) minmax(420px, 1.05fr) !important;
        gap: 22px !important;
        align-items: start !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-kicker {
        font-size: 12px !important;
        margin-bottom: 8px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero h1 {
        font-size: clamp(40px, 4.6vw, 66px) !important;
        line-height: .94 !important;
        letter-spacing: -.065em !important;
        margin: 0 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-subtitle {
        margin-top: 12px !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        color: rgba(255,255,255,.78) !important;
    }

    /*
    | Smaller action buttons, same style, better fit
    */
    .sf-finance-expense-livewire-root .sf-md3-hero-actions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
        align-items: start !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions button[wire\:click="mountAction('cancel')"],
    .sf-finance-expense-livewire-root .sf-md3-hero-actions button[wire\:click*="cancel"] {
        display: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action {
        min-height: 38px !important;
        padding: 9px 14px !important;
        border-radius: 999px !important;
        gap: 7px !important;
        box-shadow: 0 12px 24px rgba(0,0,0,.12) !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action strong {
        font-size: 12px !important;
        line-height: 1.05 !important;
        font-weight: 950 !important;
        letter-spacing: 0 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon,
    .sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon svg {
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
        display: inline-flex !important;
        color: inherit !important;
        fill: currentColor !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon,
    .sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon svg {
        color: #fff !important;
        fill: #fff !important;
    }

    /*
    | Header stats smaller
    */
    .sf-finance-expense-livewire-root .sf-md3-hero-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 16px !important;
        margin-top: 22px !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats > div {
        min-height: 92px !important;
        border-radius: 22px !important;
        padding: 16px !important;
        background: rgba(255,255,255,.08) !important;
        border: 1px solid rgba(255,255,255,.12) !important;
        box-shadow: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats small {
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        color: #94a3b8 !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-stats strong {
        font-size: 20px !important;
        line-height: 1.05 !important;
        color: #fff !important;
        font-weight: 950 !important;
    }

    /*
    | Remove outer body box feeling
    */
    .sf-finance-expense-livewire-root .fe-view-shell {
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    /*
    | First 4 KPI cards - Employment style, aligned with header
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div {
        position: relative !important;
        overflow: hidden !important;
        min-height: 210px !important;
        padding: 44px 22px 22px !important;
        border-radius: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255,255,255,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 16px 42px rgba(15,23,42,.07) !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::before,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::before {
        content: "" !important;
        position: absolute !important;
        top: 24px !important;
        left: 22px !important;
        right: 76px !important;
        height: 5px !important;
        border-radius: 999px !important;
        background: linear-gradient(90deg, #22d3ee, #2563eb) !important;
        z-index: 1 !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::after,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::after {
        position: absolute !important;
        top: 13px !important;
        right: 18px !important;
        width: 46px !important;
        height: 46px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: rgba(224, 242, 254, .95) !important;
        color: rgba(37, 99, 235, .38) !important;
        font-family: 'Material Symbols Rounded' !important;
        font-size: 23px !important;
        line-height: 1 !important;
        z-index: 2 !important;
        -webkit-font-feature-settings: 'liga' !important;
        font-feature-settings: 'liga' !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid > div:nth-child(1)::after { content: "payments" !important; }
    .sf-finance-expense-livewire-root .fe-kpi-grid > div:nth-child(2)::after { content: "calendar_month" !important; }
    .sf-finance-expense-livewire-root .fe-kpi-grid > div:nth-child(3)::after { content: "person" !important; }
    .sf-finance-expense-livewire-root .fe-kpi-grid > div:nth-child(4)::after { content: "category" !important; }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-label,
    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-field-label,
    .sf-finance-expense-livewire-root .fe-kpi-grid [class*="label"] {
        font-size: 18px !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        text-transform: none !important;
        color: #234b74 !important;
        margin-bottom: 20px !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-value,
    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-field-value,
    .sf-finance-expense-livewire-root .fe-kpi-grid strong,
    .sf-finance-expense-livewire-root .fe-kpi-grid [class*="value"] {
        font-size: clamp(28px, 3.1vw, 46px) !important;
        line-height: .98 !important;
        font-weight: 950 !important;
        letter-spacing: -.065em !important;
        color: #234b74 !important;
        margin-top: 0 !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-sub,
    .sf-finance-expense-livewire-root .fe-kpi-grid small,
    .sf-finance-expense-livewire-root .fe-kpi-grid p {
        margin-top: 14px !important;
        font-size: 13px !important;
        line-height: 1.45 !important;
        color: #64748b !important;
        font-weight: 650 !important;
    }

    /*
    | New status strip under KPI cards
    */
    .sf-finance-body-status-strip {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 16px !important;
        margin-top: 16px !important;
    }

    .sf-finance-status-card {
        display: grid !important;
        grid-template-columns: auto 1fr !important;
        align-items: center !important;
        column-gap: 12px !important;
        row-gap: 2px !important;
        min-height: 86px !important;
        padding: 18px 20px !important;
        border-radius: 24px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255,255,255,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 16px 42px rgba(15,23,42,.07) !important;
    }

    .sf-finance-status-card .material-symbols-rounded {
        grid-row: 1 / span 2 !important;
        width: 42px !important;
        height: 42px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #e0f2fe !important;
        color: #234b74 !important;
        font-size: 22px !important;
        font-family: 'Material Symbols Rounded' !important;
        -webkit-font-feature-settings: 'liga' !important;
        font-feature-settings: 'liga' !important;
    }

    .sf-finance-status-card small {
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #8090a6 !important;
        line-height: 1 !important;
    }

    .sf-finance-status-card strong {
        font-size: 20px !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        color: #0f172a !important;
        line-height: 1.05 !important;
    }

    /*
    | Main body panels/cards with top-right circular icons
    */
    .sf-finance-expense-livewire-root .fe-two-grid > div,
    .sf-finance-expense-livewire-root .fe-panel {
        position: relative !important;
        overflow: hidden !important;
        padding: 28px !important;
        border-radius: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255,255,255,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 16px 42px rgba(15,23,42,.07) !important;
    }

    .sf-finance-expense-livewire-root .fe-two-grid > div::after,
    .sf-finance-expense-livewire-root .fe-panel::after {
        position: absolute !important;
        top: 28px !important;
        right: 28px !important;
        width: 48px !important;
        height: 48px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #e0f2fe !important;
        color: #234b74 !important;
        font-family: 'Material Symbols Rounded' !important;
        font-size: 24px !important;
        line-height: 1 !important;
        -webkit-font-feature-settings: 'liga' !important;
        font-feature-settings: 'liga' !important;
        opacity: .95 !important;
    }

    .sf-finance-expense-livewire-root .fe-two-grid > div:nth-child(1)::after { content: "dashboard" !important; }
    .sf-finance-expense-livewire-root .fe-two-grid > div:nth-child(2)::after { content: "verified" !important; }
    .sf-finance-expense-livewire-root .fe-panel:nth-of-type(3)::after { content: "link" !important; }
    .sf-finance-expense-livewire-root .fe-panel::after { content: "receipt_long" !important; }

    .sf-finance-expense-livewire-root .fe-pill {
        display: inline-flex !important;
        align-items: center !important;
        min-height: 34px !important;
        padding: 8px 15px !important;
        border-radius: 999px !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #234b74 !important;
        background: #e0f2fe !important;
        margin-bottom: 16px !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--amber {
        background: #f1dfd6 !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--purple {
        background: #ead7ff !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--green {
        background: #dff3e7 !important;
    }

    .sf-finance-expense-livewire-root .fe-pill--slate {
        background: #e2e8f0 !important;
    }

    .sf-finance-expense-livewire-root .fe-box {
        border-radius: 22px !important;
        padding: 18px !important;
        background: rgba(248,250,252,.74) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: none !important;
    }

    .sf-finance-expense-livewire-root .fe-field-label {
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
        color: #8090a6 !important;
    }

    .sf-finance-expense-livewire-root .fe-field-value {
        margin-top: 10px !important;
        color: #0f172a !important;
        font-size: 17px !important;
        line-height: 1.35 !important;
        font-weight: 950 !important;
        letter-spacing: -.035em !important;
    }

    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .sf-md3-hero-main,
        .sf-finance-expense-livewire-root .sf-md3-hero-stats,
        .sf-finance-expense-livewire-root .fe-kpi-grid,
        .sf-finance-expense-livewire-root .fe-two-grid,
        .sf-finance-body-status-strip {
            grid-template-columns: 1fr !important;
        }

        .sf-finance-expense-livewire-root .sf-md3-hero-actions {
            grid-template-columns: 1fr !important;
        }
    }

    /*
    | Dark mode
    */
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid > div,
    .dark .sf-finance-status-card,
    .dark .sf-finance-expense-livewire-root .fe-two-grid > div,
    .dark .sf-finance-expense-livewire-root .fe-panel,
    .dark .sf-finance-expense-livewire-root .fe-box {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
            rgba(15,23,42,.72) !important;
        border-color: rgba(148,163,184,.18) !important;
        box-shadow: 0 18px 46px rgba(0,0,0,.18) !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-label,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-value,
    .dark .sf-finance-expense-livewire-root .fe-field-value,
    .dark .sf-finance-status-card strong {
        color: #fff !important;
    }
</style>



<style id="sf-finance-body-align-with-header-only">
    /*
    |--------------------------------------------------------------------------
    | Finance Expense - align body/cards with header width
    |--------------------------------------------------------------------------
    */

    .sf-finance-expense-livewire-root {
        width: min(100%, 1280px) !important;
        max-width: 1280px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        box-sizing: border-box !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    /*
    | Remove the inner body indentation / outer box offset
    */
    .sf-finance-expense-livewire-root .fe-view-shell {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: 0 !important;
        box-sizing: border-box !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .sf-finance-expense-livewire-root .fe-view-shell > section,
    .sf-finance-expense-livewire-root .fe-kpi-grid,
    .sf-finance-expense-livewire-root .sf-finance-body-status-strip,
    .sf-finance-expense-livewire-root .fe-two-grid,
    .sf-finance-expense-livewire-root .fe-panel {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    /*
    | Keep cards white and clean
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid > div,
    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .sf-finance-expense-livewire-root .sf-finance-status-card,
    .sf-finance-expense-livewire-root .fe-two-grid > div,
    .sf-finance-expense-livewire-root .fe-panel,
    .sf-finance-expense-livewire-root .fe-box {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 34%),
            #ffffff !important;
    }

    /*
    | Make the first row fit full width exactly like header
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }

    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .fe-kpi-grid,
        .sf-finance-expense-livewire-root .sf-finance-body-status-strip,
        .sf-finance-expense-livewire-root .fe-two-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>



<style id="sf-finance-body-wide-right-icons-final">
    /*
    |--------------------------------------------------------------------------
    | Finance Expense Body - match Employment width + right circular icons
    |--------------------------------------------------------------------------
    | Do not touch the dark header design. This only fixes body alignment/cards.
    */

    .sf-finance-expense-livewire-root {
        width: min(100%, 1280px) !important;
        max-width: 1280px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        box-sizing: border-box !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    /*
    | Body must start and end at the same x-axis as the header.
    */
    .sf-finance-expense-livewire-root .fe-view-shell {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        box-sizing: border-box !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .sf-finance-expense-livewire-root .fe-view-shell > section,
    .sf-finance-expense-livewire-root .fe-kpi-grid,
    .sf-finance-expense-livewire-root .fe-two-grid,
    .sf-finance-expense-livewire-root .fe-panel,
    .sf-finance-expense-livewire-root .sf-finance-body-status-strip {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    /*
    | First row KPI cards: wide, clean, and aligned.
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div {
        position: relative !important;
        overflow: hidden !important;
        min-height: 185px !important;
        padding: 26px 24px 22px !important;
        border-radius: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 34%),
            #ffffff !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 16px 42px rgba(15, 23, 42, .07) !important;
    }

    /*
    | Fix KPI top-right icons: they should not collide with the gradient line.
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::after,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::after {
        top: 22px !important;
        right: 24px !important;
        transform: none !important;
        opacity: .20 !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::before,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::before {
        margin-bottom: 24px !important;
    }

    /*
    | Main body blocks like Employment:
    | title/text left, icon circle on top-right.
    */
    .sf-finance-expense-livewire-root .fe-two-grid > .fe-panel,
    .sf-finance-expense-livewire-root .fe-panel {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 30px !important;
        padding: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255, 255, 255, .94) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: 0 16px 42px rgba(15, 23, 42, .07) !important;
    }

    /*
    | Convert old pill title into normal block heading.
    */
    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        width: calc(100% - 86px) !important;
        max-width: calc(100% - 86px) !important;
        padding: 0 !important;
        margin: 0 0 10px 0 !important;
        border: 0 !important;
        background: transparent !important;
        color: #234b74 !important;
        font-size: 23px !important;
        line-height: 1.12 !important;
        font-weight: 950 !important;
        letter-spacing: -.045em !important;
        text-transform: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    /*
    | Hide/neutralize the inline icon beside the heading if it exists.
    | The visual icon will be created as a right circular icon below.
    */
    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child .material-symbols-rounded,
    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child svg,
    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child i {
        display: none !important;
    }

    /*
    | Right circular icon for every finance section.
    */
    .sf-finance-expense-livewire-root .fe-panel::after {
        font-family: 'Material Symbols Rounded' !important;
        position: absolute !important;
        top: 28px !important;
        right: 28px !important;
        width: 56px !important;
        height: 56px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #e0f2fe !important;
        color: #0f172a !important;
        font-size: 28px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        font-style: normal !important;
        letter-spacing: normal !important;
        text-transform: none !important;
        -webkit-font-feature-settings: 'liga' !important;
        font-feature-settings: 'liga' !important;
        -webkit-font-smoothing: antialiased !important;
        opacity: 1 !important;
        z-index: 2 !important;
        pointer-events: none !important;
        box-shadow: 0 14px 34px rgba(14, 165, 233, .12) !important;
    }

    /*
    | Icon mapping by title/section class keywords.
    */
    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--blue)::after {
        content: "dashboard" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--amber)::after {
        content: "verified" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--purple)::after {
        content: "link" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--green)::after {
        content: "receipt_long" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--slate)::after {
        content: "notes" !important;
    }

    /*
    | Subtitle / explanatory text directly under section title.
    */
    .sf-finance-expense-livewire-root .fe-panel > .fe-sub,
    .sf-finance-expense-livewire-root .fe-panel > p,
    .sf-finance-expense-livewire-root .fe-panel > small {
        max-width: calc(100% - 90px) !important;
        color: #64748b !important;
        font-size: 15px !important;
        line-height: 1.45 !important;
        font-weight: 750 !important;
    }

    /*
    | Inner rows should look like Employment row cards, not tiny pill boxes.
    */
    .sf-finance-expense-livewire-root .fe-box {
        border-radius: 22px !important;
        padding: 18px 20px !important;
        background: rgba(248, 250, 252, .90) !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.65) !important;
    }

    .sf-finance-expense-livewire-root .fe-field-label {
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .10em !important;
        text-transform: uppercase !important;
        margin-bottom: 8px !important;
    }

    .sf-finance-expense-livewire-root .fe-field-value {
        color: #0f172a !important;
        font-size: 17px !important;
        line-height: 1.35 !important;
        font-weight: 900 !important;
        letter-spacing: -.025em !important;
        margin-top: 0 !important;
    }

    /*
    | Keep body typography closer to Employment page.
    */
    .sf-finance-expense-livewire-root .fe-label {
        color: #234b74 !important;
        font-size: 18px !important;
        letter-spacing: -.04em !important;
        text-transform: none !important;
        font-weight: 950 !important;
    }

    .sf-finance-expense-livewire-root .fe-value {
        color: #234b74 !important;
        font-size: clamp(34px, 3.25vw, 50px) !important;
        line-height: .98 !important;
        font-weight: 950 !important;
        letter-spacing: -.065em !important;
    }

    .sf-finance-expense-livewire-root .fe-sub {
        color: #64748b !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1.45 !important;
    }

    /*
    | Dark mode compatibility.
    */
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .dark .sf-finance-expense-livewire-root .fe-kpi-grid > div,
    .dark .sf-finance-expense-livewire-root .fe-two-grid > .fe-panel,
    .dark .sf-finance-expense-livewire-root .fe-panel {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
            rgba(15, 23, 42, .72) !important;
        border-color: rgba(148, 163, 184, .18) !important;
        box-shadow: 0 18px 46px rgba(0,0,0,.18) !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child,
    .dark .sf-finance-expense-livewire-root .fe-label,
    .dark .sf-finance-expense-livewire-root .fe-value {
        color: #ffffff !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-box {
        background: rgba(15, 23, 42, .58) !important;
        border-color: rgba(148, 163, 184, .14) !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-field-label,
    .dark .sf-finance-expense-livewire-root .fe-sub {
        color: #94a3b8 !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-field-value {
        color: #f8fafc !important;
    }

    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .fe-kpi-grid,
        .sf-finance-expense-livewire-root .fe-two-grid,
        .sf-finance-expense-livewire-root .fe-meta-grid,
        .sf-finance-expense-livewire-root .fe-three-grid {
            grid-template-columns: 1fr !important;
        }

        .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
            width: calc(100% - 76px) !important;
            max-width: calc(100% - 76px) !important;
        }
    }
</style>



<style id="sf-finance-expense-final-alignment-icons-fix">
    @import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,600,0,0');

    /*
    |--------------------------------------------------------------------------
    | FINAL: Finance Expense must align exactly like Employment page
    |--------------------------------------------------------------------------
    */

    .sf-finance-expense-livewire-root {
        width: min(100%, 1280px) !important;
        max-width: 1280px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        box-sizing: border-box !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-expense-hero {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    /*
    | Body wrapper must not be narrower than header.
    */
    .sf-finance-expense-livewire-root .fe-view-shell {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        box-sizing: border-box !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    .sf-finance-expense-livewire-root .fe-view-shell > section,
    .sf-finance-expense-livewire-root .fe-kpi-grid,
    .sf-finance-expense-livewire-root .fe-two-grid,
    .sf-finance-expense-livewire-root .fe-meta-grid,
    .sf-finance-expense-livewire-root .fe-panel {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    /*
    | The first cards row must fill the same width as header.
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 16px !important;
    }

    /*
    | Main body panels same Employment style.
    */
    .sf-finance-expense-livewire-root .fe-panel,
    .sf-finance-expense-livewire-root .fe-two-grid > .fe-panel {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 30px !important;
        padding: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
            rgba(255,255,255,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 16px 42px rgba(15,23,42,.07) !important;
    }

    /*
    | Hide broken inline Material icon text beside titles:
    | dashboard / verified / payments / etc.
    */
    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child .material-symbols-rounded,
    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child svg,
    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child i {
        display: none !important;
        font-size: 0 !important;
        width: 0 !important;
        height: 0 !important;
        overflow: hidden !important;
    }

    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
        display: block !important;
        width: calc(100% - 90px) !important;
        max-width: calc(100% - 90px) !important;
        margin: 0 0 8px 0 !important;
        padding: 0 !important;
        border: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        color: #234b74 !important;
        font-size: 24px !important;
        line-height: 1.12 !important;
        font-weight: 950 !important;
        letter-spacing: -.045em !important;
        text-transform: none !important;
    }

    /*
    | Right icon circle exactly like Employment body blocks.
    */
    .sf-finance-expense-livewire-root .fe-panel::after {
        font-family: 'Material Symbols Rounded' !important;
        position: absolute !important;
        top: 28px !important;
        right: 28px !important;
        width: 56px !important;
        height: 56px !important;
        border-radius: 999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #e0f2fe !important;
        color: #0f172a !important;
        font-size: 28px !important;
        line-height: 1 !important;
        font-weight: 600 !important;
        letter-spacing: normal !important;
        text-transform: none !important;
        -webkit-font-feature-settings: 'liga' !important;
        font-feature-settings: 'liga' !important;
        -webkit-font-smoothing: antialiased !important;
        z-index: 5 !important;
        pointer-events: none !important;
        box-shadow: 0 14px 34px rgba(14,165,233,.12) !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--blue)::after {
        content: "dashboard" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--amber)::after {
        content: "verified" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--purple)::after {
        content: "link" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--green)::after {
        content: "receipt_long" !important;
    }

    .sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--slate)::after {
        content: "notes" !important;
    }

    /*
    | Make subtitle under section title leave space for right icon.
    */
    .sf-finance-expense-livewire-root .fe-panel > p,
    .sf-finance-expense-livewire-root .fe-panel > .fe-sub,
    .sf-finance-expense-livewire-root .fe-panel > small {
        max-width: calc(100% - 90px) !important;
        color: #64748b !important;
        font-size: 15px !important;
        line-height: 1.45 !important;
        font-weight: 750 !important;
    }

    /*
    | KPI cards top icons should not touch the blue line.
    */
    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div {
        position: relative !important;
        overflow: hidden !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::after,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::after {
        top: 24px !important;
        right: 24px !important;
        opacity: .18 !important;
        transform: none !important;
        font-family: 'Material Symbols Rounded' !important;
        -webkit-font-feature-settings: 'liga' !important;
        font-feature-settings: 'liga' !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid .fe-card::before,
    .sf-finance-expense-livewire-root .fe-kpi-grid > div::before {
        margin-bottom: 24px !important;
    }

    /*
    | Inner field rows cleaner.
    */
    .sf-finance-expense-livewire-root .fe-box {
        border-radius: 22px !important;
        padding: 18px 20px !important;
        background: rgba(248,250,252,.90) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.70) !important;
    }

    .sf-finance-expense-livewire-root .fe-field-label {
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .10em !important;
        text-transform: uppercase !important;
        margin-bottom: 8px !important;
    }

    .sf-finance-expense-livewire-root .fe-field-value {
        color: #0f172a !important;
        font-size: 17px !important;
        line-height: 1.35 !important;
        font-weight: 900 !important;
        letter-spacing: -.025em !important;
        margin-top: 0 !important;
    }

    /*
    | Dark mode.
    */
    .dark .sf-finance-expense-livewire-root .fe-panel,
    .dark .sf-finance-expense-livewire-root .fe-two-grid > .fe-panel {
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.12), transparent 35%),
            rgba(15,23,42,.72) !important;
        border-color: rgba(148,163,184,.18) !important;
        box-shadow: 0 18px 46px rgba(0,0,0,.18) !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
        color: #ffffff !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-box {
        background: rgba(15,23,42,.58) !important;
        border-color: rgba(148,163,184,.14) !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-field-label,
    .dark .sf-finance-expense-livewire-root .fe-panel > p,
    .dark .sf-finance-expense-livewire-root .fe-panel > .fe-sub,
    .dark .sf-finance-expense-livewire-root .fe-panel > small {
        color: #94a3b8 !important;
    }

    .dark .sf-finance-expense-livewire-root .fe-field-value {
        color: #f8fafc !important;
    }

    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .fe-kpi-grid,
        .sf-finance-expense-livewire-root .fe-two-grid,
        .sf-finance-expense-livewire-root .fe-meta-grid,
        .sf-finance-expense-livewire-root .fe-three-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>


</div>




<style id="sf-finance-expense-employment-body-final-v2">
/* =========================================================
   FINANCE EXPENSE VIEW — BODY MATCH EMPLOYMENT STYLE
   Final visual-only patch: alignment, cards, right icons
   ========================================================= */

.sf-finance-expense-livewire-root {
    width: min(100%, 1280px) !important;
    max-width: 1280px !important;
    margin-inline: auto !important;
}

/* Header and body must share same visual width */
.sf-finance-expense-livewire-root .sf-md3-expense-hero,
.sf-finance-expense-livewire-root .fe-view-shell {
    width: 100% !important;
    max-width: 1280px !important;
    margin-left: auto !important;
    margin-right: auto !important;
    box-sizing: border-box !important;
}

/* Keep header compact and do not enlarge it again */
.sf-finance-expense-livewire-root .sf-md3-expense-hero {
    margin-bottom: 36px !important;
}

/* Remove the extra large boxed container feeling under the hero */
.sf-finance-expense-livewire-root .fe-view-shell {
    padding: 0 !important;
    border: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    backdrop-filter: none !important;
}

/* Body spacing same direction as Employment page */
.sf-finance-expense-livewire-root .fe-view-shell > section {
    width: 100% !important;
    max-width: 1280px !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}

/* KPI cards row */
.sf-finance-expense-livewire-root .fe-kpi-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 18px !important;
    width: 100% !important;
    margin: 0 0 22px 0 !important;
}

/* Main two-column sections */
.sf-finance-expense-livewire-root .fe-two-grid {
    display: grid !important;
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 22px !important;
    width: 100% !important;
    margin: 0 0 22px 0 !important;
}

/* Ownership / details grids */
.sf-finance-expense-livewire-root .fe-meta-grid,
.sf-finance-expense-livewire-root .fe-three-grid {
    gap: 14px !important;
}

/* Employment-like cards */
.sf-finance-expense-livewire-root .fe-card,
.sf-finance-expense-livewire-root .fe-panel {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 30px !important;
    background:
        radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
        rgba(255,255,255,.94) !important;
    border: 1px solid rgba(15,23,42,.08) !important;
    box-shadow: 0 16px 42px rgba(15,23,42,.07) !important;
    color: #0f172a !important;
}

/* KPI cards like employment finance cards */
.sf-finance-expense-livewire-root .fe-card {
    min-height: 205px !important;
    padding: 26px 24px 22px !important;
}

/* Top gradient accent line */
.sf-finance-expense-livewire-root .fe-card::before,
.sf-finance-expense-livewire-root .fe-panel::before {
    content: "" !important;
    display: block !important;
    height: 5px !important;
    border-radius: 999px !important;
    margin-bottom: 22px !important;
    background: linear-gradient(90deg, #22d3ee, #2563eb) !important;
}

/* KPI right icon circle */
.sf-finance-expense-livewire-root .fe-card::after {
    position: absolute !important;
    top: 22px !important;
    right: 24px !important;
    width: 50px !important;
    height: 50px !important;
    border-radius: 999px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(224, 242, 254, .92) !important;
    color: #0f172a !important;
    font-family: "Material Symbols Rounded" !important;
    font-size: 26px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    -webkit-font-feature-settings: "liga" !important;
    font-feature-settings: "liga" !important;
    opacity: .55 !important;
}

/* KPI icon mapping */
.sf-finance-expense-livewire-root .fe-kpi-grid .fe-card:nth-child(1)::after { content: "payments" !important; }
.sf-finance-expense-livewire-root .fe-kpi-grid .fe-card:nth-child(2)::after { content: "event" !important; }
.sf-finance-expense-livewire-root .fe-kpi-grid .fe-card:nth-child(3)::after { content: "person" !important; }
.sf-finance-expense-livewire-root .fe-kpi-grid .fe-card:nth-child(4)::after { content: "category" !important; }

/* KPI text sizes closer to Employment page */
.sf-finance-expense-livewire-root .fe-label {
    margin: 0 0 18px 0 !important;
    font-size: 17px !important;
    line-height: 1.15 !important;
    font-weight: 950 !important;
    letter-spacing: -0.04em !important;
    text-transform: none !important;
    color: #234b74 !important;
}

.sf-finance-expense-livewire-root .fe-value {
    margin: 0 !important;
    font-size: clamp(34px, 3.4vw, 56px) !important;
    line-height: .95 !important;
    font-weight: 950 !important;
    letter-spacing: -0.075em !important;
    color: #234b74 !important;
}

.sf-finance-expense-livewire-root .fe-sub {
    margin-top: 14px !important;
    font-size: 13px !important;
    line-height: 1.45 !important;
    font-weight: 700 !important;
    color: #64748b !important;
}

/* Main panels same as Employment blocks */
.sf-finance-expense-livewire-root .fe-panel {
    min-height: 260px !important;
    padding: 28px !important;
}

/* Section title area: text left, icon circle right */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-start !important;
    width: 100% !important;
    min-height: 58px !important;
    margin: 0 0 12px 0 !important;
    padding: 0 76px 0 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #234b74 !important;
    font-size: 22px !important;
    line-height: 1.15 !important;
    font-weight: 950 !important;
    letter-spacing: -0.05em !important;
    text-transform: none !important;
}

/* The icon circle on the RIGHT of each panel title */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child::after {
    content: "dashboard" !important;
    position: absolute !important;
    top: 0 !important;
    right: 0 !important;
    width: 58px !important;
    height: 58px !important;
    border-radius: 999px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(224, 242, 254, .95) !important;
    color: #0f172a !important;
    font-family: "Material Symbols Rounded" !important;
    font-size: 28px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    -webkit-font-feature-settings: "liga" !important;
    font-feature-settings: "liga" !important;
    letter-spacing: normal !important;
    text-transform: none !important;
}

/* Panel icon mapping */
.sf-finance-expense-livewire-root .fe-two-grid > .fe-panel:nth-child(1) > .fe-pill:first-child::after {
    content: "dashboard" !important;
}
.sf-finance-expense-livewire-root .fe-two-grid > .fe-panel:nth-child(2) > .fe-pill:first-child::after {
    content: "verified" !important;
}
.sf-finance-expense-livewire-root section.fe-panel > .fe-pill:first-child::after {
    content: "link" !important;
}
.sf-finance-expense-livewire-root .fe-two-grid .fe-panel:nth-child(1) > .fe-pill--green:first-child::after {
    content: "receipt_long" !important;
}
.sf-finance-expense-livewire-root .fe-two-grid .fe-panel:nth-child(2) > .fe-pill--slate:first-child::after {
    content: "sticky_note_2" !important;
}

/* Hide any old inline icon/text placed before section title */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child .material-symbols-rounded,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child svg,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child i {
    display: none !important;
}

/* Panel subtitle under title */
.sf-finance-expense-livewire-root .fe-panel > .fe-sub:first-of-type,
.sf-finance-expense-livewire-root .fe-panel > p:first-of-type {
    max-width: 620px !important;
    margin: -6px 0 20px 0 !important;
    color: #64748b !important;
    font-size: 14px !important;
    line-height: 1.55 !important;
    font-weight: 700 !important;
}

/* Inner rows/boxes like employment list rows */
.sf-finance-expense-livewire-root .fe-box {
    border-radius: 20px !important;
    padding: 16px 18px !important;
    background: rgba(248,250,252,.86) !important;
    border: 1px solid rgba(15,23,42,.08) !important;
    box-shadow: none !important;
}

.sf-finance-expense-livewire-root .fe-field-label {
    margin: 0 !important;
    font-size: 11px !important;
    font-weight: 950 !important;
    letter-spacing: .13em !important;
    text-transform: uppercase !important;
    color: #64748b !important;
}

.sf-finance-expense-livewire-root .fe-field-value {
    margin-top: 8px !important;
    font-size: 15px !important;
    line-height: 1.35 !important;
    font-weight: 900 !important;
    color: #0f172a !important;
    letter-spacing: -0.02em !important;
}

/* Prevent top icons from touching gradient line */
.sf-finance-expense-livewire-root .fe-card::after {
    top: 34px !important;
    right: 22px !important;
}

/* Attachment links / open buttons */
.sf-finance-expense-livewire-root .fe-box a,
.sf-finance-expense-livewire-root a:not(.sf-md3-action) {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 38px !important;
    padding: 8px 14px !important;
    border-radius: 999px !important;
    background: #e0f2fe !important;
    color: #0f172a !important;
    text-decoration: none !important;
    font-weight: 900 !important;
}

/* Dark compatible */
.dark .sf-finance-expense-livewire-root .fe-card,
.dark .sf-finance-expense-livewire-root .fe-panel {
    background:
        radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
        rgba(15,23,42,.72) !important;
    border-color: rgba(148,163,184,.18) !important;
    box-shadow: 0 18px 46px rgba(0,0,0,.18) !important;
}

.dark .sf-finance-expense-livewire-root .fe-label,
.dark .sf-finance-expense-livewire-root .fe-value,
.dark .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
    color: #fff !important;
}

.dark .sf-finance-expense-livewire-root .fe-sub,
.dark .sf-finance-expense-livewire-root .fe-panel > .fe-sub:first-of-type,
.dark .sf-finance-expense-livewire-root .fe-panel > p:first-of-type {
    color: #94a3b8 !important;
}

.dark .sf-finance-expense-livewire-root .fe-box {
    background: rgba(15,23,42,.52) !important;
    border-color: rgba(148,163,184,.16) !important;
}

.dark .sf-finance-expense-livewire-root .fe-field-value {
    color: #f8fafc !important;
}

/* Responsive */
@media (max-width: 1100px) {
    .sf-finance-expense-livewire-root .fe-kpi-grid,
    .sf-finance-expense-livewire-root .fe-two-grid {
        grid-template-columns: 1fr !important;
    }

    .sf-finance-expense-livewire-root .fe-card {
        min-height: 170px !important;
    }
}
</style>









<style id="sf-finance-expense-body-exact-align-final">
/* Finance Expense: exact body alignment with header */

/* The page root follows the same max width as Employment */
.sf-finance-expense-livewire-root {
    width: min(100%, 1280px) !important;
    max-width: 1280px !important;
    margin-inline: auto !important;
}

/* Header stays as the reference */
.sf-finance-expense-livewire-root > .sf-md3-expense-hero {
    width: 100% !important;
    max-width: 1280px !important;
    margin-inline: 0 !important;
}

/*
   Body is visibly narrower because of an inner wrapper.
   Pull it outward to match the header line.
*/
.sf-finance-expense-livewire-root .fe-view-shell {
    width: calc(100% + 184px) !important;
    max-width: calc(100% + 184px) !important;
    margin-left: -92px !important;
    margin-right: -92px !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    box-sizing: border-box !important;
    background: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
}

/* Body children should fill the corrected shell */
.sf-finance-expense-livewire-root .fe-view-shell > section,
.sf-finance-expense-livewire-root .fe-view-shell > div,
.sf-finance-expense-livewire-root .fe-kpi-grid,
.sf-finance-expense-livewire-root .fe-two-grid,
.sf-finance-expense-livewire-root .fe-panel {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
}

/* Keep clean spacing like Employment */
.sf-finance-expense-livewire-root .fe-kpi-grid {
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 18px !important;
}

.sf-finance-expense-livewire-root .fe-two-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    gap: 22px !important;
}

/* Fix top stat icons so they don't overlap the blue line */
.sf-finance-expense-livewire-root .fe-card::after {
    top: 38px !important;
    right: 24px !important;
    opacity: .42 !important;
}

/* Section headers: keep icon/title line clean until we rebuild icons as circles */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
    background: transparent !important;
    border: 0 !important;
    padding: 0 !important;
    color: #234b74 !important;
    font-size: 22px !important;
    font-weight: 950 !important;
    letter-spacing: -0.04em !important;
    text-transform: none !important;
}

/* Responsive: remove negative alignment on small screens */
@media (max-width: 1100px) {
    .sf-finance-expense-livewire-root .fe-view-shell {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .sf-finance-expense-livewire-root .fe-kpi-grid,
    .sf-finance-expense-livewire-root .fe-two-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>



<style id="sf-finance-expense-gap-right-icons-final">
/* 1) Reduce gap between header and body */
.sf-finance-expense-livewire-root > .sf-md3-expense-hero {
    margin-bottom: 22px !important;
}

.sf-finance-expense-livewire-root .fe-view-shell {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* 2) Make body panels behave like Employment blocks */
.sf-finance-expense-livewire-root .fe-panel {
    position: relative !important;
    overflow: hidden !important;
}

/* Make section title clean on the left */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    width: auto !important;
    max-width: calc(100% - 88px) !important;
    margin: 0 0 10px 0 !important;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    color: #234b74 !important;
    font-size: 24px !important;
    line-height: 1.1 !important;
    font-weight: 950 !important;
    letter-spacing: -0.045em !important;
    text-transform: none !important;
}

/* Hide the inline/left icon inside section headline */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child svg,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child .material-symbols-rounded,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child i,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child::before {
    display: none !important;
    content: none !important;
}

/* Right circular icon like Employment cards */
.sf-finance-expense-livewire-root .fe-panel::after {
    position: absolute !important;
    top: 28px !important;
    right: 30px !important;
    width: 58px !important;
    height: 58px !important;
    border-radius: 999px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(224, 242, 254, .92) !important;
    color: #0f2542 !important;
    font-family: 'Material Symbols Rounded' !important;
    font-size: 29px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    letter-spacing: normal !important;
    text-transform: none !important;
    -webkit-font-feature-settings: 'liga' !important;
    font-feature-settings: 'liga' !important;
    -webkit-font-smoothing: antialiased !important;
    box-shadow: 0 16px 34px rgba(14, 165, 233, .10) !important;
    z-index: 3 !important;
    opacity: 1 !important;
}

/* Icons by panel type */
.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--blue)::after {
    content: "dashboard" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--amber)::after {
    content: "verified" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--purple)::after {
    content: "link" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--green)::after {
    content: "receipt_long" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--slate)::after {
    content: "notes" !important;
}

/* Give title/subtitle enough room before the right icon */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child + *,
.sf-finance-expense-livewire-root .fe-panel > p:first-of-type,
.sf-finance-expense-livewire-root .fe-panel > .fe-sub:first-of-type,
.sf-finance-expense-livewire-root .fe-panel > .fe-field-value--soft:first-of-type {
    max-width: calc(100% - 92px) !important;
}

/* Top KPI icons: keep them clean in the corner, not crossing the line */
.sf-finance-expense-livewire-root .fe-card {
    position: relative !important;
    overflow: hidden !important;
}

.sf-finance-expense-livewire-root .fe-card::after {
    top: 34px !important;
    right: 26px !important;
    width: 40px !important;
    height: 40px !important;
    opacity: .34 !important;
    z-index: 2 !important;
}

/* Dark mode */
.dark .sf-finance-expense-livewire-root .fe-panel::after,
html.dark .sf-finance-expense-livewire-root .fe-panel::after,
body.dark .sf-finance-expense-livewire-root .fe-panel::after {
    background: rgba(15, 23, 42, .62) !important;
    color: #e0f2fe !important;
    border: 1px solid rgba(148, 163, 184, .18) !important;
}

/* Mobile */
@media (max-width: 900px) {
    .sf-finance-expense-livewire-root > .sf-md3-expense-hero {
        margin-bottom: 18px !important;
    }

    .sf-finance-expense-livewire-root .fe-panel::after {
        top: 22px !important;
        right: 22px !important;
        width: 50px !important;
        height: 50px !important;
        font-size: 25px !important;
    }

    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
        max-width: calc(100% - 76px) !important;
        font-size: 22px !important;
    }
}
</style>



<style id="sf-force-panel-icons-right-final">
/* Force Finance Expense body section icons to match Employment: right circle, not beside title */

/* Less gap between hero and body */
.sf-finance-expense-livewire-root > .sf-md3-expense-hero {
    margin-bottom: 18px !important;
}

/* Body shell starts closer */
.sf-finance-expense-livewire-root .fe-view-shell {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* Every panel must be relative for right icon */
.sf-finance-expense-livewire-root .fe-panel {
    position: relative !important;
    overflow: hidden !important;
}

/* Hide existing left icons beside titles */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child svg,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child i,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child .material-symbols-rounded,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child > span:first-child,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child::before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child::after {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
    width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
    content: none !important;
}

/* Clean title text */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
    display: block !important;
    width: auto !important;
    max-width: calc(100% - 92px) !important;
    margin: 0 0 10px 0 !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    color: #234b74 !important;
    font-size: 24px !important;
    line-height: 1.1 !important;
    font-weight: 950 !important;
    letter-spacing: -0.045em !important;
    text-transform: none !important;
}

/* Right circle icon */
.sf-finance-expense-livewire-root .fe-panel::after {
    position: absolute !important;
    top: 28px !important;
    right: 30px !important;
    width: 58px !important;
    height: 58px !important;
    border-radius: 999px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(224, 242, 254, .95) !important;
    color: #0f2542 !important;
    font-family: 'Material Symbols Rounded' !important;
    font-size: 29px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    letter-spacing: normal !important;
    text-transform: none !important;
    -webkit-font-feature-settings: 'liga' !important;
    font-feature-settings: 'liga' !important;
    -webkit-font-smoothing: antialiased !important;
    box-shadow: 0 16px 34px rgba(14, 165, 233, .10) !important;
    z-index: 10 !important;
    opacity: 1 !important;
}

/* Icon content based on panel type */
.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--blue)::after {
    content: "dashboard" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--amber)::after {
    content: "verified" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--purple)::after {
    content: "link" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--green)::after {
    content: "receipt_long" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(.fe-pill--slate)::after {
    content: "notes" !important;
}

/* Keep subtitle away from right icon */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child + .fe-sub,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child + p,
.sf-finance-expense-livewire-root .fe-panel > .fe-sub:first-of-type,
.sf-finance-expense-livewire-root .fe-panel > p:first-of-type {
    max-width: calc(100% - 92px) !important;
}

/* Dark mode */
.dark .sf-finance-expense-livewire-root .fe-panel::after,
html.dark .sf-finance-expense-livewire-root .fe-panel::after,
body.dark .sf-finance-expense-livewire-root .fe-panel::after {
    background: rgba(224, 242, 254, .14) !important;
    color: #e0f2fe !important;
    border: 1px solid rgba(148, 163, 184, .18) !important;
}

/* Mobile */
@media (max-width: 900px) {
    .sf-finance-expense-livewire-root > .sf-md3-expense-hero {
        margin-bottom: 16px !important;
    }

    .sf-finance-expense-livewire-root .fe-panel::after {
        top: 22px !important;
        right: 22px !important;
        width: 50px !important;
        height: 50px !important;
        font-size: 25px !important;
    }

    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
        max-width: calc(100% - 76px) !important;
        font-size: 22px !important;
    }
}
</style>






<style id="sf-final-force-body-panel-icons-right">
/* FINAL FIX: move body section icons to right circle, same employment style */

/* reduce gap between hero and body */
.sf-finance-expense-livewire-root .sf-md3-expense-hero {
    margin-bottom: 14px !important;
}

.sf-finance-expense-livewire-root .fe-view-shell {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

/* panels */
.sf-finance-expense-livewire-root .fe-panel {
    position: relative !important;
    overflow: hidden !important;
    padding-top: 42px !important;
}

/* kill all old left icons on section title */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child::before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child:before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child::after,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child:after,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--blue::before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--blue:before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--amber::before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--amber:before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--purple::before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--purple:before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--green::before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--green:before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--slate::before,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill--slate:before {
    content: none !important;
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

/* hide real icon elements if they exist before title */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child svg,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child i,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child .material-symbols-rounded,
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child [class*="icon"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    min-width: 0 !important;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* title text only */
.sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
    display: block !important;
    width: auto !important;
    max-width: calc(100% - 92px) !important;
    margin: 0 0 12px 0 !important;
    padding: 0 !important;
    background: transparent !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    color: #234b74 !important;
    font-size: 26px !important;
    line-height: 1.05 !important;
    font-weight: 950 !important;
    letter-spacing: -0.055em !important;
    text-transform: none !important;
}

/* right circle icon */
.sf-finance-expense-livewire-root .fe-panel::after {
    position: absolute !important;
    top: 34px !important;
    right: 34px !important;
    width: 58px !important;
    height: 58px !important;
    border-radius: 999px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(224, 242, 254, .95) !important;
    color: #0f2542 !important;
    font-family: 'Material Symbols Rounded' !important;
    font-size: 30px !important;
    font-weight: 600 !important;
    line-height: 1 !important;
    letter-spacing: normal !important;
    text-transform: none !important;
    -webkit-font-feature-settings: 'liga' !important;
    font-feature-settings: 'liga' !important;
    -webkit-font-smoothing: antialiased !important;
    box-shadow: 0 16px 34px rgba(14, 165, 233, .12) !important;
    z-index: 20 !important;
    pointer-events: none !important;
}

/* exact section icons */
.sf-finance-expense-livewire-root .fe-panel:has(> .fe-pill--blue)::after {
    content: "dashboard" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(> .fe-pill--amber)::after {
    content: "verified" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(> .fe-pill--purple)::after {
    content: "link" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(> .fe-pill--green)::after {
    content: "receipt_long" !important;
}

.sf-finance-expense-livewire-root .fe-panel:has(> .fe-pill--slate)::after {
    content: "notes" !important;
}

/* subtitle should not go under the icon */
.sf-finance-expense-livewire-root .fe-panel > .fe-sub:first-of-type,
.sf-finance-expense-livewire-root .fe-panel > p:first-of-type {
    max-width: calc(100% - 92px) !important;
}

/* dark mode */
.dark .sf-finance-expense-livewire-root .fe-panel::after,
html.dark .sf-finance-expense-livewire-root .fe-panel::after,
body.dark .sf-finance-expense-livewire-root .fe-panel::after {
    background: rgba(224, 242, 254, .14) !important;
    color: #e0f2fe !important;
    border: 1px solid rgba(148, 163, 184, .18) !important;
}

/* mobile */
@media (max-width: 900px) {
    .sf-finance-expense-livewire-root .fe-panel {
        padding-top: 34px !important;
    }

    .sf-finance-expense-livewire-root .fe-panel::after {
        top: 24px !important;
        right: 24px !important;
        width: 50px !important;
        height: 50px !important;
        font-size: 25px !important;
    }

    .sf-finance-expense-livewire-root .fe-panel > .fe-pill:first-child {
        max-width: calc(100% - 76px) !important;
        font-size: 23px !important;
    }

    .sf-finance-expense-livewire-root .fe-panel > .fe-sub:first-of-type,
    .sf-finance-expense-livewire-root .fe-panel > p:first-of-type {
        max-width: calc(100% - 76px) !important;
    }
}
</style>




















<style id="sf-finance-real-right-circle-icons-final">
/* Final finance body alignment + Employment-like right block icons */
.sf-finance-expense-livewire-root .sf-md3-expense-hero {
    margin-bottom: 34px !important;
}

.sf-finance-expense-livewire-root .fe-view-shell,
.sf-finance-expense-livewire-root .sf-fe-body {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.sf-finance-expense-livewire-root .sf-block-head {
    display: flex !important;
    align-items: flex-start !important;
    justify-content: space-between !important;
    gap: 14px !important;
    margin-bottom: 18px !important;
}

.sf-finance-expense-livewire-root .sf-block-head > div:first-child {
    min-width: 0 !important;
    flex: 1 1 auto !important;
}

.sf-finance-expense-livewire-root .sf-block-title {
    display: block !important;
    color: #234b74 !important;
    font-size: 20px !important;
    font-weight: 950 !important;
    letter-spacing: -.045em !important;
    line-height: 1.1 !important;
}

.sf-finance-expense-livewire-root .sf-block-title .material-symbols-rounded,
.sf-finance-expense-livewire-root .sf-block-title svg,
.sf-finance-expense-livewire-root .sf-block-title i {
    display: none !important;
}

.sf-finance-expense-livewire-root .sf-block-head-icon {
    flex: 0 0 44px !important;
    width: 44px !important;
    height: 44px !important;
    padding: 0 !important;
    border-radius: 999px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    background: #e0f2fe !important;
    color: #0f172a !important;
    border: 0 !important;
    box-shadow: none !important;
}

.sf-finance-expense-livewire-root .sf-block-head-icon svg {
    width: 20px !important;
    height: 20px !important;
    display: block !important;
}

/* Remove pseudo icons that were fighting with the real icons */
.sf-finance-expense-livewire-root .sf-block::after,
.sf-finance-expense-livewire-root .sf-block::before,
.sf-finance-expense-livewire-root .fe-panel::after,
.sf-finance-expense-livewire-root .fe-panel::before {
    content: none !important;
    display: none !important;
}

/* Keep first metric card icons away from the top accent line */
.sf-finance-expense-livewire-root .sf-finance-card {
    position: relative !important;
    overflow: hidden !important;
}

.sf-finance-expense-livewire-root .sf-finance-card .sf-card-icon,
.sf-finance-expense-livewire-root .sf-finance-card > .material-symbols-rounded:first-child {
    position: absolute !important;
    top: 22px !important;
    right: 24px !important;
    width: 34px !important;
    height: 34px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: .28 !important;
    color: #2563eb !important;
    font-size: 27px !important;
    line-height: 1 !important;
    overflow: hidden !important;
}

.dark .sf-finance-expense-livewire-root .sf-block-title {
    color: #ffffff !important;
}

.dark .sf-finance-expense-livewire-root .sf-block-head-icon {
    background: rgba(224,242,254,.14) !important;
    color: #ffffff !important;
    border: 1px solid rgba(148,163,184,.18) !important;
}
</style>


<style id="sf-finance-kpi-icons-beside-title-final">
/* Move top KPI icons beside the title text instead of top-right */
.sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card,
.sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card {
    position: relative !important;
    overflow: hidden !important;
}

/* Cancel old top-right absolute KPI icon style */
.sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card .sf-card-icon,
.sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card > .material-symbols-rounded:first-child,
.sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card .sf-card-icon,
.sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card > .material-symbols-rounded:first-child {
    position: static !important;
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    border-radius: 999px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 9px 0 0 !important;
    padding: 0 !important;
    background: #e0f2fe !important;
    color: #234b74 !important;
    opacity: 1 !important;
    font-size: 18px !important;
    line-height: 1 !important;
    vertical-align: middle !important;
    transform: translateY(-1px) !important;
}

/* Put icon and title on one line */
.sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card .sf-finance-title,
.sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card .sf-finance-title {
    display: inline-flex !important;
    align-items: center !important;
    gap: 0 !important;
    margin: 0 0 14px 0 !important;
    line-height: 1.15 !important;
}

/* Reorder: icon first, title second */
.sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card .sf-card-icon,
.sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card .sf-card-icon {
    float: left !important;
}

/* Keep the blue top line clean */
.sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card::before,
.sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card::before {
    margin-bottom: 18px !important;
}

/* Prevent old pseudo icons from appearing on KPI cards */
.sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card::after,
.sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card::after {
    content: none !important;
    display: none !important;
}

.dark .sf-finance-expense-livewire-root .sf-fe-summary-grid .sf-finance-card .sf-card-icon,
.dark .sf-finance-expense-livewire-root .sf-finance-grid.sf-fe-summary-grid .sf-finance-card .sf-card-icon {
    background: rgba(224,242,254,.14) !important;
    color: #ffffff !important;
    border: 1px solid rgba(148,163,184,.18) !important;
}
</style>


<style id="sf-finance-action-icon-color-match-final">
/* Finance Expense header action icons: icon color follows button text */
.sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action .sf-md3-icon svg path {
    color: currentColor !important;
    fill: currentColor !important;
    stroke: currentColor !important;
}

/* Yellow / warning button: black text + black icon */
.sf-finance-expense-livewire-root .sf-md3-action-warning,
.sf-finance-expense-livewire-root .sf-md3-action-yellow {
    color: #07152f !important;
}

.sf-finance-expense-livewire-root .sf-md3-action-warning strong,
.sf-finance-expense-livewire-root .sf-md3-action-yellow strong,
.sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-yellow .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-yellow .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-warning .sf-md3-icon svg path,
.sf-finance-expense-livewire-root .sf-md3-action-yellow .sf-md3-icon svg path {
    color: #07152f !important;
    fill: #07152f !important;
    stroke: #07152f !important;
}

/* Blue / red / purple / green / gray: white text + white icon */
.sf-finance-expense-livewire-root .sf-md3-action-blue,
.sf-finance-expense-livewire-root .sf-md3-action-red,
.sf-finance-expense-livewire-root .sf-md3-action-purple,
.sf-finance-expense-livewire-root .sf-md3-action-green,
.sf-finance-expense-livewire-root .sf-md3-action-gray,
.sf-finance-expense-livewire-root .sf-md3-action-back {
    color: #ffffff !important;
}

.sf-finance-expense-livewire-root .sf-md3-action-blue strong,
.sf-finance-expense-livewire-root .sf-md3-action-red strong,
.sf-finance-expense-livewire-root .sf-md3-action-purple strong,
.sf-finance-expense-livewire-root .sf-md3-action-green strong,
.sf-finance-expense-livewire-root .sf-md3-action-gray strong,
.sf-finance-expense-livewire-root .sf-md3-action-back strong,
.sf-finance-expense-livewire-root .sf-md3-action-blue .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-red .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-purple .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-green .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-gray .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-back .sf-md3-icon,
.sf-finance-expense-livewire-root .sf-md3-action-blue .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-red .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-purple .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-green .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-gray .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-back .sf-md3-icon svg,
.sf-finance-expense-livewire-root .sf-md3-action-blue .sf-md3-icon svg path,
.sf-finance-expense-livewire-root .sf-md3-action-red .sf-md3-icon svg path,
.sf-finance-expense-livewire-root .sf-md3-action-purple .sf-md3-icon svg path,
.sf-finance-expense-livewire-root .sf-md3-action-green .sf-md3-icon svg path,
.sf-finance-expense-livewire-root .sf-md3-action-gray .sf-md3-icon svg path,
.sf-finance-expense-livewire-root .sf-md3-action-back .sf-md3-icon svg path {
    color: #ffffff !important;
    fill: #ffffff !important;
    stroke: #ffffff !important;
}
</style>

    <x-filament-actions::modals />


<style>
    /*
     |--------------------------------------------------------------------------
     | Sada Fezzan Finance Expense Print Layout
     |--------------------------------------------------------------------------
     | Makes the Finance Expense view printable as a clean formal report.
     */
    @media print {
        @page {
            size: A4;
            margin: 12mm;
        }

        html,
        body {
            background: #ffffff !important;
            color: #0f172a !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body * {
            visibility: hidden !important;
        }

        .sf-finance-expense-livewire-root,
        .sf-finance-expense-livewire-root * {
            visibility: visible !important;
        }

        .sf-finance-expense-livewire-root {
            position: absolute !important;
            inset: 0 auto auto 0 !important;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }

        .fi-sidebar,
        .fi-topbar,
        .fi-header,
        header.fi-header,
        nav,
        aside,
        .sf-md3-hero-actions,
        .sf-print-button,
        .fi-btn,
        [data-filament-sidebar],
        [data-filament-topbar] {
            display: none !important;
            visibility: hidden !important;
        }

        .sf-md3-expense-hero,
        .sf-finance-expense-livewire-root .sf-block,
        .sf-finance-expense-livewire-root .sf-card,
        .sf-finance-expense-livewire-root .sf-finance-card {
            box-shadow: none !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .sf-md3-expense-hero {
            background: linear-gradient(135deg, #081a34 0%, #12385d 55%, #2f6f73 100%) !important;
            border-radius: 18px !important;
            padding: 22px !important;
            margin: 0 0 12px 0 !important;
            color: #ffffff !important;
        }

        .sf-md3-expense-hero *,
        .sf-md3-expense-hero h1,
        .sf-md3-expense-hero strong,
        .sf-md3-expense-hero span,
        .sf-md3-expense-hero small,
        .sf-md3-expense-hero p {
            color: #ffffff !important;
        }

        .sf-md3-expense-hero h1 {
            font-size: 34px !important;
            line-height: 1 !important;
            letter-spacing: -0.03em !important;
            margin: 0 !important;
        }

        .sf-md3-hero-main {
            display: block !important;
        }

        .sf-md3-hero-stats {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 10px !important;
            margin-top: 18px !important;
        }

        .sf-md3-hero-stats > div {
            border: 1px solid rgba(255,255,255,.28) !important;
            border-radius: 14px !important;
            padding: 12px !important;
            background: rgba(255,255,255,.10) !important;
        }

        .sf-finance-expense-livewire-root section,
        .sf-finance-expense-livewire-root .sf-block-grid,
        .sf-finance-expense-livewire-root .sf-grid,
        .sf-finance-expense-livewire-root .sf-kpi-grid {
            max-width: none !important;
            width: 100% !important;
        }

        .sf-block-grid {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 10px !important;
        }

        .sf-finance-expense-livewire-root .sf-block {
            border: 1px solid #dbe3ea !important;
            border-radius: 16px !important;
            padding: 16px !important;
            background: #ffffff !important;
            margin-bottom: 10px !important;
        }

        .sf-block-title {
            color: #0f2f4a !important;
            font-size: 18px !important;
            font-weight: 900 !important;
        }

        .sf-block-subtitle,
        .sf-expense-field-hint {
            color: #475569 !important;
            font-size: 11px !important;
            line-height: 1.4 !important;
        }

        .sf-list,
        .sf-list-compact {
            gap: 7px !important;
        }

        .sf-row {
            border: 1px solid #e5edf3 !important;
            border-radius: 10px !important;
            padding: 9px 11px !important;
            background: #f8fafc !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .sf-row span {
            color: #64748b !important;
            font-size: 10px !important;
            letter-spacing: .10em !important;
            text-transform: uppercase !important;
            font-weight: 800 !important;
        }

        .sf-row strong {
            color: #0f172a !important;
            font-size: 12px !important;
            font-weight: 850 !important;
        }

        .sf-pill {
            border: 1px solid #dbeafe !important;
            background: #eff6ff !important;
            color: #0f2f4a !important;
            box-shadow: none !important;
        }

        a[href]::after {
            content: "" !important;
        }

        .sf-block-head-icon {
            display: none !important;
        }

        .sf-finance-expense-livewire-root::after {
            content: "Sada Fezzan ERP • Finance Expense Report";
            display: block !important;
            visibility: visible !important;
            margin-top: 18px !important;
            padding-top: 8px !important;
            border-top: 1px solid #dbe3ea !important;
            color: #64748b !important;
            font-size: 10px !important;
            text-align: center !important;
            font-weight: 700 !important;
        }
    }
</style>

</div>

<style id="sf-finance-action-buttons-match-employment-final">
    /*
     * Finance Expense View action buttons
     * Final reference: Employment Profile compact action buttons.
     * Buttons remain controlled by PHP state/action logic.
     */

    .sf-finance-expense-livewire-root .sf-md3-hero-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: flex-start !important;
        justify-content: flex-end !important;
        gap: 10px 12px !important;
        max-width: 590px !important;
        margin-left: auto !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions a.sf-fe-btn,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions button.sf-fe-btn {
        width: auto !important;
        min-width: 132px !important;
        max-width: none !important;
        min-height: 46px !important;
        height: 46px !important;
        padding: 0 20px !important;
        border-radius: 999px !important;

        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 9px !important;

        border: 1px solid rgba(255,255,255,.16) !important;
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.16),
            0 12px 26px rgba(15,23,42,.18) !important;

        font-size: 13px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: -.015em !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        transform: none !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn strong,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn span:not(.sf-md3-icon),
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn .fi-btn-label {
        font-size: 13px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        color: currentColor !important;
        white-space: nowrap !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn .sf-md3-icon {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        min-height: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: currentColor !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn .sf-md3-icon svg,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn svg,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn .fi-btn-icon {
        width: 16px !important;
        height: 16px !important;
        color: currentColor !important;
        fill: currentColor !important;
        stroke: currentColor !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--edit {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #0f172a !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--approve,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--bank {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--paid {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--partial {
        background: linear-gradient(135deg, #a855f7, #7c3aed) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--danger,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--reject {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--back {
        background: linear-gradient(135deg, rgba(71,85,105,.94), rgba(51,65,85,.92)) !important;
        color: #ffffff !important;
    }

    /*
     * When only 2 buttons exist, keep them nice and balanced like Employment.
     */
    .sf-finance-expense-livewire-root .sf-md3-hero-actions:has(.sf-fe-btn:nth-child(2):last-child) .sf-fe-btn {
        min-width: 250px !important;
    }

    /*
     * When 3 or 4 buttons exist, keep compact rows.
     */
    .sf-finance-expense-livewire-root .sf-md3-hero-actions:has(.sf-fe-btn:nth-child(3)) .sf-fe-btn {
        min-width: 220px !important;
    }

    /*
     * When many actions exist, keep them compact and employment-like.
     */
    .sf-finance-expense-livewire-root .sf-md3-hero-actions:has(.sf-fe-btn:nth-child(5)) .sf-fe-btn {
        min-width: 178px !important;
    }

    @media (max-width: 1100px) {
        .sf-finance-expense-livewire-root .sf-md3-hero-actions {
            max-width: 100% !important;
            justify-content: flex-start !important;
        }

        .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn {
            min-width: 160px !important;
        }
    }

    @media (max-width: 720px) {
        .sf-finance-expense-livewire-root .sf-md3-hero-actions {
            display: grid !important;
            grid-template-columns: 1fr !important;
        }

        .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn {
            width: 100% !important;
            min-width: 0 !important;
        }
    }
</style>

<style id="sf-finance-reimbursement-back-button-final">
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-gray,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--back {
        background: linear-gradient(135deg, rgba(71,85,105,.94), rgba(51,65,85,.92)) !important;
        color: #ffffff !important;
    }

    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-md3-action-gray svg,
    .sf-finance-expense-livewire-root .sf-md3-hero-actions .sf-fe-btn--back svg {
        color: #ffffff !important;
        fill: currentColor !important;
        stroke: currentColor !important;
    }
</style>

