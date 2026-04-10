<?php

namespace App\Filament\Resources\PreEmployments\Pages;

use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use App\Models\PreEmployment;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPreEmployments extends ListRecords
{
    protected static string $resource = PreEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Pre-Employment Record'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return PreEmployment::query()
            ->with(['job.project.client', 'jobApplication', 'assignedHrUser'])
            ->where('is_archived', false)
            ->where('is_declined', false)
            ->whereNull('declined_at')
            ->whereNull('converted_to_employment_at');
    }
}