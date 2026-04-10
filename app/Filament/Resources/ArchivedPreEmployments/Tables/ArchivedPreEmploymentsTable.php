<?php

namespace App\Filament\Resources\ArchivedPreEmployments\Tables;

use App\Models\PreEmployment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ArchivedPreEmploymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with(['job', 'jobApplication', 'assignedHrUser'])
                ->where(function (Builder $subQuery) {
                    $subQuery
                        ->where('is_archived', true)
                        ->orWhere('is_declined', true)
                        ->orWhereNotNull('declined_at');
                }))
            ->columns([
                Tables\Columns\TextColumn::make('candidate_name')
                    ->label('Candidate')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Final Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'declined' => 'danger',
                        'converted_to_employment' => 'success',
                        'ready_for_employment' => 'success',
                        'pending_medical' => 'warning',
                        'pending_visa' => 'warning',
                        'pending_travel' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
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
                        default => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-',
                    }),

                Tables\Columns\TextColumn::make('decline_reason')
                    ->label('Decline Reason')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
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
                            null, '' => '-',
                            default => ucfirst(str_replace('_', ' ', (string) $state)),
                        };
                    })
                    ->color(function ($state) {
                        return filled($state) ? 'danger' : 'gray';
                    }),

                Tables\Columns\TextColumn::make('decline_notes')
                    ->label('Decline Notes')
                    ->limit(60)
                    ->tooltip(fn ($record) => filled($record->decline_notes) ? $record->decline_notes : null)
                    ->wrap()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assignedHrUser.name')
                    ->label('HR Officer')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Archived At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('declined_at')
                    ->label('Declined At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('archive_reason')
                    ->label('Archive Type')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'declined' => 'Declined',
                            'archived_manually' => 'Archived Manually',
                            'converted_to_employment' => 'Converted to Employment',
                            null, '' => '-',
                            default => ucfirst(str_replace('_', ' ', (string) $state)),
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'declined' => 'danger',
                            'archived_manually' => 'gray',
                            'converted_to_employment' => 'success',
                            default => 'gray',
                        };
                    })
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Final Status')
                    ->options([
                        'declined' => 'Declined',
                        'converted_to_employment' => 'Converted to Employment',
                        'ready_for_employment' => 'Ready for Employment',
                        'pending_medical' => 'Pending Medical',
                        'pending_visa' => 'Pending Visa',
                        'pending_travel' => 'Pending Travel',
                    ]),

                Tables\Filters\SelectFilter::make('decline_reason')
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
                    ]),
            ])
            ->recordActions([
                Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Restore Pre-Employment Record')
                    ->modalDescription('Are you sure you want to restore this Pre-Employment record back to the active list?')
                    ->modalSubmitActionLabel('Yes, Restore')
                    ->action(function (PreEmployment $record) {
                        $record->update([
                            'is_archived' => false,
                            'archive_reason' => null,
                            'archived_at' => null,
                            'is_declined' => false,
                            'decline_reason' => null,
                            'decline_notes' => null,
                            'declined_at' => null,
                            'status' => 'documents_under_review',
                        ]);

                        Notification::make()
                            ->title('Pre-Employment record restored successfully')
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
                                'is_declined' => false,
                                'decline_reason' => null,
                                'decline_notes' => null,
                                'declined_at' => null,
                                'status' => 'documents_under_review',
                            ]);
                        }

                        Notification::make()
                            ->title('Selected archived Pre-Employment records restored successfully')
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
                            ->title('Selected archived Pre-Employment records permanently deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('archived_at', 'desc');
    }
}