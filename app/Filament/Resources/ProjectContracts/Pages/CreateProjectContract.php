<?php

namespace App\Filament\Resources\ProjectContracts\Pages;

use App\Filament\Resources\ProjectContracts\ProjectContractResource;
use App\Models\Project;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectContract extends CreateRecord
{
    protected static string $resource = ProjectContractResource::class;

    protected string $view = 'filament.resources.project-contracts.pages.create-project-contract-premium';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $projectId = $data['project_id'] ?? request()->query('project_id');

        if ($projectId) {
            $project = Project::find($projectId);

            if ($project) {
                $data['project_id'] = $project->id;
                $data['client_id'] = $project->client_id;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $projectId = $this->record?->project_id;

        return $projectId
            ? url('/admin/projects/' . $projectId)
            : ProjectContractResource::getUrl('index');
    }
}
