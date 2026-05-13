<?php

namespace App\Filament\Resources\TreasuryOperations\Pages;

use App\Filament\Resources\TreasuryOperations\TreasuryOperationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTreasuryOperation extends CreateRecord
{
    protected static string $resource = TreasuryOperationResource::class;

    public function getHeading(): string
    {
        return '';
    }


    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'create') ?? false);
    }

}
