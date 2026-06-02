<?php

namespace App\Filament\Resources\ProjectContractDocuments\Pages;

use App\Filament\Resources\ProjectContractDocuments\ProjectContractDocumentResource;
use App\Models\ProjectContract;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectContractDocument extends CreateRecord
{
    protected static string $resource = ProjectContractDocumentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $contractId = $data['project_contract_id'] ?? request()->query('project_contract_id');

        if ($contractId) {
            $contract = ProjectContract::find($contractId);

            if ($contract) {
                $data['project_contract_id'] = $contract->id;
                $data['project_id'] = $contract->project_id;
                $data['client_id'] = $contract->client_id;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        $projectId = $this->record?->project_id;

        return $projectId
            ? url('/admin/projects/' . $projectId)
            : ProjectContractDocumentResource::getUrl('index');
    }
}
