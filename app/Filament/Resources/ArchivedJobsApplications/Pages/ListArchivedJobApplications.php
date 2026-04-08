<?php

namespace App\Filament\Resources\ArchivedJobApplications\Pages;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use App\Models\JobApplication;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListArchivedJobApplications extends ListRecords
{
    protected static string $resource = ArchivedJobApplicationResource::class;

    protected function getTableQuery(): Builder
    {
        return JobApplication::query()
            ->with(['job', 'values.field'])
            ->where('is_archived', true);
    }
}