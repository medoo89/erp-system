<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobApplicationDocumentController;
use App\Http\Controllers\PublicRecruitmentCalendarController;
use App\Http\Controllers\PreEmploymentPortalController;
use App\Models\Employment;
use App\Models\Job;
use App\Models\Job as JobModel;
use App\Models\JobApplication;
use App\Models\PreEmployment;
use App\Services\CodeGeneratorService;
use App\Http\Controllers\EmploymentPrintController;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::get('/admin/job-applications/{jobApplication}/open-cv', [JobApplicationDocumentController::class, 'openCv'])
    ->name('job-applications.open-cv');

Route::get('/employment/{employment}/print-profile', [EmploymentPrintController::class, 'profile'])
    ->name('employment.print.profile');

Route::get('/employment/{employment}/print-rotation-history', [EmploymentPrintController::class, 'rotationHistory'])
    ->name('employment.print.rotation-history');

Route::get('/admin/job-applications-export-excel', function (): StreamedResponse {
    $applications = JobApplication::query()
        ->with(['job.project.client', 'values.field'])
        ->where('is_archived', false)
        ->orderByDesc('created_at')
        ->get();

    $excludedLabels = [
        'full name',
        'name',
        'email',
        'email address',
        'phone',
        'phone number',
        'whatsapp',
        'whatsapp number',
        'position',
        'position applying for',
        'job title',
        'nationality',
        'client',
        'project',
        'years of experience',
        'experience',
        'status',
        'applied at',
        'cv',
        'cv file',
    ];

    $dynamicFieldLabels = $applications
        ->flatMap(function (JobApplication $application) {
            return $application->values
                ->filter(fn ($value) => filled($value->field?->label))
                ->map(fn ($value) => trim((string) $value->field->label));
        })
        ->filter(fn ($label) => ! in_array(strtolower($label), $excludedLabels, true))
        ->unique()
        ->values()
        ->toArray();

    $resolveNationality = function (JobApplication $application): string {
        if (filled($application->nationality ?? null)) {
            return (string) $application->nationality;
        }

        $nationalityValue = $application->values->first(function ($value) {
            $fieldKey = strtolower((string) ($value->field->field_key ?? ''));
            $fieldLabel = strtolower((string) ($value->field->label ?? ''));

            return str_contains($fieldKey, 'nationality')
                || str_contains($fieldLabel, 'nationality')
                || str_contains($fieldLabel, 'الجنسية');
        });

        return filled($nationalityValue?->value) ? (string) $nationalityValue->value : '-';
    };

    $resolveYearsOfExperience = function (JobApplication $application): string {
        if (filled($application->years_of_experience ?? null)) {
            return (string) $application->years_of_experience;
        }

        $experienceValue = $application->values->first(function ($value) {
            $fieldKey = strtolower((string) ($value->field->field_key ?? ''));
            $fieldLabel = strtolower((string) ($value->field->label ?? ''));

            return str_contains($fieldKey, 'experience')
                || str_contains($fieldKey, 'year')
                || str_contains($fieldLabel, 'experience')
                || str_contains($fieldLabel, 'year')
                || str_contains($fieldLabel, 'years of experience')
                || str_contains($fieldLabel, 'عدد سنوات الخبرة')
                || str_contains($fieldLabel, 'سنوات الخبرة');
        });

        return filled($experienceValue?->value) ? (string) $experienceValue->value : '-';
    };

    return response()->streamDownload(function () use ($applications, $dynamicFieldLabels, $resolveNationality, $resolveYearsOfExperience) {
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
        echo '<body><table border="1">';

        echo '<tr style="font-weight:bold;background:#dbeafe;">';
        echo '<th>Full Name</th>';
        echo '<th>Email</th>';
        echo '<th>Phone Number</th>';
        echo '<th>WhatsApp Number</th>';
        echo '<th>Position</th>';
        echo '<th>Nationality</th>';
        echo '<th>Project</th>';
        echo '<th>Client</th>';
        echo '<th>Status</th>';
        echo '<th>Years of Experience</th>';
        echo '<th>Applied At</th>';
        echo '<th>Notes</th>';
        echo '<th>Decline Reason</th>';
        echo '<th>Decline Notes</th>';
        echo '<th>CV Link</th>';

        foreach ($dynamicFieldLabels as $label) {
            echo '<th>' . e($label) . '</th>';
        }

        echo '</tr>';

        foreach ($applications as $application) {
            $cvValue = $application->values->first(function ($value) {
                return ($value->field->field_key ?? null) === 'cv_file' && filled($value->value);
            });

            $cvLink = filled($application->cv_path)
                ? route('job-applications.open-cv', $application)
                : (filled($cvValue?->value) ? route('job-applications.open-cv', $application) : '');

            echo '<tr>';
            echo '<td>' . e($application->full_name) . '</td>';
            echo '<td>' . e($application->email) . '</td>';
            echo '<td>' . e($application->phone) . '</td>';
            echo '<td>' . e($application->whatsapp_number) . '</td>';
            echo '<td>' . e(optional($application->job)->title) . '</td>';
            echo '<td>' . e($resolveNationality($application)) . '</td>';
            echo '<td>' . e(optional($application->job?->project)->name) . '</td>';
            echo '<td>' . e(optional($application->job?->project?->client)->name) . '</td>';
            echo '<td>' . e((string) $application->status) . '</td>';
            echo '<td>' . e($resolveYearsOfExperience($application)) . '</td>';
            echo '<td>' . e(optional($application->created_at)?->format('Y-m-d H:i:s')) . '</td>';
            echo '<td>' . e((string) ($application->notes ?? '')) . '</td>';
            echo '<td>' . e((string) ($application->decline_reason ?? '')) . '</td>';
            echo '<td>' . e((string) ($application->decline_notes ?? '')) . '</td>';

            if (filled($cvLink)) {
                echo '<td><a href="' . e($cvLink) . '">Open CV</a></td>';
            } else {
                echo '<td></td>';
            }

            foreach ($dynamicFieldLabels as $label) {
                $fieldValue = $application->values->first(function ($value) use ($label) {
                    return trim((string) ($value->field->label ?? '')) === $label;
                });

                $fieldType = $fieldValue->field->field_type ?? null;
                $fieldKey = $fieldValue->field->field_key ?? null;
                $rawValue = $fieldValue->value ?? '';

                if ($fieldType === 'file' && filled($rawValue)) {
                    $documentUrl = $fieldKey === 'cv_file'
                        ? route('job-applications.open-cv', $application)
                        : asset('storage/' . ltrim($rawValue, '/'));

                    $documentText = $fieldKey === 'cv_file' ? 'Open CV' : 'Open File';

                    echo '<td><a href="' . e($documentUrl) . '">' . e($documentText) . '</a></td>';
                } else {
                    echo '<td>' . e(is_array($rawValue) ? implode(', ', $rawValue) : (string) $rawValue) . '</td>';
                }
            }

            echo '</tr>';
        }

        echo '</table></body></html>';
    }, 'job_applications_' . now()->format('Y_m_d_H_i_s') . '.xls', [
        'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
    ]);
})->name('job-applications.export-excel');

