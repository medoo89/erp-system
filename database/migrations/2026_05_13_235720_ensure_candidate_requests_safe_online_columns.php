<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('candidate_requests')) {
            return;
        }

        Schema::table('candidate_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('candidate_requests', 'public_token')) {
                $table->string('public_token')->nullable()->after('created_by');
            }

            if (! Schema::hasColumn('candidate_requests', 'email_sent_at')) {
                $table->timestamp('email_sent_at')->nullable()->after('public_token');
            }

            if (! Schema::hasColumn('candidate_requests', 'candidate_counter_offer')) {
                $table->decimal('candidate_counter_offer', 15, 2)->nullable()->after('currency');
            }

            if (! Schema::hasColumn('candidate_requests', 'accepted_salary')) {
                $table->decimal('accepted_salary', 15, 2)->nullable()->after('candidate_counter_offer');
            }

            if (! Schema::hasColumn('candidate_requests', 'accepted_currency')) {
                $table->string('accepted_currency')->nullable()->after('accepted_salary');
            }

            if (! Schema::hasColumn('candidate_requests', 'negotiation_result')) {
                $table->string('negotiation_result')->nullable()->after('accepted_currency');
            }

            if (! Schema::hasColumn('candidate_requests', 'is_final_offer')) {
                $table->boolean('is_final_offer')->default(false)->after('negotiation_result');
            }

            if (! Schema::hasColumn('candidate_requests', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('is_final_offer');
            }
        });

        // Important for salary negotiation thread/history JSON.
        DB::statement('ALTER TABLE candidate_requests MODIFY candidate_response LONGTEXT NULL');
    }

    public function down(): void
    {
        // Safe non-destructive migration.
        // We intentionally do not drop columns because these fields may already contain live candidate workflow data.
    }
};