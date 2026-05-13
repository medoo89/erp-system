<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            if (! Schema::hasColumn('salary_slips', 'addition_note')) {
                $table->text('addition_note')->nullable()->after('deductions_amount');
            }

            if (! Schema::hasColumn('salary_slips', 'deduction_note')) {
                $table->text('deduction_note')->nullable()->after('addition_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            foreach (['deduction_note', 'addition_note'] as $column) {
                if (Schema::hasColumn('salary_slips', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
