<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treasury_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('treasury_transactions', 'treasury_operation_id')) {
                $table->foreignId('treasury_operation_id')
                    ->nullable()
                    ->after('treasury_account_id')
                    ->constrained('treasury_operations')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('treasury_transactions', 'settlement_status')) {
                $table->string('settlement_status', 30)
                    ->default('cleared')
                    ->after('is_posted');
            }
        });
    }

    public function down(): void
    {
        Schema::table('treasury_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('treasury_transactions', 'treasury_operation_id')) {
                $table->dropConstrainedForeignId('treasury_operation_id');
            }

            if (Schema::hasColumn('treasury_transactions', 'settlement_status')) {
                $table->dropColumn('settlement_status');
            }
        });
    }
};
