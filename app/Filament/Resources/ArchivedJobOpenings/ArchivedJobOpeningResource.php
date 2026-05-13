<?php

namespace App\Filament\Resources\ArchivedJobOpenings;

use App\Filament\Resources\ArchivedJobOpenings\Pages\ListArchivedJobOpenings;
use App\Filament\Resources\ArchivedJobOpenings\Tables\ArchivedJobOpeningsTable;
use App\Models\Job;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchivedJobOpeningResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Job Openings';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Archived Job Opening';

    protected static ?string $pluralModelLabel = 'Archived Job Openings';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['project.client'])
            ->where('is_archived', true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return ArchivedJobOpeningsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArchivedJobOpenings::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'edit') ?? auth()->user()?->canErp('jobs', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('archive', 'delete') ?? false);
    }
}
