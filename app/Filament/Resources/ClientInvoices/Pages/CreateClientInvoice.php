<?php

namespace App\Filament\Resources\ClientInvoices\Pages;

use App\Models\ProjectContract;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientInvoice extends CreateRecord
{
    protected static string $resource = ClientInvoiceResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'create') ?? false);
    }


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $projectId = $data['project_id'] ?? request()->query('project_id');
        $contractId = $data['project_contract_id'] ?? request()->query('project_contract_id');

        if ($projectId) {
            $data['project_id'] = $projectId;
        }

        if ($contractId) {
            $contract = ProjectContract::find($contractId);

            if ($contract) {
                $data['project_contract_id'] = $contract->id;
                $data['project_id'] = $contract->project_id ?: ($data['project_id'] ?? null);
                $data['client_id'] = $contract->client_id ?: ($data['client_id'] ?? null);
            }
        }

        return $data;
    }

}
