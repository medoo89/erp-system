<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_employments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_application_id')->nullable()->constrained('job_applications')->nullOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('jobs')->nullOnDelete();

            $table->string('candidate_name');
            $table->string('candidate_email')->nullable();
            $table->string('candidate_phone')->nullable();

            $table->string('status')->default('initiated');

            $table->string('portal_token')->unique();

            $table->date('availability_date')->nullable();

            $table->string('expected_rate')->nullable();
            $table->string('final_rate')->nullable();

            $table->string('contract_status')->nullable();
            $table->string('medical_status')->nullable();
            $table->string('visa_status')->nullable();
            $table->string('travel_status')->nullable();

            $table->foreignId('assigned_hr_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            $table->boolean('is_declined')->default(false);
            $table->string('decline_reason')->nullable();
            $table->text('decline_notes')->nullable();
            $table->timestamp('declined_at')->nullable();

            $table->boolean('is_archived')->default(false);
            $table->string('archive_reason')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamp('converted_to_employment_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('portal_token');
            $table->index('is_declined');
            $table->index('is_archived');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_employments');
    }
};