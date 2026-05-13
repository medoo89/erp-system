<?php

namespace App\Filament\Resources\TreasuryOperations;

use App\Filament\Resources\TreasuryOperations\Pages\CreateTreasuryOperation;
use App\Filament\Resources\TreasuryOperations\Pages\EditTreasuryOperation;
use App\Filament\Resources\TreasuryOperations\Pages\ListTreasuryOperations;
use App\Filament\Resources\TreasuryOperations\Pages\ViewTreasuryOperation;
use App\Filament\Resources\TreasuryOperations\Schemas\TreasuryOperationForm;
use App\Filament\Resources\TreasuryOperations\Tables\TreasuryOperationsTable;
use App\Models\TreasuryOperation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TreasuryOperationResource extends Resource
{
    protected static ?string $model = TreasuryOperation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPathRoundedSquare;

    protected static ?string $navigationLabel = 'Treasury Operations';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationParentItem = 'Treasury';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return TreasuryOperationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TreasuryOperationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTreasuryOperations::route('/'),
            'create' => CreateTreasuryOperation::route('/create'),
            'view' => ViewTreasuryOperation::route('/{record}'),
            'edit' => EditTreasuryOperation::route('/{record}/edit'),
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
