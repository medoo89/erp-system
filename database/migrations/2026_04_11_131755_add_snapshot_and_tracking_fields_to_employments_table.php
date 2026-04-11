<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            if (! Schema::hasColumn('employments', 'position_title')) {
                $table->string('position_title')->nullable()->after('job_id');
            }

            if (! Schema::hasColumn('employments', 'client_name')) {
                $table->string('client_name')->nullable()->after('position_title');
            }

            if (! Schema::hasColumn('employments', 'project_name')) {
                $table->string('project_name')->nullable()->after('client_name');
            }

            if (! Schema::hasColumn('employments', 'operation_officer_name')) {
                $table->string('operation_officer_name')->nullable()->after('project_name');
            }

            if (! Schema::hasColumn('employments', 'contract_start_date')) {
                $table->date('contract_start_date')->nullable()->after('contract_status');
            }

            if (! Schema::hasColumn('employments', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable()->after('contract_start_date');
            }

            if (! Schema::hasColumn('employments', 'medical_date')) {
                $table->date('medical_date')->nullable()->after('medical_status');
            }

            if (! Schema::hasColumn('employments', 'medical_expiry_date')) {
                $table->date('medical_expiry_date')->nullable()->after('medical_date');
            }

            if (! Schema::hasColumn('employments', 'visa_issue_date')) {
                $table->date('visa_issue_date')->nullable()->after('visa_status');
            }

            if (! Schema::hasColumn('employments', 'visa_expiry_date')) {
                $table->date('visa_expiry_date')->nullable()->after('visa_issue_date');
            }

            if (! Schema::hasColumn('employments', 'travel_status')) {
                $table->string('travel_status')->nullable()->after('visa_expiry_date');
            }

            if (! Schema::hasColumn('employments', 'travel_request_date')) {
                $table->date('travel_request_date')->nullable()->after('travel_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            foreach ([
                'position_title',
                'client_name',
                'project_name',
                'operation_officer_name',
                'contract_start_date',
                'contract_end_date',
                'medical_date',
                'medical_expiry_date',
                'visa_issue_date',
                'visa_expiry_date',
                'travel_status',
                'travel_request_date',
            ] as $column) {
                if (Schema::hasColumn('employments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};