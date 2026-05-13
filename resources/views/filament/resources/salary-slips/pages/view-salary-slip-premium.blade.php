
@php
    $slipRecord = $record ?? $salarySlip ?? $slip ?? null;

    if ($slipRecord) {
        try {
            $slipRecord->loadMissing('days');
        } catch (\Throwable $e) {
            //
        }
    }

    $sfDays = collect($slipRecord->days ?? []);

    $sfPresentCount = $sfDays->where('attendance_status', 'present')->count();
    $sfAbsentCount = $sfDays->where('attendance_status', 'absent')->count();
    $sfSickCount = $sfDays->where('attendance_status', 'sick')->count();
    $sfLeaveCount = $sfDays->where('attendance_status', 'leave')->count();
    $sfUnpaidLeaveCount = $sfDays->where('attendance_status', 'unpaid_leave')->count();
    $sfHolidayCount = $sfDays->where('attendance_status', 'holiday')->count();
    $sfTravelCount = $sfDays->where('attendance_status', 'travel')->count();
    $sfOtherCount = $sfDays->where('attendance_status', 'other')->count();

    $sfPaidDays = $sfDays->filter(function ($day) {
        $status = $day->attendance_status ?: 'present';

        if (in_array($status, ['absent', 'unpaid_leave'], true)) {
            return false;
        }

        if (property_exists($day, 'is_paid') || isset($day->is_paid_day)) {
            return (bool) $day->is_paid_day;
        }

        if (property_exists($day, 'paid') || false) {
            return false;
        }

        return true;
    })->count();

    $sfNotWorkedDays = $sfDays->count() - $sfPaidDays;

    $sfDailyRate = (float) ($slipRecord->daily_rate ?? 0);
    $sfMonthlySalary = (float) ($slipRecord->monthly_salary ?? 0);
    $sfAdjustments = (float) ($slipRecord->adjustments_amount ?? 0);
    $sfDeductions = (float) ($slipRecord->deductions_amount ?? 0);

    if (($slipRecord->salary_basis ?? null) === \App\Models\SalarySlip::BASIS_MONTHLY && $sfMonthlySalary > 0) {
        $sfBaseAmount = round(($sfMonthlySalary / max(1, $sfDays->count())) * $sfPaidDays, 2);
    } else {
        $sfBaseAmount = round($sfDailyRate * $sfPaidDays, 2);
    }

    $sfNetAmount = round($sfBaseAmount + $sfAdjustments - $sfDeductions, 2);
@endphp
@php
    /*
     |--------------------------------------------------------------------------
     | Sada Fezzan Salary Slip Attendance / Financial Summary Override
     |--------------------------------------------------------------------------
     | The visual page uses $summary in several places. We force $summary here
     | from actual attendance rows so Draft/old days_worked/net_amount values
     | cannot override the correct result.
     */
    $summary = $summary ?? [];

    $summary['worked_days'] = $sfPaidDays;
    $summary['paid_days'] = $sfPaidDays;
    $summary['not_worked_unpaid'] = $sfNotWorkedDays;

    $summary['present'] = $sfPresentCount;
    $summary['absent'] = $sfAbsentCount;
    $summary['sick'] = $sfSickCount;
    $summary['leave'] = $sfLeaveCount;
    $summary['unpaid_leave'] = $sfUnpaidLeaveCount;
    $summary['holiday'] = $sfHolidayCount;
    $summary['travel'] = $sfTravelCount;
    $summary['other'] = $sfOtherCount;

    $summary['gross_amount'] = $sfBaseAmount;
    $summary['base_amount'] = $sfBaseAmount;
    $summary['additions'] = $sfAdjustments;
    $summary['deductions'] = $sfDeductions;
    $summary['daily_rate'] = $sfDailyRate;
    $summary['net_amount'] = $sfNetAmount;
    $summary['final_net_amount'] = $sfNetAmount;
@endphp

@php
    $record = $this->record;
    $theme = $this->getStatusTheme();
    $summary = $this->getAttendanceSummary();
    $attachments = $this->getSlipAttachments();

    $employeeName = $this->getEmployeeDisplayName();
    $jobTitle = $this->getEmployeeJobTitle();
    $employeeCode = $this->getEmployeeCode();

    $periodLabel = trim(($record->month ?? $record->period_month ?? '') . ' ' . ($record->year ?? $record->period_year ?? ''));
    if ($periodLabel === '') {
        $periodLabel = $this->safeDate($record->period_start ?? $record->start_date ?? null) . ' - ' . $this->safeDate($record->period_end ?? $record->end_date ?? null);
    }

    $currency = $record->currency ?? 'EUR';

    $grossAmount = $sfBaseAmount;
    $deductions = $sfDeductions;
    $additions = $record->additions_total ?? $record->total_additions ?? $record->addition_amount ?? 0;
    $netAmount = $sfNetAmount;

    $sfLinkedReimbursements = \App\Models\FinanceExpense::query()
        ->where('reimbursed_salary_slip_id', $record->id)
        ->where('paid_by', \App\Models\FinanceExpense::PAID_BY_CANDIDATE)
        ->whereIn('reimbursement_status', [
            \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED,
            \App\Models\FinanceExpense::REIMBURSEMENT_PAID,
        ])
        ->get();

    $sfLinkedReimbursementTotals = $sfLinkedReimbursements
        ->groupBy(fn ($expense) => strtoupper((string) ($expense->reimbursement_currency ?: $expense->currency ?: $currency)))
        ->map(fn ($items) => (float) $items->sum(fn ($expense) => (float) ($expense->reimbursement_amount ?: $expense->amount ?: 0)))
        ->toArray();

    $sfLinkedReimbursementSameCurrency = (float) ($sfLinkedReimbursementTotals[strtoupper((string) $currency)] ?? 0);

    $sfLinkedReimbursementText = collect($sfLinkedReimbursementTotals)
        ->map(fn ($amount, $cur) => number_format((float) $amount, 2) . ' ' . strtoupper((string) $cur))
        ->values()
        ->implode(' · ');

    if ($sfLinkedReimbursementText === '') {
        $sfLinkedReimbursementText = '0.00 ' . strtoupper((string) $currency);
    }

    $additions = (float) $additions + $sfLinkedReimbursementSameCurrency;
    $netAmount = (float) $netAmount + $sfLinkedReimbursementSameCurrency;

    $sfPaymentTreasuryLines = [];

    try {
        $mainTreasuryLabel = method_exists($this, 'getTreasuryAccountLabel') ? $this->getTreasuryAccountLabel() : null;

        if (filled($mainTreasuryLabel) && $mainTreasuryLabel !== '-') {
            $sfPaymentTreasuryLines[] = $mainTreasuryLabel;
        }

        foreach (['treasuryAccount', 'treasury_account'] as $relationName) {
            if ($record && method_exists($record, $relationName)) {
                $account = $record->{$relationName};

                $label = $account?->account_name
                    ?? $account?->name
                    ?? $account?->title
                    ?? null;

                $currencyLabel = $account?->currency ?? null;

                if (filled($label)) {
                    $line = trim($label . (filled($currencyLabel) ? ' — ' . $currencyLabel : ''));

                    if (! in_array($line, $sfPaymentTreasuryLines, true)) {
                        $sfPaymentTreasuryLines[] = $line;
                    }
                }
            }
        }

        if (blank($sfPaymentTreasuryLines)) {
            $sfPaymentTreasuryLines[] = '-';
        }
    } catch (\Throwable $e) {
        $sfPaymentTreasuryLines = ['-'];
    }

    $sfPaymentReferenceLines = [];

    try {
        $mainReference = method_exists($this, 'getPaymentReference') ? $this->getPaymentReference() : ($record->payment_reference ?? $record->bank_reference ?? null);

        if (filled($mainReference) && $mainReference !== '-') {
            $sfPaymentReferenceLines[] = $mainReference;
        }

        foreach (['payment_reference', 'bank_reference', 'transfer_reference', 'cash_reference'] as $refField) {
            if (filled($record?->{$refField}) && ! in_array((string) $record->{$refField}, $sfPaymentReferenceLines, true)) {
                $sfPaymentReferenceLines[] = (string) $record->{$refField};
            }
        }

        if (blank($sfPaymentReferenceLines)) {
            $sfPaymentReferenceLines[] = '-';
        }
    } catch (\Throwable $e) {
        $sfPaymentReferenceLines = ['-'];
    }

@endphp

<x-filament-panels::page>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,600,0,0" />



