<?php

namespace App\Filament\Resources\ProjectContracts\Pages;

use App\Filament\Resources\ProjectContracts\ProjectContractResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectContract extends EditRecord
{
    protected static string $resource = ProjectContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['project_id']) && empty($data['client_id'])) {
            $project = \App\Models\Project::find($data['project_id']);
            $data['client_id'] = $project?->client_id;
        }

        return $data;
    }
}
