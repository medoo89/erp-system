<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('decline_reason')
                ->nullable()
                ->after('archive_reason');

            $table->text('decline_notes')
                ->nullable()
                ->after('decline_reason');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'decline_reason',
                'decline_notes',
            ]);
        });
    }
};