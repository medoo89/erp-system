<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employment_rotations', function (Blueprint $table) {
            if (! Schema::hasColumn('employment_rotations', 'travel_request_file_path')) {
                $table->string('travel_request_file_path')->nullable()->after('travel_status');
            }

            if (! Schema::hasColumn('employment_rotations', 'ticket_file_path')) {
                $table->string('ticket_file_path')->nullable()->after('travel_request_file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employment_rotations', function (Blueprint $table) {
            foreach ([
                'travel_request_file_path',
                'ticket_file_path',
            ] as $column) {
                if (Schema::hasColumn('employment_rotations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};