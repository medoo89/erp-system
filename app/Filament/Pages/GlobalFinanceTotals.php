<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Employment;
use App\Models\Project;
use App\Services\FinanceTotalsService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GlobalFinanceTotals extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Global Finance Totals';

    protected static ?string $title = 'Global Finance Totals';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.global-finance-totals';

    public ?array $data = [];

    public array $totals = [];

    public function mount(FinanceTotalsService $service): void
    {
        $this->date_from = '2025-01-01';
        $this->date_to = now()->toDateString();

        if (property_exists($this, 'dateFrom')) {
            $this->dateFrom = $this->date_from;
        }

        if (property_exists($this, 'dateTo')) {
            $this->dateTo = $this->date_to;
        }

        if (property_exists($this, 'year')) {
            $this->year = null;
        }

        if (property_exists($this, 'month')) {
            $this->month = null;
        }

        $defaults = [
            'date_from' => '2025-01-01',
            'date_to' => now()->toDateString(),

            'base_currency' => 'EUR',
            'rate_usd' => 0.92,
            'rate_eur' => 1,
            'rate_lyd' => 0.19,
            'rate_gbp' => 1.17,

            'project_id' => null,
            'client_id' => null,
            'employment_id' => null,
            'year' => null,
            'month' => null,
        ];

        if (property_exists($this, 'data') && is_array($this->data)) {
            $this->data = array_merge($defaults, $this->data);
        }

        try {
            $this->form->fill($defaults);
        } catch (\Throwable $e) {
            // Keep page safe even if form schema changes.
        }

        $this->refreshTotals($service);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Filters')
                    ->schema([
                        TextInput::make('year')
                            ->label('Year')
                            ->numeric()
                            ->default(null),

                        Select::make('month')
                            ->label('Month')
                            ->options([
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December',
                            ])
                            ->default(null)
                            ->native(false),

                        DatePicker::make('date_from')
                            ->label('Date From'),

                        DatePicker::make('date_to')
                            ->label('Date To'),

                        Select::make('client_id')
                            ->label('Client')
                            ->options(
                                Client::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('project_id')
                            ->label('Project')
                            ->options(
                                Project::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('employment_id')
                            ->label('Employee')
                            ->options(
                                Employment::query()
                                    ->orderBy('employee_name')
                                    ->pluck('employee_name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->columns(3),

                Section::make('Exchange & Print Settings')
                    ->schema([
                        Select::make('base_currency')
                            ->label('Base Currency')
                            ->options([
                                'EUR' => 'EUR',
                                'USD' => 'USD',
                                'LYD' => 'LYD',
                                'GBP' => 'GBP',
                            ])
                            ->default('EUR')
                            ->required()
                            ->native(false),

                        TextInput::make('rate_usd')
                            ->label('1 USD = Base Currency')
                            ->numeric()
                            ->default(0.92)
                            ->required()
                            ->helperText('Enter the value of 1 USD in the selected base currency.'),

                        TextInput::make('rate_eur')
                            ->label('1 EUR = Base Currency')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->helperText('If base currency is EUR, this should remain 1.'),

                        TextInput::make('rate_lyd')
                            ->label('1 LYD = Base Currency')
                            ->numeric()
                            ->default(0.19)
                            ->required(),

                        TextInput::make('rate_gbp')
                            ->label('1 GBP = Base Currency')
                            ->numeric()
                            ->default(1.17)
                            ->required(),
                    ])
                    ->columns(4),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printReport')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('treasury', 'view_totals'))
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    public function applyFilters(): void
    {
        $this->refreshTotals(app(FinanceTotalsService::class));
    }
    protected function getFinanceTotalsFilters(): array
    {
        try {
            $state = $this->form->getRawState();
        } catch (\Throwable $e) {
            $state = $this->data ?? [];
        }

        $dateFrom = $state['date_from'] ?? null;
        $dateTo = $state['date_to'] ?? null;

        $filters = [
            'date_from' => $dateFrom ?: '2025-01-01',
            'date_to' => $dateTo ?: now()->toDateString(),
        ];

        foreach (['project_id', 'client_id', 'employment_id'] as $key) {
            if (! empty($state[$key])) {
                $filters[$key] = $state[$key];
            }
        }

        return $filters;
    }

    protected function refreshTotals(FinanceTotalsService $service): void
    {
        $this->totals = $service->build($this->getFinanceTotalsFilters());
    }
    protected function getPrintUrl(): string
    {
        try {
            $state = $this->form->getRawState();
        } catch (\Throwable $e) {
            $state = $this->data ?? [];
        }

        return route('finance.totals.print', [
            'year' => null,
            'month' => null,
            'date_from' => $state['date_from'] ?? '2025-01-01',
            'date_to' => $state['date_to'] ?? now()->toDateString(),
            'client_id' => $state['client_id'] ?? null,
            'project_id' => $state['project_id'] ?? null,
            'employment_id' => $state['employment_id'] ?? null,
            'base_currency' => $state['base_currency'] ?? 'EUR',
            'rate_usd' => $state['rate_usd'] ?? 0.92,
            'rate_eur' => $state['rate_eur'] ?? 1,
            'rate_lyd' => $state['rate_lyd'] ?? 0.19,
            'rate_gbp' => $state['rate_gbp'] ?? 1.17,
        ]);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view_totals') ?? false);
    }

}
