<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('client_invoices', 'tax_recorded_amount')) {
                $table->decimal('tax_recorded_amount', 18, 2)->nullable()->after('tax_amount');
            }

            if (! Schema::hasColumn('client_invoices', 'tax_recorded_currency')) {
                $table->string('tax_recorded_currency', 10)->nullable()->after('tax_recorded_amount');
            }

            if (! Schema::hasColumn('client_invoices', 'tax_recorded_at')) {
                $table->timestamp('tax_recorded_at')->nullable()->after('tax_recorded_currency');
            }

            if (! Schema::hasColumn('client_invoices', 'tax_notes')) {
                $table->text('tax_notes')->nullable()->after('tax_recorded_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            $drops = [];

            foreach (['tax_recorded_amount', 'tax_recorded_currency', 'tax_recorded_at', 'tax_notes'] as $column) {
                if (Schema::hasColumn('client_invoices', $column)) {
                    $drops[] = $column;
                }
            }

            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