Route::get('/admin/job-applications-export-pdf', function () {
    $applications = JobApplication::query()
        ->with(['job.project.client', 'values.field'])
        ->where('is_archived', false)
        ->orderByDesc('created_at')
        ->get();

    $excludedLabels = [
        'full name',
        'name',
        'email',
        'email address',
        'phone',
        'phone number',
        'whatsapp',
        'whatsapp number',
        'position',
        'position applying for',
        'job title',
        'nationality',
        'client',
        'project',
        'years of experience',
        'experience',
        'status',
        'applied at',
        'cv',
        'cv file',
    ];

    $dynamicFieldLabels = $applications
        ->flatMap(function (JobApplication $application) {
            return $application->values
                ->filter(fn ($value) => filled($value->field?->label))
                ->map(fn ($value) => trim((string) $value->field->label));
        })
        ->filter(fn ($label) => ! in_array(strtolower($label), $excludedLabels, true))
        ->unique()
        ->values()
        ->toArray();

    $resolveNationality = function (JobApplication $application): string {
        if (filled($application->nationality ?? null)) {
            return (string) $application->nationality;
        }

        $nationalityValue = $application->values->first(function ($value) {
            $fieldKey = strtolower((string) ($value->field->field_key ?? ''));
            $fieldLabel = strtolower((string) ($value->field->label ?? ''));

            return str_contains($fieldKey, 'nationality')
                || str_contains($fieldLabel, 'nationality')
                || str_contains($fieldLabel, 'الجنسية');
        });

        return filled($nationalityValue?->value) ? (string) $nationalityValue->value : '-';
    };

    $resolveYearsOfExperience = function (JobApplication $application): string {
        if (filled($application->years_of_experience ?? null)) {
            return (string) $application->years_of_experience;
        }

        $experienceValue = $application->values->first(function ($value) {
            $fieldKey = strtolower((string) ($value->field->field_key ?? ''));
            $fieldLabel = strtolower((string) ($value->field->label ?? ''));

            return str_contains($fieldKey, 'experience')
                || str_contains($fieldKey, 'year')
                || str_contains($fieldLabel, 'experience')
                || str_contains($fieldLabel, 'year')
                || str_contains($fieldLabel, 'years of experience')
                || str_contains($fieldLabel, 'عدد سنوات الخبرة')
                || str_contains($fieldLabel, 'سنوات الخبرة');
        });

        return filled($experienceValue?->value) ? (string) $experienceValue->value : '-';
    };

    return view('admin.exports.job-applications-pdf', [
        'applications' => $applications,
        'dynamicFieldLabels' => $dynamicFieldLabels,
        'resolveNationality' => $resolveNationality,
        'resolveYearsOfExperience' => $resolveYearsOfExperience,
    ]);
})->name('job-applications.export-pdf');

