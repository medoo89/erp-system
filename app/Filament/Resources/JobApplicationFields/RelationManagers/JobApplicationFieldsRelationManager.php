<?php

namespace App\Filament\Resources\JobApplicationFields\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JobApplicationFieldOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    protected static ?string $title = 'Field Options';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('option_label')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('option_value')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('option_label'),
                Tables\Columns\TextColumn::make('option_value'),
                Tables\Columns\TextColumn::make('sort_order'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}