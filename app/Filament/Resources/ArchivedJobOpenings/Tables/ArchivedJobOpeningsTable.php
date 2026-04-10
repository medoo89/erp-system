<?php

namespace App\Filament\Resources\ArchivedJobOpenings\Tables;

use App\Models\Job;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ArchivedJobOpeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('is_archived', true))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Job Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Employment Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('closing_date')
                    ->label('Closing Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('archive_reason')
                    ->label('Archive Reason')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'expired' => 'Expired',
                            'closed' => 'Closed',
                            'filled' => 'Filled',
                            'archived_manually' => 'Archived Manually',
                            null, '' => '-',
                            default => ucfirst(str_replace('_', ' ', (string) $state)),
                        };
                    })
                    ->color(function ($state) {
                        return match ($state) {
                            'expired' => 'warning',
                            'closed' => 'gray',
                            'filled' => 'success',
                            'archived_manually' => 'warning',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Archived At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employment_type')
                    ->label('Employment Type')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'contract' => 'Contract',
                        'temporary' => 'Temporary',
                    ]),

                Tables\Filters\SelectFilter::make('archive_reason')
                    ->label('Archive Reason')
                    ->options([
                        'expired' => 'Expired',
                        'closed' => 'Closed',
                        'filled' => 'Filled',
                        'archived_manually' => 'Archived Manually',
                    ]),
            ])
            ->recordActions([
                Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Restore Job Opening')
                    ->modalDescription('Are you sure you want to restore this job opening back to the active list?')
                    ->modalSubmitActionLabel('Yes, Restore')
                    ->action(function (Job $record) {
                        $record->update([
                            'is_archived' => false,
                            'archive_reason' => null,
                            'archived_at' => null,
                        ]);

                        Notification::make()
                            ->title('Job opening restored successfully')
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
                            ]);
                        }

                        Notification::make()
                            ->title('Selected archived job openings restored successfully')
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
                            ->title('Selected archived job openings permanently deleted')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('archived_at', 'desc');
    }
}