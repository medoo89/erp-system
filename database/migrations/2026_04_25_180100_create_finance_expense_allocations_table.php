<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_expense_allocations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('finance_expense_id')
                ->constrained('finance_expenses')
                ->cascadeOnDelete();

            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('employment_id')->nullable()->constrained('employments')->nullOnDelete();
            $table->foreignId('employment_rotation_id')->nullable()->constrained('employment_rotations')->nullOnDelete();

            $table->string('allocation_type', 50)->nullable(); // company, office, project, employment, rotation
            $table->decimal('allocated_amount', 18, 2)->default(0);
            $table->decimal('allocation_percentage', 8, 2)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_expense_allocations');
    }
};
