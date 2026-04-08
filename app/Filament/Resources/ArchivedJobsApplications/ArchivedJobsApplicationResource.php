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

    protected static ?string $navigationLabel = 'Archived Job Applications';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_archived', true);
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
        return true;
    }
}