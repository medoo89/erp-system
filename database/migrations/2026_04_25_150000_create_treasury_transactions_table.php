<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasury_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('transaction_no')->unique();

            $table->foreignId('treasury_account_id')
                ->constrained('treasury_accounts')
                ->cascadeOnDelete();

            $table->string('transaction_type', 50);
            $table->string('direction', 10); // in, out
            $table->decimal('amount', 18, 2);
            $table->string('currency', 10);

            $table->date('transaction_date');

            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('employment_id')->nullable()->constrained('employments')->nullOnDelete();

            $table->string('reference_type')->nullable(); // invoice, salary_slip, expense, transfer, manual
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_posted')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['transaction_type']);
            $table->index(['direction']);
            $table->index(['currency']);
            $table->index(['transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasury_transactions');
    }
};
