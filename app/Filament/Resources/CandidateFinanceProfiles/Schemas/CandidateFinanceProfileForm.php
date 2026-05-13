<?php

namespace App\Filament\Resources\CandidateFinanceProfiles\Schemas;

use App\Models\CandidateFinanceProfile;
use App\Models\Client;
use App\Models\Employment;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\PreEmployment;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CandidateFinanceProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile Links')
                ->schema([
                    Select::make('job_application_id')
                        ->label('Job Application')
                        ->options(JobApplication::query()->orderByDesc('id')->pluck('full_name', 'id')->toArray())
                        ->searchable()
                        ->preload(),

                    Select::make('pre_employment_id')
                        ->label('Pre-Employment')
                        ->options(PreEmployment::query()->orderByDesc('id')->pluck('full_name', 'id')->toArray())
                        ->searchable()
                        ->preload(),

                    Select::make('employment_id')
                        ->label('Employment')
                        ->options(
                            Employment::query()
                                ->orderBy('employee_name')
                                ->get()
                                ->mapWithKeys(fn ($item) => [$item->id => ($item->employee_name ?: 'Unknown') . ' — ' . ($item->position_title ?: '-')])
                                ->toArray()
                        )
                        ->searchable()
                        ->preload(),

                    Select::make('job_id')
                        ->label('Job')
                        ->options(Job::query()->orderByDesc('id')->pluck('title', 'id')->toArray())
                        ->searchable()
                        ->preload(),

                    Select::make('client_id')
                        ->label('Client')
                        ->options(Client::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload(),

                    Select::make('project_id')
                        ->label('Project')
                        ->options(Project::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload(),
                ])
                ->columns(3),

            Section::make('Employee Cost Profile')
                ->schema([
                    Select::make('salary_basis')
                        ->label('Salary Basis')
                        ->options([
                            CandidateFinanceProfile::BASIS_DAILY_RATE => 'Daily Rate',
                            CandidateFinanceProfile::BASIS_MONTHLY => 'Monthly',
                        ])
                        ->default(CandidateFinanceProfile::BASIS_DAILY_RATE)
                        ->required(),

                    TextInput::make('daily_rate')
                        ->label('Daily Rate')
                        ->numeric(),

                    TextInput::make('monthly_salary')
                        ->label('Monthly Salary')
                        ->numeric(),

                    Select::make('payout_currency')
                        ->label('Payout Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->native(false),

                    TextInput::make('agreed_salary_amount')
                        ->label('Agreed Salary Amount')
                        ->numeric(),

                    Select::make('agreed_salary_currency')
                        ->label('Agreed Salary Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->native(false),
                ])
                ->columns(3),

            Section::make('Client Billing Profile')
                ->schema([
                    Select::make('client_billing_basis')
                        ->label('Client Billing Basis')
                        ->options([
                            CandidateFinanceProfile::BASIS_DAILY_RATE => 'Daily Rate',
                            CandidateFinanceProfile::BASIS_MONTHLY => 'Monthly',
                        ])
                        ->default(CandidateFinanceProfile::BASIS_DAILY_RATE)
                        ->required(),

                    TextInput::make('client_billing_rate')
                        ->label('Client Billing Rate')
                        ->numeric()
                        ->required(),

                    Select::make('client_billing_currency')
                        ->label('Client Billing Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->native(false)
                        ->required(),
                ])
                ->columns(3),

            Section::make('Status & Notes')
                ->schema([
                    TextInput::make('finance_status')
                        ->label('Finance Status')
                        ->maxLength(255),

                    DatePicker::make('effective_from')
                        ->label('Effective From'),

                    DatePicker::make('effective_to')
                        ->label('Effective To'),

                    Toggle::make('is_current')
                        ->label('Current Profile')
                        ->default(true),

                    Toggle::make('is_hidden_from_non_finance')
                        ->label('Hide From Non-Finance')
                        ->default(true),

                    Textarea::make('finance_notes')
                        ->label('Finance Notes')
                        ->rows(5)
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }
}