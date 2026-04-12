<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobApplicationDocumentController;
use App\Http\Controllers\PublicRecruitmentCalendarController;
use App\Http\Controllers\PreEmploymentPortalController;
use App\Models\Employment;
use App\Models\Job;
use App\Models\Job as JobModel;
use App\Models\PreEmployment;
use App\Services\CodeGeneratorService;
use App\Http\Controllers\EmploymentPrintController;
use App\Http\Controllers\JobApplicationDocumentController;

Route::get('/admin/job-applications/{jobApplication}/open-cv', [JobApplicationDocumentController::class, 'openCv'])
    ->name('job-applications.open-cv');
    
Route::get('/employment/{employment}/print-profile', [EmploymentPrintController::class, 'profile'])
    ->name('employment.print.profile');

Route::get('/employment/{employment}/print-rotation-history', [EmploymentPrintController::class, 'rotationHistory'])
    ->name('employment.print.rotation-history');
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

            // 1) من pre-employment لو موجود
            if ($employment->pre_employment_id) {
                $preEmployment = PreEmployment::find($employment->pre_employment_id);

                if ($preEmployment?->employee_code) {
                    $employment->employee_code = $preEmployment->employee_code;
                    $employment->save();
                    $updated++;
                    return;
                }
            }

            // 2) من job -> project -> client
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

            // 3) fallback من النصوص المحفوظة في employment نفسه
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
                    // آخر fallback: توليد مباشر من اسم المشروع المكتوب في السجل
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

Route::get('/admin/job-applications/{jobApplication}/open-cv', [JobApplicationDocumentController::class, 'openCv'])
    ->name('job-applications.open-cv');

Route::get('/', function () {
    return redirect('/jobs');
})->name('home');

Route::get('/recruitment-calendar/public', [PublicRecruitmentCalendarController::class, 'index'])
    ->name('recruitment-calendar.public');

Route::get('/pre-employment/portal/{token}', [PreEmploymentPortalController::class, 'show'])
    ->name('pre-employment.portal.show');

Route::post('/pre-employment/portal/{token}', [PreEmploymentPortalController::class, 'submit'])
    ->name('pre-employment.portal.submit');

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