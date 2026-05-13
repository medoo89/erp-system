<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_profile_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('bank_profile_id')
                ->constrained('bank_profiles')
                ->cascadeOnDelete();

            $table->string('currency', 10);
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();

            $table->foreignId('treasury_account_id')
                ->nullable()
                ->constrained('treasury_accounts')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['bank_profile_id', 'currency'], 'bank_profile_currency_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_profile_accounts');
    }
};
