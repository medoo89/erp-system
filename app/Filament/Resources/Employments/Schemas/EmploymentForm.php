<?php

namespace App\Filament\Resources\Employments\Schemas;

use App\Models\Job;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmploymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->schema([
                        Select::make('employee_category')
                            ->label('Employee Category')
                            ->options([
                                'operational' => 'Operational / Recruitment Employee',
                                'office' => 'Office / Internal Employee',
                            ])
                            ->default('operational')
                            ->native(false)
                            ->required()
                            ->live()
                            ->helperText('Operational employees are linked to client/project/rotation. Office employees are internal Sada Fezzan staff.'),

                        Select::make('office_department')
                            ->label('Office Department')
                            ->options([
                                'management' => 'Management',
                                'finance' => 'Finance',
                                'hr' => 'HR',
                                'recruitment' => 'Recruitment',
                                'operations' => 'Operations',
                                'administration' => 'Administration',
                                'sales' => 'Sales',
                                'marketing' => 'Marketing',
                                'it' => 'IT',
                                'other' => 'Other',
                            ])
                            ->native(false)
                            ->searchable()
                            ->visible(fn (callable $get) => $get('employee_category') === 'office')
                            ->required(fn (callable $get) => $get('employee_category') === 'office'),

                        Select::make('office_employee_type')
                            ->label('Office Employee Type')
                            ->options([
                                'full_time' => 'Full Time',
                                'part_time' => 'Part Time',
                                'contractor' => 'Contractor',
                                'consultant' => 'Consultant',
                                'temporary' => 'Temporary',
                            ])
                            ->default('full_time')
                            ->native(false)
                            ->visible(fn (callable $get) => $get('employee_category') === 'office'),

                        TextInput::make('employee_name')
                            ->label('Employee Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('employee_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('employee_phone')
                            ->label('Phone')
                            ->maxLength(255),

                        TextInput::make('employee_code')
                            ->label('Employee Code')
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Operational codes are generated automatically from Sada Fezzan + Client Code + Project Code + Sequence. Office employees can be saved without client/project.'),

                        Select::make('job_id')
                            ->label('Position / Job')
                            ->options(function () {
                                return Job::query()
                                    ->with(['project.client'])
                                    ->orderBy('title')
                                    ->get()
                                    ->mapWithKeys(function ($job) {
                                        $client = $job->project?->client?->name ?: '-';
                                        $project = $job->project?->name ?: '-';

                                        return [
                                            $job->id => "{$job->title} — {$client} / {$project}",
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->required(fn ($record, callable $get) => blank($record) && $get('employee_category') !== 'office')
                            ->visible(fn ($record, callable $get) => blank($record) && $get('employee_category') !== 'office')
                            ->dehydrated(fn ($record) => blank($record))
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('position_title', null);
                                    $set('client_name', null);
                                    $set('project_name', null);
                                    $set('employee_code', null);
                                    return;
                                }

                                $job = Job::with('project.client')->find($state);

                                if (! $job) {
                                    return;
                                }

                                $set('position_title', $job->title);
                                $set('client_name', $job->project?->client?->name);
                                $set('project_name', $job->project?->name);

                                $clientCode = $job->project?->client?->code;
                                $projectCode = $job->project?->project_code ?: $job->project?->code;

                                if ($clientCode && $projectCode) {
                                    $set(
                                        'employee_code',
                                        app(CodeGeneratorService::class)->generateEmployeeCode($clientCode, $projectCode)
                                    );
                                }
                            }),

                        TextInput::make('position_title')
                            ->label('Position')
                            ->readOnly(fn (callable $get) => $get('employee_category') !== 'office')
                            ->required(fn (callable $get) => $get('employee_category') === 'office')
                            ->placeholder('Example: Finance Officer / Recruitment Coordinator')
                            ->dehydrated(),

                        TextInput::make('client_name')
                            ->label('Client')
                            ->readOnly()
                            ->visible(fn (callable $get) => $get('employee_category') !== 'office')
                            ->dehydrated(),

                        TextInput::make('project_name')
                            ->label('Project')
                            ->readOnly()
                            ->visible(fn (callable $get) => $get('employee_category') !== 'office')
                            ->dehydrated(),

                        Select::make('assigned_hr_user_id')
                            ->label('Operation Officer')
                            ->options(
                                User::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateHydrated(function ($state, callable $set, $record) {
                                if (blank($record?->operation_officer_name) && filled($state)) {
                                    $user = User::find($state);

                                    if ($user) {
                                        $set('operation_officer_name', $user->name);
                                    }
                                }
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('operation_officer_name', null);
                                    return;
                                }

                                $user = User::find($state);

                                if ($user) {
                                    $set('operation_officer_name', $user->name);
                                }
                            }),

                        TextInput::make('operation_officer_name')
                            ->label('Operation Officer Name')
                            ->readOnly()
                            ->dehydrated(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Salary Configuration')
                ->hidden(fn (string $operation): bool => $operation === 'edit')
                ->description('Salary values are controlled from the finance/salary profile workflow and are locked during employment editing.')
                    ->schema([
                        Select::make('salary_basis')
                            ->label('Salary Basis')
                            ->options([
                                'daily_rate' => 'Daily Rate',
                                'monthly' => 'Monthly',
                            ])
                            ->native(false)
                            ->required()
                            ->live()
                            ->helperText('For your current workflow, the final agreed rate is usually Daily Rate.'),

                        TextInput::make('daily_rate')
                            ->label('Final Agreed Daily Rate')
                            ->numeric()
                            ->prefix('Rate')
                            ->visible(fn (callable $get) => $get('salary_basis') === 'daily_rate')
                            ->required(fn (callable $get) => $get('salary_basis') === 'daily_rate')
                            ->helperText('This is the amount used for Salary Slip calculation and daily payroll logic.'),

                        TextInput::make('monthly_salary')
                            ->label('Monthly Salary')
                            ->numeric()
                            ->visible(fn (callable $get) => $get('salary_basis') === 'monthly')
                            ->required(fn (callable $get) => $get('salary_basis') === 'monthly')
                            ->helperText('Use this only if the employee is truly managed by monthly salary, not daily rate.'),

                        Select::make('salary_currency')
                            ->label('Salary Currency')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                                'LYD' => 'LYD',
                            ])
                            ->native(false)
                            ->required(),

                        TextInput::make('source_candidate_request_id')
                            ->label('Source Candidate Request ID')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(),

                        Textarea::make('salary_notes')
                            ->label('Salary Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Agreement notes, salary comments, negotiation source, or payroll remarks.'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Employment Tracking')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'on_hold' => 'On Hold',
                                'completed' => 'Completed',
                                'terminated' => 'Terminated',
                            ])
                            ->default('active')
                            ->native(false)
                            ->required(),

                        Select::make('current_work_status')
                            ->label('Current Work Status')
                            ->options([
                                'pending_mobilization' => 'Pending Mobilization',
                                'mobilized' => 'Mobilized',
                                'on_rotation' => 'On Rotation',
                                'demobilized' => 'Demobilized',
                                'on_leave' => 'On Leave',
                            ])
                            ->native(false),

                        Select::make('rotation_status')
                            ->label('Rotation Status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'paused' => 'Paused',
                            ])
                            ->native(false)
                            ->visible(fn (callable $get) => $get('employee_category') !== 'office'),

                        TextInput::make('rotation_pattern')
                            ->label('Rotation Pattern')
                            ->maxLength(255)
                            ->visible(fn (callable $get) => $get('employee_category') !== 'office'),

                        Select::make('contract_status')
                            ->label('Contract Status')
                            ->options([
                                'active' => 'Active',
                                'renewal_in_progress' => 'Renewal In Progress',
                                'renewed' => 'Renewed',
                                'completed' => 'Completed',
                                'terminated' => 'Terminated',
                            ])
                            ->native(false),

                        Select::make('contract_type')
                            ->label('Contract Type')
                            ->options([
                                'open_ended' => 'Open-ended Contract',
                                'fixed_term' => 'Fixed-term Contract',
                                'project_based' => 'Project-based Contract',
                                'consultancy' => 'Consultancy Agreement',
                                'temporary' => 'Temporary Contract',
                            ])
                            ->default('open_ended')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('is_open_ended_contract', $state === 'open_ended');

                                if ($state === 'open_ended') {
                                    $set('contract_end_date', null);
                                }
                            }),

                        Toggle::make('is_open_ended_contract')
                            ->label('Open-ended Contract')
                            ->helperText('Enable this when the contract has no fixed end date.')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('contract_type', 'open_ended');
                                    $set('contract_end_date', null);
                                }
                            }),

                        DatePicker::make('contract_start_date')
                            ->label('Contract Start Date'),

                        DatePicker::make('contract_end_date')
                            ->label('Contract End Date')
                            ->visible(fn (callable $get) => ! (bool) $get('is_open_ended_contract')),

                        Select::make('medical_status')
                            ->label('Medical Status')
                            ->options([
                                'pending' => 'Pending',
                                'fit' => 'Fit',
                                'not_fit' => 'Not Fit',
                                'expired' => 'Expired',
                                'renewed' => 'Renewed',
                            ])
                            ->native(false)
                            ->visible(fn (callable $get) => $get('employee_category') !== 'office'),

                        DatePicker::make('medical_date')
                            ->label('Medical Date')
                            ->visible(fn (callable $get) => $get('employee_category') !== 'office'),

                        DatePicker::make('medical_expiry_date')
                            ->label('Medical Expiry Date')
                            ->visible(fn (callable $get) => $get('employee_category') !== 'office'),

                        Select::make('visa_status')
                            ->label('Visa Status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'issued' => 'Issued',
                                'expired' => 'Expired',
                                'renewed' => 'Renewed',
                            ])
                            ->native(false),

                        DatePicker::make('visa_issue_date')
                            ->label('Visa Issue Date'),

                        DatePicker::make('visa_expiry_date')
                            ->label('Visa Expiry Date'),

                        Select::make('travel_status')
                            ->label('Travel Status')
                            ->options([
                                'pending_request' => 'Pending Request',
                                'request_sent' => 'Request Sent',
                                'ticket_issued' => 'Ticket Issued',
                                'travel_completed' => 'Travel Completed',
                            ])
                            ->native(false),

                        DatePicker::make('travel_request_date')
                            ->label('Travel Request Date'),

                        DatePicker::make('mobilization_date')
                            ->label('Mobilization Date'),

                        DatePicker::make('demobilization_date')
                            ->label('Demobilization Date'),

                        TextInput::make('work_location')
                            ->label('Work Location')
                            ->maxLength(255),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),


                Section::make('ERP Login Setup')
                    ->description('Visible only for Office Internal Employment. Create or update ERP admin login after saving this employee.')
                    ->visible(fn (callable $get): bool => (string) ($get('employee_category') ?? 'operational') === 'office')
                    ->schema([
                        Toggle::make('create_erp_user_after_save')
                            ->label('Create / update ERP user after saving')
                            ->default(false)
                            ->live()
                            ->dehydrated(),

                        Select::make('erp_login_role')
                            ->label('ERP Role')
                            ->options(User::erpRoleOptions())
                            ->default('viewer')
                            ->native(true)
                            ->visible(fn (callable $get): bool => (bool) $get('create_erp_user_after_save'))
                            ->required(fn (callable $get): bool => (bool) $get('create_erp_user_after_save'))
                            ->dehydrated(),

                        TextInput::make('erp_login_department')
                            ->label('ERP Department')
                            ->placeholder('admin / finance / hr / recruitment / operations')
                            ->default(fn (callable $get) => $get('office_department') ?: 'admin')
                            ->visible(fn (callable $get): bool => (bool) $get('create_erp_user_after_save'))
                            ->required(fn (callable $get): bool => (bool) $get('create_erp_user_after_save'))
                            ->maxLength(255)
                            ->dehydrated(),

                        TextInput::make('erp_login_temp_password')
                            ->label('Temporary Password')
                            ->password()
                            ->revealable()
                            ->default('password123')
                            ->minLength(8)
                            ->visible(fn (callable $get): bool => (bool) $get('create_erp_user_after_save'))
                            ->required(fn (callable $get): bool => (bool) $get('create_erp_user_after_save'))
                            ->helperText('The user can change it later from My Profile.')
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Readiness Check')
                    ->schema([
                        Placeholder::make('salary_readiness_check')
                            ->label('Salary Readiness')
                            ->content(function (callable $get, $record) {
                                $salaryBasis = $get('salary_basis') ?: $record?->salary_basis;
                                $currency = $get('salary_currency') ?: $record?->salary_currency;
                                $dailyRate = $get('daily_rate') ?: $record?->daily_rate;
                                $monthlySalary = $get('monthly_salary') ?: $record?->monthly_salary;

                                $isValid = filled($salaryBasis)
                                    && filled($currency)
                                    && (
                                        ($salaryBasis === 'daily_rate' && filled($dailyRate))
                                        || ($salaryBasis === 'monthly' && filled($monthlySalary))
                                    );

                                return $isValid
                                    ? 'Salary is configured and ready for Salary Slip generation.'
                                    : 'Salary is NOT fully configured yet. Please set salary basis, currency, and value.';
                            }),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),
            ]);
    }
}