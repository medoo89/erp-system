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
                            ->helperText('Generated automatically from Sada Fezzan + Client Code + Project Code + Sequence.'),

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
                            ->required(fn ($record) => blank($record))
                            ->visible(fn ($record) => blank($record))
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
                            ->readOnly()
                            ->dehydrated(),

                        TextInput::make('client_name')
                            ->label('Client')
                            ->readOnly()
                            ->dehydrated(),

                        TextInput::make('project_name')
                            ->label('Project')
                            ->readOnly()
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
                            ->native(false),

                        TextInput::make('rotation_pattern')
                            ->label('Rotation Pattern')
                            ->maxLength(255),

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

                        DatePicker::make('contract_start_date')
                            ->label('Contract Start Date'),

                        DatePicker::make('contract_end_date')
                            ->label('Contract End Date'),

                        Select::make('medical_status')
                            ->label('Medical Status')
                            ->options([
                                'pending' => 'Pending',
                                'fit' => 'Fit',
                                'not_fit' => 'Not Fit',
                                'expired' => 'Expired',
                                'renewed' => 'Renewed',
                            ])
                            ->native(false),

                        DatePicker::make('medical_date')
                            ->label('Medical Date'),

                        DatePicker::make('medical_expiry_date')
                            ->label('Medical Expiry Date'),

                        Select::make('visa_status')
                            ->label('Visa Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'expired' => 'Expired',
                                'renewed' => 'Renewed',
                                'rejected' => 'Rejected',
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
                                'request_received' => 'Request Received',
                                'ticket_booked' => 'Ticket Booked',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
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

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Operations Notes')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(6)
                            ->columnSpanFull(),

                        Placeholder::make('converted_from_pre_employment_at_display')
                            ->label('Converted From Pre-Employment')
                            ->content(fn ($record) => $record?->converted_from_pre_employment_at?->format('M j, Y H:i') ?: '-'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}