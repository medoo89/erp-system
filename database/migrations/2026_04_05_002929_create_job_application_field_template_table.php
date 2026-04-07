<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_application_field_template', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('job_application_template_id');
            $table->unsignedBigInteger('job_application_field_id');

            $table->foreign('job_application_template_id', 'jaf_template_fk')
                ->references('id')
                ->on('job_application_templates')
                ->cascadeOnDelete();

            $table->foreign('job_application_field_id', 'jaf_field_fk')
                ->references('id')
                ->on('job_application_fields')
                ->cascadeOnDelete();

            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_application_field_template');
    }
};