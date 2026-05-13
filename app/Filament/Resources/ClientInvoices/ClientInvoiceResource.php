<?php

namespace App\Filament\Resources\ClientInvoices;

use App\Filament\Resources\ClientInvoices\Pages\CreateClientInvoice;
use App\Filament\Resources\ClientInvoices\Pages\EditClientInvoice;
use App\Filament\Resources\ClientInvoices\Pages\ListClientInvoices;
use App\Filament\Resources\ClientInvoices\Pages\ViewClientInvoice;
use App\Filament\Resources\ClientInvoices\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\ClientInvoices\Schemas\ClientInvoiceForm;
use App\Filament\Resources\ClientInvoices\Tables\ClientInvoicesTable;
use App\Models\ClientInvoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ClientInvoiceResource extends Resource
{
    protected static ?string $model = ClientInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Client Invoices';

    protected static ?string $modelLabel = 'Client Invoice';

    protected static ?string $pluralModelLabel = 'Client Invoices';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ClientInvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientInvoicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientInvoices::route('/'),
            'create' => CreateClientInvoice::route('/create'),
            'view' => ViewClientInvoice::route('/{record}'),
            'edit' => EditClientInvoice::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'delete') ?? false);
    }

}
