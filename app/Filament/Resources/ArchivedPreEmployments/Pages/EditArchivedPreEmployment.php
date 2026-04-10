<?php

namespace App\Filament\Resources\ArchivedPreEmployments\Pages;

use App\Filament\Resources\ArchivedPreEmployments\ArchivedPreEmploymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArchivedPreEmployment extends EditRecord
{
    protected static string $resource = ArchivedPreEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
