<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_rotations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employment_id')
                ->constrained('employments')
                ->cascadeOnDelete();

            $table->string('rotation_label')->nullable();
            $table->string('status')->nullable();
            $table->string('rotation_pattern')->nullable();
            $table->string('travel_status')->nullable();

            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->date('mobilization_date')->nullable();
            $table->date('demobilization_date')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_current')->default(false);

            $table->timestamps();

            $table->index(['employment_id', 'is_current']);
            $table->index(['employment_id', 'from_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_rotations');
    }
};