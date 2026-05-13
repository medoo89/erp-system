<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PreEmploymentPortalField extends Model
{
    protected $fillable = [
        'pre_employment_id',
        'label',
        'field_key',
        'field_type',
        'document_category',
        'signature_source_file_name',
        'signature_source_file_path',
        'signature_status',        'request_type',
        'document_to_sign_path',
        'document_to_sign_original_name',
        'signed_file_required',

        'is_required',
        'is_active',
        'visible_to_candidate',
        'instructions',
        'sort_order',
        'source_file_path',
        'source_original_name',
        'signed_file_path',
        'signed_original_name',
        'signed_at',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'visible_to_candidate' => 'boolean',
        'signed_file_required' => 'boolean',
        'signed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $field) {
            if (blank($field->field_key)) {
                $field->field_key = Str::slug($field->label, '_');
            }

            if (blank($field->sort_order)) {
                $maxSortOrder = static::query()
                    ->where('pre_employment_id', $field->pre_employment_id)
                    ->max('sort_order');

                $field->sort_order = ($maxSortOrder ?? 0) + 1;
            }
        });
    }

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class);
    }

    public function value()
    {
        return $this->hasOne(PreEmploymentPortalValue::class, 'portal_field_id');
    }
}