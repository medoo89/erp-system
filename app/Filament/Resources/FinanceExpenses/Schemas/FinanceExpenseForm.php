<?php

namespace App\Filament\Resources\FinanceExpenses\Schemas;

use App\Models\CandidateFinanceProfile;
use App\Models\Client;
use App\Models\Employment;
use App\Models\EmploymentRotation;
use App\Models\FinanceExpense;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\PreEmployment;
use App\Models\Project;
use App\Models\TreasuryAccount;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Schema as DatabaseSchema;

class FinanceExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Expense Scope & Ownership')
                    ->schema([
                        Select::make('expense_scope')
                            ->label('Expense Scope')
                            ->options([
                                FinanceExpense::SCOPE_PRE_HIRE => 'Pre-Hire',
                                FinanceExpense::SCOPE_EMPLOYMENT => 'Employment',
                                FinanceExpense::SCOPE_ROTATION => 'Rotation',
                                FinanceExpense::SCOPE_AD_HOC => 'Ad Hoc',
                            ])
                            ->required()
                            ->default(FinanceExpense::SCOPE_AD_HOC)
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state === FinanceExpense::SCOPE_AD_HOC) {
                                    $set('job_application_id', null);
                                    $set('pre_employment_id', null);
                                    $set('employment_id', null);
                                    $set('employment_rotation_id', null);
                                    $set('candidate_finance_profile_id', null);
                                }

                                if ($state === FinanceExpense::SCOPE_PRE_HIRE) {
                                    $set('employment_id', null);
                                    $set('employment_rotation_id', null);
                                }

                                if ($state === FinanceExpense::SCOPE_EMPLOYMENT) {
                                    $set('employment_rotation_id', null);
                                }
                            }),

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
                            ->native(false)
                            ->visible(fn ($get) => in_array($get('expense_scope'), [
                                FinanceExpense::SCOPE_PRE_HIRE,
                                FinanceExpense::SCOPE_AD_HOC,
                            ], true))
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (! $state) {
                                    return;
                                }

                                $application = JobApplication::query()->find($state);

                                if (! $application) {
                                    return;
                                }

                                $set('job_id', $application->job_id);

                                if ($get('expense_scope') === FinanceExpense::SCOPE_AD_HOC) {
                                    $job = Job::query()->with(['project.client'])->find($application->job_id);

                                    $set('project_id', $job?->project?->id);
                                    $set('client_id', $job?->project?->client?->id);
                                }
                            }),

                        Select::make('pre_employment_id')
                            ->label('Pre-Employment')
                            ->options(function () {
                                return PreEmployment::query()
                                    ->orderBy('candidate_name')
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [
                                        $item->id => ($item->candidate_name ?: 'Unknown Candidate') . ' #' . $item->id,
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn ($get) => $get('expense_scope') === FinanceExpense::SCOPE_PRE_HIRE)
                            ->required(fn ($get) => $get('expense_scope') === FinanceExpense::SCOPE_PRE_HIRE)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }

                                $preEmployment = PreEmployment::query()
                                    ->with(['job.project.client', 'currentFinanceProfile'])
                                    ->find($state);

                                if (! $preEmployment) {
                                    return;
                                }

                                $set('job_application_id', $preEmployment->job_application_id);
                                $set('job_id', $preEmployment->job_id);
                                $set('project_id', $preEmployment->job?->project?->id);
                                $set('client_id', $preEmployment->job?->project?->client?->id);
                                $set('candidate_finance_profile_id', $preEmployment->currentFinanceProfile?->id);
                            }),

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
                            ->visible(fn ($get) => in_array($get('expense_scope'), [
                                FinanceExpense::SCOPE_EMPLOYMENT,
                                FinanceExpense::SCOPE_ROTATION,
                            ], true))
                            ->required(fn ($get) => in_array($get('expense_scope'), [
                                FinanceExpense::SCOPE_EMPLOYMENT,
                                FinanceExpense::SCOPE_ROTATION,
                            ], true))
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (! $state) {
                                    return;
                                }

                                $employment = Employment::query()
                                    ->with(['job.project.client', 'preEmployment', 'currentFinanceProfile'])
                                    ->find($state);

                                if (! $employment) {
                                    return;
                                }

                                $set('pre_employment_id', $employment->pre_employment_id);
                                $set('job_application_id', $employment->preEmployment?->job_application_id);
                                $set('job_id', $employment->job_id);
                                $set('project_id', $employment->job?->project?->id);
                                $set('client_id', $employment->job?->project?->client?->id);
                                $set('candidate_finance_profile_id', $employment->currentFinanceProfile?->id);

                                if ($get('expense_scope') !== FinanceExpense::SCOPE_ROTATION) {
                                    $set('employment_rotation_id', null);
                                }
                            }),

                        Select::make('employment_rotation_id')
                            ->label('Rotation')
                            ->options(function ($get) {
                                $employmentId = $get('employment_id');

                                $query = EmploymentRotation::query()
                                    ->with('employment')
                                    ->orderByDesc('from_date');

                                if ($employmentId) {
                                    $query->where('employment_id', $employmentId);
                                }

                                return $query
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        $label = 'Rotation #' . $item->id;

                                        if ($item->employment?->employee_name) {
                                            $label = $item->employment->employee_name . ' — ' . $label;
                                        }

                                        if ($item->from_date || $item->to_date) {
                                            $label .= ' (' . optional($item->from_date)->format('Y-m-d') . ' → ' . optional($item->to_date)->format('Y-m-d') . ')';
                                        }

                                        return [$item->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn ($get) => $get('expense_scope') === FinanceExpense::SCOPE_ROTATION)
                            ->required(fn ($get) => $get('expense_scope') === FinanceExpense::SCOPE_ROTATION)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }

                                $rotation = EmploymentRotation::query()
                                    ->with(['employment.job.project.client', 'employment.preEmployment', 'employment.currentFinanceProfile'])
                                    ->find($state);

                                if (! $rotation || ! $rotation->employment) {
                                    return;
                                }

                                $employment = $rotation->employment;

                                $set('employment_id', $employment->id);
                                $set('pre_employment_id', $employment->pre_employment_id);
                                $set('job_application_id', $employment->preEmployment?->job_application_id);
                                $set('job_id', $employment->job_id);
                                $set('project_id', $employment->job?->project?->id);
                                $set('client_id', $employment->job?->project?->client?->id);
                                $set('candidate_finance_profile_id', $employment->currentFinanceProfile?->id);
                            }),

                        Select::make('job_id')
                            ->label('Job')
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
                            ->visible(fn ($get) => $get('expense_scope') === FinanceExpense::SCOPE_AD_HOC)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }

                                $job = Job::query()->with(['project.client'])->find($state);

                                if (! $job) {
                                    return;
                                }

                                $set('project_id', $job->project?->id);
                                $set('client_id', $job->project?->client?->id);
                            }),

                        Select::make('client_id')
                            ->label('Client')
                            ->options(Client::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn ($get) => $get('expense_scope') === FinanceExpense::SCOPE_AD_HOC),

                        Select::make('project_id')
                            ->label('Project')
                            ->options(Project::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn ($get) => $get('expense_scope') === FinanceExpense::SCOPE_AD_HOC),

                        Select::make('candidate_finance_profile_id')
                            ->label('Finance Profile')
                            ->options(function () {
                                return CandidateFinanceProfile::query()
                                    ->orderByDesc('id')
                                    ->get()
                                    ->mapWithKeys(fn ($item) => [
                                        $item->id => 'Profile #' . $item->id,
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false),
                    ]),

                Section::make('Expense Details')
                    ->schema([
                        Select::make('category')
                            ->label('Category')
                            ->options(FinanceExpense::categoryLabels())
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('is_travel_expense', in_array($state, [
                                    FinanceExpense::CATEGORY_TICKET,
                                    FinanceExpense::CATEGORY_TRANSPORT,
                                    FinanceExpense::CATEGORY_HOTEL,
                                    FinanceExpense::CATEGORY_ACCOMMODATION,
                                ], true));
                            }),

                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ((bool) $get('reimbursement_required') && blank($get('reimbursement_amount'))) {
                                    $set('reimbursement_amount', $state);
                                }
                            }),

                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                                'LYD' => 'LYD',
                            ])
                            ->required()
                            ->default('USD')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ((bool) $get('reimbursement_required') && blank($get('reimbursement_currency'))) {
                                    $set('reimbursement_currency', $state ?: 'USD');
                                }
                            }),

                        DatePicker::make('expense_date')
                            ->label(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Booking / Expense Date',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Ticket / Travel Date',
                                FinanceExpense::CATEGORY_VISA => 'Application / Expense Date',
                                FinanceExpense::CATEGORY_MEDICAL => 'Medical Visit Date',
                                FinanceExpense::CATEGORY_TRAINING => 'Training / Certificate Date',
                                default => 'Expense Date',
                            })
                            ->helperText(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Main record date. Use check-in and check-out below for the covered period.',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Main ticket date. Use departure and return below when applicable.',
                                FinanceExpense::CATEGORY_VISA => 'Main visa/application date. Use issue/expiry period below when applicable.',
                                FinanceExpense::CATEGORY_MEDICAL => 'Main medical expense or appointment date.',
                                FinanceExpense::CATEGORY_TRAINING => 'Main training/certificate expense date.',
                                default => 'Main expense date.',
                            })
                            ->required()
                            ->default(now()),

                        DatePicker::make('incurred_from')
                            ->label(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Check-in Date',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Departure Date',
                                FinanceExpense::CATEGORY_VISA => 'Issue / Start Date',
                                FinanceExpense::CATEGORY_MEDICAL => 'Medical Date',
                                FinanceExpense::CATEGORY_TRAINING => 'Training Start Date',
                                FinanceExpense::CATEGORY_DESERT_PASS => 'Pass Start Date',
                                default => 'Covered From',
                            })
                            ->helperText(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Used later for hotel stay calendar.',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Used later for travel calendar.',
                                FinanceExpense::CATEGORY_VISA => 'Used later for visa validity tracking.',
                                FinanceExpense::CATEGORY_TRAINING => 'Used later for training/certificate calendar.',
                                default => 'Optional covered period start.',
                            })
                            ->native(false),

                        DatePicker::make('incurred_to')
                            ->label(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Check-out Date',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Return Date',
                                FinanceExpense::CATEGORY_VISA => 'Expiry / End Date',
                                FinanceExpense::CATEGORY_MEDICAL => 'Follow-up / End Date',
                                FinanceExpense::CATEGORY_TRAINING => 'Training End / Expiry Date',
                                FinanceExpense::CATEGORY_DESERT_PASS => 'Pass Expiry Date',
                                default => 'Covered To',
                            })
                            ->helperText(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Leave empty for one-way ticket.',
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Used later for hotel stay calendar.',
                                FinanceExpense::CATEGORY_VISA,
                                FinanceExpense::CATEGORY_DESERT_PASS => 'Used later for expiry reminders.',
                                default => 'Optional covered period end.',
                            })
                            ->native(false),

                        Toggle::make('is_first_mobilization')
                            ->label('First Mobilization')
                            ->default(false),

                        Textarea::make('description')
                            ->label(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Hotel / Stay Details',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Trip / Route Details',
                                FinanceExpense::CATEGORY_VISA => 'Visa Details',
                                FinanceExpense::CATEGORY_MEDICAL => 'Medical Details',
                                FinanceExpense::CATEGORY_TRAINING => 'Training / Certificate Details',
                                default => 'Description',
                            })
                            ->placeholder(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Example: Hotel name, city, room nights, booking reference...',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Example: Trip route, one-way/round-trip, airline, ticket reference...',
                                FinanceExpense::CATEGORY_VISA => 'Example: Visa type, embassy/agency, application reference...',
                                FinanceExpense::CATEGORY_MEDICAL => 'Example: Clinic, medical test, result or appointment note...',
                                FinanceExpense::CATEGORY_TRAINING => 'Example: Training course, provider, certificate validity...',
                                default => 'Write clear expense details.',
                            })
                            ->rows(4),
                    ]),

                Section::make('Treasury Posting & Allocation')
                    ->schema([
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
                            ->helperText('Required when status is Paid.'),

                        TextInput::make('vendor_name')
                            ->label(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Hotel / Booking Supplier',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Airline / Travel Agency',
                                FinanceExpense::CATEGORY_VISA => 'Embassy / Visa Agency',
                                FinanceExpense::CATEGORY_MEDICAL => 'Clinic / Hospital / Lab',
                                FinanceExpense::CATEGORY_TRAINING => 'Training Provider',
                                default => 'Vendor / Supplier',
                            })
                            ->placeholder(fn ($get) => match ((string) $get('category')) {
                                FinanceExpense::CATEGORY_HOTEL,
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Hotel name or booking supplier',
                                FinanceExpense::CATEGORY_TICKET,
                                FinanceExpense::CATEGORY_TRANSPORT => 'Airline or travel agency',
                                FinanceExpense::CATEGORY_VISA => 'Embassy, visa office, or agency',
                                FinanceExpense::CATEGORY_MEDICAL => 'Clinic, hospital, or lab',
                                FinanceExpense::CATEGORY_TRAINING => 'Training provider',
                                default => 'Vendor / supplier name',
                            })
                            ->maxLength(255),

                        Select::make('allocation_status')
                            ->label('Allocation Status')
                            ->options(FinanceExpense::allocationLabels())
                            ->default(FinanceExpense::ALLOCATION_UNALLOCATED)
                            ->native(false),

                        Toggle::make('is_travel_expense')
                            ->label('Travel Expense')
                            ->default(false),

                        Toggle::make('is_company_expense')
                            ->label('Company / Office Expense')
                            ->default(false),

                        Toggle::make('is_manual_expense')
                            ->label('Manual Expense')
                            ->default(false),
                    ]),

                Section::make('Payment, Approval & Attachment')
                    ->schema([
                        Select::make('paid_by')
                            ->label('Paid By')
                            ->options(FinanceExpense::paidByLabels())
                            ->required()
                            ->default(FinanceExpense::PAID_BY_COMPANY)
                            ->native(false)
                            ->live()
                            ->helperText('If paid by Candidate / Employee, this becomes a reimbursement claim and stays unpaid until approved/paid.')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state === FinanceExpense::PAID_BY_CANDIDATE) {
                                    $set('reimbursement_required', true);
                                    $set('reimbursement_status', FinanceExpense::REIMBURSEMENT_PENDING);
                                    $set('reimbursement_amount', $get('amount'));
                                    $set('reimbursement_currency', $get('currency') ?: 'USD');
                                    $set('is_company_expense', false);
                                    return;
                                }

                                $set('reimbursement_required', false);
                                $set('reimbursement_status', FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE);

                                if ($state === FinanceExpense::PAID_BY_COMPANY) {
                                    $set('is_company_expense', true);
                                }
                            }),

                        Toggle::make('reimbursement_required')
                            ->label('Reimbursement Required')
                            ->default(false)
                            ->live()
                            ->helperText('Automatically enabled when the candidate/employee paid from pocket.'),

                        Select::make('reimbursement_status')
                            ->label('Reimbursement Status')
                            ->options(FinanceExpense::reimbursementLabels())
                            ->required()
                            ->default(FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE)
                            ->native(false)
                            ->helperText('Pending/Approved/Paid applies only to Candidate / Employee paid expenses.'),

                        TextInput::make('reimbursement_amount')
                            ->label('Reimbursement Amount')
                            ->numeric()
                            ->visible(fn ($get) => (bool) $get('reimbursement_required'))
                            ->default(fn ($get) => $get('amount')),

                        Select::make('reimbursement_currency')
                            ->label('Reimbursement Currency')
                            ->options([
                                'USD' => 'USD',
                                'EUR' => 'EUR',
                                'GBP' => 'GBP',
                                'LYD' => 'LYD',
                            ])
                            ->native(false)
                            ->visible(fn ($get) => (bool) $get('reimbursement_required'))
                            ->default(fn ($get) => $get('currency') ?: 'USD'),

                        Textarea::make('reimbursement_notes')
                            ->label('Reimbursement Notes')
                            ->rows(3)
                            ->visible(fn ($get) => (bool) $get('reimbursement_required')),

                        Select::make('status')
                            ->label('Status')
                            ->options(FinanceExpense::statusLabels())
                            ->required()
                            ->default(FinanceExpense::STATUS_DRAFT)
                            ->native(false)
                            ->live()
                            ->dehydrated(true),

                        Select::make('created_by')
                            ->label('Created By')
                            ->options(User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->default(fn () => auth()->id())
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Select::make('approved_by')
                            ->label('Approved By')
                            ->options(User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->native(false),

                        FileUpload::make('attachment_path')
                            ->label('Attachment')
                            ->directory('finance-expenses')
                            ->visibility('private')
                            ->downloadable()
                            ->openable()
                            ->previewable(false)
                            ->helperText('Optional, but recommended for ticket, hotel, transport, visa, medical, and reimbursement expenses.'),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(4),
                    ]),
            ]);
    }
}
