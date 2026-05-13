<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class SalarySlip extends Model
{
    public const BASIS_DAILY_RATE = 'daily_rate';
    public const BASIS_MONTHLY = 'monthly';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SENT_TO_BANK = 'sent_to_bank';
    public const STATUS_PAID = 'paid';
    public const STATUS_BANK_REJECTED = 'bank_rejected';

    public const PAYMENT_METHOD_BANK = 'bank';
    public const PAYMENT_METHOD_CASH = 'cash';

    public const EMPLOYEE_CONFIRMATION_PENDING = 'pending';
    public const EMPLOYEE_CONFIRMATION_RECEIVED = 'received';
    public const EMPLOYEE_CONFIRMATION_NOT_RECEIVED = 'not_received';

    public const STATUS_LOCKED = 'locked';

    protected $fillable = [
        'employment_id',
        'job_application_id',
        'client_id',
        'project_id',
        'employment_rotation_id',
        'period_start',
        'period_end',
        'salary_year',
        'salary_month',
        'days_worked',
        'salary_basis',
        'daily_rate',
        'monthly_salary',
        'base_amount',
        'adjustments_amount',
        'deductions_amount',
        'addition_note',
        'deduction_note',
        'net_amount',
        'payment_total_amount',
        'reimbursement_same_currency_total',
        'reimbursement_converted_total',
        'reimbursement_exchange_rates',
        'reimbursement_breakdown',
        'currency',
        'status',
        'payment_method',
        'employee_confirmation_status',
        'employee_confirmed_at',
        'employee_confirmation_notes',
        'employee_confirmation_ip',
        'employee_confirmation_user_agent',
        'generated_by',
        'approved_by',
        'treasury_account_id',
        'treasury_operation_id',
        'treasury_transaction_id',
        'bank_profile_id',
        'bank_sent_at',
        'paid_at',
        'rejected_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'daily_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'base_amount' => 'decimal:2',
        'adjustments_amount' => 'decimal:2',
        'deductions_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'payment_total_amount' => 'decimal:2',
        'reimbursement_same_currency_total' => 'decimal:2',
        'reimbursement_converted_total' => 'decimal:2',
        'reimbursement_exchange_rates' => 'array',
        'reimbursement_breakdown' => 'array',
        'bank_sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
        'employee_confirmed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $slip) {
            $slip->currency = strtoupper((string) ($slip->currency ?: 'USD'));

            if (blank($slip->status)) {
                $slip->status = self::STATUS_DRAFT;
            }

            if ($slip->status === self::STATUS_SENT_TO_BANK) {
                $slip->payment_method = self::PAYMENT_METHOD_BANK;

                if (! $slip->treasury_account_id) {
                    throw ValidationException::withMessages([
                        'treasury_account_id' => 'Treasury Account is required when status is Sent to Bank.',
                    ]);
                }

                if (blank($slip->bank_sent_at)) {
                    $slip->bank_sent_at = now();
                }
            }

            if ($slip->status === self::STATUS_PAID) {
                if ($slip->payment_method === self::PAYMENT_METHOD_CASH && ! $slip->treasury_account_id) {
                    throw ValidationException::withMessages([
                        'treasury_account_id' => 'Treasury Account is required when paying this salary slip in cash.',
                    ]);
                }

                if (blank($slip->paid_at)) {
                    $slip->paid_at = now();
                }
            }

            if (! in_array($slip->status, [self::STATUS_SENT_TO_BANK, self::STATUS_PAID], true)) {
                $slip->bank_sent_at = null;
                $slip->paid_at = null;
            }
        });

        static::saved(function (self $slip) {
            $slip->syncTreasuryPosting();
            $slip->syncLinkedReimbursements();
        });

        static::deleted(function (self $slip) {
            $slip->deleteTreasuryPosting();
        });
    }


    public static function employeeConfirmationLabels(): array
    {
        return [
            self::EMPLOYEE_CONFIRMATION_PENDING => 'Pending Employee Confirmation',
            self::EMPLOYEE_CONFIRMATION_RECEIVED => 'Employee Confirmed Receipt',
            self::EMPLOYEE_CONFIRMATION_NOT_RECEIVED => 'Employee Reported Not Received',
        ];
    }

    public function requiresEmployeeReceiptConfirmation(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_SENT_TO_BANK,
            self::STATUS_PAID,
        ], true) && blank($this->employee_confirmation_status);
    }

    public function employeeReceiptStatusLabel(): string
    {
        if (blank($this->employee_confirmation_status)) {
            return self::employeeConfirmationLabels()[self::EMPLOYEE_CONFIRMATION_PENDING];
        }

        return self::employeeConfirmationLabels()[$this->employee_confirmation_status]
            ?? ucfirst(str_replace('_', ' ', (string) $this->employee_confirmation_status));
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_SENT_TO_BANK => 'Sent to Bank',
            self::STATUS_PAID => 'Paid',
            self::STATUS_BANK_REJECTED => 'Bank Rejected',
            self::STATUS_LOCKED => 'Locked (Legacy)',
        ];
    }

    public static function paymentMethodLabels(): array
    {
        return [
            self::PAYMENT_METHOD_BANK => 'Bank',
            self::PAYMENT_METHOD_CASH => 'Cash',
        ];
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employmentRotation(): BelongsTo
    {
        return $this->belongsTo(EmploymentRotation::class, 'employment_rotation_id');
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'treasury_account_id');
    }

    public function treasuryOperation(): BelongsTo
    {
        return $this->belongsTo(TreasuryOperation::class, 'treasury_operation_id');
    }

    public function treasuryTransaction(): BelongsTo
    {
        return $this->belongsTo(TreasuryTransaction::class, 'treasury_transaction_id');
    }

    public function bankProfile(): BelongsTo
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reimbursedFinanceExpenses(): HasMany
    {
        return $this->hasMany(FinanceExpense::class, 'reimbursed_salary_slip_id');
    }

    public function days()
    {
        return $this->hasMany(SalarySlipDay::class);
    }

    public function attachments()
    {
        return $this->hasMany(SalarySlipAttachment::class, 'salary_slip_id')->latest('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_BANK_REJECTED]);
    }

    public function syncLinkedReimbursements(): void
    {
        $isPaidThroughSalary = in_array($this->status, [
            self::STATUS_SENT_TO_BANK,
            self::STATUS_PAID,
        ], true);

        $query = FinanceExpense::query()
            ->where('reimbursed_salary_slip_id', $this->id);

        if ($isPaidThroughSalary) {
            $query->where('reimbursement_status', '!=', FinanceExpense::REIMBURSEMENT_PAID)
                ->update([
                    'reimbursement_status' => FinanceExpense::REIMBURSEMENT_PAID,
                    'reimbursed_at' => $this->status === self::STATUS_PAID
                        ? ($this->paid_at ?: now())
                        : ($this->bank_sent_at ?: now()),
                    'reimbursement_payment_method' => 'salary_slip',
                ]);

            return;
        }

        $query->where('reimbursement_status', FinanceExpense::REIMBURSEMENT_PAID)
            ->update([
                'reimbursement_status' => FinanceExpense::REIMBURSEMENT_APPROVED,
                'reimbursed_at' => null,
                'reimbursement_payment_method' => 'salary_slip',
            ]);
    }

    public function syncTreasuryPosting(): void
    {
        if ($this->status === self::STATUS_SENT_TO_BANK && $this->payment_method === self::PAYMENT_METHOD_BANK) {
            $this->upsertTreasuryPosting(
                ($this->bank_sent_at?->toDateString()) ?: now()->toDateString()
            );
            return;
        }

        if ($this->status === self::STATUS_PAID && $this->payment_method === self::PAYMENT_METHOD_CASH) {
            $this->upsertTreasuryPosting(
                ($this->paid_at?->toDateString()) ?: now()->toDateString()
            );
            return;
        }

        if ($this->status === self::STATUS_PAID && $this->payment_method === self::PAYMENT_METHOD_BANK) {
            return;
        }

        $this->deleteTreasuryPosting();
    }

    protected function upsertTreasuryPosting(string $date): void
    {
        if (! $this->treasury_account_id) {
            return;
        }

        $transaction = TreasuryTransaction::query()->firstOrNew([
            'reference_type' => 'salary_slip',
            'reference_id' => $this->id,
        ]);

        $transaction->treasury_account_id = $this->treasury_account_id;
        $transaction->treasury_operation_id = $this->treasury_operation_id;
        $transaction->transaction_type = TreasuryTransaction::TYPE_SALARY_PAYMENT;
        $transaction->direction = TreasuryTransaction::DIRECTION_OUT;
        $transaction->amount = $this->payment_total_amount ?? $this->net_amount ?? 0;
        $transaction->currency = $this->currency ?: 'USD';
        $transaction->transaction_date = $date;
        $transaction->client_id = $this->client_id;
        $transaction->project_id = $this->project_id;
        $transaction->employment_id = $this->employment_id;
        $transaction->description = 'Salary Slip #' . $this->id . ' (' . ($this->payment_method ?: 'payment') . ')';
        $transaction->notes = $this->notes;
        $transaction->is_posted = true;
        $transaction->settlement_status = TreasuryTransaction::SETTLEMENT_CLEARED;
        $transaction->save();

        if ((int) $this->treasury_transaction_id !== (int) $transaction->id) {
            $this->treasury_transaction_id = $transaction->id;
            $this->saveQuietly();
        }
    }

    public function deleteTreasuryPosting(): void
    {
        $transaction = null;

        if ($this->treasury_transaction_id) {
            $transaction = TreasuryTransaction::query()->find($this->treasury_transaction_id);
        }

        if (! $transaction) {
            $transaction = TreasuryTransaction::query()
                ->where('reference_type', 'salary_slip')
                ->where('reference_id', $this->id)
                ->first();
        }

        if ($transaction && $transaction->reference_type === 'salary_slip' && (int) $transaction->reference_id === (int) $this->id) {
            $transaction->delete();
        }

        if ($this->treasury_transaction_id !== null) {
            $this->treasury_transaction_id = null;
            $this->saveQuietly();
        }
    }

    public function unpaidDaysCount(): int
    {
        if (method_exists($this, 'days') && $this->relationLoaded('days')) {
            return (int) $this->days->where('day_type', 'unpaid_leave')->count();
        }

        if (method_exists($this, 'days')) {
            return (int) $this->days()->where('day_type', 'unpaid_leave')->count();
        }

        return 0;
    }

    public function absentDaysCount(): int
    {
        if (method_exists($this, 'days') && $this->relationLoaded('days')) {
            return (int) $this->days->where('day_type', 'absent')->count();
        }

        if (method_exists($this, 'days')) {
            return (int) $this->days()->where('day_type', 'absent')->count();
        }

        return 0;
    }

    public function presentDaysCount(): int
    {
        if ($this->days_worked !== null) {
            return (int) $this->days_worked;
        }

        if (method_exists($this, 'days') && $this->relationLoaded('days')) {
            return (int) $this->days->where('day_type', 'present')->count();
        }

        if (method_exists($this, 'days')) {
            return (int) $this->days()->where('day_type', 'present')->count();
        }

        return 0;
    }
}
