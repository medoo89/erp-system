<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_contract_terms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->foreignId('employment_id')
                ->nullable()
                ->constrained('employments')
                ->nullOnDelete();

            $table->string('name')->nullable();
            $table->string('billing_basis')->default('daily_rate');
            $table->decimal('client_rate', 14, 2)->nullable();
            $table->string('currency', 3)->nullable();

            $table->decimal('foreign_percentage', 8, 2)->default(100);
            $table->decimal('local_percentage', 8, 2)->default(0);
            $table->string('local_currency', 3)->default('LYD');
            $table->decimal('default_exchange_rate', 14, 4)->nullable();

            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('client_id');
            $table->index('project_id');
            $table->index('employment_id');
            $table->index('billing_basis');
            $table->index('currency');
            $table->index('is_active');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_contract_terms');
    }
};
