<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_slip_days', function (Blueprint $table) {
            $table->id();

            $table->foreignId('salary_slip_id')
                ->constrained('salary_slips')
                ->cascadeOnDelete();

            $table->date('work_date');
            $table->string('day_name')->nullable();

            $table->string('attendance_status')->default('present');
            // present, absent, sick, leave, unpaid_leave, holiday, travel, other

            $table->boolean('is_paid_day')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('salary_slip_id');
            $table->index('work_date');
            $table->index('attendance_status');

            $table->unique(['salary_slip_id', 'work_date'], 'salary_slip_days_unique_date_per_slip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_slip_days');
    }
};