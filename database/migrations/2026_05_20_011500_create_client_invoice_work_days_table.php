<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('client_invoice_work_days')) {
            return;
        }

        Schema::create('client_invoice_work_days', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('client_invoice_id')
                ->constrained('client_invoices')
                ->cascadeOnDelete();

            $table->foreignId('client_invoice_line_id')
                ->nullable()
                ->index();

            $table->foreignId('employment_id')
                ->nullable()
                ->index();

            $table->date('work_date')->index();

            $table->string('day_status', 30)->default('paid')->index();
            // paid, not_paid, absent

            $table->boolean('is_billable')->default(true);
            $table->decimal('billable_units', 8, 2)->default(1);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('client_invoice_id');
            $table->index(['client_invoice_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_invoice_work_days');
    }
};
