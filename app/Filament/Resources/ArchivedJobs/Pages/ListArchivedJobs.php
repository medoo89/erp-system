<?php

namespace App\Filament\Resources\ArchivedJobs\Pages;

use App\Filament\Resources\ArchivedJobs\ArchivedJobResource;
use Filament\Resources\Pages\ListRecords;

class ListArchivedJobs extends ListRecords
{
    protected static string $resource = ArchivedJobResource::class;
}