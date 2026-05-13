<?php

namespace App\Filament\Resources\InvoiceProfiles\Pages;

use App\Filament\Resources\InvoiceProfiles\InvoiceProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoiceProfile extends ViewRecord
{
    protected static string $resource = InvoiceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('invoice_profiles', 'edit')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'view') ?? false);
    }

}
