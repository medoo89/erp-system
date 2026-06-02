<?php

namespace App\Services;

use App\Models\Employment;
use App\Models\EmploymentFile;
use App\Models\JobApplication;
use App\Models\PreEmployment;
use Illuminate\Support\Str;

class EmploymentFileImportService
{
    /**
     * Copy every useful file from Pre-Employment into Employment.
     *
     * Sources covered:
     * - pre_employment_files
     * - pre_employment_uploads
     * - pre_employment_portal_values where the linked field is a file field
     */
    public function copyFromPreEmployment(Employment $employment, PreEmployment $preEmployment): void
    {
        $preEmployment->loadMissing([
            'files',
            'uploads.requirement',
            'portalValues.field',
        ]);

        foreach ($preEmployment->files as $file) {
            if (! (bool) ($file->is_active ?? true) || ! filled($file->file_path)) {
                continue;
            }

            $this->createEmploymentFileOnce(
                employment: $employment,
                preEmployment: $preEmployment,
                title: $file->title ?? 'Pre-Employment File',
                category: $file->category ?? 'pre_employment_file',
                filePath: $file->file_path,
                uploadedByType: $file->uploaded_by_type ?: 'admin',
                uploadedByUserId: $file->uploaded_by_user_id ?? null,
                documentDate: $file->document_date ?? null,
                expiryDate: $file->expiry_date ?? null,
                notes: trim((string) ($file->notes ?? '') . "\nImported automatically from Pre-Employment Files.")
            );
        }

        foreach ($preEmployment->uploads as $upload) {
            if (! filled($upload->file_path)) {
                continue;
            }

            $label = $upload->title
                ?: $upload->original_name
                ?: $upload->requirement?->title
                ?: $upload->document_type
                ?: 'Pre-Employment Upload';

            $this->createEmploymentFileOnce(
                employment: $employment,
                preEmployment: $preEmployment,
                title: $label,
                category: $upload->document_type ?: $label,
                filePath: $upload->file_path,
                uploadedByType: (bool) ($upload->uploaded_by_candidate ?? false) ? 'candidate' : 'admin',
                uploadedByUserId: null,
                documentDate: null,
                expiryDate: null,
                notes: 'Imported automatically from Pre-Employment Uploads. Upload ID: ' . $upload->id
                    . ($upload->status ? "\nUpload status: {$upload->status}" : '')
            );
        }

        foreach ($preEmployment->portalValues as $value) {
            $field = $value->field;

            if (! $field || strtolower((string) ($field->field_type ?? '')) !== 'file') {
                continue;
            }

            $label = $field->label ?: 'Pre-Employment Portal File';
            $category = $field->document_category ?: $field->field_key ?: $label;

            foreach ($this->extractFilePayloads($value->value) as $payload) {
                $filePath = $payload['file_path'] ?? null;

                if (! filled($filePath)) {
                    continue;
                }

                $title = $payload['title'] ?: $label;

                $this->createEmploymentFileOnce(
                    employment: $employment,
                    preEmployment: $preEmployment,
                    title: $title,
                    category: $category,
                    filePath: $filePath,
                    uploadedByType: $value->submitted_by_type ?: 'candidate',
                    uploadedByUserId: $value->submitted_by_user_id ?? null,
                    documentDate: null,
                    expiryDate: null,
                    notes: 'Imported automatically from Pre-Employment Portal field: ' . $label
                );
            }
        }
    }

    /**
     * Copy direct Job Application files into Employment.
     *
     * Uses JobApplication::applicationFilePayloads() so future job-opening file fields
     * are carried forward without duplicating logic here.
     */
    public function copyFromJobApplication(Employment $employment, JobApplication $jobApplication): void
    {
        foreach (collect($jobApplication->applicationFilePayloads()) as $payload) {
            $this->createEmploymentFileOnce(
                employment: $employment,
                preEmployment: $employment->preEmployment,
                title: $payload['title'] ?? 'Job Application File',
                category: $payload['category'] ?? 'job_application_file',
                filePath: $payload['file_path'] ?? $payload['path'] ?? $payload['stored_path'] ?? null,
                uploadedByType: $payload['uploaded_by_type'] ?? 'candidate',
                uploadedByUserId: null,
                documentDate: null,
                expiryDate: null,
                notes: $payload['notes'] ?? 'Imported automatically from Job Application.'
            );
        }
    }

