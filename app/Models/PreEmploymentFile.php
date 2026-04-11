<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreEmploymentFile extends Model
{
    protected $fillable = [
        'pre_employment_id',
        'title',
        'category',
        'document_date',
        'expiry_date',
        'version_no',
        'is_current',
        'file_path',
        'uploaded_by_type',
        'uploaded_by_user_id',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiry_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $file) {
            if (blank($file->version_no)) {
                $maxVersion = static::query()
                    ->where('pre_employment_id', $file->pre_employment_id)
                    ->where('category', $file->category)
                    ->max('version_no');

                $file->version_no = ($maxVersion ?? 0) + 1;
            }
        });

        static::saved(function (self $file) {
            if ($file->is_current && filled($file->category)) {
                static::query()
                    ->where('pre_employment_id', $file->pre_employment_id)
                    ->where('category', $file->category)
                    ->where('id', '!=', $file->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class);
    }

    public function uploadedByUser()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}