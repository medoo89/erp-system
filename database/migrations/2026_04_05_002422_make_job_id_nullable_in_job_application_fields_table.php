<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 🔹 نعدل عمود job_id ليقبل null
        // 🔹 هذا مهم لأن الحقول العامة global ما ترتبطش بـ Job معينة
        Schema::table('job_application_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // 🔹 لو رجعنا migration للخلف
        // 🔹 نخلي job_id إجباري من جديد
        Schema::table('job_application_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('job_id')->nullable(false)->change();
        });
    }
};