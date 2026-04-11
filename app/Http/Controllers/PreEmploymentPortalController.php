<?php

namespace App\Http\Controllers;

use App\Mail\PreEmploymentSubmissionReceivedMail;
use App\Mail\PreEmploymentSubmissionReviewMail;
use App\Models\PreEmployment;
use App\Models\PreEmploymentFile;
use App\Models\PreEmploymentPortalValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PreEmploymentPortalController extends Controller
{
    public function show(string $token)
    {
        $preEmployment = PreEmployment::query()
            ->with([
                'job.project.client',
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
                'portalFields' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('visible_to_candidate', true)
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->where('portal_token', $token)
            ->firstOrFail();

        $rules = [];

        foreach ($preEmployment->portalFields as $field) {
            $rule = [];

            if ($field->is_required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if ($field->field_type === 'file') {
                $rule[] = 'file';
                $rule[] = 'max:20480';
            } elseif ($field->field_type === 'date') {
                $rule[] = 'date';
            } elseif ($field->field_type === 'email') {
                $rule[] = 'email:rfc';
            } elseif ($field->field_type === 'number') {
                $rule[] = 'numeric';
            } else {
                $rule[] = 'string';
            }

            $rules['field_' . $field->id] = implode('|', $rule);
        }

        $validated = $request->validate($rules);

        foreach ($preEmployment->portalFields as $field) {
            $inputKey = 'field_' . $field->id;
            $value = $validated[$inputKey] ?? null;

            if ($field->field_type === 'file' && $request->hasFile($inputKey)) {
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

                $category = $field->document_category ?: 'candidate_upload';

                $maxVersion = PreEmploymentFile::query()
                    ->where('pre_employment_id', $preEmployment->id)
                    ->where('category', $category)
                    ->max('version_no');

                PreEmploymentFile::create([
                    'pre_employment_id' => $preEmployment->id,
                    'title' => $field->label,
                    'category' => $category,
                    'file_path' => $value,
                    'uploaded_by_type' => 'candidate',
                    'uploaded_by_user_id' => null,
                    'notes' => 'Uploaded by candidate from public portal.',
                    'version_no' => ($maxVersion ?? 0) + 1,
                    'is_current' => true,
                    'is_active' => true,
                ]);
            }

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
            ->with('success', 'Thank you for submitting your required documents. We have received your submission and will review it.');
    }
}