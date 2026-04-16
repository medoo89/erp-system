<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateRequestItem extends Model
{
    protected $table = 'candidate_request_items';

    protected $fillable = [
        'candidate_request_id',
        'item_type',
        'label',
        'file_format',
        'is_required',
        'allow_multiple',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'allow_multiple' => 'boolean',
    ];

    public function candidateRequest(): BelongsTo
    {
        return $this->belongsTo(CandidateRequest::class, 'candidate_request_id');
    }
}