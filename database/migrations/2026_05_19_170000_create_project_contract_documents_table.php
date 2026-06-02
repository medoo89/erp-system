<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_contract_documents')) {
            return;
        }

        Schema::create('project_contract_documents', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('project_contract_id')->nullable()->index();
            $table->foreignId('project_id')->nullable()->index();
            $table->foreignId('client_id')->nullable()->index();

            $table->string('document_type', 80)->default('agreement')->index();
            $table->string('title')->nullable();
            $table->string('document_no')->nullable();

            $table->json('file_paths')->nullable();

            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 10)->nullable();

            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_contract_documents');
    }
};
