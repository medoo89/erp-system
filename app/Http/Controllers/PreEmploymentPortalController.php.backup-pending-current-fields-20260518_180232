<?php

namespace App\Http\Controllers;

use App\Mail\PreEmploymentSubmissionReceivedMail;
use App\Mail\PreEmploymentSubmissionReviewMail;
use App\Models\PreEmployment;
use App\Models\PreEmploymentFile;
use App\Models\FinanceExpense;
use App\Services\AdminErpNotificationService;
use App\Models\PreEmploymentPortalValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PreEmploymentPortalController extends Controller
{
    public function show(string $token)
    {
        $preEmployment = PreEmployment::query()
            ->with([
                'job.project.client',
                'files' => fn ($query) => $query
                    ->where('is_active', true)
                    ->latest('is_current')
                    ->latest('id'),
                'portalFields' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('visible_to_candidate', true)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
                'portalValues',
            ])
            ->where('portal_token', $token)
            ->firstOrFail();

        $values = $preEmployment->portalValues->keyBy('portal_field_id');

        return view('pre-employment.portal', compact('preEmployment', 'values'));
    }

    public function submit(Request $request, string $token)
    {
        $preEmployment = PreEmployment::query()
            ->with([
                'job.project.client',
                'assignedHrUser',
                'files' => fn ($query) => $query
                    ->where('is_active', true)
                    ->latest('is_current')
                    ->latest('id'),
                'portalFields' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('visible_to_candidate', true)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->where('portal_token', $token)
            ->firstOrFail();

        $rules = [];

        $signedFilesSubmitted = false;
        $submittedFileRecords = [];

        foreach ($preEmployment->portalFields as $field) {
            $inputKey = 'field_' . $field->id;
            $rule = [];

            if ($field->field_type === 'file') {
                $category = $this->categoryForField($field);

                $existingFile = PreEmploymentFile::query()
                    ->where('pre_employment_id', $preEmployment->id)
                    ->where('category', $category)
                    ->where('is_active', true)
                    ->where('is_current', true)
                    ->first();

                /*
                 * Important:
                 * If the required file already exists, do NOT ask candidate to upload it again.
                 * If no existing file, keep it required when the admin marked it required.
                 */
                if ($field->is_required && ! $existingFile) {
                    $rule[] = 'required';
                } else {
                    $rule[] = 'nullable';
                }

                $rule[] = 'file';
                $rule[] = 'max:20480';
            } else {
                $rule[] = $field->is_required ? 'required' : 'nullable';

                if ($field->field_type === 'date') {
                    $rule[] = 'date';
                } elseif ($field->field_type === 'email') {
                    $rule[] = 'email:rfc';
                } elseif ($field->field_type === 'number') {
                    $rule[] = 'numeric';
                } else {
                    $rule[] = 'string';
                }
            }

            $rules[$inputKey] = implode('|', $rule);
        }

        $validated = $request->validate($rules);

        foreach ($preEmployment->portalFields as $field) {
            $inputKey = 'field_' . $field->id;

            if ($field->field_type === 'file') {
                if (! $request->hasFile($inputKey)) {
                    continue;
                }

                $file = $request->file($inputKey);

                $safeCandidate = Str::slug($preEmployment->candidate_name ?: 'candidate');
                $safeLabel = Str::slug($field->label ?: 'document');
                $extension = $file->getClientOriginalExtension();

                $fileName = $safeCandidate . '-' . $safeLabel . '-' . now()->format('YmdHis') . '.' . $extension;

                $value = $file->storeAs(
                    'pre-employment-portal/' . $preEmployment->id,
                    $fileName,
                    'public'
                );

                $category = $this->categoryForField($field);

                if (((string) ($field->request_type ?? '')) === 'download_sign_upload') {
                    $signedFilesSubmitted = true;
                }

                PreEmploymentFile::query()
                    ->where('pre_employment_id', $preEmployment->id)
                    ->where('category', $category)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);

                $maxVersion = PreEmploymentFile::query()
                    ->where('pre_employment_id', $preEmployment->id)
                    ->where('category', $category)
                    ->max('version_no');

                $createdPreEmploymentFile = PreEmploymentFile::create([
                    'pre_employment_id' => $preEmployment->id,
                    'title' => $field->label ?: ucfirst(str_replace('_', ' ', $category)),
                    'category' => $category,
                    'file_path' => $value,
                    'uploaded_by_type' => 'candidate',
                    'uploaded_by_user_id' => null,
                    'notes' => 'Uploaded by candidate from public pre-employment portal. Latest version is marked as current.',
                    'version_no' => ($maxVersion ?? 0) + 1,
                    'is_current' => true,
                    'is_active' => true,
                ]);

                PreEmploymentPortalValue::updateOrCreate(
                    [
                        'pre_employment_id' => $preEmployment->id,
                        'portal_field_id' => $field->id,
                    ],
                    [
                        'value' => $value,
                        'submitted_at' => now(),
                        'submitted_by_type' => 'candidate',
                        'submitted_by_user_id' => null,
                    ]
                );

                $submittedFileRecords[] = [
                    'id' => $createdPreEmploymentFile->id,
                    'title' => $createdPreEmploymentFile->title,
                    'category' => $createdPreEmploymentFile->category,
                    'path' => $createdPreEmploymentFile->file_path,
                ];

                continue;
            }

            PreEmploymentPortalValue::updateOrCreate(
                [
                    'pre_employment_id' => $preEmployment->id,
                    'portal_field_id' => $field->id,
                ],
                [
                    'value' => $validated[$inputKey] ?? null,
                    'submitted_at' => now(),
                    'submitted_by_type' => 'candidate',
                    'submitted_by_user_id' => null,
                ]
            );
        }

        $preEmployment->update([
            'portal_last_submitted_at' => now(),
            'status' => in_array($preEmployment->status, ['initiated', 'under_preparation', 'awaiting_candidate_upload'], true)
                ? 'documents_under_review'
                : $preEmployment->status,
        ]);

        if (filled($preEmployment->candidate_email)) {
            Mail::to($preEmployment->candidate_email)
                ->send(new PreEmploymentSubmissionReceivedMail($preEmployment));
        }

        if ($signedFilesSubmitted && $preEmployment->assignedHrUser) {
            FilamentNotification::make()
                ->title('Signed document submitted')
                ->body(($preEmployment->candidate_name ?: 'Candidate') . ' uploaded a signed Pre-Employment document.')
                ->success()
                ->sendToDatabase($preEmployment->assignedHrUser);
        }

        if (! empty($submittedFileRecords)) {
            try {
                $fileTitles = collect($submittedFileRecords)
                    ->pluck('title')
                    ->filter()
                    ->take(5)
                    ->implode(', ');

                app(AdminErpNotificationService::class)->notifyFileEvent(
                    title: 'Candidate uploaded requested file(s)',
                    body: trim(($preEmployment->candidate_name ?: 'Candidate') . ' uploaded ' . count($submittedFileRecords) . ' file(s)' . ($fileTitles ? ': ' . $fileTitles : '') . '.'),
                    url: url('/admin/pre-employments/' . $preEmployment->id),
                    department: 'hr',
                    module: 'pre_employment_files',
                    relatedType: PreEmployment::class,
                    relatedId: (int) $preEmployment->id,
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $internalEmails = array_filter([
            $preEmployment->assignedHrUser?->email,
            config('mail.from.address'),
        ]);

        foreach (array_unique($internalEmails) as $email) {
            if (filled($email)) {
                Mail::to($email)->send(new PreEmploymentSubmissionReviewMail($preEmployment));
            }
        }

        return redirect()
            ->route('pre-employment.portal.show', $token)
            ->with('success', 'Thank you. Your pre-employment documents have been submitted successfully.');
    }

    public function submitReimbursement(Request $request, string $token)
    {
        $preEmployment = PreEmployment::query()
            ->with([
                'job.project.client',
                'assignedHrUser',
                'currentFinanceProfile',
            ])
            ->where('portal_token', $token)
            ->firstOrFail();

        $validated = $request->validate([
            'expense_title' => ['required', 'string', 'max:255'],
            'expense_category' => ['required', 'string', 'max:80'],
            'expense_amount' => ['required', 'numeric', 'min:0.01'],
            'expense_currency' => ['required', 'string', 'max:10'],
            'expense_date' => ['required', 'date'],
            'expense_notes' => ['nullable', 'string', 'max:3000'],
            'receipt_file' => ['nullable', 'file', 'max:20480'],
        ]);

        $receiptPath = null;

        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');

            $safeCandidate = Str::slug($preEmployment->candidate_name ?: 'candidate');
            $safeCategory = Str::slug($validated['expense_category'] ?: 'expense');
            $extension = $file->getClientOriginalExtension() ?: 'file';

            $fileName = $safeCandidate . '-' . $safeCategory . '-receipt-' . now()->format('YmdHis') . '.' . $extension;

            $receiptPath = $file->storeAs(
                'pre-employment-reimbursements/' . $preEmployment->id,
                $fileName,
                'public'
            );
        }

        $category = $validated['expense_category'] ?: FinanceExpense::CATEGORY_OTHER;
        $currency = strtoupper((string) ($validated['expense_currency'] ?: 'EUR'));
        $amount = (float) ($validated['expense_amount'] ?? 0);

        $expense = FinanceExpense::query()->create([
            'job_application_id' => $preEmployment->job_application_id,
            'pre_employment_id' => $preEmployment->id,
            'employment_id' => null,
            'employment_rotation_id' => null,
            'job_id' => $preEmployment->job_id,
            'client_id' => $preEmployment->job?->project?->client?->id,
            'project_id' => $preEmployment->job?->project?->id,
            'candidate_finance_profile_id' => $preEmployment->currentFinanceProfile?->id,
            'created_by' => null,
            'expense_scope' => FinanceExpense::SCOPE_PRE_HIRE,
            'category' => $category,
            'expense_category' => $category,
            'title' => $validated['expense_title'],
            'description' => $validated['expense_notes'] ?? null,
            'amount' => $amount,
            'currency' => $currency,
            'expense_date' => $validated['expense_date'],
            'paid_by' => FinanceExpense::PAID_BY_CANDIDATE,
            'reimbursement_status' => FinanceExpense::REIMBURSEMENT_PENDING,
            'reimbursement_required' => true,
            'reimbursement_amount' => $amount,
            'reimbursement_currency' => $currency,
            'reimbursement_notes' => $validated['expense_notes'] ?? null,
            'status' => FinanceExpense::STATUS_DRAFT,
            'is_company_expense' => false,
            'is_manual_expense' => false,
            'candidate_submitted' => true,
            'candidate_submitted_at' => now(),
            'has_attachment' => filled($receiptPath),
            'attachment_path' => $receiptPath,
            'notes' => trim('Submitted by candidate from public pre-employment portal.' . "\n" . (string) ($validated['expense_notes'] ?? '')),
        ]);

        try {
            app(AdminErpNotificationService::class)->notifyReimbursementClaimSubmitted($expense, 'pre_employment_portal');
        } catch (\Throwable $e) {
            report($e);
        }

        if ($preEmployment->assignedHrUser) {
            FilamentNotification::make()
                ->title('Candidate reimbursement claim submitted')
                ->body(($preEmployment->candidate_name ?: 'Candidate') . ' submitted a pre-employment reimbursement claim.')
                ->warning()
                ->sendToDatabase($preEmployment->assignedHrUser);
        }

        return redirect()
            ->route('pre-employment.portal.show', $token)
            ->with('success', 'Your reimbursement claim has been submitted successfully and is pending review.');
    }


    protected function categoryForField($field): string
    {
        $raw = strtolower(trim(($field->document_category ?: '') . ' ' . ($field->field_key ?: '') . ' ' . ($field->label ?: '')));

        return match (true) {
            str_contains($raw, 'passport') => 'passport',
            str_contains($raw, 'photo') || str_contains($raw, 'picture') || str_contains($raw, 'personal') => 'personal_photo',
            str_contains($raw, 'medical') || str_contains($raw, 'health') => 'medical_certificate',
            str_contains($raw, 'contract') => 'contract',
            str_contains($raw, 'visa') => 'visa',
            str_contains($raw, 'certificate') || str_contains($raw, 'cert') => 'certificate',
            str_contains($raw, 'cv') || str_contains($raw, 'resume') => 'cv',
            default => Str::slug($field->document_category ?: $field->field_key ?: $field->label ?: 'candidate_document', '_'),
        };
    }
}
