<?php

namespace App\Filament\Resources\TreasuryTransactions;

use App\Filament\Resources\TreasuryTransactions\Pages\CreateTreasuryTransaction;
use App\Filament\Resources\TreasuryTransactions\Pages\EditTreasuryTransaction;
use App\Filament\Resources\TreasuryTransactions\Pages\ListTreasuryTransactions;
use App\Filament\Resources\TreasuryTransactions\Pages\ViewTreasuryTransaction;
use App\Filament\Resources\TreasuryTransactions\Schemas\TreasuryTransactionForm;
use App\Filament\Resources\TreasuryTransactions\Tables\TreasuryTransactionsTable;
use App\Models\TreasuryTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TreasuryTransactionResource extends Resource
{
    protected static ?string $model = TreasuryTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Treasury Transactions';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationParentItem = 'Treasury';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'transaction_no';

    public static function form(Schema $schema): Schema
    {
        return TreasuryTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TreasuryTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTreasuryTransactions::route('/'),
            'create' => CreateTreasuryTransaction::route('/create'),
            'view' => ViewTreasuryTransaction::route('/{record}'),
            'edit' => EditTreasuryTransaction::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'delete') ?? false);
    }

}
