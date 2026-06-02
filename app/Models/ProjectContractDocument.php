<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContractDocument extends Model
{
    protected $fillable = [
        'project_contract_id',
        'project_id',
        'client_id',
        'document_type',
        'title',
        'document_no',
        'file_paths',
        'amount',
        'currency',
        'issue_date',
        'expiry_date',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'file_paths' => 'array',
        'amount' => 'decimal:2',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function projectContract(): BelongsTo
    {
        return $this->belongsTo(ProjectContract::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
