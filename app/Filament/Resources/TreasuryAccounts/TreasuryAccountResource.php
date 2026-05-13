<?php

namespace App\Filament\Resources\TreasuryAccounts;

use App\Filament\Resources\TreasuryAccounts\Pages\CreateTreasuryAccount;
use App\Filament\Resources\TreasuryAccounts\Pages\EditTreasuryAccount;
use App\Filament\Resources\TreasuryAccounts\Pages\ListTreasuryAccounts;
use App\Filament\Resources\TreasuryAccounts\Pages\ViewTreasuryAccount;
use App\Filament\Resources\TreasuryAccounts\Schemas\TreasuryAccountForm;
use App\Filament\Resources\TreasuryAccounts\Tables\TreasuryAccountsTable;
use App\Models\TreasuryAccount;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TreasuryAccountResource extends Resource
{
    protected static ?string $model = TreasuryAccount::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $navigationLabel = 'Treasury Accounts';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationParentItem = 'Treasury';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'account_name';

    public static function form(Schema $schema): Schema
    {
        return TreasuryAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TreasuryAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTreasuryAccounts::route('/'),
            'create' => CreateTreasuryAccount::route('/create'),
            'view' => ViewTreasuryAccount::route('/{record}'),
            'edit' => EditTreasuryAccount::route('/{record}/edit'),
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
