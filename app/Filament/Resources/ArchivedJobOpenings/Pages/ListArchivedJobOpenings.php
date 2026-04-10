<?php

namespace App\Filament\Resources\ArchivedJobOpenings\Pages;

use App\Filament\Resources\ArchivedJobOpenings\ArchivedJobOpeningResource;
use App\Models\Job;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListArchivedJobOpenings extends ListRecords
{
    protected static string $resource = ArchivedJobOpeningResource::class;

    protected function getTableQuery(): Builder
    {
        return Job::query()
            ->where('is_archived', true);
    }
}