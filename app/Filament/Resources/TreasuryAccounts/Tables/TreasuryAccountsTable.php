<?php

namespace App\Filament\Resources\TreasuryAccounts\Tables;

use App\Models\TreasuryAccount;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class TreasuryAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('account_name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('account_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(function (?string $state): string {
                        return match ((string) $state) {
                            TreasuryAccount::TYPE_BANK => 'Bank',
                            TreasuryAccount::TYPE_CASH => 'Cash',
                            TreasuryAccount::TYPE_CLEARING => 'Clearing',
                            default => ucfirst((string) $state),
                        };
                    })
                    ->colors([
                        'info' => TreasuryAccount::TYPE_BANK,
                        'success' => TreasuryAccount::TYPE_CASH,
                        'warning' => TreasuryAccount::TYPE_CLEARING,
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('institution_name')
                    ->label('Institution')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bankProfile.profile_name')
                    ->label('Bank Profile')
                    ->searchable()
                    ->toggleable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('opening_balance')
                    ->label('Opening Balance')
                    ->money(fn ($record) => $record->currency ?: 'USD', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_balance')
                    ->label('Current Balance')
                    ->money(fn ($record) => $record->currency ?: 'USD', true)
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_default')
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
                Tables\Filters\SelectFilter::make('account_type')
                    ->label('Account Type')
                    ->options(TreasuryAccount::getAccountTypeOptions()),

                Tables\Filters\SelectFilter::make('currency')
                    ->label('Currency')
                    ->options(TreasuryAccount::getCurrencyOptions()),

                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('treasury', 'edit')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('treasury', 'delete')),
                ]),
            ]);
    }
}
