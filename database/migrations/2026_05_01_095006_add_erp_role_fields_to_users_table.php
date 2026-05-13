<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'erp_role')) {
                $table->string('erp_role')->default('super_admin')->after('is_admin');
            }

            if (! Schema::hasColumn('users', 'erp_department')) {
                $table->string('erp_department')->nullable()->after('erp_role');
            }

            if (! Schema::hasColumn('users', 'can_view_finance')) {
                $table->boolean('can_view_finance')->default(false)->after('erp_department');
            }

            if (! Schema::hasColumn('users', 'can_view_recruitment')) {
                $table->boolean('can_view_recruitment')->default(false)->after('can_view_finance');
            }

            if (! Schema::hasColumn('users', 'can_view_hr')) {
                $table->boolean('can_view_hr')->default(false)->after('can_view_recruitment');
            }

            if (! Schema::hasColumn('users', 'can_view_operations')) {
                $table->boolean('can_view_operations')->default(false)->after('can_view_hr');
            }

            if (! Schema::hasColumn('users', 'can_manage_admin_settings')) {
                $table->boolean('can_manage_admin_settings')->default(false)->after('can_view_operations');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'erp_role',
                'erp_department',
                'can_view_finance',
                'can_view_recruitment',
                'can_view_hr',
                'can_view_operations',
                'can_manage_admin_settings',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
