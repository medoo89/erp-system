<?php

namespace App\Filament\Resources\ArchivedJobApplications;

use App\Filament\Resources\ArchivedJobApplications\Pages\ListArchivedJobApplications;
use App\Filament\Resources\ArchivedJobApplications\Tables\ArchivedJobApplicationsTable;
use App\Models\JobApplication;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchivedJobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Job Applications';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_archived', true)
            ->where(function (Builder $query) {
                $query
                    ->where('archive_reason', 'declined')
                    ->orWhere('archive_reason', 'archived_manually')
                    ->orWhereNull('archive_reason');
            });
    }

    public static function table(Table $table): Table
    {
        return ArchivedJobApplicationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArchivedJobApplications::route('/'),
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
        return false;
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
