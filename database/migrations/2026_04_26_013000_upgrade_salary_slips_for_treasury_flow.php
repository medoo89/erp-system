<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            if (! Schema::hasColumn('salary_slips', 'treasury_account_id')) {
                $table->foreignId('treasury_account_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('treasury_accounts')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('salary_slips', 'treasury_transaction_id')) {
                $table->unsignedBigInteger('treasury_transaction_id')->nullable()->after('treasury_account_id');
            }

            if (! Schema::hasColumn('salary_slips', 'bank_sent_at')) {
                $table->timestamp('bank_sent_at')->nullable()->after('treasury_transaction_id');
            }

            if (! Schema::hasColumn('salary_slips', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('bank_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            $drops = [];

            foreach (['treasury_account_id', 'treasury_transaction_id', 'bank_sent_at', 'paid_at'] as $column) {
                if (Schema::hasColumn('salary_slips', $column)) {
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
