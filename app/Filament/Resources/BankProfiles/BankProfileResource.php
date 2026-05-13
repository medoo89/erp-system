<?php

namespace App\Filament\Resources\BankProfiles;

use App\Filament\Resources\BankProfiles\Pages\CreateBankProfile;
use App\Filament\Resources\BankProfiles\Pages\EditBankProfile;
use App\Filament\Resources\BankProfiles\Pages\ListBankProfiles;
use App\Filament\Resources\BankProfiles\Pages\ViewBankProfile;
use App\Filament\Resources\BankProfiles\Schemas\BankProfileForm;
use App\Filament\Resources\BankProfiles\Tables\BankProfilesTable;
use App\Models\BankProfile;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BankProfileResource extends Resource
{
    protected static ?string $model = BankProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Bank Profiles';

    protected static ?string $modelLabel = 'Bank Profile';

    protected static ?string $pluralModelLabel = 'Bank Profiles';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationParentItem = 'Treasury';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return BankProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankProfilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankProfiles::route('/'),
            'create' => CreateBankProfile::route('/create'),
            'view' => ViewBankProfile::route('/{record}'),
            'edit' => EditBankProfile::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'delete') ?? false);
    }

}
