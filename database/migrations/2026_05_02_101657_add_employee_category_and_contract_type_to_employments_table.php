<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            if (! Schema::hasColumn('employments', 'employee_category')) {
                $table->string('employee_category')->default('operational')->after('job_id');
            }

            if (! Schema::hasColumn('employments', 'office_department')) {
                $table->string('office_department')->nullable()->after('employee_category');
            }

            if (! Schema::hasColumn('employments', 'office_employee_type')) {
                $table->string('office_employee_type')->nullable()->after('office_department');
            }

            if (! Schema::hasColumn('employments', 'contract_type')) {
                $table->string('contract_type')->nullable()->after('contract_status');
            }

            if (! Schema::hasColumn('employments', 'is_open_ended_contract')) {
                $table->boolean('is_open_ended_contract')->default(false)->after('contract_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            foreach ([
                'is_open_ended_contract',
                'contract_type',
                'office_employee_type',
                'office_department',
                'employee_category',
            ] as $column) {
                if (Schema::hasColumn('employments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
