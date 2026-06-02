<?php

namespace App\Filament\Resources\ProjectContractDocuments\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectContractDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('projectContract.title')
                    ->label('Contract')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('document_type')
                    ->label('Type')
                    ->badge()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('document_no')
                    ->label('Document No.')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(2)
                    ->sortable(),

                TextColumn::make('currency')
                    ->label('Currency')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('issue_date')
                    ->label('Issue Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->requiresConfirmation(),
            ]);
    }
}
