<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_employment_portal_fields', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_employment_id')
                ->constrained('pre_employments')
                ->cascadeOnDelete();

            $table->string('label');
            $table->string('field_key')->nullable();
            $table->string('field_type')->default('text');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('visible_to_candidate')->default(true);
            $table->text('instructions')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['pre_employment_id', 'sort_order']);
            $table->index(['pre_employment_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_employment_portal_fields');
    }
};