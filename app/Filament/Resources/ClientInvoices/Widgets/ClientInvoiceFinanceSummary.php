<?php

namespace App\Filament\Resources\ClientInvoices\Widgets;

use App\Models\ClientInvoice;
use Filament\Widgets\Widget;

class ClientInvoiceFinanceSummary extends Widget
{
    protected string $view = 'filament.resources.client-invoices.widgets.client-invoice-finance-summary';

    public ?ClientInvoice $record = null;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $invoice = $this->record;

        if (! $invoice) {
            return ['cards' => []];
        }

        return [
            'cards' => [
                [
                    'label' => 'Total Amount',
                    'value' => number_format((float) $invoice->total_amount, 2) . ' ' . ($invoice->display_currency ?: $invoice->foreign_currency ?: ''),
                    'note' => 'Full invoice value',
                    'tone' => 'dark',
                ],
                [
                    'label' => 'Foreign Due / Paid / Remaining',
                    'value' => number_format((float) $invoice->foreign_amount_due, 2) . ' ' . ($invoice->foreign_currency ?: ''),
                    'note' => 'Paid: ' . number_format($invoice->foreignPaidAmount(), 2) . ' ' . ($invoice->foreign_currency ?: '') . ' • Remaining: ' . number_format($invoice->foreignRemainingAmount(), 2) . ' ' . ($invoice->foreign_currency ?: ''),
                    'tone' => 'info',
                ],
                [
                    'label' => 'Local Due / Paid / Remaining',
                    'value' => number_format((float) $invoice->local_amount_due, 2) . ' ' . ($invoice->local_currency ?: ''),
                    'note' => 'Paid: ' . number_format($invoice->localPaidAmount(), 2) . ' ' . ($invoice->local_currency ?: '') . ' • Remaining: ' . number_format($invoice->localRemainingAmount(), 2) . ' ' . ($invoice->local_currency ?: ''),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Payments Count',
                    'value' => number_format($invoice->payments()->count()),
                    'note' => 'Recorded payment entries',
                    'tone' => 'neutral',
                ],
            ],
        ];
    }
}