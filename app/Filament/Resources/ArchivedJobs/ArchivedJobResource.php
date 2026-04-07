<?php

namespace App\Filament\Resources\ArchivedJobs;

use App\Filament\Resources\ArchivedJobs\Pages;
use App\Filament\Resources\ArchivedJobs\Tables\ArchivedJobsTable;
use App\Models\Job;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchivedJobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $navigationLabel = 'Archived Job Openings';

    protected static ?string $modelLabel = 'Archived Job Opening';

    protected static ?string $pluralModelLabel = 'Archived Job Openings';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return ArchivedJobsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArchivedJobs::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_archived', true);
    }
}