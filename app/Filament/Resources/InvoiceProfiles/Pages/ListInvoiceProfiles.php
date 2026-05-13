<?php

namespace App\Filament\Resources\InvoiceProfiles\Pages;

use App\Filament\Resources\InvoiceProfiles\InvoiceProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceProfiles extends ListRecords
{
    protected static string $resource = InvoiceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('invoice_profiles', 'create')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'view') ?? false);
    }

}
