<?php

namespace App\Filament\Resources\ArchivedJobOpenings;

use App\Filament\Resources\ArchivedJobOpenings\Pages\ListArchivedJobOpenings;
use App\Filament\Resources\ArchivedJobOpenings\Tables\ArchivedJobOpeningsTable;
use App\Models\Job;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchivedJobOpeningResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Job Openings';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_archived', true);
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
        return true;
    }
}