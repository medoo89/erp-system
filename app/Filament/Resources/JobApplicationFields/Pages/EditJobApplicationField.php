<?php

namespace App\Filament\Resources\JobApplicationFields\Pages;

use App\Filament\Resources\JobApplicationFields\JobApplicationFieldResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJobApplicationField extends EditRecord
{
    protected static string $resource = JobApplicationFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
