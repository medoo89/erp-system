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
                $table->string('request_type')->nullable()->after('field_type');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'document_to_sign_path')) {
                $table->string('document_to_sign_path')->nullable()->after('document_category');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'document_to_sign_original_name')) {
                $table->string('document_to_sign_original_name')->nullable()->after('document_to_sign_path');
            }

            if (! Schema::hasColumn('pre_employment_portal_fields', 'signed_file_required')) {
                $table->boolean('signed_file_required')->default(false)->after('document_to_sign_original_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_employment_portal_fields', function (Blueprint $table) {
            foreach ([
                'signed_file_required',
                'document_to_sign_original_name',
                'document_to_sign_path',
                'request_type',
            ] as $column) {
                if (Schema::hasColumn('pre_employment_portal_fields', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
