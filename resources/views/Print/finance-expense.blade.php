@php
    use App\Models\FinanceExpense;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\Storage;

    $expense = $expense->fresh([
        'jobApplication',
        'preEmployment',
        'employment',
        'employmentRotation',
        'job',
        'client',
        'project',
        'financeProfile',
        'createdBy',
        'approvedBy',
        'treasuryAccount',
        'reimbursedSalarySlip',
    ]);

    $owner = method_exists($expense, 'ownerName') ? $expense->ownerName() : '-';

    $scopeLabel = FinanceExpense::scopeLabels()[$expense->expense_scope] ?? ucfirst(str_replace('_', ' ', (string) $expense->expense_scope));
    $categoryLabel = FinanceExpense::categoryLabels()[$expense->category] ?? ucfirst(str_replace('_', ' ', (string) $expense->category));
    $statusLabel = FinanceExpense::statusLabels()[$expense->status] ?? ucfirst(str_replace('_', ' ', (string) $expense->status));
    $paidByLabel = FinanceExpense::paidByLabels()[$expense->paid_by] ?? ucfirst(str_replace('_', ' ', (string) $expense->paid_by));
    $reimbursementLabel = FinanceExpense::reimbursementLabels()[$expense->reimbursement_status] ?? ucfirst(str_replace('_', ' ', (string) $expense->reimbursement_status));
    $allocationLabel = FinanceExpense::allocationLabels()[$expense->allocation_status] ?? ucfirst(str_replace('_', ' ', (string) ($expense->allocation_status ?: 'unallocated')));

    $amount = number_format((float) ($expense->amount ?? 0), 2) . ' ' . ($expense->currency ?: '');
    $claimAmount = number_format((float) ($expense->reimbursement_amount ?? $expense->amount ?? 0), 2) . ' ' . ($expense->reimbursement_currency ?: $expense->currency ?: '');

    $attachmentPath = $expense->attachment_path ?: $expense->receipt_file_path;
    $hasAttachment = filled($attachmentPath) || (bool) $expense->has_attachment;

    $createdBy = $expense->createdBy?->name ?: '-';
    $approvedBy = $expense->approvedBy?->name ?: '-';

    if (filled($expense->reimbursement_decision_by ?? null)) {
        $approvedBy = \App\Models\User::query()->whereKey($expense->reimbursement_decision_by)->value('name') ?: $approvedBy;
    }

    $primaryStageLabel = match ((string) $expense->expense_scope) {
        FinanceExpense::SCOPE_PRE_HIRE => 'Pre-Employment',
        FinanceExpense::SCOPE_EMPLOYMENT => 'Employment',
        FinanceExpense::SCOPE_ROTATION => 'Rotation',
        default => 'Ad Hoc / Manual',
    };

    $primaryStageValue = match ((string) $expense->expense_scope) {
        FinanceExpense::SCOPE_PRE_HIRE => $expense->preEmployment?->candidate_name ?: $expense->jobApplication?->full_name ?: $owner,
        FinanceExpense::SCOPE_EMPLOYMENT => $expense->employment?->employee_name ?: $owner,
        FinanceExpense::SCOPE_ROTATION => $expense->employmentRotation?->employment?->employee_name ?: $owner,
        default => $owner,
    };

    $coveredFrom = $expense->incurred_from ? Carbon::parse($expense->incurred_from)->format('Y-m-d') : '-';
    $coveredTo = $expense->incurred_to ? Carbon::parse($expense->incurred_to)->format('Y-m-d') : '-';
    $sfLogoUrl = asset('logo.png');
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Finance Expense #{{ $expense->id }} - Print</title>

    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        
        .brand-line {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo-wrap {
            width: 70px;
            height: 70px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            overflow: hidden;
            padding: 5px;
            flex: 0 0 auto;
        }

        .brand-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        body {
            margin: 0;
            background: #ffffff;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
        }

        .page {
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            padding-bottom: 14px;
            border-bottom: 4px solid #0f4f5f;
            margin-bottom: 16px;
        }

        .brand {
            font-size: 19px;
            font-weight: 900;
            color: #0f2f4a;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .doc-title {
            margin-top: 5px;
            font-size: 13px;
            font-weight: 800;
            color: #475569;
        }

        .meta {
            min-width: 210px;
            text-align: right;
            color: #475569;
            font-size: 10px;
            line-height: 1.7;
        }

        .meta strong {
            color: #111827;
            font-weight: 900;
        }

        .hero {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }

        .hero-box {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
            padding: 11px 12px;
        }

        .label {
            display: block;
            color: #64748b;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .13em;
            margin-bottom: 5px;
        }

        .value {
            display: block;
            color: #0f172a;
            font-size: 13px;
            font-weight: 900;
        }

        .title-value {
            font-size: 17px;
        }

        .section-title {
            margin: 15px 0 7px;
            padding: 7px 10px;
            border-radius: 7px;
            background: #0f4f5f;
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 7px 8px;
            vertical-align: top;
            word-break: break-word;
        }

        th {
            width: 18%;
            background: #eef6f7;
            color: #0f4f5f;
            font-size: 9px;
            font-weight: 900;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        td {
            color: #111827;
            font-size: 11px;
            font-weight: 700;
            background: #ffffff;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            font-size: 10px;
            font-weight: 900;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .signature-box {
            height: 76px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 9px;
        }

        .signature-box span {
            display: block;
            color: #64748b;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .10em;
        }

        .signature-box strong {
            display: block;
            margin-top: 34px;
            padding-top: 5px;
            border-top: 1px dashed #cbd5e1;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
        }

        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            text-align: center;
            color: #64748b;
            font-size: 9px;
            font-weight: 700;
        }

        .no-print {
            position: fixed;
            right: 18px;
            top: 18px;
            display: flex;
            gap: 8px;
        }

        .no-print button,
        .no-print a {
            border: 0;
            border-radius: 999px;
            padding: 10px 16px;
            background: #0f4f5f;
            color: #fff;
            font-weight: 900;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
        }

        .no-print a {
            background: #475569;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    
.sf-print-logo-force {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    margin: 0 0 10px 0;
    padding: 0;
}

.sf-print-logo-force img {
    width: 96px !important;
    height: auto !important;
    max-height: 82px !important;
    object-fit: contain !important;
    display: block !important;
}



/* SADA RESTORED PRINT BRAND HEADER */
.sf-print-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin: 0 0 22px 0;
    padding: 0 0 18px 0;
    border-bottom: 5px solid #064f5a;
}

.sf-print-brand-left {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 0;
}

.sf-print-brand-logo {
    width: 92px;
    height: auto;
    object-fit: contain;
    display: block;
    flex: 0 0 auto;
}

.sf-print-brand-text {
    min-width: 0;
}

.sf-print-brand-title {
    margin: 0;
    color: #08233d;
    font-size: 27px;
    line-height: 1.05;
    font-weight: 950;
    letter-spacing: .11em;
    text-transform: uppercase;
}

.sf-print-brand-subtitle {
    margin-top: 10px;
    color: #0f2740;
    font-size: 15px;
    font-weight: 850;
}

.sf-print-meta-right {
    text-align: right;
    color: #0f2740;
    font-size: 13px;
    line-height: 1.7;
    font-weight: 650;
    min-width: 190px;
}

.sf-print-meta-right strong {
    font-weight: 950;
}

@media print {
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    .sf-print-header-row {
        display: flex !important;
        border-bottom: 5px solid #064f5a !important;
    }

    .sf-print-brand-logo {
        display: block !important;
        width: 92px !important;
    }

    .no-print,
    .print-actions,
    .sf-print-actions {
        display: none !important;
    }
}




/* SADA PRINT HEADER FINAL GILORY FIX 2026-05-07 */
@font-face {
    font-family: "GilorySF";
    src:
        url("/fonts/Gilory/Gilory-Bold.woff2") format("woff2"),
        url("/fonts/Gilory/Gilory-Bold.woff") format("woff"),
        url("/fonts/Gilory/Gilory-Bold.ttf") format("truetype"),
        url("/fonts/Gilory/Gilory-ExtraBold.woff2") format("woff2"),
        url("/fonts/Gilory/Gilory-ExtraBold.woff") format("woff"),
        url("/fonts/Gilory/Gilory-ExtraBold.ttf") format("truetype");
    font-weight: 800;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: "GilorySF";
    src:
        url("/fonts/Gilory/Gilory-Light.woff2") format("woff2"),
        url("/fonts/Gilory/Gilory-Light.woff") format("woff"),
        url("/fonts/Gilory/Gilory-Light.ttf") format("truetype"),
        url("/fonts/Gilory/Gilory-Regular.woff2") format("woff2"),
        url("/fonts/Gilory/Gilory-Regular.woff") format("woff"),
        url("/fonts/Gilory/Gilory-Regular.ttf") format("truetype");
    font-weight: 300;
    font-style: normal;
    font-display: swap;
}

.sf-print-header-row {
    display: grid !important;
    grid-template-columns: 86px minmax(0, 1fr) 175px !important;
    align-items: center !important;
    column-gap: 18px !important;
    margin: 0 0 14px 0 !important;
    padding: 0 0 12px 0 !important;
    border-bottom: 4px solid #064f5a !important;
}

.sf-print-brand-left {
    display: contents !important;
}

.sf-print-brand-logo {
    grid-column: 1 !important;
    width: 62px !important;
    height: auto !important;
    max-height: 78px !important;
    object-fit: contain !important;
}

.sf-print-brand-copy {
    grid-column: 2 !important;
    min-width: 0 !important;
    overflow: hidden !important;
}

.sf-print-brand-title {
    font-family: "GilorySF", "Gill Sans", "Avenir Next", "Montserrat", Arial, sans-serif !important;
    display: block !important;
    color: #071f36 !important;
    font-size: 22px !important;
    line-height: 1 !important;
    letter-spacing: .20em !important;
    margin: 0 !important;
    padding: 0 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: clip !important;
    text-transform: uppercase !important;
}

.sf-print-title-bold {
    font-weight: 800 !important;
}

.sf-print-title-light {
    font-weight: 300 !important;
    letter-spacing: .14em !important;
}

.sf-print-brand-subtitle {
    margin-top: 8px !important;
    font-size: 12.5px !important;
    line-height: 1.15 !important;
    font-weight: 800 !important;
    color: #071f36 !important;
    white-space: nowrap !important;
}

.sf-print-meta-right {
    grid-column: 3 !important;
    min-width: 175px !important;
    max-width: 175px !important;
    text-align: right !important;
    font-size: 12px !important;
    line-height: 1.45 !important;
    font-weight: 800 !important;
    color: #071f36 !important;
    align-self: center !important;
}

@media print {
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .sf-print-header-row {
        grid-template-columns: 86px minmax(0, 1fr) 175px !important;
        column-gap: 18px !important;
        border-bottom-color: #064f5a !important;
    }

    .sf-print-brand-logo {
        width: 62px !important;
        max-height: 78px !important;
    }

    .sf-print-brand-title {
        font-size: 22px !important;
        letter-spacing: .20em !important;
        white-space: nowrap !important;
        overflow: hidden !important;
    }

    .sf-print-title-light {
        letter-spacing: .14em !important;
    }

    .sf-print-brand-subtitle {
        font-size: 12.5px !important;
    }

    .sf-print-meta-right {
        min-width: 175px !important;
        max-width: 175px !important;
    }
}


/* SADA HEADER FINAL COMPACT FIX 2026-05-07 */
.sf-print-header-row {
    grid-template-columns: 74px minmax(0, 1fr) 190px !important;
    gap: 14px !important;
    align-items: center !important;
}

.sf-print-brand-copy {
    max-width: 100% !important;
    overflow: hidden !important;
}

.sf-print-brand-title {
    display: flex !important;
    align-items: baseline !important;
    gap: 12px !important;
    max-width: 100% !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    color: #061f36 !important;
    line-height: 1 !important;
}

.sf-print-title-bold {
    font-family: "SadaGallery", Arial, sans-serif !important;
    font-weight: 800 !important;
    font-size: 21px !important;
    letter-spacing: .17em !important;
    white-space: nowrap !important;
    flex: 0 0 auto !important;
}

.sf-print-title-light {
    font-family: "SadaGallery", Arial, sans-serif !important;
    font-weight: 300 !important;
    font-size: 20px !important;
    letter-spacing: .11em !important;
    white-space: nowrap !important;
    margin-left: 0 !important;
    flex: 1 1 auto !important;
    min-width: 0 !important;
    overflow: hidden !important;
    text-overflow: clip !important;
}

.sf-print-brand-subtitle {
    font-size: 11.5px !important;
    margin-top: 8px !important;
    white-space: nowrap !important;
}

.sf-print-meta-right {
    min-width: 190px !important;
    max-width: 190px !important;
    text-align: right !important;
    font-size: 11.5px !important;
    line-height: 1.45 !important;
    z-index: 5 !important;
    background: #fff !important;
}

@media print {
    .sf-print-header-row {
        grid-template-columns: 74px minmax(0, 1fr) 190px !important;
        gap: 14px !important;
    }

    .sf-print-title-bold {
        font-size: 21px !important;
        letter-spacing: .17em !important;
    }

    .sf-print-title-light {
        font-size: 20px !important;
        letter-spacing: .11em !important;
    }

    .sf-print-meta-right {
        min-width: 190px !important;
        max-width: 190px !important;
        background: #fff !important;
    }
}


/* SADA HOTFIX remove print header tail */
.print-brand-title,
.sf-print-brand-title,
.company-title,
.brand-title {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: clip !important;
}
</style>
</head>

<body>

<div class="sf-print-header-row">
    <div class="sf-print-brand-left">
        <img class="sf-print-brand-logo" src="{{ asset('logo.png') }}" alt="Sada Fezzan Logo">
        <div class="sf-print-brand-text">
            <h1 class="sf-print-brand-title"><span class="sf-print-title-bold">SADA FEZZAN</span> <span class="sf-print-title-light">FOR OIL SERVICES</span></span> <span class="sf-print-title-light"> </span></h1>
            <div class="sf-print-brand-subtitle">Finance Expense / Reimbursement Claim Form</div>
        </div>
    </div>

    <div class="sf-print-meta-right">
        <div>Expense ID: <strong>#{{ $expense->id ?? $record->id ?? '-' }}</strong></div>
        <div>Printed At: <strong>{{ now()->format('Y-m-d H:i') }}</strong></div>
        <div>ERP Version: <strong>1.2</strong></div>
    </div>
</div>

    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <a href="{{ \App\Filament\Resources\FinanceExpenses\FinanceExpenseResource::getUrl('view', ['record' => $expense->id]) }}">Back</a>
    </div>

    <main class="page">
        <header class="header">
            <div>
                </div>

            <div class="meta">
                <div></div>
            </div>
        </header>

        <section class="hero">
            <div class="hero-box">
                <span class="label">Expense Title</span>
                <strong class="value title-value">{{ $expense->title ?: '-' }}</strong>
            </div>

            <div class="hero-box">
                <span class="label">Current Status</span>
                <strong class="value"><span class="status-pill">{{ $statusLabel }}</span></strong>
            </div>
        </section>

        <div class="section-title">Main Information</div>

        <table>
            <tbody>
                <tr>
                    <th>Owner</th>
                    <td>{{ $owner }}</td>
                    <th>Stage</th>
                    <td>{{ $primaryStageLabel }} — {{ $primaryStageValue ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Scope</th>
                    <td>{{ $scopeLabel }}</td>
                    <th>Category</th>
                    <td>{{ $categoryLabel }}</td>
                </tr>
                <tr>
                    <th>Expense Date</th>
                    <td>{{ $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '-' }}</td>
                    <th>Covered Period</th>
                    <td>{{ $coveredFrom }} → {{ $coveredTo }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>{{ $amount }}</td>
                    <th>Claim Amount</th>
                    <td>{{ $claimAmount }}</td>
                </tr>
                <tr>
                    <th>Paid By</th>
                    <td>{{ $paidByLabel }}</td>
                    <th>Reimbursement</th>
                    <td>{{ $reimbursementLabel }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Project / Work Links</div>

        <table>
            <tbody>
                <tr>
                    <th>Job</th>
                    <td>{{ $expense->job?->title ?? '-' }}</td>
                    <th>Client</th>
                    <td>{{ $expense->client?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Project</th>
                    <td>{{ $expense->project?->name ?? '-' }}</td>
                    <th>Rotation</th>
                    <td>{{ $expense->employmentRotation?->rotation_label ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Salary Slip Link</th>
                    <td colspan="3">
                        @if($expense->reimbursedSalarySlip)
                            Salary Slip #{{ $expense->reimbursedSalarySlip->id }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Expense Details</div>

        <table>
            <tbody>
                <tr>
                    <th>Description</th>
                    <td colspan="3">{{ $expense->description ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Vendor / Supplier</th>
                    <td>{{ $expense->vendor_name ?: '-' }}</td>
                    <th>Treasury Account</th>
                    <td>{{ $expense->treasuryAccount?->account_name ?? $expense->treasuryAccount?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Allocation</th>
                    <td>{{ $allocationLabel }}</td>
                    <th>Attachment</th>
                    <td>{{ $hasAttachment ? 'Attached' : 'No Attachment' }}</td>
                </tr>
                @if($hasAttachment && $attachmentPath)
                    <tr>
                        <th>Attachment Path</th>
                        <td colspan="3">{{ $attachmentPath }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="section-title">Approval & Audit</div>

        <table>
            <tbody>
                <tr>
                    <th>Created By</th>
                    <td>{{ $createdBy }}</td>
                    <th>Approved By</th>
                    <td>{{ $approvedBy }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $expense->created_at ? $expense->created_at->format('Y-m-d H:i') : '-' }}</td>
                    <th>Last Updated</th>
                    <td>{{ $expense->updated_at ? $expense->updated_at->format('Y-m-d H:i') : '-' }}</td>
                </tr>
                <tr>
                    <th>Notes</th>
                    <td colspan="3">{{ $expense->notes ?? $expense->reimbursement_notes ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <section class="signatures">
            <div class="signature-box">
                <span>Prepared By</span>
                <strong>Signature / Stamp</strong>
            </div>

            <div class="signature-box">
                <span>Finance Approval</span>
                <strong>Signature / Stamp</strong>
            </div>

            <div class="signature-box">
                <span>Management Approval</span>
                <strong>Signature / Stamp</strong>
            </div>
        </section>

        <div class="footer">
            Generated from Sada Fezzan ERP System.
        </div>
    </main>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 350);
        });
    </script>
</body>
</html>
