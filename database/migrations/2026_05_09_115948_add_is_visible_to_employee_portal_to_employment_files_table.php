<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employment_files')) {
            return;
        }

        Schema::table('employment_files', function (Blueprint $table) {
            if (! Schema::hasColumn('employment_files', 'is_visible_to_employee_portal')) {
                $table->boolean('is_visible_to_employee_portal')
                    ->default(true)
                    ->after('is_active');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('employment_files')) {
            return;
        }

        Schema::table('employment_files', function (Blueprint $table) {
            if (Schema::hasColumn('employment_files', 'is_visible_to_employee_portal')) {
                $table->dropColumn('is_visible_to_employee_portal');
            }
        });
    }
};
