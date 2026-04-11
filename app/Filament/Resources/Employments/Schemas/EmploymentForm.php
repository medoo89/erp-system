<?php

namespace App\Filament\Resources\Employments\Schemas;

use App\Models\User;
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
                            ->maxLength(255),

                        TextInput::make('position_title')
                            ->label('Position')
                            ->maxLength(255),

                        TextInput::make('client_name')
                            ->label('Client')
                            ->maxLength(255),

                        TextInput::make('project_name')
                            ->label('Project')
                            ->maxLength(255),

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
                            ->native(false),

                        TextInput::make('operation_officer_name')
                            ->label('Operation Officer Name')
                            ->maxLength(255)
                            ->helperText('Stored text snapshot from conversion or can be adjusted manually.'),
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