<?php

namespace App\Filament\Resources\TreasuryOperations\Pages;

use App\Filament\Resources\TreasuryOperations\TreasuryOperationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTreasuryOperation extends EditRecord
{
    protected static string $resource = TreasuryOperationResource::class;

    public function getHeading(): string
    {
        return '';
    }


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
