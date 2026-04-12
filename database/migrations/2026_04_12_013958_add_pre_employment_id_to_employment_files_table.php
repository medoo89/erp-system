<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employment_files', function (Blueprint $table) {
            if (! Schema::hasColumn('employment_files', 'pre_employment_id')) {
                $table->foreignId('pre_employment_id')
                    ->nullable()
                    ->after('employment_id')
                    ->constrained('pre_employments')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employment_files', function (Blueprint $table) {
            if (Schema::hasColumn('employment_files', 'pre_employment_id')) {
                $table->dropConstrainedForeignId('pre_employment_id');
            }
        });
    }
};