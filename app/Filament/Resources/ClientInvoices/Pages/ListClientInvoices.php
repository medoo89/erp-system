<?php

namespace App\Filament\Resources\ClientInvoices\Pages;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use App\Filament\Resources\ClientInvoices\Widgets\ClientInvoiceStatsOverview;
use App\Models\ClientContractTerm;
use App\Models\InvoiceProfile;
use App\Models\Project;
use App\Models\SalarySlip;
use App\Services\GenerateClientInvoiceService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ListClientInvoices extends ListRecords
{
    protected string $view = 'filament.resources.client-invoices.pages.list-client-invoices-premium';
    protected static string $resource = ClientInvoiceResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getViewData(): array
    {
        $draftCount = \App\Models\ClientInvoice::query()->where('status', \App\Models\ClientInvoice::STATUS_DRAFT)->count();
        $approvedCount = \App\Models\ClientInvoice::query()->where('status', \App\Models\ClientInvoice::STATUS_APPROVED)->count();
        $submittedCount = \App\Models\ClientInvoice::query()->where('status', \App\Models\ClientInvoice::STATUS_SUBMITTED)->count();
        $partialCount = \App\Models\ClientInvoice::query()->where('status', \App\Models\ClientInvoice::STATUS_PARTIALLY_PAID)->count();
        $paidCount = \App\Models\ClientInvoice::query()->where('status', \App\Models\ClientInvoice::STATUS_PAID)->count();

        $openInvoiceValue = (float) \App\Models\ClientInvoice::query()
            ->whereIn('status', [
                \App\Models\ClientInvoice::STATUS_DRAFT,
                \App\Models\ClientInvoice::STATUS_APPROVED,
                \App\Models\ClientInvoice::STATUS_SUBMITTED,
                \App\Models\ClientInvoice::STATUS_PARTIALLY_PAID,
            ])
            ->sum('total_amount');

        return [
            'draftCount' => $draftCount,
            'approvedCount' => $approvedCount,
            'submittedCount' => $submittedCount,
            'partialCount' => $partialCount,
            'paidCount' => $paidCount,
            'openInvoiceValue' => $openInvoiceValue,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateInvoice')
                ->visible(fn () => (bool) auth()->user()?->canErp('client_invoices', 'create'))
                ->label('Generate Invoice')
                ->color('success')
                ->icon('heroicon-o-document-text')
                ->modalHeading('Generate Monthly Draft Invoice')
                ->modalDescription('Create a draft invoice directly from Client Invoices page.')
                ->modalSubmitActionLabel('Generate')
                ->form([
                    Select::make('project_id')
                        ->label('Project')
                        ->options(
                            Project::query()
                                ->with('client')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function ($project) {
                                    $label = $project->client?->name ?: 'Unknown Client';
                                    $label .= ' — ';
                                    $label .= $project->name ?: 'Unnamed Project';

                                    return [$project->id => $label];
                                })
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (! $state) {
                                return;
                            }

                            $project = Project::query()->find($state);

                            if (! $project) {
                                return;
                            }

                            $term = ClientContractTerm::query()
                                ->where('project_id', $project->id)
                                ->where('is_active', true)
                                ->orderByDesc('is_default')
                                ->orderByDesc('effective_from')
                                ->first();

                            if ($term) {
                                $set('billing_currency', $term->currency ?: 'EUR');
                                $set('foreign_percentage', $term->foreign_percentage ?? 100);
                                $set('local_percentage', $term->local_percentage ?? 0);
                                $set('local_currency', $term->local_currency ?: 'LYD');
                                $set('exchange_rate', $term->default_exchange_rate);
                            }

                            $profile = InvoiceProfile::query()
                                ->where('is_active', true)
                                ->orderByDesc('is_default')
                                ->orderBy('name')
                                ->first();

                            if ($profile) {
                                $set('invoice_profile_id', $profile->id);
                            }

                            $set('employment_ids', []);
                        }),

                    DatePicker::make('invoice_date')
                        ->label('Invoice Date')
                        ->default(now()->toDateString())
                        ->native(false)
                        ->required(),

                    Select::make('invoice_year')
                        ->label('Year')
                        ->options(function (Get $get) {
                            $projectId = $get('project_id');

                            if (! $projectId) {
                                return [now()->year => now()->year];
                            }

                            $years = SalarySlip::query()
                                ->where('project_id', $projectId)
                                ->select('salary_year')
                                ->distinct()
                                ->orderByDesc('salary_year')
                                ->pluck('salary_year', 'salary_year')
                                ->toArray();

                            return ! empty($years) ? $years : [now()->year => now()->year];
                        })
                        ->native(false)
                        ->required()
                        ->live(),

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
                        ->required()
                        ->live(),

                    Select::make('employment_ids')
                        ->label('Employees')
                        ->options(function (Get $get) {
                            $projectId = $get('project_id');

                            if (! $projectId) {
                                return [];
                            }

                            return SalarySlip::query()
                                ->with('employment')
                                ->where('project_id', $projectId)
                                ->when($get('invoice_year'), fn ($q, $year) => $q->where('salary_year', $year))
                                ->when($get('invoice_month'), fn ($q, $month) => $q->where('salary_month', $month))
                                ->get()
                                ->filter(fn ($slip) => $slip->employment)
                                ->mapWithKeys(function ($slip) {
                                    $employment = $slip->employment;

                                    $label = ($employment->employee_name ?: 'Unknown Employee')
                                        . ' — '
                                        . ($employment->position_title ?: '-');

                                    if (filled($employment->employee_code)) {
                                        $label .= ' [' . $employment->employee_code . ']';
                                    }

                                    return [$employment->id => $label];
                                })
                                ->toArray();
                        })
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Invoice lines will be generated from salary slips and paid days only.'),

                    Select::make('billing_currency')
                        ->label('Billing Currency')
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->default('EUR')
                        ->native(false)
                        ->required(),

                    TextInput::make('foreign_percentage')
                        ->label('Foreign %')
                        ->numeric()
                        ->default(100)
                        ->required(),

                    TextInput::make('local_percentage')
                        ->label('Local %')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Select::make('local_currency')
                        ->label('Local Currency')
                        ->options([
                            'LYD' => 'LYD',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                        ])
                        ->default('LYD')
                        ->native(false)
                        ->required(),

                    TextInput::make('exchange_rate')
                        ->label('Local Exchange Rate')
                        ->numeric(),

                    Select::make('invoice_profile_id')
                        ->label('Bank / Terms Profile')
                        ->options(
                            InvoiceProfile::query()
                                ->where('is_active', true)
                                ->orderByDesc('is_default')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(function ($profile) {
                                    $label = $profile->name;

                                    if ($profile->currency) {
                                        $label .= ' — ' . $profile->currency;
                                    }

                                    if ($profile->is_default) {
                                        $label .= ' [Default]';
                                    }

                                    return [$profile->id => $label];
                                })
                                ->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    try {
                        $project = Project::query()->findOrFail($data['project_id']);

                        $invoice = app(GenerateClientInvoiceService::class)->createDraftForProjectMonth(
                            project: $project,
                            employmentIds: $data['employment_ids'] ?? [],
                            year: (int) $data['invoice_year'],
                            month: (int) $data['invoice_month'],
                            billingCurrency: $data['billing_currency'] ?? null,
                            exchangeRate: filled($data['exchange_rate'] ?? null) ? (float) $data['exchange_rate'] : null,
                            foreignPercentage: isset($data['foreign_percentage']) ? (float) $data['foreign_percentage'] : null,
                            localPercentage: isset($data['local_percentage']) ? (float) $data['local_percentage'] : null,
                            localCurrency: $data['local_currency'] ?? null,
                            invoiceDate: $data['invoice_date'] ?? null,
                            invoiceProfileId: filled($data['invoice_profile_id'] ?? null) ? (int) $data['invoice_profile_id'] : null,
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

            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('client_invoices', 'create')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'view') ?? false);
    }

}
