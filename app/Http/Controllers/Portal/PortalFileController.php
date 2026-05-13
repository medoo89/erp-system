<?php

namespace App\Http\Controllers\Portal;

use App\Models\EmploymentFile;
use App\Models\JobApplication;
use App\Models\PreEmploymentFile;
use App\Models\PreEmploymentPortalField;
use App\Models\PreEmploymentPortalValue;
use App\Services\AdminErpNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $pendingFileRequests = $this->collectPendingFileRequests($employment, $preEmployment);

        return view('portal.files.index', array_merge($shared, [
            'files' => $files,
            'pendingFileRequests' => $pendingFileRequests,
        ]));
    }

    public function uploadRequestedFile(Request $request, int $field): RedirectResponse
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

        abort_if(! $preEmployment, 404, 'No linked pre-employment record was found.');

        $portalField = PreEmploymentPortalField::query()
            ->whereKey($field)
            ->where('pre_employment_id', $preEmployment->id)
            ->where('field_type', 'file')
            ->where('is_active', true)
            ->where('visible_to_candidate', true)
            ->firstOrFail();

        $validated = $request->validate([
            'requested_file' => ['required', 'file', 'max:20480'],
            'document_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after_or_equal:document_date'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $file = $request->file('requested_file');

        if (! $file || ! $file->isValid()) {
            return back()
                ->withErrors(['requested_file' => 'The uploaded file is invalid. Please choose the file again.'])
                ->withInput();
        }

        $category = $this->categoryForPortalField($portalField);
        $safeName = Str::slug($employment->employee_name ?: $preEmployment->candidate_name ?: 'employee');
        $safeLabel = Str::slug($portalField->label ?: 'requested-file');
        $extension = $file->getClientOriginalExtension() ?: 'file';

        $fileName = $safeName . '-' . $safeLabel . '-' . now()->format('YmdHis') . '.' . $extension;

        $path = $file->storeAs(
            'employee-portal-requested-files/' . $employment->id,
            $fileName,
            'public'
        );

        if (blank($path)) {
            return back()
                ->withErrors(['requested_file' => 'The file could not be stored. Please try again.'])
                ->withInput();
        }

        $createdEmploymentFile = null;

        DB::transaction(function () use (
            $validated,
            $employment,
            $preEmployment,
            $portalField,
            $category,
            $path,
            &$createdEmploymentFile
        ): void {
            PreEmploymentPortalValue::query()->updateOrCreate(
                [
                    'pre_employment_id' => $preEmployment->id,
                    'portal_field_id' => $portalField->id,
                ],
                [
                    'value' => $path,
                    'submitted_at' => now(),
                    'submitted_by_type' => 'employee_portal',
                    'submitted_by_user_id' => null,
                ]
            );

            PreEmploymentFile::query()
                ->where('pre_employment_id', $preEmployment->id)
                ->where('category', $category)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            PreEmploymentFile::query()->create([
                'pre_employment_id' => $preEmployment->id,
                'title' => $portalField->label ?: ucfirst(str_replace('_', ' ', $category)),
                'category' => $category,
                'document_date' => $validated['document_date'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'file_path' => $path,
                'uploaded_by_type' => 'employee_portal',
                'uploaded_by_user_id' => null,
                'notes' => trim('Uploaded by employee from Employee Portal request.' . "\n" . (string) ($validated['notes'] ?? '')),
                'is_current' => true,
                'is_active' => true,
            ]);

            EmploymentFile::query()
                ->where('employment_id', $employment->id)
                ->where('category', $category)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $createdEmploymentFile = EmploymentFile::query()->create([
                'employment_id' => $employment->id,
                'pre_employment_id' => $preEmployment->id,
                'title' => $portalField->label ?: ucfirst(str_replace('_', ' ', $category)),
                'category' => $category,
                'document_date' => $validated['document_date'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'file_path' => $path,
                'uploaded_by_type' => 'employee_portal',
                'uploaded_by_user_id' => null,
                'notes' => trim('Uploaded by employee from Employee Portal request.' . "\n" . (string) ($validated['notes'] ?? '')),
                'is_current' => true,
                'is_active' => true,
            ]);

            if (
                Schema::hasColumn('pre_employment_portal_fields', 'signature_status')
                && (
                    (bool) ($portalField->signed_file_required ?? false)
                    || (string) ($portalField->request_type ?? '') === 'download_sign_upload'
                )
            ) {
                $payload = ['signature_status' => 'signed'];

                if (Schema::hasColumn('pre_employment_portal_fields', 'signed_file_path')) {
                    $payload['signed_file_path'] = $path;
                }

                if (Schema::hasColumn('pre_employment_portal_fields', 'signed_original_name')) {
                    $payload['signed_original_name'] = $portalField->label;
                }

                if (Schema::hasColumn('pre_employment_portal_fields', 'signed_at')) {
                    $payload['signed_at'] = now();
                }

                $portalField->forceFill($payload)->save();
            }
        });

        try {
            app(AdminErpNotificationService::class)->notifyFileEvent(
                title: 'Requested employee portal file uploaded',
                body: ($employment->employee_name ?: $preEmployment->candidate_name ?: 'Employee') . ' uploaded requested file: ' . ($portalField->label ?: 'File'),
                url: url('/admin/employments/' . $employment->id),
                department: 'hr',
                module: 'employments',
                relatedType: $createdEmploymentFile ? get_class($createdEmploymentFile) : EmploymentFile::class,
                relatedId: $createdEmploymentFile?->id,
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('portal.files.index')
            ->with('success', 'Requested file uploaded successfully. It has been added to your files.');
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

    protected function collectPendingFileRequests($employment, $preEmployment): Collection
    {
        if (! $preEmployment) {
            return collect();
        }

        $submittedFieldIds = PreEmploymentPortalValue::query()
            ->where('pre_employment_id', $preEmployment->id)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->pluck('portal_field_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return PreEmploymentPortalField::query()
            ->where('pre_employment_id', $preEmployment->id)
            ->where('field_type', 'file')
            ->where('is_active', true)
            ->where('visible_to_candidate', true)
            ->when(! empty($submittedFieldIds), fn ($query) => $query->whereNotIn('id', $submittedFieldIds))
            ->orderByDesc('id')
            ->get();
    }

    protected function categoryForPortalField(PreEmploymentPortalField $field): string
    {
        $raw = strtolower(trim(($field->document_category ?: '') . ' ' . ($field->field_key ?: '') . ' ' . ($field->label ?: '')));

        return match (true) {
            str_contains($raw, 'passport') => 'passport',
            str_contains($raw, 'photo') || str_contains($raw, 'picture') || str_contains($raw, 'personal') => 'personal_photo',
            str_contains($raw, 'medical') || str_contains($raw, 'health') => 'medical',
            str_contains($raw, 'contract') => 'contract',
            str_contains($raw, 'visa') => 'visa',
            str_contains($raw, 'ticket') => 'ticket',
            str_contains($raw, 'travel') => 'travel_request',
            str_contains($raw, 'rotation') => 'rotation_document',
            str_contains($raw, 'caf') => 'caf',
            str_contains($raw, 'gl') || str_contains($raw, 'general letter') => 'gl',
            str_contains($raw, 'cv') || str_contains($raw, 'resume') => 'cv',
            str_contains($raw, 'certificate') || str_contains($raw, 'atex') => 'certificate',
            default => $field->document_category ?: 'candidate_upload',
        };
    }

    protected function portalVisibleFileCategories(): array
    {
        return [
            'cv',
            'candidate_upload',
            'passport',
            'visa',
            'medical',
            'medical_certificate',
            'personal_photo',
            'certificate',
            'caf',
            'gl',
            'contract',
            'rotation_document',
            'travel_request',
            'ticket',
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
            || str_contains($combined, 'ticket')
            || str_contains($combined, 'travel')
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

            /*
             * Employment file portal visibility is controlled only from:
             * Employment Profile > Upload File > Show in Employee Portal.
             */
            if (Schema::hasColumn('employment_files', 'is_visible_to_employee_portal')
                && ! (bool) ($file->is_visible_to_employee_portal ?? false)
            ) {
                continue;
            }
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

    protected function mapEmploymentFile($file): ?array
    {
        if (! $file) {
            return null;
        }

        if (Schema::hasColumn('employment_files', 'is_visible_to_employee_portal')
            && ! (bool) ($file->is_visible_to_employee_portal ?? false)
        ) {
            return null;
        }

        if (! $this->isPortalVisibleFile(
            $file->category ?? null,
            $file->title ?? $file->file_name ?? null,
            (bool) ($file->is_current ?? true)
        )) {
            return null;
        }

        return [
            'title' => $file->title ?? $file->file_name ?? ('Employment File #' . $file->id),
            'file_path' => $file->file_path ?? $file->path ?? null,
        ];
    }

    protected function mapEmploymentDocument($doc): ?array
    {
        if (! $doc) {
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

        return [
            'title' => $file->title ?? $file->file_name ?? ('Pre-Employment File #' . $file->id),
            'file_path' => $file->file_path ?? $file->path ?? null,
        ];
    }

    protected function mapJobApplicationCv($application): ?array
    {
        if (! $application) {
            return null;
        }

        return [
            'title' => ($application->full_name ?: 'Candidate') . ' CV',
            'file_path' => $application->cv_path ?? $application->cv_file ?? $application->file_path ?? null,
        ];
    }

    protected function resolveJobApplication($preEmployment)
    {
        if (! $preEmployment) {
            return null;
        }

        if ($preEmployment->relationLoaded('jobApplication') && $preEmployment->jobApplication) {
            return $preEmployment->jobApplication;
        }

        if (filled($preEmployment->job_application_id)) {
            return JobApplication::query()->find($preEmployment->job_application_id);
        }

        return null;
    }

    protected function resolveAbsolutePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = ltrim((string) $path, '/');

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        if (is_file(storage_path('app/public/' . $path))) {
            return storage_path('app/public/' . $path);
        }

        if (is_file(public_path($path))) {
            return public_path($path);
        }

        return null;
    }

    protected function safeDownloadName(string $name, string $absolutePath): string
    {
        $extension = pathinfo($absolutePath, PATHINFO_EXTENSION);
        $base = Str::slug(pathinfo($name, PATHINFO_FILENAME) ?: 'file');

        return $base . ($extension ? '.' . $extension : '');
    }
}