<style>
    .sf-erp-confirm-card {
        margin: 22px 0;
        padding: 22px;
        border-radius: 32px;
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 18px;
        align-items: flex-start;
        border: 1px solid rgba(15, 23, 42, .08);
        box-shadow: 0 20px 54px rgba(15, 23, 42, .09);
        overflow: hidden;
        position: relative;
    }

    .sf-erp-confirm-card--pending {
        background: linear-gradient(135deg, #fff7ed 0%, #fffbeb 48%, #ecfeff 100%);
        border-color: rgba(245, 158, 11, .42);
    }

    .sf-erp-confirm-card--received {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0fdfa 100%);
        border-color: rgba(16, 185, 129, .38);
    }

    .sf-erp-confirm-card--danger {
        background: linear-gradient(135deg, #fef2f2 0%, #fff7ed 100%);
        border-color: rgba(239, 68, 68, .38);
    }

    .sf-erp-confirm-icon {
        width: 54px;
        height: 54px;
        border-radius: 20px;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, .72);
        border: 1px solid rgba(15, 23, 42, .07);
        color: #0f172a;
        font-size: 22px;
        font-weight: 950;
        box-shadow: 0 12px 30px rgba(15,23,42,.06);
    }

    .sf-erp-confirm-kicker {
        color: #64748b;
        font-size: 12px;
        font-weight: 950;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 7px;
    }

    .sf-erp-confirm-title {
        color: #0f172a;
        font-size: 24px;
        line-height: 1.15;
        font-weight: 950;
        letter-spacing: -.04em;
    }

    .sf-erp-confirm-text {
        margin-top: 8px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.65;
        font-weight: 750;
        max-width: 920px;
    }

    .sf-erp-confirm-meta-grid {
        margin-top: 16px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .sf-erp-confirm-meta-grid > div,
    .sf-erp-confirm-note {
        border-radius: 18px;
        padding: 12px 14px;
        background: rgba(255, 255, 255, .72);
        border: 1px solid rgba(15, 23, 42, .07);
    }

    .sf-erp-confirm-meta-grid strong,
    .sf-erp-confirm-note strong {
        display: block;
        color: #334155;
        font-size: 11px;
        font-weight: 950;
        letter-spacing: .12em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .sf-erp-confirm-meta-grid span,
    .sf-erp-confirm-note span {
        color: #0f172a;
        font-size: 13px;
        font-weight: 850;
    }

    .sf-erp-confirm-note {
        margin-top: 10px;
    }

    .sf-erp-confirm-badge {
        border-radius: 999px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, .74);
        border: 1px solid rgba(15, 23, 42, .08);
        color: #0f172a;
        font-size: 12px;
        font-weight: 950;
        white-space: nowrap;
        box-shadow: 0 10px 26px rgba(15,23,42,.055);
    }

    .sf-erp-confirm-card--pending .sf-erp-confirm-badge {
        color: #b45309;
        border-color: rgba(245, 158, 11, .34);
    }

    .sf-erp-confirm-card--received .sf-erp-confirm-badge {
        color: #047857;
        border-color: rgba(16, 185, 129, .32);
    }

    .sf-erp-confirm-card--danger .sf-erp-confirm-badge {
        color: #b91c1c;
        border-color: rgba(239, 68, 68, .32);
    }

    .dark .sf-erp-confirm-card {
        background: linear-gradient(135deg, rgba(15,23,42,.94), rgba(30,41,59,.88));
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-erp-confirm-icon,
    .dark .sf-erp-confirm-meta-grid > div,
    .dark .sf-erp-confirm-note,
    .dark .sf-erp-confirm-badge {
        background: rgba(255,255,255,.07);
        border-color: rgba(255,255,255,.10);
    }

    .dark .sf-erp-confirm-title,
    .dark .sf-erp-confirm-meta-grid span,
    .dark .sf-erp-confirm-note span {
        color: #ffffff;
    }

    .dark .sf-erp-confirm-kicker,
    .dark .sf-erp-confirm-meta-grid strong,
    .dark .sf-erp-confirm-note strong {
        color: rgba(226,232,240,.74);
    }

    @media (max-width: 980px) {
        .sf-erp-confirm-card {
            grid-template-columns: 1fr;
        }

        .sf-erp-confirm-meta-grid {
            grid-template-columns: 1fr 1fr;
        }

        .sf-erp-confirm-badge {
            width: fit-content;
        }
    }

    @media (max-width: 640px) {
        .sf-erp-confirm-meta-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


<style>
    .sf-attendance-alert-box {
        background: rgba(251, 113, 133, .10) !important;
        border-color: rgba(244, 63, 94, .28) !important;
    }

    .sf-attendance-alert-box .sf-label {
        color: #fb7185 !important;
    }

    .sf-attendance-alert-box .sf-big-number {
        color: #fb7185 !important;
    }

    .dark .sf-attendance-alert-box {
        background: rgba(244, 63, 94, .12) !important;
        border-color: rgba(251, 113, 133, .32) !important;
    }
</style>

<style>
    /*
     * Force Filament page header actions to wrap cleanly.
     * This prevents the last action buttons from disappearing outside the page.
     */
    .fi-page > header,
    .fi-header,
    header.fi-header {
        display: grid !important;
        grid-template-columns: minmax(260px, 0.9fr) minmax(420px, 1.4fr) !important;
        align-items: center !important;
        gap: 22px !important;
        overflow: visible !important;
    }

    .fi-page > header > div:last-child,
    .fi-header > div:last-child,
    header.fi-header > div:last-child,
    .fi-header-actions,
    .fi-ac {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 10px !important;
        overflow: visible !important;
        min-width: 0 !important;
        max-width: 100% !important;
    }

    .fi-page > header .fi-btn,
    .fi-header .fi-btn,
    .fi-header-actions .fi-btn,
    .fi-ac .fi-btn {
        flex: 0 0 auto !important;
        white-space: nowrap !important;
        border-radius: 999px !important;
        min-height: 42px !important;
        padding-inline: 16px !important;
        font-weight: 850 !important;
    }

    .fi-page > header [class*="overflow"],
    .fi-header [class*="overflow"] {
        overflow: visible !important;
    }

    @media (max-width: 1450px) {
        .fi-page > header,
        .fi-header,
        header.fi-header {
            grid-template-columns: 1fr !important;
            align-items: start !important;
        }

        .fi-page > header > div:last-child,
        .fi-header > div:last-child,
        header.fi-header > div:last-child,
        .fi-header-actions,
        .fi-ac {
            justify-content: flex-start !important;
        }
    }
</style>

<style>
    /*
     * Salary Slip Admin Header Actions Fix
     * Allows Filament page actions to wrap cleanly into two lines when there are many actions.
     */
    .fi-header {
        gap: 18px !important;
        align-items: flex-start !important;
    }

    .fi-header .fi-header-heading {
        min-width: 220px !important;
    }

    .fi-header .fi-header-actions,
    .fi-header-actions,
    .fi-ac {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 10px !important;
        max-width: 100% !important;
        overflow: visible !important;
    }

    .fi-header .fi-header-actions > *,
    .fi-header-actions > *,
    .fi-ac > * {
        flex: 0 0 auto !important;
    }

    .fi-header .fi-btn,
    .fi-header-actions .fi-btn,
    .fi-ac .fi-btn {
        white-space: nowrap !important;
        border-radius: 999px !important;
        min-height: 42px !important;
        padding-inline: 16px !important;
        font-weight: 850 !important;
    }

    @media (max-width: 1280px) {
        .fi-header {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        .fi-header .fi-header-actions,
        .fi-header-actions,
        .fi-ac {
            justify-content: flex-start !important;
        }
    }

    @media (max-width: 768px) {
        .fi-header .fi-btn,
        .fi-header-actions .fi-btn,
        .fi-ac .fi-btn {
            width: auto !important;
            max-width: 100% !important;
        }
    }
</style>


    <style>
        .sf-slip-page {
            --sf-card: #ffffff;
            --sf-text: #0f172a;
            --sf-muted: #64748b;
            --sf-soft: #f8fafc;
            --sf-border: rgba(15, 23, 42, .08);
            display: flex;
            flex-direction: column;
            gap: 24px;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .dark .sf-slip-page {
            --sf-card: #111827;
            --sf-text: #f8fafc;
            --sf-muted: #94a3b8;
            --sf-soft: rgba(255, 255, 255, .05);
            --sf-border: rgba(255, 255, 255, .10);
        }

        .sf-hero {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 28px;
            color: white;
            background: linear-gradient(135deg, {{ str_replace(['from-', 'via-', 'to-'], '', $theme['wrap']) == $theme['wrap'] ? '#0f172a,#1e293b,#334155' : match($record->status) {
                'approved' => '#2563eb,#4338ca,#075985',
                'sent_to_bank' => '#f59e0b,#ea580c,#a16207',
                'paid' => '#059669,#047857,#115e59',
                'bank_rejected', 'cancelled' => '#e11d48,#b91c1c,#7f1d1d',
                default => '#334155,#1e293b,#18181b',
            } }});
            box-shadow: 0 24px 70px rgba(15, 23, 42, .22);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .sf-hero:before,
        .sf-hero:after {
            content: "";
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, .20);
            filter: blur(42px);
        }

        .sf-hero:before {
            width: 280px;
            height: 280px;
            right: -90px;
            top: -120px;
        }

        .sf-hero:after {
            width: 330px;
            height: 330px;
            left: 35%;
            bottom: -170px;
        }

        .sf-hero-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
        }

        @media (min-width: 1024px) {
            .sf-hero-content {
                grid-template-columns: 1.1fr 1fr;
                align-items: start;
            }
        }

        .sf-kicker {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 7px 12px;
            background: rgba(255,255,255,.14);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 11px;
            letter-spacing: .18em;
            text-transform: uppercase;
            font-weight: 800;
        }

        .sf-status-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            padding: 7px 12px;
            background: rgba(255,255,255,.92);
            color: #0f172a;
            font-size: 12px;
            font-weight: 900;
        }

        .sf-hero-title {
            margin: 16px 0 0;
            font-size: clamp(30px, 4vw, 46px);
            line-height: 1;
            font-weight: 950;
            letter-spacing: -.05em;
        }

        .sf-hero-subtitle {
            margin-top: 8px;
            color: rgba(255,255,255,.78);
            font-size: 14px;
            font-weight: 600;
        }

        .sf-hero-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        @media (min-width: 640px) {
            .sf-hero-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .sf-hero-stat {
            border-radius: 20px;
            padding: 15px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(14px);
        }

        .sf-label {
            color: var(--sf-muted);
            font-size: 12px;
            font-weight: 700;
        }

        .sf-hero-stat .sf-label {
            color: rgba(255,255,255,.68);
        }

        .sf-value {
            margin-top: 5px;
            color: var(--sf-text);
            font-size: 15px;
            font-weight: 850;
        }

        .sf-hero-stat .sf-value {
            color: white;
            font-size: 14px;
        }

        .sf-grid-3 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        @media (min-width: 1024px) {
            .sf-grid-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .sf-card {
            border-radius: 28px;
            background: var(--sf-card);
            border: 1px solid var(--sf-border);
            box-shadow: 0 14px 40px rgba(15, 23, 42, .06);
            padding: 24px;
        }

        .sf-section-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .sf-section-kicker {
            color: var(--sf-muted);
            font-size: 11px;
            letter-spacing: .18em;
            text-transform: uppercase;
            font-weight: 900;
        }

        .sf-section-title {
            margin-top: 5px;
            color: var(--sf-text);
            font-size: 22px;
            line-height: 1.1;
            font-weight: 950;
            letter-spacing: -.03em;
        }

        .sf-info-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .sf-soft-box {
            border-radius: 20px;
            background: var(--sf-soft);
            border: 1px solid var(--sf-border);
            padding: 16px;
        }

        .sf-money-grid,
        .sf-attendance-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        @media (min-width: 768px) {
            .sf-money-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }

            .sf-attendance-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .sf-big-number {
            color: var(--sf-text);
            font-size: 28px;
            font-weight: 950;
            line-height: 1;
            margin-top: 8px;
        }

        .sf-accent {
            color: {{ match($record->status) {
                'approved' => '#2563eb',
                'sent_to_bank' => '#d97706',
                'paid' => '#047857',
                'bank_rejected', 'cancelled' => '#e11d48',
                default => '#334155',
            } }};
        }

        .sf-attachments-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 20px;
        }

        .sf-attachments-table th {
            background: var(--sf-soft);
            color: var(--sf-muted);
            font-size: 11px;
            letter-spacing: .08em;
            text-transform: uppercase;
            text-align: left;
            padding: 13px 14px;
            border-bottom: 1px solid var(--sf-border);
        }

        .sf-attachments-table td {
            color: var(--sf-text);
            padding: 13px 14px;
            border-bottom: 1px solid var(--sf-border);
            font-size: 14px;
        }

        .sf-attachments-table tr:last-child td {
            border-bottom: 0;
        }

        .sf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            padding: 10px 14px;
            background: var(--sf-text);
            color: var(--sf-card);
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
        }

        .sf-empty {
            border: 1px dashed var(--sf-border);
            border-radius: 22px;
            padding: 30px;
            text-align: center;
            color: var(--sf-muted);
        }
    </style>

    <div class="sf-slip-page">
        <section class="sf-hero">
            <div class="sf-hero-content">
                <div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <span class="sf-kicker">Salary Slip</span>
                        <span class="sf-status-badge">{{ $this->getStatusLabel() }}</span>
                    </div>

                    <h1 class="sf-hero-title">{{ $employeeName }}</h1>
                    <p class="sf-hero-subtitle">{{ $jobTitle }} · Employee Code: {{ $employeeCode }}</p>

                    <div class="sf-salary-hero-actions">
                        
                        @if((bool) auth()->user()?->canErp('salary_slips', 'print'))
                            <a class="sf-salary-hero-btn sf-salary-hero-btn-gray"
                               href="{{ route('salary-slips.print', $record) }}"
                               target="_blank"
                               rel="noopener">
                                <span class="material-symbols-rounded">print</span>
                                <strong>Print</strong>
                            </a>

                            <a class="sf-salary-hero-btn sf-salary-hero-btn-white"
                               href="{{ route('salary-slips.print', $record) }}"
                               target="_blank"
                               rel="noopener">
                                <span class="material-symbols-rounded">download</span>
                                <strong>Download</strong>
                            </a>
                        @endif

@if((bool) auth()->user()?->canErp('salary_slips', 'edit'))
                            <a class="sf-salary-hero-btn sf-salary-hero-btn-yellow"
                               href="{{ \App\Filament\Resources\SalarySlips\SalarySlipResource::getUrl('edit', ['record' => $record]) }}">
                                <span><svg viewBox="0 0 24 24"><path d="M5 19h1.4l9.85-9.85-1.4-1.4L5 17.6V19Zm-2 2v-4.25L16.25 3.5q.3-.3.675-.45T17.7 2.9q.4 0 .775.15t.675.45l1.35 1.35q.3.3.45.675t.15.775q0 .4-.15.775t-.45.675L7.25 21H3Z"/></svg></span>
                                <strong>Edit</strong>
                            </a>
                        @endif

                        
                        @if((string) $record->status === \App\Models\SalarySlip::STATUS_DRAFT && (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                            <button type="button" class="sf-salary-hero-btn sf-salary-hero-btn-blue" wire:click="mountAction('update_attendance_days')">
                                <span class="material-symbols-rounded">event_available</span>
                                <strong>Attendance Schedule / Report</strong>
                            </button>
                        @endif


                        @if((bool) auth()->user()?->canErp('salary_slips', 'edit'))
                            <button type="button" class="sf-salary-hero-btn sf-salary-hero-btn-purple" wire:click="mountAction('salary_adjustment')">
                                <span class="material-symbols-rounded">calculate</span>
                                <strong>Addition / Deduction</strong>
                            </button>
                        @endif

@if(in_array((string) $record->status, ['draft', 'pending', ''], true) && (bool) auth()->user()?->canErp('salary_slips', 'approve'))
                            <button type="button" class="sf-salary-hero-btn sf-salary-hero-btn-blue" wire:click="mountAction('approve')">
                                <span><svg viewBox="0 0 24 24"><path d="m9.55 17.65-5.2-5.2 1.4-1.4 3.8 3.8 8.7-8.7 1.4 1.4-10.1 10.1Z"/></svg></span>
                                <strong>Approve</strong>
                            </button>
                        @endif

                        @if(in_array((string) $record->status, ['approved', 'bank_rejected'], true) && (bool) (auth()->user()?->canErp('salary_slips', 'process_payment') || auth()->user()?->canErp('salary_slips', 'send_to_bank') || auth()->user()?->canErp('salary_slips', 'mark_paid')))
                            <button type="button" class="sf-salary-hero-btn sf-salary-hero-btn-green" wire:click="mountAction('process_payment')">
                                <span><svg viewBox="0 0 24 24"><path d="M3 18V6q0-.825.588-1.413T5 4h14q.825 0 1.413.588T21 6v12q0 .825-.588 1.413T19 20H5q-.825 0-1.413-.588T3 18Zm2-8h14V7H5v3Zm0 3v5h14v-5H5Z"/></svg></span>
                                <strong>Process Payment</strong>
                            </button>
                        @endif

                        @if(! in_array((string) $record->status, ['draft', ''], true) && (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                            <button type="button" class="sf-salary-hero-btn sf-salary-hero-btn-gray" wire:click="mountAction('back_to_draft')">
                                <span><svg viewBox="0 0 24 24"><path d="M12 20q-3.35 0-5.675-2.325T4 12q0-3.35 2.325-5.675T12 4h5.15l-2.6-2.6L16 0l5 5-5 5-1.45-1.4L17.15 6H12q-2.5 0-4.25 1.75T6 12q0 2.5 1.75 4.25T12 18q1.7 0 3.075-.85T17.2 14.9h2.1q-.75 2.25-2.725 3.675T12 20Z"/></svg></span>
                                <strong>Back to Draft</strong>
                            </button>
                        @endif
                    </div>

                </div>

                <div class="sf-hero-grid">
                    <div class="sf-hero-stat">
                        <div class="sf-label">Period</div>
                        <div class="sf-value">{{ $periodLabel }}</div>
                    </div>

                    <div class="sf-hero-stat">
                        <div class="sf-label">Currency</div>
                        <div class="sf-value">{{ $currency }}</div>
                    </div>

                    <div class="sf-hero-stat">
                        <div class="sf-label">Worked Days</div>
                        <div class="sf-value">{{ $summary['worked_days_total'] }}</div>
                    </div>

                    <div class="sf-hero-stat">
                        <div class="sf-label">Net Amount</div>
                        <div class="sf-value">{{ $this->formatMoney($netAmount, $currency) }}</div>
                    </div>
                </div>
            </div>
        </section>

{{-- SADA_RECEIPT_NOTICE_INSIDE_ROOT_START --}}
@php
    $sfReceiptSlip = $slipRecord ?? $record ?? ($this->record ?? null);

    $sfReceiptStatus = $sfReceiptSlip?->employee_confirmation_status;
    $sfMainStatus = $sfReceiptSlip?->status;
    $sfPaymentMethod = $sfReceiptSlip?->payment_method;

    $sfReceiptIsCash = $sfPaymentMethod === 'cash';
    $sfReceiptIsBank = $sfPaymentMethod === 'bank';

    /*
     * Important:
     * Approved salary slip is NOT a payment confirmation stage.
     * Confirmation starts only after Process Payment:
     * - Bank: sent_to_bank
     * - Cash: paid + cash
     */
    $sfNeedsReceiptConfirmation = $sfReceiptSlip
        && in_array($sfMainStatus, ['sent_to_bank', 'paid'], true)
        && in_array($sfReceiptStatus, [null, '', 'pending'], true)
        && (
            $sfMainStatus === 'sent_to_bank'
            || ($sfMainStatus === 'paid' && $sfReceiptIsCash)
        );

    $sfReceiptConfirmed = $sfReceiptStatus === 'received';
    $sfReceiptNotReceived = $sfReceiptStatus === 'not_received' || $sfMainStatus === 'bank_rejected';

    $sfReceiptConfirmedAt = $sfReceiptSlip?->employee_confirmed_at
        ? \Carbon\Carbon::parse($sfReceiptSlip->employee_confirmed_at)->format('d M Y H:i')
        : '-';

    $sfReceiptMethodLabel = $sfReceiptIsCash ? 'Cash Payment' : 'Bank Transfer';

    $sfReceiptTitle = 'Employee Receipt Confirmation';
    $sfReceiptHeadline = null;
    $sfReceiptText = null;
    $sfReceiptBadge = null;
    $sfReceiptCardClass = 'sf-erp-confirm-card--pending';

    if ($sfNeedsReceiptConfirmation) {
        $sfReceiptHeadline = $sfReceiptIsCash
            ? 'Waiting for employee cash receipt confirmation'
            : 'Waiting for employee bank payment confirmation';

        $sfReceiptText = $sfReceiptIsCash
            ? 'This cash salary payment is already posted in treasury, but the employee has not confirmed receiving the cash from the portal yet.'
            : 'This bank salary payment has been sent, but the employee has not confirmed receipt from the portal yet.';

        $sfReceiptBadge = 'Waiting Employee';
        $sfReceiptCardClass = 'sf-erp-confirm-card--pending';
    }

    if ($sfReceiptConfirmed) {
        $sfReceiptHeadline = 'Employee confirmed receipt';
        $sfReceiptText = 'The employee confirmed receiving this salary payment from the portal. This confirmation is linked to the ERP salary slip.';
        $sfReceiptBadge = 'Received';
        $sfReceiptCardClass = 'sf-erp-confirm-card--received';
    }

    if ($sfReceiptNotReceived) {
        $sfReceiptHeadline = 'Employee reported payment not received';
        $sfReceiptText = 'The employee reported from the portal that this bank salary payment was not received. Finance should review the transfer and process the next action.';
        $sfReceiptBadge = 'Not Received';
        $sfReceiptCardClass = 'sf-erp-confirm-card--danger';
    }

    $sfShowReceiptCard = $sfNeedsReceiptConfirmation || $sfReceiptConfirmed || $sfReceiptNotReceived;
@endphp

@if($sfShowReceiptCard)
    <section class="sf-erp-confirm-card {{ $sfReceiptCardClass }}">
        <div class="sf-erp-confirm-icon">
            @if($sfReceiptConfirmed)
                ✓
            @elseif($sfReceiptNotReceived)
                !
            @else
                ⏳
            @endif
        </div>

        <div class="sf-erp-confirm-main">
            <div class="sf-erp-confirm-kicker">{{ $sfReceiptTitle }}</div>
            <div class="sf-erp-confirm-title">{{ $sfReceiptHeadline }}</div>
            <div class="sf-erp-confirm-text">{{ $sfReceiptText }}</div>

            <div class="sf-erp-confirm-meta-grid">
                <div>
                    <strong>Payment Method</strong>
                    <span>{{ $sfReceiptMethodLabel }}</span>
                </div>

                <div>
                    <strong>Slip Status</strong>
                    <span>{{ strtoupper(str_replace('_', ' ', (string) $sfMainStatus)) }}</span>
                </div>

                <div>
                    <strong>Employee Confirmation</strong>
                    <span>{{ strtoupper(str_replace('_', ' ', (string) ($sfReceiptStatus ?: 'pending'))) }}</span>
                </div>

                <div>
                    <strong>Confirmed / Reported At</strong>
                    <span>{{ $sfReceiptConfirmedAt }}</span>
                </div>
            </div>

            @if($sfReceiptSlip?->employee_confirmation_notes)
                <div class="sf-erp-confirm-note">
                    <strong>Employee Note</strong>
                    <span>{{ $sfReceiptSlip->employee_confirmation_notes }}</span>
                </div>
            @endif
        </div>

        <div class="sf-erp-confirm-badge">
            {{ $sfReceiptBadge }}
        </div>
    </section>
@endif
{{-- SADA_RECEIPT_NOTICE_INSIDE_ROOT_END --}}

        <section class="sf-grid-3">
            <div class="sf-card sf-premium-mini-card">
                <div class="sf-premium-mini-icon" aria-hidden="true"><span class="material-symbols-rounded">badge</span></div>
                <div class="sf-section-kicker">Employee</div>
                <div class="sf-info-list" style="margin-top:16px;">
                    <div>
                        <div class="sf-label">Name</div>
                        <div class="sf-value">{{ $employeeName }}</div>
                    </div>
                    <div>
                        <div class="sf-label">Job Title</div>
                        <div class="sf-value">{{ $jobTitle }}</div>
                    </div>
                    <div>
                        <div class="sf-label">Employee Code</div>
                        <div class="sf-value">{{ $employeeCode }}</div>
                    </div>
                </div>
            </div>

            <div class="sf-card sf-premium-mini-card">
                <div class="sf-premium-mini-icon" aria-hidden="true"><span class="material-symbols-rounded">receipt_long</span></div>
                <div class="sf-section-kicker">Slip Details</div>
                <div class="sf-info-list" style="margin-top:16px;">
                    <div>
                        <div class="sf-label">Slip Number</div>
                        <div class="sf-value">{{ $this->getSlipNumber() }}</div>
                    </div>
                    <div>
                        <div class="sf-label">Issue Date</div>
                        <div class="sf-value">{{ $this->safeDate($record->issue_date ?? $record->created_at ?? null) }}</div>
                    </div>
                    <div>
                        <div class="sf-label">Payment Date</div>
                        <div class="sf-value">{{ $this->safeDate($record->paid_at ?? $record->payment_date ?? null) }}</div>
                    </div>
                </div>
            </div>

            <div class="sf-card sf-premium-mini-card">
                <div class="sf-premium-mini-icon" aria-hidden="true"><span class="material-symbols-rounded">payments</span></div>
                <div class="sf-section-kicker">Payment</div>
                <div class="sf-info-list" style="margin-top:16px;">
                    <div>
                        <div class="sf-label">Payment Route</div>
                        <div class="sf-value">{{ $this->getPaymentRouteLabel() }}</div>
                    </div>
                    <div>
                        <div class="sf-label">Treasury Account</div>
                        <div class="sf-value sf-payment-dynamic-lines">
                            @foreach($sfPaymentTreasuryLines as $line)
                                <span>{{ $line }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <div class="sf-label">Reference</div>
                        <div class="sf-value sf-payment-dynamic-lines">
                            @foreach($sfPaymentReferenceLines as $line)
                                <span>{{ $line }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
@php
    /*
     |--------------------------------------------------------------------------
     | SADA SAFE FALLBACK 2026-05-07: linked reimbursement variables
     |--------------------------------------------------------------------------
     | Prevents undefined variable errors and calculates reimbursements attached
     | to this salary slip directly from finance_expenses.
     */
    $sfLinkedReimbursements = $sfLinkedReimbursements ?? \App\Models\FinanceExpense::query()
        ->where('reimbursed_salary_slip_id', $record->id)
        ->get();

    $sfLinkedReimbursementTotal = $sfLinkedReimbursementTotal ?? (float) $sfLinkedReimbursements
        ->filter(fn ($item) => strtoupper((string) ($item->reimbursement_currency ?: $item->currency ?: $currency)) === strtoupper((string) $currency))
        ->sum(fn ($item) => (float) ($item->reimbursement_amount ?: $item->amount ?: 0));

    $sfLinkedReimbursementText = $sfLinkedReimbursementText ?? $sfLinkedReimbursements
        ->groupBy(fn ($item) => strtoupper((string) ($item->reimbursement_currency ?: $item->currency ?: $currency)))
        ->map(fn ($items, $cur) => number_format((float) $items->sum(fn ($item) => (float) ($item->reimbursement_amount ?: $item->amount ?: 0)), 2) . ' ' . $cur)
        ->values()
        ->implode(' + ');

    if (blank($sfLinkedReimbursementText)) {
        $sfLinkedReimbursementText = '0.00 ' . $currency;
    }

    $sfFinalNetIncludingSameCurrencyReimbursement = $sfFinalNetIncludingSameCurrencyReimbursement
        ?? round((float) $netAmount + (float) $sfLinkedReimbursementTotal, 2);
@endphp


{{-- SADA_SALARY_DYNAMIC_CURRENCY_TOTALS_START --}}
@php
    /*
     |--------------------------------------------------------------------------
     | Dynamic Currency Totals for Salary Slip Financial Overview
     |--------------------------------------------------------------------------
     | Salary net remains in salary slip currency.
     | Reimbursements are grouped by their own currency.
     | Final totals are displayed per currency without fake conversion.
     */
    $sfSalaryCurrency = strtoupper((string) ($currency ?: ($record->currency ?? 'EUR')));
    $sfSalaryNetAmount = (float) ($netAmount ?? $record->net_amount ?? 0);

    $sfLinkedReimbursements = \App\Models\FinanceExpense::query()
        ->where('reimbursed_salary_slip_id', $record->id)
        ->get();

    $sfCurrencyTotals = collect();

    $sfCurrencyTotals->push([
        'currency' => $sfSalaryCurrency,
        'amount' => $sfSalaryNetAmount,
        'label' => 'Salary Net',
        'type' => 'salary',
    ]);

    foreach ($sfLinkedReimbursements as $sfReimbExpense) {
        $sfReimbCurrency = strtoupper((string) (
            $sfReimbExpense->reimbursement_currency
            ?: $sfReimbExpense->currency
            ?: $sfSalaryCurrency
        ));

        $sfReimbAmount = (float) (
            $sfReimbExpense->reimbursement_amount
            ?: $sfReimbExpense->amount
            ?: 0
        );

        $sfCurrencyTotals->push([
            'currency' => $sfReimbCurrency,
            'amount' => $sfReimbAmount,
            'label' => 'Linked Reimbursement',
            'type' => 'reimbursement',
        ]);
    }

    $sfGroupedCurrencyTotals = $sfCurrencyTotals
        ->groupBy('currency')
        ->map(function ($rows, $cur) {
            return [
                'currency' => $cur,
                'amount' => (float) $rows->sum('amount'),
                'salary' => (float) $rows->where('type', 'salary')->sum('amount'),
                'reimbursement' => (float) $rows->where('type', 'reimbursement')->sum('amount'),
            ];
        })
        ->values();

    $sfLinkedReimbursementByCurrency = $sfCurrencyTotals
        ->where('type', 'reimbursement')
        ->groupBy('currency')
        ->map(function ($rows, $cur) {
            return [
                'currency' => $cur,
                'amount' => (float) $rows->sum('amount'),
            ];
        })
        ->values();

    $sfLinkedReimbursementText = $sfLinkedReimbursementByCurrency->isNotEmpty()
        ? $sfLinkedReimbursementByCurrency
            ->map(fn ($row) => number_format((float) $row['amount'], 2) . ' ' . $row['currency'])
            ->implode(' + ')
        : ('0.00 ' . $sfSalaryCurrency);

    $sfSameCurrencyReimbursementTotal = (float) $sfLinkedReimbursementByCurrency
        ->where('currency', $sfSalaryCurrency)
        ->sum('amount');

    $sfSameCurrencyReimbursementText = number_format($sfSameCurrencyReimbursementTotal, 2) . ' ' . $sfSalaryCurrency;

    $sfPayableIfSameCurrency = $sfSalaryNetAmount + $sfSameCurrencyReimbursementTotal;

    $sfDynamicFinalNetText = $sfGroupedCurrencyTotals
        ->map(fn ($row) => number_format((float) $row['amount'], 2) . ' ' . $row['currency'])
        ->implode(' / ');

    if (blank($sfDynamicFinalNetText)) {
        $sfDynamicFinalNetText = number_format($sfSalaryNetAmount, 2) . ' ' . $sfSalaryCurrency;
    }
@endphp
{{-- SADA_SALARY_DYNAMIC_CURRENCY_TOTALS_END --}}



@php
    /*
     | Admin Salary Slip Financial Overview
     | Same visual and logic concept as Portal Salary Slip.
     | Uses stored processed payment totals after finance enters exchange rates.
     */
    $sfAdminSalaryCurrency = strtoupper((string) ($record->currency ?: 'EUR'));
    $sfAdminSalaryNet = (float) ($record->net_amount ?? 0);

    $sfAdminSameCurrencyTotal = (float) ($record->reimbursement_same_currency_total ?? 0);
    $sfAdminConvertedTotal = (float) ($record->reimbursement_converted_total ?? 0);
    $sfAdminAllConvertedReimb = round($sfAdminSameCurrencyTotal + $sfAdminConvertedTotal, 2);

    $sfAdminPaymentTotal = (float) ($record->payment_total_amount ?: 0);
    if ($sfAdminPaymentTotal <= 0) {
        $sfAdminPaymentTotal = round($sfAdminSalaryNet + $sfAdminAllConvertedReimb, 2);
    }

    $sfAdminLinkedReimbursements = \App\Models\FinanceExpense::query()
        ->where('reimbursed_salary_slip_id', $record->id)
        ->get();

    $sfAdminReimbursementByCurrency = $sfAdminLinkedReimbursements
        ->groupBy(fn ($item) => strtoupper((string) ($item->reimbursement_currency ?: $item->currency ?: $sfAdminSalaryCurrency)))
        ->map(function ($items, $cur) {
            return [
                'currency' => strtoupper((string) $cur),
                'amount' => (float) $items->sum(fn ($item) => (float) ($item->reimbursement_amount ?: $item->amount ?: 0)),
                'items' => $items,
            ];
        })
        ->values();

    $sfAdminBreakdown = collect((array) ($record->reimbursement_breakdown ?? []));
@endphp

<section class="sf-card sf-admin-finance-portal-style">
    <div class="sf-section-head">
        <div>
            <div class="sf-section-kicker">Salary Calculation</div>
            <div class="sf-section-title">Financial Overview</div>
            <p style="margin-top:8px;color:var(--sf-muted);font-size:13px;font-weight:750;">
                Salary net, linked reimbursements, exchange-rate conversion, and final payable amount.
            </p>
        </div>

        <div class="sf-soft-box sf-admin-final-card">
            <div class="sf-label">Final Net Amount</div>
            <div class="sf-value">{{ number_format($sfAdminPaymentTotal, 2) }} {{ $sfAdminSalaryCurrency }}</div>
            <div style="margin-top:6px;color:var(--sf-muted);font-size:12px;font-weight:800;">
                Salary {{ number_format($sfAdminSalaryNet, 2) }} · Reimb. {{ number_format($sfAdminAllConvertedReimb, 2) }}
            </div>
        </div>
    </div>

    <div class="sf-admin-money-grid">
        <div class="sf-soft-box sf-admin-money-card">
            <div class="sf-label">Salary Net</div>
            <div class="sf-kpi-value">{{ number_format($sfAdminSalaryNet, 2) }} {{ $sfAdminSalaryCurrency }}</div>
            <p>Original salary slip net amount.</p>
        </div>

        <div class="sf-soft-box sf-admin-money-card">
            <div class="sf-label">Linked Reimbursement</div>
            <div class="sf-kpi-value">
                @if($sfAdminReimbursementByCurrency->count())
                    @foreach($sfAdminReimbursementByCurrency as $row)
                        {{ number_format((float) $row['amount'], 2) }} {{ $row['currency'] }}@if(!$loop->last)<br>+ @endif
                    @endforeach
                @else
                    0.00 {{ $sfAdminSalaryCurrency }}
                @endif
            </div>
            <p>Original reimbursement currencies.</p>
        </div>

        <div class="sf-soft-box sf-admin-money-card">
            <div class="sf-label">Converted Reimb.</div>
            <div class="sf-kpi-value">{{ number_format($sfAdminAllConvertedReimb, 2) }} {{ $sfAdminSalaryCurrency }}</div>
            <p>All reimbursements converted into payment currency.</p>
        </div>

        <div class="sf-soft-box sf-admin-money-card sf-admin-money-card-final">
            <div class="sf-label">Final Payable</div>
            <div class="sf-kpi-value">{{ number_format($sfAdminPaymentTotal, 2) }} {{ $sfAdminSalaryCurrency }}</div>
            <p>Salary net plus converted reimbursements.</p>
        </div>
    </div>

    <div class="sf-admin-breakdown-box">
        <div class="sf-section-kicker">Currency Breakdown</div>
        <p style="margin-top:6px;color:var(--sf-muted);font-size:13px;font-weight:750;">
            Original currencies are shown clearly. Converted totals appear in {{ $sfAdminSalaryCurrency }} after finance enters exchange rates.
        </p>

        <div class="sf-admin-breakdown-grid">
            <div class="sf-soft-box sf-admin-breakdown-card">
                <div class="sf-label">{{ $sfAdminSalaryCurrency }}</div>
                <div class="sf-value">{{ number_format($sfAdminSalaryNet, 2) }}</div>
                <p>Salary Net</p>
            </div>

            @foreach($sfAdminReimbursementByCurrency as $row)
                <div class="sf-soft-box sf-admin-breakdown-card">
                    <div class="sf-label">{{ $row['currency'] }}</div>
                    <div class="sf-value">{{ number_format((float) $row['amount'], 2) }}</div>
                    <p>Original reimbursement amount</p>
                </div>
            @endforeach

            @foreach($sfAdminBreakdown as $row)
                <div class="sf-soft-box sf-admin-breakdown-card">
                    <div class="sf-label">{{ $row['currency'] ?? '-' }} → {{ $row['payment_currency'] ?? $sfAdminSalaryCurrency }}</div>
                    <div class="sf-value">{{ number_format((float) ($row['converted_amount'] ?? 0), 2) }}</div>
                    <p>
                        Original {{ number_format((float) ($row['original_amount'] ?? 0), 2) }}
                        · Rate {{ $row['exchange_rate'] ?? 1 }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="sf-admin-breakdown-grid" style="margin-top:16px;">
        <div class="sf-soft-box sf-admin-breakdown-card">
            <div class="sf-label">Gross Amount</div>
            <div class="sf-value">{{ number_format((float) ($record->base_amount ?? 0), 2) }} {{ $sfAdminSalaryCurrency }}</div>
        </div>
        <div class="sf-soft-box sf-admin-breakdown-card">
            <div class="sf-label">Additions</div>
            <div class="sf-value">{{ number_format((float) ($record->adjustments_amount ?? 0), 2) }} {{ $sfAdminSalaryCurrency }}</div>
        </div>
        <div class="sf-soft-box sf-admin-breakdown-card">
            <div class="sf-label">Deductions</div>
            <div class="sf-value">{{ number_format((float) ($record->deductions_amount ?? 0), 2) }} {{ $sfAdminSalaryCurrency }}</div>
        </div>
    </div>
</section>



        <section class="sf-card">
            <div class="sf-section-head">
                <div>
                    <div class="sf-section-kicker">Attendance Summary</div>
                    <div class="sf-section-title">Compact Totals Only</div>
                </div>
                <div class="sf-premium-block-icon" aria-hidden="true">
                    <span class="material-symbols-rounded">calendar_month</span>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;">
                    <div class="sf-soft-box" style="text-align:right;">
                        <div class="sf-label">Worked Days Total</div>
                        <div class="sf-big-number sf-accent">{{ $summary['worked_days_total'] }}</div>
                    </div>

                    <div class="sf-soft-box sf-attendance-alert-box" style="text-align:right;">
                        <div class="sf-label">Not Worked / Unpaid</div>
                        <div class="sf-big-number">{{ $sfNotWorkedDays }}</div>
                    </div>
                </div>
            </div>

            <div class="sf-attendance-grid">
                @foreach ([
                    'present' => 'Present',
                    'absent' => 'Absent',
                    'sick' => 'Sick',
                    'leave' => 'Leave',
                    'unpaid_leave' => 'Unpaid Leave',
                    'holiday' => 'Holiday',
                    'travel' => 'Travel',
                    'other' => 'Other',
                ] as $key => $label)
                    <div class="sf-soft-box">
                        <div class="sf-label">{{ $label }}</div>
                        <div class="sf-big-number">{{ $summary[$key] ?? 0 }}</div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="sf-card">
            <div class="sf-section-head">
                <div>
                    <div class="sf-section-kicker">Attachments</div>
                    <div class="sf-section-title">Timesheet / Attendance / Schedule / Supporting Files</div>
                </div>
                <div class="sf-premium-block-icon" aria-hidden="true">
                    <span class="material-symbols-rounded">attach_file</span>
                </div>
                <button class="sf-btn" wire:click="mountAction('upload_attachment')" type="button">Upload Attachment</button>
            </div>

            @if ($attachments->count())
                <div style="overflow:hidden; border-radius:22px; border:1px solid var(--sf-border);">
                    <table class="sf-attachments-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>File</th>
                                <th>Uploaded</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attachments as $attachment)
                                @php
                                    $path = $attachment->file_path ?? $attachment->path ?? $attachment->attachment ?? null;
                                    $fileUrl = $path ? asset('storage/' . ltrim($path, '/')) : '#';
                                @endphp
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $attachment->type ?? $attachment->attachment_type ?? 'Supporting File')) }}</td>
                                    <td style="font-weight:800;">{{ $attachment->file_name ?? $attachment->name ?? basename((string) $path) ?: 'Attachment' }}</td>
                                    <td>{{ $this->safeDate($attachment->created_at ?? null) }}</td>
                                    <td style="text-align:right;">
                                        @if ($path)
                                            <a href="{{ $fileUrl }}" target="_blank" style="font-weight:900; color:#2563eb;">Open</a>
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
                <div class="sf-empty">
                    <strong style="color:var(--sf-text);">No attachments uploaded yet.</strong>
                    <div style="margin-top:6px;">Upload timesheet, attendance sheet, day schedule, or supporting files from the ERP.</div>
                </div>
            @endif
        </section>
    </div>
    <x-filament-actions::modals />

<style id="sf-final-salary-hero-actions-and-reimbursement">
    .sf-salary-hero-actions {
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 10px;
        position: relative;
        z-index: 20;
    }

    .sf-salary-hero-btn,
    a.sf-salary-hero-btn,
    button.sf-salary-hero-btn {
        height: 44px !important;
        min-height: 44px !important;
        padding: 0 17px !important;
        border: 1px solid rgba(255,255,255,.16) !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        font-size: 13px !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        cursor: pointer !important;
        box-shadow: 0 12px 24px rgba(0,0,0,.18), inset 0 1px 0 rgba(255,255,255,.18) !important;
        white-space: nowrap !important;
    }

    .sf-salary-hero-btn strong {
        color: inherit !important;
        font-size: 13px !important;
        font-weight: 950 !important;
    }

    .sf-salary-hero-btn span,
    .sf-salary-hero-btn svg {
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        min-height: 16px !important;
        fill: currentColor !important;
        color: inherit !important;
    }

    .sf-salary-hero-btn-yellow {
        background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
        color: #07111f !important;
    }

    .sf-salary-hero-btn-yellow *,
    .sf-salary-hero-btn-yellow svg {
        color: #07111f !important;
        fill: #07111f !important;
    }

    .sf-salary-hero-btn-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: #fff !important;
    }

    .sf-salary-hero-btn-green {
        background: linear-gradient(135deg, #22c55e, #16a34a) !important;
        color: #fff !important;
    }

    .sf-salary-hero-btn-gray {
        background: linear-gradient(135deg, rgba(71,85,105,.94), rgba(51,65,85,.92)) !important;
        color: #fff !important;
    }

    .sf-linked-reimbursement-strip {
        margin: 18px 0 20px 0 !important;
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 14px !important;
        width: calc(100% - 250px) !important;
        max-width: 100% !important;
    }

    .sf-linked-reimbursement-strip > div {
        min-height: 98px !important;
        border-radius: 22px !important;
        padding: 18px 20px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .07), transparent 38%),
            rgba(248,250,252,.94) !important;
        border: 1px solid rgba(15,23,42,.08) !important;
        box-shadow: 0 14px 30px rgba(15,23,42,.055) !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
    }

    .sf-linked-reimbursement-strip span {
        display: block !important;
        margin-bottom: 10px !important;
        color: #64748b !important;
        font-size: 11px !important;
        line-height: 1.25 !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
        white-space: normal !important;
    }

    .sf-linked-reimbursement-strip strong {
        display: block !important;
        color: #0f172a !important;
        font-size: clamp(20px, 1.7vw, 30px) !important;
        line-height: 1.05 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        white-space: nowrap !important;
    }

    @media (max-width: 1200px) {
        .sf-linked-reimbursement-strip {
            width: 100% !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 900px) {
        .sf-linked-reimbursement-strip {
            grid-template-columns: 1fr !important;
        }

        .sf-linked-reimbursement-strip strong {
            white-space: normal !important;
        }
    }
</style>


<style>
/* SADA FINAL PATCH: Salary Slip Financial Overview layout */
.sf-slip-page .sf-section-head {
    align-items: flex-start !important;
}

.sf-slip-page .sf-finance-mini-grid {
    margin-top: 22px !important;
    display: grid !important;
    grid-template-columns: repeat(3, minmax(210px, 1fr)) !important;
    gap: 16px !important;
    max-width: 980px !important;
}

.sf-slip-page .sf-finance-mini-card {
    min-height: 128px !important;
    border-radius: 26px !important;
    padding: 24px 26px !important;
    background:
        radial-gradient(circle at top right, rgba(76, 167, 168, .10), transparent 34%),
        linear-gradient(180deg, rgba(248, 250, 252, .96), rgba(255, 255, 255, .98)) !important;
    border: 1px solid rgba(15, 23, 42, .08) !important;
    box-shadow: 0 18px 46px rgba(15, 23, 42, .055) !important;
    overflow: visible !important;
}

.sf-slip-page .sf-finance-mini-card span {
    display: block !important;
    margin-bottom: 16px !important;
    color: #64748b !important;
    font-size: 12px !important;
    line-height: 1.35 !important;
    font-weight: 950 !important;
    letter-spacing: .22em !important;
    text-transform: uppercase !important;
    white-space: normal !important;
}

.sf-slip-page .sf-finance-mini-card strong {
    display: block !important;
    color: #0f172a !important;
    font-size: clamp(25px, 2.4vw, 38px) !important;
    line-height: 1.05 !important;
    font-weight: 950 !important;
    letter-spacing: -.045em !important;
    white-space: nowrap !important;
}

.sf-slip-page .sf-section-head > .sf-soft-box {
    min-width: 230px !important;
    border-radius: 26px !important;
    padding: 26px 28px !important;
}

.sf-slip-page .sf-section-head > .sf-soft-box .sf-value {
    font-size: clamp(22px, 2vw, 32px) !important;
    line-height: 1.1 !important;
    white-space: nowrap !important;
}

@media (max-width: 1180px) {
    .sf-slip-page .sf-section-head {
        flex-direction: column !important;
    }

    .sf-slip-page .sf-section-head > .sf-soft-box {
        width: 100% !important;
        text-align: left !important;
    }

    .sf-slip-page .sf-finance-mini-grid {
        max-width: 100% !important;
        grid-template-columns: 1fr !important;
    }
}

.dark .sf-slip-page .sf-finance-mini-card {
    background:
        radial-gradient(circle at top right, rgba(76, 167, 168, .16), transparent 34%),
        linear-gradient(180deg, rgba(17, 24, 39, .96), rgba(15, 23, 42, .98)) !important;
    border-color: rgba(255, 255, 255, .10) !important;
}

.dark .sf-slip-page .sf-finance-mini-card span {
    color: #94a3b8 !important;
}

.dark .sf-slip-page .sf-finance-mini-card strong {
    color: #f8fafc !important;
}
</style>


<style>
/* SADA FINAL OVERRIDE 2026-05-07: compact salary financial overview */
.sf-slip-page .sf-card:has(.sf-finance-mini-grid) {
    overflow: hidden !important;
}

.sf-slip-page .sf-card:has(.sf-finance-mini-grid) .sf-section-head {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) 260px !important;
    gap: 28px !important;
    align-items: start !important;
}

.sf-slip-page .sf-finance-mini-grid,
.sf-slip-page .sf-linked-reimbursement-strip {
    width: 100% !important;
    max-width: 920px !important;
    margin-top: 20px !important;
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 14px !important;
}

.sf-slip-page .sf-finance-mini-card,
.sf-slip-page .sf-linked-reimbursement-strip > div {
    min-width: 0 !important;
    width: 100% !important;
    min-height: 112px !important;
    max-height: none !important;
    border-radius: 24px !important;
    padding: 22px 24px !important;
    background:
        radial-gradient(circle at top right, rgba(76, 167, 168, .10), transparent 36%),
        linear-gradient(180deg, rgba(248, 250, 252, .98), rgba(255, 255, 255, .99)) !important;
    border: 1px solid rgba(15, 23, 42, .08) !important;
    box-shadow: 0 14px 34px rgba(15, 23, 42, .045) !important;
    overflow: hidden !important;
}

.sf-slip-page .sf-finance-mini-card span,
.sf-slip-page .sf-linked-reimbursement-strip > div span {
    display: block !important;
    margin-bottom: 14px !important;
    color: #64748b !important;
    font-size: 11px !important;
    line-height: 1.45 !important;
    font-weight: 950 !important;
    letter-spacing: .18em !important;
    text-transform: uppercase !important;
    white-space: normal !important;
    word-break: normal !important;
}

.sf-slip-page .sf-finance-mini-card strong,
.sf-slip-page .sf-linked-reimbursement-strip > div strong {
    display: block !important;
    color: #0f172a !important;
    font-size: clamp(22px, 2vw, 34px) !important;
    line-height: 1.05 !important;
    font-weight: 950 !important;
    letter-spacing: -.045em !important;
    white-space: nowrap !important;
}

.sf-slip-page .sf-card:has(.sf-finance-mini-grid) .sf-section-head > .sf-soft-box,
.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head > .sf-soft-box {
    width: 260px !important;
    min-width: 260px !important;
    max-width: 260px !important;
    border-radius: 26px !important;
    padding: 26px 24px !important;
    text-align: center !important;
}

.sf-slip-page .sf-card:has(.sf-finance-mini-grid) .sf-section-head > .sf-soft-box .sf-label,
.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head > .sf-soft-box .sf-label {
    font-size: 14px !important;
    font-weight: 850 !important;
    letter-spacing: .02em !important;
    text-transform: none !important;
}

.sf-slip-page .sf-card:has(.sf-finance-mini-grid) .sf-section-head > .sf-soft-box .sf-value,
.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head > .sf-soft-box .sf-value {
    font-size: 26px !important;
    line-height: 1.15 !important;
    white-space: nowrap !important;
}

.sf-slip-page .sf-money-grid {
    margin-top: 6px !important;
}

@media (max-width: 1280px) {
    .sf-slip-page .sf-card:has(.sf-finance-mini-grid) .sf-section-head,
    .sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head {
        grid-template-columns: 1fr !important;
    }

    .sf-slip-page .sf-card:has(.sf-finance-mini-grid) .sf-section-head > .sf-soft-box,
    .sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head > .sf-soft-box {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        text-align: left !important;
    }
}

@media (max-width: 900px) {
    .sf-slip-page .sf-finance-mini-grid,
    .sf-slip-page .sf-linked-reimbursement-strip {
        grid-template-columns: 1fr !important;
    }

    .sf-slip-page .sf-finance-mini-card strong,
    .sf-slip-page .sf-linked-reimbursement-strip > div strong {
        font-size: 28px !important;
    }
}

.dark .sf-slip-page .sf-finance-mini-card,
.dark .sf-slip-page .sf-linked-reimbursement-strip > div {
    background:
        radial-gradient(circle at top right, rgba(76, 167, 168, .16), transparent 36%),
        linear-gradient(180deg, rgba(17, 24, 39, .96), rgba(15, 23, 42, .98)) !important;
    border-color: rgba(255, 255, 255, .10) !important;
}

.dark .sf-slip-page .sf-finance-mini-card span,
.dark .sf-slip-page .sf-linked-reimbursement-strip > div span {
    color: #94a3b8 !important;
}

.dark .sf-slip-page .sf-finance-mini-card strong,
.dark .sf-slip-page .sf-linked-reimbursement-strip > div strong {
    color: #f8fafc !important;
}
</style>



<style>
/* SADA HOTFIX 2026-05-07: stop salary financial cards overflow */
.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) {
    overflow: hidden !important;
}

.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) minmax(220px, 300px) !important;
    align-items: start !important;
    gap: 24px !important;
    width: 100% !important;
    overflow: hidden !important;
}

