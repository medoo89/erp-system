<?php

namespace App\Filament\Pages;

use App\Models\ClientInvoice;
use App\Models\Employment;
use App\Models\FinanceExpense;
use App\Models\Project;
use App\Models\SalarySlip;
use Filament\Pages\Page;

class ProjectProfilePage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.project-profile-page';

    public Project $project;

    public $client;

    public $jobs;

    public $employees;

    public int $jobsCount = 0;

    public int $employeesCount = 0;

    public int $clientInvoicesCount = 0;

    public int $salarySlipsCount = 0;

    public int $expensesCount = 0;

    public function mount(): void
    {
        $projectId = (int) request()->query('project');

        abort_unless($projectId > 0, 404);

        $project = Project::query()->findOrFail($projectId);

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

        $this->project = $project;
        $this->client = $project->client;
        $this->jobs = $project->jobs;
        $this->employees = $employees;
        $this->jobsCount = $project->jobs()->count();
        $this->employeesCount = $employees->count();
        $this->clientInvoicesCount = class_exists(ClientInvoice::class)
            ? ClientInvoice::query()->where('project_id', $project->id)->count()
            : 0;
        $this->salarySlipsCount = class_exists(SalarySlip::class)
            ? SalarySlip::query()->where('project_id', $project->id)->count()
            : 0;
        $this->expensesCount = class_exists(FinanceExpense::class)
            ? FinanceExpense::query()->where('project_id', $project->id)->count()
            : 0;
    }

    public function getTitle(): string
    {
        return 'Project Profile — ' . ($this->project->name ?? 'Project');
    }


    public static function canAccess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canErp('projects', 'view') ?? false);
    }

}
