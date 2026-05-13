<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('salary_slip_days')) {
            return;
        }

        Schema::table('salary_slip_days', function (Blueprint $table) {
            if (! Schema::hasColumn('salary_slip_days', 'is_extra_day')) {
                $table->boolean('is_extra_day')->default(false)->after('is_paid_day');
            }

            if (! Schema::hasColumn('salary_slip_days', 'day_type')) {
                $table->string('day_type')->nullable()->after('is_extra_day');
            }

            if (! Schema::hasColumn('salary_slip_days', 'pay_multiplier')) {
                $table->decimal('pay_multiplier', 8, 2)->default(1)->after('day_type');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('salary_slip_days')) {
            return;
        }

        Schema::table('salary_slip_days', function (Blueprint $table) {
            if (Schema::hasColumn('salary_slip_days', 'pay_multiplier')) {
                $table->dropColumn('pay_multiplier');
            }

            if (Schema::hasColumn('salary_slip_days', 'day_type')) {
                $table->dropColumn('day_type');
            }

            if (Schema::hasColumn('salary_slip_days', 'is_extra_day')) {
                $table->dropColumn('is_extra_day');
            }
        });
    }
};
