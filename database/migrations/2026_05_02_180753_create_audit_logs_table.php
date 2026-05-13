<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable()->index();
            $table->string('user_email')->nullable()->index();
            $table->string('user_role')->nullable()->index();
            $table->string('user_department')->nullable()->index();

            $table->string('action')->index(); // view/create/update/delete/print/send/approve/login/logout/enable/disable
            $table->string('module')->index(); // employment/page_rules/salary_slips/etc
            $table->string('module_label')->nullable();

            $table->nullableMorphs('subject');
            $table->string('subject_title')->nullable();
            $table->string('subject_reference')->nullable()->index();

            $table->string('severity')->default('info')->index(); // info/success/warning/danger
            $table->string('status')->default('success')->index(); // success/failed

            $table->text('description')->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('meta')->nullable();

            $table->string('ip_address')->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('method')->nullable();
            $table->text('url')->nullable();
            $table->string('route_name')->nullable()->index();

            $table->timestamp('performed_at')->nullable()->index();
            $table->timestamps();

            $table->index(['module', 'action']);
            $table->index(['user_id', 'performed_at']);
            $table->index(['severity', 'performed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
