<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employment_id')
                ->constrained('employments')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('category')->nullable();
            $table->date('document_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('version_no')->default(1);
            $table->boolean('is_current')->default(true);

            $table->string('file_path');
            $table->string('uploaded_by_type')->default('admin');
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['employment_id', 'category']);
            $table->index(['employment_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_files');
    }
};