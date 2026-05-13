<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employment_id')
                ->constrained('employments')
                ->cascadeOnDelete();

            $table->foreignId('job_application_id')
                ->nullable()
                ->constrained('job_applications')
                ->nullOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->foreignId('employment_rotation_id')
                ->nullable()
                ->constrained('employment_rotations')
                ->nullOnDelete();

            $table->foreignId('candidate_finance_profile_id')
                ->nullable()
                ->constrained('candidate_finance_profiles')
                ->nullOnDelete();

            $table->date('period_start');
            $table->date('period_end');

            $table->unsignedInteger('salary_year');
            $table->unsignedTinyInteger('salary_month');

            $table->decimal('days_worked', 8, 2)->default(0);

            $table->string('salary_basis');
            // daily_rate, monthly

            $table->decimal('daily_rate', 14, 2)->nullable();
            $table->decimal('monthly_salary', 14, 2)->nullable();

            $table->decimal('base_amount', 14, 2)->default(0);
            $table->decimal('adjustments_amount', 14, 2)->default(0);
            $table->decimal('deductions_amount', 14, 2)->default(0);
            $table->decimal('net_amount', 14, 2)->default(0);

            $table->string('currency', 3);

            $table->string('status')->default('draft');
            // draft, approved, locked, paid

            $table->text('notes')->nullable();

            $table->foreignId('generated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('generated_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index('employment_id');
            $table->index('job_application_id');
            $table->index('client_id');
            $table->index('project_id');
            $table->index('employment_rotation_id');
            $table->index('salary_year');
            $table->index('salary_month');
            $table->index('currency');
            $table->index('status');

            $table->unique([
                'employment_id',
                'salary_year',
                'salary_month',
                'period_start',
                'period_end',
            ], 'salary_slips_unique_period_per_employee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};