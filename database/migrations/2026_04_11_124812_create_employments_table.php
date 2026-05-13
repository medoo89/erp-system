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

            $table->foreignId('pre_employment_id')

                ->nullable()

                ->constrained('pre_employments')

                ->nullOnDelete();

            $table->foreignId('job_id')

                ->nullable()

                ->constrained('job_openings')

                ->nullOnDelete();

            $table->string('position_title')->nullable();

            $table->string('client_name')->nullable();

            $table->string('project_name')->nullable();

            $table->foreignId('assigned_hr_user_id')

                ->nullable()

                ->constrained('users')

                ->nullOnDelete();

            $table->string('operation_officer_name')->nullable();

            $table->string('employee_name');

            $table->string('employee_email')->nullable();

            $table->string('employee_phone')->nullable();

            $table->string('employee_code')->nullable();

            $table->string('status')->default('active');

            $table->string('current_work_status')->default('pending_mobilization');

            $table->string('rotation_status')->nullable();

            $table->string('rotation_pattern')->nullable();

            $table->string('contract_status')->nullable();

            $table->date('contract_start_date')->nullable();

            $table->date('contract_end_date')->nullable();

            $table->string('medical_status')->nullable();

            $table->date('medical_date')->nullable();

            $table->date('medical_expiry_date')->nullable();

            $table->string('visa_status')->nullable();

            $table->date('visa_issue_date')->nullable();

            $table->date('visa_expiry_date')->nullable();

            $table->string('travel_status')->nullable();

            $table->date('travel_request_date')->nullable();

            $table->date('mobilization_date')->nullable();

            $table->date('demobilization_date')->nullable();

            $table->string('work_location')->nullable();

            $table->text('notes')->nullable();

            $table->text('internal_notes')->nullable();

            $table->timestamp('converted_from_pre_employment_at')->nullable();

            $table->timestamps();

        });

    }

    public function down(): void

    {

        Schema::dropIfExists('employments');

    }

};