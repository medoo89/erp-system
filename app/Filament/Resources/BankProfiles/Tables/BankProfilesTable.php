<?php

namespace App\Filament\Resources\BankProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class BankProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('profile_name')
                    ->label('Profile Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('beneficiary_name')
                    ->label('Beneficiary')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch_name')
                    ->label('Branch')
                    ->searchable()
                    ->toggleable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('accounts_count')
                    ->label('Currencies')
                    ->counts('accounts')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('currencies_preview')
                    ->label('Currency List')
                    ->state(function ($record): string {
                        $record->loadMissing('accounts');

                        $currencies = $record->accounts
                            ->where('is_active', true)
                            ->pluck('currency')
                            ->filter()
                            ->map(fn ($value) => strtoupper((string) $value))
                            ->unique()
                            ->values()
                            ->toArray();

                        return count($currencies) ? implode(' / ', $currencies) : '-';
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('swift_code')
                    ->label('SWIFT')
                    ->searchable()
                    ->toggleable()
                    ->default('-'),

                Tables\Columns\IconColumn::make('is_default_for_invoices')
                    ->label('Default')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_default_for_invoices')
                    ->label('Default for Invoices'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('bank_profiles', 'edit')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('bank_profiles', 'delete')),
                ]),
            ]);
    }
}
