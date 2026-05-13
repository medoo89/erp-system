<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJob extends EditRecord
{
    
    protected string $view = 'filament.resources.jobs.pages.edit-job-premium';
protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'delete')),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.jobs.pages.edit-job-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('jobs', 'edit') ?? false);
    }

}
