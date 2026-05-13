<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('finance_expenses')) {
            return;
        }

        Schema::table('finance_expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('finance_expenses', 'reimbursement_required')) {
                $table->boolean('reimbursement_required')->default(false)->after('reimbursement_status');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursement_amount')) {
                $table->decimal('reimbursement_amount', 15, 2)->nullable()->after('reimbursement_required');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursement_currency')) {
                $table->string('reimbursement_currency', 10)->nullable()->after('reimbursement_amount');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursement_notes')) {
                $table->text('reimbursement_notes')->nullable()->after('reimbursement_currency');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursement_decision_by')) {
                $table->unsignedBigInteger('reimbursement_decision_by')->nullable()->after('reimbursement_notes');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursement_decision_at')) {
                $table->timestamp('reimbursement_decision_at')->nullable()->after('reimbursement_decision_by');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursed_salary_slip_id')) {
                $table->unsignedBigInteger('reimbursed_salary_slip_id')->nullable()->after('reimbursement_decision_at');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursed_at')) {
                $table->timestamp('reimbursed_at')->nullable()->after('reimbursed_salary_slip_id');
            }

            if (! Schema::hasColumn('finance_expenses', 'reimbursement_payment_method')) {
                $table->string('reimbursement_payment_method', 50)->nullable()->after('reimbursed_at');
            }

            if (! Schema::hasColumn('finance_expenses', 'candidate_submitted')) {
                $table->boolean('candidate_submitted')->default(false)->after('reimbursement_payment_method');
            }

            if (! Schema::hasColumn('finance_expenses', 'candidate_submitted_at')) {
                $table->timestamp('candidate_submitted_at')->nullable()->after('candidate_submitted');
            }

            if (! Schema::hasColumn('finance_expenses', 'receipt_file_path')) {
                $table->string('receipt_file_path')->nullable()->after('candidate_submitted_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('finance_expenses')) {
            return;
        }

        Schema::table('finance_expenses', function (Blueprint $table) {
            foreach ([
                'receipt_file_path',
                'candidate_submitted_at',
                'candidate_submitted',
                'reimbursement_payment_method',
                'reimbursed_at',
                'reimbursed_salary_slip_id',
                'reimbursement_decision_at',
                'reimbursement_decision_by',
                'reimbursement_notes',
                'reimbursement_currency',
                'reimbursement_amount',
                'reimbursement_required',
            ] as $column) {
                if (Schema::hasColumn('finance_expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
