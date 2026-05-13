<?php

namespace App\Filament\Resources\PreEmployments\Pages;

use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePreEmployment extends CreateRecord
{
    
    protected string $view = 'filament.resources.pre-employments.pages.create-pre-employment-premium';
protected static string $resource = PreEmploymentResource::class;

    public function getView(): string
    {
        return 'filament.resources.pre-employments.pages.create-pre-employment-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'create') ?? false);
    }

}
