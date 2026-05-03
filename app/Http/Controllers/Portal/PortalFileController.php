<?php

namespace App\Http\Controllers\Portal;

use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PortalFileController extends PortalBaseController
{
    public function index(Request $request)
    {
        $shared = $this->sharedPortalData($request);

        if (blank($shared['portalAccount'] ?? null)) {
            return redirect()->route('portal.login');
        }

        if (blank($shared['portalEmployment'] ?? null)) {
            abort(403, 'No employment is linked to this portal account.');
        }

        $employment = $shared['portalEmployment'];
        $preEmployment = $employment?->preEmployment;

        $files = $this->collectFiles($employment, $preEmployment);

        return view('portal.files.index', array_merge($shared, [
            'files' => $files,
        ]));
    }

    public function open(Request $request, string $type, int $id)
    {
        $shared = $this->sharedPortalData($request);

        if (blank($shared['portalAccount'] ?? null)) {
            return redirect()->route('portal.login');
        }

        if (blank($shared['portalEmployment'] ?? null)) {
            abort(403, 'No employment is linked to this portal account.');
        }

        [$record, $file] = $this->findAuthorizedFile($shared['portalEmployment'], $type, $id);

        abort_if(! $record || ! $file, 404);

        $absolutePath = $this->resolveAbsolutePath($file['file_path']);
        abort_if(! $absolutePath, 404, 'File not found.');

        return response()->file($absolutePath);
    }

    public function download(Request $request, string $type, int $id): BinaryFileResponse
    {
        $shared = $this->sharedPortalData($request);

        if (blank($shared['portalAccount'] ?? null)) {
            abort(403, 'Portal login required.');
        }

        if (blank($shared['portalEmployment'] ?? null)) {
            abort(403, 'No employment is linked to this portal account.');
        }

        [$record, $file] = $this->findAuthorizedFile($shared['portalEmployment'], $type, $id);

        abort_if(! $record || ! $file, 404);

        $absolutePath = $this->resolveAbsolutePath($file['file_path']);
        abort_if(! $absolutePath, 404, 'File not found.');

        $downloadName = $this->safeDownloadName(
            $file['title'] ?: basename($absolutePath),
            $absolutePath
        );

        return response()->download($absolutePath, $downloadName);
    }

    protected function portalVisibleFileCategories(): array
    {
        return [
            'cv',
            'candidate_upload',
            'passport',
            'visa',
            'medical',
            'personal_photo',
            'certificate',
            'caf',
            'gl',
            'contract',
            'rotation_document',
            'internal_document',
        ];
    }

    protected function portalHiddenFileKeywords(): array
    {
        return [
            'expense',
            'expenses',
            'invoice',
            'receipt',
            'payment',
            'payment_proof',
            'bank',
            'treasury',
            'salary',
            'payslip',
            'payroll',
            'voucher',
            'cost',
            'finance',
            'financial',
        ];
    }

    protected function isPortalVisibleFile(?string $category, ?string $title = null, ?bool $isCurrent = true): bool
    {
        if ($isCurrent === false) {
            return false;
        }

        $category = strtolower(trim((string) $category));
        $title = strtolower(trim((string) $title));
        $combined = $category . ' ' . $title;

        foreach ($this->portalHiddenFileKeywords() as $keyword) {
            if (str_contains($combined, $keyword)) {
                return false;
            }
        }

        return in_array($category, $this->portalVisibleFileCategories(), true)
            || str_contains($combined, 'cv')
            || str_contains($combined, 'resume')
            || str_contains($combined, 'passport')
            || str_contains($combined, 'visa')
            || str_contains($combined, 'medical')
            || str_contains($combined, 'certificate')
            || str_contains($combined, 'contract')
            || str_contains($combined, 'rotation');
    }

    protected function normalizePortalFileTitle(?string $title, ?string $category = null): string
    {
        $category = strtolower(trim((string) $category));

        if ($category === 'cv') {
            return 'Candidate CV';
        }

        return $title ?: ucfirst(str_replace('_', ' ', $category ?: 'File'));
    }

    protected function collectFiles($employment, $preEmployment): Collection
    {
        if ($employment) {
            $employment->loadMissing([
                'files',
                'documents',
                'preEmployment.files',
            ]);
        }

        $items = collect();

        foreach (($employment?->files ?? collect()) as $file) {
            $category = $file->category ?? 'employment_file';
            $title = $file->title ?? $file->file_name ?? ('Employment File #' . $file->id);

            if (! $this->isPortalVisibleFile($category, $title, (bool) ($file->is_current ?? true))) {
                continue;
            }

            $items->push([
                'source_type' => 'employment_file',
                'source_id' => (int) $file->id,
                'title' => $this->normalizePortalFileTitle($title, $category),
                'description' => $category,
                'file_path' => $file->file_path ?? $file->path ?? null,
                'created_at' => $file->created_at,
            ]);
        }

        foreach (($employment?->documents ?? collect()) as $doc) {
            $category = $doc->document_type ?? 'employment_document';
            $title = $doc->document_name ?? $doc->title ?? ('Employment Document #' . $doc->id);

            if (! $this->isPortalVisibleFile($category, $title, true)) {
                continue;
            }

            $items->push([
                'source_type' => 'employment_document',
                'source_id' => (int) $doc->id,
                'title' => $title,
                'description' => $category,
                'file_path' => $doc->preferred_file_path ?? $doc->final_file_path ?? $doc->pdf_file_path ?? $doc->file_path ?? $doc->path ?? null,
                'created_at' => $doc->created_at,
            ]);
        }

        foreach (($preEmployment?->files ?? collect()) as $file) {
            $category = $file->category ?? 'pre_employment_file';
            $title = $file->title ?? $file->file_name ?? ('Pre-Employment File #' . $file->id);

            if (! $this->isPortalVisibleFile($category, $title, (bool) ($file->is_current ?? true))) {
                continue;
            }

            $items->push([
                'source_type' => 'pre_employment_file',
                'source_id' => (int) $file->id,
                'title' => $this->normalizePortalFileTitle($title, $category),
                'description' => $category,
                'file_path' => $file->file_path ?? $file->path ?? null,
                'created_at' => $file->created_at,
            ]);
        }

        $jobApplication = $this->resolveJobApplication($preEmployment);

        if ($jobApplication) {
            $cvPath = $jobApplication->cv_path ?? $jobApplication->cv_file ?? $jobApplication->file_path ?? null;

            if (filled($cvPath)) {
                $items->push([
                    'source_type' => 'job_application_cv',
                    'source_id' => (int) $jobApplication->id,
                    'title' => ($jobApplication->full_name ?: 'Candidate') . ' CV',
                    'description' => 'cv',
                    'file_path' => $cvPath,
                    'created_at' => $jobApplication->updated_at ?: $jobApplication->created_at,
                ]);
            }
        }

        return $items
            ->filter(fn ($item) => filled($item['file_path']))
            ->unique(fn ($item) => strtolower($item['description'] ?? '') . '|' . ($item['file_path'] ?? ''))
            ->sortByDesc(fn ($item) => optional($item['created_at'])->timestamp ?? 0)
            ->values();
    }

    protected function findAuthorizedFile($employment, string $type, int $id): array
    {
        $preEmployment = $employment?->preEmployment;

        if ($employment) {
            $employment->loadMissing([
                'files',
                'documents',
                'preEmployment.files',
            ]);
        }

        return match ($type) {
            'employment_file' => [
                $employment?->files?->firstWhere('id', $id),
                $this->mapEmploymentFile($employment?->files?->firstWhere('id', $id)),
            ],
            'employment_document' => [
                $employment?->documents?->firstWhere('id', $id),
                $this->mapEmploymentDocument($employment?->documents?->firstWhere('id', $id)),
            ],
            'pre_employment_file' => [
                $preEmployment?->files?->firstWhere('id', $id),
                $this->mapPreEmploymentFile($preEmployment?->files?->firstWhere('id', $id)),
            ],
            'job_application_cv' => [
                ($this->resolveJobApplication($preEmployment) && (int) $this->resolveJobApplication($preEmployment)->id === $id)
                    ? $this->resolveJobApplication($preEmployment)
                    : null,
                $this->mapJobApplicationCv(
                    ($this->resolveJobApplication($preEmployment) && (int) $this->resolveJobApplication($preEmployment)->id === $id)
                        ? $this->resolveJobApplication($preEmployment)
                        : null
                ),
            ],
            default => [null, null],
        };
    }

    protected function resolveJobApplication($preEmployment): ?JobApplication
    {
        if (! $preEmployment) {
            return null;
        }

        if (filled($preEmployment->job_application_id)) {
            return JobApplication::query()->find($preEmployment->job_application_id);
        }

        if (method_exists($preEmployment, 'jobApplication')) {
            try {
                return $preEmployment->jobApplication()->first();
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    protected function mapEmploymentFile($file): ?array
    {
        if (! $file) {
            return null;
        }

        if (! $this->isPortalVisibleFile($file->category ?? null, $file->title ?? null, (bool) ($file->is_current ?? true))) {
            return null;
        }

        return [
            'title' => $this->normalizePortalFileTitle($file->title ?? $file->file_name ?? ('Employment File #' . $file->id), $file->category ?? null),
            'file_path' => $file->file_path ?? $file->path ?? null,
        ];
    }

    protected function mapEmploymentDocument($doc): ?array
    {
        if (! $doc) {
            return null;
        }

        if (! $this->isPortalVisibleFile($doc->document_type ?? null, $doc->title ?? null, true)) {
            return null;
        }

        return [
            'title' => $doc->document_name ?? $doc->title ?? ('Employment Document #' . $doc->id),
            'file_path' => $doc->preferred_file_path ?? $doc->final_file_path ?? $doc->pdf_file_path ?? $doc->file_path ?? $doc->path ?? null,
        ];
    }

    protected function mapPreEmploymentFile($file): ?array
    {
        if (! $file) {
            return null;
        }

        if (! $this->isPortalVisibleFile($file->category ?? null, $file->title ?? null, (bool) ($file->is_current ?? true))) {
            return null;
        }

        return [
            'title' => $this->normalizePortalFileTitle($file->title ?? $file->file_name ?? ('Pre-Employment File #' . $file->id), $file->category ?? null),
            'file_path' => $file->file_path ?? $file->path ?? null,
        ];
    }

    protected function mapJobApplicationCv($jobApplication): ?array
    {
        if (! $jobApplication) {
            return null;
        }

        return [
            'title' => ($jobApplication->full_name ?: 'Candidate') . ' CV',
            'file_path' => $jobApplication->cv_path ?? $jobApplication->cv_file ?? $jobApplication->file_path ?? null,
        ];
    }

    protected function resolveAbsolutePath(?string $storedPath): ?string
    {
        $storedPath = trim((string) $storedPath);

        if ($storedPath === '') {
            return null;
        }

        if (str_starts_with($storedPath, 'http://') || str_starts_with($storedPath, 'https://')) {
            $parsed = parse_url($storedPath, PHP_URL_PATH);
            if ($parsed) {
                $storedPath = ltrim((string) $parsed, '/');
            }
        }

        if (str_starts_with($storedPath, '/')) {
            return is_file($storedPath) ? $storedPath : null;
        }

        $normalized = ltrim($storedPath, '/');

        $candidates = [
            storage_path('app/public/' . $normalized),
            storage_path('app/' . $normalized),
            public_path('storage/' . $normalized),
            public_path($normalized),
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        if (Storage::disk('public')->exists($normalized)) {
            return Storage::disk('public')->path($normalized);
        }

        if (Storage::disk('local')->exists($normalized)) {
            return Storage::disk('local')->path($normalized);
        }

        return null;
    }

    protected function safeDownloadName(string $title, string $absolutePath): string
    {
        $ext = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $base = trim(preg_replace('/[^A-Za-z0-9\-_ ]+/', '', $title)) ?: 'file';

        if ($ext && ! str_ends_with(strtolower($base), '.' . strtolower($ext))) {
            return $base . '.' . $ext;
        }

        return $base;
    }
}
