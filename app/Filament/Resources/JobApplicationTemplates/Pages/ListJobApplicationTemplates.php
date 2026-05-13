<?php

namespace App\Filament\Resources\JobApplicationTemplates\Pages;

use App\Filament\Resources\JobApplicationTemplates\JobApplicationTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobApplicationTemplates extends ListRecords
{
    
    protected string $view = 'filament.resources.job-application-templates.pages.list-job-application-templates-premium';
protected static string $resource = JobApplicationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('application_templates', 'create')),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.job-application-templates.pages.list-job-application-templates-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('application_templates', 'view') ?? false);
    }

}
