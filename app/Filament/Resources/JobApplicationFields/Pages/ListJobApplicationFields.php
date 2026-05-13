<?php

namespace App\Filament\Resources\JobApplicationFields\Pages;

use App\Filament\Resources\JobApplicationFields\JobApplicationFieldResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobApplicationFields extends ListRecords
{
    
    protected string $view = 'filament.resources.job-application-fields.pages.list-job-application-fields-premium';
protected static string $resource = JobApplicationFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('application_fields', 'create')),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.job-application-fields.pages.list-job-application-fields-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'view') ?? false);
    }

}
