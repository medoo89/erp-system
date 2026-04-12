<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'project_code')) {
                $table->string('project_code', 20)->nullable()->after('name');
            }
        });

        Schema::table('employments', function (Blueprint $table) {
            if (! Schema::hasColumn('employments', 'employee_code')) {
                $table->string('employee_code', 50)->nullable()->unique()->after('employee_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            if (Schema::hasColumn('employments', 'employee_code')) {
                $table->dropUnique(['employee_code']);
                $table->dropColumn('employee_code');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'project_code')) {
                $table->dropColumn('project_code');
            }
        });
    }
};