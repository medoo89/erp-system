<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;

class FinanceExpense extends Model
{
    public const SCOPE_PRE_HIRE = 'pre_hire';
    public const SCOPE_EMPLOYMENT = 'employment';
    public const SCOPE_ROTATION = 'rotation';
    public const SCOPE_AD_HOC = 'ad_hoc';

    public const CATEGORY_VISA = 'visa';
    public const CATEGORY_TICKET = 'ticket';
    public const CATEGORY_HOTEL = 'hotel';
    public const CATEGORY_FOOD = 'food';
    public const CATEGORY_TRANSPORT = 'transport';
    public const CATEGORY_MEDICAL = 'medical';
    public const CATEGORY_TRAINING = 'training';
    public const CATEGORY_FIELD_COST = 'field_cost';
    public const CATEGORY_ACCOMMODATION = 'accommodation';
    public const CATEGORY_DESERT_PASS = 'desert_pass';
    public const CATEGORY_OTHER = 'other';

    public const PAID_BY_COMPANY = 'company';
    public const PAID_BY_CANDIDATE = 'candidate';
    public const PAID_BY_CLIENT = 'client';
    public const PAID_BY_THIRD_PARTY = 'third_party';

    public const REIMBURSEMENT_NOT_APPLICABLE = 'not_applicable';
    public const REIMBURSEMENT_PENDING = 'pending';
    public const REIMBURSEMENT_APPROVED = 'approved';
    public const REIMBURSEMENT_PAID = 'paid';
    public const REIMBURSEMENT_REJECTED = 'rejected';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public const ALLOCATION_UNALLOCATED = 'unallocated';
    public const ALLOCATION_PARTIAL = 'partial';
    public const ALLOCATION_ALLOCATED = 'allocated';

