<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Models\ClientContractTerm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContractTermsRelationManager extends RelationManager
{
    protected static string $relationship = 'contractTerms';

    protected static ?string $title = 'Project Contract Terms';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Term Name')
                ->maxLength(255)
                ->placeholder('Example: Dietsmann Elbouri Default Terms'),

            Select::make('billing_basis')
                ->label('Billing Basis')
                ->options(ClientContractTerm::billingBasisOptions())
                ->default(ClientContractTerm::BILLING_DAILY_RATE)
                ->required()
                ->native(false),

            TextInput::make('client_rate')
                ->label('Client Billing Rate')
                ->numeric()
                ->required()
                ->helperText('This is the rate charged to the client, not the employee daily rate.'),

            Select::make('currency')
                ->label('Foreign Currency')
                ->options([
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'GBP' => 'GBP',
                    'LYD' => 'LYD',
                ])
                ->default('EUR')
                ->required()
                ->native(false),

            TextInput::make('foreign_percentage')
                ->label('Foreign %')
                ->numeric()
                ->default(100)
                ->required(),

            TextInput::make('local_percentage')
                ->label('Local %')
                ->numeric()
                ->default(0)
                ->required(),

            Select::make('local_currency')
                ->label('Local Currency')
                ->options([
                    'LYD' => 'LYD',
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'GBP' => 'GBP',
                ])
                ->default('LYD')
                ->required()
                ->native(false),

            TextInput::make('default_exchange_rate')
                ->label('Default Exchange Rate')
                ->numeric()
                ->helperText('Default only. You can change it later while generating the invoice.'),

            DatePicker::make('effective_from')
                ->label('Effective From')
                ->native(false)
                ->displayFormat('d/m/Y'),

            DatePicker::make('effective_to')
                ->label('Effective To')
                ->native(false)
                ->displayFormat('d/m/Y'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),

            Toggle::make('is_default')
                ->label('Default Terms')
                ->default(false),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(4)
                ->columnSpanFull(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('effective_from', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->weight('bold')
                    ->default('-'),

                Tables\Columns\TextColumn::make('billing_basis')
                    ->label('Basis')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ClientContractTerm::billingBasisOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('client_rate')
                    ->label('Client Billing Rate')
                    ->formatStateUsing(fn ($state, $record) => number_format((float) $state, 2) . ' ' . ($record->currency ?: '')),

                Tables\Columns\TextColumn::make('split')
                    ->label('Split')
                    ->state(fn ($record) => rtrim(rtrim(number_format((float) $record->foreign_percentage, 2), '0'), '.') . '% ' . ($record->currency ?: '-') . ' + ' . rtrim(rtrim(number_format((float) $record->local_percentage, 2), '0'), '.') . '% ' . ($record->local_currency ?: '-')),

                Tables\Columns\TextColumn::make('default_exchange_rate')
                    ->label('Exch. Rate')
                    ->state(fn ($record) => filled($record->default_exchange_rate) ? rtrim(rtrim(number_format((float) $record->default_exchange_rate, 4), '0'), '.') : '-'),

                Tables\Columns\TextColumn::make('effective_from')
                    ->label('From')
                    ->state(fn ($record) => $record->effective_from ? $record->effective_from->format('Y-m-d') : '-'),

                Tables\Columns\TextColumn::make('effective_to')
                    ->label('To')
                    ->state(fn ($record) => $record->effective_to ? $record->effective_to->format('Y-m-d') : '-'),

                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('projects', 'contract_terms'))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['client_id'] = $this->ownerRecord->client_id;
                        $data['project_id'] = $this->ownerRecord->id;

                        if (($data['is_default'] ?? false) === true) {
                            ClientContractTerm::query()
                                ->where('project_id', $this->ownerRecord->id)
                                ->update(['is_default' => false]);
                        }

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('projects', 'contract_terms'))
                    ->mutateRecordDataUsing(function (array $data): array {
                        if (blank($data['effective_from'] ?? null)) {
                            $data['effective_from'] = null;
                        }

                        if (blank($data['effective_to'] ?? null)) {
                            $data['effective_to'] = null;
                        }

                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        if (($data['is_default'] ?? false) === true) {
                            ClientContractTerm::query()
                                ->where('project_id', $this->ownerRecord->id)
                                ->where('id', '!=', $this->getMountedTableActionRecord()->id)
                                ->update(['is_default' => false]);
                        }

                        return $data;
                    }),
                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('projects', 'contract_terms')),
            ]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'contract_terms') ?? false);
    }
}
