<?php

namespace App\Filament\Resources\ClientInvoices\RelationManagers;

use App\Models\ClientInvoicePayment;
use App\Models\TreasuryAccount;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Invoice Receipts';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('payment_date')
                ->label('Receipt Date')
                ->required(),

            TextInput::make('amount')
                ->label('Receipt Amount')
                ->numeric()
                ->required(),

            Select::make('currency')
                ->label('Receipt Currency')
                ->options(ClientInvoicePayment::currencyOptions())
                ->required()
                ->native(false),

            Select::make('applies_to')
                ->label('Applies To')
                ->options(ClientInvoicePayment::appliesToOptions())
                ->required()
                ->native(false),

            TextInput::make('exchange_rate')
                ->label('Exchange Rate')
                ->numeric(),

            TextInput::make('reference_no')
                ->label('Reference No')
                ->maxLength(255),

            Select::make('treasury_account_id')
                ->label('Treasury Account')
                ->options(function () {
                    return TreasuryAccount::query()
                        ->orderBy('account_name')
                        ->get()
                        ->mapWithKeys(fn ($item) => [
                            $item->id => ($item->account_name ?: 'Treasury Account') . ' — ' . ($item->currency ?: '-'),
                        ])
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->required()
                ->native(false),

            FileUpload::make('attachment_path')
                ->label('Receipt Attachment')
                ->directory('client-invoice-payments')
                ->visibility('private')
                ->downloadable()
                ->openable()
                ->previewable(false),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(4),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Date')
                    ->date(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('currency')
                    ->label('Currency')
                    ->badge(),

                Tables\Columns\TextColumn::make('applies_to')
                    ->label('Applies To')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ClientInvoicePayment::appliesToOptions()[$state] ?? $state),

                Tables\Columns\TextColumn::make('amount_in_invoice_currency')
                    ->label('In Invoice Currency')
                    ->numeric(decimalPlaces: 2),

                Tables\Columns\TextColumn::make('treasuryAccount.account_name')
                    ->label('Treasury Account')
                    ->default('-'),

                Tables\Columns\IconColumn::make('treasury_transaction_id')
                    ->label('Posted')
                    ->boolean()
                    ->state(fn ($record) => filled($record->treasury_transaction_id)),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) (auth()->user()?->canErp('client_invoices', 'record_payment') || auth()->user()?->canErp('treasury', 'receive'))),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) (auth()->user()?->canErp('client_invoices', 'record_payment') || auth()->user()?->canErp('client_invoices', 'edit'))),
                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('client_invoices', 'delete')),
            ]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'view') ?? false);
    }
}