    protected $fillable = [
        'job_application_id',
        'pre_employment_id',
        'employment_id',
        'employment_rotation_id',
        'job_id',
        'client_id',
        'project_id',
        'candidate_finance_profile_id',
        'created_by',
        'approved_by',
        'expense_scope',
        'category',
        'expense_category',
        'title',
        'description',
        'vendor_name',
        'amount',
        'currency',
        'expense_date',
        'incurred_from',
        'incurred_to',
        'paid_by',
        'reimbursement_status',
        'reimbursement_required',
        'reimbursement_amount',
        'reimbursement_currency',
        'reimbursement_notes',
        'reimbursement_decision_by',
        'reimbursement_decision_at',
        'reimbursed_salary_slip_id',
        'reimbursed_at',
        'reimbursement_payment_method',
        'candidate_submitted',
        'candidate_submitted_at',
        'receipt_file_path',
        'is_first_mobilization',
        'is_travel_expense',
        'is_company_expense',
        'is_manual_expense',
        'allocation_status',
        'treasury_account_id',
        'treasury_operation_id',
        'treasury_transaction_id',
        'has_attachment',
        'attachment_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'incurred_from' => 'date',
        'incurred_to' => 'date',
        'reimbursement_required' => 'boolean',
        'reimbursement_amount' => 'decimal:2',
        'reimbursement_decision_at' => 'datetime',
        'reimbursed_at' => 'datetime',
        'candidate_submitted' => 'boolean',
        'candidate_submitted_at' => 'datetime',
        'is_first_mobilization' => 'boolean',
        'is_travel_expense' => 'boolean',
        'is_company_expense' => 'boolean',
        'is_manual_expense' => 'boolean',
        'has_attachment' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $expense) {
            $expense->currency = strtoupper((string) ($expense->currency ?: 'USD'));
            $expense->has_attachment = filled($expense->attachment_path) || filled($expense->receipt_file_path);

            if ($expense->paid_by === self::PAID_BY_CANDIDATE) {
                $expense->reimbursement_required = true;

                if (blank($expense->reimbursement_status) || $expense->reimbursement_status === self::REIMBURSEMENT_NOT_APPLICABLE) {
                    $expense->reimbursement_status = self::REIMBURSEMENT_PENDING;
                }

                if (blank($expense->reimbursement_amount)) {
                    $expense->reimbursement_amount = $expense->amount;
                }

                if (blank($expense->reimbursement_currency)) {
                    $expense->reimbursement_currency = $expense->currency;
                }
            } else {
                $expense->reimbursement_required = false;

                if (blank($expense->reimbursement_status)) {
                    $expense->reimbursement_status = self::REIMBURSEMENT_NOT_APPLICABLE;
                }
            }

            if (blank($expense->reimbursement_currency) && filled($expense->currency)) {
                $expense->reimbursement_currency = $expense->currency;
            }

            if (blank($expense->status)) {
                $expense->status = self::STATUS_DRAFT;
            }

            if (blank($expense->expense_scope)) {
                if ($expense->employment_rotation_id) {
                    $expense->expense_scope = self::SCOPE_ROTATION;
                } elseif ($expense->employment_id) {
                    $expense->expense_scope = self::SCOPE_EMPLOYMENT;
                } elseif ($expense->pre_employment_id) {
                    $expense->expense_scope = self::SCOPE_PRE_HIRE;
                } else {
                    $expense->expense_scope = self::SCOPE_AD_HOC;
                }
            }

            if (blank($expense->allocation_status)) {
                $expense->allocation_status = self::ALLOCATION_UNALLOCATED;
            }

            if ($expense->category === self::CATEGORY_TICKET) {
                $expense->is_travel_expense = true;
            }

            if ($expense->status === self::STATUS_PAID && ! $expense->treasury_account_id) {
                throw ValidationException::withMessages([
                    'treasury_account_id' => 'Treasury Account is required when marking this expense as Paid.',
                ]);
            }
        });

        static::saved(function (self $expense) {
            /*
             * Calendar rule:
             * Any finance expense / reimbursement claim with meaningful dates
             * must appear immediately in the unified ERP / portal calendars,
             * regardless of approval/payment status.
             */
            $expense->syncCalendarEvents();

            /*
             * Critical finance rule:
             * Only PAID expenses are allowed to have treasury postings.
             * If expense is moved back to Approved/Draft/Cancelled,
             * remove treasury posting immediately so Global Finance Totals
             * and treasury reports stop counting it.
             */
            if ($expense->status === self::STATUS_PAID) {
                $expense->syncTreasuryPosting();
                return;
            }

            $expense->deleteTreasuryPosting();
        });

        static::deleted(function (self $expense) {
            $expense->deleteCalendarEvents();
            $expense->deleteTreasuryPosting();
        });
    }

    public static function scopeLabels(): array
    {
        return [
            self::SCOPE_PRE_HIRE => 'Pre-Hire',
            self::SCOPE_EMPLOYMENT => 'Employment',
            self::SCOPE_ROTATION => 'Rotation',
            self::SCOPE_AD_HOC => 'Ad Hoc',
        ];
    }

    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_VISA => 'Visa',
            self::CATEGORY_TICKET => 'Ticket',
            self::CATEGORY_HOTEL => 'Hotel',
            self::CATEGORY_FOOD => 'Food',
            self::CATEGORY_TRANSPORT => 'Transport',
            self::CATEGORY_MEDICAL => 'Medical',
            self::CATEGORY_TRAINING => 'Training',
            self::CATEGORY_FIELD_COST => 'Field Cost',
            self::CATEGORY_ACCOMMODATION => 'Accommodation',
            self::CATEGORY_DESERT_PASS => 'Desert Pass',
            self::CATEGORY_OTHER => 'Other',
        ];
    }

    public static function paidByLabels(): array
    {
        return [
            self::PAID_BY_COMPANY => 'Company',
            self::PAID_BY_CANDIDATE => 'Candidate / Employee',
            self::PAID_BY_CLIENT => 'Client',
            self::PAID_BY_THIRD_PARTY => 'Third Party',
        ];
    }

    public static function reimbursementLabels(): array
    {
        return [
            self::REIMBURSEMENT_NOT_APPLICABLE => 'Not Applicable',
            self::REIMBURSEMENT_PENDING => 'Pending',
            self::REIMBURSEMENT_APPROVED => 'Approved',
            self::REIMBURSEMENT_PAID => 'Paid',
            self::REIMBURSEMENT_REJECTED => 'Rejected',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function allocationLabels(): array
    {
        return [
            self::ALLOCATION_UNALLOCATED => 'Unallocated',
            self::ALLOCATION_PARTIAL => 'Partial',
            self::ALLOCATION_ALLOCATED => 'Allocated',
        ];
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function preEmployment(): BelongsTo
    {
        return $this->belongsTo(PreEmployment::class, 'pre_employment_id');
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public function employmentRotation(): BelongsTo
    {
        return $this->belongsTo(EmploymentRotation::class, 'employment_rotation_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function financeProfile(): BelongsTo
    {
        return $this->belongsTo(CandidateFinanceProfile::class, 'candidate_finance_profile_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reimbursedSalarySlip(): BelongsTo
    {
        return $this->belongsTo(SalarySlip::class, 'reimbursed_salary_slip_id');
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

    public function allocations(): HasMany
    {
        return $this->hasMany(FinanceExpenseAllocation::class);
    }

    public function travelDetail(): HasOne
    {
        return $this->hasOne(FinanceExpenseTravelDetail::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_CANCELLED);
    }

    public function scopeForScope(Builder $query, string $scope): Builder
    {
        return $query->where('expense_scope', $scope);
    }

    public function scopeForCurrency(Builder $query, string $currency): Builder
    {
        return $query->where('currency', strtoupper($currency));
    }

    public function ownerName(): string
    {
        if ($this->employmentRotation?->employment?->employee_name) {
            return $this->employmentRotation->employment->employee_name;
        }

        if ($this->employment?->employee_name) {
            return $this->employment->employee_name;
        }

        if ($this->preEmployment?->candidate_name) {
            return $this->preEmployment->candidate_name;
        }

        return $this->jobApplication?->full_name
            ?: $this->vendor_name
            ?: 'Unknown Owner';
    }


    public function syncCalendarEvents(): void
    {
        if (! Schema::hasTable('calendar_events')) {
            return;
        }

        $this->deleteCalendarEvents();

        $events = $this->calendarEventPayloads();

        foreach ($events as $event) {
            if (blank($event['event_date'] ?? null)) {
                continue;
            }

            CalendarEvent::query()->create([
                'title' => $event['title'],
                'event_type' => $event['event_type'],
                'notes' => $event['notes'],
                'event_date' => $event['event_date'],
                'is_all_day' => true,
                'color' => $event['color'],
                'linked_type' => self::class,
                'linked_id' => $this->id,
                'job_id' => $this->job_id,
                'is_active' => true,
                'created_by' => $this->created_by,
                'updated_by' => auth()->id(),
            ]);
        }
    }

    public function deleteCalendarEvents(): void
    {
        if (! Schema::hasTable('calendar_events')) {
            return;
        }

        CalendarEvent::query()
            ->where('linked_type', self::class)
            ->where('linked_id', $this->id)
            ->delete();
    }

    protected function calendarEventPayloads(): array
    {
        $category = strtolower((string) ($this->category ?: $this->expense_category ?: self::CATEGORY_OTHER));
        $owner = $this->ownerName();
        $title = $this->title ?: ucfirst(str_replace('_', ' ', $category)) . ' Expense';
        $statusLabel = static::statusLabels()[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
        $reimbursementLabel = static::reimbursementLabels()[$this->reimbursement_status] ?? ucfirst(str_replace('_', ' ', (string) $this->reimbursement_status));

        $baseNotes = trim(implode(' · ', array_filter([
            'Finance Expense #' . $this->id,
            'Owner: ' . ($owner ?: '-'),
            'Status: ' . ($statusLabel ?: '-'),
            'Reimbursement: ' . ($reimbursementLabel ?: '-'),
            'Amount: ' . number_format((float) ($this->amount ?? 0), 2) . ' ' . ($this->currency ?: 'USD'),
            $this->candidate_submitted ? 'Portal Submitted' : null,
        ])));

        $payloads = [];

        $add = function (?string $date, string $label, string $type, string $color) use (&$payloads, $title, $baseNotes) {
            if (blank($date)) {
                return;
            }

            $payloads[] = [
                'title' => $label . ': ' . $title,
                'event_type' => $type,
                'notes' => $baseNotes,
                'event_date' => $date,
                'color' => $color,
            ];
        };

        /*
         * IMPORTANT:
         * expense_date is only the invoice/claim record date.
         * It must NOT appear in any ERP / portal calendar.
         *
         * Calendar events are created only from meaningful operational dates:
         * - Ticket departure / return
         * - Hotel check-in / check-out
         * - Visa submission / follow-up / expiry
         * - Medical appointment / follow-up / expiry
         * - Training / desert pass / other actual scheduled date ranges
         */
        $from = $this->incurred_from?->toDateString();
        $to = $this->incurred_to?->toDateString();

        if ($category === self::CATEGORY_TICKET) {
            $add($from, 'Ticket Departure', 'finance_ticket_departure', '#2563eb');
            $add($to, 'Ticket Return', 'finance_ticket_return', '#1d4ed8');
            return $payloads;
        }

        if (in_array($category, [self::CATEGORY_HOTEL, self::CATEGORY_ACCOMMODATION], true)) {
            $add($from, 'Hotel Check-in', 'finance_hotel_check_in', '#0ea5e9');
            $add($to, 'Hotel Check-out', 'finance_hotel_check_out', '#0284c7');
            return $payloads;
        }

        if ($category === self::CATEGORY_VISA) {
            $add($from, 'Visa Submission / Follow-up', 'finance_visa_follow_up', '#7c3aed');
            $add($to, 'Visa Expiry / Critical Date', 'finance_visa_expiry', '#ef4444');
            return $payloads;
        }

        if ($category === self::CATEGORY_MEDICAL) {
            $add($from, 'Medical Appointment / Follow-up', 'finance_medical_follow_up', '#06b6d4');
            $add($to, 'Medical Expiry / Critical Date', 'finance_medical_expiry', '#0891b2');
            return $payloads;
        }

        if ($category === self::CATEGORY_TRAINING) {
            $add($from, 'Training Start', 'finance_training_start', '#2563eb');
            $add($to, 'Training End', 'finance_training_end', '#1d4ed8');
            return $payloads;
        }

        if ($category === self::CATEGORY_DESERT_PASS) {
            $add($from, 'Desert Pass Date', 'finance_desert_pass', '#f59e0b');
            $add($to, 'Desert Pass Expiry', 'finance_desert_pass_expiry', '#d97706');
            return $payloads;
        }

        $add($from, 'Expense Scheduled Start', 'finance_expense_start', '#2563eb');
        $add($to, 'Expense Scheduled End', 'finance_expense_end', '#1d4ed8');

        return $payloads;
    }

    public function syncTreasuryPosting(): void
    {
        if ($this->status !== self::STATUS_PAID || ! $this->treasury_account_id) {
            $this->deleteTreasuryPosting();
            return;
        }

        $transaction = TreasuryTransaction::query()->firstOrNew([
            'reference_type' => 'finance_expense',
            'reference_id' => $this->id,
        ]);

        $transaction->treasury_account_id = $this->treasury_account_id;
        $transaction->treasury_operation_id = $this->treasury_operation_id;
        $transaction->transaction_type = TreasuryTransaction::TYPE_EXPENSE_PAYMENT;
        $transaction->direction = TreasuryTransaction::DIRECTION_OUT;
        $transaction->amount = $this->amount ?? 0;
        $transaction->currency = $this->currency ?: 'USD';
        $transaction->transaction_date = ($this->expense_date?->toDateString()) ?: now()->toDateString();
        $transaction->client_id = $this->client_id;
        $transaction->project_id = $this->project_id;
        $transaction->employment_id = $this->employment_id;
        $transaction->description = $this->title ?: ('Finance Expense #' . $this->id);
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
        /*
         * Remove treasury traces created by this expense.
         * Works safely even if some legacy columns do not exist in SQLite.
         */

        $expenseId = (int) $this->id;

        if ($expenseId <= 0) {
            return;
        }

        if (Schema::hasColumn('finance_expenses', 'treasury_transaction_id') && $this->treasury_transaction_id) {
            TreasuryTransaction::query()
                ->where('id', $this->treasury_transaction_id)
                ->delete();
        }

        if (Schema::hasColumn('finance_expenses', 'treasury_operation_id') && $this->treasury_operation_id) {
            TreasuryOperation::query()
                ->where('id', $this->treasury_operation_id)
                ->delete();
        }

        TreasuryTransaction::query()
            ->where('reference_type', 'finance_expense')
            ->where('reference_id', $expenseId)
            ->delete();

        TreasuryOperation::query()
            ->where('reference_type', 'finance_expense')
            ->where('reference_id', $expenseId)
            ->delete();

        $updates = [];

        if (Schema::hasColumn('finance_expenses', 'treasury_transaction_id')) {
            $updates['treasury_transaction_id'] = null;
        }

        if (Schema::hasColumn('finance_expenses', 'treasury_operation_id')) {
            $updates['treasury_operation_id'] = null;
        }

        if ($this->status !== self::STATUS_PAID && Schema::hasColumn('finance_expenses', 'treasury_account_id')) {
            $updates['treasury_account_id'] = null;
        }

        if (! empty($updates)) {
            $this->forceFill($updates)->saveQuietly();
        }
    }
}
