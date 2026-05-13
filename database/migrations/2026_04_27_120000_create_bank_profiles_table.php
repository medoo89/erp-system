<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_profiles', function (Blueprint $table) {
            $table->id();

            $table->string('profile_name');
            $table->string('beneficiary_name');
            $table->string('bank_name');
            $table->string('branch_name')->nullable();
            $table->text('bank_address')->nullable();

            $table->string('currency', 10);
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('routing_code')->nullable();

            $table->foreignId('treasury_account_id')->nullable()->constrained('treasury_accounts')->nullOnDelete();

            $table->boolean('is_default_for_invoices')->default(false);
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['currency', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_profiles');
    }
};
