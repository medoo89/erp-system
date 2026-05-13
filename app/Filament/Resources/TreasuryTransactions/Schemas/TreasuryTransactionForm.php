<?php

namespace App\Filament\Resources\TreasuryTransactions\Schemas;

use App\Models\Client;
use App\Models\Employment;
use App\Models\Project;
use App\Models\TreasuryAccount;
use App\Models\TreasuryTransaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;

class TreasuryTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaView::make('filament.resources.treasury-transactions.pages.form-hero')
                    ->columnSpanFull(),

                Section::make('Transaction Identity')
                    ->description('Basic transaction information. The transaction number is generated automatically after saving.')
                    ->schema([
                        Placeholder::make('transaction_no_display')
                            ->label('Transaction No')
                            ->content(fn ($record) => $record?->transaction_no ?: 'Auto-generated on save'),

                        DatePicker::make('transaction_date')
                            ->label('Transaction Date')
                            ->helperText('The official date of this treasury transaction.')
                            ->required()
                            ->default(now()),

                        Toggle::make('is_posted')
                            ->label('Posted')
                            ->helperText('When enabled, this transaction is treated as confirmed in treasury tracking.')
                            ->default(true),
                    ])
                    ->columns(3),

                Section::make('Account & Movement')
                    ->description('Choose the account, direction, transaction type, amount, and currency.')
                    ->schema([
                        Select::make('treasury_account_id')
                            ->label('Treasury Account')
                            ->helperText('The bank, cash, or clearing account affected by this transaction.')
                            ->options(fn () => TreasuryAccount::query()
                                ->orderBy('account_name')
                                ->pluck('account_name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('transaction_type')
                            ->label('Transaction Type')
                            ->helperText('Business category of this transaction, such as invoice payment, salary payment, expense payment, or manual adjustment.')
                            ->options(TreasuryTransaction::getTransactionTypeOptions())
                            ->required()
                            ->native(false),

                        Select::make('direction')
                            ->label('Direction')
                            ->helperText('Incoming increases the selected account. Outgoing decreases the selected account.')
                            ->options(TreasuryTransaction::getDirectionOptions())
                            ->required()
                            ->native(false),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->helperText('The transaction amount in the selected currency.')
                            ->numeric()
                            ->required(),

                        Select::make('currency')
                            ->label('Currency')
                            ->helperText('Currency used for this transaction.')
                            ->options(TreasuryAccount::getCurrencyOptions())
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Business Links')
                    ->description('Optional links for reporting and traceability.')
                    ->schema([
                        Select::make('client_id')
                            ->label('Client')
                            ->helperText('Optional. Link this transaction to a client when applicable.')
                            ->options(fn () => Client::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload(),

                        Select::make('project_id')
                            ->label('Project')
                            ->helperText('Optional. Link this transaction to a project when applicable.')
                            ->options(fn () => Project::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload(),

                        Select::make('employment_id')
                            ->label('Employment')
                            ->helperText('Optional. Link this transaction to an employee/employment when applicable.')
                            ->options(fn () => Employment::query()
                                ->orderBy('employee_name')
                                ->pluck('employee_name', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(3),

                Section::make('Reference & Notes')
                    ->description('Use references to connect treasury transactions with invoices, salary slips, expenses, transfers, or manual entries.')
                    ->schema([
                        Select::make('reference_type')
                            ->label('Reference Type')
                            ->helperText('The source document or workflow that created or explains this transaction.')
                            ->options([
                                'invoice' => 'Invoice',
                                'invoice_payment' => 'Invoice Payment',
                                'salary_slip' => 'Salary Slip',
                                'expense' => 'Expense',
                                'transfer' => 'Transfer',
                                'manual' => 'Manual',
                            ])
                            ->native(false),

                        TextInput::make('reference_id')
                            ->label('Reference ID')
                            ->helperText('Optional internal ID of the linked document or workflow.')
                            ->numeric(),

                        Textarea::make('description')
                            ->label('Description')
                            ->helperText('Short business description shown in treasury screens.')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->helperText('Internal notes for finance/admin users.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
