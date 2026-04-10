<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_openings', function (Blueprint $table) {
            if (! Schema::hasColumn('job_openings', 'project_id')) {
                $table->foreignId('project_id')
                    ->nullable()
                    ->after('template_id')
                    ->constrained('projects')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_openings', function (Blueprint $table) {
            if (Schema::hasColumn('job_openings', 'project_id')) {
                $table->dropConstrainedForeignId('project_id');
            }
        });
    }
};