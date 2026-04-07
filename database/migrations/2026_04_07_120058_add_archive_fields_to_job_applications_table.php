<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->boolean('is_archived')
                ->default(false)
                ->after('status');

            $table->string('archive_reason')
                ->nullable()
                ->after('is_archived');

            $table->timestamp('archived_at')
                ->nullable()
                ->after('archive_reason');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'is_archived',
                'archive_reason',
                'archived_at',
            ]);
        });
    }
};