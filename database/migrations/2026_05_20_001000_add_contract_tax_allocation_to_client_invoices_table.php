<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('client_invoices')) {
            return;
        }

        Schema::table('client_invoices', function (Blueprint $table): void {
            if (! Schema::hasColumn('client_invoices', 'project_contract_id')) {
                $table->foreignId('project_contract_id')
                    ->nullable()
                    ->index()
                    ->after('project_id');
            }

            if (! Schema::hasColumn('client_invoices', 'contract_consumption_amount')) {
                $table->decimal('contract_consumption_amount', 15, 2)
                    ->default(0)
                    ->after('project_contract_id');
            }

            if (! Schema::hasColumn('client_invoices', 'contract_consumption_currency')) {
                $table->string('contract_consumption_currency', 10)
                    ->nullable()
                    ->after('contract_consumption_amount');
            }

            if (! Schema::hasColumn('client_invoices', 'contract_consumption_percent')) {
                $table->decimal('contract_consumption_percent', 10, 4)
                    ->default(0)
                    ->after('contract_consumption_currency');
            }

            if (! Schema::hasColumn('client_invoices', 'allocated_contract_tax_amount')) {
                $table->decimal('allocated_contract_tax_amount', 15, 2)
                    ->default(0)
                    ->after('contract_consumption_percent');
            }

            if (! Schema::hasColumn('client_invoices', 'allocated_contract_tax_currency')) {
                $table->string('allocated_contract_tax_currency', 10)
                    ->nullable()
                    ->after('allocated_contract_tax_amount');
            }

            if (! Schema::hasColumn('client_invoices', 'show_contract_tax_on_invoice_document')) {
                $table->boolean('show_contract_tax_on_invoice_document')
                    ->default(false)
                    ->after('allocated_contract_tax_currency');
            }
        });
    }

    public function down(): void
    {
        // Safe migration: do not drop finance data automatically.
    }
};
