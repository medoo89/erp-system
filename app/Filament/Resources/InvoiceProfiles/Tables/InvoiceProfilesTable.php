<?php

namespace App\Filament\Resources\InvoiceProfiles\Tables;

use App\Filament\Resources\InvoiceProfiles\InvoiceProfileResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->recordUrl(fn ($record): string => InvoiceProfileResource::getUrl('view', ['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Profile')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->default('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank')
                    ->default('-')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('invoice_profiles', 'view')),
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('invoice_profiles', 'edit')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('invoice_profiles', 'delete')),
                ]),
            ]);
    }
}
