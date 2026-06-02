<?php

namespace App\Filament\Resources\ProjectContracts;

use App\Filament\Resources\ProjectContracts\Pages\CreateProjectContract;
use App\Filament\Resources\ProjectContracts\Pages\EditProjectContract;
use App\Filament\Resources\ProjectContracts\Pages\ListProjectContracts;
use App\Models\ProjectContract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProjectContractResource extends Resource
{
    protected static ?string $model = ProjectContract::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Project Contracts';

    protected static ?string $modelLabel = 'Project Contract';

    protected static ?string $pluralModelLabel = 'Project Contracts';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 18;

    public static function form(Schema $schema): Schema
    {
        return \App\Filament\Resources\ProjectContracts\Schemas\ProjectContractForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return \App\Filament\Resources\ProjectContracts\Tables\ProjectContractsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectContracts::route('/'),
            'create' => CreateProjectContract::route('/create'),
            'edit' => EditProjectContract::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'view') ?? true);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'edit') ?? true);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'edit') ?? true);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('projects', 'delete') ?? false);
    }
}
