<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_account_id')->constrained('portal_accounts')->cascadeOnDelete();
            $table->string('event_type', 80);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('event_date')->nullable();
            $table->string('badge_status', 50)->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('visible_to_user')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['portal_account_id', 'visible_to_user']);
            $table->index(['event_type']);
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_timeline_events');
    }
};
