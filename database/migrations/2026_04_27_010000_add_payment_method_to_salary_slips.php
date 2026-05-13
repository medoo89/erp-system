<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            if (! Schema::hasColumn('salary_slips', 'payment_method')) {
                $table->string('payment_method', 20)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            if (Schema::hasColumn('salary_slips', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
