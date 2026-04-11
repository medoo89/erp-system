<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_employment_portal_fields', function (Blueprint $table) {
            if (! Schema::hasColumn('pre_employment_portal_fields', 'document_category')) {
                $table->string('document_category')->nullable()->after('field_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_employment_portal_fields', function (Blueprint $table) {
            if (Schema::hasColumn('pre_employment_portal_fields', 'document_category')) {
                $table->dropColumn('document_category');
            }
        });
    }
};