<?php

namespace App\Filament\Resources\ClientInvoices\Widgets;

use App\Models\ClientInvoice;
use Filament\Widgets\Widget;

class ClientInvoiceStatsOverview extends Widget
{
    protected string $view = 'filament.resources.client-invoices.widgets.client-invoice-stats-overview';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $draftCount = ClientInvoice::query()->where('status', ClientInvoice::STATUS_DRAFT)->count();
        $approvedCount = ClientInvoice::query()->where('status', ClientInvoice::STATUS_APPROVED)->count();
        $submittedCount = ClientInvoice::query()->whereIn('status', [
            ClientInvoice::STATUS_SUBMITTED,
            ClientInvoice::STATUS_SENT_TO_CLIENT,
            ClientInvoice::STATUS_ISSUED,
        ])->count();
        $partialCount = ClientInvoice::query()->where('status', ClientInvoice::STATUS_PARTIALLY_PAID)->count();
        $paidCount = ClientInvoice::query()->where('status', ClientInvoice::STATUS_PAID)->count();

        $totalOpenAmount = (float) ClientInvoice::query()
            ->whereIn('status', [
                ClientInvoice::STATUS_DRAFT,
                ClientInvoice::STATUS_APPROVED,
                ClientInvoice::STATUS_SUBMITTED,
                ClientInvoice::STATUS_SENT_TO_CLIENT,
                ClientInvoice::STATUS_ISSUED,
                ClientInvoice::STATUS_PARTIALLY_PAID,
            ])
            ->sum('total_amount');

        return [
            'cards' => [
                [
                    'title' => 'Draft Invoices',
                    'value' => number_format($draftCount),
                    'note' => 'Invoices still in draft stage',
                    'accent' => 'slate',
                ],
                [
                    'title' => 'Approved',
                    'value' => number_format($approvedCount),
                    'note' => 'Approved and ready',
                    'accent' => 'blue',
                ],
                [
                    'title' => 'Submitted',
                    'value' => number_format($submittedCount),
                    'note' => 'Sent to client',
                    'accent' => 'amber',
                ],
                [
                    'title' => 'Partially Paid',
                    'value' => number_format($partialCount),
                    'note' => 'Partially settled',
                    'accent' => 'purple',
                ],
                [
                    'title' => 'Paid',
                    'value' => number_format($paidCount),
                    'note' => 'Fully settled invoices',
                    'accent' => 'green',
                ],
                [
                    'title' => 'Open Invoice Value',
                    'value' => number_format($totalOpenAmount, 2),
                    'note' => 'Draft + approved + submitted + partial',
                    'accent' => 'teal',
                ],
            ],
        ];
    }
}
