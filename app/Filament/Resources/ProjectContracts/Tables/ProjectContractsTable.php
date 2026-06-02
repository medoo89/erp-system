<?php

namespace App\Filament\Resources\ProjectContracts\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Project')
                    ->formatStateUsing(fn ($state, $record) => $record->project?->project_name ?? $record->project?->name ?? ('Project #' . $record->project_id))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('contract_no')
                    ->label('Contract No.')
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('version')
                    ->label('Ver.'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('contract_value')
                    ->label('Value')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Currency')
                    ->badge(),

                TextColumn::make('tax_paid')
                    ->label('Tax Paid')
                    ->numeric(2),

                TextColumn::make('tax_currency')
                    ->label('Tax Currency')
                    ->badge()
                    ->placeholder('LYD'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make()
                    ->label('Edit'),

                DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation(),
            ]);
    }
}
