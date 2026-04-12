<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Storage;

class JobApplicationDocumentController extends Controller
{
    public function openCv(JobApplication $jobApplication)
    {
        if (blank($jobApplication->cv_path)) {
            abort(404, 'CV file not found.');
        }

        if (! Storage::disk('public')->exists($jobApplication->cv_path)) {
            abort(404, 'CV file does not exist on storage.');
        }

        return redirect(Storage::disk('public')->url($jobApplication->cv_path));
    }
}