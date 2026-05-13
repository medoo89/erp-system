<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_finance_profiles', function (Blueprint $table) {
            $table->string('client_billing_basis')->nullable()->after('payout_currency');
            $table->decimal('client_billing_rate', 14, 2)->nullable()->after('client_billing_basis');
            $table->string('client_billing_currency', 3)->nullable()->after('client_billing_rate');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_finance_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'client_billing_basis',
                'client_billing_rate',
                'client_billing_currency',
            ]);
        });
    }
};
