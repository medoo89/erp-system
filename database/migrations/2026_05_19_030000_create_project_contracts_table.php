<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_contracts')) {
            return;
        }

        Schema::create('project_contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('parent_contract_id')->nullable()->constrained('project_contracts')->nullOnDelete();

            $table->string('contract_no')->nullable();
            $table->string('title')->nullable();
            $table->unsignedInteger('version_no')->default(1);

            $table->string('contract_kind')->default('base_contract');
            $table->string('amendment_type')->nullable();

            $table->decimal('value_amount', 18, 2)->default(0);
            $table->string('currency', 10)->default('EUR');

            $table->decimal('tax_paid_amount', 18, 2)->nullable();
            $table->string('tax_currency', 10)->nullable();
            $table->decimal('tax_percent', 8, 2)->nullable();

            $table->string('contract_file_path')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('status')->default('draft');

            $table->decimal('green_threshold_percent', 8, 2)->default(60);
            $table->decimal('yellow_threshold_percent', 8, 2)->default(80);
            $table->decimal('red_threshold_percent', 8, 2)->default(95);

            $table->boolean('is_active')->default(true);
            $table->timestamp('approved_at')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['client_id', 'project_id']);
            $table->index(['project_id', 'currency']);
            $table->index('status');
            $table->index('contract_kind');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_contracts');
    }
};
