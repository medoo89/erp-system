<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Project Contracts
|--------------------------------------------------------------------------
| Stores project-level contract values, currencies, taxes, files, versions,
| and amendments. This is the base for the contract balance workflow.
*/

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('project_contracts')) {
            Schema::create('project_contracts', function (Blueprint $table) {
                $table->id();

                $table->foreignId('client_id')->nullable()->index();
                $table->foreignId('project_id')->nullable()->index();

                $table->string('title')->nullable();
                $table->string('contract_reference')->nullable();
                $table->string('currency', 10)->default('EUR');

                $table->decimal('contract_value', 18, 2)->default(0);
                $table->decimal('tax_recorded', 18, 2)->default(0);
                $table->decimal('tax_consumed', 18, 2)->default(0);

                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();

                $table->string('status')->default('active');
                $table->string('version_label')->nullable();
                $table->string('amendment_type')->nullable();

                $table->string('contract_file_path')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
            });
        } else {
            Schema::table('project_contracts', function (Blueprint $table) {
                if (! Schema::hasColumn('project_contracts', 'client_id')) {
                    $table->foreignId('client_id')->nullable()->index();
                }

                if (! Schema::hasColumn('project_contracts', 'project_id')) {
                    $table->foreignId('project_id')->nullable()->index();
                }

                if (! Schema::hasColumn('project_contracts', 'title')) {
                    $table->string('title')->nullable();
                }

                if (! Schema::hasColumn('project_contracts', 'contract_reference')) {
                    $table->string('contract_reference')->nullable();
                }

                if (! Schema::hasColumn('project_contracts', 'currency')) {
                    $table->string('currency', 10)->default('EUR');
                }

                if (! Schema::hasColumn('project_contracts', 'contract_value')) {
                    $table->decimal('contract_value', 18, 2)->default(0);
                }

                if (! Schema::hasColumn('project_contracts', 'tax_recorded')) {
                    $table->decimal('tax_recorded', 18, 2)->default(0);
                }

                if (! Schema::hasColumn('project_contracts', 'tax_consumed')) {
                    $table->decimal('tax_consumed', 18, 2)->default(0);
                }

                if (! Schema::hasColumn('project_contracts', 'start_date')) {
                    $table->date('start_date')->nullable();
                }

                if (! Schema::hasColumn('project_contracts', 'end_date')) {
                    $table->date('end_date')->nullable();
                }

                if (! Schema::hasColumn('project_contracts', 'status')) {
                    $table->string('status')->default('active');
                }

                if (! Schema::hasColumn('project_contracts', 'version_label')) {
                    $table->string('version_label')->nullable();
                }

                if (! Schema::hasColumn('project_contracts', 'amendment_type')) {
                    $table->string('amendment_type')->nullable();
                }

                if (! Schema::hasColumn('project_contracts', 'contract_file_path')) {
                    $table->string('contract_file_path')->nullable();
                }

                if (! Schema::hasColumn('project_contracts', 'notes')) {
                    $table->text('notes')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_contracts');
    }
};
