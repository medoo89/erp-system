<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('client_invoices', 'bank_profile_id')) {
                $table->foreignId('bank_profile_id')
                    ->nullable()
                    ->after('invoice_profile_id')
                    ->constrained('bank_profiles')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('client_invoices', 'treasury_operation_id')) {
                $table->foreignId('treasury_operation_id')
                    ->nullable()
                    ->after('bank_profile_id')
                    ->constrained('treasury_operations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('client_invoices', 'treasury_operation_id')) {
                $table->dropConstrainedForeignId('treasury_operation_id');
            }

            if (Schema::hasColumn('client_invoices', 'bank_profile_id')) {
                $table->dropConstrainedForeignId('bank_profile_id');
            }
        });
    }
};
