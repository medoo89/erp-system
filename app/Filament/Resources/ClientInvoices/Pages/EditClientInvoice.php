<?php

namespace App\Filament\Resources\ClientInvoices\Pages;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditClientInvoice extends EditRecord
{
    protected static string $resource = ClientInvoiceResource::class;

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        $invoiceNo = $this->record->invoice_number ?: ('Invoice #' . $this->record->id);

        return 'Edit · ' . $invoiceNo;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $client = $this->record->client?->name ?: 'Unknown Client';
        $project = $this->record->project?->name ?: 'No Project';

        return "Client: {$client} · Project: {$project}";
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('client_invoices', 'delete')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'edit') ?? false);
    }

}
