<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treasury_accounts', function (Blueprint $table) {
            if (! Schema::hasColumn('treasury_accounts', 'bank_profile_id')) {
                $table->foreignId('bank_profile_id')
                    ->nullable()
                    ->after('institution_name')
                    ->constrained('bank_profiles')
                    ->nullOnDelete();
            }
        });

        // توسيع أي enums / constraints محتملة عبر update مباشر للقيم المتوقعة في التطبيق
        // لا يوجد enum صريح ظاهر في المايغريشن الحالية، لذلك لا نحتاج SQL خاص هنا.
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
