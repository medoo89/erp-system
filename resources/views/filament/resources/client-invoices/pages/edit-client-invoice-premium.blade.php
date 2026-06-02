<x-filament-panels::page>
    @php
        $invoice = $this->record;
        $clientName = $invoice->client?->name ?? '-';
        $projectName = $invoice->project?->name ?? '-';
        $statusLabel = \App\Models\ClientInvoice::statusOptions()[$invoice->status] ?? $invoice->status;

        $backUrl = static::getResource()::getUrl('index');
        $viewUrl = static::getResource()::getUrl('view', ['record' => $invoice]);

    @endphp

    <style>
        .sf-edit-page-wrap {
            width: min(1040px, calc(100vw - 64px));
            margin: 28px auto 60px;
        }

        .sf-edit-hero {
            border-radius: 30px;
            padding: 34px;
            background:
                radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
                linear-gradient(135deg, #0b223d 0%, #12365a 48%, #245f66 100%);
            border-bottom: 4px solid rgba(34, 211, 238, .55);
            box-shadow: 0 24px 70px rgba(15, 39, 67, .16);
            color: #fff;
            margin-bottom: 24px;
        }

        .sf-edit-hero-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            align-items: start;
        }

        .sf-edit-kicker {
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
            font-weight: 850;
        }

        .sf-edit-title {
            margin-top: 12px;
            font-size: 38px;
            font-weight: 950;
            letter-spacing: -.055em;
            line-height: 1.05;
        }

        .sf-edit-subtitle {
            margin-top: 14px;
            color: rgba(255, 255, 255, .78);
            font-size: 14px;
            font-weight: 600;
            line-height: 1.55;
        }


        .sf-edit-pill-btn {
            min-height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 0;
            text-decoration: none !important;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .18);
            white-space: nowrap;
        }

        .sf-edit-pill-gray {
            color: #ffffff !important;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255,255,255,.18);
        }

        .sf-edit-pill-blue {
            color: #ffffff !important;
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
        }

        .sf-edit-pill-yellow {
            color: #111827 !important;
            background: linear-gradient(135deg, #facc15, #f59e0b);
        }

        .sf-edit-pill-green {
            color: #ffffff !important;
            background: linear-gradient(135deg, #10b981, #14b8a6);
        }

        .sf-edit-pill-red {
            color: #ffffff !important;
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .sf-edit-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
            max-width: 560px;
        }

        .sf-edit-actions .fi-ac,
        .sf-edit-actions a,
        .sf-edit-actions button {
            border-radius: 999px !important;
            min-height: 40px !important;
            font-weight: 950 !important;
        }

        .sf-edit-stats {
            margin-top: 26px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .sf-edit-stat {
            border-radius: 18px;
            padding: 14px;
            background: rgba(255,255,255,.11);
            border: 1px solid rgba(255,255,255,.16);
        }

        .sf-edit-stat-label {
            color: rgba(255,255,255,.65);
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .sf-edit-stat-value {
            margin-top: 8px;
            color: #fff;
            font-size: 18px;
            font-weight: 950;
            line-height: 1.2;
        }

        .sf-edit-form-card {
            border-radius: 30px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(148, 163, 184, .22);
            box-shadow: 0 18px 50px rgba(15, 39, 67, .08);
            padding: 24px;
        }

        .sf-edit-form-actions {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            position: sticky;
            bottom: 18px;
            z-index: 20;
        }

        .sf-edit-save {
            min-height: 42px;
            border: 0;
            border-radius: 999px;
            padding: 0 22px;
            font-size: 13px;
            font-weight: 950;
            color: #111827;
            background: linear-gradient(135deg, #facc15, #f59e0b);
            box-shadow: 0 12px 28px rgba(245, 158, 11, .24);
            cursor: pointer;
        }

        .sf-edit-cancel {
            min-height: 42px;
            border-radius: 999px;
            padding: 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 950;
            text-decoration: none;
            color: #334155;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, .28);
        }

        @media (max-width: 980px) {
            .sf-edit-page-wrap {
                width: calc(100vw - 24px);
            }

            .sf-edit-hero {
                padding: 24px;
            }

            .sf-edit-hero-row {
                grid-template-columns: 1fr;
            }

            .sf-edit-actions {
                justify-content: flex-start;
            }

            .sf-edit-stats {
                grid-template-columns: 1fr;
            }

            .sf-edit-title {
                font-size: 28px;
            }
        }
    </style>

    <div class="sf-edit-page-wrap">
        <section class="sf-edit-hero">
            <div class="sf-edit-hero-row">
                <div>
                    <div class="sf-edit-kicker">Client Invoice · Edit</div>
                    <div class="sf-edit-title">{{ $invoice->invoice_number ?: ('Invoice #' . $invoice->id) }}</div>
                    <div class="sf-edit-subtitle">
                        Client: {{ $clientName }} · Project: {{ $projectName }} · Status: {{ $statusLabel }}
                    </div>
                </div>

                <div class="sf-edit-actions">
                    
                    <a href="{{ $viewUrl }}" class="sf-edit-pill-btn sf-edit-pill-gray">
                        Back
                    </a>

                    <a href="{{ $viewUrl }}" class="sf-edit-pill-btn sf-edit-pill-blue">
                        View Invoice
                    </a>

                    <button type="button"
                        wire:click="mountAction('manageTimesheet')"
                        class="sf-edit-pill-btn sf-edit-pill-yellow">
                        Manage Timesheet
                    </button>

                    <button type="button"
                        wire:click="mountAction('generateTimesheetFromSalarySlips')"
                        class="sf-edit-pill-btn sf-edit-pill-green">
                        Generate From Salary Slips
                    </button>

                    <button type="button"
                        wire:click="mountAction('delete')"
                        class="sf-edit-pill-btn sf-edit-pill-red">
                        Delete
                    </button>

                </div>
            </div>

            <div class="sf-edit-stats">
                <div class="sf-edit-stat">
                    <div class="sf-edit-stat-label">Total Amount</div>
                    <div class="sf-edit-stat-value">
                        {{ number_format((float) ($invoice->total_amount ?? 0), 2) }}
                        {{ $invoice->display_currency ?: $invoice->foreign_currency ?: '' }}
                    </div>
                </div>

                <div class="sf-edit-stat">
                    <div class="sf-edit-stat-label">Foreign Portion</div>
                    <div class="sf-edit-stat-value">
                        {{ number_format((float) ($invoice->foreign_amount_due ?? 0), 2) }}
                        {{ $invoice->foreign_currency ?: '-' }}
                    </div>
                </div>

                <div class="sf-edit-stat">
                    <div class="sf-edit-stat-label">Local Portion</div>
                    <div class="sf-edit-stat-value">
                        {{ number_format((float) ($invoice->local_amount_due ?? 0), 2) }}
                        {{ $invoice->local_currency ?: '-' }}
                    </div>
                </div>

                <div class="sf-edit-stat">
                    <div class="sf-edit-stat-label">Service Period</div>
                    <div class="sf-edit-stat-value">
                        {{ optional($invoice->period_start)->format('Y-m-d') ?: '-' }}
                        →
                        {{ optional($invoice->period_end)->format('Y-m-d') ?: '-' }}
                    </div>
                </div>
            </div>
        </section>

        <form wire:submit="save" class="sf-edit-form-card">
            {{ $this->form }}

            <div class="sf-edit-form-actions">
                <a href="{{ static::getResource()::getUrl('view', ['record' => $invoice]) }}" class="sf-edit-cancel">
                    Cancel
                </a>

                <button type="submit" class="sf-edit-save">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
