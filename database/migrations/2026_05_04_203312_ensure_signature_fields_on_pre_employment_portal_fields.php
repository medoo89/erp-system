<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pre_employment_portal_fields')) {
            return;
        }

        Schema::table('pre_employment_portal_fields', function (Blueprint $table) {
            if (! Schema::hasColumn('pre_employment_portal_fields', 'request_type')) {
                $table->string('request_type')->nullable()->after('document_category');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signature_status')) {
                $table->string('signature_status')->nullable()->after('request_type');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'source_file_path')) {
                $table->string('source_file_path')->nullable()->after('signature_status');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'source_original_name')) {
                $table->string('source_original_name')->nullable()->after('source_file_path');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signed_file_path')) {
                $table->string('signed_file_path')->nullable()->after('source_original_name');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signed_original_name')) {
                $table->string('signed_original_name')->nullable()->after('signed_file_path');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signed_original_name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pre_employment_portal_fields')) {
            return;
        }

        Schema::table('pre_employment_portal_fields', function (Blueprint $table) {
            foreach ([
                'signed_at',
                'signed_original_name',
                'signed_file_path',
                'source_original_name',
                'source_file_path',
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
