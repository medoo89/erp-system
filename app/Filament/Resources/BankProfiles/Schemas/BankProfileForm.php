<?php

namespace App\Filament\Resources\BankProfiles\Schemas;

use App\Models\BankProfile;
use App\Models\BankProfileAccount;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class BankProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                View::make('filament.resources.bank-profiles.components.form-header')
                    ->columnSpanFull(),

                Section::make('Bank Profile Details')
                    ->schema([
                        TextInput::make('profile_name')
                            ->label('Profile Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('beneficiary_name')
                            ->label('Beneficiary Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('branch_name')
                            ->label('Branch Name')
                            ->maxLength(255),

                        TextInput::make('swift_code')
                            ->label('SWIFT Code')
                            ->maxLength(255),

                        TextInput::make('routing_code')
                            ->label('Routing Code')
                            ->maxLength(255),

                        Toggle::make('is_default_for_invoices')
                            ->label('Default for Invoices')
                            ->inline(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),

                        Textarea::make('bank_address')
                            ->label('Bank Address')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Bank Accounts by Currency')
                    ->description('Add one account per currency. Treasury accounts will be created/updated automatically.')
                    ->schema([
                        Repeater::make('accounts')
                            ->relationship('accounts')
                            ->schema([
                                Select::make('currency')
                                    ->label('Currency')
                                    ->options(BankProfileAccount::currencyOptions())
                                    ->required()
                                    ->native(false),

                                TextInput::make('account_number')
                                    ->label('Account Number')
                                    ->maxLength(255),

                                TextInput::make('iban')
                                    ->label('IBAN')
                                    ->maxLength(255),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->inline(false),

                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->addActionLabel('Add Currency Account')
                            ->itemLabel(function (array $state): ?string {
                                $currency = $state['currency'] ?? null;
                                return $currency ? ('Currency: ' . $currency) : 'Currency Account';
                            }),
                    ]),
            ]);
    }
}
