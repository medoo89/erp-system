<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_terms_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidate_finance_profile_id')
                ->constrained('candidate_finance_profiles')
                ->cascadeOnDelete();

            $table->foreignId('job_application_id')
                ->constrained('job_applications')
                ->cascadeOnDelete();

            $table->foreignId('pre_employment_id')
                ->nullable()
                ->constrained('pre_employments')
                ->nullOnDelete();

            $table->foreignId('employment_id')
                ->nullable()
                ->constrained('employments')
                ->nullOnDelete();

            $table->foreignId('job_id')
                ->nullable()
                ->constrained('job_openings')
                ->nullOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->foreignId('source_candidate_request_id')
                ->nullable()
                ->constrained('candidate_requests')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('source_type')->nullable();
            $table->string('change_reason')->nullable();

            $table->string('salary_basis')->default('monthly');
            $table->decimal('amount', 14, 2)->nullable();
            $table->string('currency', 3)->nullable();

            $table->decimal('daily_rate', 14, 2)->nullable();
            $table->decimal('monthly_salary', 14, 2)->nullable();

            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('candidate_finance_profile_id');
            $table->index('job_application_id');
            $table->index('pre_employment_id');
            $table->index('employment_id');
            $table->index('job_id');
            $table->index('client_id');
            $table->index('project_id');
            $table->index('source_candidate_request_id');
            $table->index('created_by');
            $table->index('salary_basis');
            $table->index('currency');
            $table->index('effective_from');
            $table->index('effective_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_terms_history');
    }
};