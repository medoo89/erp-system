<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_invoice_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_invoice_id')
                ->constrained('client_invoices')
                ->cascadeOnDelete();

            $table->foreignId('employment_id')
                ->nullable()
                ->constrained('employments')
                ->nullOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->foreignId('salary_slip_id')
                ->nullable()
                ->constrained('salary_slips')
                ->nullOnDelete();

            $table->foreignId('client_contract_term_id')
                ->nullable()
                ->constrained('client_contract_terms')
                ->nullOnDelete();

            $table->string('service_title')->nullable();
            $table->string('position_title')->nullable();
            $table->string('candidate_name')->nullable();
            $table->string('project_name')->nullable();

            $table->date('service_period_start')->nullable();
            $table->date('service_period_end')->nullable();
            $table->string('service_month_label')->nullable();

            $table->decimal('quantity', 14, 2)->default(0);
            $table->decimal('unit_rate', 14, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);

            $table->string('currency', 3)->nullable();

            $table->text('scope_description')->nullable();
            $table->text('line_notes')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index('client_invoice_id');
            $table->index('employment_id');
            $table->index('project_id');
            $table->index('salary_slip_id');
            $table->index('client_contract_term_id');
            $table->index('currency');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_invoice_lines');
    }
};
