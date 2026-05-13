<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finance_expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('finance_expenses', 'expense_scope')) {
                $table->string('expense_scope', 50)->nullable()->after('title');
            }

            if (! Schema::hasColumn('finance_expenses', 'expense_category')) {
                $table->string('expense_category', 100)->nullable()->after('expense_scope');
            }

            if (! Schema::hasColumn('finance_expenses', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('expense_category');
            }

            if (! Schema::hasColumn('finance_expenses', 'allocation_status')) {
                $table->string('allocation_status', 50)->nullable()->after('vendor_name');
            }

            if (! Schema::hasColumn('finance_expenses', 'treasury_account_id')) {
                $table->foreignId('treasury_account_id')
                    ->nullable()
                    ->after('allocation_status')
                    ->constrained('treasury_accounts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('finance_expenses', 'treasury_transaction_id')) {
                $table->unsignedBigInteger('treasury_transaction_id')->nullable()->after('treasury_account_id');
            }

            if (! Schema::hasColumn('finance_expenses', 'is_travel_expense')) {
                $table->boolean('is_travel_expense')->default(false)->after('treasury_transaction_id');
            }

            if (! Schema::hasColumn('finance_expenses', 'is_company_expense')) {
                $table->boolean('is_company_expense')->default(false)->after('is_travel_expense');
            }

            if (! Schema::hasColumn('finance_expenses', 'is_manual_expense')) {
                $table->boolean('is_manual_expense')->default(false)->after('is_company_expense');
            }
        });
    }

    public function down(): void
    {
        Schema::table('finance_expenses', function (Blueprint $table) {
            $drops = [];

            foreach ([
                'expense_scope',
                'expense_category',
                'vendor_name',
                'allocation_status',
                'treasury_account_id',
                'treasury_transaction_id',
                'is_travel_expense',
                'is_company_expense',
                'is_manual_expense',
            ] as $column) {
                if (Schema::hasColumn('finance_expenses', $column)) {
                    $drops[] = $column;
                }
            }

            if (in_array('treasury_account_id', $drops, true)) {
                try {
                    $table->dropConstrainedForeignId('treasury_account_id');
                    $drops = array_values(array_diff($drops, ['treasury_account_id']));
                } catch (\Throwable $e) {
                }
            }

            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
