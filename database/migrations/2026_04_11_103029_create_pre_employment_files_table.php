<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_employment_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_employment_id')
                ->constrained('pre_employments')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('category')->nullable();
            $table->string('file_path');
            $table->string('uploaded_by_type')->default('admin');
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['pre_employment_id', 'category']);
            $table->index(['pre_employment_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_employment_files');
    }
};