.sf-slip-page .sf-linked-reimbursement-strip {
    width: 100% !important;
    max-width: 100% !important;
    display: grid !important;
    grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    gap: 16px !important;
    overflow: hidden !important;
}

.sf-slip-page .sf-linked-reimbursement-strip > div {
    min-width: 0 !important;
    max-width: 100% !important;
    overflow: hidden !important;
    padding: 22px 24px !important;
    border-radius: 26px !important;
}

.sf-slip-page .sf-linked-reimbursement-strip > div span {
    display: block !important;
    max-width: 100% !important;
    font-size: 10px !important;
    line-height: 1.55 !important;
    letter-spacing: .18em !important;
    white-space: normal !important;
}

.sf-slip-page .sf-linked-reimbursement-strip > div strong {
    display: block !important;
    max-width: 100% !important;
    margin-top: 12px !important;
    font-size: clamp(24px, 2vw, 34px) !important;
    line-height: 1.04 !important;
    letter-spacing: -.045em !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: clip !important;
}

.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-soft-box {
    min-width: 0 !important;
}

@media (max-width: 1550px) {
    .sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head {
        grid-template-columns: minmax(0, 1fr) 260px !important;
    }

    .sf-slip-page .sf-linked-reimbursement-strip > div {
        padding: 20px 20px !important;
    }

    .sf-slip-page .sf-linked-reimbursement-strip > div strong {
        font-size: clamp(20px, 1.65vw, 28px) !important;
    }
}

