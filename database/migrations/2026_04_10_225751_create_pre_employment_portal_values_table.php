<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_employment_portal_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_employment_id')
                ->constrained('pre_employments')
                ->cascadeOnDelete();

            $table->foreignId('portal_field_id')
                ->constrained('pre_employment_portal_fields')
                ->cascadeOnDelete();

            $table->longText('value')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->unique(['pre_employment_id', 'portal_field_id'], 'pre_emp_portal_unique_value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_employment_portal_values');
    }
};