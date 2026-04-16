<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Mail\CandidateRequestMail;
use App\Models\Job;
use App\Models\JobApplication;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn ($query) => $query
                    ->with(['job.project.client', 'values.field'])
                    ->where('is_archived', false)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job.project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('job.project.client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('years_of_experience_display')
                    ->label('Years of Experience')
                    ->getStateUsing(fn (JobApplication $record) => self::resolveYearsOfExperience($record)),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'new' => 'gray',
                        'screening' => 'warning',
                        'under_review' => 'info',
                        'client_submitted' => 'primary',
                        'approved' => 'success',
                        'qualified' => 'gray',
                        'hired' => 'success',
                        'declined' => 'danger',
                        'interview' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'new' => 'New',
                        'screening' => 'Screening',
                        'under_review' => 'Under Review',
                        'client_submitted' => 'Client Submitted',
                        'approved' => 'Approved',
                        'qualified' => 'Qualified',
                        'hired' => 'Hired',
                        'declined' => 'Declined',
                        'interview' => 'Interview',
                        default => ucfirst(str_replace('_', ' ', (string) $state)),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('candidate_request_status')
                    ->label('Request Workflow')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'awaiting_response' => 'warning',
                        'response_received' => 'info',
                        'documents_submitted' => 'success',
                        'request_completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'awaiting_response' => 'Awaiting Response',
                        'response_received' => 'Response Received',
                        'documents_submitted' => 'Documents Submitted',
                        'request_completed' => 'Request Completed',
                        null, '' => '-',
                        default => ucfirst(str_replace('_', ' ', (string) $state)),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied At')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->recordUrl(fn ($record) => JobApplicationResource::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new' => 'New',
                        'screening' => 'Screening',
                        'under_review' => 'Under Review',
                        'client_submitted' => 'Client Submitted',
                        'approved' => 'Approved',
                        'qualified' => 'Qualified',
                        'hired' => 'Hired',
                        'declined' => 'Declined',
                        'interview' => 'Interview',
                    ]),

                Tables\Filters\SelectFilter::make('candidate_request_status')
                    ->label('Request Workflow')
                    ->options([
                        'awaiting_response' => 'Awaiting Response',
                        'response_received' => 'Response Received',
                        'documents_submitted' => 'Documents Submitted',
                        'request_completed' => 'Request Completed',
                    ]),

                Tables\Filters\SelectFilter::make('job_id')
                    ->label('Position')
                    ->options(
                        Job::query()
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->toArray()
                    )
                    ->searchable(),
            ])
            ->headerActions([
                //
            ])
            ->bulkActions([
                BulkAction::make('bulk_create_candidate_request')
                    ->label('Create Request')
                    ->color('primary')
                    ->icon('heroicon-o-document-text')
                    ->form([
                        Select::make('type')
                            ->label('Request Type')
                            ->required()
                            ->options([
                                'document_request' => 'Document Request',
                                'missing_certificates' => 'Missing Certificates',
                                'passport_copy_request' => 'Passport Copy Request',
                                'experience_certificates_request' => 'Experience Certificates Request',
                                'salary_negotiation' => 'Salary Negotiation',
                                'availability_confirmation' => 'Availability Confirmation',
                                'offer_clarification' => 'Offer Clarification',
                                'general_special_request' => 'General Special Request',
                                'other' => 'Other',
                            ])
                            ->live(),

                        TextInput::make('title')
                            ->label('Request Title')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('notes')
                            ->label('Notes / Instructions')
                            ->rows(5),

                        DatePicker::make('due_date')
                            ->label('Due Date'),

                        Toggle::make('send_email')
                            ->label('Send email to candidates')
                            ->default(true),

                        Placeholder::make('request_items_help')
                            ->label('Request Items Info')
                            ->content('For salary negotiation, request items are optional. You can send salary only, or salary + files/notes in the same request.'),

                        Repeater::make('request_items')
                            ->label('Request Items')
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->addActionLabel('Add Another Request')
                            ->schema([
                                Select::make('item_type')
                                    ->label('Item Type')
                                    ->required()
                                    ->default('file')
                                    ->options([
                                        'file' => 'File Upload',
                                        'note' => 'Information / Note',
                                    ])
                                    ->live(),

                                TextInput::make('label')
                                    ->label('Item Title / Label')
                                    ->required()
                                    ->placeholder('Example: ATEX Certificate or Salary Expectation'),

                                Select::make('file_format')
                                    ->label('File Format')
                                    ->options([
                                        'pdf' => 'PDF',
                                        'image' => 'Image',
                                        'pdf_or_image' => 'PDF or Image',
                                        'document' => 'Document',
                                        'other' => 'Other',
                                    ])
                                    ->visible(fn (callable $get) => ($get('item_type') ?? 'file') === 'file'),

                                Toggle::make('is_required')
                                    ->label('Required')
                                    ->default(true),

                                Toggle::make('allow_multiple')
                                    ->label('Allow Multiple Uploads')
                                    ->default(false)
                                    ->visible(fn (callable $get) => ($get('item_type') ?? 'file') === 'file'),

                                Textarea::make('notes')
                                    ->label('Item Notes')
                                    ->rows(3),
                            ])
                            ->columns(2),

                        Placeholder::make('salary_internal_note')
                            ->label('Salary Negotiation')
                            ->content('Use these fields when you want negotiation only, or negotiation together with request items.')
                            ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),

                        TextInput::make('proposed_salary')
                            ->label('Proposed Salary')
                            ->numeric()
                            ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),

                        TextInput::make('currency')
                            ->label('Currency')
                            ->default('USD')
                            ->maxLength(10)
                            ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),

                        Toggle::make('requires_approval')
                            ->label('Requires Candidate Approval')
                            ->default(true)
                            ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Create Candidate Request for Selected Applicants')
                    ->modalSubmitActionLabel('Create Requests')
                    ->action(function (Collection $records, array $data): void {
                        $createdCount = 0;
                        $emailFailures = 0;

                        $requestItems = collect($data['request_items'] ?? [])
                            ->filter(function ($item) {
                                return filled($item['label'] ?? null);
                            })
                            ->values()
                            ->all();

                        $isSalaryNegotiation = ($data['type'] ?? null) === 'salary_negotiation';
                        $hasSalaryValue = filled($data['proposed_salary'] ?? null);
                        $hasRequestItems = count($requestItems) > 0;

                        if ($isSalaryNegotiation && ! $hasSalaryValue && ! $hasRequestItems) {
                            Notification::make()
                                ->title('For salary negotiation, add a proposed salary or at least one request item.')
                                ->danger()
                                ->send();

                            return;
                        }

                        if (! $isSalaryNegotiation && ! $hasRequestItems) {
                            Notification::make()
                                ->title('Please add at least one request item for this request type.')
                                ->danger()
                                ->send();

                            return;
                        }

                        foreach ($records as $record) {
                            try {
                                $hasFileItems = collect($requestItems)
                                    ->contains(fn ($item) => ($item['item_type'] ?? 'file') === 'file');

                                $request = $record->candidateRequests()->create([
                                    'type' => $data['type'],
                                    'title' => $data['title'],
                                    'notes' => $data['notes'] ?? null,
                                    'request_status' => 'pending',
                                    'due_date' => $data['due_date'] ?? null,
                                    'requires_upload' => $hasFileItems,
                                    'proposed_salary' => $data['proposed_salary'] ?? null,
                                    'currency' => $data['currency'] ?? null,
                                    'requires_approval' => (bool) ($data['requires_approval'] ?? false),
                                    'created_by' => Auth::id(),
                                    'public_token' => (string) Str::uuid(),
                                ]);

                                foreach ($requestItems as $index => $item) {
                                    $request->items()->create([
                                        'item_type' => $item['item_type'] ?? 'file',
                                        'label' => $item['label'],
                                        'file_format' => ($item['item_type'] ?? 'file') === 'file'
                                            ? ($item['file_format'] ?? null)
                                            : null,
                                        'is_required' => (bool) ($item['is_required'] ?? false),
                                        'allow_multiple' => (bool) ($item['allow_multiple'] ?? false),
                                        'notes' => $item['notes'] ?? null,
                                        'sort_order' => $index + 1,
                                    ]);
                                }

                                $record->update([
                                    'candidate_request_status' => 'awaiting_response',
                                ]);

                                if ((bool) ($data['send_email'] ?? false) && filled($record->email)) {
                                    try {
                                        $portalUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $request->public_token;

                                        Mail::to($record->email)->send(
                                            new CandidateRequestMail(
                                                $request->load('items', 'jobApplication.job'),
                                                $portalUrl
                                            )
                                        );
                                    } catch (\Throwable $e) {
                                        $emailFailures++;

                                        Log::error('Bulk candidate request email send failed', [
                                            'job_application_id' => $record->id,
                                            'candidate_request_id' => $request->id,
                                            'email' => $record->email,
                                            'message' => $e->getMessage(),
                                        ]);
                                    }
                                }

                                $createdCount++;
                            } catch (\Throwable $e) {
                                Log::error('Bulk candidate request create failed', [
                                    'job_application_id' => $record->id,
                                    'message' => $e->getMessage(),
                                ]);
                            }
                        }

                        if ($createdCount > 0) {
                            $message = "{$createdCount} request(s) created successfully";

                            if ($emailFailures > 0) {
                                $message .= " ({$emailFailures} email(s) failed)";
                            }

                            Notification::make()
                                ->title($message)
                                ->success()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('No requests were created')
                            ->danger()
                            ->send();
                    }),

                BulkAction::make('export_selected_csv')
                    ->label('Export Selected CSV')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records): StreamedResponse {
                        $query = JobApplication::query()
                            ->with(['job.project.client', 'values.field'])
                            ->whereIn('id', $records->pluck('id'));

                        return self::streamCsvDownload(
                            $query,
                            'job_applications_selected_' . now()->format('Y_m_d_H_i_s') . '.csv'
                        );
                    }),

                BulkAction::make('bulk_screening')
                    ->label('Move to Screening')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update([
                            'status' => 'screening',
                        ]);

                        Notification::make()
                            ->title('Selected applications moved to Screening')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_under_review')
                    ->label('Move to Under Review')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update([
                            'status' => 'under_review',
                        ]);

                        Notification::make()
                            ->title('Selected applications moved to Under Review')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_client_submitted')
                    ->label('Move to Client Submitted')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update([
                            'status' => 'client_submitted',
                        ]);

                        Notification::make()
                            ->title('Selected applications moved to Client Submitted')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_qualified')
                    ->label('Move to Qualified')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update([
                            'status' => 'qualified',
                        ]);

                        Notification::make()
                            ->title('Selected applications moved to Qualified')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_hired')
                    ->label('Move to Hired')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->update([
                            'status' => 'hired',
                        ]);

                        Notification::make()
                            ->title('Selected applications moved to Hired')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_declined')
                    ->label('Decline and Archive')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'status' => 'declined',
                                'is_archived' => true,
                                'archive_reason' => 'declined',
                                'archived_at' => now(),
                            ]);
                        }

                        Notification::make()
                            ->title('Selected applications declined and archived')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_archive')
                    ->label('Archive')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'is_archived' => true,
                                'archive_reason' => $record->archive_reason ?: 'archived_manually',
                                'archived_at' => now(),
                            ]);
                        }

                        Notification::make()
                            ->title('Selected applications archived')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_delete')
                    ->label('Permanent Delete')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->delete();

                        Notification::make()
                            ->title('Selected applications permanently deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([10, 25, 50, 100, 150, 200]);
    }

    protected static function resolveYearsOfExperience(JobApplication $record): string
    {
        if (filled($record->years_of_experience ?? null)) {
            return (string) $record->years_of_experience;
        }

        $record->loadMissing(['values.field']);

        $experienceValue = $record->values->first(function ($value) {
            $fieldKey = strtolower((string) ($value->field->field_key ?? ''));
            $fieldLabel = strtolower((string) ($value->field->label ?? ''));

            return str_contains($fieldKey, 'experience')
                || str_contains($fieldKey, 'year')
                || str_contains($fieldLabel, 'experience')
                || str_contains($fieldLabel, 'year')
                || str_contains($fieldLabel, 'years of experience')
                || str_contains($fieldLabel, 'عدد سنوات الخبرة')
                || str_contains($fieldLabel, 'سنوات الخبرة');
        });

        return filled($experienceValue?->value) ? (string) $experienceValue->value : '-';
    }

    protected static function streamCsvDownload($query, string $fileName): StreamedResponse
    {
        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            $baseHeaders = [
                'Full Name',
                'Email',
                'Phone',
                'WhatsApp',
                'Position',
                'Project',
                'Client',
                'Status',
                'Request Workflow',
                'Years of Experience',
                'Applied At',
                'CV Link',
            ];

            $dynamicFieldHeaders = [];

            $firstApplication = (clone $query)->first();

            if ($firstApplication) {
                $dynamicFieldHeaders = $firstApplication->values
                    ->map(fn ($value) => $value->field->label ?? null)
                    ->filter()
                    ->values()
                    ->unique()
                    ->toArray();
            }

            fputcsv($handle, array_merge($baseHeaders, $dynamicFieldHeaders));

            $query->chunk(200, function ($applications) use ($handle, $dynamicFieldHeaders) {
                foreach ($applications as $application) {
                    $dynamicValues = [];

                    foreach ($dynamicFieldHeaders as $headerLabel) {
                        $valueRow = $application->values->first(function ($value) use ($headerLabel) {
                            return ($value->field->label ?? null) === $headerLabel;
                        });

                        $fieldType = $valueRow->field->field_type ?? null;
                        $fieldKey = $valueRow->field->field_key ?? null;
                        $rawValue = $valueRow->value ?? '';

                        if ($fieldType === 'file' && filled($rawValue)) {
                            if ($fieldKey === 'cv_file') {
                                $dynamicValues[] = route('job-applications.open-cv', $application);
                            } else {
                                $dynamicValues[] = asset('storage/' . ltrim($rawValue, '/'));
                            }
                        } else {
                            $dynamicValues[] = $rawValue;
                        }
                    }

                    $cvValue = $application->values->first(function ($value) {
                        return ($value->field->field_key ?? null) === 'cv_file' && filled($value->value);
                    });

                    $cvLink = filled($application->cv_path)
                        ? route('job-applications.open-cv', $application)
                        : (filled($cvValue?->value) ? route('job-applications.open-cv', $application) : '');

                    fputcsv($handle, array_merge([
                        $application->full_name,
                        $application->email,
                        $application->phone,
                        $application->whatsapp_number,
                        optional($application->job)->title,
                        optional($application->job?->project)->name,
                        optional($application->job?->project?->client)->name,
                        $application->status,
                        $application->candidate_request_status,
                        self::resolveYearsOfExperience($application),
                        optional($application->created_at)?->format('Y-m-d H:i:s'),
                        $cvLink,
                    ], $dynamicValues));
                }
            });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}