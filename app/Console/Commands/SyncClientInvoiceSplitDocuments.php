<?php

namespace App\Console\Commands;

use App\Models\ClientInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SyncClientInvoiceSplitDocuments extends Command
{
    protected $signature = 'client-invoices:sync-split-documents {--project_id=} {--invoice_id=}';

    protected $description = 'Create/update foreign and local invoice document records from logical client invoices.';

    public function handle(): int
    {
        if (! Schema::hasTable('client_invoice_documents')) {
            $this->warn('client_invoice_documents table does not exist.');
            return self::SUCCESS;
        }

        $query = ClientInvoice::query();

        if ($projectId = $this->option('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($invoiceId = $this->option('invoice_id')) {
            $query->where('id', $invoiceId);
        }

        $count = 0;

        $query->chunkById(100, function ($invoices) use (&$count): void {
            foreach ($invoices as $invoice) {
                if (method_exists($invoice, 'syncSplitDocuments')) {
                    $invoice->syncSplitDocuments();
                    $count++;
                }
            }
        });

        $this->info("Synced split documents for {$count} invoice(s).");

        return self::SUCCESS;
    }
}
