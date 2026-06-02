<?php

namespace App\Console\Commands;

use App\Models\ClientInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class RefreshClientInvoiceContractTaxAllocations extends Command
{
    protected $signature = 'client-invoices:refresh-contract-tax {--project_id=} {--contract_id=}';

    protected $description = 'Recalculate internal contract tax allocation for client invoices linked to project contracts.';

    public function handle(): int
    {
        if (! Schema::hasTable('client_invoices') || ! Schema::hasColumn('client_invoices', 'project_contract_id')) {
            $this->warn('client_invoices.project_contract_id does not exist.');
            return self::SUCCESS;
        }

        $query = ClientInvoice::query()
            ->whereNotNull('project_contract_id');

        if ($projectId = $this->option('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($contractId = $this->option('contract_id')) {
            $query->where('project_contract_id', $contractId);
        }

        $count = 0;

        $query->chunkById(100, function ($invoices) use (&$count): void {
            foreach ($invoices as $invoice) {
                if (method_exists($invoice, 'recalculateContractTaxAllocationQuietly')) {
                    $invoice->recalculateContractTaxAllocationQuietly();
                    $count++;
                }
            }
        });

        $this->info("Refreshed {$count} invoice(s).");

        return self::SUCCESS;
    }
}
