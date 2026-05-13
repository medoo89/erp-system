<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_invoice_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('client_invoice_payments', 'treasury_account_id')) {
                $table->unsignedBigInteger('treasury_account_id')->nullable()->after('client_invoice_id');
            }

            if (! Schema::hasColumn('client_invoice_payments', 'treasury_transaction_id')) {
                $table->unsignedBigInteger('treasury_transaction_id')->nullable()->after('treasury_account_id');
            }

            if (! Schema::hasColumn('client_invoice_payments', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('reference_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_invoice_payments', function (Blueprint $table) {
            $drops = [];

            foreach (['treasury_account_id', 'treasury_transaction_id', 'attachment_path'] as $column) {
                if (Schema::hasColumn('client_invoice_payments', $column)) {
                    $drops[] = $column;
                }
            }

            if (! empty($drops)) {
                $table->dropColumn($drops);
            }
        });
    }
};
