<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ClientContractTerm;
use App\Models\Employment;
use App\Models\FinanceExpense;
use App\Models\SalarySlip;
use App\Services\GenerateClientInvoiceService;
use App\Services\SalarySlipGenerationService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected string $view = 'filament.resources.projects.pages.view-project';

    public function getTitle(): string
    {
        return (string) ($this->record->name ?: 'Project');
    }

    public function projectStatusLabel(): string
    {
        $status = (string) ($this->record->status ?: 'active');

        return match ($status) {
            'planning' => 'Planning',
            'active' => 'Active',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    public function projectHeroClass(): string
    {
        $status = (string) ($this->record->status ?: 'active');

        return match ($status) {
            'planning' => 'sf-hero--planning',
            'active' => 'sf-hero--active',
            'on_hold' => 'sf-hero--hold',
            'completed' => 'sf-hero--completed',
            'cancelled' => 'sf-hero--cancelled',
            default => 'sf-hero--active',
        };
    }

    public function projectStats(): array
    {
        $project = $this->record;

        $jobsCount = method_exists($project, 'jobs') ? $project->jobs()->count() : 0;

        $employeesQuery = $this->employmentQuery();
        $employeesCount = (clone $employeesQuery)->count();
        $activeEmployeesCount = (clone $employeesQuery)
            ->whereIn('status', ['active'])
            ->count();

        return [
            'jobs_count' => $jobsCount,
            'employees_count' => $employeesCount,
            'active_employees_count' => $activeEmployeesCount,
            'expenses' => $this->expenseTotals(),
            'financials' => $this->projectFinancialTotals(),
        ];
    }

    public function latestEmployees()
    {
        return $this->employmentQuery()
            ->latest('id')
            ->limit(6)
            ->get();
    }

    public function latestExpenses()
    {
        if (! class_exists(FinanceExpense::class)) {
            return collect();
        }

        return FinanceExpense::query()
            ->where('project_id', $this->record->id)
            ->whereNotIn('status', ['cancelled'])
            ->latest('expense_date')
            ->latest('id')
            ->limit(6)
            ->get();
    }

    public function projectFinancialTotals(): array
    {
        return [
            'revenue_total' => (float) $this->record->totalRevenueGenerated(),
            'revenue_foreign_by_currency' => $this->record->revenueForeignByCurrency(),
            'revenue_local_by_currency' => $this->record->revenueLocalByCurrency(),
            'salary_cost_total' => (float) $this->record->totalSalaryCost(),
            'salary_paid_total' => (float) $this->record->paidSalaryCost(),
            'salary_remaining_total' => (float) $this->record->remainingSalaryCost(),
            'other_expenses_total' => (float) $this->record->totalOtherExpenses(),
            'other_expenses_by_currency' => $this->record->otherExpensesByCurrency(),
            'paid_invoices_total' => (float) $this->record->totalPaidInvoices(),
            'draft_unpaid_invoices_total' => (float) $this->record->totalDraftAndUnpaidInvoices(),
            'net_result' => (float) $this->record->netResult(),
        ];
    }

    public function projectEmployeeFinancialBreakdown(): array
    {
        return $this->employmentQuery()
            ->with(['currentFinanceProfile'])
            ->get()
            ->map(function (Employment $employment) {
                return [
                    'employment_id' => $employment->id,
                    'employee_name' => $employment->employee_name,
                    'employee_code' => $employment->employee_code,
                    'position_title' => $employment->position_title,
                    'daily_rate' => (float) $employment->resolvedDailyRate(),
                    'salary_currency' => $employment->resolvedSalaryCurrency(),
                    'client_billing_rate' => (float) $employment->resolvedClientBillingRate(),
                    'client_billing_currency' => $employment->resolvedClientBillingCurrency(),
                    'salary_cost_total' => (float) $employment->totalSalaryCost(),
                    'salary_paid_total' => (float) $employment->paidSalaryCost(),
                    'salary_remaining_total' => (float) $employment->remainingSalaryCost(),
                    'other_expenses_total' => (float) $employment->totalOtherExpenses(),
                    'revenue_total' => (float) $employment->totalRevenueGenerated(),
                    'revenue_foreign_by_currency' => $employment->revenueForeignByCurrency(),
                    'revenue_local_by_currency' => $employment->revenueLocalByCurrency(),
                    'net_result' => (float) $employment->netResult(),
                ];
            })
            ->values()
            ->all();
    }

    protected function employmentQuery()
    {
        return Employment::query()
            ->where(function ($query) {
                $query->where('project_name', $this->record->name);

                if (method_exists($this->record, 'jobs')) {
                    $jobIds = $this->record->jobs()->pluck('id')->filter()->values()->all();

                    if (! empty($jobIds)) {
                        $query->orWhereIn('job_id', $jobIds);
                    }
                }
            });
    }

    protected function expenseTotals(): array
    {
        if (! class_exists(FinanceExpense::class)) {
            return [
                'USD' => 0,
                'EUR' => 0,
                'GBP' => 0,
                'LYD' => 0,
            ];
        }

        $rows = FinanceExpense::query()
            ->where('project_id', $this->record->id)
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('currency, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->toArray();

        return [
            'USD' => (float) ($rows['USD'] ?? 0),
            'EUR' => (float) ($rows['EUR'] ?? 0),
            'GBP' => (float) ($rows['GBP'] ?? 0),
            'LYD' => (float) ($rows['LYD'] ?? 0),
        ];
    }

    protected function resolveDefaultContractTerm(): ?ClientContractTerm
    {
        if (! method_exists($this->record, 'contractTerms')) {
            return null;
        }

        return $this->record->contractTerms()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderByDesc('effective_from')
            ->first();
    }

    protected function employmentOptions(): array
    {
        return $this->employmentQuery()
            ->orderBy('employee_name')
            ->get()
            ->mapWithKeys(function ($employment) {
                $label = ($employment->employee_name ?: 'Unknown Employee') . ' — ' . ($employment->position_title ?: '-');

                if (filled($employment->employee_code)) {
                    $label .= ' [' . $employment->employee_code . ']';
                }

                return [$employment->id => $label];
            })
            ->toArray();
    }

    protected function approvedSalarySlipExists(): bool
    {
        return SalarySlip::query()
            ->where('project_id', $this->record->id)
            ->where('status', SalarySlip::STATUS_APPROVED)
            ->exists();
    }

    protected function eligibleApprovedSalarySlipEmploymentOptions(?int $year = null, ?int $month = null): array
    {
        $query = SalarySlip::query()
            ->with(['employment.currentFinanceProfile'])
            ->where('project_id', $this->record->id)
            ->where('status', SalarySlip::STATUS_APPROVED);

        if ($year) {
            $query->where('salary_year', $year);
        }

        if ($month) {
            $query->where('salary_month', $month);
        }

        return $query
            ->get()
            ->filter(function ($slip) {
                $employment = $slip->employment;
                $profile = $employment?->currentFinanceProfile;

                return $employment
                    && $profile
                    && filled($profile->client_billing_currency)
                    && (float) ($profile->client_billing_rate ?? 0) > 0;
            })
            ->mapWithKeys(function ($slip) {
                $employment = $slip->employment;
                $label = ($employment->employee_name ?: 'Unknown Employee') . ' — ' . ($employment->position_title ?: '-');

                if (filled($employment->employee_code)) {
                    $label .= ' [' . $employment->employee_code . ']';
                }

                return [$employment->id => $label];
            })
            ->unique()
            ->toArray();
    }

    protected function invoiceReadySalarySlipExists(): bool
    {
        return ! empty($this->eligibleApprovedSalarySlipEmploymentOptions());
    }

    protected function invoiceBlockReason(): string
    {
        if (! $this->approvedSalarySlipExists()) {
            return 'No approved salary slips are available for this project yet.';
        }

        return 'Approved salary slips exist, but no employee is invoice-ready. Each employee must have a Current Finance Profile with valid Client Billing Rate and Client Billing Currency.';
    }

    protected function getHeaderActions(): array
    {
        $contractTerm = $this->resolveDefaultContractTerm();

        return [
            Action::make('generateSalarySlips')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('projects', 'generate_salary_slips') || auth()->user()?->canErp('salary_slips', 'create')))
                ->label('Generate Salary Slips')
                ->color('warning')
                ->icon('heroicon-o-calendar-days')
                ->modalHeading('Generate Monthly Draft Salary Slips')
                ->modalDescription('Generate draft salary slips for selected employees in this project.')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    Select::make('salary_year')
                        ->label('Year')
                        ->options(function () {
                            $currentYear = (int) now()->year;
                            $years = [];

                            for ($year = $currentYear - 3; $year <= $currentYear + 2; $year++) {
                                $years[$year] = (string) $year;
                            }

                            return $years;
                        })
                        ->default((int) now()->year)
                        ->native(false)
                        ->required(),

                    Select::make('salary_month')
                        ->label('Month')
                        ->options([
                            1 => '01 - January',
                            2 => '02 - February',
                            3 => '03 - March',
                            4 => '04 - April',
                            5 => '05 - May',
                            6 => '06 - June',
                            7 => '07 - July',
                            8 => '08 - August',
                            9 => '09 - September',
                            10 => '10 - October',
                            11 => '11 - November',
                            12 => '12 - December',
                        ])
                        ->default((int) now()->format('m'))
                        ->native(false)
                        ->required(),

                    Select::make('employment_ids')
                        ->label('Employees')
                        ->options($this->employmentOptions())
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText('Leave empty if you want to generate for all linked employees.'),

                    Select::make('replace_existing')
                        ->label('If already exists')
                        ->options([
                            1 => 'Replace existing slip',
                            0 => 'Keep existing slip',
                        ])
                        ->default(1)
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        $generated = app(SalarySlipGenerationService::class)
                            ->generateForProjectMonth(
                                $this->record,
                                (int) $data['salary_year'],
                                (int) $data['salary_month'],
                                $data['employment_ids'] ?? [],
                                (bool) $data['replace_existing'],
                                auth()->id()
                            );

                        Notification::make()
                            ->title('Salary slips generated successfully')
                            ->body(count($generated) . ' slip(s) generated as draft.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Could not generate salary slips')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('generateInvoice')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('projects', 'generate_invoice') || auth()->user()?->canErp('client_invoices', 'create')))
                ->label('Generate Invoice')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->disabled(fn () => ! $this->invoiceReadySalarySlipExists())
                ->tooltip(fn () => ! $this->invoiceReadySalarySlipExists()
                    ? $this->invoiceBlockReason()
                    : null)
                ->modalHeading('Generate Monthly Draft Invoice')
                ->modalDescription('Generate a monthly draft invoice from approved salary slips only. Employee billing rate is taken automatically from each employee finance profile.')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    DatePicker::make('invoice_date')
                        ->label('Invoice Date')
                        ->default(now()->toDateString())
                        ->native(false)
                        ->required(),

                    Select::make('invoice_year')
                        ->label('Year')
                        ->options(function () {
                            $years = SalarySlip::query()
                                ->where('project_id', $this->record->id)
                                ->where('status', SalarySlip::STATUS_APPROVED)
                                ->select('salary_year')
                                ->distinct()
                                ->orderByDesc('salary_year')
                                ->pluck('salary_year', 'salary_year')
                                ->toArray();

                            return ! empty($years) ? $years : [now()->year => now()->year];
                        })
                        ->default(now()->year)
                        ->native(false)
                        ->required(),

                    Select::make('invoice_month')
                        ->label('Month')
                        ->options([
                            1 => '01 - January',
                            2 => '02 - February',
                            3 => '03 - March',
                            4 => '04 - April',
                            5 => '05 - May',
                            6 => '06 - June',
                            7 => '07 - July',
                            8 => '08 - August',
                            9 => '09 - September',
                            10 => '10 - October',
                            11 => '11 - November',
                            12 => '12 - December',
                        ])
                        ->default((int) now()->format('m'))
                        ->native(false)
                        ->required(),

                    Select::make('employment_ids')
                        ->label('Employees')
                        ->options(function () {
                            return $this->eligibleApprovedSalarySlipEmploymentOptions();
                        })
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Only employees with approved salary slips and valid client billing data are eligible for invoicing.'),

                    Select::make('billing_currency')
                        ->label('Fallback Billing Currency')
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->default($contractTerm?->currency ?: 'EUR')
                        ->native(false)
                        ->helperText('Used only if an employee finance profile has no billing currency.'),

                    TextInput::make('foreign_percentage')
                        ->label('Foreign %')
                        ->numeric()
                        ->default($contractTerm?->foreign_percentage ?? 100)
                        ->required(),

                    TextInput::make('local_percentage')
                        ->label('Local %')
                        ->numeric()
                        ->default($contractTerm?->local_percentage ?? 0)
                        ->required(),

                    Select::make('local_currency')
                        ->label('Local Currency')
                        ->options([
                            'LYD' => 'LYD',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                        ])
                        ->default($contractTerm?->local_currency ?: 'LYD')
                        ->native(false)
                        ->required(),

                    TextInput::make('exchange_rate')
                        ->label('Local Exchange Rate')
                        ->numeric()
                        ->default($contractTerm?->default_exchange_rate)
                        ->helperText('Project terms control the invoice split.'),
                ])
                ->action(function (array $data) {
                    try {
                        if (! $this->invoiceReadySalarySlipExists()) {
                            Notification::make()
                                ->title('Cannot generate invoice')
                                ->body($this->invoiceBlockReason())
                                ->danger()
                                ->send();

                            return;
                        }

                        $invoice = app(GenerateClientInvoiceService::class)->createDraftForProjectMonth(
                            project: $this->record,
                            employmentIds: $data['employment_ids'] ?? [],
                            year: (int) $data['invoice_year'],
                            month: (int) $data['invoice_month'],
                            billingRate: null,
                            billingCurrency: $data['billing_currency'] ?? null,
                            exchangeRate: filled($data['exchange_rate'] ?? null) ? (float) $data['exchange_rate'] : null,
                            foreignPercentage: isset($data['foreign_percentage']) ? (float) $data['foreign_percentage'] : null,
                            localPercentage: isset($data['local_percentage']) ? (float) $data['local_percentage'] : null,
                            localCurrency: $data['local_currency'] ?? null,
                            invoiceDate: $data['invoice_date'] ?? null,
                        );

                        Notification::make()
                            ->title('Draft invoice generated successfully')
                            ->success()
                            ->send();

                        $this->redirect(ClientInvoiceResource::getUrl('view', ['record' => $invoice]));
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Could not generate invoice')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('edit')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('projects', 'edit'))
                ->label('Edit Project')
                ->color('gray')
                ->url(fn (): string => ProjectResource::getUrl('edit', ['record' => $this->record])),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'view') ?? false);
    }

}
