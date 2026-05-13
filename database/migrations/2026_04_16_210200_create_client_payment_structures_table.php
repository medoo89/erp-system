<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_payment_structures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->string('name')->nullable();

            $table->boolean('allow_dual_currency')->default(true);

            $table->string('foreign_currency', 3)->nullable();
            $table->decimal('foreign_percentage', 5, 2)->default(0);

            $table->decimal('lyd_percentage', 5, 2)->default(0);

            $table->string('lyd_conversion_mode')->default('fixed_contract_split');
            $table->decimal('manual_exchange_rate', 14, 4)->nullable();

            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('project_id');
            $table->index('foreign_currency');
            $table->index('effective_from');
            $table->index('effective_to');
            $table->index('is_default');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_payment_structures');
    }
};