@media (max-width: 1180px) {
    .sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip) .sf-section-head {
        grid-template-columns: 1fr !important;
    }

    .sf-slip-page .sf-linked-reimbursement-strip {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }

    .sf-slip-page .sf-linked-reimbursement-strip > div strong {
        font-size: 24px !important;
    }
}

@media (max-width: 760px) {
    .sf-slip-page .sf-linked-reimbursement-strip {
        grid-template-columns: 1fr !important;
    }

    .sf-slip-page .sf-linked-reimbursement-strip > div strong {
        font-size: 30px !important;
    }
}
</style>


<style>
/* SADA FINAL 2026-05-07: Salary Slip Financial Overview aligned premium layout */
.sf-salary-finance-card {
    overflow: hidden !important;
}

.sf-salary-finance-head {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) minmax(220px, 280px) !important;
    gap: 18px !important;
    align-items: start !important;
}

.sf-section-subtitle {
    margin-top: 8px;
    color: var(--sf-muted);
    font-size: 13px;
    line-height: 1.55;
    font-weight: 700;
}

.sf-final-net-card {
    border-radius: 24px;
    padding: 20px 22px;
    background: var(--sf-soft);
    border: 1px solid var(--sf-border);
    text-align: right;
    min-width: 0;
}

.sf-final-net-card .sf-value {
    font-size: clamp(22px, 2vw, 30px) !important;
    line-height: 1.05 !important;
    white-space: nowrap !important;
}

