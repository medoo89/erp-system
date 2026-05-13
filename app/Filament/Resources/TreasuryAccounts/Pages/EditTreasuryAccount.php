<?php

namespace App\Filament\Resources\TreasuryAccounts\Pages;

use App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTreasuryAccount extends EditRecord
{
    protected static string $resource = TreasuryAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('treasury', 'delete')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'edit') ?? false);
    }

}
