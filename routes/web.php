<?php

// Sada Fezzan ERP: old archive jobs URL fallback.
// Keeps /admin/archived-jobs from showing 404 and redirects to the real Archived Job Openings page.
Route::redirect('/admin/archived-jobs', '/admin/archived-job-openings', 301);


use App\Http\Controllers\JobApplicationController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PreEmploymentPortalController;
use App\Http\Controllers\CandidateRequestPortalController;
use App\Http\Controllers\SalarySlipEmployeeConfirmationController;
use App\Http\Controllers\SalarySlipPrintController;
use App\Http\Controllers\ClientInvoicePrintController;
use App\Http\Controllers\GlobalFinanceTotalsPrintController;
use App\Http\Controllers\Admin\EmploymentPortalPreviewController;
use App\Http\Controllers\Portal\PortalFileController;
use App\Http\Controllers\Portal\PortalReimbursementController;

// SADA_CLIENT_REVIEW_ROUTE_START
\Illuminate\Support\Facades\Route::middleware(['web', 'auth'])
    ->get('/admin/clients/{client}/view', [\App\Http\Controllers\Admin\ClientReviewController::class, 'show'])
    ->name('admin.clients.review');
// SADA_CLIENT_REVIEW_ROUTE_END


use Illuminate\Http\Request;
use App\Models\Employment;
use App\Models\Job;
use App\Models\Job as JobModel;
use App\Models\JobApplication;
use App\Models\PreEmployment;
use App\Services\CodeGeneratorService;
use Symfony\Component\HttpFoundation\StreamedResponse;

// SADA_SALARY_SLIP_ATTENDANCE_DIRECT_ROUTES_START
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/salary-slips/{salarySlip}/attendance-direct', [\App\Http\Controllers\Admin\SalarySlipAttendanceDirectController::class, 'edit'])
        ->name('admin.salary-slips.attendance.direct.edit');

    Route::post('/admin/salary-slips/{salarySlip}/attendance-direct', [\App\Http\Controllers\Admin\SalarySlipAttendanceDirectController::class, 'update'])
        ->name('admin.salary-slips.attendance.direct.update');
});
// SADA_SALARY_SLIP_ATTENDANCE_DIRECT_ROUTES_END


// SADA_PORTAL_SALARY_CONFIRM_BRIDGE_START
// Allows salary receipt confirmation URL to be reached before portal.auth middleware blocks it.
// Controller still validates portal/admin access.
// SADA_PORTAL_SALARY_CONFIRM_BRIDGE_END


