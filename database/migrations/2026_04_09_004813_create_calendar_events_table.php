<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('event_type')->nullable();
            $table->text('notes')->nullable();

            $table->date('event_date');
            $table->boolean('is_all_day')->default(true);

            $table->string('color')->nullable();

            $table->string('linked_type')->nullable();
            $table->unsignedBigInteger('linked_id')->nullable();

            $table->foreignId('job_id')->nullable()->constrained('jobs')->nullOnDelete();

            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('event_date');
            $table->index('event_type');
            $table->index(['linked_type', 'linked_id']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};