Route::get('/fix-employment-codes', function () {
    abort_unless(request('key') === 'SF_FIX_EMP_CODES_2026', 403);

    $updated = 0;

    Employment::query()
        ->get()
        ->each(function (Employment $employment) use (&$updated) {
            $currentCode = trim((string) $employment->employee_code);
            $needsFix = blank($currentCode) || $currentCode === '-';

            if (! $needsFix) {
                return;
            }

            $code = null;
            $clientCode = null;
            $projectCode = null;

            if ($employment->pre_employment_id) {
                $preEmployment = PreEmployment::find($employment->pre_employment_id);

                if ($preEmployment?->employee_code) {
                    $employment->employee_code = $preEmployment->employee_code;
                    $employment->save();
                    $updated++;
                    return;
                }
            }

            if ($employment->job_id) {
                $job = Job::with('project.client')->find($employment->job_id);

                if ($job) {
                    $clientCode = $job->project?->client?->code;

                    $projectCode = $job->project?->project_code
                        ?: $job->project?->code;

                    if (blank($projectCode) && filled($job->project?->name)) {
                        $projectCode = app(CodeGeneratorService::class)
                            ->generateProjectCode(
                                $job->project->name,
                                $job->project->client_id,
                                $job->project->id
                            );

                        $job->project->project_code = $projectCode;
                        $job->project->save();
                    }

                    if (blank($employment->position_title)) {
                        $employment->position_title = $job->title;
                    }

                    if (blank($employment->project_name)) {
                        $employment->project_name = $job->project?->name;
                    }

                    if (blank($employment->client_name)) {
                        $employment->client_name = $job->project?->client?->name;
                    }
                }
            }

            if (blank($clientCode) && filled($employment->client_name)) {
                $client = \App\Models\Client::query()
                    ->where('name', $employment->client_name)
                    ->first();

                if ($client) {
                    $clientCode = $client->code;
                }
            }

            if (blank($projectCode) && filled($employment->project_name)) {
                $project = \App\Models\Project::query()
                    ->where('name', $employment->project_name)
                    ->first();

                if ($project) {
                    $projectCode = $project->project_code ?: $project->code;

                    if (blank($projectCode)) {
                        $projectCode = app(CodeGeneratorService::class)
                            ->generateProjectCode(
                                $project->name,
                                $project->client_id,
                                $project->id
                            );

                        $project->project_code = $projectCode;
                        $project->save();
                    }
                } else {
                    $projectCode = app(CodeGeneratorService::class)
                        ->generateProjectCode($employment->project_name);
                }
            }

            if ($clientCode && $projectCode) {
                $code = app(CodeGeneratorService::class)
                    ->generateEmployeeCode($clientCode, $projectCode);
            }

            if ($code) {
                $employment->employee_code = $code;
                $employment->save();
                $updated++;
            }
        });

    return "Employment codes fixed. Updated records: {$updated}";
})->name('fix-employment-codes');

Route::get('/', function () {
    return redirect('/jobs');
})->name('home');

Route::get('/recruitment-calendar/public', [PublicRecruitmentCalendarController::class, 'index'])
    ->name('recruitment-calendar.public');

Route::get('/pre-employment/portal/{token}', [PreEmploymentPortalController::class, 'show'])
    ->name('pre-employment.portal.show');

Route::post('/pre-employment/portal/{token}', [PreEmploymentPortalController::class, 'submit'])
    ->name('pre-employment.portal.submit');

Route::get('/candidate-request/{token}', [\App\Http\Controllers\CandidateRequestPortalController::class, 'show'])
    ->name('candidate-request.show');

Route::post('/candidate-request/{token}', [\App\Http\Controllers\CandidateRequestPortalController::class, 'submit'])
    ->name('candidate-request.submit');

Route::get('/jobs', function () {
    $jobs = JobModel::query()
        ->where('is_active', true)
        ->orderByDesc('created_at')
        ->get();

    return view('jobs.index', compact('jobs'));
})->name('jobs.index');

Route::get('/jobs/{job}', function (JobModel $job) {
    return view('jobs.show', compact('job'));
})->name('jobs.show');

Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])
    ->name('jobs.apply');

Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])
    ->name('jobs.apply.store');

Route::get('/jobs/{job}/apply/success', [JobApplicationController::class, 'success'])
    ->name('jobs.apply.success');