<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->string('name');
            $table->string('code')->nullable()->unique();

            $table->string('location')->nullable();

            $table->string('site_type')->nullable();
            $table->string('status')->default('active');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->string('archive_reason')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('code');
            $table->index('status');
            $table->index('is_active');
            $table->index('is_archived');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};