<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('bank_profiles', 'currency')) {
                $table->string('currency')->nullable()->change();
            }

            if (Schema::hasColumn('bank_profiles', 'account_number')) {
                $table->string('account_number')->nullable()->change();
            }

            if (Schema::hasColumn('bank_profiles', 'iban')) {
                $table->string('iban')->nullable()->change();
            }
        });

        Schema::table('bank_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('bank_profiles', 'treasury_account_id')) {
                $table->dropConstrainedForeignId('treasury_account_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('bank_profiles', 'treasury_account_id')) {
                $table->foreignId('treasury_account_id')
                    ->nullable()
                    ->constrained('treasury_accounts')
                    ->nullOnDelete();
            }
        });
    }
};
