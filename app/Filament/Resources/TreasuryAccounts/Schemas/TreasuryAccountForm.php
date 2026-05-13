<?php

namespace App\Filament\Resources\TreasuryAccounts\Schemas;

use App\Models\BankProfile;
use App\Models\TreasuryAccount;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TreasuryAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Details')
                    ->schema([
                        TextInput::make('account_name')
                            ->label('Account Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('account_type')
                            ->label('Account Type')
                            ->options(TreasuryAccount::getAccountTypeOptions())
                            ->required()
                            ->live()
                            ->native(false),

                        Select::make('currency')
                            ->label('Currency')
                            ->options(TreasuryAccount::getCurrencyOptions())
                            ->required()
                            ->native(false),

                        Select::make('bank_profile_id')
                            ->label('Linked Bank Profile')
                            ->options(function () {
                                return BankProfile::query()
                                    ->where('is_active', true)
                                    ->orderBy('profile_name')
                                    ->get()
                                    ->mapWithKeys(fn ($profile) => [
                                        $profile->id => ($profile->profile_name ?: 'Bank Profile')
                                            . ' — ' . ($profile->bank_name ?: 'Bank')
                                            . ' — ' . ($profile->currency ?: '-'),
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->visible(fn (callable $get) => $get('account_type') === TreasuryAccount::TYPE_BANK)
                            ->helperText('Only used for bank treasury accounts.'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('is_default')
                            ->label('Default For Same Type + Currency')
                            ->inline(false)
                            ->helperText('Only one default account per type and currency.'),
                    ])
                    ->columns(3),

                Section::make('Bank / Institution Info')
                    ->schema([
                        TextInput::make('institution_name')
                            ->label('Institution Name')
                            ->maxLength(255),

                        TextInput::make('branch_name')
                            ->label('Branch Name')
                            ->maxLength(255),

                        TextInput::make('account_holder_name')
                            ->label('Account Holder Name')
                            ->maxLength(255),

                        TextInput::make('account_number')
                            ->label('Account Number')
                            ->maxLength(255),

                        TextInput::make('iban')
                            ->label('IBAN')
                            ->maxLength(255),

                        TextInput::make('swift_code')
                            ->label('SWIFT Code')
                            ->maxLength(255),

                        TextInput::make('account_code')
                            ->label('Account Code')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Section::make('Balances & Notes')
                    ->schema([
                        TextInput::make('opening_balance')
                            ->label('Opening Balance')
                            ->numeric()
                            ->default(0),

                        TextInput::make('current_balance')
                            ->label('Current Balance')
                            ->numeric()
                            ->helperText('Leave empty to auto-use opening balance on create.'),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
