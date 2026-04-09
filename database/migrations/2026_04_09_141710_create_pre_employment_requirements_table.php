<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_employment_requirements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_employment_id')->constrained('pre_employments')->cascadeOnDelete();

            $table->string('title');
            $table->string('requirement_type')->nullable();
            $table->boolean('is_required')->default(true);

            $table->string('status')->default('pending');
            $table->date('deadline')->nullable();

            $table->text('hr_note')->nullable();
            $table->text('candidate_note')->nullable();

            $table->unsignedBigInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('status');
            $table->index('requirement_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_employment_requirements');
    }
};