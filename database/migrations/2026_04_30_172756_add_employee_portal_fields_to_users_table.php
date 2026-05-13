<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'employment_id')) {
                $table->foreignId('employment_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('employments')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'user_type')) {
                $table->string('user_type')->default('admin')->after('employment_id');
            }

            if (! Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(true)->after('user_type');
            }

            if (! Schema::hasColumn('users', 'portal_status')) {
                $table->string('portal_status')->nullable()->after('is_admin');
            }

            if (! Schema::hasColumn('users', 'portal_access_enabled')) {
                $table->boolean('portal_access_enabled')->default(false)->after('portal_status');
            }

            if (! Schema::hasColumn('users', 'portal_disabled_reason')) {
                $table->string('portal_disabled_reason')->nullable()->after('portal_access_enabled');
            }

            if (! Schema::hasColumn('users', 'portal_disabled_at')) {
                $table->timestamp('portal_disabled_at')->nullable()->after('portal_disabled_reason');
            }

            if (! Schema::hasColumn('users', 'password_setup_sent_at')) {
                $table->timestamp('password_setup_sent_at')->nullable()->after('portal_disabled_at');
            }
        });

        DB::table('users')
            ->whereNull('user_type')
            ->orWhere('user_type', '')
            ->update([
                'user_type' => 'admin',
                'is_admin' => true,
            ]);

        DB::table('users')
            ->where('user_type', 'admin')
            ->update([
                'is_admin' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'password_setup_sent_at',
                'portal_disabled_at',
                'portal_disabled_reason',
                'portal_access_enabled',
                'portal_status',
                'is_admin',
                'user_type',
                'employment_id',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    if ($column === 'employment_id') {
                        try {
                            $table->dropConstrainedForeignId('employment_id');
                        } catch (\Throwable $e) {
                            $table->dropColumn('employment_id');
                        }
                    } else {
                        $table->dropColumn($column);
                    }
                }
            }
        });
    }
};
