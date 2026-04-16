<?php

namespace App\Http\Controllers;

use App\Models\Job;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::query()
            ->where('is_active', true)
            ->where('is_archived', false)
            ->latest()
            ->get();

        return view('jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        abort_if(
            ! $job->isPubliclyVisible(),
            404
        );

        return view('jobs.show', compact('job'));
    }
}