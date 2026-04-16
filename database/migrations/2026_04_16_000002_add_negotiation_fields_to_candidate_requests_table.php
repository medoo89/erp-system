<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('candidate_requests', 'accepted_salary')) {
                $table->decimal('accepted_salary', 12, 2)->nullable()->after('candidate_counter_offer');
            }

            if (! Schema::hasColumn('candidate_requests', 'accepted_currency')) {
                $table->string('accepted_currency', 10)->nullable()->after('accepted_salary');
            }

            if (! Schema::hasColumn('candidate_requests', 'negotiation_result')) {
                $table->string('negotiation_result', 50)->nullable()->after('accepted_currency');
            }

            if (! Schema::hasColumn('candidate_requests', 'is_final_offer')) {
                $table->boolean('is_final_offer')->default(false)->after('negotiation_result');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidate_requests', function (Blueprint $table) {
            $columns = [];

            foreach (['accepted_salary', 'accepted_currency', 'negotiation_result', 'is_final_offer'] as $column) {
                if (Schema::hasColumn('candidate_requests', $column)) {
                    $columns[] = $column;
                }
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};