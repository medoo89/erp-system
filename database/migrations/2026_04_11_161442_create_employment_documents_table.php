<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employment_id')
                ->constrained('employments')
                ->cascadeOnDelete();

            $table->string('document_type'); // caf, general_letter
            $table->string('reference')->unique();

            $table->unsignedInteger('reference_year');
            $table->unsignedInteger('reference_sequence');

            $table->string('title')->nullable();
            $table->string('status')->default('draft'); // draft, generated, sent, signed, received
            $table->string('file_path')->nullable();

            $table->foreignId('generated_by_user_id')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('generated_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['employment_id', 'document_type']);
            $table->index(['document_type', 'reference_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_documents');
    }
};