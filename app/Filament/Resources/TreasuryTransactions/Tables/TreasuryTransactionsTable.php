<?php

namespace App\Filament\Resources\TreasuryTransactions\Tables;

use App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource;
use App\Models\TreasuryTransaction;
use Filament\Tables;
use Filament\Tables\Table;

class TreasuryTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->recordUrl(fn (TreasuryTransaction $record): ?string => auth()->user()?->canErp('treasury', 'view') ? TreasuryTransactionResource::getUrl('view', ['record' => $record]) : null)
            ->columns([
                Tables\Columns\TextColumn::make('transaction_no')
                    ->label('Transaction No')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('treasuryAccount.account_name')
                    ->label('Account')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? str_replace('_', ' ', ucwords((string) $state, '_')) : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('direction')
                    ->label('Direction')
                    ->badge()
                    ->color(fn ($state) => $state === 'in' ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state === 'in' ? 'Incoming' : 'Outgoing')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_posted')
                    ->label('Posted')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->label('Type')
                    ->options([
                        'invoice_payment' => 'Invoice Payment',
                        'salary_payment' => 'Salary Payment',
                        'expense_payment' => 'Expense Payment',
                        'transfer_in' => 'Transfer In',
                        'transfer_out' => 'Transfer Out',
                        'adjustment' => 'Adjustment',
                        'bank_fee' => 'Bank Fee',
                        'deduction' => 'Deduction',
                        'manual' => 'Manual',
                    ]),

                Tables\Filters\SelectFilter::make('direction')
                    ->label('Direction')
                    ->options([
                        'in' => 'Incoming',
                        'out' => 'Outgoing',
                    ]),

                Tables\Filters\SelectFilter::make('currency')
                    ->label('Currency')
                    ->options([
                        'EUR' => 'EUR',
                        'USD' => 'USD',
                        'LYD' => 'LYD',
                    ]),

                Tables\Filters\TernaryFilter::make('is_posted')
                    ->label('Posted'),
            ]);
    }
}
