<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            if (! Schema::hasColumn('salary_slips', 'employee_confirmation_status')) {
                $table->string('employee_confirmation_status')->nullable()->after('status');
            }

            if (! Schema::hasColumn('salary_slips', 'employee_confirmation_notes')) {
                $table->text('employee_confirmation_notes')->nullable()->after('employee_confirmation_status');
            }

            if (! Schema::hasColumn('salary_slips', 'employee_confirmed_at')) {
                $table->timestamp('employee_confirmed_at')->nullable()->after('employee_confirmation_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            if (Schema::hasColumn('salary_slips', 'employee_confirmed_at')) {
                $table->dropColumn('employee_confirmed_at');
            }

            if (Schema::hasColumn('salary_slips', 'employee_confirmation_notes')) {
                $table->dropColumn('employee_confirmation_notes');
            }

            if (Schema::hasColumn('salary_slips', 'employee_confirmation_status')) {
                $table->dropColumn('employee_confirmation_status');
            }
        });
    }
};
