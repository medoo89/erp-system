<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_request_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('candidate_request_id')
                ->constrained('candidate_requests')
                ->cascadeOnDelete();

            $table->string('item_type')->default('file');
            $table->string('label');
            $table->string('file_format')->nullable();

            $table->boolean('is_required')->default(true);
            $table->boolean('allow_multiple')->default(false);

            $table->text('notes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_request_items');
    }
};