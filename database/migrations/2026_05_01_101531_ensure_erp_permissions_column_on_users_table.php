<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'erp_permissions')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('erp_permissions')->nullable()->after('erp_department');
            });
        }

        if (! Schema::hasColumn('users', 'erp_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('erp_role')->default('viewer')->after('is_admin');
            });
        }

        if (! Schema::hasColumn('users', 'erp_department')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('erp_department')->nullable()->after('erp_role');
            });
        }
    }

    public function down(): void
    {
        // Keep columns intentionally. Do not drop access control data on rollback.
    }
};
