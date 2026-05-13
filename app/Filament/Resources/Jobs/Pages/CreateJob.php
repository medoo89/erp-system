<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJob extends CreateRecord
{
    
    protected string $view = 'filament.resources.jobs.pages.create-job-premium';
protected static string $resource = JobResource::class;

    public function getView(): string
    {
        return 'filament.resources.jobs.pages.create-job-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('jobs', 'create') ?? false);
    }

}
