<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_finance_profiles', function (Blueprint $table) {
            $table->id();

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

            $table->string('finance_status')->default('draft');
            $table->string('salary_basis')->default('monthly');
            $table->decimal('agreed_salary_amount', 14, 2)->nullable();
            $table->string('agreed_salary_currency', 3)->nullable();

            $table->decimal('daily_rate', 14, 2)->nullable();
            $table->decimal('monthly_salary', 14, 2)->nullable();

            $table->string('payout_currency', 3)->nullable();

            $table->string('source_type')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->boolean('is_current')->default(true);
            $table->boolean('is_hidden_from_non_finance')->default(true);

            $table->text('finance_notes')->nullable();
            $table->timestamps();

            $table->index('job_application_id');
            $table->index('pre_employment_id');
            $table->index('employment_id');
            $table->index('job_id');
            $table->index('client_id');
            $table->index('project_id');
            $table->index('source_candidate_request_id');
            $table->index('finance_status');
            $table->index('salary_basis');
            $table->index('agreed_salary_currency');
            $table->index('payout_currency');
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_finance_profiles');
    }
};