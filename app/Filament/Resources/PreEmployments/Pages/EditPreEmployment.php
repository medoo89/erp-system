<?php

namespace App\Filament\Resources\PreEmployments\Pages;

use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPreEmployment extends EditRecord
{
    protected static string $resource = PreEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}