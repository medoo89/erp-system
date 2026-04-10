<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code')->nullable()->unique();

            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->text('address')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->string('archive_reason')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('code');
            $table->index('is_active');
            $table->index('is_archived');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};