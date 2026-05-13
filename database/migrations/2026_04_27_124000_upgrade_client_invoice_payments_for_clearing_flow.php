<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invoice_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('client_invoice_payments', 'treasury_operation_id')) {
                $table->foreignId('treasury_operation_id')
                    ->nullable()
                    ->after('treasury_account_id')
                    ->constrained('treasury_operations')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('client_invoice_payments', 'bank_profile_id')) {
                $table->foreignId('bank_profile_id')
                    ->nullable()
                    ->after('treasury_transaction_id')
                    ->constrained('bank_profiles')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('client_invoice_payments', 'settlement_status')) {
                $table->string('settlement_status', 30)
                    ->default('cleared')
                    ->after('attachment_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_invoice_payments', function (Blueprint $table) {
            if (Schema::hasColumn('client_invoice_payments', 'settlement_status')) {
                $table->dropColumn('settlement_status');
            }

            if (Schema::hasColumn('client_invoice_payments', 'bank_profile_id')) {
                $table->dropConstrainedForeignId('bank_profile_id');
            }

            if (Schema::hasColumn('client_invoice_payments', 'treasury_operation_id')) {
                $table->dropConstrainedForeignId('treasury_operation_id');
            }
        });
    }
};
