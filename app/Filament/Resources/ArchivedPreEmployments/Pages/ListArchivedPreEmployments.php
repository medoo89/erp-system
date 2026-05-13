<?php

namespace App\Filament\Resources\ArchivedPreEmployments\Pages;

use App\Filament\Resources\ArchivedPreEmployments\ArchivedPreEmploymentResource;
use App\Models\PreEmployment;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListArchivedPreEmployments extends ListRecords
{
    
    protected string $view = 'filament.resources.archived-pre-employments.pages.list-archived-pre-employments-premium';
protected static string $resource = ArchivedPreEmploymentResource::class;

    protected function getTableQuery(): Builder
    {
        return PreEmployment::query()
            ->with(['job', 'jobApplication', 'assignedHrUser'])
            ->where(function (Builder $query) {
                $query
                    ->where('is_archived', true)
                    ->orWhere('is_declined', true)
                    ->orWhereNotNull('declined_at')
                    ->orWhereNotNull('converted_to_employment_at');
            });
    }

    public function getView(): string
    {
        return 'filament.resources.archived-pre-employments.pages.list-archived-pre-employments-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }

}
