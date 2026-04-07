<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'full_name',
        'email',
        'phone',
        'phone_country_code',
        'phone_number',
        'whatsapp_country_code',
        'whatsapp_number',
        'cover_letter',
        'cv_path',
        'status',
        'notes',
    ];

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }

    public function values()
    {
        return $this->hasMany(\App\Models\JobApplicationValue::class, 'job_application_id');
    }
}