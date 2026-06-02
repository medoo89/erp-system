<?php

namespace App\Filament\Resources\ClientInvoices\Schemas;

use App\Models\ProjectContract;

use App\Models\BankProfile;
use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\Project;
use App\Models\SalarySlip;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice Header')
                ->schema([
                    TextInput::make('invoice_number')
                        ->label('Invoice Number')
                        ->required()
                        ->maxLength(255),

                    DatePicker::make('invoice_date')
                        ->label('Invoice Date')
                        ->native(false),

                    Select::make('status')
                        ->label('Status')
                        ->options(ClientInvoice::statusOptions())
                        ->default(ClientInvoice::STATUS_DRAFT)
                        ->required()
                        ->native(false),

                    Select::make('client_id')
                        ->label('Client')
                        ->options(
                            Client::query()->orderBy('name')->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),

                    
                    \Filament\Forms\Components\Select::make('project_contract_id')
                        ->label('Contract / Agreement Counter')
                        ->default(fn () => request()->query('project_contract_id'))
                        ->options(function ($get = null): array {
                            $projectId = null;

                            try {
                                if (is_callable($get)) {
                                    $projectId = $get('project_id');
                                }
                            } catch (\Throwable $e) {
                                $projectId = null;
                            }

                            $query = ProjectContract::query()->orderByDesc('id');

                            if ($projectId) {
                                $query->where('project_id', $projectId);
                            }

                            return $query
                                ->get()
                                ->mapWithKeys(function (ProjectContract $contract): array {
                                    $title = $contract->title
                                        ?? $contract->contract_no
                                        ?? ('Contract #' . $contract->id);

                                    $value = number_format((float) ($contract->contract_value ?? 0), 2);
                                    $currency = $contract->currency ?? '';

                                    return [
                                        $contract->id => $title . ' • ' . $value . ' ' . $currency,
                                    ];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->helperText('Used for contract balance and internal tax allocation.'),

                    \Filament\Forms\Components\Toggle::make('show_contract_tax_on_invoice_document')
                        ->label('Show allocated contract tax on invoice document')
                        ->default(false)
                        ->helperText('Default is OFF. Tax allocation is internal/admin only unless enabled.'),

Select::make('project_id')
                        ->default(fn () => request()->query('project_id'))
                        ->label('Project')
                        ->options(
                            Project::query()->orderBy('name')->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DatePicker::make('period_start')
                        ->label('Invoice Period Start')
                        ->native(false),

                    DatePicker::make('period_end')
                        ->label('Invoice Period End')
                        ->native(false),

                    TextInput::make('payment_terms_label')
                        ->label('Payment Terms'),

                    TextInput::make('bill_to_name')
                        ->label('Bill To Name'),

                    Textarea::make('bill_to_address')
                        ->label('Bill To Address')
                        ->rows(3),

                    TextInput::make('bill_to_phone')
                        ->label('Bill To Phone'),
                ])
                ->columns(3),

            Section::make('Currency Split')
                ->schema([
                    Select::make('foreign_currency')
                        ->label('Foreign Currency')
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->native(false),

                    TextInput::make('foreign_percentage')
                        ->label('Foreign %')
                        ->numeric(),

                    Select::make('local_currency')
                        ->label('Local Currency')
                        ->options([
                            'LYD' => 'LYD',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                        ])
                        ->native(false),

                    TextInput::make('local_percentage')
                        ->label('Local %')
                        ->numeric(),

                    TextInput::make('exchange_rate')
                        ->label('Exchange Rate')
                        ->numeric(),

                    Select::make('display_currency')
                        ->label('Display Currency')
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->native(false),
                ])
                ->columns(3),

            Section::make('Amounts')
                ->schema([
                    TextInput::make('subtotal_amount')->label('Subtotal')->numeric(),
                    TextInput::make('tax_percent')->label('Tax %')->numeric(),
                    TextInput::make('tax_amount')->label('Tax Amount')->numeric(),
                    TextInput::make('total_amount')->label('Total')->numeric(),
                    TextInput::make('foreign_amount_due')->label('Foreign Amount Due')->numeric(),
                    TextInput::make('local_amount_due')->label('Local Amount Due')->numeric(),
                    TextInput::make('local_amount_foreign_equivalent')->label('Local Amount Foreign Equivalent')->numeric(),
                ])
                ->columns(3),

            Section::make('Invoice Lines')
                ->description('Each employee line uses that employee’s own client billing rate from the finance profile.')
                ->schema([
                    Repeater::make('lines')
                        ->relationship('lines')
                        ->schema([

                            Select::make('salary_slip_id')
                                ->label('Salary Slip')
                                ->options(function (): array {
                                    return SalarySlip::query()
                                        ->with(['employment', 'project'])
                                        ->orderByDesc('period_start')
                                        ->limit(250)
                                        ->get()
                                        ->mapWithKeys(function (SalarySlip $slip): array {
                                            $employee = $slip->employment?->full_name
                                                ?? $slip->employment?->name
                                                ?? ('Employment #' . $slip->employment_id);

                                            $project = $slip->project?->name
                                                ?? $slip->project?->project_name
                                                ?? 'No Project';

                                            $period = optional($slip->period_start)->format('Y-m-d')
                                                . ' → '
                                                . optional($slip->period_end)->format('Y-m-d');

                                            $amount = number_format((float) ($slip->net_amount ?? $slip->base_amount ?? 0), 2);
                                            $currency = $slip->currency ?: '';

                                            return [
                                                $slip->id => "{$employee} — {$project} — {$period} — {$amount} {$currency}",
                                            ];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->preload()
                                ->live()
                                ->native(false)
                                ->helperText('Select a salary slip to generate invoice line data and timesheet days.')
                                ->afterStateUpdated(function ($state, $set, $get): void {
                                    if (blank($state)) {
                                        return;
                                    }

                                    $slip = SalarySlip::query()
                                        ->with(['employment', 'project', 'days'])
                                        ->find($state);

                                    if (! $slip) {
                                        return;
                                    }

                                    $employee = $slip->employment;
                                    $project = $slip->project;

                                    $paidUnits = (float) $slip->days
                                        ->filter(fn ($day) => (bool) ($day->is_paid_day ?? false))
                                        ->sum(fn ($day) => (float) ($day->pay_multiplier ?? 1));

                                    if ($paidUnits <= 0) {
                                        $paidUnits = (float) ($slip->days_worked ?? 0);
                                    }

                                    $unitRate = (float) ($get('unit_rate') ?: $slip->daily_rate ?: 0);
                                    $amount = round($paidUnits * $unitRate, 2);

                                    $employeeName = $employee?->full_name
                                        ?? $employee?->name
                                        ?? $employee?->employee_name
                                        ?? null;

                                    $position = $employee?->position
                                        ?? $employee?->job_title
                                        ?? $employee?->designation
                                        ?? null;

                                    $projectName = $project?->name
                                        ?? $project?->project_name
                                        ?? null;

                                    $set('employment_id', $slip->employment_id);
                                    $set('project_id', $slip->project_id);
                                    $set('candidate_name', $employeeName);
                                    $set('position_title', $position);
                                    $set('project_name', $projectName);
                                    $set('service_title', $position ?: 'Manpower Supply / Daily Rate');
                                    $set('service_period_start', optional($slip->period_start)->format('Y-m-d'));
                                    $set('service_period_end', optional($slip->period_end)->format('Y-m-d'));
                                    $set('service_month_label', optional($slip->period_start)->format('F Y'));
                                    $set('quantity', $paidUnits);
                                    $set('unit_rate', $unitRate);
                                    $set('amount', $amount);
                                    $set('currency', $slip->currency);
                                    $set('foreign_currency', $slip->currency);
                                }),

                            TextInput::make('service_title')
                                ->label('Service Title')
                                ->maxLength(255),

                            TextInput::make('candidate_name')
                                ->label('Employee Name')
                                ->maxLength(255),

                            TextInput::make('position_title')
                                ->label('Position')
                                ->maxLength(255),

                            TextInput::make('project_name')
                                ->label('Project Name')
                                ->maxLength(255),

                            DatePicker::make('service_period_start')
                                ->label('Service Start')
                                ->native(false),

                            DatePicker::make('service_period_end')
                                ->label('Service End')
                                ->native(false),

                            TextInput::make('service_month_label')
                                ->label('Service Month')
                                ->maxLength(255),

                            TextInput::make('quantity')
                                ->label('Paid Days')
                                ->numeric(),

                            TextInput::make('unit_rate')
                                ->label('Client Billing Rate')
                                ->numeric(),

                            TextInput::make('amount')
                                ->label('Line Amount')
                                ->numeric(),

                            Select::make('currency')
                                ->label('Billing Currency')
                                ->options([
                                    'USD' => 'USD',
                                    'EUR' => 'EUR',
                                    'GBP' => 'GBP',
                                    'LYD' => 'LYD',
                                ])
                                ->native(false),

                            Textarea::make('scope_description')
                                ->label('Scope / Description')
                                ->rows(3)
                                ->columnSpanFull(),

                            Textarea::make('line_notes')
                                ->label('Internal Notes')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->defaultItems(0),
                ]),

            Section::make('Bank & Terms')
                ->schema([
                    Select::make('bank_profile_id')
                        ->label('Bank Profile')
                        ->options(function () {
                            return BankProfile::query()
                                ->where('is_active', true)
                                ->orderByDesc('is_default_for_invoices')
                                ->orderBy('profile_name')
                                ->get()
                                ->mapWithKeys(fn (BankProfile $profile) => [
                                    $profile->id => ($profile->profile_name ?: 'Bank Profile')
                                        . ' — ' . ($profile->bank_name ?: 'Bank')
                                        . ' — Treasury: ' . ($profile->treasuryAccount?->account_name ?: 'No Treasury Account'),
                                ])
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->live()
                        ->native(false)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if (blank($state)) {
                                return;
                            }

                            $profile = BankProfile::query()->find($state);

                            if (! $profile) {
                                return;
                            }

                            $set('bank_name', $profile->bank_name);
                            $set('swift_code', $profile->swift_code);

                            $currency = strtoupper((string) ($profile->currency ?: ''));

                            if ($currency === 'USD') {
                                $set('iban_usd', $profile->iban);
                            }

                            if ($currency === 'EUR') {
                                $set('iban_eur', $profile->iban);
                            }

                            if ($currency === 'LYD') {
                                $set('iban_lyd', $profile->iban);
                                $set('account_number_lyd', $profile->account_number);
                            }
                        }),

                    TextInput::make('bank_name')->label('Bank Name'),

                    TextInput::make('swift_code')->label('Swift Code'),

                    TextInput::make('account_number_lyd')->label('Account Number LYD'),

                    TextInput::make('iban_lyd')->label('IBAN LYD'),

                    TextInput::make('iban_usd')->label('IBAN USD'),

                    TextInput::make('iban_eur')->label('IBAN EUR'),

                    Textarea::make('notes')->label('Notes')->rows(4)->columnSpanFull(),
                    Textarea::make('terms_text')->label('Terms Text')->rows(6)->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }
}
