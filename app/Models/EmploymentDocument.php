<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentDocument extends Model
{
    protected $fillable = [
        'employment_id',
        'document_type',
        'reference',
        'reference_year',
        'reference_sequence',
        'title',
        'status',
        'file_path',
        'docx_file_path',
        'pdf_file_path',
        'final_file_path',
        'generated_by_user_id',
        'generated_at',
        'sent_at',
        'signed_at',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'signed_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $document) {
            if (blank($document->reference)) {
                [$year, $sequence, $reference] = static::generateReference($document->document_type);

                $document->reference_year = $year;
                $document->reference_sequence = $sequence;
                $document->reference = $reference;
            }
        });
    }

    public static function generateReference(string $documentType): array
    {
        $year = now()->year;

        $lastSequence = static::query()
            ->where('document_type', $documentType)
            ->where('reference_year', $year)
            ->max('reference_sequence');

        $sequence = ($lastSequence ?? 0) + 1;

        $prefix = match ($documentType) {
            'caf' => 'CAF',
            'general_letter' => 'GL',
            default => 'DOC',
        };

        $reference = sprintf('%s-%s-%05d', $prefix, $year, $sequence);

        return [$year, $sequence, $reference];
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }

    public function generatedByUser()
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }

    public function getPreferredFilePathAttribute(): ?string
    {
        return $this->final_file_path
            ?: $this->pdf_file_path
            ?: $this->docx_file_path
            ?: $this->file_path;
    }
}