<?php

namespace App\Filament\Resources\FinanceExpenses\RelationManagers;

use App\Models\Employment;
use App\Models\EmploymentRotation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class TravelDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'travelDetail';

    protected static ?string $title = 'Travel Detail';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('employment_id')
                ->label('Employment')
                ->options(fn () => Employment::query()->orderBy('employee_name')->pluck('employee_name', 'id')->toArray())
                ->searchable()
                ->preload(),

            TextInput::make('traveler_name')
                ->label('Traveler Name')
                ->maxLength(255),

            Select::make('trip_type')
                ->label('Trip Type')
                ->options([
                    'one_way' => 'One Way',
                    'round_trip' => 'Round Trip',
                    'open_return' => 'Open Return',
                    'split_rotation' => 'Split Rotation',
                ])
                ->native(false),

            TextInput::make('origin')
                ->label('Origin')
                ->maxLength(255),

            TextInput::make('destination')
                ->label('Destination')
                ->maxLength(255),

            DatePicker::make('departure_date')
                ->label('Departure Date'),

            DatePicker::make('return_date')
                ->label('Return Date'),

            Toggle::make('return_open')
                ->label('Return Open')
                ->default(false),

            Toggle::make('split_across_rotations')
                ->label('Split Across Rotations')
                ->default(false),

            Select::make('outbound_rotation_id')
                ->label('Outbound Rotation')
                ->options(fn () => EmploymentRotation::query()->orderByDesc('id')->pluck('rotation_label', 'id')->toArray())
                ->searchable()
                ->preload(),

            Select::make('inbound_rotation_id')
                ->label('Inbound Rotation')
                ->options(fn () => EmploymentRotation::query()->orderByDesc('id')->pluck('rotation_label', 'id')->toArray())
                ->searchable()
                ->preload(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(4),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('traveler_name')
                    ->label('Traveler'),

                Tables\Columns\TextColumn::make('trip_type')
                    ->label('Trip Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', (string) $state)) : '-'),

                Tables\Columns\TextColumn::make('origin')
                    ->label('Origin'),

                Tables\Columns\TextColumn::make('destination')
                    ->label('Destination'),

                Tables\Columns\TextColumn::make('departure_date')
                    ->label('Departure')
                    ->date(),

                Tables\Columns\TextColumn::make('return_date')
                    ->label('Return')
                    ->date(),

                Tables\Columns\IconColumn::make('return_open')
                    ->label('Open Return')
                    ->boolean(),

                Tables\Columns\IconColumn::make('split_across_rotations')
                    ->label('Split')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'edit')),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'edit')),
                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'delete')),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }
}
