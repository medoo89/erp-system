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
                $table->foreignId('source_account_id')->nullable()->constrained('treasury_accounts')->nullOnDelete();
            }

            if (! Schema::hasColumn('treasury_operations', 'destination_account_id')) {
                $table->foreignId('destination_account_id')->nullable()->constrained('treasury_accounts')->nullOnDelete();
            }

            if (! Schema::hasColumn('treasury_operations', 'clearing_account_id')) {
                $table->foreignId('clearing_account_id')->nullable()->constrained('treasury_accounts')->nullOnDelete();
            }

            if (! Schema::hasColumn('treasury_operations', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('treasury_operations', 'currency')) {
                $table->string('currency', 10)->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'fee_amount')) {
                $table->decimal('fee_amount', 15, 2)->default(0);
            }

            if (! Schema::hasColumn('treasury_operations', 'business_status')) {
                $table->string('business_status')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'settlement_status')) {
                $table->string('settlement_status')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'operation_date')) {
                $table->date('operation_date')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'cleared_at')) {
                $table->dateTime('cleared_at')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'reference_type')) {
                $table->string('reference_type')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'notes')) {
                $table->text('notes')->nullable();
            }

            if (! Schema::hasColumn('treasury_operations', 'operation_no')) {
                $table->string('operation_no')->nullable()->index();
            }

            if (! Schema::hasColumn('treasury_operations', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('treasury_operations', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('treasury_operations', function (Blueprint $table) {
            foreach ([
                'updated_by',
                'created_by',
                'clearing_account_id',
                'destination_account_id',
                'source_account_id',
            ] as $column) {
                if (Schema::hasColumn('treasury_operations', $column)) {
                    try {
                        $table->dropConstrainedForeignId($column);
                    } catch (Throwable $e) {
                        $table->dropColumn($column);
                    }
                }
            }

            foreach ([
                'amount',
                'currency',
                'fee_amount',
                'business_status',
                'settlement_status',
                'operation_date',
                'cleared_at',
                'reference_type',
                'reference_id',
                'description',
                'notes',
                'operation_no',
            ] as $column) {
                if (Schema::hasColumn('treasury_operations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
