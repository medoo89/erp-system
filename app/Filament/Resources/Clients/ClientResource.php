<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Filament\Resources\Clients\RelationManagers\ProjectsRelationManager;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Clients';

    protected static ?string $modelLabel = 'Client';

    protected static ?string $pluralModelLabel = 'Clients';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table)
            ->recordUrl(fn ($record) => url('/admin/clients/' . $record->id . '/view'));
}

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),            'edit' => EditClient::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'delete') ?? false);
    }

}
