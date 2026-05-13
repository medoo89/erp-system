<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FinanceExpense extends Model
{
    public const SCOPE_MOBILIZATION = 'mobilization';
    public const SCOPE_MONTHLY = 'monthly';
    public const SCOPE_ROTATION = 'rotation';
    public const SCOPE_AD_HOC = 'ad_hoc';
    public const SCOPE_REIMBURSABLE = 'reimbursable';

    public const SCOPES = [
        self::SCOPE_MOBILIZATION,
        self::SCOPE_MONTHLY,
        self::SCOPE_ROTATION,
        self::SCOPE_AD_HOC,
        self::SCOPE_REIMBURSABLE,
    ];

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

    public const CATEGORIES = [
        self::CATEGORY_VISA,
        self::CATEGORY_TICKET,
        self::CATEGORY_HOTEL,
        self::CATEGORY_FOOD,
        self::CATEGORY_TRANSPORT,
        self::CATEGORY_MEDICAL,
        self::CATEGORY_TRAINING,
        self::CATEGORY_FIELD_COST,
        self::CATEGORY_ACCOMMODATION,
        self::CATEGORY_DESERT_PASS,
        self::CATEGORY_OTHER,
    ];

    public const PAID_BY_COMPANY = 'company';
    public const PAID_BY_CANDIDATE = 'candidate';
    public const PAID_BY_CLIENT = 'client';
    public const PAID_BY_THIRD_PARTY = 'third_party';

    public const PAID_BY_OPTIONS = [
        self::PAID_BY_COMPANY,
        self::PAID_BY_CANDIDATE,
        self::PAID_BY_CLIENT,
        self::PAID_BY_THIRD_PARTY,
    ];

    public const REIMBURSEMENT_NOT_APPLICABLE = 'not_applicable';
    public const REIMBURSEMENT_PENDING = 'pending';
    public const REIMBURSEMENT_APPROVED = 'approved';
    public const REIMBURSEMENT_PAID = 'paid';
    public const REIMBURSEMENT_REJECTED = 'rejected';

    public const REIMBURSEMENT_STATUSES = [
        self::REIMBURSEMENT_NOT_APPLICABLE,
        self::REIMBURSEMENT_PENDING,
        self::REIMBURSEMENT_APPROVED,
        self::REIMBURSEMENT_PAID,
        self::REIMBURSEMENT_REJECTED,
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_POSTED = 'posted';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_APPROVED,
        self::STATUS_POSTED,
        self::STATUS_CANCELLED,
    ];

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
        'title',
        'description',
        'amount',
        'currency',
        'expense_date',
        'incurred_from',
        'incurred_to',
        'paid_by',
        'reimbursement_status',
        'is_first_mobilization',
        'has_attachment',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'incurred_from' => 'date',
        'incurred_to' => 'date',
        'is_first_mobilization' => 'boolean',
        'has_attachment' => 'boolean',
    ];

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class, 'pre_employment_id');
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public function employmentRotation()
    {
        return $this->belongsTo(EmploymentRotation::class, 'employment_rotation_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function financeProfile()
    {
        return $this->belongsTo(CandidateFinanceProfile::class, 'candidate_finance_profile_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePosted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED]);
    }

    public function scopeForCurrency(Builder $query, ?string $currency): Builder
    {
        if (blank($currency)) {
            return $query;
        }

        return $query->where('currency', strtoupper($currency));
    }

    public function scopeForCandidate(Builder $query, ?int $jobApplicationId): Builder
    {
        if (blank($jobApplicationId)) {
            return $query;
        }

        return $query->where('job_application_id', $jobApplicationId);
    }

    public function scopeForEmployment(Builder $query, ?int $employmentId): Builder
    {
        if (blank($employmentId)) {
            return $query;
        }

        return $query->where('employment_id', $employmentId);
    }

    public function scopeForClient(Builder $query, ?int $clientId): Builder
    {
        if (blank($clientId)) {
            return $query;
        }

        return $query->where('client_id', $clientId);
    }

    public function scopeForProject(Builder $query, ?int $projectId): Builder
    {
        if (blank($projectId)) {
            return $query;
        }

        return $query->where('project_id', $projectId);
    }

    public function scopeForScope(Builder $query, ?string $expenseScope): Builder
    {
        if (blank($expenseScope)) {
            return $query;
        }

        return $query->where('expense_scope', $expenseScope);
    }

    public function scopeForCategory(Builder $query, ?string $category): Builder
    {
        if (blank($category)) {
            return $query;
        }

        return $query->where('category', $category);
    }

    public function scopeBetweenDates(Builder $query, $from = null, $to = null): Builder
    {
        if ($from) {
            $query->whereDate('expense_date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('expense_date', '<=', $to);
        }

        return $query;
    }

    public function scopeForMonth(Builder $query, ?int $year = null, ?int $month = null): Builder
    {
        if (blank($year) || blank($month)) {
            return $query;
        }

        $month = str_pad((string) $month, 2, '0', STR_PAD_LEFT);

        return $query->whereRaw("strftime('%Y', expense_date) = ?", [(string) $year])
            ->whereRaw("strftime('%m', expense_date) = ?", [$month]);
    }

    public function scopeFirstMobilization(Builder $query): Builder
    {
        return $query->where('is_first_mobilization', true);
    }

    public function scopeReimbursableOnly(Builder $query): Builder
    {
        return $query->where('paid_by', self::PAID_BY_CANDIDATE);
    }

    public function isPosted(): bool
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function isReimbursable(): bool
    {
        return $this->paid_by === self::PAID_BY_CANDIDATE;
    }

    public static function totalsByCurrency(Builder $query): array
    {
        $rows = (clone $query)
            ->selectRaw('currency, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->toArray();

        return [
            'USD' => (float) ($rows['USD'] ?? 0),
            'EUR' => (float) ($rows['EUR'] ?? 0),
            'GBP' => (float) ($rows['GBP'] ?? 0),
            'LYD' => (float) ($rows['LYD'] ?? 0),
        ];
    }

    public static function scopeLabels(): array
    {
        return [
            self::SCOPE_MOBILIZATION => 'Mobilization',
            self::SCOPE_MONTHLY => 'Monthly',
            self::SCOPE_ROTATION => 'Rotation',
            self::SCOPE_AD_HOC => 'Ad Hoc',
            self::SCOPE_REIMBURSABLE => 'Reimbursable',
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
            self::PAID_BY_CANDIDATE => 'Candidate',
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
            self::STATUS_POSTED => 'Posted',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}