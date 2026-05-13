<?php

namespace App\Filament\Resources\SalarySlips\Schemas;

use App\Models\Client;
use App\Models\Employment;
use App\Models\EmploymentRotation;
use App\Models\JobApplication;
use App\Models\Project;
use App\Models\SalarySlip;
use App\Models\TreasuryAccount;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalarySlipForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Salary Slip Details')
                    ->schema([
                        Select::make('employment_id')
                            ->label('Employment')
                            ->options(function () {
                                return Employment::query()
                                    ->orderBy('employee_name')
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [
                                        $item->id => ($item->employee_name ?: 'Unknown Employee') . ' #' . $item->id,
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Select::make('job_application_id')
                            ->label('Candidate / Job Application')
                            ->options(function () {
                                return JobApplication::query()
                                    ->orderBy('full_name')
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [
                                        $item->id => ($item->full_name ?: 'Unknown Candidate') . ' #' . $item->id,
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('client_id')
                            ->label('Client')
                            ->options(Client::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('project_id')
                            ->label('Project')
                            ->options(Project::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('employment_rotation_id')
                            ->label('Rotation')
                            ->options(function () {
                                return EmploymentRotation::query()
                                    ->orderByDesc('from_date')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        $label = 'Rotation #' . $item->id;

                                        if ($item->employment?->employee_name) {
                                            $label = $item->employment->employee_name . ' — ' . $label;
                                        }

                                        if ($item->from_date || $item->to_date) {
                                            $label .= ' (' .
                                                optional($item->from_date)->format('Y-m-d') .
                                                ' → ' .
                                                optional($item->to_date)->format('Y-m-d') .
                                                ')';
                                        }

                                        return [$item->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false),

                        DatePicker::make('period_start')
                            ->label('Period Start')
                            ->required(),

                        DatePicker::make('period_end')
                            ->label('Period End')
                            ->required(),

                        TextInput::make('salary_year')
                            ->label('Salary Year')
                            ->numeric()
                            ->required(),

                        TextInput::make('salary_month')
                            ->label('Salary Month')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(12)
                            ->required(),

                        TextInput::make('days_worked')
                            ->label('Days Worked')
                            ->numeric()
                            ->required(),

                        Select::make('salary_basis')
                            ->label('Salary Basis')
                            ->options([
                                SalarySlip::BASIS_DAILY_RATE => 'Daily Rate',
                                SalarySlip::BASIS_MONTHLY => 'Monthly',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('daily_rate')
                            ->label('Daily Rate')
                            ->numeric(),

                        TextInput::make('monthly_salary')
                            ->label('Monthly Salary')
                            ->numeric(),

                        TextInput::make('base_amount')
                            ->label('Base Amount')
                            ->numeric()
                            ->required(),

                        TextInput::make('adjustments_amount')
                            ->label('Adjustments')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('deductions_amount')
                            ->label('Deductions')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        TextInput::make('net_amount')
                            ->label('Net Amount')
                            ->numeric()
                            ->required(),

                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                                'LYD' => 'LYD',
                            ])
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                SalarySlip::STATUS_DRAFT => 'Draft',
                                SalarySlip::STATUS_APPROVED => 'Approved',
                                SalarySlip::STATUS_SENT_TO_BANK => 'Sent to Bank',
                                SalarySlip::STATUS_PAID => 'Paid',
                                SalarySlip::STATUS_BANK_REJECTED => 'Bank Rejected',
                            ])
                            ->required()
                            ->default(SalarySlip::STATUS_DRAFT)
                            ->native(false)
                            ->live()
                            ->dehydrated(true),

                        Select::make('treasury_account_id')
                            ->label('Treasury Account')
                            ->options(function () {
                                return TreasuryAccount::query()
                                    ->orderBy('account_name')
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [
                                        $item->id => ($item->account_name ?: 'Treasury Account') . ' — ' . ($item->currency ?: '-'),
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->helperText('Required when status is Sent to Bank.'),

                        Select::make('generated_by')
                            ->label('Generated By')
                            ->options(User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('approved_by')
                            ->label('Approved By')
                            ->options(User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ])
                    ->columns(3),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }
}
