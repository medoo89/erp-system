<?php

namespace App\Filament\Resources\ArchivedEmployments\Pages;

use App\Filament\Resources\ArchivedEmployments\ArchivedEmploymentResource;
use Filament\Resources\Pages\ListRecords;

class ListArchivedEmployments extends ListRecords
{
    
    protected string $view = 'filament.resources.archived-employments.pages.list-archived-employments-premium';
protected static string $resource = ArchivedEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getView(): string
    {
        return 'filament.resources.archived-employments.pages.list-archived-employments-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }

}
