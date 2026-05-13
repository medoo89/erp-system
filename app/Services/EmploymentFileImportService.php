<?php

namespace App\Services;

use App\Models\Employment;
use App\Models\JobApplication;
use App\Models\PreEmployment;
use Illuminate\Support\Str;

class EmploymentFileImportService
{
    public function copyFromPreEmployment(Employment $employment, PreEmployment $preEmployment): void
    {
        $preEmployment->loadMissing('files');

        $files = $preEmployment->files
            ->filter(fn ($file) => (bool) ($file->is_active ?? true))
            ->filter(fn ($file) => (bool) ($file->is_current ?? true))
            ->filter(fn ($file) => filled($file->file_path))
            ->values();

        foreach ($files as $file) {
            $category = $this->normalizeCategory($file->category, $file->title);
            $filePath = ltrim((string) $file->file_path, '/');

            $currentSameFile = $employment->files()
                ->where('category', $category)
                ->where('file_path', $filePath)
                ->where('is_current', true)
                ->exists();

            if ($currentSameFile) {
                continue;
            }

            $this->deactivateCurrentCategory($employment, $category);

            $maxVersion = $employment->files()
                ->where('category', $category)
                ->max('version_no');

            $employment->files()->create([
                'title' => $this->normalizeTitle($file->title, $category),
                'category' => $category,
                'document_date' => $file->document_date,
                'expiry_date' => $file->expiry_date,
                'version_no' => ($maxVersion ?? 0) + 1,
                'is_current' => true,
                'file_path' => $filePath,
                'uploaded_by_type' => $file->uploaded_by_type ?: 'candidate',
                'uploaded_by_user_id' => null,
                'notes' => trim(($file->notes ?: 'Imported automatically from Pre-Employment file.') . ' Latest version imported only.'),
                'is_active' => (bool) ($file->is_active ?? true),
                'pre_employment_id' => $preEmployment->id,
            ]);
        }
    }

    public function copyFromJobApplication(Employment $employment, JobApplication $jobApplication): void
    {
        if (filled($jobApplication->cv_path)) {
            $this->createCurrentEmploymentFile(
                employment: $employment,
                title: 'Candidate CV',
                category: 'cv',
                filePath: $jobApplication->cv_path,
                uploadedByType: 'candidate',
                notes: 'Imported automatically from Job Application latest CV.'
            );
        }

        $jobApplication->loadMissing(['values.field']);

        foreach ($jobApplication->values as $value) {
            $field = $value->field;

            if (! $field || ($field->field_type ?? null) !== 'file' || blank($value->value)) {
                continue;
            }

            $fieldKey = strtolower((string) ($field->field_key ?? ''));
            $fieldLabel = (string) ($field->label ?? 'Application File');

            /*
             * Important:
             * cv_file is already represented by job_applications.cv_path.
             * Skipping it here prevents duplicate CV files during Employment conversion.
             */
            if ($fieldKey === 'cv_file') {
                continue;
            }

            $category = $this->detectCategory($fieldKey, $fieldLabel);
            $title = $this->normalizeTitle($fieldLabel, $category);

            $this->createCurrentEmploymentFile(
                employment: $employment,
                title: $title,
                category: $category,
                filePath: $value->value,
                uploadedByType: 'candidate',
                notes: 'Imported automatically from Job Application field: ' . $fieldLabel
            );
        }
    }

    protected function createCurrentEmploymentFile(
        Employment $employment,
        string $title,
        string $category,
        string $filePath,
        string $uploadedByType = 'candidate',
        ?string $notes = null
    ): void {
        $category = $this->normalizeCategory($category, $title);
        $filePath = ltrim((string) $filePath, '/');

        if ($filePath === '') {
            return;
        }

        $currentSameFile = $employment->files()
            ->where('category', $category)
            ->where('file_path', $filePath)
            ->where('is_current', true)
            ->exists();

        if ($currentSameFile) {
            return;
        }

        $this->deactivateCurrentCategory($employment, $category);

        $maxVersion = $employment->files()
            ->where('category', $category)
            ->max('version_no');

        $employment->files()->create([
            'title' => $this->normalizeTitle($title, $category),
            'category' => $category,
            'document_date' => null,
            'expiry_date' => null,
            'version_no' => ($maxVersion ?? 0) + 1,
            'is_current' => true,
            'file_path' => $filePath,
            'uploaded_by_type' => $uploadedByType,
            'uploaded_by_user_id' => null,
            'notes' => $notes,
            'is_active' => true,
        ]);
    }

    protected function deactivateCurrentCategory(Employment $employment, string $category): void
    {
        $employment->files()
            ->where('category', $category)
            ->where('is_current', true)
            ->update(['is_current' => false]);
    }

    protected function detectCategory(string $fieldKey, string $fieldLabel): string
    {
        $text = strtolower(trim($fieldKey . ' ' . $fieldLabel));

        return match (true) {
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
    }

    protected function normalizeCategory(?string $category, ?string $title = null): string
    {
        $text = strtolower(trim((string) $category . ' ' . (string) $title));

        if (str_contains($text, 'cv') || str_contains($text, 'resume')) {
            return 'cv';
        }

        return Str::slug($category ?: 'candidate_upload', '_');
    }

    protected function normalizeTitle(?string $title, string $category): string
    {
        if ($category === 'cv') {
            return 'Candidate CV';
        }

        return $title ?: Str::headline($category);
    }

    /**
     * Copy candidate request uploaded files from the linked Job Application into Employment.
     * This covers files uploaded during Job Application Candidate Requests before Pre-Employment.
     */
    public function copyCandidateRequestFilesFromJobApplication(\App\Models\Employment $employment, \App\Models\JobApplication $jobApplication): void
    {
        try {
            $requests = $jobApplication->candidateRequests()->get();
        } catch (\Throwable $e) {
            return;
        }

        foreach ($requests as $request) {
            $decoded = json_decode((string) $request->candidate_response, true);

            if (! is_array($decoded)) {
                continue;
            }

            $uploadedFiles = $decoded['uploaded_files'] ?? [];

            if (! is_array($uploadedFiles)) {
                continue;
            }

            foreach ($uploadedFiles as $file) {
                $filePath = $file['stored_path']
                    ?? $file['path']
                    ?? $file['file_path']
                    ?? null;

                if (! filled($filePath)) {
                    continue;
                }

                $title = $file['item_label']
                    ?? $file['label']
                    ?? $file['original_name']
                    ?? $request->title
                    ?? 'Candidate Request File';

                $category = 'candidate_request';

                try {
                    \App\Models\EmploymentFile::query()->firstOrCreate(
                        [
                            'employment_id' => $employment->id,
                            'file_path' => $filePath,
                        ],
                        [
                            'title' => $title,
                            'category' => $category,
                            'uploaded_by_type' => 'candidate',
                            'uploaded_by_user_id' => null,
                            'notes' => 'Imported from Job Application Candidate Request #' . $request->id,
                            'is_active' => true,
                            'is_current' => true,
                        ]
                    );
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }

}
