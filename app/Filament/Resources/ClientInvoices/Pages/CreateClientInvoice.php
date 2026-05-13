<?php

namespace App\Filament\Resources\ClientInvoices\Pages;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientInvoice extends CreateRecord
{
    protected static string $resource = ClientInvoiceResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'create') ?? false);
    }

}
