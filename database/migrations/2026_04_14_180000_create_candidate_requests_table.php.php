<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_application_id')
                ->constrained('job_applications')
                ->cascadeOnDelete();

            $table->string('type');
            $table->string('title');
            $table->text('notes')->nullable();

            $table->string('request_status')->default('pending');
            $table->date('due_date')->nullable();

            $table->boolean('requires_upload')->default(false);
            $table->string('requested_file_label')->nullable();
            $table->boolean('allow_multiple_files')->default(false);

            $table->decimal('proposed_salary', 12, 2)->nullable();
            $table->string('currency', 10)->nullable();

            $table->boolean('requires_approval')->default(false);

            $table->string('candidate_response')->nullable();
            $table->decimal('candidate_counter_offer', 12, 2)->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_requests');
    }
};