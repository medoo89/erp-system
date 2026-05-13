<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\FinanceExpense;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ClientExpensesPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.client-finance-page';

    public Client $client;

    public string $titleText = 'Client Finance Expenses';

    public string $type = 'expenses';

    public array $rows = [];

    public int $totalRecords = 0;

    public function mount(): void
    {
        $clientId = (int) request()->query('client');

        abort_unless($clientId > 0, 404);

        $this->client = Client::query()->findOrFail($clientId);

        $items = class_exists(FinanceExpense::class)
            ? FinanceExpense::query()
                ->where('client_id', $this->client->id)
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
                'project' => $latest->project_name ?: '-',
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
        return 'Client Expenses — ' . ($this->client->name ?? 'Client');
    }


    public static function canAccess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canErp('finance_expenses', 'view') ?? false);
    }

}
