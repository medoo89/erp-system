<?php

namespace App\Filament\Resources\PreEmployments\Pages;

use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use App\Models\PreEmployment;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPreEmployments extends ListRecords
{
    protected string $view = 'filament.resources.pre-employments.pages.list-pre-employments-premium';

    protected static string $resource = PreEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'create'))
                ->label('New Pre-Employment Record'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        /*
         * One-stage visibility rule:
         * A person must appear in only one active stage at a time.
         *
         * Active Pre-Employment list shows only real active Pre-Employment records.
         * Records returned/reopened to Job Application remain archived here and should
         * only appear again under Job Applications.
         */
        return PreEmployment::query()
            ->with(['job.project.client', 'jobApplication', 'assignedHrUser'])
            ->where('is_archived', false)
            ->where('is_declined', false)
            ->whereNull('declined_at')
            ->whereNull('converted_to_employment_at')
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('status')
                    ->orWhereNotIn('status', [
                        'returned_to_job_application',
                        'converted_to_employment',
                        'declined',
                        'archived',
                    ]);
            });
    }

    public function getView(): string
    {
        return 'filament.resources.pre-employments.pages.list-pre-employments-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'view') ?? false);
    }
}
