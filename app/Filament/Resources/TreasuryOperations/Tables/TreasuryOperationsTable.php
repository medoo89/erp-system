<?php

namespace App\Filament\Resources\TreasuryOperations\Tables;

use App\Filament\Resources\TreasuryOperations\TreasuryOperationResource;
use App\Models\TreasuryOperation;
use Filament\Tables;
use Filament\Tables\Table;

class TreasuryOperationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->recordUrl(fn (TreasuryOperation $record): ?string => auth()->user()?->canErp('treasury', 'view') ? TreasuryOperationResource::getUrl('view', ['record' => $record]) : null)
            ->columns([
                Tables\Columns\TextColumn::make('operation_no')
                    ->label('Operation No')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('operation_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('operation_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? str_replace('_', ' ', ucwords((string) $state, '_')) : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fromAccount.account_name')
                    ->label('From')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('toAccount.account_name')
                    ->label('To')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('from_amount')
                    ->label('From Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('to_amount')
                    ->label('To Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\TextColumn::make('fee_amount')
                    ->label('Fee')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_posted')
                    ->label('Posted')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('operation_type')
                    ->label('Type')
                    ->options(TreasuryOperation::getOperationTypeOptions()),

                Tables\Filters\TernaryFilter::make('is_posted')
                    ->label('Posted'),
            ]);
    }
}
