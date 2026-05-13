<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_expenses', function (Blueprint $table) {
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

            $table->foreignId('employment_rotation_id')
                ->nullable()
                ->constrained('employment_rotations')
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

            $table->foreignId('candidate_finance_profile_id')
                ->nullable()
                ->constrained('candidate_finance_profiles')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('expense_scope')->default('ad_hoc');
            $table->string('category');
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->decimal('amount', 14, 2);
            $table->string('currency', 3);

            $table->date('expense_date')->nullable();
            $table->date('incurred_from')->nullable();
            $table->date('incurred_to')->nullable();

            $table->string('paid_by')->default('company');
            $table->string('reimbursement_status')->default('not_applicable');

            $table->boolean('is_first_mobilization')->default(false);
            $table->boolean('has_attachment')->default(false);

            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('job_application_id');
            $table->index('pre_employment_id');
            $table->index('employment_id');
            $table->index('employment_rotation_id');
            $table->index('job_id');
            $table->index('client_id');
            $table->index('project_id');
            $table->index('candidate_finance_profile_id');
            $table->index('created_by');
            $table->index('approved_by');
            $table->index('expense_scope');
            $table->index('category');
            $table->index('currency');
            $table->index('expense_date');
            $table->index('paid_by');
            $table->index('reimbursement_status');
            $table->index('is_first_mobilization');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_expenses');
    }
};