<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treasury_operations', function (Blueprint $table) {
            if (! Schema::hasColumn('treasury_operations', 'source_account_id')) {
                $table->foreignId('source_account_id')
                    ->nullable()
                    ->after('operation_type')
                    ->constrained('treasury_accounts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('treasury_operations', 'destination_account_id')) {
                $table->foreignId('destination_account_id')
                    ->nullable()
                    ->after('source_account_id')
                    ->constrained('treasury_accounts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('treasury_operations', 'clearing_account_id')) {
                $table->foreignId('clearing_account_id')
                    ->nullable()
                    ->after('destination_account_id')
                    ->constrained('treasury_accounts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('treasury_operations', function (Blueprint $table) {
            if (Schema::hasColumn('treasury_operations', 'clearing_account_id')) {
                $table->dropConstrainedForeignId('clearing_account_id');
            }

            if (Schema::hasColumn('treasury_operations', 'destination_account_id')) {
                $table->dropConstrainedForeignId('destination_account_id');
            }

            if (Schema::hasColumn('treasury_operations', 'source_account_id')) {
                $table->dropConstrainedForeignId('source_account_id');
            }
        });
    }
};
