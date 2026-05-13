<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Models\SalarySlip;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ProjectSalarySlipsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.project-finance-page';

    public Project $project;

    public $client;

    public string $titleText = 'Project Salary Slips';

    public string $type = 'salary_slips';

    public array $rows = [];

    public int $totalRecords = 0;

    public function mount(): void
    {
        $projectId = (int) request()->query('project');

        abort_unless($projectId > 0, 404);

        $this->project = Project::query()->with('client')->findOrFail($projectId);
        $this->client = $this->project->client;

        $items = class_exists(SalarySlip::class)
            ? SalarySlip::query()
                ->where('project_id', $this->project->id)
                ->latest('id')
                ->get()
            : collect();

        $grouped = $items->groupBy(function ($item) {
            return $this->resolveEmployeeName($item);
        });

        $this->rows = $grouped->map(function ($employeeItems, $employeeName) {
            $latest = $employeeItems->sortByDesc('id')->first();

            $totalNet = $employeeItems->sum(function ($item) {
                return (float) ($item->net_salary ?? $item->net_amount ?? $item->total_net ?? 0);
            });

            return [
                'employee' => $employeeName ?: '-',
                'total_slips' => $employeeItems->count(),
                'latest_status' => $this->formatStatus($latest->status ?? null),
                'latest_period' => $this->resolvePeriod($latest),
                'latest_amount' => $this->formatMoney(
                    $latest->net_salary ?? $latest->net_amount ?? $latest->total_net ?? 0,
                    $latest->currency ?? null
                ),
                'total_amount' => $this->formatMoney($totalNet, $latest->currency ?? null),
            ];
        })->values()->all();

        $this->totalRecords = count($this->rows);
    }

    protected function resolveEmployeeName($item): string
    {
        foreach ([
            'employee_name',
            'employee_full_name',
            'full_name',
            'staff_name',
            'employee',
            'name',
        ] as $field) {
            $value = $item->{$field} ?? null;
            if (filled($value)) {
                return (string) $value;
            }
        }

        if (isset($item->employment) && $item->employment) {
            foreach (['employee_name', 'employee_full_name', 'full_name', 'name'] as $field) {
                $value = $item->employment->{$field} ?? null;
                if (filled($value)) {
                    return (string) $value;
                }
            }
        }

        return '-';
    }

    protected function resolvePeriod($item): string
    {
        if (filled($item->period_label ?? null)) {
            return (string) $item->period_label;
        }

        $month = $item->month ?? ($item->payroll_month ?? null);
        $year = $item->year ?? ($item->payroll_year ?? null);

        if (filled($month) || filled($year)) {
            return trim(($month ?: '-') . ' / ' . ($year ?: '-'));
        }

        $start = $item->period_start ?? null;
        $end = $item->period_end ?? null;

        if (filled($start) || filled($end)) {
            return $this->formatDate($start) . ' → ' . $this->formatDate($end);
        }

        return '-';
    }

    protected function formatMoney($amount, ?string $currency = null): string
    {
        return number_format((float) $amount, 2) . ($currency ? ' ' . $currency : '');
    }

    protected function formatDate($value): string
    {
        if (blank($value)) {
            return '-';
        }

        try {
            return Carbon::parse($value)->format('M j, Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

    protected function formatStatus(?string $status): string
    {
        if (blank($status)) {
            return '-';
        }

        $mapped = ['locked' => 'Finalized'];

        $status = $mapped[strtolower((string) $status)] ?? $status;

        return str_replace('_', ' ', ucwords((string) $status, '_'));
    }

    public function getTitle(): string
    {
        return 'Project Salary Slips — ' . ($this->project->name ?? 'Project');
    }


    public static function canAccess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canErp('salary_slips', 'view') ?? false);
    }

}
