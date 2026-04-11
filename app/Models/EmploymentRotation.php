<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentRotation extends Model
{
    protected $fillable = [
        'employment_id',
        'rotation_label',
        'status',
        'rotation_pattern',
        'travel_status',
        'travel_request_file_path',
        'ticket_file_path',
        'from_date',
        'to_date',
        'mobilization_date',
        'demobilization_date',
        'notes',
        'is_current',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'mobilization_date' => 'date',
        'demobilization_date' => 'date',
        'is_current' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $rotation) {
            if ($rotation->is_current) {
                static::query()
                    ->where('employment_id', $rotation->employment_id)
                    ->where('id', '!=', $rotation->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }
}