.sf-salary-money-top-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 14px !important;
    margin-top: 18px !important;
    margin-bottom: 18px !important;
    width: 100% !important;
}

.sf-salary-money-card {
    min-width: 0 !important;
    border-radius: 24px;
    padding: 22px 20px;
    background:
        radial-gradient(circle at 100% 0%, rgba(14, 165, 233, .10), transparent 36%),
        var(--sf-soft);
    border: 1px solid var(--sf-border);
    box-shadow: 0 14px 34px rgba(15, 23, 42, .045);
}

.sf-salary-money-card .sf-label {
    letter-spacing: .13em;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 950;
    line-height: 1.35;
    min-height: 30px;
}

.sf-money-big {
    margin-top: 12px;
    color: var(--sf-text);
    font-size: clamp(22px, 2.1vw, 34px);
    font-weight: 950;
    line-height: 1.05;
    letter-spacing: -.035em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: clip;
}

.sf-salary-money-card-reimbursement {
    background:
        radial-gradient(circle at 100% 0%, rgba(245, 158, 11, .13), transparent 36%),
        var(--sf-soft);
}

.sf-salary-money-card-final {
    background:
        radial-gradient(circle at 100% 0%, rgba(16, 185, 129, .14), transparent 36%),
        var(--sf-soft);
}

.sf-salary-money-bottom-grid {
    margin-top: 10px !important;
}

.sf-salary-money-bottom-grid .sf-soft-box {
    min-width: 0 !important;
}

.sf-salary-money-bottom-grid .sf-value {
    white-space: nowrap !important;
}

.dark .sf-salary-money-card,
.dark .sf-final-net-card {
    background: rgba(255, 255, 255, .055);
    border-color: rgba(255, 255, 255, .10);
}

@media (max-width: 1280px) {
    .sf-salary-money-top-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
}

