<?php

namespace App\Filament\Resources\ArchivedJobApplications\Pages;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use App\Models\JobApplication;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListArchivedJobApplications extends ListRecords
{
    
    protected string $view = 'filament.resources.archived-job-applications.pages.list-archived-job-applications-premium';
protected static string $resource = ArchivedJobApplicationResource::class;

    protected function getTableQuery(): Builder
    {
        return JobApplication::query()
            ->with(['job', 'values.field'])
            ->where('is_archived', true)
            ->where(function (Builder $query) {
                $query
                    ->where('archive_reason', 'declined')
                    ->orWhere('archive_reason', 'archived_manually')
                    ->orWhereNull('archive_reason');
            });
    }

    public function getView(): string
    {
        return 'filament.resources.archived-job-applications.pages.list-archived-job-applications-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }

}
