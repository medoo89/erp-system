<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treasury_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('treasury_accounts', 'bank_profile_id')) {
                $table->foreignId('bank_profile_id')
                    ->nullable()
                    ->after('account_type')
                    ->constrained('bank_profiles')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('treasury_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('treasury_accounts', 'bank_profile_id')) {
                $table->dropConstrainedForeignId('bank_profile_id');
            }
        });
    }
};
