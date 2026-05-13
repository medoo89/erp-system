<?php

namespace App\Filament\Resources\InvoiceProfiles\Pages;

use App\Filament\Resources\InvoiceProfiles\InvoiceProfileResource;
use App\Models\InvoiceProfile;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoiceProfile extends EditRecord
{
    protected static string $resource = InvoiceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('invoice_profiles', 'delete')),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['is_default'] ?? false) === true) {
            InvoiceProfile::query()
                ->where('id', '!=', $this->record->id)
                ->update(['is_default' => false]);
        }

        return $data;
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'edit') ?? false);
    }

}
