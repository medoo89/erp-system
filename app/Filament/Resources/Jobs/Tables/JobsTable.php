<?php

namespace App\Filament\Resources\Jobs\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class JobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Position')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('project.client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
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
                    ->label('Published')
                    ->boolean(),

                Tables\Columns\TextColumn::make('closing_date')
                    ->label('Expiry Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->tooltip('Edit job opening')
                    ->icon('heroicon-o-pencil-square')
                    ->iconButton()
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'sf-job-row-action sf-job-row-action-edit',
                    ])
                    ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'edit')),

                Action::make('archive')
                    ->label('')
                    ->tooltip('Archive job opening')
                    ->icon('heroicon-o-archive-box')
                    ->iconButton()
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Archive Job Opening')
                    ->modalDescription('This job opening will be moved to the archive. You can restore it later from the Archive section.')
                    ->modalSubmitActionLabel('Archive')
                    ->action(function ($record): void {
                        $record->forceFill([
                            'is_archived' => true,
                        ])->save();
                    })
                    ->extraAttributes([
                        'class' => 'sf-job-row-action sf-job-row-action-archive',
                    ])
                    ->visible(fn () => (bool) auth()->user()?->canErp('jobs', 'edit')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
