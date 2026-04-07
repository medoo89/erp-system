<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_application_field_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('job_application_fields')->cascadeOnDelete();
            $table->string('option_label');
            $table->string('option_value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_application_field_options');
    }
};