<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employment_files', function (Blueprint $table) {
            if (! Schema::hasColumn('employment_files', 'document_status')) {
                $table->string('document_status')->nullable()->after('category');
            }

            if (! Schema::hasColumn('employment_files', 'apply_to_current_rotation')) {
                $table->boolean('apply_to_current_rotation')->default(false)->after('document_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employment_files', function (Blueprint $table) {
            if (Schema::hasColumn('employment_files', 'apply_to_current_rotation')) {
                $table->dropColumn('apply_to_current_rotation');
            }

            if (Schema::hasColumn('employment_files', 'document_status')) {
                $table->dropColumn('document_status');
            }
        });
    }
};