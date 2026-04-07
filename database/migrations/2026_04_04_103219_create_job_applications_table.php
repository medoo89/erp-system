<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')
                ->constrained('job_openings')
                ->cascadeOnDelete();

            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();

            $table->text('cover_letter')->nullable();

            $table->string('cv_path')->nullable();

            $table->string('status')->default('new');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};