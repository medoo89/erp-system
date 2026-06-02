<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('project_contracts')) {
            return;
        }

        Schema::table('project_contracts', function (Blueprint $table): void {
            if (! Schema::hasColumn('project_contracts', 'contract_no')) {
                $table->string('contract_no')->nullable()->after('client_id');
            }

            if (! Schema::hasColumn('project_contracts', 'title')) {
                $table->string('title')->nullable()->after('contract_no');
            }

            if (! Schema::hasColumn('project_contracts', 'version')) {
                $table->string('version', 50)->nullable()->default('V1')->after('title');
            }

            if (! Schema::hasColumn('project_contracts', 'type')) {
                $table->string('type', 80)->nullable()->default('base_contract')->after('version');
            }

            if (! Schema::hasColumn('project_contracts', 'amendment_type')) {
                $table->string('amendment_type', 80)->nullable()->default('none')->after('type');
            }

            if (! Schema::hasColumn('project_contracts', 'contract_value')) {
                $table->decimal('contract_value', 15, 2)->default(0)->after('amendment_type');
            }

            if (! Schema::hasColumn('project_contracts', 'currency')) {
                $table->string('currency', 10)->nullable()->default('EUR')->after('contract_value');
            }

            if (! Schema::hasColumn('project_contracts', 'tax_paid')) {
                $table->decimal('tax_paid', 15, 2)->default(0)->after('currency');
            }

            if (! Schema::hasColumn('project_contracts', 'tax_currency')) {
                $table->string('tax_currency', 10)->nullable()->default('LYD')->after('tax_paid');
            }

            if (! Schema::hasColumn('project_contracts', 'start_date')) {
                $table->date('start_date')->nullable()->after('tax_currency');
            }

            if (! Schema::hasColumn('project_contracts', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (! Schema::hasColumn('project_contracts', 'status')) {
                $table->string('status', 80)->nullable()->default('active')->after('end_date');
            }

            if (! Schema::hasColumn('project_contracts', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }

            if (! Schema::hasColumn('project_contracts', 'file_path')) {
                $table->string('file_path')->nullable()->after('is_active');
            }

            if (! Schema::hasColumn('project_contracts', 'notes')) {
                $table->text('notes')->nullable()->after('file_path');
            }
        });
    }

    public function down(): void
    {
        // Safe migration: do not drop data columns automatically.
    }
};
