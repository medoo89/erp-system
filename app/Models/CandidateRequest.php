<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateRequest extends Model
{
    protected $table = 'candidate_requests';

    protected $fillable = [
        'job_application_id',
        'type',
        'title',
        'notes',
        'request_status',
        'due_date',
        'requires_upload',
        'requested_file_label',
        'allow_multiple_files',
        'proposed_salary',
        'currency',
        'requires_approval',
        'candidate_response',
        'candidate_counter_offer',
        'responded_at',
        'created_by',
        'public_token',
    ];

    protected $casts = [
        'due_date' => 'date',
        'responded_at' => 'datetime',
        'requires_upload' => 'boolean',
        'allow_multiple_files' => 'boolean',
        'requires_approval' => 'boolean',
        'proposed_salary' => 'decimal:2',
        'candidate_counter_offer' => 'decimal:2',
    ];

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CandidateRequestItem::class, 'candidate_request_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function isNegotiation(): bool
    {
        return $this->type === 'salary_negotiation';
    }
}