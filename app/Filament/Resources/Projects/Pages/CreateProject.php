<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'create') ?? false);
    }

}
