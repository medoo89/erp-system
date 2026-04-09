<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreEmploymentRequirement extends Model
{
    protected $fillable = [
        'pre_employment_id',
        'title',
        'requirement_type',
        'is_required',
        'status',
        'deadline',
        'hr_note',
        'candidate_note',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'deadline' => 'date',
    ];

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class);
    }

    public function uploads()
    {
        return $this->hasMany(PreEmploymentUpload::class, 'pre_employment_requirement_id');
    }
}