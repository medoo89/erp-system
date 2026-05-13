<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('invoice_date')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();

            $table->string('status')->default('draft');
            // draft, approved, issued, submitted, partially_paid, paid, cancelled

            $table->string('payment_terms_label')->nullable();

            $table->string('foreign_currency', 3)->nullable();
            $table->decimal('foreign_percentage', 8, 2)->default(100);

            $table->string('local_currency', 3)->default('LYD');
            $table->decimal('local_percentage', 8, 2)->default(0);

            $table->decimal('exchange_rate', 14, 4)->nullable();

            $table->decimal('subtotal_amount', 14, 2)->default(0);
            $table->decimal('tax_percent', 8, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            $table->decimal('foreign_amount_due', 14, 2)->default(0);
            $table->decimal('local_amount_due', 14, 2)->default(0);
            $table->decimal('local_amount_foreign_equivalent', 14, 2)->default(0);

            $table->string('display_currency', 3)->nullable();

            $table->string('bill_to_name')->nullable();
            $table->text('bill_to_address')->nullable();
            $table->string('bill_to_phone')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('account_number_lyd')->nullable();
            $table->string('iban_lyd')->nullable();
            $table->string('iban_usd')->nullable();
            $table->string('iban_eur')->nullable();

            $table->text('notes')->nullable();
            $table->longText('terms_text')->nullable();

            $table->timestamps();

            $table->index('client_id');
            $table->index('project_id');
            $table->index('invoice_date');
            $table->index('status');
            $table->index('foreign_currency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_invoices');
    }
};
