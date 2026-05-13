<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_profiles', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('currency', 3)->nullable();

            $table->string('bank_name')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('account_number_lyd')->nullable();
            $table->string('iban_lyd')->nullable();
            $table->string('iban_usd')->nullable();
            $table->string('iban_eur')->nullable();

            $table->longText('terms_text')->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('currency');
            $table->index('is_default');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_profiles');
    }
};
