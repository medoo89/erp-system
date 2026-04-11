<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentFile extends Model
{
    protected $fillable = [
        'employment_id',
        'title',
        'category',
        'document_status',
        'apply_to_current_rotation',
        'document_date',
        'expiry_date',
        'version_no',
        'is_current',
        'file_path',
        'uploaded_by_type',
        'uploaded_by_user_id',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'document_date' => 'date',
        'expiry_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
        'apply_to_current_rotation' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $file) {
            if (blank($file->version_no)) {
                $maxVersion = static::query()
                    ->where('employment_id', $file->employment_id)
                    ->where('category', $file->category)
                    ->max('version_no');

                $file->version_no = ($maxVersion ?? 0) + 1;
            }
        });

        static::saved(function (self $file) {
            if ($file->is_current && filled($file->category)) {
                static::query()
                    ->where('employment_id', $file->employment_id)
                    ->where('category', $file->category)
                    ->where('id', '!=', $file->id)
                    ->update(['is_current' => false]);
            }

            $employment = $file->employment;

            if (! $employment) {
                return;
            }

            // Auto-update Employment from uploaded file metadata
            if ($file->is_current) {
                if ($file->category === 'visa') {
                    $employment->visa_status = $file->document_status ?: $employment->visa_status;
                    $employment->visa_issue_date = $file->document_date ?: $employment->visa_issue_date;
                    $employment->visa_expiry_date = $file->expiry_date ?: $employment->visa_expiry_date;
                }

                if ($file->category === 'medical') {
                    $employment->medical_status = $file->document_status ?: $employment->medical_status;
                    $employment->medical_date = $file->document_date ?: $employment->medical_date;
                    $employment->medical_expiry_date = $file->expiry_date ?: $employment->medical_expiry_date;
                }

                if ($file->category === 'contract') {
                    $employment->contract_status = $file->document_status ?: $employment->contract_status;
                    $employment->contract_start_date = $file->document_date ?: $employment->contract_start_date;
                    $employment->contract_end_date = $file->expiry_date ?: $employment->contract_end_date;
                }

                if (in_array($file->category, ['travel_request', 'ticket'], true) && $file->apply_to_current_rotation) {
                    $rotation = $employment->currentRotation ?: $employment->rotations()->latest('from_date')->first();

                    if ($rotation) {
                        if ($file->category === 'travel_request') {
                            $rotation->travel_status = $file->document_status ?: 'request_received';
                            $rotation->travel_request_file_path = $file->file_path;
                        }

                        if ($file->category === 'ticket') {
                            $rotation->travel_status = $file->document_status ?: 'ticket_booked';
                            $rotation->ticket_file_path = $file->file_path;
                        }

                        $rotation->save();
                    }
                }
            }

            $employment->save();
        });
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }

    public function uploadedByUser()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}