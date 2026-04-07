<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Models\Job;
use App\Models\JobApplication;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['job', 'values.field'])->where('is_archived', false))
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable(),

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

                Tables\Filters\SelectFilter::make('job_id')
                    ->label('Job')
                    ->options(
                        Job::query()
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->toArray()
                    )
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->headerActions([
                // intentionally empty
            ])
            ->bulkActions([
                BulkAction::make('export_selected_csv')
                    ->label('Export Selected CSV')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records): StreamedResponse {
                        $query = JobApplication::query()
                            ->with(['job', 'values.field'])
                            ->whereIn('id', $records->pluck('id'));

                        return self::streamCsvDownload($query, 'job_applications_selected_' . now()->format('Y_m_d_H_i_s') . '.csv');
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
                'Job',
                'Status',
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
                        $application->status,
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