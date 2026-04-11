<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employment_documents', function (Blueprint $table) {
            if (! Schema::hasColumn('employment_documents', 'docx_file_path')) {
                $table->string('docx_file_path')->nullable()->after('file_path');
            }

            if (! Schema::hasColumn('employment_documents', 'pdf_file_path')) {
                $table->string('pdf_file_path')->nullable()->after('docx_file_path');
            }

            if (! Schema::hasColumn('employment_documents', 'final_file_path')) {
                $table->string('final_file_path')->nullable()->after('pdf_file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employment_documents', function (Blueprint $table) {
            foreach ([
                'docx_file_path',
                'pdf_file_path',
                'final_file_path',
            ] as $column) {
                if (Schema::hasColumn('employment_documents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};