<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->string('salary_basis')->nullable()->after('rotation_pattern');
            // daily_rate, monthly

            $table->decimal('daily_rate', 14, 2)->nullable()->after('salary_basis');
            $table->decimal('monthly_salary', 14, 2)->nullable()->after('daily_rate');
            $table->string('salary_currency', 3)->nullable()->after('monthly_salary');

            $table->foreignId('source_candidate_request_id')
                ->nullable()
                ->after('salary_currency')
                ->constrained('candidate_requests')
                ->nullOnDelete();

            $table->text('salary_notes')->nullable()->after('source_candidate_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('source_candidate_request_id');
            $table->dropColumn([
                'salary_basis',
                'daily_rate',
                'monthly_salary',
                'salary_currency',
                'salary_notes',
            ]);
        });
    }
};