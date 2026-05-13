<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invoice_lines', function (Blueprint $table) {
            $table->decimal('foreign_amount', 14, 2)->default(0)->after('amount');
            $table->decimal('local_amount_foreign_equivalent', 14, 2)->default(0)->after('foreign_amount');
            $table->decimal('local_amount', 14, 2)->default(0)->after('local_amount_foreign_equivalent');
            $table->string('foreign_currency', 3)->nullable()->after('local_amount');
            $table->string('local_currency', 3)->nullable()->after('foreign_currency');
        });
    }

    public function down(): void
    {
        Schema::table('client_invoice_lines', function (Blueprint $table) {
            $table->dropColumn([
                'foreign_amount',
                'local_amount_foreign_equivalent',
                'local_amount',
                'foreign_currency',
                'local_currency',
            ]);
        });
    }
};
