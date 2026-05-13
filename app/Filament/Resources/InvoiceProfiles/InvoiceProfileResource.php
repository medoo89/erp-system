<?php

namespace App\Filament\Resources\InvoiceProfiles;

use App\Filament\Resources\InvoiceProfiles\Pages\CreateInvoiceProfile;
use App\Filament\Resources\InvoiceProfiles\Pages\EditInvoiceProfile;
use App\Filament\Resources\InvoiceProfiles\Pages\ListInvoiceProfiles;
use App\Filament\Resources\InvoiceProfiles\Pages\ViewInvoiceProfile;
use App\Filament\Resources\InvoiceProfiles\Schemas\InvoiceProfileForm;
use App\Filament\Resources\InvoiceProfiles\Tables\InvoiceProfilesTable;
use App\Models\InvoiceProfile;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class InvoiceProfileResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = InvoiceProfile::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Invoice Profiles';

    protected static ?string $modelLabel = 'Invoice Profile';

    protected static ?string $pluralModelLabel = 'Invoice Profiles';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return InvoiceProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoiceProfilesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoiceProfiles::route('/'),
            'create' => CreateInvoiceProfile::route('/create'),
            'view' => ViewInvoiceProfile::route('/{record}'),
            'edit' => EditInvoiceProfile::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('invoice_profiles', 'delete') ?? false);
    }

}
