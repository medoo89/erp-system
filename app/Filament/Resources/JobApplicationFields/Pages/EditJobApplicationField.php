<?php

namespace App\Filament\Resources\JobApplicationFields\Pages;

use App\Filament\Resources\JobApplicationFields\JobApplicationFieldResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJobApplicationField extends EditRecord
{
    
    protected string $view = 'filament.resources.job-application-fields.pages.edit-job-application-field-premium';
protected static string $resource = JobApplicationFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('application_fields', 'delete')),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.job-application-fields.pages.edit-job-application-field-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'edit') ?? false);
    }

}
