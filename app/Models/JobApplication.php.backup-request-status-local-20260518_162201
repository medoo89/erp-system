<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    public const STATUS_HIRED = 'hired';
    protected $fillable = [
        'job_id',
        'full_name',
        'email',
        'phone',
        'phone_country_code',
        'phone_number',
        'whatsapp_country_code',
        'whatsapp_number',
        'cover_letter',
        'cv_path',
        'status',
        'candidate_request_status',
        'notes',

        'is_archived',
        'archive_reason',
        'archived_at',

        'decline_reason',
        'decline_notes',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }

    public function values()
    {
        return $this->hasMany(\App\Models\JobApplicationValue::class, 'job_application_id');
    }

    public function preEmployments()
    {
        return $this->hasMany(\App\Models\PreEmployment::class);
    }



    public function currentAcceptedNegotiation()
    {
        return $this->candidateRequests()
            ->where('type', 'salary_negotiation')
            ->whereIn('request_status', ['accepted', 'approved'])
            ->latest('id')
            ->first();
    }

    public function currentAcceptedDailyRate(): ?float
    {
        $request = $this->currentAcceptedNegotiation();

        if (! $request || blank($request->proposed_salary)) {
            return null;
        }

        return (float) $request->proposed_salary;
    }

    public function currentAcceptedCurrency(): ?string
    {
        $request = $this->currentAcceptedNegotiation();

        return $request?->currency ? strtoupper((string) $request->currency) : null;
    }

    public function applicationFilePayloads(): array
    {
        $files = [];

        if (filled($this->cv_path)) {
            $files[] = [
                'title' => 'Candidate CV',
                'category' => 'cv',
                'file_path' => $this->cv_path,
                'uploaded_by_type' => 'candidate',
                'notes' => 'Copied automatically from Job Application latest CV.',
            ];
        }

        $this->loadMissing('values.field');

        foreach ($this->values as $value) {
            $rawValue = $value->value ?? null;
            $fieldType = strtolower((string) ($value->field->field_type ?? ''));
            $fieldLabel = (string) ($value->field->label ?? 'Application File');

            if ($fieldType !== 'file' || blank($rawValue)) {
                continue;
            }

            $fieldKey = strtolower((string) ($value->field->field_key ?? ''));

            if ($fieldKey === 'cv_file') {
                continue;
            }

            $text = strtolower(trim($fieldKey . ' ' . $fieldLabel));

            $category = match (true) {
                str_contains($text, 'cv'), str_contains($text, 'resume') => 'cv',
                str_contains($text, 'passport') => 'passport',
                str_contains($text, 'visa') => 'visa',
                str_contains($text, 'medical') => 'medical',
                str_contains($text, 'photo'), str_contains($text, 'image') => 'personal_photo',
                str_contains($text, 'certificate'), str_contains($text, 'cert') => 'certificate',
                str_contains($text, 'contract') => 'contract',
                str_contains($text, 'ticket') => 'ticket',
                default => 'candidate_upload',
            };

            $files[] = [
                'title' => $category === 'cv' ? 'Candidate CV' : $fieldLabel,
                'category' => $category,
                'file_path' => ltrim((string) $rawValue, '/'),
                'uploaded_by_type' => 'candidate',
                'notes' => 'Copied automatically from Job Application file field.',
            ];
        }

        return $files;
    }

    public function candidateRequests()
    {
        return $this->hasMany(\App\Models\CandidateRequest::class)
            ->latest();
    }
}