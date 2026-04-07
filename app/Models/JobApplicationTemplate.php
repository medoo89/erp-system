<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationTemplate extends Model
{
    // 🔹 اسم الجدول الحقيقي في قاعدة البيانات
    protected $table = 'job_application_templates';

    // 🔹 الحقول التي يسمح Laravel بحفظها وتحديثها
    protected $fillable = [
        'name',         // اسم التمبليت
        'description',  // وصف التمبليت
        'is_active',    // هل التمبليت مفعّل
    ];

    // 🔹 كل تمبليت يمكن أن يكون مربوط بعدة وظائف
    public function jobs()
    {
        return $this->hasMany(\App\Models\Job::class, 'template_id', 'id');
    }

    // 🔹 الحقول المرتبطة بهذا التمبليت
    public function fields()
    {
        return $this->belongsToMany(
            \App\Models\JobApplicationField::class,   // الموديل المرتبط
            'job_application_field_template',         // اسم جدول الربط
            'job_application_template_id',            // المفتاح الخاص بالتمبليت في pivot
            'job_application_field_id'                // المفتاح الخاص بالحقل في pivot
        )
        ->withPivot('sort_order')
        ->withTimestamps();
    }
}