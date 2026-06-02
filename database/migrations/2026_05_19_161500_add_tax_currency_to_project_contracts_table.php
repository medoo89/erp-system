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

        if (! Schema::hasColumn('project_contracts', 'tax_currency')) {
            Schema::table('project_contracts', function (Blueprint $table): void {
                $table->string('tax_currency', 10)
                    ->nullable()
                    ->default('LYD')
                    ->after('tax_paid');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('project_contracts')) {
            return;
        }

        if (Schema::hasColumn('project_contracts', 'tax_currency')) {
            Schema::table('project_contracts', function (Blueprint $table): void {
                $table->dropColumn('tax_currency');
            });
        }
    }
};
