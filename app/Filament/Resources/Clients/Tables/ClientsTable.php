<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('client_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('projects_count')
                    ->label('Projects')
                    ->counts('projects')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn (): bool => (bool) auth()->user()?->canErp('clients', 'edit')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => (bool) auth()->user()?->canErp('clients', 'delete')),
                ]),
            ]);
    }
}
