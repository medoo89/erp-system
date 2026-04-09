<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreEmploymentUpload extends Model
{
    protected $fillable = [
        'pre_employment_id',
        'pre_employment_requirement_id',
        'title',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'status',
        'review_note',
        'uploaded_by_candidate',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'uploaded_by_candidate' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class);
    }

    public function requirement()
    {
        return $this->belongsTo(PreEmploymentRequirement::class, 'pre_employment_requirement_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}