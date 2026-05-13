<?php

namespace App\Filament\Resources\ArchivedPreEmployments\Pages;

use App\Filament\Resources\ArchivedPreEmployments\ArchivedPreEmploymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArchivedPreEmployment extends CreateRecord
{
    protected static string $resource = ArchivedPreEmploymentResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }

}
