<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_employments', function (Blueprint $table) {
            if (! Schema::hasColumn('pre_employments', 'portal_status')) {
                $table->string('portal_status')->default('not_sent')->after('portal_token');
            }

            if (! Schema::hasColumn('pre_employments', 'portal_last_sent_at')) {
                $table->timestamp('portal_last_sent_at')->nullable()->after('portal_status');
            }

            if (! Schema::hasColumn('pre_employments', 'portal_last_submitted_at')) {
                $table->timestamp('portal_last_submitted_at')->nullable()->after('portal_last_sent_at');
            }

            if (! Schema::hasColumn('pre_employments', 'caf_status')) {
                $table->string('caf_status')->nullable()->after('travel_status');
            }

            if (! Schema::hasColumn('pre_employments', 'caf_file_path')) {
                $table->string('caf_file_path')->nullable()->after('caf_status');
            }

            if (! Schema::hasColumn('pre_employments', 'gl_status')) {
                $table->string('gl_status')->nullable()->after('caf_file_path');
            }

            if (! Schema::hasColumn('pre_employments', 'gl_file_path')) {
                $table->string('gl_file_path')->nullable()->after('gl_status');
            }

            if (! Schema::hasColumn('pre_employments', 'client_tracking_notes')) {
                $table->text('client_tracking_notes')->nullable()->after('gl_file_path');
            }

            if (! Schema::hasColumn('pre_employments', 'candidate_tracking_notes')) {
                $table->text('candidate_tracking_notes')->nullable()->after('client_tracking_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_employments', function (Blueprint $table) {
            foreach ([
                'portal_status',
                'portal_last_sent_at',
                'portal_last_submitted_at',
                'caf_status',
                'caf_file_path',
                'gl_status',
                'gl_file_path',
                'client_tracking_notes',
                'candidate_tracking_notes',
            ] as $column) {
                if (Schema::hasColumn('pre_employments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};