@media (max-width: 900px) {
    .sf-salary-finance-head {
        grid-template-columns: 1fr !important;
    }

    .sf-final-net-card {
        text-align: left;
    }

    .sf-salary-money-top-grid {
        grid-template-columns: 1fr !important;
    }

    .sf-money-big {
        white-space: normal;
    }
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



<style>
/* SADA SALARY EMPLOYMENT STYLE FINANCE CSS 2026-05-07 */
.sf-salary-finance-employment-style {
    position: relative;
    overflow: hidden;
    border-radius: 30px !important;
    padding: 28px !important;
    background:
        radial-gradient(circle at 92% 10%, rgba(76, 167, 168, .18), transparent 27%),
        linear-gradient(135deg, rgba(255,255,255,.99) 0%, rgba(248,250,252,.98) 54%, rgba(236,253,245,.68) 100%) !important;
    border: 1px solid rgba(15, 23, 42, .075) !important;
    box-shadow: 0 18px 48px rgba(15, 23, 42, .075) !important;
}

.sf-salary-finance-employment-style::before {
    content: "";
    position: absolute;
    top: 0;
    left: 28px;
    right: 28px;
    height: 4px;
    border-radius: 999px;
    background: linear-gradient(90deg, #22d3ee 0%, #2563eb 100%);
}

.sf-salary-finance-head {
    display: flex !important;
    justify-content: space-between !important;
    align-items: stretch !important;
    gap: 22px !important;
    margin-bottom: 22px !important;
}

.sf-section-subtitle {
    margin-top: 8px;
    color: var(--sf-muted);
    font-size: 13px;
    line-height: 1.55;
    font-weight: 750;
    max-width: 720px;
}

.sf-salary-final-total-card {
    min-width: 330px;
    max-width: 420px;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 14px;
    align-items: start;
    padding: 18px;
    border-radius: 26px;
    background: rgba(248, 250, 252, .88);
    border: 1px solid rgba(15, 23, 42, .08);
    box-shadow: 0 14px 34px rgba(15, 23, 42, .055);
}

.sf-salary-final-total-card > .material-symbols-rounded {
    width: 48px;
    height: 48px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    color: #0f766e;
    background: rgba(76, 167, 168, .13);
    font-size: 25px;
}

.sf-final-currency-line {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(15, 23, 42, .07);
}

.sf-final-currency-line:first-of-type {
    border-top: 0;
    padding-top: 0;
}

.sf-final-currency-line strong {
    display: block;
    color: var(--sf-text);
    font-size: 21px;
    line-height: 1.05;
    font-weight: 950;
    letter-spacing: -.035em;
    white-space: nowrap;
}

.sf-final-currency-line small {
    display: block;
    margin-top: 4px;
    color: var(--sf-muted);
    font-size: 11px;
    font-weight: 800;
}

.sf-salary-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
}

.sf-salary-kpi-card {
    position: relative;
    overflow: hidden;
    min-height: 162px;
    border-radius: 26px;
    padding: 22px;
    background:
        radial-gradient(circle at 92% 12%, rgba(76, 167, 168, .15), transparent 25%),
        linear-gradient(135deg, rgba(255,255,255,.98), rgba(248,250,252,.95)) !important;
    border: 1px solid rgba(15, 23, 42, .075);
    box-shadow: 0 14px 34px rgba(15, 23, 42, .055);
}

.sf-salary-kpi-card::before {
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 4px;
    background: linear-gradient(90deg, #22d3ee, #2563eb);
}

.sf-salary-kpi-card-final::before {
    background: linear-gradient(90deg, #10b981, #0f766e);
}

.sf-kpi-icon {
    position: absolute;
    top: 18px;
    right: 18px;
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    color: #0f766e;
    background: rgba(224, 242, 254, .72);
}

.sf-kpi-icon .material-symbols-rounded {
    font-size: 22px;
}

.sf-salary-kpi-card .sf-label {
    display: block;
    max-width: calc(100% - 52px);
    min-height: 34px;
    color: #64748b;
    font-size: 11px;
    line-height: 1.35;
    font-weight: 950;
    letter-spacing: .18em;
    text-transform: uppercase;
}

.sf-kpi-value {
    margin-top: 16px;
    color: #0f172a;
    font-size: clamp(22px, 2vw, 34px);
    line-height: 1.05;
    font-weight: 950;
    letter-spacing: -.045em;
    overflow-wrap: anywhere;
}

.sf-salary-kpi-card p {
    margin: 10px 0 0;
    color: #64748b;
    font-size: 12px;
    line-height: 1.45;
    font-weight: 750;
}

.sf-salary-currency-breakdown {
    margin-top: 16px;
    padding: 16px;
    border-radius: 24px;
    background: rgba(236, 253, 245, .55);
    border: 1px solid rgba(15, 118, 110, .14);
}

.sf-currency-breakdown-head {
    display: grid;
    grid-template-columns: auto minmax(0, 1fr);
    gap: 12px;
    align-items: start;
}

.sf-currency-breakdown-head > .material-symbols-rounded {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 16px;
    background: rgba(15, 118, 110, .10);
    color: #0f766e;
}

.sf-currency-breakdown-head strong {
    display: block;
    color: #0f172a;
    font-size: 14px;
    font-weight: 950;
}

.sf-currency-breakdown-head p {
    margin: 4px 0 0;
    color: #475569;
    font-size: 12px;
    line-height: 1.5;
    font-weight: 750;
}

.sf-currency-row-grid {
    margin-top: 14px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.sf-currency-row {
    border-radius: 18px;
    padding: 13px 14px;
    background: rgba(255,255,255,.72);
    border: 1px solid rgba(15, 23, 42, .07);
}

.sf-currency-row span {
    display: block;
    color: #0f766e;
    font-size: 11px;
    font-weight: 950;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.sf-currency-row strong {
    display: block;
    margin-top: 5px;
    color: #0f172a;
    font-size: 20px;
    font-weight: 950;
    letter-spacing: -.03em;
}

.sf-currency-row small {
    display: block;
    margin-top: 4px;
    color: #64748b;
    font-size: 11px;
    font-weight: 750;
}

.sf-salary-money-bottom-grid {
    margin-top: 16px;
}

.sf-salary-money-bottom-grid .sf-soft-box {
    position: relative;
    overflow: hidden;
    border-radius: 22px !important;
    padding: 18px !important;
    background:
        radial-gradient(circle at 95% 15%, rgba(76, 167, 168, .10), transparent 24%),
        rgba(248, 250, 252, .92) !important;
    border: 1px solid rgba(15, 23, 42, .075) !important;
}

.dark .sf-salary-finance-employment-style,
.dark .sf-salary-kpi-card,
.dark .sf-salary-final-total-card,
.dark .sf-salary-money-bottom-grid .sf-soft-box {
    background:
        radial-gradient(circle at 92% 10%, rgba(76, 167, 168, .16), transparent 27%),
        linear-gradient(135deg, rgba(17,24,39,.98), rgba(15,23,42,.94)) !important;
    border-color: rgba(255,255,255,.10) !important;
}

.dark .sf-kpi-value,
.dark .sf-final-currency-line strong,
.dark .sf-currency-breakdown-head strong,
.dark .sf-currency-row strong {
    color: #f8fafc;
}

.dark .sf-salary-currency-breakdown {
    background: rgba(20, 184, 166, .08);
    border-color: rgba(45, 212, 191, .18);
}

.dark .sf-currency-row {
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.10);
}

.dark .sf-salary-kpi-card p,
.dark .sf-currency-breakdown-head p,
.dark .sf-currency-row small,
.dark .sf-final-currency-line small {
    color: rgba(248,250,252,.68);
}

@media (max-width: 1280px) {
    .sf-salary-kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sf-salary-finance-head {
        flex-direction: column;
    }

    .sf-salary-final-total-card {
        min-width: 0;
        max-width: none;
        width: 100%;
    }

    .sf-currency-row-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 720px) {
    .sf-salary-kpi-grid,
    .sf-currency-row-grid {
        grid-template-columns: 1fr;
    }

    .sf-final-currency-line strong {
        white-space: normal;
    }
}
</style>



<style>
/* SADA SALARY GLOBAL PREMIUM BLOCK STYLE 2026-05-07 */

/* Main large blocks: same visual language as Employment / Finance Expense */
.sf-slip-page > .sf-card,
.sf-salary-finance-employment-style {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 32px !important;
    padding: 28px !important;
    background:
        radial-gradient(circle at 93% 12%, rgba(76, 167, 168, .18), transparent 28%),
        radial-gradient(circle at 10% 120%, rgba(37, 99, 235, .08), transparent 34%),
        linear-gradient(135deg, rgba(255,255,255,.99) 0%, rgba(248,250,252,.97) 52%, rgba(236,253,245,.64) 100%) !important;
    border: 1px solid rgba(15, 23, 42, .075) !important;
    box-shadow: 0 18px 52px rgba(15, 23, 42, .075) !important;
}

.sf-slip-page > .sf-card::before,
.sf-salary-finance-employment-style::before {
    content: "";
    position: absolute;
    top: 0;
    left: 28px;
    right: 28px;
    height: 4px;
    border-radius: 999px;
    background: linear-gradient(90deg, #22d3ee 0%, #2563eb 100%);
    z-index: 1;
}

/* Keep content above the background effects */
.sf-slip-page > .sf-card > *,
.sf-salary-finance-employment-style > * {
    position: relative;
    z-index: 2;
}

/* Section header alignment */
.sf-slip-page .sf-section-head,
.sf-slip-page .sf-salary-finance-head {
    display: grid !important;
    grid-template-columns: minmax(0, 1fr) auto !important;
    align-items: start !important;
    gap: 18px !important;
    margin-bottom: 22px !important;
}

/* Big block icon circle */
.sf-premium-block-icon {
    width: 58px;
    height: 58px;
    border-radius: 22px;
    display: grid;
    place-items: center;
    background: rgba(224, 242, 254, .78);
    color: #0f766e;
    border: 1px solid rgba(15, 118, 110, .08);
    box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
}

.sf-premium-block-icon .material-symbols-rounded {
    font-size: 28px;
}

/* Keep the final net card on the same head row */
.sf-salary-finance-head .sf-salary-final-total-card {
    grid-column: 2;
    grid-row: 1 / span 2;
}

.sf-salary-finance-head > div:first-child {
    grid-column: 1;
}

/* Financial final card refinement */
.sf-salary-final-total-card {
    min-width: 330px !important;
    max-width: 430px !important;
    border-radius: 26px !important;
    background:
        radial-gradient(circle at 92% 14%, rgba(76, 167, 168, .13), transparent 30%),
        rgba(248, 250, 252, .90) !important;
    border: 1px solid rgba(15, 23, 42, .08) !important;
}

/* Mini top cards: Employee / Slip Details / Payment */
.sf-grid-3 > .sf-card,
.sf-premium-mini-card {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 30px !important;
    padding: 28px !important;
    background:
        radial-gradient(circle at 94% 12%, rgba(76, 167, 168, .13), transparent 30%),
        linear-gradient(135deg, rgba(255,255,255,.99), rgba(248,250,252,.95)) !important;
    border: 1px solid rgba(15, 23, 42, .075) !important;
    box-shadow: 0 16px 42px rgba(15, 23, 42, .065) !important;
}

.sf-grid-3 > .sf-card::before,
.sf-premium-mini-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 24px;
    right: 24px;
    height: 4px;
    border-radius: 999px;
    background: linear-gradient(90deg, #22d3ee, #2563eb);
}

.sf-premium-mini-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    background: rgba(224, 242, 254, .78);
    color: #0f766e;
    border: 1px solid rgba(15, 118, 110, .08);
}

.sf-premium-mini-icon .material-symbols-rounded {
    font-size: 24px;
}

/* Better spacing so icon doesn't collide with title */
.sf-premium-mini-card .sf-section-kicker {
    padding-right: 56px;
}

/* Small inner KPI cards */
.sf-soft-box,
.sf-salary-kpi-card,
.sf-currency-row {
    border-radius: 24px !important;
    background:
        radial-gradient(circle at 95% 14%, rgba(76, 167, 168, .10), transparent 28%),
        rgba(248, 250, 252, .93) !important;
    border: 1px solid rgba(15, 23, 42, .075) !important;
    box-shadow: 0 10px 28px rgba(15, 23, 42, .045) !important;
}

/* Attendance cards should match financial cards */
.sf-attendance-grid .sf-soft-box {
    min-height: 112px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Typography consistency */
.sf-slip-page .sf-section-kicker,
.sf-slip-page .sf-label {
    color: #64748b;
    font-weight: 950 !important;
    letter-spacing: .16em !important;
    text-transform: uppercase !important;
}

.sf-slip-page .sf-section-title {
    color: #0f172a;
    font-weight: 950 !important;
    letter-spacing: -.04em !important;
}

.sf-slip-page .sf-value,
.sf-slip-page .sf-big-number,
.sf-slip-page .sf-kpi-value {
    color: #0f172a;
    font-weight: 950 !important;
    letter-spacing: -.035em !important;
}

/* Attachments block table/cards should not look flat */
.sf-slip-page a.sf-btn,
.sf-slip-page .sf-pill {
    border-radius: 999px !important;
    font-weight: 900 !important;
}

/* Dark mode */
.dark .sf-slip-page > .sf-card,
.dark .sf-salary-finance-employment-style,
.dark .sf-grid-3 > .sf-card,
.dark .sf-premium-mini-card {
    background:
        radial-gradient(circle at 93% 12%, rgba(76, 167, 168, .16), transparent 28%),
        radial-gradient(circle at 10% 120%, rgba(37, 99, 235, .10), transparent 34%),
        linear-gradient(135deg, rgba(17,24,39,.98), rgba(15,23,42,.94)) !important;
    border-color: rgba(255,255,255,.10) !important;
}

.dark .sf-soft-box,
.dark .sf-salary-kpi-card,
.dark .sf-currency-row,
.dark .sf-salary-final-total-card {
    background:
        radial-gradient(circle at 95% 14%, rgba(76, 167, 168, .10), transparent 28%),
        rgba(255,255,255,.06) !important;
    border-color: rgba(255,255,255,.10) !important;
}

.dark .sf-premium-block-icon,
.dark .sf-premium-mini-icon {
    background: rgba(45, 212, 191, .12);
    border-color: rgba(45, 212, 191, .18);
    color: #5eead4;
}

.dark .sf-slip-page .sf-section-title,
.dark .sf-slip-page .sf-value,
.dark .sf-slip-page .sf-big-number,
.dark .sf-slip-page .sf-kpi-value {
    color: #f8fafc !important;
}

.dark .sf-slip-page .sf-section-kicker,
.dark .sf-slip-page .sf-label {
    color: rgba(248,250,252,.68) !important;
}

/* Responsive */
@media (max-width: 1180px) {
    .sf-slip-page .sf-section-head,
    .sf-slip-page .sf-salary-finance-head {
        grid-template-columns: minmax(0, 1fr) !important;
    }

    .sf-salary-finance-head .sf-salary-final-total-card {
        grid-column: 1;
        grid-row: auto;
        min-width: 0 !important;
        max-width: none !important;
        width: 100%;
    }

    .sf-premium-block-icon {
        position: absolute;
        top: 24px;
        right: 24px;
    }

    .sf-slip-page .sf-section-head > div:first-child {
        padding-right: 74px;
    }
}

@media (max-width: 720px) {
    .sf-slip-page > .sf-card,
    .sf-salary-finance-employment-style {
        padding: 22px !important;
        border-radius: 26px !important;
    }

    .sf-premium-block-icon,
    .sf-premium-mini-icon {
        width: 46px;
        height: 46px;
        border-radius: 18px;
    }
}
</style>



<style>
/* SADA SALARY BLUE STYLE + HERO LABEL FIX 2026-05-07 */

/* Header hero stat cards: force white labels/text again */
.sf-slip-page .sf-hero-stat .sf-label,
.sf-slip-page .sf-hero-stat .sf-value,
.sf-slip-page .sf-hero-stat small,
.sf-slip-page .sf-hero-stat span,
.sf-slip-page .sf-hero-stat div {
    color: rgba(255, 255, 255, .92) !important;
}

.sf-slip-page .sf-hero-stat .sf-value,
.sf-slip-page .sf-hero-stat strong {
    color: #ffffff !important;
}

.sf-slip-page .sf-hero-stat {
    background: rgba(255, 255, 255, .13) !important;
    border-color: rgba(255, 255, 255, .18) !important;
}

/* Replace green/teal premium accents with Employment-style blue */
.sf-slip-page > .sf-card::before,
.sf-salary-finance-employment-style::before,
.sf-grid-3 > .sf-card::before,
.sf-premium-mini-card::before {
    background: linear-gradient(90deg, #22d3ee 0%, #2563eb 58%, #1d4ed8 100%) !important;
}

/* Large cards: blue glow, not green */
.sf-slip-page > .sf-card,
.sf-salary-finance-employment-style {
    background:
        radial-gradient(circle at 93% 12%, rgba(37, 99, 235, .13), transparent 28%),
        radial-gradient(circle at 10% 120%, rgba(14, 165, 233, .09), transparent 34%),
        linear-gradient(135deg, rgba(255,255,255,.99) 0%, rgba(248,250,252,.97) 54%, rgba(239,246,255,.70) 100%) !important;
}

/* Small cards: blue glow */
.sf-grid-3 > .sf-card,
.sf-premium-mini-card {
    background:
        radial-gradient(circle at 94% 12%, rgba(37, 99, 235, .11), transparent 30%),
        linear-gradient(135deg, rgba(255,255,255,.99), rgba(248,250,252,.95)) !important;
}

/* Icon circles blue */
.sf-premium-block-icon,
.sf-premium-mini-icon {
    background: rgba(219, 234, 254, .88) !important;
    color: #1d4ed8 !important;
    border-color: rgba(37, 99, 235, .14) !important;
}

/* Inner KPI cards blue glow */
.sf-soft-box,
.sf-salary-kpi-card,
.sf-currency-row,
.sf-salary-final-total-card {
    background:
        radial-gradient(circle at 95% 14%, rgba(37, 99, 235, .08), transparent 28%),
        rgba(248, 250, 252, .94) !important;
}

/* Dynamic payment lines inside Payment block */
.sf-payment-dynamic-lines {
    display: flex !important;
    flex-direction: column !important;
    gap: 4px !important;
}

.sf-payment-dynamic-lines span {
    display: block !important;
    color: inherit !important;
    font-weight: 950 !important;
    line-height: 1.25 !important;
}

/* Dark mode blue accents */
.dark .sf-slip-page > .sf-card,
.dark .sf-salary-finance-employment-style,
.dark .sf-grid-3 > .sf-card,
.dark .sf-premium-mini-card {
    background:
        radial-gradient(circle at 93% 12%, rgba(37, 99, 235, .18), transparent 28%),
        radial-gradient(circle at 10% 120%, rgba(14, 165, 233, .12), transparent 34%),
        linear-gradient(135deg, rgba(17,24,39,.98), rgba(15,23,42,.94)) !important;
}

.dark .sf-premium-block-icon,
.dark .sf-premium-mini-icon {
    background: rgba(37, 99, 235, .18) !important;
    border-color: rgba(96, 165, 250, .22) !important;
    color: #93c5fd !important;
}

.dark .sf-soft-box,
.dark .sf-salary-kpi-card,
.dark .sf-currency-row,
.dark .sf-salary-final-total-card {
    background:
        radial-gradient(circle at 95% 14%, rgba(37, 99, 235, .12), transparent 28%),
        rgba(255,255,255,.06) !important;
}
</style>


<style id="sf-salary-employment-exact-blue-palette-20260507">
/*
|--------------------------------------------------------------------------
| SADA Salary Slip - Employment exact blue palette
| Matches Employment view colors:
| #22d3ee, #2563eb, #234b74, #1d4ed8, #e0f2fe
|--------------------------------------------------------------------------
*/

/* Hero top KPI labels back to white */
.sf-slip-page .sf-hero-stat .sf-label,
.sf-slip-page .sf-hero-stat .sf-section-kicker,
.sf-slip-page .sf-hero-stat div:first-child,
.sf-slip-page .sf-hero-grid .sf-label {
    color: rgba(255, 255, 255, .86) !important;
}

.sf-slip-page .sf-hero-stat .sf-value,
.sf-slip-page .sf-hero-grid .sf-value {
    color: #ffffff !important;
}

/* Main big section cards: same Employment background feeling */
.sf-slip-page > .sf-card,
.sf-slip-page .sf-card:has(.sf-finance-mini-grid),
.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip),
.sf-slip-page .sf-card:has(.sf-attendance-grid),
.sf-slip-page .sf-card:has(.sf-attachments-list),
.sf-slip-page .sf-premium-mini-card {
    border-radius: 30px !important;
    border: 1px solid rgba(15, 23, 42, .08) !important;
    box-shadow: 0 16px 42px rgba(15, 23, 42, .07) !important;
    background:
        radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
        rgba(255, 255, 255, .94) !important;
}

/* Top accent line: exact Employment finance card line */
.sf-slip-page > .sf-card::before,
.sf-slip-page .sf-card:has(.sf-finance-mini-grid)::before,
.sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip)::before,
.sf-slip-page .sf-card:has(.sf-attendance-grid)::before,
.sf-slip-page .sf-card:has(.sf-attachments-list)::before,
.sf-slip-page .sf-grid-3 > .sf-card::before,
.sf-slip-page .sf-premium-mini-card::before {
    content: "" !important;
    display: block !important;
    height: 5px !important;
    border-radius: 999px !important;
    margin-bottom: 18px !important;
    background: linear-gradient(90deg, #22d3ee, #2563eb) !important;
}

/* Titles and large values: same Employment dark blue */
.sf-slip-page .sf-section-title,
.sf-slip-page .sf-block-title,
.sf-slip-page .sf-finance-title,
.sf-slip-page .sf-premium-mini-card .sf-section-title {
    color: #234b74 !important;
    letter-spacing: -.04em !important;
}

.sf-slip-page .sf-big-number,
.sf-slip-page .sf-money-big,
.sf-slip-page .sf-money-big *,
.sf-slip-page .sf-finance-number,
.sf-slip-page .sf-value.sf-accent {
    color: #234b74 !important;
}

/* Right top icons: same blue language */
.sf-slip-page .sf-premium-block-icon,
.sf-slip-page .sf-premium-mini-icon,
.sf-slip-page .sf-card .sf-premium-block-icon,
.sf-slip-page .sf-card .sf-premium-mini-icon {
    background: #e0f2fe !important;
    color: #1d4ed8 !important;
    border: 1px solid rgba(59, 130, 246, .22) !important;
    box-shadow: none !important;
}

.sf-slip-page .sf-premium-block-icon svg,
.sf-slip-page .sf-premium-mini-icon svg,
.sf-slip-page .sf-premium-block-icon .material-symbols-rounded,
.sf-slip-page .sf-premium-mini-icon .material-symbols-rounded {
    color: #1d4ed8 !important;
    fill: #1d4ed8 !important;
}

/* Inner soft boxes: Employment clean blue soft cards */
.sf-slip-page .sf-soft-box,
.sf-slip-page .sf-money-card,
.sf-slip-page .sf-salary-money-bottom-grid .sf-soft-box,
.sf-slip-page .sf-attendance-grid .sf-soft-box,
.sf-slip-page .sf-finance-mini-grid .sf-soft-box,
.sf-slip-page .sf-linked-reimbursement-strip > div {
    border: 1px solid rgba(15, 23, 42, .08) !important;
    background:
        radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 35%),
        rgba(255, 255, 255, .94) !important;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .045) !important;
}

/* Keep danger / unpaid cards red only where needed */
.sf-slip-page .sf-attendance-alert-box {
    background: linear-gradient(135deg, #fff1f2, #fff7f8) !important;
    border-color: #fecdd3 !important;
}

.sf-slip-page .sf-attendance-alert-box .sf-label,
.sf-slip-page .sf-attendance-alert-box .sf-value {
    color: #be123c !important;
}

/* Currency breakdown stays blue, not green */
.sf-slip-page .sf-currency-breakdown,
.sf-slip-page .sf-currency-breakdown-card,
.sf-slip-page .sf-money-breakdown,
.sf-slip-page .sf-money-breakdown-card {
    background:
        radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 35%),
        rgba(255, 255, 255, .94) !important;
    border-color: rgba(15, 23, 42, .08) !important;
}

.sf-slip-page .sf-currency-breakdown .sf-premium-block-icon,
.sf-slip-page .sf-money-breakdown .sf-premium-block-icon {
    background: #e0f2fe !important;
    color: #1d4ed8 !important;
}

/* Dark mode mirrors Employment dark card treatment */
.dark .sf-slip-page > .sf-card,
.dark .sf-slip-page .sf-card:has(.sf-finance-mini-grid),
.dark .sf-slip-page .sf-card:has(.sf-linked-reimbursement-strip),
.dark .sf-slip-page .sf-card:has(.sf-attendance-grid),
.dark .sf-slip-page .sf-card:has(.sf-attachments-list),
.dark .sf-slip-page .sf-premium-mini-card {
    background:
        radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
        rgba(15, 23, 42, .72) !important;
    border-color: rgba(148, 163, 184, .18) !important;
    box-shadow: 0 18px 46px rgba(0, 0, 0, .18) !important;
}

.dark .sf-slip-page .sf-section-title,
.dark .sf-slip-page .sf-block-title,
.dark .sf-slip-page .sf-finance-title,
.dark .sf-slip-page .sf-big-number,
.dark .sf-slip-page .sf-money-big,
.dark .sf-slip-page .sf-value.sf-accent {
    color: #ffffff !important;
}

.dark .sf-slip-page .sf-soft-box,
.dark .sf-slip-page .sf-money-card,
.dark .sf-slip-page .sf-salary-money-bottom-grid .sf-soft-box,
.dark .sf-slip-page .sf-attendance-grid .sf-soft-box,
.dark .sf-slip-page .sf-finance-mini-grid .sf-soft-box,
.dark .sf-slip-page .sf-linked-reimbursement-strip > div {
    background:
        radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 35%),
        rgba(15, 23, 42, .72) !important;
    border-color: rgba(148, 163, 184, .18) !important;
}

/* Make section icons consistent size and placement */
.sf-slip-page .sf-premium-block-icon,
.sf-slip-page .sf-premium-mini-icon {
    width: 48px !important;
    height: 48px !important;
    min-width: 48px !important;
    border-radius: 999px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
</style>

<style id="sf-attendance-button-icon-fix">
.sf-salary-hero-btn .material-symbols-rounded{
    font-size:20px!important;
    line-height:1!important;
    display:inline-flex!important;
    align-items:center!important;
}
</style>


<script id="sf-salary-fx-live-preview-final">
(function () {
    function parseNum(value) {
        value = String(value || '').trim().replace(',', '.').replace(/[^0-9.\-]/g, '');
        var number = parseFloat(value);
        return isNaN(number) ? 0 : number;
    }

    function refreshFxPreview(scope) {
        scope = scope || document;

        var modal = scope.querySelector('[role="dialog"]') || document;
        var inputs = modal.querySelectorAll('input');

        inputs.forEach(function (input) {
            var label = '';
            var wrapper = input.closest('[data-field-wrapper], .fi-fo-field-wrp, .fi-field, div');
            if (wrapper) label = wrapper.innerText || '';

            if (!/Exchange Rate To Payment Currency/i.test(label)) return;

            var row = input.closest('[data-repeater-item], .fi-fo-repeater-item, fieldset, section, .grid') || input.parentElement;
            if (!row) return;

            var allInputs = Array.from(row.querySelectorAll('input'));
            var amountInput = allInputs.find(function (el) {
                var txt = (el.closest('[data-field-wrapper], .fi-fo-field-wrp, .fi-field, div') || {}).innerText || '';
                return /Original Amount/i.test(txt);
            });

            var previewInput = allInputs.find(function (el) {
                var txt = (el.closest('[data-field-wrapper], .fi-fo-field-wrp, .fi-field, div') || {}).innerText || '';
                return /Converted Preview/i.test(txt);
            });

            if (!amountInput || !previewInput) return;

            var amount = parseNum(amountInput.value);
            var rate = parseNum(input.value);
            var converted = amount > 0 && rate > 0 ? (amount * rate).toFixed(2) : 'Enter rate';

            previewInput.value = converted;
            previewInput.dispatchEvent(new Event('input', { bubbles: true }));
            previewInput.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    document.addEventListener('input', function (event) {
        refreshFxPreview(document);
    }, true);

    document.addEventListener('change', function (event) {
        refreshFxPreview(document);
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        refreshFxPreview(document);
    });

    document.addEventListener('livewire:navigated', function () {
        refreshFxPreview(document);
    });

    setInterval(function () {
        refreshFxPreview(document);
    }, 800);
})();
</script>

<style id="sf-md3-attendance-popup-final">
    .sf-md3-attendance-modal .fi-modal-window {
        border-radius: 34px !important;
        overflow: hidden !important;
    }

    .sf-md3-attendance-modal .fi-modal-content {
        max-height: 74vh !important;
        overflow-y: auto !important;
        padding-right: 10px !important;
    }

    .sf-md3-attendance-repeater-scroll .fi-fo-repeater-items {
        max-height: 520px !important;
        overflow-y: auto !important;
        padding-right: 8px !important;
        display: grid !important;
        gap: 12px !important;
    }

    .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item,
    .sf-md3-travel-days-repeater .fi-fo-repeater-item {
        border-radius: 24px !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .09), transparent 32%),
            rgba(255, 255, 255, .96) !important;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .055) !important;
        overflow: hidden !important;
    }

    .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item-content,
    .sf-md3-travel-days-repeater .fi-fo-repeater-item-content {
        padding: 16px !important;
    }

    .sf-md3-travel-days-repeater {
        margin-top: 20px !important;
        padding-top: 18px !important;
        border-top: 1px solid rgba(15, 23, 42, .08) !important;
    }

    .sf-md3-travel-days-repeater .fi-fo-repeater-add-action {
        border-radius: 999px !important;
        background: linear-gradient(135deg, #22c55e, #0f766e) !important;
        color: #ffffff !important;
        font-weight: 900 !important;
        box-shadow: 0 14px 32px rgba(15, 118, 110, .18) !important;
    }

    .dark .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item,
    .dark .sf-md3-travel-days-repeater .fi-fo-repeater-item {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 34%),
            rgba(15, 23, 42, .92) !important;
        border-color: rgba(255, 255, 255, .10) !important;
    }
</style>

<style id="sf-md3-attendance-popup-hard-fix">
    /*
     * HARD FIX — Attendance modal layout.
     * Keeps the modal scrollable, but stops Filament repeater rows from overlapping.
     */

    .sf-md3-attendance-modal .fi-modal-window {
        width: min(1180px, calc(100vw - 48px)) !important;
        max-width: min(1180px, calc(100vw - 48px)) !important;
        border-radius: 32px !important;
        overflow: hidden !important;
    }

    .sf-md3-attendance-modal .fi-modal-content {
        max-height: 76vh !important;
        overflow-y: auto !important;
        padding: 22px 26px 28px !important;
    }

    .sf-md3-attendance-modal .fi-modal-heading {
        font-size: 22px !important;
        font-weight: 900 !important;
        letter-spacing: -.02em !important;
        color: #0f172a !important;
    }

    .sf-md3-attendance-modal .fi-modal-description {
        font-size: 14px !important;
        color: #64748b !important;
        font-weight: 650 !important;
    }

    /*
     * Normal Attendance Days list
     */
    .sf-md3-attendance-repeater-scroll {
        display: block !important;
        width: 100% !important;
    }

    .sf-md3-attendance-repeater-scroll .fi-fo-repeater-items {
        max-height: 460px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 10px 10px 10px 2px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
    }

    .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item {
        position: relative !important;
        display: block !important;
        min-height: auto !important;
        height: auto !important;
        overflow: visible !important;
        border-radius: 22px !important;
        border: 1px solid rgba(15, 23, 42, .08) !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .08), transparent 34%),
            rgba(255, 255, 255, .98) !important;
        box-shadow: 0 10px 26px rgba(15, 23, 42, .055) !important;
    }

    .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item-content {
        display: block !important;
        padding: 14px !important;
        overflow: visible !important;
    }

    .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item-content > .grid {
        display: grid !important;
        grid-template-columns: 1.05fr 1.05fr .75fr 1.15fr 1.45fr !important;
        gap: 14px !important;
        align-items: end !important;
    }

    .sf-md3-attendance-modal .fi-fo-field-wrp {
        min-width: 0 !important;
        margin: 0 !important;
    }

    .sf-md3-attendance-modal .fi-fo-field-wrp-label {
        margin-bottom: 6px !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        color: #334155 !important;
    }

    .sf-md3-attendance-modal input,
    .sf-md3-attendance-modal select,
    .sf-md3-attendance-modal textarea,
    .sf-md3-attendance-modal .fi-input-wrp,
    .sf-md3-attendance-modal .fi-select-input {
        min-height: 42px !important;
        border-radius: 14px !important;
    }

    .sf-md3-attendance-modal input[disabled] {
        opacity: 1 !important;
        background: #f8fafc !important;
        color: #475569 !important;
        -webkit-text-fill-color: #475569 !important;
    }

    /*
     * Additional Travel Days section
     */
    .sf-md3-travel-days-repeater {
        margin-top: 22px !important;
        padding-top: 18px !important;
        border-top: 1px solid rgba(15, 23, 42, .10) !important;
    }

    .sf-md3-travel-days-repeater .fi-fo-repeater-items {
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
    }

    .sf-md3-travel-days-repeater .fi-fo-repeater-item {
        border-radius: 22px !important;
        border: 1px solid rgba(20, 184, 166, .22) !important;
        background:
            radial-gradient(circle at top right, rgba(20, 184, 166, .10), transparent 34%),
            rgba(240, 253, 250, .94) !important;
        box-shadow: 0 12px 28px rgba(15, 118, 110, .075) !important;
        overflow: visible !important;
    }

    .sf-md3-travel-days-repeater .fi-fo-repeater-item-content {
        padding: 14px !important;
    }

    .sf-md3-travel-days-repeater .fi-fo-repeater-item-content > .grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr .85fr 1.35fr !important;
        gap: 14px !important;
        align-items: end !important;
    }

    .sf-md3-travel-days-repeater .fi-fo-repeater-add-action,
    .sf-md3-travel-days-repeater button[type="button"] {
        border-radius: 999px !important;
        font-weight: 900 !important;
    }

    /*
     * Modal footer
     */
    .sf-md3-attendance-modal .fi-modal-footer {
        padding: 18px 26px !important;
        border-top: 1px solid rgba(15, 23, 42, .08) !important;
        background: rgba(255, 255, 255, .92) !important;
    }

    .sf-md3-attendance-modal .fi-modal-footer-actions {
        justify-content: flex-start !important;
        gap: 12px !important;
    }

    .sf-md3-attendance-modal .fi-modal-footer-actions button {
        min-height: 46px !important;
        border-radius: 999px !important;
        padding-inline: 22px !important;
        font-weight: 950 !important;
    }

    .dark .sf-md3-attendance-modal .fi-modal-heading {
        color: #f8fafc !important;
    }

    .dark .sf-md3-attendance-modal .fi-modal-description {
        color: #cbd5e1 !important;
    }

    .dark .sf-md3-attendance-modal .fi-modal-footer {
        background: rgba(15, 23, 42, .94) !important;
        border-top-color: rgba(255, 255, 255, .10) !important;
    }

    .dark .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item {
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .12), transparent 34%),
            rgba(15, 23, 42, .96) !important;
        border-color: rgba(255, 255, 255, .10) !important;
    }

    .dark .sf-md3-travel-days-repeater .fi-fo-repeater-item {
        background:
            radial-gradient(circle at top right, rgba(20, 184, 166, .16), transparent 34%),
            rgba(15, 23, 42, .96) !important;
        border-color: rgba(45, 212, 191, .18) !important;
    }

    @media (max-width: 900px) {
        .sf-md3-attendance-repeater-scroll .fi-fo-repeater-item-content > .grid,
        .sf-md3-travel-days-repeater .fi-fo-repeater-item-content > .grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>


<style id="sf-admin-financial-overview-portal-copy-final">
    .sf-admin-finance-portal-style {
        background:
            radial-gradient(circle at 92% 8%, rgba(34,211,238,.13), transparent 30%),
            var(--sf-card) !important;
    }

    .sf-admin-final-card {
        min-width: 280px;
        border-radius: 26px !important;
        background: rgba(248,250,252,.92) !important;
    }

    .dark .sf-admin-final-card {
        background: rgba(15,23,42,.72) !important;
    }

    .sf-admin-final-card .sf-value {
        margin-top: 8px !important;
        color: var(--sf-text) !important;
        font-size: 29px !important;
        font-weight: 950 !important;
        line-height: 1.05 !important;
        letter-spacing: -.045em !important;
    }

    .sf-admin-money-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .sf-admin-money-card {
        position: relative;
        overflow: hidden;
        border-radius: 26px !important;
        padding: 20px !important;
        background: linear-gradient(135deg, rgba(255,255,255,.98), rgba(248,250,252,.92)) !important;
        box-shadow: 0 12px 34px rgba(15,23,42,.045) !important;
    }

    .dark .sf-admin-money-card {
        background: linear-gradient(135deg, rgba(15,23,42,.96), rgba(30,41,59,.92)) !important;
    }

    .sf-admin-money-card::before {
        content: "";
        position: absolute;
        inset: 0 0 auto 0;
        height: 4px;
        background: linear-gradient(90deg, #22d3ee, #2563eb);
    }

    .sf-admin-money-card-final::before {
        background: linear-gradient(90deg, #10b981, #0f766e);
    }

    .sf-admin-money-card .sf-kpi-value {
        margin-top: 14px;
        color: var(--sf-text);
        font-size: 28px;
        font-weight: 950;
        line-height: 1.08;
        letter-spacing: -.045em;
        overflow-wrap: anywhere;
    }

    .sf-admin-money-card p,
    .sf-admin-breakdown-card p {
        margin: 10px 0 0;
        color: var(--sf-muted);
        font-size: 12px;
        font-weight: 750;
        line-height: 1.45;
    }

    .sf-admin-breakdown-box {
        margin-top: 16px;
        border-radius: 24px;
        padding: 16px;
        background: rgba(236,253,245,.55);
        border: 1px solid rgba(15,118,110,.14);
    }

    .dark .sf-admin-breakdown-box {
        background: rgba(6,78,59,.18);
        border-color: rgba(45,212,191,.15);
    }

    .sf-admin-breakdown-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }

    .sf-admin-breakdown-card {
        border-radius: 18px !important;
        padding: 14px !important;
        background: rgba(255,255,255,.75) !important;
    }

    .dark .sf-admin-breakdown-card {
        background: rgba(15,23,42,.72) !important;
    }

    .sf-admin-breakdown-card .sf-value {
        margin-top: 6px;
        color: var(--sf-text);
        font-size: 20px;
        font-weight: 950;
        overflow-wrap: anywhere;
    }

    @media (max-width: 1200px) {
        .sf-admin-money-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sf-admin-breakdown-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .sf-admin-money-grid,
        .sf-admin-breakdown-grid {
            grid-template-columns: 1fr;
        }

        .sf-admin-final-card {
            min-width: 0;
            width: 100%;
        }
    }
</style>

<style id="sf-salary-download-white-button-final">

    .sf-salary-hero-btn-white {
        background: rgba(255,255,255,.92) !important;
        color: #234b74 !important;
        border: 1px solid rgba(255,255,255,.70) !important;
        box-shadow: 0 14px 30px rgba(15,23,42,.12) !important;
    }

    .sf-salary-hero-btn-white *,
    .sf-salary-hero-btn-white svg,
    .sf-salary-hero-btn-white span,
    .sf-salary-hero-btn-white strong {
        color: #234b74 !important;
    }

    .dark .sf-salary-hero-btn-white {
        background: rgba(15,23,42,.72) !important;
        border-color: rgba(148,163,184,.22) !important;
        color: #e0f2fe !important;
    }

    .dark .sf-salary-hero-btn-white *,
    .dark .sf-salary-hero-btn-white svg,
    .dark .sf-salary-hero-btn-white span,
    .dark .sf-salary-hero-btn-white strong {
        color: #e0f2fe !important;
    }

</style>


<style id="sf-salary-adjustment-purple-button-final">
    .sf-salary-hero-btn-purple {
        background: linear-gradient(135deg, #7c3aed, #a855f7) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255,255,255,.24) !important;
        box-shadow: 0 16px 34px rgba(124,58,237,.28) !important;
    }

    .sf-salary-hero-btn-purple *,
    .sf-salary-hero-btn-purple svg,
    .sf-salary-hero-btn-purple span,
    .sf-salary-hero-btn-purple strong {
        color: #ffffff !important;
    }
</style>

