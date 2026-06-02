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

        Schema::table('finance_expenses', function (Blueprint $table): void {
            if (! Schema::hasColumn('finance_expenses', 'project_contract_id')) {
                $table->foreignId('project_contract_id')
                    ->nullable()
                    ->index()
                    ->after('project_id');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('finance_expenses')) {
            return;
        }

        if (Schema::hasColumn('finance_expenses', 'project_contract_id')) {
            Schema::table('finance_expenses', function (Blueprint $table): void {
                $table->dropColumn('project_contract_id');
            });
        }
    }
};
