<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_employment_uploads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_employment_id')->constrained('pre_employments')->cascadeOnDelete();
            $table->foreignId('pre_employment_requirement_id')->nullable()->constrained('pre_employment_requirements')->nullOnDelete();

            $table->string('title')->nullable();
            $table->string('document_type')->nullable();

            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('status')->default('uploaded');
            $table->text('review_note')->nullable();

            $table->boolean('uploaded_by_candidate')->default(true);

            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('status');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_employment_uploads');
    }
};