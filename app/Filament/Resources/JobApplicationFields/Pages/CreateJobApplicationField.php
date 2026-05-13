<?php

namespace App\Filament\Resources\JobApplicationFields\Pages;

use App\Filament\Resources\JobApplicationFields\JobApplicationFieldResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplicationField extends CreateRecord
{
    
    protected string $view = 'filament.resources.job-application-fields.pages.create-job-application-field-premium';
protected static string $resource = JobApplicationFieldResource::class;

    public function getView(): string
    {
        return 'filament.resources.job-application-fields.pages.create-job-application-field-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'create') ?? false);
    }

}
