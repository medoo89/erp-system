<?php

namespace App\Http\Controllers;

use App\Mail\JobApplicationSubmittedMail;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class JobApplicationController extends Controller
{
    public function create(Job $job)
    {
        if (! $job->isPubliclyVisible()) {
            abort(404);
        }

        if (! $job->canAcceptApplications()) {
            return redirect()
                ->route('jobs.show', $job)
                ->with('job_closed', 'This job is closed and no longer accepting applications.');
        }

        $template = $job->template;

        if (! $template) {
            $fields = collect();

            return view('jobs.apply', compact('job', 'fields'));
        }

        $fields = $template->fields()
            ->where('job_application_fields.is_active', true)
            ->orderByRaw("
                CASE 
                    WHEN job_application_fields.field_group = 'basic' THEN 1
                    WHEN job_application_fields.field_group = 'additional' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('job_application_fields.sort_order')
            ->get();

        return view('jobs.apply', compact('job', 'fields'));
    }

    public function store(Request $request, Job $job)
    {
        if (! $job->isPubliclyVisible()) {
            abort(404);
        }

        if (! $job->canAcceptApplications()) {
            return redirect()
                ->route('jobs.show', $job)
                ->with('job_closed', 'This job is closed and no longer accepting applications.');
        }

        if ($job->closing_date && now()->gt($job->closing_date)) {
            return back()
                ->withErrors(['closed' => 'This job is closed and no longer accepting applications.'])
                ->withInput();
        }

        $template = $job->template;

        if (! $template) {
            return back()
                ->withErrors(['template' => 'No application template is assigned to this job.'])
                ->withInput();
        }

        $fields = $template->fields()
            ->where('job_application_fields.is_active', true)
            ->orderByRaw("
                CASE 
                    WHEN job_application_fields.field_group = 'basic' THEN 1
                    WHEN job_application_fields.field_group = 'additional' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('job_application_fields.sort_order')
            ->get();

        $rules = [];

        foreach ($fields as $field) {
            $rule = [];

            if ($field->is_required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if ($field->field_type === 'file') {
                $rule[] = 'file';
            }

            if ($field->field_type === 'number') {
                $rule[] = 'numeric';
            }

            if ($field->field_type === 'email' || $field->field_key === 'email') {
                $rule[] = 'email:rfc,dns';
            }

            $rules[$field->field_key] = implode('|', $rule);
        }

        $rules['phone_country_code'] = 'nullable|string|max:20';
        $rules['phone_number'] = 'nullable|string|max:50';
        $rules['whatsapp_country_code'] = 'nullable|string|max:20';
        $rules['whatsapp_number'] = 'nullable|string|max:50';

        $validated = $request->validate($rules);

        $fullName = $request->input('full_name');
        $email = $request->input('email');

        if (! $fullName || ! $email) {
            return back()
                ->withErrors(['basic' => 'Full name and email are required.'])
                ->withInput();
        }

        $phoneCountry = $request->input('phone_country_code');
        $phoneNumber = $request->input('phone_number');
        $phone = null;

        if ($phoneCountry && $phoneNumber) {
            $phone = $phoneCountry . $phoneNumber;
        }

        $whatsappCountry = $request->input('whatsapp_country_code');
        $whatsappNumber = $request->input('whatsapp_number');
        $whatsapp = null;

        if ($whatsappCountry && $whatsappNumber) {
            $whatsapp = $whatsappCountry . $whatsappNumber;
        }

        $alreadyApplied = JobApplication::query()
            ->where('job_id', $job->id)
            ->where(function ($query) use ($email, $phone) {
                $query->where('email', $email);

                if ($phone) {
                    $query->orWhere('phone', $phone);
                }
            })
            ->exists();

        if ($alreadyApplied) {
            return back()
                ->withErrors(['duplicate' => 'You have already applied for this job.'])
                ->withInput();
        }

        $cvPath = null;

        if ($request->hasFile('cv_file')) {
            $file = $request->file('cv_file');
            $extension = $file->getClientOriginalExtension();
            $safeName = Str::slug($fullName) . '-cv.' . $extension;
            $cvPath = $file->storeAs('cvs', $safeName, 'public');
        }

        $application = JobApplication::create([
            'job_id' => $job->id,
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'phone_country_code' => $phoneCountry,
            'phone_number' => $phoneNumber,
            'whatsapp_country_code' => $whatsappCountry,
            'whatsapp_number' => $whatsappNumber,
            'cv_path' => $cvPath,
            'status' => 'new',
        ]);

        foreach ($fields as $field) {
            $value = $request->input($field->field_key);

            if ($field->field_type === 'checkbox') {
                $value = $request->has($field->field_key)
                    ? implode(', ', (array) $request->input($field->field_key))
                    : null;
            }

            if ($field->field_type === 'file') {
                if ($field->field_key === 'cv_file' && $cvPath) {
                    $value = $cvPath;
                } elseif ($request->hasFile($field->field_key)) {
                    $dynamicFile = $request->file($field->field_key);
                    $dynamicExtension = $dynamicFile->getClientOriginalExtension();
                    $dynamicName = Str::slug($fullName) . '-' . Str::slug($field->field_key) . '.' . $dynamicExtension;

                    $value = $dynamicFile->storeAs('applications', $dynamicName, 'public');
                }
            }

            if ($field->field_key === 'phone_number') {
                $value = $phone;
            }

            if ($field->field_key === 'whatsapp_number') {
                $value = $whatsapp;
            }

            JobApplicationValue::create([
                'job_application_id' => $application->id,
                'field_id' => $field->id,
                'value' => $value,
            ]);
        }

        $application->load(['job', 'values.field']);

        $applicationAnswers = $application->values
            ->filter(function ($value) {
                return $value->field
                    && $value->field->field_type !== 'file'
                    && filled($value->value);
            })
            ->map(function ($value) {
                return [
                    'label' => $value->field->label ?? '-',
                    'value' => $value->value,
                ];
            })
            ->values()
            ->toArray();

        if (filled($application->email)) {
            try {
                Mail::to($application->email)->queue(
                    new JobApplicationSubmittedMail($application, $applicationAnswers)
                );
            } catch (\Throwable $e) {
                \Log::error('Application submitted email queue failed', [
                    'job_application_id' => $application->id,
                    'email' => $application->email,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('jobs.apply.success', $job)
            ->with('application_id', $application->id);
    }

    public function success(Job $job)
    {
        return view('jobs.apply-success', compact('job'));
    }
}