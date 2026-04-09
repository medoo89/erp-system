<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobApplicationDocumentController;
use App\Http\Controllers\PublicRecruitmentCalendarController;
use App\Models\Job;

Route::get('/admin/job-applications/{jobApplication}/open-cv', [JobApplicationDocumentController::class, 'openCv'])
    ->name('job-applications.open-cv');

Route::get('/admin/job-applications/{jobApplication}/open-cv', [JobApplicationController::class, 'openCv'])
    ->name('job-applications.open-cv');

Route::get('/', function () {
    return redirect('/jobs');
})->name('home');

// Public recruitment calendar display
Route::get('/recruitment-calendar/public', [PublicRecruitmentCalendarController::class, 'index'])
    ->name('recruitment-calendar.public');

// صفحة كل الوظائف
Route::get('/jobs', function () {
    $jobs = Job::query()
        ->where('is_active', true)
        ->orderByDesc('created_at')
        ->get();

    return view('jobs.index', compact('jobs'));
})->name('jobs.index');

// صفحة تفاصيل وظيفة واحدة
Route::get('/jobs/{job}', function (Job $job) {
    return view('jobs.show', compact('job'));
})->name('jobs.show');

// صفحة التقديم
Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])
    ->name('jobs.apply');

// إرسال التقديم
Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])
    ->name('jobs.apply.store');

// صفحة النجاح
Route::get('/jobs/{job}/apply/success', [JobApplicationController::class, 'success'])
    ->name('jobs.apply.success');