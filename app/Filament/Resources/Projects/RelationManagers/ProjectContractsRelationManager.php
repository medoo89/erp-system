<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Models\ProjectContract;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectContracts';

    protected static ?string $title = 'Project Contracts / Counter';

    protected static ?string $modelLabel = 'Project Contract';

    protected static ?string $pluralModelLabel = 'Project Contracts';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('contract_no')
                ->label('Contract No.')
                ->maxLength(255),

            TextInput::make('title')
                ->label('Contract Title')
                ->placeholder('Example: Dietsmann M40 NY16 Contract')
                ->maxLength(255),

            TextInput::make('version_no')
                ->label('Version')
                ->numeric()
                ->default(1)
                ->required(),

            Select::make('parent_contract_id')
                ->label('Parent Contract')
                ->options(function () {
                    return ProjectContract::query()
                        ->where('project_id', $this->ownerRecord->id)
                        ->orderByDesc('id')
                        ->get()
                        ->mapWithKeys(fn (ProjectContract $contract) => [
                            $contract->id => trim(($contract->contract_no ?: 'Contract #' . $contract->id) . ' — ' . ($contract->title ?: 'Untitled')),
                        ])
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->native(false),

            Select::make('contract_kind')
                ->label('Contract Type')
                ->options(ProjectContract::kindOptions())
                ->default(ProjectContract::KIND_BASE)
                ->required()
                ->native(false)
                ->live(),

            Select::make('amendment_type')
                ->label('Amendment / Renewal Type')
                ->options(ProjectContract::amendmentTypeOptions())
                ->native(false)
                ->visible(fn ($get) => in_array($get('contract_kind'), [
                    ProjectContract::KIND_RENEWAL,
                    ProjectContract::KIND_AMENDMENT,
                ], true)),

            TextInput::make('value_amount')
                ->label('Contract / Added Value')
                ->numeric()
                ->default(0)
                ->helperText('For date-only renewal, keep value as 0. Value additions increase the project contract counter.'),

            Select::make('currency')
                ->label('Currency')
                ->options(ProjectContract::currencyOptions())
                ->default('EUR')
                ->required()
                ->native(false),

            TextInput::make('tax_paid_amount')
                ->label('Paid / Recorded Contract Tax')
                ->numeric()
                ->helperText('Tax paid/recorded against this contract or amendment.'),

            Select::make('tax_currency')
                ->label('Tax Currency')
                ->options(ProjectContract::currencyOptions())
                ->default('LYD')
                ->native(false),

            TextInput::make('tax_percent')
                ->label('Tax %')
                ->numeric(),

            DatePicker::make('start_date')
                ->label('Start Date')
                ->native(false),

            DatePicker::make('end_date')
                ->label('End Date')
                ->native(false),

            Select::make('status')
                ->label('Status')
                ->options(ProjectContract::statusOptions())
                ->default(ProjectContract::STATUS_DRAFT)
                ->required()
                ->native(false),

            FileUpload::make('contract_file_path')
                ->label('Contract File')
                ->disk('public')
                ->directory(fn () => 'project-contracts/' . ($this->ownerRecord?->id ?? 'new'))
                ->preserveFilenames()
                ->downloadable()
                ->openable(),

            TextInput::make('green_threshold_percent')
                ->label('Green Until %')
                ->numeric()
                ->default(60),

            TextInput::make('yellow_threshold_percent')
                ->label('Yellow Until %')
                ->numeric()
                ->default(80),

            TextInput::make('red_threshold_percent')
                ->label('Red From %')
                ->numeric()
                ->default(95),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(4)
                ->columnSpanFull(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('contract_no')
                    ->label('Contract No.')
                    ->searchable()
                    ->weight('bold')
                    ->default('-'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->limit(34)
                    ->default('-'),

                Tables\Columns\TextColumn::make('version_no')
                    ->label('Ver.')
                    ->badge(),

                Tables\Columns\TextColumn::make('contract_kind')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ProjectContract::kindOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('amendment_type')
                    ->label('Amendment')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? (ProjectContract::amendmentTypeOptions()[$state] ?? $state) : '-'),

                Tables\Columns\TextColumn::make('value_amount')
                    ->label('Value')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->currency ?: '')),

                Tables\Columns\TextColumn::make('tax_paid_amount')
                    ->label('Tax Paid')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->tax_currency ?: $record->currency ?: '') : '-'),

                Tables\Columns\TextColumn::make('period')
                    ->label('Period')
                    ->state(fn ($record) => ($record->start_date ? $record->start_date->format('Y-m-d') : '-') . ' → ' . ($record->end_date ? $record->end_date->format('Y-m-d') : '-')),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ProjectContract::statusOptions()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        ProjectContract::STATUS_ACTIVE => 'success',
                        ProjectContract::STATUS_DRAFT => 'gray',
                        ProjectContract::STATUS_EXPIRED => 'warning',
                        ProjectContract::STATUS_CLOSED => 'info',
                        ProjectContract::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) (
                        auth()->user()?->canErp('projects', 'contracts')
                        || auth()->user()?->canErp('projects', 'contract_terms')
                        || auth()->user()?->canErp('projects', 'edit')
                    ))
                    ->label('Add Contract / Amendment')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['client_id'] = $this->ownerRecord->client_id;
                        $data['project_id'] = $this->ownerRecord->id;
                        $data['created_by'] = auth()->id();
                        $data['updated_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) (
                        auth()->user()?->canErp('projects', 'contracts')
                        || auth()->user()?->canErp('projects', 'contract_terms')
                        || auth()->user()?->canErp('projects', 'edit')
                    ))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['updated_by'] = auth()->id();

                        return $data;
                    }),

                DeleteAction::make()
                    ->visible(fn () => (bool) (
                        auth()->user()?->canErp('projects', 'contracts')
                        || auth()->user()?->canErp('projects', 'delete')
                    )),
            ]);
    }

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (
            auth()->user()?->canErp('projects', 'view')
            || auth()->user()?->canErp('projects', 'contract_terms')
            || auth()->user()?->canErp('projects', 'contracts')
        );
    }
}
