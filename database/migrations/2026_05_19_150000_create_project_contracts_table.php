<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Project Contracts / Counter
|--------------------------------------------------------------------------
| This table stores the contract counter workflow for each project:
| base contracts, renewals, amendments, value additions, tax paid,
| contract files, versioning, and active/inactive contract history.
*/

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('project_contracts')) {
            return;
        }

        Schema::create('project_contracts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->nullOnDelete();

            $table->string('contract_no')->nullable()->index();
            $table->string('title')->nullable();

            $table->string('version')->nullable(); // V1, V2, Amendment 1...
            $table->string('type')->default('base'); // base, amendment, renewal, extension
            $table->string('amendment_type')->nullable(); // value_addition, date_extension, both

            $table->decimal('contract_value', 18, 2)->default(0);
            $table->decimal('tax_paid', 18, 2)->default(0);
            $table->string('currency', 10)->default('EUR');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('status')->default('draft'); // draft, active, closed, cancelled
            $table->boolean('is_active')->default(true);

            $table->string('contract_file')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['project_id', 'currency']);
            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_contracts');
    }
};
