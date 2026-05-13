<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite safe approach:
        // create temp table with correct nullable columns, copy data, drop old, rename new

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('finance_expenses', function (Blueprint $table) {
                $table->unsignedBigInteger('job_application_id')->nullable()->change();
                $table->unsignedBigInteger('pre_employment_id')->nullable()->change();
                $table->unsignedBigInteger('employment_id')->nullable()->change();
                $table->unsignedBigInteger('employment_rotation_id')->nullable()->change();
                $table->unsignedBigInteger('job_id')->nullable()->change();
                $table->unsignedBigInteger('client_id')->nullable()->change();
                $table->unsignedBigInteger('project_id')->nullable()->change();
                $table->unsignedBigInteger('candidate_finance_profile_id')->nullable()->change();
                $table->unsignedBigInteger('approved_by')->nullable()->change();
            });

            return;
        }

        Schema::create('finance_expenses_tmp', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('job_application_id')->nullable();
            $table->unsignedBigInteger('pre_employment_id')->nullable();
            $table->unsignedBigInteger('employment_id')->nullable();
            $table->unsignedBigInteger('employment_rotation_id')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('candidate_finance_profile_id')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->string('expense_scope', 50)->nullable();
            $table->string('category')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('currency', 10)->nullable();
            $table->date('expense_date')->nullable();
            $table->date('incurred_from')->nullable();
            $table->date('incurred_to')->nullable();
            $table->string('paid_by', 50)->nullable();
            $table->string('reimbursement_status', 50)->nullable();
            $table->boolean('is_first_mobilization')->default(false);
            $table->boolean('has_attachment')->default(false);
            $table->string('attachment_path')->nullable();
            $table->string('status', 50)->nullable();
            $table->text('notes')->nullable();

            // newer fields
            $table->string('expense_category', 100)->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('allocation_status', 50)->nullable();
            $table->unsignedBigInteger('treasury_account_id')->nullable();
            $table->unsignedBigInteger('treasury_transaction_id')->nullable();
            $table->boolean('is_travel_expense')->default(false);
            $table->boolean('is_company_expense')->default(false);
            $table->boolean('is_manual_expense')->default(false);

            $table->timestamps();
        });

        $columns = Schema::getColumnListing('finance_expenses');
        $copyColumns = array_values(array_intersect($columns, [
            'id',
            'job_application_id',
            'pre_employment_id',
            'employment_id',
            'employment_rotation_id',
            'job_id',
            'client_id',
            'project_id',
            'candidate_finance_profile_id',
            'created_by',
            'approved_by',
            'expense_scope',
            'category',
            'title',
            'description',
            'amount',
            'currency',
            'expense_date',
            'incurred_from',
            'incurred_to',
            'paid_by',
            'reimbursement_status',
            'is_first_mobilization',
            'has_attachment',
            'attachment_path',
            'status',
            'notes',
            'expense_category',
            'vendor_name',
            'allocation_status',
            'treasury_account_id',
            'treasury_transaction_id',
            'is_travel_expense',
            'is_company_expense',
            'is_manual_expense',
            'created_at',
            'updated_at',
        ]));

        $columnList = implode(', ', $copyColumns);

        DB::statement("INSERT INTO finance_expenses_tmp ($columnList) SELECT $columnList FROM finance_expenses");

        Schema::drop('finance_expenses');
        Schema::rename('finance_expenses_tmp', 'finance_expenses');
    }

    public function down(): void
    {
        // intentionally left simple; no rollback to NOT NULL constraints
    }
};
