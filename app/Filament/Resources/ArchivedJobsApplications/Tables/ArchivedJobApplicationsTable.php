<?php

namespace App\Filament\Resources\ArchivedJobApplications\Tables;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Models\Job;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ArchivedJobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['job', 'values.field'])
                ->where('is_archived', true)
                ->where(function (Builder $subQuery) {
                    $subQuery
                        ->where('archive_reason', 'declined')
                        ->orWhere('archive_reason', 'archived_manually')
                        ->orWhereNull('archive_reason');
                }))
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Candidate')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('decline_reason_display')
                    ->label('Decline Reason')
                    ->getStateUsing(function ($record): string {
                        return match ($record->decline_reason) {
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
                            default => match ($record->archive_reason) {
                                'declined' => 'Declined',
                                'archived_manually' => 'Archived Manually',
                                null, '' => '-',
                                default => ucfirst(str_replace('_', ' ', (string) $record->archive_reason)),
                            },
                        };
                    })
                    ->badge()
                    ->color(function ($record): string {
                        return match ($record->decline_reason) {
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
                            default => match ($record->archive_reason) {
                                'declined' => 'danger',
                                'archived_manually' => 'gray',
                                default => 'gray',
                            },
                        };
                    }),

                Tables\Columns\TextColumn::make('decline_notes')
                    ->label('Decline Notes')
                    ->limit(60)
                    ->tooltip(fn ($record) => filled($record->decline_notes) ? $record->decline_notes : null)
                    ->wrap()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Archived At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('archive_reason')
                    ->label('Archive Type')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'declined' => 'Declined',
                            'archived_manually' => 'Archived Manually',
                            'converted_to_pre_employment' => 'Converted to Pre-Employment',
                            null, '' => '-',
                            default => ucfirst(str_replace('_', ' ', (string) $state)),
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'declined' => 'danger',
                            'archived_manually' => 'gray',
                            'converted_to_pre_employment' => 'info',
                            default => 'gray',
                        };
                    })
                    ->toggleable(),
            ])
            ->recordUrl(fn ($record) => JobApplicationResource::getUrl('view', ['record' => $record]))
            ->filters([
                Tables\Filters\SelectFilter::make('job_id')
                    ->label('Job')
                    ->options(
                        Job::query()
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->toArray()
                    )
                    ->searchable(),

                Tables\Filters\SelectFilter::make('decline_reason')
                    ->label('Decline Reason')
                    ->options([
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
                    ]),
            ])
            ->recordActions([
                Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'is_archived' => false,
                            'archive_reason' => null,
                            'archived_at' => null,
                            'decline_reason' => null,
                            'decline_notes' => null,
                            'status' => 'screening',
                        ]);

                        Notification::make()
                            ->title('Job application restored successfully')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->label('Permanent Delete')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkAction::make('restore_selected')
                    ->label('Restore Selected')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'is_archived' => false,
                                'archive_reason' => null,
                                'archived_at' => null,
                                'decline_reason' => null,
                                'decline_notes' => null,
                                'status' => 'screening',
                            ]);
                        }

                        Notification::make()
                            ->title('Selected archived applications restored successfully')
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
                            ->title('Selected archived applications permanently deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('archived_at', 'desc');
    }
}