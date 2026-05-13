<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AuditModelObserver
{
    public function created(Model $model): void
    {
        $this->write('create', $model, [], $model->getAttributes(), 'Created ' . class_basename($model));
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();

        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        $changedLabels = collect(array_keys($changes))
            ->map(fn ($field) => str($field)->replace('_', ' ')->title()->toString())
            ->implode(', ');

        $this->write('update', $model, $oldValues, $changes, 'Updated ' . class_basename($model) . ': ' . $changedLabels);
    }

    public function deleted(Model $model): void
    {
        $this->write('delete', $model, $model->getOriginal(), [], 'Deleted ' . class_basename($model));
    }

    protected function write(string $action, Model $model, array $oldValues = [], array $newValues = [], string $description = ''): void
    {
        try {
            if (! Schema::hasTable('audit_logs')) {
                return;
            }

            if ($model instanceof AuditLog) {
                return;
            }

        /*
         |--------------------------------------------------------------------------
         | Skip noisy automatic user sync logs
         |--------------------------------------------------------------------------
         | Employee/portal provisioning may create or update App\Models\User
         | automatically when an Employment is saved. We only want user logs
         | when they come from Page Rules / Access Control, not internal sync noise.
         */
        if ($model instanceof \App\Models\User) {
            $path = trim((string) request()?->path(), '/');

            if (
                ! str_contains($path, 'page-rules')
                && ! str_contains($path, 'erp-access-control')
            ) {
                return;
            }
        }


            /*
             |--------------------------------------------------------------------------
             | Skip generic User observer logs
             |--------------------------------------------------------------------------
             | User changes are noisy because employee portal provisioning can create/update
             | User records automatically. Page Rules controller logs manual user actions
             | separately with clearer descriptions.
             */
            if ($model instanceof \App\Models\User) {
                return;
            }

            if (! Auth::check()) {
                return;
            }

            $module = $this->moduleName($model);

            $severity = match ($action) {
                'delete' => 'danger',
                'update' => 'warning',
                'create' => 'success',
                default => 'info',
            };

            AuditLogService::log(
                action: $action,
                module: $module,
                subject: $model,
                description: $description,
                oldValues: $oldValues,
                newValues: $newValues,
                meta: [
                    'source' => 'model_observer',
                    'model' => get_class($model),
                    'record_id' => $model->getKey(),
                ],
                severity: $severity,
                status: 'success',
            );
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function moduleName(Model $model): string
    {
        $class = get_class($model);

        return match ($class) {
            \App\Models\User::class => 'users',
            \App\Models\Employment::class => 'employments',
            \App\Models\EmploymentRotation::class => 'employment_rotations',
            \App\Models\EmploymentFile::class => 'employment_files',
            \App\Models\PreEmployment::class => 'pre_employments',
            \App\Models\Job::class => 'jobs',
            \App\Models\JobApplication::class => 'job_applications',
            \App\Models\ClientInvoice::class => 'client_invoices',
            \App\Models\SalarySlip::class => 'salary_slips',
            \App\Models\FinanceExpense::class => 'finance_expenses',
            \App\Models\TreasuryAccount::class => 'treasury_accounts',
            \App\Models\TreasuryTransaction::class => 'treasury_transactions',
            \App\Models\TreasuryOperation::class => 'treasury_operations',
            \App\Models\BankProfile::class => 'bank_profiles',
            \App\Models\Client::class => 'clients',
            \App\Models\Project::class => 'projects',
            \App\Models\CalendarEvent::class => 'calendar_events',
            default => str($class)->afterLast('\\')->snake()->plural()->toString(),
        };
    }
}
