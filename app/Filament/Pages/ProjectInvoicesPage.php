<?php

namespace App\Filament\Pages;

use App\Models\ClientInvoice;
use App\Models\Project;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ProjectInvoicesPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = null;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.project-finance-page';

    public Project $project;

    public $client;

    public string $titleText = 'Project Client Invoices';

    public string $type = 'invoices';

    public array $rows = [];

    public int $totalRecords = 0;

    public function mount(): void
    {
        $projectId = (int) request()->query('project');

        abort_unless($projectId > 0, 404);

        $this->project = Project::query()->with('client')->findOrFail($projectId);
        $this->client = $this->project->client;

        $items = class_exists(ClientInvoice::class)
            ? ClientInvoice::query()
                ->where('project_id', $this->project->id)
                ->latest('id')
                ->get()
            : collect();

        $this->rows = $items->map(function ($item) {
            return [
                'invoice' => $item->invoice_number ?: ($item->reference_no ?: ('#' . $item->id)),
                'status' => $this->formatStatus($item->status ?? null),
                'amount' => $this->formatMoney(
                    $item->total_amount ?? $item->grand_total ?? $item->amount ?? 0,
                    $item->currency ?? null
                ),
                'date' => $this->formatDate($item->invoice_date ?? $item->created_at ?? null),
                'client' => $item->client_name ?: ($this->client->name ?? '-'),
            ];
        })->all();

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

        $mapped = [
            'locked' => 'Finalized',
        ];

        $status = $mapped[strtolower($status)] ?? $status;

        return str_replace('_', ' ', ucwords((string) $status, '_'));
    }

    public function getTitle(): string
    {
        return 'Project Invoices — ' . ($this->project->name ?? 'Project');
    }


    public static function canAccess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canErp('client_invoices', 'view') ?? false);
    }

}
