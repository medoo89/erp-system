<?php

namespace App\Filament\Resources\InvoiceProfiles\Schemas;

use App\Models\InvoiceProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile')
                ->schema([
                    TextInput::make('name')
                        ->label('Profile Name')
                        ->required()
                        ->maxLength(255),

                    Select::make('currency')
                        ->label('Preferred Currency')
                        ->options(InvoiceProfile::currencyOptions())
                        ->native(false),

                    Toggle::make('is_default')
                        ->label('Default Profile')
                        ->default(false),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->columns(2),

            Section::make('Bank Details')
                ->schema([
                    TextInput::make('bank_name')->label('Bank Name'),
                    TextInput::make('swift_code')->label('Swift Code'),
                    TextInput::make('account_number_lyd')->label('Account Number LYD'),
                    TextInput::make('iban_lyd')->label('IBAN LYD'),
                    TextInput::make('iban_usd')->label('IBAN USD'),
                    TextInput::make('iban_eur')->label('IBAN EUR'),
                ])
                ->columns(2),

            Section::make('Default Terms')
                ->schema([
                    Textarea::make('terms_text')
                        ->label('Terms Text')
                        ->rows(10)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
