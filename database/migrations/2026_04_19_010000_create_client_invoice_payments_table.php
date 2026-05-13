<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_invoice_id')->constrained('client_invoices')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('exchange_rate', 18, 4)->nullable();
            $table->decimal('amount_in_invoice_currency', 18, 2)->default(0);
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['client_invoice_id', 'payment_date']);
            $table->index('currency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_invoice_payments');
    }
};
