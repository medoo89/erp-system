<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_application_templates', function (Blueprint $table) {
            $table->id(); // رقم القالب
            $table->string('name'); // اسم القالب، مثال: Offshore Template
            $table->text('description')->nullable(); // وصف القالب
            $table->boolean('is_active')->default(true); // هل القالب مفعل؟
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_application_templates');
    }
};