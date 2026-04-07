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
            ->where(function ($query) {
                $query->whereNull('closing_date')
                    ->orWhereDate('closing_date', '>=', today());
            })
            ->latest()
            ->get();

        return view('jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        abort_if(
            ! $job->is_active
            || $job->is_archived
            || (
                filled($job->closing_date)
                && $job->closing_date->lt(today())
            ),
            404
        );

        return view('jobs.show', compact('job'));
    }
}