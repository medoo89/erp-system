<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationFieldOption extends Model
{
    protected $fillable = [
        'field_id',
        'option_label',
        'option_value',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function ($option) {

            // sort_order auto
            if (blank($option->sort_order)) {
                $last = static::where('field_id', $option->field_id)->max('sort_order');
                $option->sort_order = ($last ?? 0) + 1;
            }

            // option_value auto
            if (blank($option->option_value) && filled($option->option_label)) {
                $option->option_value = str($option->option_label)->snake()->lower()->toString();
            }
        });
    }

    public function field()
    {
        return $this->belongsTo(JobApplicationField::class, 'field_id');
    }
}