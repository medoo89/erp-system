<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplication extends CreateRecord
{
    protected static string $resource = JobApplicationResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('job_applications', 'create') ?? false);
    }

}
