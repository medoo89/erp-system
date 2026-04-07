<?php

namespace App\Filament\Resources\JobApplicationTemplates\Pages;

use App\Filament\Resources\JobApplicationTemplates\JobApplicationTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobApplicationTemplates extends ListRecords
{
    protected static string $resource = JobApplicationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
