<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_employments', function (Blueprint $table) {
            if (! Schema::hasColumn('pre_employments', 'employee_code')) {
                $table->string('employee_code', 50)->nullable()->unique()->after('candidate_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_employments', function (Blueprint $table) {
            if (Schema::hasColumn('pre_employments', 'employee_code')) {
                $table->dropUnique(['employee_code']);
                $table->dropColumn('employee_code');
            }
        });
    }
};