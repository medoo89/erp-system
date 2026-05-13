<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySlipDay extends Model
{
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_SICK = 'sick';
    public const STATUS_LEAVE = 'leave';
    public const STATUS_UNPAID_LEAVE = 'unpaid_leave';
    public const STATUS_HOLIDAY = 'holiday';
    public const STATUS_TRAVEL = 'travel';
    public const STATUS_OTHER = 'other';

    public const STATUSES = [
        self::STATUS_PRESENT,
        self::STATUS_ABSENT,
        self::STATUS_SICK,
        self::STATUS_LEAVE,
        self::STATUS_UNPAID_LEAVE,
        self::STATUS_HOLIDAY,
        self::STATUS_TRAVEL,
        self::STATUS_OTHER,
    ];

    protected $fillable = [
        'salary_slip_id',
        'work_date',
        'day_name',
        'attendance_status',
        'is_paid_day',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'is_paid_day' => 'boolean',
    ];

    public function salarySlip()
    {
        return $this->belongsTo(SalarySlip::class, 'salary_slip_id');
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_SICK => 'Sick',
            self::STATUS_LEAVE => 'Leave',
            self::STATUS_UNPAID_LEAVE => 'Unpaid Leave',
            self::STATUS_HOLIDAY => 'Holiday',
            self::STATUS_TRAVEL => 'Travel',
            self::STATUS_OTHER => 'Other',
        ];
    }

    public function isAbsentLike(): bool
    {
        return in_array($this->attendance_status, [
            self::STATUS_ABSENT,
            self::STATUS_SICK,
            self::STATUS_LEAVE,
            self::STATUS_UNPAID_LEAVE,
            self::STATUS_OTHER,
        ], true);
    }
}