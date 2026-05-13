<?php

namespace App\Filament\Resources\ArchivedJobOpenings\Pages;

use App\Filament\Resources\ArchivedJobOpenings\ArchivedJobOpeningResource;
use Filament\Resources\Pages\ListRecords;

class ListArchivedJobOpenings extends ListRecords
{
    protected static string $resource = ArchivedJobOpeningResource::class;

    protected string $view = 'filament.resources.archived-job-openings.pages.list-archived-job-openings-premium';

    public function getTitle(): string
    {
        return 'Archived Job Openings';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }
}
