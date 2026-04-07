<?php

namespace App\Filament\Resources\JobApplicationFields\Pages;

use App\Filament\Resources\JobApplicationFields\JobApplicationFieldResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobApplicationFields extends ListRecords
{
    protected static string $resource = JobApplicationFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
