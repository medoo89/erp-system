<x-filament-panels::page>

<style>
    /* ===== Invoice header aligned with Clients page style ===== */
    .sf-invoice-page-header {
        width: min(920px, calc(100vw - 64px)) !important;
        margin: 30px auto 22px !important;
        border-radius: 30px !important;
        padding: 38px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
            linear-gradient(135deg, #0b223d 0%, #12365a 48%, #245f66 100%) !important;
        border: 0 !important;
        border-bottom: 4px solid rgba(34, 211, 238, .55) !important;
        box-shadow: 0 24px 70px rgba(15, 39, 67, .16) !important;
        color: #ffffff !important;
        overflow: hidden !important;
        position: relative !important;
        z-index: 2 !important;
    }

    .sf-invoice-page-header-row {
        display: grid !important;
        grid-template-columns: 1fr auto !important;
        align-items: start !important;
        gap: 24px !important;
    }

    .sf-invoice-page-kicker {
        background: transparent !important;
        border: 0 !important;
        padding: 0 !important;
        min-height: auto !important;
        color: rgba(255, 255, 255, .72) !important;
        font-size: 13px !important;
        font-weight: 850 !important;
        letter-spacing: 0 !important;
        text-transform: none !important;
    }

    .sf-invoice-page-title {
        margin-top: 14px !important;
        color: #ffffff !important;
        font-size: 38px !important;
        font-weight: 950 !important;
        letter-spacing: -.055em !important;
        line-height: 1.05 !important;
        max-width: 520px !important;
        text-shadow: none !important;
    }

    .sf-invoice-page-subtitle {
        margin-top: 16px !important;
        color: rgba(255, 255, 255, .78) !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        line-height: 1.55 !important;
        max-width: 720px !important;
    }

    .sf-invoice-page-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 10px !important;
        max-width: 470px !important;
        padding-top: 18px !important;
    }

    .sf-invoice-page-btn {
        min-height: 40px !important;
        padding: 0 16px !important;
        border-radius: 999px !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        border: none !important;
        text-decoration: none !important;
        box-shadow: 0 12px 24px rgba(15, 23, 42, .18) !important;
    }

    .sf-invoice-page-btn-gray {
        color: #ffffff !important;
        background: rgba(255, 255, 255, .14) !important;
    }

    .sf-invoice-page-btn-yellow {
        color: #111827 !important;
        background: #facc15 !important;
    }

    .sf-invoice-page-btn-blue {
        color: #ffffff !important;
        background: linear-gradient(135deg, #0ea5e9, #2563eb) !important;
    }

    .sf-invoice-page-btn-purple {
        color: #ffffff !important;
        background: linear-gradient(135deg, #8b5cf6, #6366f1) !important;
    }

    .sf-invoice-page-btn-green {
        color: #ffffff !important;
        background: linear-gradient(135deg, #10b981, #14b8a6) !important;
    }

    .sf-invoice-header-stats {
        margin-top: 28px !important;
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    .sf-invoice-header-stat {
        border-radius: 18px !important;
        padding: 14px !important;
        background: rgba(255, 255, 255, .11) !important;
        border: 1px solid rgba(255, 255, 255, .15) !important;
        box-shadow: none !important;
    }

    .sf-invoice-header-stat-label {
        color: rgba(255, 255, 255, .65) !important;
        font-size: 10px !important;
        font-weight: 900 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
    }

    .sf-invoice-header-stat-value {
        margin-top: 8px !important;
        color: #ffffff !important;
        font-size: 18px !important;
        font-weight: 950 !important;
        line-height: 1.2 !important;
    }

    /* Align the main invoice body blocks with the same header width */
    .sf-receipts,
    .sf-invoice-workflow-block,
    .sf-card,
    .sf-stat-grid,
    .sf-invoice-grid,
    .sf-invoice-section,
    .sf-invoice-body,
    .sf-invoice-premium-body {
        max-width: 920px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    @media (max-width: 980px) {
        .sf-invoice-page-header {
            width: min(100% - 24px, 920px) !important;
            padding: 24px !important;
        }

        .sf-invoice-page-header-row {
            grid-template-columns: 1fr !important;
        }

        .sf-invoice-page-actions {
            justify-content: flex-start !important;
            padding-top: 0 !important;
        }

        .sf-invoice-header-stats {
            grid-template-columns: 1fr !important;
        }

        .sf-invoice-page-title {
            font-size: 28px !important;
        }
    }

        .sf-invoice-page-btn-red {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255,255,255,.18) !important;
        }

        .sf-invoice-page-btn-orange {
            background: linear-gradient(135deg, #facc15, #f59e0b) !important;
            color: #111827 !important;
            border: 1px solid rgba(255,255,255,.18) !important;
        }

        .sf-invoice-page-btn-gray {
            background: linear-gradient(135deg, #475569, #334155) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255,255,255,.18) !important;
        }

</style>


<style>
    /* ===== Forced Premium Invoice Header - unified with ERP blue pages ===== */
    .sf-invoice-page-header {
        width: min(1180px, calc(100vw - 32px)) !important;
        margin: 22px auto 18px !important;
        border-radius: 32px !important;
        padding: 24px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .22), transparent 34%),
            linear-gradient(135deg, #0f274a 0%, #234b74 48%, #2563eb 100%) !important;
        box-shadow: 0 24px 70px rgba(15, 39, 67, .24) !important;
        color: #ffffff !important;
        overflow: hidden !important;
        position: relative !important;
        z-index: 2 !important;
        opacity: 1 !important;
        filter: none !important;
    }

    .sf-invoice-page-header * {
        opacity: 1 !important;
        filter: none !important;
    }

    .sf-invoice-page-header-row {
        display: flex !important;
        align-items: flex-start !important;
        justify-content: space-between !important;
        gap: 20px !important;
    }

    .sf-invoice-page-kicker {
        display: inline-flex !important;
        align-items: center !important;
        min-height: 28px !important;
        padding: 0 13px !important;
        border-radius: 999px !important;
        background: rgba(224, 242, 254, .16) !important;
        border: 1px solid rgba(255, 255, 255, .18) !important;
        color: #e0f2fe !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
    }

    .sf-invoice-page-title {
        margin-top: 12px !important;
        color: #ffffff !important;
        font-size: 34px !important;
        font-weight: 950 !important;
        letter-spacing: -.045em !important;
        line-height: 1.05 !important;
        text-shadow: 0 2px 12px rgba(0, 0, 0, .18) !important;
    }

    .sf-invoice-page-subtitle {
        margin-top: 8px !important;
        color: rgba(255, 255, 255, .82) !important;
        font-size: 12px !important;
        font-weight: 850 !important;
        line-height: 1.55 !important;
    }

    .sf-invoice-page-actions {
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 9px !important;
        max-width: 650px !important;
        position: relative !important;
        z-index: 3 !important;
    }

    .sf-invoice-page-btn {
        min-height: 42px !important;
        padding: 0 16px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 7px !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        text-decoration: none !important;
        border: 1px solid rgba(255, 255, 255, .18) !important;
        box-shadow: 0 12px 26px rgba(15, 23, 42, .18) !important;
        white-space: nowrap !important;
    }

    .sf-invoice-page-btn-gray {
        color: #ffffff !important;
        background: rgba(255, 255, 255, .14) !important;
    }

    .sf-invoice-page-btn-yellow {
        color: #111827 !important;
        background: linear-gradient(135deg, #facc15, #f59e0b) !important;
    }

    .sf-invoice-page-btn-blue {
        color: #ffffff !important;
        background: linear-gradient(135deg, #0ea5e9, #2563eb) !important;
    }

    .sf-invoice-page-btn-purple {
        color: #ffffff !important;
        background: linear-gradient(135deg, #8b5cf6, #6366f1) !important;
    }

    .sf-invoice-page-btn-green {
        color: #ffffff !important;
        background: linear-gradient(135deg, #10b981, #14b8a6) !important;
    }

    .sf-invoice-header-stats {
        margin-top: 20px !important;
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 12px !important;
    }

    .sf-invoice-header-stat {
        border-radius: 22px !important;
        padding: 14px !important;
        background: rgba(255, 255, 255, .14) !important;
        border: 1px solid rgba(255, 255, 255, .18) !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .12) !important;
    }

    .sf-invoice-header-stat-label {
        color: rgba(255, 255, 255, .68) !important;
        font-size: 9px !important;
        font-weight: 950 !important;
        letter-spacing: .14em !important;
        text-transform: uppercase !important;
    }

    .sf-invoice-header-stat-value {
        margin-top: 7px !important;
        color: #ffffff !important;
        font-size: 17px !important;
        font-weight: 950 !important;
        line-height: 1.15 !important;
    }

    @media (max-width: 980px) {
        .sf-invoice-page-header-row {
            flex-direction: column !important;
        }

        .sf-invoice-page-actions {
            justify-content: flex-start !important;
        }

        .sf-invoice-header-stats {
            grid-template-columns: 1fr !important;
        }

        .sf-invoice-page-title {
            font-size: 26px !important;
        }
    }
</style>


@php
    $invoice = $this->record;
    $clientName = $invoice->client?->name ?? '-';
    $projectName = $invoice->project?->name ?? '-';
    $statusLabel = \App\Models\ClientInvoice::statusOptions()[$invoice->status] ?? $invoice->status;

    $backUrl = \App\Filament\Resources\ClientInvoices\ClientInvoiceResource::getUrl('index');
    $editUrl = \App\Filament\Resources\ClientInvoices\ClientInvoiceResource::getUrl('edit', ['record' => $invoice]);
    $printUrl = route('client-invoices.print', ['clientInvoice' => $invoice]);

    $printForeignUrl = \Illuminate\Support\Facades\Route::has('client-invoices.print-document')
        ? route('client-invoices.print-document', ['clientInvoice' => $invoice, 'documentType' => 'foreign'])
        : '#';

    $printLocalUrl = \Illuminate\Support\Facades\Route::has('client-invoices.print-document')
        ? route('client-invoices.print-document', ['clientInvoice' => $invoice, 'documentType' => 'local'])
        : '#';

    $timesheetPrintUrl = \Illuminate\Support\Facades\Route::has('client-invoices.print-timesheet')
        ? route('client-invoices.print-timesheet', ['clientInvoice' => $invoice])
        : '#';
@endphp







@php
    $invoice = $invoice ?? $this->record;
    $clientName = $clientName ?? ($invoice->client?->name ?? '-');
    $projectName = $projectName ?? ($invoice->project?->name ?? '-');
    $statusLabel = $statusLabel ?? (\App\Models\ClientInvoice::statusOptions()[$invoice->status] ?? $invoice->status);

    $backUrl = $backUrl ?? \App\Filament\Resources\ClientInvoices\ClientInvoiceResource::getUrl('index');
    $editUrl = $editUrl ?? \App\Filament\Resources\ClientInvoices\ClientInvoiceResource::getUrl('edit', ['record' => $invoice]);
    $printUrl = $printUrl ?? route('client-invoices.print', ['clientInvoice' => $invoice]);

    $printForeignUrl = $printForeignUrl ?? (
        \Illuminate\Support\Facades\Route::has('client-invoices.print-document')
            ? route('client-invoices.print-document', ['clientInvoice' => $invoice, 'documentType' => 'foreign'])
            : '#'
    );

    $printLocalUrl = $printLocalUrl ?? (
        \Illuminate\Support\Facades\Route::has('client-invoices.print-document')
            ? route('client-invoices.print-document', ['clientInvoice' => $invoice, 'documentType' => 'local'])
            : '#'
    );
@endphp

<div class="sf-invoice-page-header">
    <div class="sf-invoice-page-header-row">
        <div>
            <div class="sf-invoice-page-kicker">Client Invoice</div>
            <div class="sf-invoice-page-title">
                {{ $invoice->invoice_number ?: ('Invoice #' . $invoice->id) }}
            </div>
            <div class="sf-invoice-page-subtitle">
                Client: {{ $clientName }} · Project: {{ $projectName }} · Status: {{ $statusLabel }}
            </div>
        </div>

        <div class="sf-invoice-page-actions">
            <a href="{{ $backUrl }}" class="sf-invoice-page-btn sf-invoice-page-btn-gray">Back</a>

                    @php
                        $viewStatus = (string) ($invoice->status ?? '');
                        $viewForeignRemaining = method_exists($invoice, 'foreignRemainingAmount') ? round((float) $invoice->foreignRemainingAmount(), 2) : 0;
                        $viewLocalRemaining = method_exists($invoice, 'localRemainingAmount') ? round((float) $invoice->localRemainingAmount(), 2) : 0;
                        $viewHasRemaining = $viewForeignRemaining > 0.01 || $viewLocalRemaining > 0.01;
                    @endphp

                    @if(! in_array($viewStatus, [\App\Models\ClientInvoice::STATUS_DRAFT, \App\Models\ClientInvoice::STATUS_CANCELLED], true))
                        <button type="button"
                            wire:click="backToDraftInvoice"
                            class="sf-invoice-page-btn sf-invoice-page-btn-gray">
                            Back to Draft
                        </button>
                    @endif

                    @if($viewStatus === \App\Models\ClientInvoice::STATUS_DRAFT)
                        <button type="button"
                            wire:click="approveInvoiceQuick"
                            class="sf-invoice-page-btn sf-invoice-page-btn-blue">
                            Approve
                        </button>

                        <button type="button"
                            wire:click="cancelInvoiceQuick"
                            class="sf-invoice-page-btn sf-invoice-page-btn-red">
                            Cancel
                        </button>
                    @endif

                    @if($viewStatus === \App\Models\ClientInvoice::STATUS_APPROVED)
                        <button type="button"
                            wire:click="sendInvoiceToClientQuick"
                            class="sf-invoice-page-btn sf-invoice-page-btn-orange">
                            Send to Client
                        </button>

                        @if($viewHasRemaining)
                            <button type="button"
                                wire:click="mountAction('receivePartialPayment')"
                                class="sf-invoice-page-btn sf-invoice-page-btn-orange">
                                Receive Partial Payment
                            </button>

                            <button type="button"
                                wire:click="mountAction('receiveFullPayment')"
                                class="sf-invoice-page-btn sf-invoice-page-btn-green">
                                Receive Full Payment
                            </button>
                        @endif

                        <button type="button"
                            wire:click="cancelInvoiceQuick"
                            class="sf-invoice-page-btn sf-invoice-page-btn-red">
                            Cancel
                        </button>
                    @endif

                    @if(in_array($viewStatus, [\App\Models\ClientInvoice::STATUS_SENT_TO_CLIENT, \App\Models\ClientInvoice::STATUS_PARTIALLY_PAID], true) && $viewHasRemaining)
                        <button type="button"
                            wire:click="mountAction('receivePartialPayment')"
                            class="sf-invoice-page-btn sf-invoice-page-btn-orange">
                            Receive Partial Payment
                        </button>

                        <button type="button"
                            wire:click="mountAction('receiveFullPayment')"
                            class="sf-invoice-page-btn sf-invoice-page-btn-green">
                            Receive Full Payment
                        </button>
                    @endif

<a href="{{ $editUrl }}" class="sf-invoice-page-btn sf-invoice-page-btn-yellow">Edit Invoice</a>
            <a href="{{ $printUrl }}" target="_blank" class="sf-invoice-page-btn sf-invoice-page-btn-blue">Print Invoice</a>
            <a href="{{ $printForeignUrl }}" target="_blank" class="sf-invoice-page-btn sf-invoice-page-btn-purple">Print Foreign Invoice</a>
            <a href="{{ $printLocalUrl }}" target="_blank" class="sf-invoice-page-btn sf-invoice-page-btn-green">Print Local Invoice</a>
        </div>
    </div>

    <div class="sf-invoice-header-stats">
        <div class="sf-invoice-header-stat">
            <div class="sf-invoice-header-stat-label">Total Amount</div>
            <div class="sf-invoice-header-stat-value">
                {{ number_format((float) ($invoice->total_amount ?? 0), 2) }}
                {{ $invoice->display_currency ?: $invoice->foreign_currency ?: '' }}
            </div>
        </div>

        <div class="sf-invoice-header-stat">
            <div class="sf-invoice-header-stat-label">Foreign Portion</div>
            <div class="sf-invoice-header-stat-value">
                {{ number_format((float) ($invoice->foreign_amount_due ?? 0), 2) }}
                {{ $invoice->foreign_currency ?: '-' }}
            </div>
        </div>

        <div class="sf-invoice-header-stat">
            <div class="sf-invoice-header-stat-label">Local Portion</div>
            <div class="sf-invoice-header-stat-value">
                {{ number_format((float) ($invoice->local_amount_due ?? 0), 2) }}
                {{ $invoice->local_currency ?: '-' }}
            </div>
        </div>

        <div class="sf-invoice-header-stat">
            <div class="sf-invoice-header-stat-label">Service Period</div>
            <div class="sf-invoice-header-stat-value">
                {{ optional($invoice->period_start)->format('Y-m-d') ?: '-' }}
                →
                {{ optional($invoice->period_end)->format('Y-m-d') ?: '-' }}
            </div>
        </div>
    </div>
</div>


@php
    /*
    |--------------------------------------------------------------------------
    | Safe defaults for settlement/account/payment badges
    |--------------------------------------------------------------------------
    | These protect the premium invoice view from undefined variables when
    | payment rows do not have a full settlement/account context yet.
    */
    $settlementBg = $settlementBg ?? 'rgba(148, 163, 184, .14)';
    $settlementColor = $settlementColor ?? '#334155';
    $settlementBorder = $settlementBorder ?? 'rgba(148, 163, 184, .28)';
    $settlementText = $settlementText ?? 'Not Settled';

    $accountTypeBg = $accountTypeBg ?? 'rgba(59, 130, 246, .12)';
    $accountTypeColor = $accountTypeColor ?? '#1d4ed8';
    $accountTypeBorder = $accountTypeBorder ?? 'rgba(59, 130, 246, .28)';
    $accountTypeText = $accountTypeText ?? '-';

    $paymentBg = $paymentBg ?? 'rgba(148, 163, 184, .14)';
    $paymentColor = $paymentColor ?? '#334155';
    $paymentBorder = $paymentBorder ?? 'rgba(148, 163, 184, .28)';
    $paymentText = $paymentText ?? '-';

    $receiptBg = $receiptBg ?? 'rgba(148, 163, 184, .14)';
    $receiptColor = $receiptColor ?? '#334155';
    $receiptBorder = $receiptBorder ?? 'rgba(148, 163, 184, .28)';
    $receiptText = $receiptText ?? '-';
@endphp






    @php
        $invoice = $this->record;
        $client = $invoice->client?->name ?: 'Unknown Client';
        $project = $invoice->project?->name ?: 'No Project';

        $rawStatus = (string) ($invoice->status ?? 'draft');
        $status = \App\Models\ClientInvoice::statusOptions()[$rawStatus] ?? $rawStatus;

        $invoiceNo = $invoice->invoice_number ?: ('Invoice #' . $invoice->id);
        $invoiceDate = $invoice->invoice_date?->format('Y-m-d') ?: '-';

        $periodAnchor = $invoice->invoice_date ?: $invoice->period_start ?: now();
        $periodAnchor = $periodAnchor instanceof \Carbon\Carbon
            ? $periodAnchor->copy()
            : \Carbon\Carbon::parse($periodAnchor);

        $servicePeriodStart = $periodAnchor->copy()->startOfMonth()->format('Y-m-d');
        $servicePeriodEnd = $periodAnchor->copy()->endOfMonth()->format('Y-m-d');
        $servicePeriod = $servicePeriodStart . ' → ' . $servicePeriodEnd;

        $displayCurrency = $invoice->display_currency ?: $invoice->foreign_currency ?: 'USD';
        $foreignCurrency = $invoice->foreign_currency ?: $displayCurrency;
        $localCurrency = $invoice->local_currency ?: 'LYD';

        $foreignDue = (float) ($invoice->foreign_amount_due ?? 0);
        $localDue = (float) ($invoice->local_amount_due ?? 0);
        $foreignPercent = (float) ($invoice->foreign_percentage ?? 0);
        $localPercent = (float) ($invoice->local_percentage ?? 0);
        $grandTotal = (float) ($invoice->total_amount ?? 0);

        $payments = $invoice->payments()->with(['treasuryAccount'])->get();
        $pendingReceiptsCount = $payments->where('settlement_status', \App\Models\ClientInvoicePayment::SETTLEMENT_PENDING)->count();
        $clearedReceiptsCount = $payments->where('settlement_status', \App\Models\ClientInvoicePayment::SETTLEMENT_CLEARED)->count();

        $theme = match ($rawStatus) {
            'draft' => [
                'pageGlow' => 'rgba(148,163,184,.13)',
                'heroGlow' => 'rgba(148,163,184,.26)',
                'accent' => '#94a3b8',
                'accentSoft' => 'rgba(148,163,184,.16)',
            ],
            'approved' => [
                'pageGlow' => 'rgba(59,130,246,.14)',
                'heroGlow' => 'rgba(59,130,246,.28)',
                'accent' => '#60a5fa',
                'accentSoft' => 'rgba(96,165,250,.16)',
            ],
            'issued', 'submitted', 'sent_to_client' => [
                'pageGlow' => 'rgba(245,158,11,.16)',
                'heroGlow' => 'rgba(245,158,11,.28)',
                'accent' => '#f59e0b',
                'accentSoft' => 'rgba(245,158,11,.16)',
            ],
            'partially_paid' => [
                'pageGlow' => 'rgba(168,85,247,.16)',
                'heroGlow' => 'rgba(168,85,247,.28)',
                'accent' => '#a855f7',
                'accentSoft' => 'rgba(168,85,247,.16)',
            ],
            'paid' => [
                'pageGlow' => 'rgba(16,185,129,.16)',
                'heroGlow' => 'rgba(16,185,129,.28)',
                'accent' => '#10b981',
                'accentSoft' => 'rgba(16,185,129,.16)',
            ],
            'cancelled' => [
                'pageGlow' => 'rgba(239,68,68,.16)',
                'heroGlow' => 'rgba(239,68,68,.26)',
                'accent' => '#ef4444',
                'accentSoft' => 'rgba(239,68,68,.16)',
            ],
            default => [
                'pageGlow' => 'rgba(148,163,184,.13)',
                'heroGlow' => 'rgba(148,163,184,.26)',
                'accent' => '#94a3b8',
                'accentSoft' => 'rgba(148,163,184,.16)',
            ],
        };

        $receiptBadge = function (?string $state): array {
            return match ((string) $state) {
                'pending' => ['Pending', '#fff7ed', '#c2410c', '#fdba74'],
                'cleared' => ['Cleared', '#ecfdf5', '#047857', '#86efac'],
                'failed' => ['Failed', '#fff1f2', '#be123c', '#fda4af'],
                'reversed' => ['Reversed', '#f8fafc', '#475569', '#cbd5e1'],
                default => [ucfirst((string) $state), '#f8fafc', '#475569', '#cbd5e1'],
            };
        };

        $accountTypeBadge = function (?string $type): array {
            return match ((string) $type) {
                'clearing' => ['Clearing', '#faf5ff', '#7e22ce', '#d8b4fe'],
                'bank' => ['Bank', '#eff6ff', '#1d4ed8', '#93c5fd'],
                'cash' => ['Cash', '#ecfdf5', '#047857', '#86efac'],
                default => [ucfirst((string) $type), '#f8fafc', '#475569', '#cbd5e1'],
            };
        };
    @endphp

    <style>

        .ci-view-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 6px;
            border-radius: 34px;
            background:
                radial-gradient(circle at top right, {{ $theme['pageGlow'] }}, transparent 22%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.08), transparent 22%);
        }

        .ci-view-shell .fi-header {
            position: relative;
            overflow: hidden;
            border-radius: 30px !important;
            border: 1px solid rgba(76,167,168,.14) !important;
            background: linear-gradient(135deg, #081a34 0%, #0b2a56 52%, #0f3f48 100%) !important;
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12) !important;
            padding: 22px 24px 20px 24px !important;
            margin-bottom: 2px !important;
        }

        .ci-view-shell .fi-header::before {
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

        .ci-view-shell .fi-header::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            background: linear-gradient(90deg, #4ca7a8, {{ $theme['accent'] }});
        }

        .ci-view-shell .fi-header > * {
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
            font-size: 48px !important;
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

        .ci-view-shell .fi-header .fi-ac {
            gap: 10px !important;
        }

        .ci-view-shell .fi-header .fi-btn {
            border-radius: 999px !important;
            min-height: 44px;
            padding-inline: 16px !important;
            font-weight: 800 !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .ci-view-shell .fi-header .fi-btn-color-gray {
            background: linear-gradient(135deg, #8b95a7 0%, #667085 100%) !important;
            color: #ffffff !important;
        }

        .ci-view-shell .fi-header .fi-btn-color-info {
            background: linear-gradient(135deg, #4f86ff 0%, #3868da 100%) !important;
            color: #ffffff !important;
        }

        .ci-view-shell .fi-header .fi-btn-color-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #ea7a00 100%) !important;
            color: #1f1400 !important;
        }

        .ci-view-shell .fi-header .fi-btn-color-success {
            background: linear-gradient(135deg, #16c172 0%, #0ea95d 100%) !important;
            color: #082114 !important;
        }

        .ci-view-shell .fi-header .fi-btn-color-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: #ffffff !important;
        }

        .ci-card,
        .ci-panel {
            background: #ffffff;
            border: 1px solid #dbe7ef;
            border-radius: 26px;
            box-shadow: 0 8px 22px rgba(15,23,42,.05);
        }

        .ci-card {
            padding: 22px;
        }

        .ci-panel {
            padding: 28px;
        }

        .ci-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
            gap: 18px;
        }

        .ci-split-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 20px;
        }

        .ci-2-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .ci-label {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .ci-value {
            margin-top: 12px;
            font-size: 36px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.08;
        }

        .ci-sub {
            margin-top: 8px;
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        .ci-pill {
            display: inline-block;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .ci-pill--amber { background:#fef3c7; color:#92400e; }
        .ci-pill--blue { background:#e0f2fe; color:#0369a1; }
        .ci-pill--purple { background:#f3e8ff; color:#7e22ce; }
        .ci-pill--green { background:#dcfce7; color:#166534; }
        .ci-pill--slate { background:#f1f5f9; color:#334155; }

        .ci-chip-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ci-chip {
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 12px;
            border: 1px solid transparent;
        }

        .ci-chip--pending { background:#fff7ed; color:#c2410c; border-color:#fdba74; }
        .ci-chip--cleared { background:#ecfdf5; color:#047857; border-color:#86efac; }
        .ci-chip--info { background:#eff6ff; color:#1d4ed8; border-color:#93c5fd; }

        .ci-table-wrap {
            overflow: auto;
            margin-top: 20px;
        }

        .ci-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .ci-table th {
            text-align: left;
            padding: 10px 14px;
            color: #94a3b8;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .ci-table td {
            background: #f8fafc;
            padding: 16px 14px;
            color: #0f172a;
            vertical-align: top;
        }

        .ci-table td:first-child {
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }

        .ci-table td:last-child {
            border-top-right-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .ci-strong {
            font-weight: 900;
            color: #0f172a;
        }

        .ci-small {
            margin-top: 4px;
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
        }

        .ci-meta-grid {
            display: grid;
            grid-template-columns: repeat(2,minmax(0,1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .ci-box {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 18px;
            background: #fcfdff;
        }

        .ci-box-soft-blue { border-color:#dbeafe; background:#f8fbff; }
        .ci-box-soft-amber { border-color:#fde68a; background:#fffdf5; }
        .ci-box-soft-indigo { border-color:#c7d2fe; background:#eef2ff; }

        .ci-field-label {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .ci-field-value {
            margin-top: 8px;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.6;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .ci-field-value-lg {
            margin-top: 10px;
            font-size: 28px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.2;
        }

        .dark .ci-view-shell {
            background:
                radial-gradient(circle at top right, {{ $theme['pageGlow'] }}, transparent 22%),
                radial-gradient(circle at bottom left, rgba(76,167,168,.05), transparent 20%),
                linear-gradient(180deg, rgba(7,20,39,.96) 0%, rgba(10,24,42,.96) 100%);
        }

        .dark .ci-card,
        .dark .ci-panel {
            background: rgba(12,23,38,.96);
            border-color: rgba(76,167,168,.16);
            box-shadow: 0 10px 24px rgba(0,0,0,.22);
        }

        .dark .ci-table td,
        .dark .ci-box {
            background: rgba(255,255,255,.03);
            border-color: rgba(76,167,168,.12);
        }

        .dark .ci-box-soft-blue,
        .dark .ci-box-soft-amber,
        .dark .ci-box-soft-indigo {
            background: rgba(255,255,255,.04);
        }

        .dark .ci-label,
        .dark .ci-field-label,
        .dark .ci-table th {
            color: #8ea8be;
        }

        .dark .ci-value,
        .dark .ci-strong,
        .dark .ci-field-value,
        .dark .ci-field-value-lg,
        .dark .ci-table td {
            color: #f6fbff;
        }

        .dark .ci-sub,
        .dark .ci-small {
            color: #9fb2c3;
        }

        .dark .ci-pill--amber { background: rgba(146,64,14,.22); color: #fdba74; }
        .dark .ci-pill--blue { background: rgba(3,105,161,.20); color: #7dd3fc; }
        .dark .ci-pill--purple { background: rgba(126,34,206,.20); color: #d8b4fe; }
        .dark .ci-pill--green { background: rgba(22,101,52,.22); color: #86efac; }
        .dark .ci-pill--slate { background: rgba(71,85,105,.22); color: #cbd5e1; }

        @media (max-width: 1100px) {
            .ci-split-grid,
            .ci-2-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .fi-header-heading {
                font-size: 36px !important;
            }

            .ci-view-shell .fi-header {
                padding: 18px 18px 16px 18px !important;
            }
        }

        /* FINAL CLIENT INVOICE VIEW - Salary Slip atmosphere */
        .ci-view-shell {
            width: 100% !important;
            max-width: 1540px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            gap: 22px !important;
        }

        .ci-view-shell .fi-header,
        .ci-view-shell .ci-hero,
        .ci-view-shell .invoice-hero {
            background:
                radial-gradient(circle at 88% 16%, {{ $theme['heroGlow'] }}, transparent 32%),
                linear-gradient(135deg, #10243d 0%, #1f4664 52%, {{ $theme['accent'] }} 145%) !important;
            color: #ffffff !important;
            border-radius: 30px !important;
            border: 1px solid rgba(255,255,255,.14) !important;
            border-bottom: 6px solid {{ $theme['accent'] }} !important;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .14) !important;
        }

        .ci-view-shell .ci-hero *,
        .ci-view-shell .invoice-hero * {
            color: #ffffff !important;
        }

        .ci-view-shell .ci-hero [class*="meta"],
        .ci-view-shell .invoice-hero [class*="meta"],
        .ci-view-shell .ci-hero small,
        .ci-view-shell .invoice-hero small {
            color: rgba(255,255,255,.78) !important;
        }

        .ci-view-shell .ci-card,
        .ci-view-shell .ci-panel,
        .ci-view-shell .ci-stat,
        .ci-view-shell .ci-section,
        .ci-view-shell .ci-block {
            border-radius: 22px !important;
            box-shadow: 0 14px 34px rgba(15,23,42,.06) !important;
        }

        /* Make Filament page header on invoice page look like Salary Slip header */
        body:has(.ci-view-shell) .fi-header {
            width: min(100% - 32px, 1540px) !important;
            max-width: 1540px !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding: 30px 34px !important;
            border-radius: 30px !important;
            background:
                radial-gradient(circle at 88% 16%, {{ $theme['heroGlow'] }}, transparent 32%),
                linear-gradient(135deg, #10243d 0%, #1f4664 52%, {{ $theme['accent'] }} 145%) !important;
            border-bottom: 6px solid {{ $theme['accent'] }} !important;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .14) !important;
            overflow: hidden !important;
        }

        body:has(.ci-view-shell) .fi-header h1,
        body:has(.ci-view-shell) .fi-header .fi-header-heading,
        body:has(.ci-view-shell) .fi-header [class*="heading"] {
            color: #ffffff !important;
            font-weight: 950 !important;
            letter-spacing: -0.04em !important;
        }

        body:has(.ci-view-shell) .fi-header .fi-header-subheading,
        body:has(.ci-view-shell) .fi-header [class*="subheading"] {
            color: rgba(255,255,255,.82) !important;
            font-weight: 700 !important;
        }

        @media (max-width: 1100px) {
            body:has(.ci-view-shell) .fi-header {
                width: min(100% - 20px, 100%) !important;
                padding: 24px 22px !important;
            }
        }


        /* FINAL: Client Invoice header action layout fix */
        .ci-view-shell .fi-header {
            min-height: auto !important;
        }

        .ci-view-shell .fi-header .fi-header-content,
        .ci-view-shell .fi-header > div {
            align-items: flex-start !important;
        }

        .ci-view-shell .fi-header-heading {
            max-width: 560px !important;
            font-size: clamp(34px, 3.4vw, 52px) !important;
            line-height: .94 !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
            white-space: normal !important;
        }

        .ci-view-shell .fi-header .fi-ac {
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 10px !important;
            max-width: 620px !important;
        }

        .ci-view-shell .fi-header .fi-btn {
            flex: 0 0 auto !important;
            min-width: fit-content !important;
            white-space: nowrap !important;
        }

        @media (max-width: 1250px) {
            .ci-view-shell .fi-header-heading {
                max-width: 100% !important;
            }

            .ci-view-shell .fi-header .fi-ac {
                justify-content: flex-start !important;
                max-width: 100% !important;
                margin-top: 14px !important;
            }
        }
        /* FINAL FIX: Client Invoice header title + actions layout */
        .ci-view-shell .fi-header {
            min-height: auto !important;
        }

        .ci-view-shell .fi-header > div,
        .ci-view-shell .fi-header .fi-header-content,
        .ci-view-shell .fi-header .fi-header-wrapper {
            min-width: 0 !important;
        }

        .ci-view-shell .fi-header-heading {
            max-width: 760px !important;
            width: auto !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
            hyphens: none !important;
            font-size: clamp(34px, 3.2vw, 52px) !important;
            line-height: .95 !important;
        }

        .ci-view-shell .fi-header-subheading {
            max-width: 760px !important;
            white-space: normal !important;
            word-break: normal !important;
        }

        .ci-view-shell .fi-header .fi-ac {
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 10px !important;
            max-width: 560px !important;
            min-width: 360px !important;
        }

        .ci-view-shell .fi-header .fi-ac .fi-btn {
            flex: 0 0 auto !important;
            white-space: nowrap !important;
        }

        @media (min-width: 1000px) {
            .ci-view-shell .fi-header .fi-ac {
                display: grid !important;
                grid-template-columns: repeat(2, max-content) !important;
                justify-content: end !important;
                align-content: center !important;
            }
        }

        @media (max-width: 1100px) {
            .ci-view-shell .fi-header-heading {
                max-width: 100% !important;
                font-size: 36px !important;
            }

            .ci-view-shell .fi-header .fi-ac {
                min-width: 0 !important;
                max-width: 100% !important;
                justify-content: flex-start !important;
            }
        }
        /* FINAL HARD FIX: invoice header should never squeeze title vertically */
        .ci-view-shell .fi-header {
            display: block !important;
            min-height: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }

        .ci-view-shell .fi-header > *,
        .ci-view-shell .fi-header [class*="heading"],
        .ci-view-shell .fi-header [class*="subheading"] {
            min-width: 0 !important;
            max-width: 100% !important;
        }

        .ci-view-shell .fi-header-heading {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            white-space: normal !important;
            word-break: keep-all !important;
            overflow-wrap: anywhere !important;
            hyphens: none !important;
            font-size: clamp(34px, 4vw, 58px) !important;
            line-height: .95 !important;
        }

        .ci-view-shell .fi-header-subheading {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: anywhere !important;
            margin-top: 12px !important;
        }

        .ci-view-shell .fi-header .fi-ac,
        .ci-view-shell .fi-header [class*="actions"] {
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: flex-start !important;
            align-items: center !important;
            gap: 10px !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            margin-top: 18px !important;
        }

        .ci-view-shell .fi-header .fi-ac .fi-btn,
        .ci-view-shell .fi-header [class*="actions"] .fi-btn {
            flex: 0 0 auto !important;
            white-space: nowrap !important;
        }

        @media (min-width: 1150px) {
            .ci-view-shell .fi-header .fi-ac,
            .ci-view-shell .fi-header [class*="actions"] {
                max-width: 760px !important;
            }
        }
        /* FINAL LAUNCH FIX: Client Invoice header layout */
        body:has(.ci-view-shell) .fi-header,
        .ci-view-shell .fi-header {
            width: 100% !important;
            max-width: 1120px !important;
            min-height: auto !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding: 30px 32px !important;
            overflow: hidden !important;
        }

        body:has(.ci-view-shell) .fi-header > div,
        .ci-view-shell .fi-header > div,
        .ci-view-shell .fi-header .fi-header-content,
        .ci-view-shell .fi-header .fi-header-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto !important;
            align-items: center !important;
            gap: 24px !important;
        }

        body:has(.ci-view-shell) .fi-header-heading,
        .ci-view-shell .fi-header-heading {
            display: block !important;
            max-width: 720px !important;
            width: auto !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: anywhere !important;
            line-height: 1.04 !important;
            font-size: clamp(34px, 4.2vw, 56px) !important;
            letter-spacing: -0.045em !important;
            margin: 0 !important;
        }

        body:has(.ci-view-shell) .fi-header-subheading,
        .ci-view-shell .fi-header-subheading {
            display: block !important;
            max-width: 760px !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
            line-height: 1.55 !important;
        }

        body:has(.ci-view-shell) .fi-header .fi-ac,
        .ci-view-shell .fi-header .fi-ac {
            width: auto !important;
            max-width: 560px !important;
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 12px !important;
            overflow: visible !important;
        }

        body:has(.ci-view-shell) .fi-header .fi-ac .fi-btn,
        .ci-view-shell .fi-header .fi-ac .fi-btn {
            flex: 0 0 auto !important;
            width: auto !important;
            min-width: 128px !important;
            max-width: 240px !important;
            white-space: nowrap !important;
        }

        @media (max-width: 1000px) {
            body:has(.ci-view-shell) .fi-header > div,
            .ci-view-shell .fi-header > div,
            .ci-view-shell .fi-header .fi-header-content,
            .ci-view-shell .fi-header .fi-header-wrapper {
                grid-template-columns: 1fr !important;
            }

            body:has(.ci-view-shell) .fi-header .fi-ac,
            .ci-view-shell .fi-header .fi-ac {
                justify-content: flex-start !important;
                max-width: 100% !important;
            }

            body:has(.ci-view-shell) .fi-header-heading,
            .ci-view-shell .fi-header-heading {
                max-width: 100% !important;
                font-size: clamp(30px, 7vw, 46px) !important;
            }
        }
        /* ==========================================================
           FINAL FIX — Client Invoice View Header Layout
           Fixes invoice number breaking vertically + stretched hero
           ========================================================== */

        body:has(.ci-view-shell) .fi-header,
        .ci-view-shell .fi-header {
            width: 100% !important;
            max-width: 1180px !important;
            min-height: unset !important;
            height: auto !important;
            margin-inline: auto !important;
            padding: 26px 34px !important;
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto !important;
            align-items: center !important;
            gap: 22px !important;
            border-radius: 30px !important;
            overflow: hidden !important;
        }

        body:has(.ci-view-shell) .fi-header > div,
        .ci-view-shell .fi-header > div,
        .ci-view-shell .fi-header .fi-header-content,
        .ci-view-shell .fi-header .fi-header-wrapper {
            width: 100% !important;
            min-width: 0 !important;
            display: contents !important;
        }

        body:has(.ci-view-shell) .fi-header-heading,
        .ci-view-shell .fi-header-heading,
        body:has(.ci-view-shell) .fi-header h1,
        .ci-view-shell .fi-header h1,
        body:has(.ci-view-shell) .fi-header [class*="heading"],
        .ci-view-shell .fi-header [class*="heading"] {
            display: block !important;
            width: auto !important;
            max-width: 760px !important;
            min-width: 0 !important;
            color: #ffffff !important;
            font-size: clamp(34px, 4vw, 54px) !important;
            line-height: 1.02 !important;
            font-weight: 950 !important;
            letter-spacing: -0.045em !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
            hyphens: none !important;
            text-wrap: balance !important;
            margin: 0 !important;
        }

        body:has(.ci-view-shell) .fi-header-subheading,
        .ci-view-shell .fi-header-subheading,
        body:has(.ci-view-shell) .fi-header [class*="subheading"],
        .ci-view-shell .fi-header [class*="subheading"] {
            display: block !important;
            max-width: 760px !important;
            color: rgba(255,255,255,.86) !important;
            font-size: 15px !important;
            line-height: 1.6 !important;
            font-weight: 700 !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
            margin-top: 10px !important;
        }

        body:has(.ci-view-shell) .fi-breadcrumbs,
        .ci-view-shell .fi-breadcrumbs {
            grid-column: 1 / -1 !important;
            margin-bottom: 4px !important;
        }

        body:has(.ci-view-shell) .fi-header .fi-ac,
        .ci-view-shell .fi-header .fi-ac,
        body:has(.ci-view-shell) .fi-header [class*="actions"],
        .ci-view-shell .fi-header [class*="actions"] {
            grid-column: 2 !important;
            grid-row: 2 / span 2 !important;
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: flex-end !important;
            align-items: center !important;
            gap: 12px !important;
            max-width: 520px !important;
            min-width: 320px !important;
            overflow: visible !important;
        }

        body:has(.ci-view-shell) .fi-header .fi-btn,
        .ci-view-shell .fi-header .fi-btn {
            flex: 0 0 auto !important;
            width: auto !important;
            min-width: max-content !important;
            max-width: none !important;
            min-height: 46px !important;
            padding-inline: 18px !important;
            border-radius: 999px !important;
            white-space: nowrap !important;
        }

        @media (max-width: 1050px) {
            body:has(.ci-view-shell) .fi-header,
            .ci-view-shell .fi-header {
                grid-template-columns: 1fr !important;
                padding: 24px !important;
            }

            body:has(.ci-view-shell) .fi-header .fi-ac,
            .ci-view-shell .fi-header .fi-ac,
            body:has(.ci-view-shell) .fi-header [class*="actions"],
            .ci-view-shell .fi-header [class*="actions"] {
                grid-column: 1 !important;
                grid-row: auto !important;
                justify-content: flex-start !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            body:has(.ci-view-shell) .fi-header-heading,
            .ci-view-shell .fi-header-heading,
            body:has(.ci-view-shell) .fi-header h1,
            .ci-view-shell .fi-header h1 {
                max-width: 100% !important;
                font-size: clamp(30px, 7vw, 44px) !important;
            }
        }

    
<style>
    .sf-invoice-page-header {
        width: min(1180px, calc(100vw - 32px));
        margin: 18px auto 16px;
        border-radius: 32px;
        padding: 22px;
        background:
            radial-gradient(circle at top right, rgba(34,211,238,.18), transparent 36%),
            linear-gradient(135deg, #0f274a, #234b74 48%, #2563eb);
        box-shadow: 0 22px 60px rgba(15, 39, 67, .20);
        color: #ffffff;
        overflow: hidden;
    }

    .sf-invoice-page-header-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 18px;
    }

    .sf-invoice-page-kicker {
        display: inline-flex;
        align-items: center;
        min-height: 26px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .18);
        color: #e0f2fe;
        font-size: 10px;
        font-weight: 950;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .sf-invoice-page-title {
        margin-top: 10px;
        font-size: 30px;
        font-weight: 950;
        letter-spacing: -.04em;
        line-height: 1.08;
    }

    .sf-invoice-page-subtitle {
        margin-top: 7px;
        color: rgba(255, 255, 255, .78);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.55;
    }

    .sf-invoice-page-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 9px;
        max-width: 620px;
    }

    .sf-invoice-page-btn {
        min-height: 40px;
        padding: 0 15px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 950;
        text-decoration: none !important;
        border: 1px solid rgba(255, 255, 255, .18);
        box-shadow: 0 10px 24px rgba(15, 23, 42, .16);
        white-space: nowrap;
    }

    .sf-invoice-page-btn-gray {
        color: #ffffff !important;
        background: rgba(255, 255, 255, .12);
    }

    .sf-invoice-page-btn-yellow {
        color: #111827 !important;
        background: linear-gradient(135deg, #facc15, #f59e0b);
    }

    .sf-invoice-page-btn-blue {
        color: #ffffff !important;
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
    }

    .sf-invoice-page-btn-green {
        color: #ffffff !important;
        background: linear-gradient(135deg, #10b981, #14b8a6);
    }

    .sf-invoice-page-btn-purple {
        color: #ffffff !important;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
    }

    .sf-invoice-header-stats {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .sf-invoice-header-stat {
        border-radius: 20px;
        padding: 13px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .16);
    }

    .sf-invoice-header-stat-label {
        color: rgba(255, 255, 255, .70);
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .13em;
        text-transform: uppercase;
    }

    .sf-invoice-header-stat-value {
        margin-top: 6px;
        color: #ffffff;
        font-size: 16px;
        font-weight: 950;
    }

    @media (max-width: 980px) {
        .sf-invoice-page-header-row {
            flex-direction: column;
        }

        .sf-invoice-page-actions {
            justify-content: flex-start;
        }

        .sf-invoice-header-stats {
            grid-template-columns: 1fr;
        }
    }

<style>
</style>

</style>

</style>

    <div class="ci-view-shell">
        <section class="ci-panel">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                <div class="ci-pill ci-pill--amber">Receipts & Settlement Visibility</div>

                <div class="ci-chip-row">
                    <div class="ci-chip ci-chip--pending">Pending: {{ $pendingReceiptsCount }}</div>
                    <div class="ci-chip ci-chip--cleared">Cleared: {{ $clearedReceiptsCount }}</div>
                    <div class="ci-chip ci-chip--info">Total Receipts: {{ $payments->count() }}</div>
                </div>
            </div>

            <div class="ci-table-wrap">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Apply To</th>
                            <th>Settlement</th>
                            <th>Account Type</th>
                            <th>Treasury Account</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            @php
                                $applyTo = $payment->applies_to === \App\Models\ClientInvoicePayment::APPLIES_TO_LOCAL ? 'Local Portion' : 'Foreign Portion';
                            @endphp
                            <tr>
                                <td><div class="ci-strong">{{ $payment->payment_date?->format('Y-m-d') ?: '-' }}</div></td>
                                <td>
                                    <div class="ci-strong">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency ?: '-' }}</div>
                                    <div class="ci-small">Invoice Currency Eq.: {{ number_format((float) $payment->amount_in_invoice_currency, 2) }}</div>
                                </td>
                                <td><div class="ci-strong">{{ $applyTo }}</div></td>
                                <td>
                                    <span style="display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;background:{{ $settlementBg }};color:{{ $settlementColor }};border:1px solid {{ $settlementBorder }};font-weight:900;font-size:12px;">
                                        {{ $settlementText }}
                                    </span>
                                </td>
                                <td>
                                    <span style="display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;background:{{ $accountTypeBg }};color:{{ $accountTypeColor }};border:1px solid {{ $accountTypeBorder }};font-weight:900;font-size:12px;">
                                        {{ $accountTypeText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="ci-strong">{{ $payment->treasuryAccount?->account_name ?: '-' }}</div>
                                    <div class="ci-small">
                                        {{ $payment->treasuryAccount?->currency ?: '-' }}
                                        @if($payment->treasuryAccount?->institution_name)
                                            — {{ $payment->treasuryAccount->institution_name }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="ci-strong">{{ $payment->reference_no ?: '-' }}</div>
                                    @if($payment->notes)
                                        <div class="ci-small">{{ $payment->notes }}</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="ci-small" style="padding:18px;background:#f8fafc;border-radius:16px;">
                                    No receipts recorded for this invoice yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    @php
        $workflowStatus = (string) ($record->status ?? $invoice->status ?? '');
        $pendingReceiptsCountForActions = method_exists($record ?? null, 'payments')
            ? (int) $record->payments()
                ->where('settlement_status', \App\Models\ClientInvoicePayment::SETTLEMENT_PENDING)
                ->count()
            : 0;

        $foreignRemainingForActions = method_exists($record ?? null, 'foreignRemainingAmount')
            ? round((float) $record->foreignRemainingAmount(), 2)
            : 0;

        $localRemainingForActions = method_exists($record ?? null, 'localRemainingAmount')
            ? round((float) $record->localRemainingAmount(), 2)
            : 0;

        $invoiceHasRemainingForActions = $foreignRemainingForActions > 0.01 || $localRemainingForActions > 0.01;
    @endphp
<section class="ci-kpi-grid">
            <div class="ci-card">
                <div class="ci-label">Total Amount</div>
                <div class="ci-value">{{ number_format($grandTotal, 2) }}</div>
                <div class="ci-sub">{{ $displayCurrency }}</div>
            </div>

            <div class="ci-card">
                <div class="ci-label">Status</div>
                <div class="ci-value" style="font-size:30px;">{{ $status }}</div>
                <div class="ci-sub">Invoice workflow stage</div>
            </div>

            <div class="ci-card">
                <div class="ci-label">Invoice Date</div>
                <div class="ci-value" style="font-size:30px;">{{ $invoiceDate }}</div>
                <div class="ci-sub">Document date</div>
            </div>

            <div class="ci-card">
                <div class="ci-label">Service Period</div>
                <div class="ci-value" style="font-size:26px;">{{ $servicePeriod }}</div>
                <div class="ci-sub">Full invoice month</div>
            </div>
        </section>

        <section class="ci-split-grid">
            <div class="ci-panel">
                <div class="ci-pill ci-pill--blue">Invoice Header</div>

                <div class="ci-meta-grid">
                    <div>
                        <div class="ci-field-label">Invoice Number</div>
                        <div class="ci-field-value">{{ $invoiceNo }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Status</div>
                        <div class="ci-field-value">{{ $status }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Client</div>
                        <div class="ci-field-value">{{ $client }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Project</div>
                        <div class="ci-field-value">{{ $project }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Invoice Date</div>
                        <div class="ci-field-value">{{ $invoiceDate }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Service Period</div>
                        <div class="ci-field-value">{{ $servicePeriod }}</div>
                    </div>
                </div>
            </div>

            <div class="ci-panel">
                <div class="ci-pill ci-pill--purple">Currency Split & Totals</div>

                <div style="display:flex;flex-direction:column;gap:18px;margin-top:20px;">
                    <div class="ci-box ci-box-soft-blue">
                        <div class="ci-field-label" style="color:#2563eb;">Foreign Portion</div>
                        <div class="ci-field-value-lg">
                            {{ rtrim(rtrim(number_format($foreignPercent, 2), '0'), '.') }}% {{ $foreignCurrency }}
                        </div>
                        <div class="ci-field-value" style="color:#1e3a8a;">{{ number_format($foreignDue, 2) }} {{ $foreignCurrency }}</div>
                        <div class="ci-sub" style="margin-top:6px;">Actual foreign portion amount</div>
                    </div>

                    <div class="ci-box ci-box-soft-amber">
                        <div class="ci-field-label" style="color:#b45309;">Local Portion</div>
                        <div class="ci-field-value-lg">
                            {{ rtrim(rtrim(number_format($localPercent, 2), '0'), '.') }}% {{ $localCurrency }}
                        </div>
                        <div class="ci-field-value" style="color:#92400e;">{{ number_format($localDue, 2) }} {{ $localCurrency }}</div>
                        <div class="ci-sub" style="margin-top:6px;">Actual local portion amount</div>
                    </div>

                    <div class="ci-box ci-box-soft-indigo">
                        <div class="ci-field-label" style="color:#4338ca;">Grand Total</div>
                        <div class="ci-field-value-lg">{{ number_format($grandTotal, 2) }} {{ $displayCurrency }}</div>

                        <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div class="ci-box" style="padding:14px;">
                                <div class="ci-field-label" style="color:#4338ca;">Foreign Included</div>
                                <div class="ci-field-value">{{ number_format($foreignDue, 2) }} {{ $foreignCurrency }}</div>
                            </div>
                            <div class="ci-box" style="padding:14px;">
                                <div class="ci-field-label" style="color:#4338ca;">Local Included</div>
                                <div class="ci-field-value">{{ number_format($localDue, 2) }} {{ $localCurrency }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="ci-panel">
            <div class="ci-pill ci-pill--green">Invoice Lines</div>

            <div class="ci-table-wrap">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Employee</th>
                            <th>Position</th>
                            <th>Qty</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoice->lines as $line)
                            <tr>
                                <td><div class="ci-strong">{{ $line->service_title ?: '-' }}</div></td>
                                <td>{{ $line->candidate_name ?: '-' }}</td>
                                <td>{{ $line->position_title ?: '-' }}</td>
                                <td>{{ $line->quantity ?: '-' }}</td>
                                <td>{{ number_format((float) ($line->unit_rate ?? 0), 2) }} {{ $line->currency ?: '' }}</td>
                                <td><div class="ci-strong">{{ number_format((float) ($line->amount ?? 0), 2) }} {{ $line->currency ?: '' }}</div></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="ci-small" style="padding:18px;background:#f8fafc;border-radius:16px;">No invoice lines found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="ci-2-grid">
            <div class="ci-panel">
                <div class="ci-pill ci-pill--amber">Bank & Billing</div>

                <div class="ci-meta-grid">
                    <div>
                        <div class="ci-field-label">Bill To Name</div>
                        <div class="ci-field-value">{{ $invoice->bill_to_name ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Bill To Phone</div>
                        <div class="ci-field-value">{{ $invoice->bill_to_phone ?: '-' }}</div>
                    </div>

                    <div style="grid-column:1 / -1;">
                        <div class="ci-field-label">Bill To Address</div>
                        <div class="ci-field-value">{{ $invoice->bill_to_address ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Bank Name</div>
                        <div class="ci-field-value">{{ $invoice->bank_name ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Swift Code</div>
                        <div class="ci-field-value">{{ $invoice->swift_code ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">IBAN USD</div>
                        <div class="ci-field-value">{{ $invoice->iban_usd ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">IBAN EUR</div>
                        <div class="ci-field-value">{{ $invoice->iban_eur ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">IBAN LYD</div>
                        <div class="ci-field-value">{{ $invoice->iban_lyd ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="ci-field-label">Account No. LYD</div>
                        <div class="ci-field-value">{{ $invoice->account_number_lyd ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="ci-panel">
                <div class="ci-pill ci-pill--slate">Notes & Terms</div>

                <div style="margin-top:18px;">
                    <div class="ci-field-label">Notes</div>
                    <div class="ci-field-value" style="font-size:15px;font-weight:500;line-height:1.9;color:#475569;">{{ $invoice->notes ?: 'No notes added.' }}</div>
                </div>

                <div style="margin-top:22px;">
                    <div class="ci-field-label">Terms Text</div>
                    <div class="ci-field-value" style="font-size:15px;font-weight:500;line-height:1.9;color:#475569;">{{ $invoice->terms_text ?: 'No terms text added.' }}</div>
                </div>
            </div>
        </section>
    </div>

<style>
    /* Restored invoice workflow actions - visible pills, no dropdown */
    .sf-invoice-workflow-actions {
        width: min(1040px, calc(100vw - 64px));
        max-width: min(1040px, calc(100vw - 64px));
        margin: -12px auto 22px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .sf-invoice-state-pill {
        min-height: 42px;
        padding: 0 18px;
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
        box-shadow: 0 12px 24px rgba(15, 23, 42, .14);
        white-space: nowrap;
    }

    .sf-invoice-state-pill-blue {
        color: #ffffff !important;
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
    }

    .sf-invoice-state-pill-yellow {
        color: #111827 !important;
        background: linear-gradient(135deg, #facc15, #f59e0b);
    }

    .sf-invoice-state-pill-green {
        color: #ffffff !important;
        background: linear-gradient(135deg, #10b981, #14b8a6);
    }

    .sf-invoice-state-pill-purple {
        color: #ffffff !important;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
    }

    .sf-invoice-state-pill-red {
        color: #ffffff !important;
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .sf-invoice-state-pill-gray {
        color: #ffffff !important;
        background: linear-gradient(135deg, #475569, #334155);
    }

    @media (max-width: 980px) {
        .sf-invoice-workflow-actions {
            width: calc(100vw - 24px);
            max-width: calc(100vw - 24px);
            justify-content: flex-start;
        }
    }
</style>

</x-filament-panels::page>

<style>
    /* FINAL OVERRIDE - Invoice page header/body alignment */
    .sf-invoice-page-header,
    .sf-receipts,
    .sf-invoice-workflow-block,
    .sf-invoice-page-main,
    .sf-invoice-premium-body,
    .sf-invoice-body,
    .sf-stat-grid,
    .sf-invoice-grid,
    .sf-card,
    .sf-section {
        width: min(1040px, calc(100vw - 64px)) !important;
        max-width: min(1040px, calc(100vw - 64px)) !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .sf-invoice-page-header {
        margin-top: 28px !important;
        margin-bottom: 26px !important;
        border-radius: 30px !important;
        padding: 38px !important;
        background:
            radial-gradient(circle at top right, rgba(34, 211, 238, .10), transparent 34%),
            linear-gradient(135deg, #0b223d 0%, #12365a 48%, #245f66 100%) !important;
        border-bottom: 4px solid rgba(34, 211, 238, .55) !important;
        box-shadow: 0 24px 70px rgba(15, 39, 67, .16) !important;
    }

    .sf-invoice-page-title {
        color: #ffffff !important;
        font-size: 38px !important;
        font-weight: 950 !important;
        letter-spacing: -.055em !important;
    }

    .sf-invoice-page-subtitle {
        color: rgba(255, 255, 255, .78) !important;
        font-size: 14px !important;
    }

    .sf-invoice-header-stats {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    }

    .sf-invoice-header-stat {
        background: rgba(255, 255, 255, .11) !important;
        border: 1px solid rgba(255, 255, 255, .16) !important;
        border-radius: 18px !important;
    }

    .sf-invoice-header-stat-label,
    .sf-invoice-header-stat-value {
        color: #ffffff !important;
    }

    @media (max-width: 980px) {
        .sf-invoice-page-header,
        .sf-receipts,
        .sf-invoice-workflow-block,
        .sf-invoice-page-main,
        .sf-invoice-premium-body,
        .sf-invoice-body,
        .sf-stat-grid,
        .sf-invoice-grid,
        .sf-card,
        .sf-section {
            width: calc(100vw - 24px) !important;
            max-width: calc(100vw - 24px) !important;
        }

        .sf-invoice-page-header {
            padding: 24px !important;
        }

        .sf-invoice-header-stats {
            grid-template-columns: 1fr !important;
        }
    }
</style>
