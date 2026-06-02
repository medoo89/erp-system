<?php

namespace App\Filament\Resources\ProjectContractDocuments\Pages;

use App\Filament\Resources\ProjectContractDocuments\ProjectContractDocumentResource;
use App\Models\ProjectContract;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectContractDocument extends EditRecord
{
    protected static string $resource = ProjectContractDocumentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $contractId = $data['project_contract_id'] ?? $this->record?->project_contract_id;

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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        $projectId = $this->record?->project_id;

        return $projectId
            ? url('/admin/projects/' . $projectId)
            : ProjectContractDocumentResource::getUrl('index');
    }
}
