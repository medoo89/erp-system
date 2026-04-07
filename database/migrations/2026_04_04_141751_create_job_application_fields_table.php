<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_application_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_openings')->cascadeOnDelete();
            $table->string('label');
            $table->string('field_key')->unique();
            $table->string('field_type');
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_application_fields');
    }
};