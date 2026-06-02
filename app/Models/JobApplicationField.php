<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplicationField extends Model
{
    public const TYPE_TEXT = 'text';
    public const TYPE_TEXTAREA = 'textarea';
    public const TYPE_NUMBER = 'number';
    public const TYPE_DATE = 'date';
    public const TYPE_FILE = 'file';
    public const TYPE_SELECT = 'select';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_MULTI_CHECKBOX = 'multi_checkbox';

    public static function fieldTypeOptions(): array
    {
        return [
            self::TYPE_TEXT => 'Text',
            self::TYPE_TEXTAREA => 'Textarea',
            self::TYPE_NUMBER => 'Number',
            self::TYPE_DATE => 'Date',
            self::TYPE_FILE => 'File Upload',
            self::TYPE_SELECT => 'Dropdown / Select',
            self::TYPE_CHECKBOX => 'Checkbox - Single Choice',
            self::TYPE_MULTI_CHECKBOX => 'Multi Checkbox - Multiple Choices',
        ];
    }

    public function usesOptions(): bool
    {
        return in_array($this->field_type, [
            self::TYPE_SELECT,
            self::TYPE_CHECKBOX,
            self::TYPE_MULTI_CHECKBOX,
        ], true);
    }

    protected $fillable = [
        'job_id',
        'label',
        'field_key',
        'field_type',
        'field_group',
        'placeholder',
        'help_text',
        'is_required',
        'is_active',
        'is_global',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function ($field) {
            // لو ما تحددش field_key، يتولد من label
            if (blank($field->field_key) && filled($field->label)) {
                $field->field_key = str($field->label)->snake()->lower()->toString();
            }

            // sort_order أوتوماتيك حسب group
            if (blank($field->sort_order)) {
                $lastSort = static::where('field_group', $field->field_group)->max('sort_order');
                $field->sort_order = ($lastSort ?? 0) + 1;
            }
        });
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function options()
    {
        return $this->hasMany(JobApplicationFieldOption::class, 'field_id')
            ->orderBy('sort_order');
    }

    public function templates()
    {
        return $this->belongsToMany(
            JobApplicationTemplate::class,
            'job_application_field_template'
        )->withPivot('sort_order')
         ->withTimestamps();
    }

    public function jobApplicationTemplates()
    {
        return $this->belongsToMany(
            JobApplicationTemplate::class,
            'job_application_field_template'
        )->withPivot('sort_order')
         ->withTimestamps();
    }
}