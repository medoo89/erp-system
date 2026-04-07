<?php

namespace App\Filament\Resources\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JobApplicationFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $title = 'Field Options';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('option_label')
                ->label('Option Label')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if (blank($get('option_value')) && filled($state)) {
                        $set('option_value', str($state)->snake()->lower()->toString());
                    }
                }),

            Forms\Components\TextInput::make('option_value')
                ->label('Option Value')
                ->required()
                ->helperText('Auto-generated from label'),

            Forms\Components\Hidden::make('sort_order')
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('option_label')
                    ->label('Label'),

                Tables\Columns\TextColumn::make('option_value')
                    ->label('Value'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Option'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}