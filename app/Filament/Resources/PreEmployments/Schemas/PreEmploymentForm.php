<?php

namespace App\Filament\Resources\PreEmployments\Schemas;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PreEmploymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Candidate & Position')
                    ->schema([
                        Select::make('job_application_id')
                            ->label('Job Application')
                            ->options(
                                JobApplication::query()
                                    ->with(['job.project.client'])
                                    ->orderByDesc('id')
                                    ->get()
                                    ->mapWithKeys(function (JobApplication $application) {
                                        $position = $application->job?->title ?: '-';
                                        $project = $application->job?->project?->name ?: null;
                                        $client = $application->job?->project?->client?->name ?: null;

                                        $label = '#' . $application->id . ' - ' . $application->full_name;

                                        if ($position) {
                                            $label .= ' / ' . $position;
                                        }

                                        if ($client || $project) {
                                            $label .= ' / ' . trim(($client ? $client . ' / ' : '') . ($project ?: ''), ' /');
                                        }

                                        return [$application->id => $label];
                                    })
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }

                                $application = JobApplication::with(['job.project.client'])->find($state);

                                if (! $application) {
                                    return;
                                }

                                $set('job_id', $application->job_id);
                                $set('candidate_name', $application->full_name);
                                $set('candidate_email', $application->email);
                                $set('candidate_phone', $application->phone ?: $application->whatsapp_number);
                            }),

                        Select::make('job_id')
                            ->label('Position')
                            ->options(
                                Job::query()
                                    ->with(['project.client'])
                                    ->orderBy('title')
                                    ->get()
                                    ->mapWithKeys(function (Job $job) {
                                        $label = $job->title;

                                        if ($job->project?->name) {
                                            $label .= ' / ' . $job->project->name;
                                        }

                                        if ($job->project?->client?->name) {
                                            $label = $job->project->client->name . ' / ' . $label;
                                        }

                                        return [$job->id => $label];
                                    })
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload(),

                        TextInput::make('candidate_name')
                            ->label('Candidate Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('candidate_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('candidate_phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Process Overview')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->default('initiated')
                            ->options([
                                'initiated' => 'Initiated',
                                'under_preparation' => 'Under Preparation',
                                'awaiting_candidate_upload' => 'Awaiting Candidate Upload',
                                'documents_under_review' => 'Documents Under Review',
                                'additional_documents_required' => 'Additional Documents Required',
                                'pending_medical' => 'Pending Medical',
                                'pending_visa' => 'Pending Visa',
                                'pending_travel' => 'Pending Travel',
                                'ready_for_employment' => 'Ready for Employment',
                                'converted_to_employment' => 'Converted to Employment',
                                'declined' => 'Declined',
                                'archived' => 'Archived',
                            ])
                            ->native(false),

                        Select::make('assigned_hr_user_id')
                            ->label('Operation Officer')
                            ->options(
                                User::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload(),

                        Placeholder::make('portal_token_display')
                            ->label('Candidate Portal Token')
                            ->content(fn ($record) => $record?->portal_token ?: 'Will be generated automatically after save'),
                    ])
                    ->columns(2),

                Section::make('Commercial & Process Details')
                    ->schema([
                        TextInput::make('expected_rate')
                            ->label('Expected Rate / Salary')
                            ->maxLength(255),

                        TextInput::make('final_rate')
                            ->label('Final Approved Rate / Salary')
                            ->maxLength(255),

                        DatePicker::make('availability_date')
                            ->label('Availability Date'),

                        Select::make('contract_status')
                            ->label('Contract Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'under_discussion' => 'Under Discussion',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                'signed' => 'Signed',
                            ])
                            ->native(false),

                        Select::make('medical_status')
                            ->label('Medical Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'pending' => 'Pending',
                                'fit' => 'Fit',
                                'not_fit' => 'Not Fit',
                            ])
                            ->native(false),

                        Select::make('visa_status')
                            ->label('Visa Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'expired' => 'Expired',
                            ])
                            ->native(false),

                        Select::make('travel_status')
                            ->label('Travel Status')
                            ->options([
                                'not_started' => 'Not Started',
                                'pending' => 'Pending',
                                'booked' => 'Booked',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsed(),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Candidate / Process Notes')
                            ->rows(4)
                            ->columnSpanFull(),

                        Textarea::make('internal_notes')
                            ->label('Internal Notes')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Section::make('Decline & Archive')
                    ->schema([
                        Toggle::make('is_declined')
                            ->label('Declined')
                            ->live(),

                        Select::make('decline_reason')
                            ->label('Decline Reason')
                            ->options([
                                'internal_rejected' => 'Internal Rejected',
                                'client_rejected' => 'Rejected by Client',
                                'candidate_withdrew' => 'Candidate Withdrew',
                                'candidate_unresponsive' => 'Candidate Unresponsive',
                                'failed_documentation' => 'Failed Documentation',
                                'failed_medical' => 'Failed Medical',
                                'visa_rejected' => 'Visa Rejected',
                                'travel_issue' => 'Travel Issue',
                                'salary_not_agreed' => 'Salary Not Agreed',
                                'contract_not_agreed' => 'Contract Not Agreed',
                                'position_closed' => 'Position Closed',
                                'other' => 'Other',
                            ])
                            ->native(false)
                            ->visible(fn (callable $get) => (bool) $get('is_declined')),

                        Textarea::make('decline_notes')
                            ->label('Decline Notes')
                            ->rows(4)
                            ->columnSpanFull()
                            ->visible(fn (callable $get) => (bool) $get('is_declined')),

                        Toggle::make('is_archived')
                            ->label('Archived')
                            ->live(),

                        TextInput::make('archive_reason')
                            ->label('Archive Reason')
                            ->maxLength(255)
                            ->visible(fn (callable $get) => (bool) $get('is_archived')),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}