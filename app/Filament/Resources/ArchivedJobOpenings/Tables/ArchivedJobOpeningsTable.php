<?php

namespace App\Filament\Resources\ArchivedJobOpenings\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ArchivedJobOpeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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

                Tables\Columns\TextColumn::make('closing_date')
                    ->label('Closing Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('archive_reason')
                    ->label('Archive Reason')
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Archived At')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('restore')
                    ->label('')
                    ->tooltip('Restore job opening')
                    ->icon('heroicon-o-arrow-path')
                    ->iconButton()
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Restore archived job opening?')
                    ->modalDescription('This job opening will be restored back to the active Job Openings list.')
                    ->modalSubmitActionLabel('Restore')
                    ->action(function ($record): void {
                        $data = ['is_archived' => false];

                        if (\Illuminate\Support\Facades\Schema::hasColumn($record->getTable(), 'archived_at')) {
                            $data['archived_at'] = null;
                        }

                        if (\Illuminate\Support\Facades\Schema::hasColumn($record->getTable(), 'archive_reason')) {
                            $data['archive_reason'] = null;
                        }

                        $record->forceFill($data)->save();

                        Notification::make()
                            ->title('Job opening restored')
                            ->success()
                            ->send();
                    })
                    ->extraAttributes([
                        'class' => 'sf-job-row-action sf-job-row-action-restore',
                    ])
                    ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'edit')),

                Action::make('permanentDelete')
                    ->label('')
                    ->tooltip('Permanent delete')
                    ->icon('heroicon-o-trash')
                    ->iconButton()
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Permanently delete this job opening?')
                    ->modalDescription('This action cannot be undone.')
                    ->modalSubmitActionLabel('Permanent Delete')
                    ->action(function ($record): void {
                        $record->delete();

                        Notification::make()
                            ->title('Job opening permanently deleted')
                            ->success()
                            ->send();
                    })
                    ->extraAttributes([
                        'class' => 'sf-job-row-action sf-job-row-action-delete',
                    ])
                    ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'delete')),
            ])
            ->bulkActions([
                BulkAction::make('restoreSelected')
                    ->label('Restore Selected')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Restore selected job openings?')
                    ->modalSubmitActionLabel('Restore Selected')
                    ->action(function (Collection $records): void {
                        $records->each(function ($record): void {
                            $data = ['is_archived' => false];

                            if (\Illuminate\Support\Facades\Schema::hasColumn($record->getTable(), 'archived_at')) {
                                $data['archived_at'] = null;
                            }

                            if (\Illuminate\Support\Facades\Schema::hasColumn($record->getTable(), 'archive_reason')) {
                                $data['archive_reason'] = null;
                            }

                            $record->forceFill($data)->save();
                        });

                        Notification::make()
                            ->title('Selected job openings restored')
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'edit')),

                BulkAction::make('permanentDeleteSelected')
                    ->label('Permanent Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Permanently delete selected job openings?')
                    ->modalDescription('This action cannot be undone.')
                    ->modalSubmitActionLabel('Permanent Delete')
                    ->action(function (Collection $records): void {
                        $records->each->delete();

                        Notification::make()
                            ->title('Selected job openings permanently deleted')
                            ->success()
                            ->send();
                    })
                    ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'delete')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
