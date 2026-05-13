<?php

namespace App\Filament\Resources\FinanceExpenses\RelationManagers;

use App\Models\Client;
use App\Models\Employment;
use App\Models\EmploymentRotation;
use App\Models\Project;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class AllocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'allocations';

    protected static ?string $title = 'Expense Allocations';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('allocation_type')
                ->label('Allocation Type')
                ->options([
                    'company' => 'Company',
                    'office' => 'Office',
                    'project' => 'Project',
                    'employment' => 'Employment',
                    'rotation' => 'Rotation',
                ])
                ->native(false),

            Select::make('client_id')
                ->label('Client')
                ->options(fn () => Client::query()->orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()
                ->preload(),

            Select::make('project_id')
                ->label('Project')
                ->options(fn () => Project::query()->orderBy('name')->pluck('name', 'id')->toArray())
                ->searchable()
                ->preload(),

            Select::make('employment_id')
                ->label('Employment')
                ->options(fn () => Employment::query()->orderBy('employee_name')->pluck('employee_name', 'id')->toArray())
                ->searchable()
                ->preload(),

            Select::make('employment_rotation_id')
                ->label('Rotation')
                ->options(fn () => EmploymentRotation::query()->orderByDesc('id')->pluck('rotation_label', 'id')->toArray())
                ->searchable()
                ->preload(),

            TextInput::make('allocated_amount')
                ->label('Allocated Amount')
                ->numeric()
                ->required()
                ->default(0),

            TextInput::make('allocation_percentage')
                ->label('Allocation %')
                ->numeric(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(3),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('allocation_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', (string) $state)) : '-'),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('employment.employee_name')
                    ->label('Employment')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rotation.rotation_label')
                    ->label('Rotation')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('allocated_amount')
                    ->label('Amount')
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('allocation_percentage')
                    ->label('%')
                    ->numeric(decimalPlaces: 2),
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


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }
}