    /**
     * Copy candidate request uploaded files from the linked Job Application into Employment.
     *
     * This covers files uploaded during Job Application Candidate Requests before
     * or during Pre-Employment.
     */
    public function copyCandidateRequestFilesFromJobApplication(Employment $employment, JobApplication $jobApplication): void
    {
        try {
            $requests = $jobApplication->candidateRequests()->with('items')->get();
        } catch (\Throwable $e) {
            report($e);

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
                if (! is_array($file)) {
                    continue;
                }

                $filePath = $file['stored_path']
                    ?? $file['path']
                    ?? $file['file_path']
                    ?? $file['value']
                    ?? null;

                if (! filled($filePath)) {
                    continue;
                }

                $title = $file['item_label']
                    ?? $file['label']
                    ?? $file['original_name']
                    ?? $file['name']
                    ?? $request->title
                    ?? 'Candidate Request File';

                $categoryText = trim((string) (
                    ($file['item_label'] ?? '')
                    . ' '
                    . ($file['label'] ?? '')
                    . ' '
                    . ($request->title ?? '')
                    . ' '
                    . ($request->type ?? '')
                ));

                $this->createEmploymentFileOnce(
                    employment: $employment,
                    preEmployment: $employment->preEmployment,
                    title: $title,
                    category: $categoryText ?: 'candidate_request',
                    filePath: $filePath,
                    uploadedByType: 'candidate',
                    uploadedByUserId: null,
                    documentDate: null,
                    expiryDate: null,
                    notes: 'Imported automatically from Job Application Candidate Request #' . $request->id
                );
            }
        }
    }

    /**
     * Create one EmploymentFile if the same normalized path is not already attached
     * to this employment profile.
     */
    protected function createEmploymentFileOnce(
        Employment $employment,
        ?PreEmployment $preEmployment,
        ?string $title,
        ?string $category,
        mixed $filePath,
        string $uploadedByType = 'candidate',
        ?int $uploadedByUserId = null,
        mixed $documentDate = null,
        mixed $expiryDate = null,
        ?string $notes = null
    ): void {
        $cleanPath = $this->cleanPath($filePath);

        if (! filled($cleanPath)) {
            return;
        }

        $normalizedCategory = $this->normalizeCategory($category, $title);
        $normalizedTitle = $this->normalizeTitle($title, $normalizedCategory);

        $alreadyExists = EmploymentFile::query()
            ->where('employment_id', $employment->id)
            ->where('file_path', $cleanPath)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $maxVersion = EmploymentFile::query()
            ->where('employment_id', $employment->id)
            ->where('category', $normalizedCategory)
            ->max('version_no');

        EmploymentFile::query()->create([
            'employment_id' => $employment->id,
            'pre_employment_id' => $preEmployment?->id,
            'title' => $normalizedTitle,
            'category' => $normalizedCategory,
            'document_status' => 'imported',
            'apply_to_current_rotation' => false,
            'document_date' => $documentDate ?: null,
            'expiry_date' => $expiryDate ?: null,
            'version_no' => ($maxVersion ?? 0) + 1,
            'is_current' => true,
            'file_path' => $cleanPath,
            'uploaded_by_type' => $uploadedByType ?: 'candidate',
            'uploaded_by_user_id' => $uploadedByUserId,
            'notes' => trim((string) $notes),
            'is_active' => true,
            'is_visible_to_employee_portal' => $this->shouldBeVisibleToEmployeePortal($normalizedCategory),
        ]);
    }

    /**
     * Convert stored paths into one consistent format for duplicate detection.
     */
    protected function cleanPath(mixed $path): ?string
    {
        if (is_array($path)) {
            $path = $path['stored_path']
                ?? $path['path']
                ?? $path['file_path']
                ?? $path['value']
                ?? null;
        }

        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#^storage/#', '', $path);
        $path = preg_replace('#^public/#', '', $path);
        $path = preg_replace('#^app/public/#', '', $path);

        return ltrim($path, '/');
    }

    /**
     * Extract file paths from portal values.
     * Supports plain path strings, JSON strings, arrays, and multiple uploaded files.
     */
    protected function extractFilePayloads(mixed $rawValue): array
    {
        if ($rawValue === null || $rawValue === '') {
            return [];
        }

        if (is_string($rawValue)) {
            $decoded = json_decode($rawValue, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rawValue = $decoded;
            } else {
                return [[
                    'title' => basename($rawValue),
                    'file_path' => $rawValue,
                ]];
            }
        }

        if (! is_array($rawValue)) {
            return [];
        }

        $isSinglePayload = isset($rawValue['stored_path'])
            || isset($rawValue['path'])
            || isset($rawValue['file_path'])
            || isset($rawValue['value']);

        $items = $isSinglePayload ? [$rawValue] : $rawValue;
        $files = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                $files[] = [
                    'title' => basename($item),
                    'file_path' => $item,
                ];

                continue;
            }

            if (! is_array($item)) {
                continue;
            }

            $filePath = $item['stored_path']
                ?? $item['path']
                ?? $item['file_path']
                ?? $item['value']
                ?? null;

            $title = $item['original_name']
                ?? $item['name']
                ?? $item['title']
                ?? ($filePath ? basename((string) $filePath) : null);

            $files[] = [
                'title' => $title,
                'file_path' => $filePath,
            ];
        }

        return $files;
    }

    protected function normalizeCategory(?string $category, ?string $title = null): string
    {
        $text = strtolower(trim((string) $category . ' ' . (string) $title));

        return match (true) {
            str_contains($text, 'cv'), str_contains($text, 'resume') => 'cv',
            str_contains($text, 'passport') => 'passport',
            str_contains($text, 'visa') => 'visa',
            str_contains($text, 'medical'), str_contains($text, 'health') => 'medical',
            str_contains($text, 'photo'), str_contains($text, 'image'), str_contains($text, 'picture') => 'personal_photo',
            str_contains($text, 'certificate'), str_contains($text, 'cert'), str_contains($text, 'atex'), str_contains($text, 'bosiet') => 'certificate',
            str_contains($text, 'contract'), str_contains($text, 'agreement') => 'contract',
            str_contains($text, 'ticket') => 'ticket',
            str_contains($text, 'travel') => 'travel_request',
            str_contains($text, 'candidate request'), str_contains($text, 'salary_negotiation') => 'candidate_request',
            default => Str::slug($category ?: 'candidate_upload', '_') ?: 'candidate_upload',
        };
    }

    protected function normalizeTitle(?string $title, string $category): string
    {
        if ($category === 'cv') {
            return 'Candidate CV';
        }

        if ($category === 'personal_photo') {
            return filled($title) ? (string) $title : 'Personal Photo';
        }

        return filled($title) ? (string) $title : Str::headline($category);
    }

    protected function shouldBeVisibleToEmployeePortal(string $category): bool
    {
        /*
         * Imported candidate files should be visible by default so the employee
         * can access their historical submitted documents later.
         * Admin can still hide any file manually from Employment Files.
         */
        return true;
    }
}
