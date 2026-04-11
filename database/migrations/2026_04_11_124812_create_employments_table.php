<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_employment_id')->nullable()->constrained('pre_employments')->nullOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('jobs')->nullOnDelete();
            $table->foreignId('assigned_hr_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('employee_name');
            $table->string('employee_email')->nullable();
            $table->string('employee_phone')->nullable();

            $table->string('employee_code')->nullable();
            $table->string('status')->default('active');
            $table->string('current_work_status')->nullable();

            $table->string('contract_status')->nullable();
            $table->string('medical_status')->nullable();
            $table->string('visa_status')->nullable();
            $table->string('rotation_status')->nullable();

            $table->date('mobilization_date')->nullable();
            $table->date('demobilization_date')->nullable();

            $table->string('work_location')->nullable();
            $table->string('rotation_pattern')->nullable();

            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            $table->timestamp('converted_from_pre_employment_at')->nullable();

            $table->timestamps();

            $table->index(['employee_name']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};