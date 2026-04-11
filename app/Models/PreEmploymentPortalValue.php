<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreEmploymentPortalValue extends Model
{
    protected $fillable = [
        'pre_employment_id',
        'portal_field_id',
        'value',
        'submitted_at',
        'submitted_by_type',
        'submitted_by_user_id',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class);
    }

    public function field()
    {
        return $this->belongsTo(PreEmploymentPortalField::class, 'portal_field_id');
    }

    public function submittedByUser()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }
}