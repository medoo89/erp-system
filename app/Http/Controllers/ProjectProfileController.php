<?php

namespace App\Http\Controllers;

use App\Models\ClientInvoice;
use App\Models\Employment;
use App\Models\FinanceExpense;
use App\Models\Project;
use App\Models\SalarySlip;
use Illuminate\View\View;

class ProjectProfileController extends Controller
{
    public function show(Project $project): View
    {
        $project->load([
            'client',
            'jobs' => fn ($query) => $query->latest('id')->withCount('applications'),
        ]);

        $employees = Employment::query()
            ->with(['job'])
            ->where(function ($query) use ($project) {
                $query->whereHas('job', function ($jobQuery) use ($project) {
                    $jobQuery->where('project_id', $project->id);
                });

                if (filled($project->name)) {
                    $query->orWhere('project_name', $project->name);
                }
            })
            ->latest('id')
            ->get();

        $jobsCount = $project->jobs()->count();

        $clientInvoicesCount = class_exists(ClientInvoice::class)
            ? ClientInvoice::query()->where('project_id', $project->id)->count()
            : 0;

        $salarySlipsCount = class_exists(SalarySlip::class)
            ? SalarySlip::query()->where('project_id', $project->id)->count()
            : 0;

        $expensesCount = class_exists(FinanceExpense::class)
            ? FinanceExpense::query()->where('project_id', $project->id)->count()
            : 0;

        return view('admin.projects.profile', [
            'project' => $project,
            'client' => $project->client,
            'jobs' => $project->jobs,
            'employees' => $employees,
            'jobsCount' => $jobsCount,
            'employeesCount' => $employees->count(),
            'clientInvoicesCount' => $clientInvoicesCount,
            'salarySlipsCount' => $salarySlipsCount,
            'expensesCount' => $expensesCount,
        ]);
    }
}
