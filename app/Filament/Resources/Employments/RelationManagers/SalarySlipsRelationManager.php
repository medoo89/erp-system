<?php

namespace App\Filament\Resources\Employments\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SalarySlipsRelationManager extends RelationManager
{
    protected static string $relationship = 'salarySlips';

    protected static ?string $title = 'Salary Slips';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('salary_year')->label('Year')->sortable(),
                Tables\Columns\TextColumn::make('salary_month')->label('Month')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'paid' => 'success',
                        'approved' => 'info',
                        'generated', 'draft' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('currency')->label('Currency')->default('-'),
                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net Amount')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) ($state ?? $record->total_amount ?? $record->base_amount ?? 0), 2) . ' ' . ($record->currency ?: '')),
                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->state(fn ($record) => ($record->period_start ? $record->period_start->format('Y-m-d') : '-') . ' → ' . ($record->period_end ? $record->period_end->format('Y-m-d') : '-')),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'view') ?? false);
    }
}