Route::prefix('portal')->name('portal.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Portal\Auth\PortalLoginController::class, 'show'])->name('login');

    Route::get('/password/request', [\App\Http\Controllers\Portal\Auth\PortalPasswordSetupController::class, 'requestForm'])->name('password.request');
    Route::post('/password/request', [\App\Http\Controllers\Portal\Auth\PortalPasswordSetupController::class, 'sendRequest'])->name('password.request.send');


    Route::get('/password/setup/{token}', [\App\Http\Controllers\Portal\Auth\PortalPasswordSetupController::class, 'show'])->name('password.setup');
    Route::post('/password/setup/{token}', [\App\Http\Controllers\Portal\Auth\PortalPasswordSetupController::class, 'store'])->name('password.setup.store');

        Route::post('/login', [\App\Http\Controllers\Portal\Auth\PortalLoginController::class, 'store'])->name('login.store');
    });

    Route::middleware('portal.auth')->group(function () {
    Route::get('/reimbursements', [PortalReimbursementController::class, 'index'])
        ->name('reimbursements.index');

    Route::post('/reimbursements', [PortalReimbursementController::class, 'store'])
        ->name('reimbursements.store');

    Route::get('/reimbursements', [\App\Http\Controllers\Portal\PortalReimbursementController::class, 'index'])
        ->name('reimbursements.index');
    Route::post('/reimbursements', [\App\Http\Controllers\Portal\PortalReimbursementController::class, 'store'])
        ->name('reimbursements.store');
        Route::get('/', \App\Http\Controllers\Portal\PortalDashboardController::class)->name('dashboard');
        Route::get('/salary-slips', [\App\Http\Controllers\Portal\PortalSalarySlipController::class, 'index'])->name('salary-slips.index');
        Route::get('/salary-slips/{salarySlip}', [\App\Http\Controllers\Portal\PortalSalarySlipController::class, 'show'])->name('salary-slips.show');
        Route::match(['GET', 'POST'], '/salary-slips/{salarySlip}/confirm-received', [\App\Http\Controllers\Portal\PortalSalarySlipController::class, 'confirmReceived'])->name('salary-slips.confirm-received');
        Route::match(['GET', 'POST'], '/salary-slips/{salarySlip}/not-received', [\App\Http\Controllers\Portal\PortalSalarySlipController::class, 'notReceived'])->name('salary-slips.not-received');
Route::get('/salary-slips/{salarySlip}/print', [\App\Http\Controllers\Portal\PortalSalarySlipPrintController::class, 'show'])->name('salary-slips.print');
        Route::get('/salary-slip-attachments/{attachment}/open', [\App\Http\Controllers\Portal\PortalSalarySlipAttachmentController::class, 'open'])->name('salary-slip-attachments.open');
        Route::get('/salary-slip-attachments/{attachment}/download', [\App\Http\Controllers\Portal\PortalSalarySlipAttachmentController::class, 'download'])->name('salary-slip-attachments.download');
        Route::get('/notifications', [\App\Http\Controllers\Portal\PortalNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{notification}/open', [\App\Http\Controllers\Portal\PortalNotificationController::class, 'open'])->name('notifications.open');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Portal\PortalNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
        Route::post('/notifications/clear-all', [\App\Http\Controllers\Portal\PortalNotificationController::class, 'clearAll'])->name('notifications.clear-all');
        Route::get('/timeline', [\App\Http\Controllers\Portal\PortalTimelineController::class, 'index'])->name('timeline.index');
        Route::get('/travel-tickets', [\App\Http\Controllers\Portal\PortalTravelTicketController::class, 'index'])->name('travel-tickets.index');
        Route::get('/travel-tickets/{rotation}/{type}/open', [\App\Http\Controllers\Portal\PortalTravelTicketController::class, 'open'])->name('travel-tickets.open');
        Route::get('/travel-tickets/{rotation}/{type}/download', [\App\Http\Controllers\Portal\PortalTravelTicketController::class, 'download'])->name('travel-tickets.download');
        Route::get('/files', [\App\Http\Controllers\Portal\PortalFileController::class, 'index'])->name('files.index');
        Route::get('/files/{type}/{id}/open', [\App\Http\Controllers\Portal\PortalFileController::class, 'open'])->name('files.open');
        Route::get('/files/{type}/{id}/download', [\App\Http\Controllers\Portal\PortalFileController::class, 'download'])->name('files.download');
        Route::post('/files/requested/{field}/upload', [\App\Http\Controllers\Portal\PortalFileController::class, 'uploadRequestedFile'])->name('files.upload-requested');
        Route::post('/logout', \App\Http\Controllers\Portal\Auth\PortalLogoutController::class)->name('logout');
    });
});

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

Route::get('/recruitment-calendar/public', [\App\Http\Controllers\PublicRecruitmentCalendarController::class, 'index'])
    ->name('recruitment-calendar.public');

Route::get('/pre-employment/portal/{token}', [PreEmploymentPortalController::class, 'show'])
    ->name('pre-employment.portal.show');

Route::post('/pre-employment/portal/{token}', [PreEmploymentPortalController::class, 'submit'])
    ->name('pre-employment.portal.submit');

Route::post('/pre-employment/portal/{token}/reimbursement', [PreEmploymentPortalController::class, 'submitReimbursement'])
    ->name('pre-employment.portal.reimbursement');

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

Route::get('/jobs/{job}/apply', [\App\Http\Controllers\JobApplicationController::class, 'create'])
    ->name('jobs.apply');

Route::post('/jobs/{job}/apply', [\App\Http\Controllers\JobApplicationController::class, 'store'])
    ->name('jobs.apply.store');

Route::get('/jobs/{job}/apply/success', [\App\Http\Controllers\JobApplicationController::class, 'success'])
    ->name('jobs.apply.success');

