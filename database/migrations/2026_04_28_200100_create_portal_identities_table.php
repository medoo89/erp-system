<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_account_id')->constrained('portal_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('job_application_id')->nullable();
            $table->unsignedBigInteger('pre_employment_id')->nullable();
            $table->unsignedBigInteger('employment_id')->nullable();
            $table->string('current_stage', 50)->default('pre_employment');
            $table->boolean('is_current')->default(true);
            $table->timestamp('linked_at')->nullable();
            $table->timestamp('unlinked_at')->nullable();
            $table->timestamps();

            $table->index(['portal_account_id', 'is_current']);
            $table->index(['job_application_id']);
            $table->index(['pre_employment_id']);
            $table->index(['employment_id']);
            $table->index(['current_stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_identities');
    }
};
