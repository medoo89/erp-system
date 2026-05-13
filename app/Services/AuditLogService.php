<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditLogService
{
    public static function log(
        string $action,
        string $module,
        ?Model $subject = null,
        ?string $description = null,
        array $oldValues = [],
        array $newValues = [],
        array $meta = [],
        string $severity = 'info',
        string $status = 'success',
        ?Request $request = null,
    ): ?AuditLog {
        try {
            if (! Schema::hasTable('audit_logs')) {
                return null;
            }

            $request = $request ?: request();
            $user = Auth::user();

            $subjectTitle = null;
            $subjectReference = null;

            if ($subject) {
                $subjectTitle = self::resolveSubjectTitle($subject);
                $subjectReference = self::resolveSubjectReference($subject);
            }

            return AuditLog::query()->create([
                'user_id' => $user?->id,
                'user_name' => $user?->name,
                'user_email' => $user?->email,
                'user_role' => $user?->erp_role,
                'user_department' => $user?->erp_department,

                'action' => $action,
                'module' => $module,
                'module_label' => str($module)->replace('_', ' ')->title()->toString(),

                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->getKey(),
                'subject_title' => $subjectTitle,
                'subject_reference' => $subjectReference,

                'severity' => $severity,
                'status' => $status,
                'description' => $description,

                'old_values' => empty($oldValues) ? null : self::cleanPayload($oldValues),
                'new_values' => empty($newValues) ? null : self::cleanPayload($newValues),
                'meta' => empty($meta) ? null : self::cleanPayload($meta),

                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'method' => $request?->method(),
                'url' => $request?->fullUrl(),
                'route_name' => $request?->route()?->getName(),

                'performed_at' => now(),
            ]);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }

    public static function view(string $module, ?Model $subject = null, ?string $description = null, array $meta = []): ?AuditLog
    {
        return self::log('view', $module, $subject, $description, [], [], $meta, 'info');
    }

    public static function created(string $module, Model $subject, ?string $description = null, array $meta = []): ?AuditLog
    {
        return self::log('create', $module, $subject, $description, [], $subject->toArray(), $meta, 'success');
    }

    public static function updated(string $module, Model $subject, array $oldValues = [], array $newValues = [], ?string $description = null, array $meta = []): ?AuditLog
    {
        return self::log('update', $module, $subject, $description, $oldValues, $newValues, $meta, 'warning');
    }

    public static function deleted(string $module, ?Model $subject = null, ?string $description = null, array $meta = []): ?AuditLog
    {
        return self::log('delete', $module, $subject, $description, $subject?->toArray() ?? [], [], $meta, 'danger');
    }

    public static function print(string $module, ?Model $subject = null, ?string $description = null, array $meta = []): ?AuditLog
    {
        return self::log('print', $module, $subject, $description, [], [], $meta, 'info');
    }

    public static function security(string $action, string $module, ?Model $subject = null, ?string $description = null, array $meta = []): ?AuditLog
    {
        return self::log($action, $module, $subject, $description, [], [], $meta, 'danger');
    }

    protected static function resolveSubjectTitle(Model $subject): ?string
    {
        foreach ([
            'name',
            'title',
            'employee_name',
            'full_name',
            'email',
            'invoice_number',
            'employee_code',
            'account_name',
            'profile_name',
            'bank_name',
        ] as $field) {
            if (isset($subject->{$field}) && filled($subject->{$field})) {
                return (string) $subject->{$field};
            }
        }

        return class_basename($subject) . ' #' . $subject->getKey();
    }

    protected static function resolveSubjectReference(Model $subject): ?string
    {
        foreach ([
            'employee_code',
            'invoice_number',
            'reference',
            'code',
            'project_code',
            'job_code',
        ] as $field) {
            if (isset($subject->{$field}) && filled($subject->{$field})) {
                return (string) $subject->{$field};
            }
        }

        return (string) $subject->getKey();
    }

    protected static function cleanPayload(array $payload): array
    {
        $hiddenKeys = [
            'password',
            'remember_token',
            'new_password',
            'temporary_password',
            'current_password',
            'password_confirmation',
        ];

        foreach ($hiddenKeys as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = '[hidden]';
            }
        }

        return $payload;
    }
}
