<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treasury_accounts', function (Blueprint $table) {
            $table->string('institution_name')->nullable()->after('account_name');
            $table->string('branch_name')->nullable()->after('institution_name');
            $table->string('account_holder_name')->nullable()->after('branch_name');
            $table->string('account_number')->nullable()->after('account_holder_name');
            $table->string('iban')->nullable()->after('account_number');
            $table->string('swift_code')->nullable()->after('iban');
            $table->boolean('is_default')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('treasury_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'institution_name',
                'branch_name',
                'account_holder_name',
                'account_number',
                'iban',
                'swift_code',
                'is_default',
            ]);
        });
    }
};
