<?php

namespace App\Filament\Resources\InvoiceProfiles\Pages;

use App\Filament\Resources\InvoiceProfiles\InvoiceProfileResource;
use App\Models\InvoiceProfile;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoiceProfile extends CreateRecord
{
    protected static string $resource = InvoiceProfileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['is_default'] ?? false) === true) {
            InvoiceProfile::query()->update(['is_default' => false]);
        }

        return $data;
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'create') ?? false);
    }

}
