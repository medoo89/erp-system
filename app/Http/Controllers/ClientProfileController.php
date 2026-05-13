<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\Employment;
use App\Models\FinanceExpense;
use App\Models\SalarySlip;
use Illuminate\View\View;

class ClientProfileController extends Controller
{
    public function show(Client $client): View
    {
        $client->load([
            'projects' => fn ($query) => $query->latest('id')->withCount('jobs'),
        ]);

        $projects = $client->projects;

        $projectIds = $projects->pluck('id')->filter()->values();

        $jobsTotal = $projects->sum('jobs_count');

        $employeesTotal = Employment::query()
            ->where(function ($query) use ($projectIds, $projects) {
                if ($projectIds->isNotEmpty()) {
                    $query->whereHas('job', function ($jobQuery) use ($projectIds) {
                        $jobQuery->whereIn('project_id', $projectIds);
                    });
                }

                foreach ($projects as $project) {
                    if (filled($project->name)) {
                        $query->orWhere('project_name', $project->name);
                    }
                }
            })
            ->count();

        $invoicesTotal = class_exists(ClientInvoice::class)
            ? ClientInvoice::query()->where('client_id', $client->id)->count()
            : 0;

        $salarySlipsTotal = class_exists(SalarySlip::class)
            ? SalarySlip::query()->where('client_id', $client->id)->count()
            : 0;

        $expensesTotal = class_exists(FinanceExpense::class)
            ? FinanceExpense::query()->where('client_id', $client->id)->count()
            : 0;

        $projectSummaries = $projects->map(function ($project) {
            $projectId = $project->id;

            $employeesCount = Employment::query()
                ->where(function ($query) use ($projectId, $project) {
                    $query->whereHas('job', function ($jobQuery) use ($projectId) {
                        $jobQuery->where('project_id', $projectId);
                    });

                    if (filled($project->name)) {
                        $query->orWhere('project_name', $project->name);
                    }
                })
                ->count();

            $invoicesCount = class_exists(ClientInvoice::class)
                ? ClientInvoice::query()->where('project_id', $projectId)->count()
                : 0;

            $salarySlipsCount = class_exists(SalarySlip::class)
                ? SalarySlip::query()->where('project_id', $projectId)->count()
                : 0;

            $expensesCount = class_exists(FinanceExpense::class)
                ? FinanceExpense::query()->where('project_id', $projectId)->count()
                : 0;

            return [
                'project' => $project,
                'jobs_count' => $project->jobs_count ?? 0,
                'employees_count' => $employeesCount,
                'invoices_count' => $invoicesCount,
                'salary_slips_count' => $salarySlipsCount,
                'expenses_count' => $expensesCount,
            ];
        });

        return view('admin.clients.profile', [
            'client' => $client,
            'projects' => $projects,
            'projectSummaries' => $projectSummaries,
            'projectsTotal' => $projects->count(),
            'jobsTotal' => $jobsTotal,
            'employeesTotal' => $employeesTotal,
            'invoicesTotal' => $invoicesTotal,
            'salarySlipsTotal' => $salarySlipsTotal,
            'expensesTotal' => $expensesTotal,
        ]);
    }
}
