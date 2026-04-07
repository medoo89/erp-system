<?php

namespace App\Filament\Resources\Jobs\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class JobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->where('is_archived', false))
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
                    ->label('Employment type')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('closing_date')
                    ->label('Expiry')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Published'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('archive')
                    ->label('Archive')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->update([
                                'is_archived' => true,
                                'archive_reason' => 'archived_manually',
                                'archived_at' => now(),
                            ]);
                        }

                        Notification::make()
                            ->title('Selected job openings archived')
                            ->success()
                            ->send();
                    }),

                BulkAction::make('reopen')
                    ->label('Reopen')
                    ->color('success')
                    ->form([
                        DatePicker::make('closing_date')
                            ->label('New Expiry Date')
                            ->required()
                            ->native(false),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading('Reopen Job Openings')
                    ->modalDescription('Please choose a new expiry date before reopening the selected job openings.')
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

                BulkAction::make('permanent_delete')
                    ->label('Permanent Delete')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each->delete();

                        Notification::make()
                            ->title('Selected job openings permanently deleted')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}