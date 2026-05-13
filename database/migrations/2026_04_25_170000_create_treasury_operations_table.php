
<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Support\Facades\Schema;

return new class extends Migration

{

    public function up(): void

    {

        Schema::create('treasury_operations', function (Blueprint $table) {

            $table->id();

            $table->string('operation_no')->unique();

            $table->string('operation_type', 50); // bank_to_cash, cash_to_bank, account_transfer, currency_exchange

            $table->foreignId('from_account_id')->constrained('treasury_accounts')->cascadeOnDelete();

            $table->foreignId('to_account_id')->nullable()->constrained('treasury_accounts')->nullOnDelete();

            $table->foreignId('fee_account_id')->nullable()->constrained('treasury_accounts')->nullOnDelete();

            $table->decimal('from_amount', 18, 2);

            $table->decimal('to_amount', 18, 2)->nullable();

            $table->decimal('fee_amount', 18, 2)->default(0);

            $table->string('from_currency', 10);

            $table->string('to_currency', 10)->nullable();

            $table->decimal('exchange_rate', 18, 6)->nullable();

            $table->date('operation_date');

            $table->text('description')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_posted')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

        });

    }

    public function down(): void

    {

        Schema::dropIfExists('treasury_operations');

    }

};

