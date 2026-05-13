<?php

namespace App\Filament\Resources\CandidateFinanceProfiles\Tables;

use App\Models\CandidateFinanceProfile;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class CandidateFinanceProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employment.employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->default('-')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('salary_basis')
                    ->label('Salary Basis')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === CandidateFinanceProfile::BASIS_DAILY_RATE ? 'Daily Rate' : 'Monthly')
                    ->color('info'),

                Tables\Columns\TextColumn::make('daily_rate')
                    ->label('Daily Rate')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->payout_currency ?: '') : '-'),

                Tables\Columns\TextColumn::make('client_billing_rate')
                    ->label('Client Billing Rate')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->client_billing_currency ?: '') : '-'),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean(),

                Tables\Columns\TextColumn::make('effective_from')
                    ->date('Y-m-d')
                    ->default('-'),

                Tables\Columns\TextColumn::make('effective_to')
                    ->date('Y-m-d')
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_current')
                    ->label('Current only'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('candidate_finance_profiles', 'view')),
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('candidate_finance_profiles', 'edit')),
                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('candidate_finance_profiles', 'delete')),
            ]);
    }
}