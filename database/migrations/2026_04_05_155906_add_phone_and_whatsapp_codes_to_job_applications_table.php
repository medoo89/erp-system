<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->string('phone_country_code')->nullable()->after('phone');
            $table->string('phone_number')->nullable()->after('phone_country_code');
            $table->string('whatsapp_country_code')->nullable()->after('phone_number');
            $table->string('whatsapp_number')->nullable()->after('whatsapp_country_code');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'phone_country_code',
                'phone_number',
                'whatsapp_country_code',
                'whatsapp_number',
            ]);
        });
    }
};