Route::get('/salary-slips/{salarySlip}/confirm-receipt', [SalarySlipEmployeeConfirmationController::class, 'confirm'])
    ->middleware('signed')
    ->name('salary-slips.confirm-receipt');

Route::get('/salary-slips/{salarySlip}/report-not-received', [SalarySlipEmployeeConfirmationController::class, 'reportNotReceived'])
    ->middleware('signed')
    ->name('salary-slips.report-not-received');

Route::get('/salary-slips/{salarySlip}/print', [SalarySlipPrintController::class, 'print'])
    ->name('salary-slips.print');

Route::get('/client-invoices/{clientInvoice}/print', [ClientInvoicePrintController::class, 'print'])
    ->name('client-invoices.print');

Route::get('/finance/global-totals/print', [GlobalFinanceTotalsPrintController::class, 'print'])
    ->name('finance.totals.print');

Route::get('/website', function () {
    return view('website.home');
})->name('website.home');

// Public Sada Fezzan Website Routes
Route::get('/website', function () {
    return view('website.home');
})->name('website.home');

Route::get('/website/jobs', function () {
    return view('website.jobs');
})->name('website.jobs');


Route::post('/website/inquiry', function (Request $request) {
    $data = $request->validate([
        'inquiry_type' => ['required', 'string', 'max:120'],
        'full_name' => ['required', 'string', 'max:160'],
        'company' => ['nullable', 'string', 'max:180'],
        'email' => ['required', 'email', 'max:180'],
        'phone' => ['nullable', 'string', 'max:80'],
        'message' => ['required', 'string', 'max:5000'],
    ]);

    $body = "New Website Inquiry - Sada Fezzan\n\n";
    $body .= "Inquiry Type: " . $data['inquiry_type'] . "\n";
    $body .= "Full Name: " . $data['full_name'] . "\n";
    $body .= "Company: " . ($data['company'] ?? 'N/A') . "\n";
    $body .= "Email: " . $data['email'] . "\n";
    $body .= "Phone: " . ($data['phone'] ?? 'N/A') . "\n\n";
    $body .= "Message:\n" . $data['message'] . "\n";

    Mail::raw($body, function ($message) use ($data) {
        $message->to('info@sfco.ly')
            ->replyTo($data['email'], $data['full_name'])
            ->subject('New Website Inquiry - ' . $data['inquiry_type']);
    });

    return redirect()->route('website.home')->with('success', 'Your request has been submitted successfully. Our team will contact you soon.');
})->name('website.inquiry.submit');
Route::get('/admin/employments/{employment}/portal-preview', [EmploymentPortalPreviewController::class, 'show'])->middleware(['web', 'auth'])->name('admin.employments.portal-preview');
Route::get('/admin/employments/{employment}/portal-preview/exit', [EmploymentPortalPreviewController::class, 'exit'])->middleware(['web', 'auth'])->name('admin.employments.portal-preview.exit');


