<x-filament-panels::page>
    @php
        $salaryStatusLabels = $statusLabels ?? \App\Models\SalarySlip::statusLabels();

        $statusBadgeClass = function (?string $status) {
            return match ((string) $status) {
                \App\Models\SalarySlip::STATUS_DRAFT => 'sf-ss-status--draft',
                \App\Models\SalarySlip::STATUS_APPROVED => 'sf-ss-status--approved',
                \App\Models\SalarySlip::STATUS_SENT_TO_BANK => 'sf-ss-status--bank',
                \App\Models\SalarySlip::STATUS_PAID => 'sf-ss-status--paid',
                \App\Models\SalarySlip::STATUS_BANK_REJECTED => 'sf-ss-status--rejected',
                default => 'sf-ss-status--draft',
            };
        };

        $cardClassByStatus = function (?string $status) {
            return match ((string) $status) {
                \App\Models\SalarySlip::STATUS_PAID => 'sf-ss-card--paid',
                \App\Models\SalarySlip::STATUS_SENT_TO_BANK => 'sf-ss-card--bank',
                \App\Models\SalarySlip::STATUS_BANK_REJECTED => 'sf-ss-card--rejected',
                \App\Models\SalarySlip::STATUS_APPROVED => 'sf-ss-card--approved',
                default => 'sf-ss-card--draft',
            };
        };
    @endphp

    <style>
        .fi-header { display: none !important; }
        .sf-ss-wrap { display: flex; flex-direction: column; gap: 24px; }

        .sf-btn-primary,.sf-btn-secondary {
            display:inline-flex;align-items:center;justify-content:center;min-height:48px;padding:0 18px;border-radius:999px;
            text-decoration:none !important;font-size:14px;font-weight:900;transition:all .18s ease;cursor:pointer;border:none;
        }

        .sf-btn-primary {
            background:#f2b705;color:#3b2a00 !important;box-shadow:0 10px 20px rgba(242,183,5,.22);
        }

        .sf-btn-secondary {
            background:rgba(255,255,255,.12);color:#fff !important;border:1px solid rgba(255,255,255,.14);
        }

        .sf-ss-hero {
            display:flex;justify-content:space-between;align-items:flex-start;gap:20px;border:1px solid #d7e2e5;border-radius:22px;
            padding:26px 28px;background:linear-gradient(135deg,#18344d 0%,#234d6f 50%,#2f6f73 100%);
            box-shadow:0 18px 34px rgba(15,23,42,.10);position:relative;overflow:hidden;
        }

        .sf-ss-hero::after {
            content:"";position:absolute;inset:auto 0 0 0;height:4px;background:linear-gradient(90deg,#4ca7a8,#b38b2f);
        }

        .sf-ss-hero-left,.sf-ss-hero-right { position:relative;z-index:1; }
        .sf-ss-hero-right { flex-shrink:0;display:flex;gap:10px;flex-wrap:wrap; }
        .sf-ss-hero-kicker { font-size:14px;color:rgba(255,255,255,.78); }
        .sf-ss-hero-title { margin-top:8px;font-size:56px;line-height:.95;font-weight:900;color:#fff; }
        .sf-ss-hero-sub { margin-top:16px;max-width:920px;font-size:15px;line-height:1.7;color:rgba(255,255,255,.84); }

        .sf-ss-summary {
            margin-top:18px;display:inline-flex;align-items:center;padding:10px 14px;border-radius:999px;background:rgba(255,255,255,.12);
        }

        .sf-ss-filters {
            background:linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%);
            border:1px solid #d7e2e5;border-radius:22px;padding:20px;box-shadow:0 10px 24px rgba(15,23,42,.04);
        }

        .sf-ss-filter-grid { display:grid;grid-template-columns:2fr repeat(4,1fr);gap:12px; }

        .sf-ss-filter-field input,.sf-ss-filter-field select {
            width:100%;min-height:46px;border-radius:14px;border:1px solid #d7e2e5;background:#fff;padding:0 14px;color:#0f172a;
        }

        .sf-ss-section {
            background:linear-gradient(180deg,#ffffff 0%,#f4f8fa 100%);
            border:1px solid #d7e2e5;border-radius:22px;padding:24px;box-shadow:0 10px 24px rgba(15,23,42,.04);
        }

        .sf-ss-section-kicker { font-size:11px;font-weight:900;letter-spacing:.18em;text-transform:uppercase;color:#1f4664; }
        .sf-ss-section-title { margin-top:8px;font-size:28px;line-height:1.1;font-weight:900;color:#0f172a; }
        .sf-ss-section-sub { margin-top:8px;font-size:15px;line-height:1.7;color:#667085; }
        .sf-ss-client-block,.sf-ss-project-block { margin-top:22px; }
        .sf-ss-client-title { font-size:22px;font-weight:900;color:#0f172a; }
        .sf-ss-project-title { font-size:16px;font-weight:800;color:#1f4664;margin-top:12px; }

        .sf-ss-grid {
            display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-top:14px;
        }

        .sf-ss-card,.sf-ss-slip-card {
            display:block;text-decoration:none !important;color:inherit !important;border:1px solid #d7e2e5;
            border-radius:18px;padding:16px;box-shadow:0 8px 18px rgba(15,23,42,.04);transition:all .18s ease;position:relative;overflow:hidden;
        }

        .sf-ss-card::before,.sf-ss-slip-card::before {
            content:"";position:absolute;left:0;top:0;bottom:0;width:5px;
        }

        .sf-ss-card:hover,.sf-ss-slip-card:hover {
            transform:translateY(-2px);box-shadow:0 14px 24px rgba(15,23,42,.08);
        }

        /* Default / Draft */
        .sf-ss-card--draft {
            background:rgba(255,255,255,.96);
            border-color:#d7e2e5;
        }
        .sf-ss-card--draft::before {
            background:linear-gradient(180deg,#1f4664,#4ca7a8);
        }

        /* Approved */
        .sf-ss-card--approved {
            background:linear-gradient(180deg,#f8fbff 0%, #eef6ff 100%);
            border-color:#bfdbfe;
            box-shadow:0 10px 22px rgba(59,130,246,.08);
        }
        .sf-ss-card--approved::before {
            background:linear-gradient(180deg,#2563eb,#60a5fa);
        }

        /* Sent to bank */
        .sf-ss-card--bank {
            background:linear-gradient(180deg,#fffaf0 0%, #fff1db 100%);
            border-color:#fdba74;
            box-shadow:0 10px 22px rgba(245,158,11,.10);
        }
        .sf-ss-card--bank::before {
            background:linear-gradient(180deg,#f59e0b,#fb923c);
        }

        /* Paid / Finished */
        .sf-ss-card--paid {
            background:linear-gradient(180deg,#f3fff7 0%, #dcfce7 100%);
            border-color:#86efac;
            box-shadow:0 12px 24px rgba(16,185,129,.12);
        }
        .sf-ss-card--paid::before {
            background:linear-gradient(180deg,#10b981,#22c55e);
        }

        /* Rejected */
        .sf-ss-card--rejected {
            background:linear-gradient(180deg,#fff6f7 0%, #ffe4e6 100%);
            border-color:#fda4af;
            box-shadow:0 10px 22px rgba(239,68,68,.10);
        }
        .sf-ss-card--rejected::before {
            background:linear-gradient(180deg,#ef4444,#f43f5e);
        }

        .sf-ss-top { display:flex;justify-content:space-between;gap:10px;align-items:flex-start; }

        .sf-ss-status {
            display:inline-flex;align-items:center;padding:7px 10px;border-radius:999px;font-size:11px;font-weight:900;letter-spacing:.14em;
            text-transform:uppercase;border:1px solid transparent;
        }

        .sf-ss-status--draft { background:#eef2f7;color:#475569;border-color:#cbd5e1; }
        .sf-ss-status--approved { background:#eff6ff;color:#1d4ed8;border-color:#93c5fd; }
        .sf-ss-status--bank { background:#fff7ed;color:#c2410c;border-color:#fdba74; }
        .sf-ss-status--paid { background:#ecfdf5;color:#047857;border-color:#86efac; }
        .sf-ss-status--rejected { background:#fff1f2;color:#be123c;border-color:#fda4af; }

        .sf-ss-period { font-size:12px;font-weight:900;letter-spacing:.16em;text-transform:uppercase;color:#667085; }
        .sf-ss-name { margin-top:12px;font-size:19px;line-height:1.15;font-weight:900;color:#0f172a; }
        .sf-ss-subname { margin-top:6px;font-size:13px;line-height:1.5;color:#667085; }
        .sf-ss-net { margin-top:14px;font-size:28px;line-height:1;font-weight:900;color:#0f172a; }

        .sf-ss-meta-grid {
            display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:14px;
        }

        .sf-ss-meta {
            border:1px solid #e4ecef;background:#fff;border-radius:14px;padding:10px;
        }

        .sf-ss-meta-label { font-size:10px;font-weight:900;letter-spacing:.16em;text-transform:uppercase;color:#607085; }
        .sf-ss-meta-value { margin-top:6px;font-size:13px;font-weight:800;color:#0f172a;line-height:1.45; }

        .sf-ss-status-grid {
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:10px;
            margin-top:14px;
        }

        .sf-ss-status-box {
            border-radius:14px;
            padding:10px 12px;
            border:1px solid #e4ecef;
            background:#fff;
        }

        .sf-ss-status-box-label {
            font-size:10px;
            font-weight:900;
            letter-spacing:.14em;
            text-transform:uppercase;
            color:#607085;
        }

        .sf-ss-status-box-value {
            margin-top:6px;
            font-size:18px;
            font-weight:900;
            line-height:1;
            color:#0f172a;
        }

        .sf-ss-status-box--draft { background:#f8fafc; border-color:#cbd5e1; }
        .sf-ss-status-box--approved { background:#eff6ff; border-color:#bfdbfe; }
        .sf-ss-status-box--bank { background:#fff7ed; border-color:#fdba74; }
        .sf-ss-status-box--paid { background:#ecfdf5; border-color:#86efac; }
        .sf-ss-status-box--rejected { background:#fff1f2; border-color:#fda4af; }

        .sf-ss-status-box--draft .sf-ss-status-box-label,
        .sf-ss-status-box--draft .sf-ss-status-box-value { color:#475569; }

        .sf-ss-status-box--approved .sf-ss-status-box-label,
        .sf-ss-status-box--approved .sf-ss-status-box-value { color:#1d4ed8; }

        .sf-ss-status-box--bank .sf-ss-status-box-label,
        .sf-ss-status-box--bank .sf-ss-status-box-value { color:#c2410c; }

        .sf-ss-status-box--paid .sf-ss-status-box-label,
        .sf-ss-status-box--paid .sf-ss-status-box-value { color:#047857; }

        .sf-ss-status-box--rejected .sf-ss-status-box-label,
        .sf-ss-status-box--rejected .sf-ss-status-box-value { color:#be123c; }

        .sf-ss-open {
            margin-top:14px;font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase;color:#4ca7a8;
        }

        .sf-ss-empty {
            margin-top:18px;padding:16px 18px;border-radius:16px;border:1px dashed #c8d3de;background:rgba(255,255,255,.85);color:#667085;
        }

        .dark .sf-ss-filters,.dark .sf-ss-section {
            background:linear-gradient(180deg,rgba(11,22,38,.96) 0%,rgba(10,27,45,.95) 100%);
            border-color:rgba(76,167,168,.14);box-shadow:0 10px 24px rgba(0,0,0,.18);
        }

        .dark .sf-ss-filter-field input,.dark .sf-ss-filter-field select,
        .dark .sf-ss-card,.dark .sf-ss-slip-card,.dark .sf-ss-meta,.dark .sf-ss-status-box {
            color:#f6fbff;
        }

        .dark .sf-ss-card--draft,
        .dark .sf-ss-slip-card.sf-ss-card--draft {
            background:rgba(12,23,38,.96);border-color:rgba(76,167,168,.14);
        }

        .dark .sf-ss-card--approved,
        .dark .sf-ss-slip-card.sf-ss-card--approved {
            background:linear-gradient(180deg, rgba(14,28,52,.98) 0%, rgba(18,44,86,.96) 100%);
            border-color:rgba(96,165,250,.28);
        }

        .dark .sf-ss-card--bank,
        .dark .sf-ss-slip-card.sf-ss-card--bank {
            background:linear-gradient(180deg, rgba(55,33,8,.96) 0%, rgba(70,42,10,.96) 100%);
            border-color:rgba(251,146,60,.30);
        }

        .dark .sf-ss-card--paid,
        .dark .sf-ss-slip-card.sf-ss-card--paid {
            background:linear-gradient(180deg, rgba(7,47,31,.98) 0%, rgba(10,64,39,.96) 100%);
            border-color:rgba(74,222,128,.30);
            box-shadow:0 16px 30px rgba(16,185,129,.12);
        }

        .dark .sf-ss-card--rejected,
        .dark .sf-ss-slip-card.sf-ss-card--rejected {
            background:linear-gradient(180deg, rgba(60,14,20,.98) 0%, rgba(76,16,30,.96) 100%);
            border-color:rgba(251,113,133,.28);
        }

        .dark .sf-ss-card:hover,.dark .sf-ss-slip-card:hover {
            border-color:rgba(76,167,168,.24);box-shadow:0 16px 28px rgba(0,0,0,.24);
        }

        .dark .sf-ss-section-kicker,.dark .sf-ss-project-title { color:#7fcfd0; }
        .dark .sf-ss-section-title,.dark .sf-ss-client-title,.dark .sf-ss-name,.dark .sf-ss-net,.dark .sf-ss-meta-value,
        .dark .sf-ss-status-box-value { color:#f6fbff; }
        .dark .sf-ss-section-sub,.dark .sf-ss-subname,.dark .sf-ss-period { color:#9fb2c3; }
        .dark .sf-ss-meta-label,.dark .sf-ss-status-box-label { color:#8ea8be; }
        .dark .sf-ss-meta { background:rgba(255,255,255,.03);border-color:rgba(76,167,168,.14); }
        .dark .sf-ss-empty { border-color:rgba(76,167,168,.14);background:rgba(255,255,255,.02);color:#9fb2c3; }

        @media (max-width: 1100px) {
            .sf-ss-filter-grid { grid-template-columns:1fr 1fr; }
            .sf-ss-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }

        @media (max-width: 768px) {
            .sf-ss-hero { flex-direction:column;align-items:flex-start; }
            .sf-ss-hero-title { font-size:42px; }
            .sf-ss-meta-grid,.sf-ss-filter-grid,.sf-ss-grid,.sf-ss-status-grid { grid-template-columns:1fr; }
        }
        /* SALARY SLIPS NIGHT FILTER INPUT CONTRAST FIX */
        .dark .sf-ss-filters input,
        .dark .sf-ss-filters select,
        .dark .sf-ss-filter-field input,
        .dark .sf-ss-filter-field select {
            background: rgba(15, 23, 42, .92) !important;
            color: #f8fafc !important;
            border: 1px solid rgba(76, 167, 168, .26) !important;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .20) !important;
        }

        .dark .sf-ss-filters input::placeholder,
        .dark .sf-ss-filter-field input::placeholder {
            color: rgba(226, 232, 240, .62) !important;
            opacity: 1 !important;
        }

        .dark .sf-ss-filters select,
        .dark .sf-ss-filter-field select {
            color-scheme: dark !important;
        }

        .dark .sf-ss-filters option,
        .dark .sf-ss-filter-field option {
            background: #0f172a !important;
            color: #f8fafc !important;
        }

        .dark .sf-ss-filters .choices,
        .dark .sf-ss-filters .choices__inner,
        .dark .sf-ss-filter-field .choices,
        .dark .sf-ss-filter-field .choices__inner {
            background: rgba(15, 23, 42, .92) !important;
            color: #f8fafc !important;
            border-color: rgba(76, 167, 168, .26) !important;
            border-radius: 14px !important;
        }

        .dark .sf-ss-filters .choices__placeholder,
        .dark .sf-ss-filter-field .choices__placeholder {
            color: rgba(226, 232, 240, .62) !important;
            opacity: 1 !important;
        }

        .dark .sf-ss-filters .choices__list--dropdown,
        .dark .sf-ss-filter-field .choices__list--dropdown {
            background: #0f172a !important;
            border-color: rgba(76, 167, 168, .22) !important;
            color: #f8fafc !important;
            border-radius: 14px !important;
            box-shadow: 0 20px 42px rgba(0, 0, 0, .35) !important;
        }

        .dark .sf-ss-filters .choices__item,
        .dark .sf-ss-filter-field .choices__item {
            color: #f8fafc !important;
        }

        .dark .sf-ss-filters .choices__item--selectable.is-highlighted,
        .dark .sf-ss-filter-field .choices__item--selectable.is-highlighted {
            background: rgba(76, 167, 168, .16) !important;
            color: #ffffff !important;
        }

</style>

    <div class="sf-ss-wrap">
        @if($selectedPerson)
            <section class="sf-ss-hero">
                <div class="sf-ss-hero-left">
                    <div class="sf-ss-hero-kicker">Salary Slips › {{ $selectedPerson['person_name'] }}</div>
                    <div class="sf-ss-hero-title">{{ $selectedPerson['person_name'] }}</div>
                    <div class="sf-ss-hero-sub">Salary slips grouped under this employee. Review totals first, then open any individual salary slip.</div>
                    <div class="sf-ss-summary">{{ $selectedPerson['salary_slips_count'] }} Salary Slips</div>
                </div>

                <div class="sf-ss-hero-right">
                    <a href="{{ \App\Filament\Resources\SalarySlips\SalarySlipResource::getUrl('index') }}" class="sf-btn-secondary">Back to People</a>
                    <button type="button" class="sf-btn-primary" wire:click="mountAction('generateSalarySlip')">Generate Salary Slips</button>
                    <button type="button" class="sf-btn-secondary" wire:click="mountAction('autoGenerateByProject')">Auto Generate by Project</button>
                </div>
            </section>

            <section class="sf-ss-section">
                <div class="sf-ss-section-kicker">Employee Totals</div>
                <div class="sf-ss-section-title">Payroll Summary</div>
                <div class="sf-ss-section-sub">Combined salary slip totals for this employee, including workflow states.</div>

                <div class="sf-ss-meta-grid">
                    <div class="sf-ss-meta">
                        <div class="sf-ss-meta-label">Salary Slips</div>
                        <div class="sf-ss-meta-value">{{ $selectedPerson['salary_slips_count'] }}</div>
                    </div>
                    <div class="sf-ss-meta">
                        <div class="sf-ss-meta-label">Currency</div>
                        <div class="sf-ss-meta-value">{{ $selectedPerson['currency'] }}</div>
                    </div>
                    <div class="sf-ss-meta">
                        <div class="sf-ss-meta-label">Worked Days Total</div>
                        <div class="sf-ss-meta-value">{{ number_format((float) $selectedPerson['worked_days_total'], 2) }}</div>
                    </div>
                    <div class="sf-ss-meta">
                        <div class="sf-ss-meta-label">Paid Days Total</div>
                        <div class="sf-ss-meta-value">{{ number_format((float) $selectedPerson['paid_days_total'], 2) }}</div>
                    </div>
                    <div class="sf-ss-meta">
                        <div class="sf-ss-meta-label">Total Days</div>
                        <div class="sf-ss-meta-value">{{ number_format((float) $selectedPerson['total_days_total'], 2) }}</div>
                    </div>
                    <div class="sf-ss-meta">
                        <div class="sf-ss-meta-label">Net Amount Total</div>
                        <div class="sf-ss-meta-value">{{ number_format((float) $selectedPerson['net_amount_total'], 2) }}</div>
                    </div>
                </div>

                <div class="sf-ss-status-grid">
                    <div class="sf-ss-status-box sf-ss-status-box--draft">
                        <div class="sf-ss-status-box-label">Draft</div>
                        <div class="sf-ss-status-box-value">{{ $selectedPerson['status_breakdown']['draft'] ?? 0 }}</div>
                    </div>
                    <div class="sf-ss-status-box sf-ss-status-box--approved">
                        <div class="sf-ss-status-box-label">Approved</div>
                        <div class="sf-ss-status-box-value">{{ $selectedPerson['status_breakdown']['approved'] ?? 0 }}</div>
                    </div>
                    <div class="sf-ss-status-box sf-ss-status-box--bank">
                        <div class="sf-ss-status-box-label">Sent to Bank</div>
                        <div class="sf-ss-status-box-value">{{ $selectedPerson['status_breakdown']['sent_to_bank'] ?? 0 }}</div>
                    </div>
                    <div class="sf-ss-status-box sf-ss-status-box--paid">
                        <div class="sf-ss-status-box-label">Paid</div>
                        <div class="sf-ss-status-box-value">{{ $selectedPerson['status_breakdown']['paid'] ?? 0 }}</div>
                    </div>
                    <div class="sf-ss-status-box sf-ss-status-box--rejected">
                        <div class="sf-ss-status-box-label">Bank Rejected</div>
                        <div class="sf-ss-status-box-value">{{ $selectedPerson['status_breakdown']['bank_rejected'] ?? 0 }}</div>
                    </div>
                </div>
            </section>

            <section class="sf-ss-section">
                <div class="sf-ss-section-kicker">Employee Salary Slips</div>
                <div class="sf-ss-section-title">Individual Salary Slip Records</div>
                <div class="sf-ss-section-sub">Open any salary slip below to review or edit the detailed payroll record.</div>

                @if(($selectedPerson['slips']->count() ?? 0) > 0)
                    <div class="sf-ss-grid">
                        @foreach($selectedPerson['slips'] as $salarySlip)
                            @php
                                $rawSlipStatus = (string) ($salarySlip->status ?: \App\Models\SalarySlip::STATUS_DRAFT);
                                $slipStatusLabel = $salaryStatusLabels[$rawSlipStatus] ?? ucfirst(str_replace('_', ' ', $rawSlipStatus));
                                $slipCardClass = $cardClassByStatus($rawSlipStatus);
                            @endphp

                            <a class="sf-ss-slip-card {{ $slipCardClass }}" href="{{ \App\Filament\Resources\SalarySlips\SalarySlipResource::getUrl('view', ['record' => $salarySlip]) }}">
                                <div class="sf-ss-top">
                                    <span class="sf-ss-status {{ $statusBadgeClass($rawSlipStatus) }}">{{ $slipStatusLabel }}</span>
                                    <span class="sf-ss-period">
                                        @if($salarySlip->salary_year && $salarySlip->salary_month)
                                            {{ sprintf('%02d / %04d', (int) $salarySlip->salary_month, (int) $salarySlip->salary_year) }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>

                                <div class="sf-ss-name">Salary Slip #{{ $salarySlip->id }}</div>
                                <div class="sf-ss-subname">{{ $salarySlip->currency ?: '-' }}</div>
                                <div class="sf-ss-net">{{ number_format((float) ($salarySlip->net_amount ?? $salarySlip->net_salary ?? 0), 2) }}</div>

                                <div class="sf-ss-meta-grid">
                                    <div class="sf-ss-meta">
                                        <div class="sf-ss-meta-label">Worked Days</div>
                                        <div class="sf-ss-meta-value">{{ number_format((float) ($salarySlip->days_worked ?? 0), 2) }}</div>
                                    </div>
                                    <div class="sf-ss-meta">
                                        <div class="sf-ss-meta-label">Paid Days</div>
                                        <div class="sf-ss-meta-value">{{ number_format((float) ($salarySlip->paid_days ?? 0), 2) }}</div>
                                    </div>
                                    <div class="sf-ss-meta">
                                        <div class="sf-ss-meta-label">Total Days</div>
                                        <div class="sf-ss-meta-value">{{ number_format((float) ($salarySlip->total_days ?? 0), 2) }}</div>
                                    </div>
                                    <div class="sf-ss-meta">
                                        <div class="sf-ss-meta-label">Net Amount</div>
                                        <div class="sf-ss-meta-value">{{ number_format((float) ($salarySlip->net_amount ?? $salarySlip->net_salary ?? 0), 2) }}</div>
                                    </div>
                                </div>

                                <div class="sf-ss-open">Open Salary Slip ↗</div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="sf-ss-empty">No salary slips found for this employee.</div>
                @endif
            </section>
        @else
            <section class="sf-ss-hero">
                <div class="sf-ss-hero-left">
                    <div class="sf-ss-hero-kicker">Finance › Salary Slips</div>
                    <div class="sf-ss-hero-title">Salary Slips</div>
                    <div class="sf-ss-hero-sub">Salary slips grouped by client, project, and employee. Search by name and filter by status, month, client, and project.</div>
                    <div class="sf-ss-summary">{{ $peopleCount }} People · {{ $salarySlipsCount }} Salary Slips</div>
                </div>

                <div class="sf-ss-hero-right">
                    <button type="button" class="sf-btn-primary" wire:click="mountAction('generateSalarySlip')">Generate Salary Slips</button>
                    <button type="button" class="sf-btn-secondary" wire:click="mountAction('autoGenerateByProject')">Auto Generate by Project</button>
                </div>
            </section>

            <section class="sf-ss-filters">
                <div class="sf-ss-filter-grid">
                    <div class="sf-ss-filter-field">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search employee name...">
                    </div>
                    <div class="sf-ss-filter-field">
                        <select wire:model.live="clientFilter">
                            <option value="">All Clients</option>
                            @foreach($clientOptions as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sf-ss-filter-field">
                        <select wire:model.live="projectFilter">
                            <option value="">All Projects</option>
                            @foreach($projectOptions as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sf-ss-filter-field">
                        <select wire:model.live="monthFilter">
                            <option value="">All Months</option>
                            @foreach($monthOptions as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sf-ss-filter-field">
                        <select wire:model.live="statusFilter">
                            <option value="">All Statuses</option>
                            @foreach($statusOptions as $item)
                                <option value="{{ $item }}">{{ $salaryStatusLabels[$item] ?? ucfirst(str_replace('_', ' ', $item)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            <section class="sf-ss-section">
                <div class="sf-ss-section-kicker">Payroll Overview</div>
                <div class="sf-ss-section-title">Employees grouped by Client / Project</div>
                <div class="sf-ss-section-sub">Browse employee payroll cards by client and project to keep the page organized.</div>

                @if($filteredGroupedTree->count() > 0)
                    @foreach($filteredGroupedTree as $clientName => $projects)
                        <div class="sf-ss-client-block">
                            <div class="sf-ss-client-title">{{ $clientName }}</div>

                            @foreach($projects as $projectName => $people)
                                <div class="sf-ss-project-block">
                                    <div class="sf-ss-project-title">{{ $projectName }}</div>

                                    <div class="sf-ss-grid">
                                        @foreach($people as $person)
                                            @php
                                                $personRawStatus = (string) ($person['last_status'] ?: \App\Models\SalarySlip::STATUS_DRAFT);
                                                $personStatusLabel = $salaryStatusLabels[$personRawStatus] ?? ucfirst(str_replace('_', ' ', $personRawStatus));
                                                $personCardClass = $cardClassByStatus($personRawStatus);
                                            @endphp

                                            <a class="sf-ss-card {{ $personCardClass }}" href="{{ \App\Filament\Resources\SalarySlips\SalarySlipResource::getUrl('index') . '?person=' . urlencode($person['person_key']) }}">
                                                <div class="sf-ss-top">
                                                    <span class="sf-ss-status {{ $statusBadgeClass($personRawStatus) }}">{{ $personStatusLabel }}</span>
                                                    <span class="sf-ss-period">{{ $person['last_period_label'] }}</span>
                                                </div>

                                                <div class="sf-ss-name">{{ $person['person_name'] }}</div>
                                                <div class="sf-ss-subname">{{ $person['currency'] ?: '-' }}</div>
                                                <div class="sf-ss-net">{{ number_format((float) $person['net_amount_total'], 2) }}</div>

                                                <div class="sf-ss-meta-grid">
                                                    <div class="sf-ss-meta">
                                                        <div class="sf-ss-meta-label">Slips</div>
                                                        <div class="sf-ss-meta-value">{{ $person['salary_slips_count'] }}</div>
                                                    </div>
                                                    <div class="sf-ss-meta">
                                                        <div class="sf-ss-meta-label">Worked</div>
                                                        <div class="sf-ss-meta-value">{{ number_format((float) $person['worked_days_total'], 2) }}</div>
                                                    </div>
                                                    <div class="sf-ss-meta">
                                                        <div class="sf-ss-meta-label">Paid</div>
                                                        <div class="sf-ss-meta-value">{{ number_format((float) $person['paid_days_total'], 2) }}</div>
                                                    </div>
                                                    <div class="sf-ss-meta">
                                                        <div class="sf-ss-meta-label">Total Days</div>
                                                        <div class="sf-ss-meta-value">{{ number_format((float) $person['total_days_total'], 2) }}</div>
                                                    </div>
                                                </div>

                                                <div class="sf-ss-status-grid">
                                                    <div class="sf-ss-status-box sf-ss-status-box--draft">
                                                        <div class="sf-ss-status-box-label">Draft</div>
                                                        <div class="sf-ss-status-box-value">{{ $person['status_breakdown']['draft'] ?? 0 }}</div>
                                                    </div>
                                                    <div class="sf-ss-status-box sf-ss-status-box--approved">
                                                        <div class="sf-ss-status-box-label">Approved</div>
                                                        <div class="sf-ss-status-box-value">{{ $person['status_breakdown']['approved'] ?? 0 }}</div>
                                                    </div>
                                                    <div class="sf-ss-status-box sf-ss-status-box--bank">
                                                        <div class="sf-ss-status-box-label">Sent to Bank</div>
                                                        <div class="sf-ss-status-box-value">{{ $person['status_breakdown']['sent_to_bank'] ?? 0 }}</div>
                                                    </div>
                                                    <div class="sf-ss-status-box sf-ss-status-box--paid">
                                                        <div class="sf-ss-status-box-label">Paid</div>
                                                        <div class="sf-ss-status-box-value">{{ $person['status_breakdown']['paid'] ?? 0 }}</div>
                                                    </div>
                                                    <div class="sf-ss-status-box sf-ss-status-box--rejected">
                                                        <div class="sf-ss-status-box-label">Bank Rejected</div>
                                                        <div class="sf-ss-status-box-value">{{ $person['status_breakdown']['bank_rejected'] ?? 0 }}</div>
                                                    </div>
                                                </div>

                                                <div class="sf-ss-open">Open Employee Salary Slips ↗</div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="sf-ss-empty">No salary slips match the current filters.</div>
                @endif
            </section>
        @endif
    </div>

    <x-filament-actions::modals />
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

