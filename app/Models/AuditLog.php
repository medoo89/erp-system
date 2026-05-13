<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'user_department',
        'action',
        'module',
        'module_label',
        'subject_type',
        'subject_id',
        'subject_title',
        'subject_reference',
        'severity',
        'status',
        'description',
        'old_values',
        'new_values',
        'meta',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'route_name',
        'performed_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'meta' => 'array',
        'performed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function subjectUrl(): ?string
    {
        if (blank($this->subject_type) || blank($this->subject_id)) {
            return null;
        }

        $map = [
            \App\Models\Employment::class => \App\Filament\Resources\Employments\EmploymentResource::class,
            \App\Models\PreEmployment::class => \App\Filament\Resources\PreEmployments\PreEmploymentResource::class,
            \App\Models\Job::class => \App\Filament\Resources\Jobs\JobResource::class,
            \App\Models\JobApplication::class => \App\Filament\Resources\JobApplications\JobApplicationResource::class,
            \App\Models\SalarySlip::class => \App\Filament\Resources\SalarySlips\SalarySlipResource::class,
            \App\Models\FinanceExpense::class => \App\Filament\Resources\FinanceExpenses\FinanceExpenseResource::class,
            \App\Models\ClientInvoice::class => \App\Filament\Resources\ClientInvoices\ClientInvoiceResource::class,
            \App\Models\TreasuryAccount::class => \App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource::class,
            \App\Models\TreasuryTransaction::class => \App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource::class,
            \App\Models\TreasuryOperation::class => \App\Filament\Resources\TreasuryOperations\TreasuryOperationResource::class,
            \App\Models\BankProfile::class => \App\Filament\Resources\BankProfiles\BankProfileResource::class,
            \App\Models\Client::class => \App\Filament\Resources\Clients\ClientResource::class,
            \App\Models\Project::class => \App\Filament\Resources\Projects\ProjectResource::class,
            \App\Models\User::class => null,
        ];

        $resource = $map[$this->subject_type] ?? null;

        if ($resource && class_exists($resource) && method_exists($resource, 'getUrl')) {
            try {
                return $resource::getUrl('view', ['record' => $this->subject_id]);
            } catch (\Throwable $e) {
                try {
                    return $resource::getUrl('edit', ['record' => $this->subject_id]);
                } catch (\Throwable $e) {
                    return null;
                }
            }
        }

        if ($this->module === 'page_rules' || $this->subject_type === \App\Models\User::class) {
            return url('/admin/page-rules');
        }

        return null;
    }

    public function changedFields(): array
    {
        $oldValues = $this->old_values ?: [];
        $newValues = $this->new_values ?: [];

        $ignore = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'password',
            'remember_token',
            'email_verified_at',
        ];

        return collect(array_unique(array_merge(array_keys($oldValues), array_keys($newValues))))
            ->reject(fn ($field) => in_array($field, $ignore, true))
            ->values()
            ->all();
    }


    public function actionLabel(): string
    {
        return str($this->action)->replace('_', ' ')->title()->toString();
    }

    public function moduleLabel(): string
    {
        return $this->module_label ?: str($this->module)->replace('_', ' ')->title()->toString();
    }

    public function severityColor(): string
    {
        return match ($this->severity) {
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            default => '#2563eb',
        };
    }
}
