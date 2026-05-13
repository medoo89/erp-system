<?php

namespace App\Filament\Resources\TreasuryAccounts\Pages;

use App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTreasuryAccount extends CreateRecord
{
    protected static string $resource = TreasuryAccountResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'create') ?? false);
    }

}
