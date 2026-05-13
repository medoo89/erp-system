<?php

namespace App\Filament\Resources\ClientInvoices\Schemas;

use App\Models\BankProfile;
use App\Models\Client;
use App\Models\ClientInvoice;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice Header')
                ->schema([
                    TextInput::make('invoice_number')
                        ->label('Invoice Number')
                        ->required()
                        ->maxLength(255),

                    DatePicker::make('invoice_date')
                        ->label('Invoice Date')
                        ->native(false),

                    Select::make('status')
                        ->label('Status')
                        ->options(ClientInvoice::statusOptions())
                        ->default(ClientInvoice::STATUS_DRAFT)
                        ->required()
                        ->native(false),

                    Select::make('client_id')
                        ->label('Client')
                        ->options(
                            Client::query()->orderBy('name')->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),

                    Select::make('project_id')
                        ->label('Project')
                        ->options(
                            Project::query()->orderBy('name')->pluck('name', 'id')->toArray()
                        )
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DatePicker::make('period_start')
                        ->label('Invoice Period Start')
                        ->native(false),

                    DatePicker::make('period_end')
                        ->label('Invoice Period End')
                        ->native(false),

                    TextInput::make('payment_terms_label')
                        ->label('Payment Terms'),

                    TextInput::make('bill_to_name')
                        ->label('Bill To Name'),

                    Textarea::make('bill_to_address')
                        ->label('Bill To Address')
                        ->rows(3),

                    TextInput::make('bill_to_phone')
                        ->label('Bill To Phone'),
                ])
                ->columns(3),

            Section::make('Currency Split')
                ->schema([
                    Select::make('foreign_currency')
                        ->label('Foreign Currency')
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->native(false),

                    TextInput::make('foreign_percentage')
                        ->label('Foreign %')
                        ->numeric(),

                    Select::make('local_currency')
                        ->label('Local Currency')
                        ->options([
                            'LYD' => 'LYD',
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                        ])
                        ->native(false),

                    TextInput::make('local_percentage')
                        ->label('Local %')
                        ->numeric(),

                    TextInput::make('exchange_rate')
                        ->label('Exchange Rate')
                        ->numeric(),

                    Select::make('display_currency')
                        ->label('Display Currency')
                        ->options([
                            'USD' => 'USD',
                            'EUR' => 'EUR',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->native(false),
                ])
                ->columns(3),

            Section::make('Amounts')
                ->schema([
                    TextInput::make('subtotal_amount')->label('Subtotal')->numeric(),
                    TextInput::make('tax_percent')->label('Tax %')->numeric(),
                    TextInput::make('tax_amount')->label('Tax Amount')->numeric(),
                    TextInput::make('total_amount')->label('Total')->numeric(),
                    TextInput::make('foreign_amount_due')->label('Foreign Amount Due')->numeric(),
                    TextInput::make('local_amount_due')->label('Local Amount Due')->numeric(),
                    TextInput::make('local_amount_foreign_equivalent')->label('Local Amount Foreign Equivalent')->numeric(),
                ])
                ->columns(3),

            Section::make('Invoice Lines')
                ->description('Each employee line uses that employee’s own client billing rate from the finance profile.')
                ->schema([
                    Repeater::make('lines')
                        ->relationship('lines')
                        ->schema([
                            TextInput::make('service_title')
                                ->label('Service Title')
                                ->maxLength(255),

                            TextInput::make('candidate_name')
                                ->label('Employee Name')
                                ->maxLength(255),

                            TextInput::make('position_title')
                                ->label('Position')
                                ->maxLength(255),

                            TextInput::make('project_name')
                                ->label('Project Name')
                                ->maxLength(255),

                            DatePicker::make('service_period_start')
                                ->label('Service Start')
                                ->native(false),

                            DatePicker::make('service_period_end')
                                ->label('Service End')
                                ->native(false),

                            TextInput::make('service_month_label')
                                ->label('Service Month')
                                ->maxLength(255),

                            TextInput::make('quantity')
                                ->label('Paid Days')
                                ->numeric(),

                            TextInput::make('unit_rate')
                                ->label('Client Billing Rate')
                                ->numeric(),

                            TextInput::make('amount')
                                ->label('Line Amount')
                                ->numeric(),

                            Select::make('currency')
                                ->label('Billing Currency')
                                ->options([
                                    'USD' => 'USD',
                                    'EUR' => 'EUR',
                                    'GBP' => 'GBP',
                                    'LYD' => 'LYD',
                                ])
                                ->native(false),

                            Textarea::make('scope_description')
                                ->label('Scope / Description')
                                ->rows(3)
                                ->columnSpanFull(),

                            Textarea::make('line_notes')
                                ->label('Internal Notes')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                        ->defaultItems(0),
                ]),

            Section::make('Bank & Terms')
                ->schema([
                    Select::make('bank_profile_id')
                        ->label('Bank Profile')
                        ->options(function () {
                            return BankProfile::query()
                                ->where('is_active', true)
                                ->orderByDesc('is_default_for_invoices')
                                ->orderBy('profile_name')
                                ->get()
                                ->mapWithKeys(fn (BankProfile $profile) => [
                                    $profile->id => ($profile->profile_name ?: 'Bank Profile')
                                        . ' — ' . ($profile->bank_name ?: 'Bank')
                                        . ' — Treasury: ' . ($profile->treasuryAccount?->account_name ?: 'No Treasury Account'),
                                ])
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->live()
                        ->native(false)
                        ->afterStateUpdated(function ($state, $set, $get) {
                            if (blank($state)) {
                                return;
                            }

                            $profile = BankProfile::query()->find($state);

                            if (! $profile) {
                                return;
                            }

                            $set('bank_name', $profile->bank_name);
                            $set('swift_code', $profile->swift_code);

                            $currency = strtoupper((string) ($profile->currency ?: ''));

                            if ($currency === 'USD') {
                                $set('iban_usd', $profile->iban);
                            }

                            if ($currency === 'EUR') {
                                $set('iban_eur', $profile->iban);
                            }

                            if ($currency === 'LYD') {
                                $set('iban_lyd', $profile->iban);
                                $set('account_number_lyd', $profile->account_number);
                            }
                        }),

                    TextInput::make('bank_name')->label('Bank Name'),

                    TextInput::make('swift_code')->label('Swift Code'),

                    TextInput::make('account_number_lyd')->label('Account Number LYD'),

                    TextInput::make('iban_lyd')->label('IBAN LYD'),

                    TextInput::make('iban_usd')->label('IBAN USD'),

                    TextInput::make('iban_eur')->label('IBAN EUR'),

                    Textarea::make('notes')->label('Notes')->rows(4)->columnSpanFull(),
                    Textarea::make('terms_text')->label('Terms Text')->rows(6)->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }
}
