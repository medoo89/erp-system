<?php

namespace App\Filament\Resources\JobApplicationFields\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobApplicationFieldsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->orderByRaw("
                        CASE 
                            WHEN field_group = 'basic' THEN 1
                            WHEN field_group = 'additional' THEN 2
                            ELSE 3
                        END
                    ")
                    ->orderBy('sort_order');
            })
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Field Label')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('field_key')
                    ->label('Field Key')
                    ->searchable(),

                Tables\Columns\TextColumn::make('field_type')
                    ->label('Field Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('field_group')
                    ->label('Group')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'basic' ? 'Basic' : 'Additional'),

                Tables\Columns\IconColumn::make('is_global')
                    ->label('Global')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('field_group')
                    ->label('Group')
                    ->options([
                        'basic' => 'Basic Fields',
                        'additional' => 'Additional Fields',
                    ]),
            ])
            ->defaultGroup('field_group')
            ->groups([
                Tables\Grouping\Group::make('field_group')
                    ->label('Group')
                    ->getTitleFromRecordUsing(
                        fn ($record) => $record->field_group === 'basic'
                            ? 'Basic Fields'
                            : 'Additional Fields'
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}