<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            if (! Schema::hasColumn('salary_slips', 'payment_total_amount')) {
                $table->decimal('payment_total_amount', 15, 2)->nullable()->after('net_amount');
            }

            if (! Schema::hasColumn('salary_slips', 'reimbursement_same_currency_total')) {
                $table->decimal('reimbursement_same_currency_total', 15, 2)->nullable()->after('payment_total_amount');
            }

            if (! Schema::hasColumn('salary_slips', 'reimbursement_converted_total')) {
                $table->decimal('reimbursement_converted_total', 15, 2)->nullable()->after('reimbursement_same_currency_total');
            }

            if (! Schema::hasColumn('salary_slips', 'reimbursement_exchange_rates')) {
                $table->json('reimbursement_exchange_rates')->nullable()->after('reimbursement_converted_total');
            }

            if (! Schema::hasColumn('salary_slips', 'reimbursement_breakdown')) {
                $table->json('reimbursement_breakdown')->nullable()->after('reimbursement_exchange_rates');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            foreach ([
                'payment_total_amount',
                'reimbursement_same_currency_total',
                'reimbursement_converted_total',
                'reimbursement_exchange_rates',
                'reimbursement_breakdown',
            ] as $column) {
                if (Schema::hasColumn('salary_slips', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
