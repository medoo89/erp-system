<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('client_invoice_documents')) {
            return;
        }

        Schema::create('client_invoice_documents', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('client_invoice_id')
                ->constrained('client_invoices')
                ->cascadeOnDelete();

            $table->string('document_type', 30)->index();
            // foreign, local

            $table->string('document_number')->nullable()->index();

            $table->string('currency', 10)->nullable();
            $table->decimal('percentage', 8, 2)->default(0);
            $table->decimal('amount', 18, 2)->default(0);

            $table->string('status', 30)->default('draft')->index();
            // draft, generated, sent, cancelled

            $table->json('snapshot')->nullable();

            $table->timestamp('generated_at')->nullable();
            $table->timestamp('sent_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['client_invoice_id', 'document_type']);
            $table->index(['client_invoice_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_invoice_documents');
    }
};
