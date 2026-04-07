<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationValue extends Model
{
    // 🔹 الحقول التي يسمح Laravel بحفظها
    protected $fillable = [
        'job_application_id', // رقم طلب التقديم
        'field_id',           // رقم الحقل
        'value',              // القيمة المدخلة أو مسار الملف
    ];

    // 🔹 القيمة تنتمي إلى طلب تقديم واحد
    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    // 🔹 القيمة تنتمي إلى حقل واحد
    public function field()
    {
        return $this->belongsTo(JobApplicationField::class, 'field_id');
    }
}