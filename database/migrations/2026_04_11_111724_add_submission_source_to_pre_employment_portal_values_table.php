<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_employment_portal_values', function (Blueprint $table) {
            if (! Schema::hasColumn('pre_employment_portal_values', 'submitted_by_type')) {
                $table->string('submitted_by_type')->default('candidate')->after('value');
            }

            if (! Schema::hasColumn('pre_employment_portal_values', 'submitted_by_user_id')) {
                $table->foreignId('submitted_by_user_id')
                    ->nullable()
                    ->after('submitted_by_type')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_employment_portal_values', function (Blueprint $table) {
            if (Schema::hasColumn('pre_employment_portal_values', 'submitted_by_user_id')) {
                $table->dropConstrainedForeignId('submitted_by_user_id');
            }

            if (Schema::hasColumn('pre_employment_portal_values', 'submitted_by_type')) {
                $table->dropColumn('submitted_by_type');
            }
        });
    }
};