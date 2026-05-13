<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalarySlipAttachment extends Model
{
    public const TYPE_TIMESHEET = 'timesheet';
    public const TYPE_ATTENDANCE_SHEET = 'attendance_sheet';
    public const TYPE_DAY_SCHEDULE = 'day_schedule';
    public const TYPE_SUPPORTING_FILE = 'supporting_file';

    protected $fillable = [
        'salary_slip_id',
        'attachment_type',
        'title',
        'file_path',
        'original_name',
        'mime_type',
        'size_bytes',
        'notes',
        'uploaded_by',
    ];

    public static function typeLabels(): array
    {
        return [
            self::TYPE_TIMESHEET => 'Timesheet',
            self::TYPE_ATTENDANCE_SHEET => 'Attendance Sheet',
            self::TYPE_DAY_SCHEDULE => 'Day Schedule',
            self::TYPE_SUPPORTING_FILE => 'Supporting File',
        ];
    }

    public function salarySlip(): BelongsTo
    {
        return $this->belongsTo(SalarySlip::class, 'salary_slip_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
