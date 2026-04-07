<?php

namespace App\Filament\Resources\ArchivedJobs\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ArchivedJobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department')
                    ->label('Department')
                    ->searchable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Location'),

                Tables\Columns\TextColumn::make('employment_type')
                    ->label('Employment Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('archive_reason')
                    ->label('Archive Reason')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'archived_manually' => 'Archived Manually',
                        'declined' => 'Declined',
                        'expired' => 'Expired',
                        'closed' => 'Closed',
                        'candidate_withdrew' => 'Candidate Withdrew',
                        'candidate_refused_contract' => 'Candidate Refused Contract',
                        'candidate_refused_salary' => 'Candidate Refused Salary',
                        'no_response' => 'No Response',
                        null, '' => '-',
                        default => ucfirst(str_replace('_', ' ', (string) $state)),
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'archived_manually' => 'gray',
                        'declined' => 'danger',
                        'expired' => 'warning',
                        'closed' => 'gray',
                        'candidate_withdrew' => 'warning',
                        'candidate_refused_contract' => 'danger',
                        'candidate_refused_salary' => 'danger',
                        'no_response' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Archived At')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('department')
                    ->label('Department')
                    ->options(fn () => \App\Models\Job::query()
                        ->whereNotNull('department')
                        ->distinct()
                        ->orderBy('department')
                        ->pluck('department', 'department')
                        ->toArray()
                    ),
            ])
            ->recordActions([
                Action::make('reopen')
                    ->label('Reopen')
                    ->color('success')
                    ->form([
                        DatePicker::make('closing_date')
                            ->label('New Expiry Date')
                            ->required()
                            ->native(false),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Reopen Job Opening')
                    ->modalDescription('Choose a new expiry date before reopening this job opening.')
                    ->modalSubmitActionLabel('Reopen')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'is_archived' => false,
                            'archive_reason' => null,
                            'archived_at' => null,
                            'is_active' => true,
                            'closing_date' => $data['closing_date'],
                        ]);

                        Notification::make()
                            ->title('Job opening reopened successfully')
                            ->success()
                            ->send();
                    }),

                DeleteAction::make()
                    ->label('Permanent Delete')
                    ->color('danger')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkAction::make('bulk_reopen')
                    ->label('Reopen Selected')
                    ->color('success')
                    ->form([
                        DatePicker::make('closing_date')
                            ->label('New Expiry Date')
                            ->required()
                            ->native(false),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Reopen Selected Job Openings')
                    ->modalDescription('Choose a new expiry date before reopening the selected job openings.')
                    ->modalSubmitActionLabel('Reopen')
                    ->action(function (Collection $records, array $data) {
                        foreach ($records as $record) {
                            $record->update([
                                'is_archived' => false,
                                'archive_reason' => null,
                                'archived_at' => null,
                                'is_active' => true,
                                'closing_date' => $data['closing_date'],
                            ]);
                        }

                        Notification::make()
                            ->title('Selected job openings reopened successfully')
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