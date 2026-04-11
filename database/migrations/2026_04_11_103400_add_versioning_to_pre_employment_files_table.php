<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_employment_files', function (Blueprint $table) {
            if (! Schema::hasColumn('pre_employment_files', 'document_date')) {
                $table->date('document_date')->nullable()->after('category');
            }

            if (! Schema::hasColumn('pre_employment_files', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('document_date');
            }

            if (! Schema::hasColumn('pre_employment_files', 'version_no')) {
                $table->unsignedInteger('version_no')->default(1)->after('expiry_date');
            }

            if (! Schema::hasColumn('pre_employment_files', 'is_current')) {
                $table->boolean('is_current')->default(true)->after('version_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_employment_files', function (Blueprint $table) {
            foreach ([
                'document_date',
                'expiry_date',
                'version_no',
                'is_current',
            ] as $column) {
                if (Schema::hasColumn('pre_employment_files', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};