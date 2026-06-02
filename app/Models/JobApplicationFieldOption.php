<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        static::creating(function (self $option): void {
            if (blank($option->option_value) && filled($option->option_label)) {
                $option->option_value = str($option->option_label)->snake()->lower()->toString();
            }

            if (blank($option->sort_order)) {
                $lastSort = static::query()
                    ->where('field_id', $option->field_id)
                    ->max('sort_order');

                $option->sort_order = ((int) ($lastSort ?? 0)) + 1;
            }
        });
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(JobApplicationField::class, 'field_id');
    }
}
