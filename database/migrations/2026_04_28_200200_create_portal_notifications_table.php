<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_account_id')->constrained('portal_accounts')->cascadeOnDelete();
            $table->string('category', 50);
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('action_type', 50)->nullable();
            $table->text('action_url')->nullable();
            $table->string('action_label')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();

            $table->index(['portal_account_id', 'is_read']);
            $table->index(['category']);
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_notifications');
    }
};
