<?php

namespace App\Filament\Resources\SalarySlips\Tables;

use App\Models\Employment;
use App\Models\SalarySlip;
use Filament\Tables;
use Filament\Tables\Table;

class SalarySlipsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('salary_year', 'desc')
            ->defaultSort('salary_month', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employment.employee_name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->default('-'),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('salary_period')
                    ->label('Salary Period')
                    ->state(function ($record) {
                        $year = $record->salary_year ?: '-';
                        $month = $record->salary_month ? str_pad((string) $record->salary_month, 2, '0', STR_PAD_LEFT) : '--';

                        return $year . '-' . $month;
                    })
                    ->sortable(query: function ($query, $direction) {
                        return $query
                            ->orderBy('salary_year', $direction)
                            ->orderBy('salary_month', $direction);
                    }),

                Tables\Columns\TextColumn::make('days_worked')
                    ->label('Paid Days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_count')
                    ->label('Scheduled Days')
                    ->state(fn ($record) => $record->days?->count() ?? 0),

                Tables\Columns\TextColumn::make('salary_basis')
                    ->label('Basis')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        SalarySlip::BASIS_DAILY_RATE => 'Daily Rate',
                        SalarySlip::BASIS_MONTHLY => 'Monthly',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        SalarySlip::BASIS_DAILY_RATE => 'info',
                        SalarySlip::BASIS_MONTHLY => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('daily_rate')
                    ->label('Daily Rate')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->currency ?: '') : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('monthly_salary')
                    ->label('Monthly Salary')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->currency ?: '') : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net Amount')
                    ->alignEnd()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->currency ?: '')),

                Tables\Columns\TextColumn::make('treasuryAccount.account_name')
                    ->label('Treasury Account')
                    ->toggleable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => SalarySlip::statusLabels()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        SalarySlip::STATUS_DRAFT => 'gray',
                        SalarySlip::STATUS_APPROVED => 'info',
                        SalarySlip::STATUS_SENT_TO_BANK => 'warning',
                        SalarySlip::STATUS_PAID => 'success',
                        SalarySlip::STATUS_BANK_REJECTED => 'danger',
                        SalarySlip::STATUS_LOCKED => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('bank_sent_at')
                    ->label('Bank Sent')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('Y-m-d H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('period_start')
                    ->label('Service Start')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('period_end')
                    ->label('Service End')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('employmentRotation.id')
                    ->label('Rotation')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('-'),

                Tables\Columns\TextColumn::make('generatedBy.name')
                    ->label('Generated By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('-'),

                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('salary_year')
                    ->label('Year')
                    ->options(function () {
                        return SalarySlip::query()
                            ->select('salary_year')
                            ->distinct()
                            ->orderByDesc('salary_year')
                            ->pluck('salary_year', 'salary_year')
                            ->toArray();
                    }),

                Tables\Filters\SelectFilter::make('salary_month')
                    ->label('Month')
                    ->options([
                        1 => '01 - January',
                        2 => '02 - February',
                        3 => '03 - March',
                        4 => '04 - April',
                        5 => '05 - May',
                        6 => '06 - June',
                        7 => '07 - July',
                        8 => '08 - August',
                        9 => '09 - September',
                        10 => '10 - October',
                        11 => '11 - November',
                        12 => '12 - December',
                    ]),

                Tables\Filters\SelectFilter::make('employment_id')
                    ->label('Employee')
                    ->options(
                        Employment::query()
                            ->orderBy('employee_name')
                            ->pluck('employee_name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(SalarySlip::statusLabels()),

                Tables\Filters\SelectFilter::make('currency')
                    ->label('Currency')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'GBP' => 'GBP',
                        'LYD' => 'LYD',
                    ]),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
