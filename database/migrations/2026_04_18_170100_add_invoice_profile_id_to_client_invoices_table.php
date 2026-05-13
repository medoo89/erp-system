<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            $table->foreignId('invoice_profile_id')
                ->nullable()
                ->after('project_id')
                ->constrained('invoice_profiles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invoice_profile_id');
        });
    }
};
