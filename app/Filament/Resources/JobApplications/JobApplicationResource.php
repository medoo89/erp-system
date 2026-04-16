<?php

namespace App\Filament\Resources\JobApplications;

use App\Filament\Resources\JobApplications\Pages;
use App\Filament\Resources\JobApplications\Schemas\JobApplicationForm;
use App\Filament\Resources\JobApplications\Tables\JobApplicationsTable;
use App\Models\JobApplication;
use BackedEnum;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static ?string $navigationLabel = 'Job Applications';

    protected static ?string $modelLabel = 'Job Application';

    protected static ?string $pluralModelLabel = 'Job Applications';

    protected static string|\UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return JobApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('')
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Applicant Name')
                            ->weight('bold')
                            ->default('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->weight('bold')
                            ->color(fn (?string $state): string => match ($state) {
                                'new' => 'gray',
                                'screening' => 'warning',
                                'under_review' => 'warning',
                                'shortlisted' => 'info',
                                'client_submitted' => 'info',
                                'interview' => 'purple',
                                'interview_scheduled' => 'purple',
                                'approved' => 'success',
                                'qualified' => 'gray',
                                'hired' => 'success',
                                'on_hold' => 'warning',
                                'talent_pool' => 'gray',
                                'backup_pool' => 'gray',
                                'rejected', 'declined' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'new' => 'New',
                                'screening' => 'Screening',
                                'under_review' => 'Under Review',
                                'shortlisted' => 'Shortlisted',
                                'client_submitted' => 'Client Submitted',
                                'interview' => 'Interview',
                                'interview_scheduled' => 'Interview Scheduled',
                                'approved' => 'Approved',
                                'qualified' => 'Qualified',
                                'hired' => 'Hired',
                                'on_hold' => 'On Hold',
                                'talent_pool' => 'Talent Pool',
                                'backup_pool' => 'Backup Pool',
                                'rejected' => 'Rejected',
                                'declined' => 'Declined',
                                default => ucfirst(str_replace('_', ' ', (string) $state)),
                            }),

                        TextEntry::make('candidate_request_status')
                            ->label('Request Workflow')
                            ->badge()
                            ->weight('bold')
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
                            }),

                        TextEntry::make('decline_reason')
                            ->label('Decline Reason')
                            ->badge()
                            ->weight('bold')
                            ->color(fn (?string $state): string => match ($state) {
                                'internal_rejected' => 'gray',
                                'client_rejected' => 'danger',
                                'applicant_withdrew' => 'warning',
                                'applicant_refused_salary' => 'warning',
                                'applicant_refused_offer' => 'warning',
                                'applicant_refused_contract' => 'warning',
                                'no_response' => 'warning',
                                'failed_requirements' => 'danger',
                                'position_closed' => 'gray',
                                'other' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'internal_rejected' => 'Internal Rejected',
                                'client_rejected' => 'Rejected by Client',
                                'applicant_withdrew' => 'Applicant Withdrew',
                                'applicant_refused_salary' => 'Applicant Refused Salary',
                                'applicant_refused_offer' => 'Applicant Refused Offer',
                                'applicant_refused_contract' => 'Applicant Refused Contract',
                                'no_response' => 'No Response',
                                'failed_requirements' => 'Failed Requirements',
                                'position_closed' => 'Position Closed',
                                'other' => 'Other',
                                null, '' => '-',
                                default => ucfirst(str_replace('_', ' ', (string) $state)),
                            })
                            ->visible(fn (JobApplication $record): bool => $record->status === 'declined' && filled($record->decline_reason)),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                Section::make('Overview')
                    ->schema([
                        Section::make('Full Name')
                            ->schema([
                                TextEntry::make('full_name')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),
                            ]),

                        Section::make('Email')
                            ->schema([
                                TextEntry::make('email')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),
                            ]),

                        Section::make('Phone Number')
                            ->schema([
                                TextEntry::make('phone')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),
                            ]),

                        Section::make('WhatsApp Number')
                            ->schema([
                                TextEntry::make('whatsapp_number')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),
                            ]),

                        Section::make('Position')
                            ->schema([
                                TextEntry::make('job.title')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),
                            ]),

                        Section::make('Project')
                            ->schema([
                                TextEntry::make('job.project.name')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),
                            ]),

                        Section::make('Client')
                            ->schema([
                                TextEntry::make('job.project.client.name')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),
                            ]),

                        Section::make('Applied At')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->dateTime('M j, Y - H:i'),
                            ]),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),

                Section::make('Latest Status Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->default('-'),
                    ])
                    ->visible(fn (JobApplication $record): bool => filled($record->notes))
                    ->columnSpanFull(),

                Section::make('Decline Notes')
                    ->schema([
                        TextEntry::make('decline_notes')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->default('-'),
                    ])
                    ->visible(fn (JobApplication $record): bool => $record->status === 'declined' && filled($record->decline_notes))
                    ->columnSpanFull(),

                Section::make('Application Answers')
                    ->schema([
                        RepeatableEntry::make('values')
                            ->hiddenLabel()
                            ->state(function (JobApplication $record): array {
                                return $record->values()
                                    ->with('field')
                                    ->get()
                                    ->filter(function ($value) {
                                        return $value->field
                                            && $value->field->field_type !== 'file';
                                    })
                                    ->map(function ($value) {
                                        return [
                                            'field_label' => $value->field->label ?? '-',
                                            'field_value' => $value->value,
                                        ];
                                    })
                                    ->values()
                                    ->toArray();
                            })
                            ->schema([
                                TextEntry::make('field_label')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),

                                TextEntry::make('field_value')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->formatStateUsing(function ($state) {
                                        if (blank($state)) {
                                            return '-';
                                        }

                                        if (is_array($state)) {
                                            return implode(', ', $state);
                                        }

                                        return $state;
                                    }),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),

                Section::make('Applicant Documents')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Status')
                            ->formatStateUsing(fn () => 'No applicant documents uploaded from apply form.')
                            ->weight('bold')
                            ->visible(fn (JobApplication $record): bool => ! $record->values()
                                ->whereHas('field', fn ($query) => $query->where('field_type', 'file'))
                                ->whereNotNull('value')
                                ->exists()),

                        RepeatableEntry::make('applicant_documents')
                            ->hiddenLabel()
                            ->state(function (JobApplication $record): array {
                                return $record->values()
                                    ->with('field')
                                    ->get()
                                    ->filter(function ($value) {
                                        return $value->field
                                            && $value->field->field_type === 'file'
                                            && filled($value->value);
                                    })
                                    ->map(function ($value) use ($record) {
                                        $fieldLabel = $value->field->label ?? 'Document';
                                        $fieldKey = $value->field->field_key ?? null;

                                        $documentUrl = $fieldKey === 'cv_file'
                                            ? route('job-applications.open-cv', $record)
                                            : asset('storage/' . ltrim($value->value, '/'));

                                        $actionLabel = $fieldKey === 'cv_file'
                                            ? 'Open CV'
                                            : 'Open ' . $fieldLabel;

                                        return [
                                            'document_label' => $fieldLabel,
                                            'document_url' => $documentUrl,
                                            'document_action' => $actionLabel,
                                        ];
                                    })
                                    ->values()
                                    ->toArray();
                            })
                            ->schema([
                                TextEntry::make('document_label')
                                    ->hiddenLabel()
                                    ->weight('bold')
                                    ->default('-'),

                                TextEntry::make('document_url')
                                    ->hiddenLabel()
                                    ->badge()
                                    ->weight('bold')
                                    ->color('info')
                                    ->formatStateUsing(fn ($state, $record) => data_get($record, 'document_action', 'Open Document'))
                                    ->url(fn ($state) => filled($state) ? $state : null)
                                    ->openUrlInNewTab(),
                            ])
                            ->columns(2)
                            ->visible(fn (JobApplication $record): bool => $record->values()
                                ->whereHas('field', fn ($query) => $query->where('field_type', 'file'))
                                ->whereNotNull('value')
                                ->exists()),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobApplications::route('/'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}