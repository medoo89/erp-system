<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_employment_portal_fields', function (Blueprint $table) {
            if (! Schema::hasColumn('pre_employment_portal_fields', 'request_type')) {
                $table->string('request_type')->nullable()->after('document_category');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signature_status')) {
                $table->string('signature_status')->nullable()->after('request_type');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signature_source_file_path')) {
                $table->string('signature_source_file_path')->nullable()->after('signature_status');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signature_source_file_name')) {
                $table->string('signature_source_file_name')->nullable()->after('signature_source_file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_employment_portal_fields', function (Blueprint $table) {
            foreach ([
                'signature_source_file_name',
                'signature_source_file_path',
                'signature_status',
                'request_type',
            ] as $column) {
                if (Schema::hasColumn('pre_employment_portal_fields', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
