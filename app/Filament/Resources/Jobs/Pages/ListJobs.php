<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobs extends ListRecords
{
    
    protected string $view = 'filament.resources.jobs.pages.list-jobs-premium';
protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'create')),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.jobs.pages.list-jobs-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('jobs', 'view') ?? false);
    }

}
