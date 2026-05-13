<?php

namespace App\Filament\Resources\TreasuryOperations\Schemas;

use App\Models\TreasuryAccount;
use App\Models\TreasuryOperation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;

class TreasuryOperationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaView::make('filament.resources.treasury-operations.pages.form-hero')
                    ->columnSpanFull(),

                Section::make('Operation Identity')
                    ->description('Basic operation information. The operation number is generated automatically after saving.')
                    ->schema([
                        Placeholder::make('operation_no_display')
                            ->label('Operation No')
                            ->content(fn ($record) => $record?->operation_no ?: 'Auto-generated on save'),

                        DatePicker::make('operation_date')
                            ->label('Operation Date')
                            ->helperText('The official date of this treasury operation.')
                            ->required()
                            ->default(now()),

                        Select::make('operation_type')
                            ->label('Operation Type')
                            ->helperText('Choose the business reason for this movement, such as transfer, invoice receipt, settlement, or adjustment.')
                            ->options(TreasuryOperation::getOperationTypeOptions())
                            ->required()
                            ->native(false),

                        Toggle::make('is_posted')
                            ->label('Posted')
                            ->helperText('When enabled, this operation is treated as posted/confirmed in treasury tracking.')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Account Flow')
                    ->description('Define where the money is moving from and where it should arrive.')
                    ->schema([
                        Select::make('from_account_id')
                            ->label('From Account')
                            ->helperText('The source account. Money will be considered as moving out from this account.')
                            ->options(fn () => TreasuryAccount::query()->orderBy('account_name')->pluck('account_name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('to_account_id')
                            ->label('To Account')
                            ->helperText('The destination account. Money will be considered as moving into this account.')
                            ->options(fn () => TreasuryAccount::query()->orderBy('account_name')->pluck('account_name', 'id')->toArray())
                            ->searchable()
                            ->preload(),

                        Select::make('fee_account_id')
                            ->label('Fee Account')
                            ->helperText('Optional. Use this only when bank charges or transfer fees are posted to a separate account.')
                            ->options(fn () => TreasuryAccount::query()->orderBy('account_name')->pluck('account_name', 'id')->toArray())
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),

                Section::make('Amounts & Currency')
                    ->description('Enter the source amount, destination amount, currency, exchange rate, and any fee amount.')
                    ->schema([
                        TextInput::make('from_amount')
                            ->label('From Amount')
                            ->helperText('Amount deducted or moved from the source account.')
                            ->numeric()
                            ->required(),

                        Select::make('from_currency')
                            ->label('From Currency')
                            ->helperText('Currency of the source amount.')
                            ->options(TreasuryAccount::getCurrencyOptions())
                            ->required()
                            ->native(false),

                        TextInput::make('to_amount')
                            ->label('To Amount')
                            ->helperText('Amount received by the destination account. For same-currency transfers, it is usually equal to From Amount minus fees.')
                            ->numeric(),

                        Select::make('to_currency')
                            ->label('To Currency')
                            ->helperText('Currency of the destination amount. Leave empty if there is no destination account.')
                            ->options(TreasuryAccount::getCurrencyOptions())
                            ->native(false),

                        TextInput::make('exchange_rate')
                            ->label('Exchange Rate')
                            ->helperText('Use 1 for same-currency movements. Use the actual rate when converting currencies.')
                            ->numeric(),

                        TextInput::make('fee_amount')
                            ->label('Fee Amount')
                            ->helperText('Optional bank fee or service charge related to this movement.')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),

                Section::make('Description & Notes')
                    ->description('Add clear context so the finance team can understand why this operation was created.')
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            ->helperText('Short business description shown in treasury screens and audit context.')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->helperText('Internal notes for finance/admin users.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}
