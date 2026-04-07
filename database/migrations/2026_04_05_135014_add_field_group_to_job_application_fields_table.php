<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_application_fields', function (Blueprint $table) {
            // 🔹 نوع المجموعة التي ينتمي لها الحقل
            // 🔹 basic = الحقول الأساسية
            // 🔹 additional = الحقول الإضافية
            $table->string('field_group')
                ->default('additional')
                ->after('field_type');
        });
    }

    public function down(): void
    {
        Schema::table('job_application_fields', function (Blueprint $table) {
            // 🔹 حذف العمود لو رجعنا migration للخلف
            $table->dropColumn('field_group');
        });
    }
};