/*
|--------------------------------------------------------------------------
| Sada Fezzan ERP Admin Notification Debug
|--------------------------------------------------------------------------
| Temporary internal admin-only routes to test the real logged-in Filament user.
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/sada-notifications/debug-current-user', function () {
        $user = auth()->user();

        $rows = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user?->id)
            ->latest('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'auth_user_id' => $user?->id,
            'auth_user_name' => $user?->name,
            'auth_user_email' => $user?->email,
            'is_admin' => (bool) ($user?->is_admin),
            'notifications_count_for_this_user' => $rows->count(),
            'latest_notifications_for_this_user' => $rows,
        ]);
    })->name('admin.sada-notifications.debug-current-user');

    Route::get('/admin/sada-notifications/send-test-current-user', function () {
        $user = auth()->user();

        abort_unless($user, 403);

        $before = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->count();

        try {
            \Filament\Notifications\Notification::make()
                ->title('ERP Admin Test Notification')
                ->body('This notification was sent to the currently logged-in ERP admin user.')
                ->icon('heroicon-o-bell-alert')
                ->color('info')
                ->persistent()
                ->sendToDatabase($user);
        } catch (\Throwable $e) {
            report($e);
        }

        $afterFilament = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->count();

        if ($afterFilament <= $before) {
            \Illuminate\Support\Facades\DB::table('notifications')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'Filament\\Notifications\\DatabaseNotification',
                'notifiable_type' => \App\Models\User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'ERP Admin Test Notification',
                    'body' => 'This notification was inserted directly for the currently logged-in ERP admin user.',
                    'icon' => 'heroicon-o-bell-alert',
                    'iconColor' => 'info',
                    'color' => 'info',
                    'actions' => [],
                    'duration' => 'persistent',
                    'view' => 'filament-notifications::notification',
                    'viewData' => [],
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect('/admin');
    })->name('admin.sada-notifications.send-test-current-user');
});


/*
|--------------------------------------------------------------------------
| Sada Fezzan ERP Admin Notification Utilities
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/admin/sada-notifications/mark-all-read', function () {
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->back();
    });

    Route::post('/admin/sada-notifications/clear-all', function () {
        \Illuminate\Support\Facades\DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', auth()->id())
            ->delete();

        return redirect()->back();
    });
});


/*
|--------------------------------------------------------------------------
| Sada Fezzan ERP - Employment Rotation Quick Edit
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/employments/{employment}/rotations/{rotation}/quick-edit', [\App\Http\Controllers\Admin\EmploymentRotationQuickEditController::class, 'edit'])
        ->name('admin.employments.rotations.quick-edit');

    Route::post('/admin/employments/{employment}/rotations/{rotation}/quick-edit', [\App\Http\Controllers\Admin\EmploymentRotationQuickEditController::class, 'update'])
        ->name('admin.employments.rotations.quick-update');

    Route::get('/admin/employments/{employment}/rotations/{rotation}/file/{type}', [\App\Http\Controllers\Admin\EmploymentRotationQuickEditController::class, 'openFile'])
        ->name('admin.employments.rotations.file');
});

/*
|--------------------------------------------------------------------------
| Sada Fezzan ERP - Access Control v2
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/erp-access-control', fn () => redirect('/admin/page-rules'))
        ->name('admin.erp-access-control.index');

    Route::post('/admin/erp-access-control/users', [\App\Http\Controllers\Admin\ErpAccessControlController::class, 'store'])
        ->name('admin.erp-access-control.store');

    Route::post('/admin/erp-access-control/users/{user}', [\App\Http\Controllers\Admin\ErpAccessControlController::class, 'update'])
        ->name('admin.erp-access-control.update');

    Route::delete('/admin/erp-access-control/users/{user}', [\App\Http\Controllers\Admin\ErpAccessControlController::class, 'destroy'])
        ->name('admin.erp-access-control.destroy');
});


Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/my-profile', [\App\Http\Controllers\Admin\ErpUserProfileController::class, 'edit'])
        ->name('admin.my-profile.edit');

    Route::put('/admin/my-profile', [\App\Http\Controllers\Admin\ErpUserProfileController::class, 'update'])
        ->name('admin.my-profile.update');
});


/*
|--------------------------------------------------------------------------
| Sada Fezzan ERP - Employment Print Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/admin/employments/{employment}/print-profile', [\App\Http\Controllers\Admin\EmploymentPrintController::class, 'profile'])
        ->name('admin.employments.print-profile');

    Route::get('/admin/employments/{employment}/print-rotation-history', [\App\Http\Controllers\Admin\EmploymentPrintController::class, 'rotationHistory'])
        ->name('admin.employments.print-rotation-history');
});


/*
|--------------------------------------------------------------------------
| Finance Expense Print Form
|--------------------------------------------------------------------------
*/
Route::get('/admin/finance-expenses/{financeExpense}/print', function (\App\Models\FinanceExpense $financeExpense) {
    abort_unless(auth()->check(), 403);

    return view('print.finance-expense', [
        'expense' => $financeExpense,
    ]);
})->name('admin.finance-expenses.print');


// Employee Portal requested file upload

Route::post('/portal/files/requested/{field}/upload', [\App\Http\Controllers\Portal\PortalFileController::class, 'uploadRequestedFile'])
    ->middleware(\App\Http\Middleware\PortalAuthenticate::class)
    ->name('portal.files.upload-requested');

