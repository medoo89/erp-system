<?php

namespace App\Filament\Pages;

use App\Models\FinanceExpense;
use App\Models\Project;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ProjectExpensesPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.project-finance-page';

    public Project $project;

    public $client;

    public string $titleText = 'Project Finance Expenses';

    public string $type = 'expenses';

    public array $rows = [];

    public int $totalRecords = 0;

    public function mount(): void
    {
        $projectId = (int) request()->query('project');

        abort_unless($projectId > 0, 404);

        $this->project = Project::query()->with('client')->findOrFail($projectId);
        $this->client = $this->project->client;

        $items = class_exists(FinanceExpense::class)
            ? FinanceExpense::query()
                ->where('project_id', $this->project->id)
                ->latest('id')
                ->get()
            : collect();

        $grouped = $items->groupBy(function ($item) {
            return $this->formatText($item->category ?? 'Uncategorized');
        });

        $this->rows = $grouped->map(function ($categoryItems, $categoryName) {
            $latest = $categoryItems->sortByDesc('id')->first();

            $totalAmount = $categoryItems->sum(fn ($item) => (float) ($item->amount ?? 0));

            return [
                'category' => $categoryName ?: '-',
                'records_count' => $categoryItems->count(),
                'status' => $this->formatStatus($latest->status ?? null),
                'total_amount' => $this->formatMoney($totalAmount, $latest->currency ?? null),
                'latest_date' => $this->formatDate($latest->expense_date ?? $latest->created_at ?? null),
            ];
        })->values()->all();

        $this->totalRecords = count($this->rows);
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

    protected function formatText(?string $value): string
    {
        if (blank($value)) {
            return '-';
        }

        return str_replace('_', ' ', ucwords((string) $value, '_'));
    }

    public function getTitle(): string
    {
        return 'Project Expenses — ' . ($this->project->name ?? 'Project');
    }


    public static function canAccess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canErp('finance_expenses', 'view') ?? false);
    }

}
