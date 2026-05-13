<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Resources\Pages\ListRecords;

class ListJobApplications extends ListRecords
{
    
    protected string $view = 'filament.resources.job-applications.pages.list-job-applications-premium';
protected static string $resource = JobApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getView(): string
    {
        return 'filament.resources.job-applications.pages.list-job-applications-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('job_applications', 'view') ?? false);
    }

}
