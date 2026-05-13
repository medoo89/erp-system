<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_expense_travel_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('finance_expense_id')
                ->constrained('finance_expenses')
                ->cascadeOnDelete();

            $table->foreignId('employment_id')->nullable()->constrained('employments')->nullOnDelete();

            $table->string('traveler_name')->nullable();
            $table->string('trip_type', 50)->nullable(); // one_way, round_trip, open_return, split_rotation
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();

            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->boolean('return_open')->default(false);

            $table->boolean('split_across_rotations')->default(false);
            $table->foreignId('outbound_rotation_id')->nullable()->constrained('employment_rotations')->nullOnDelete();
            $table->foreignId('inbound_rotation_id')->nullable()->constrained('employment_rotations')->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_expense_travel_details');
    }
};
