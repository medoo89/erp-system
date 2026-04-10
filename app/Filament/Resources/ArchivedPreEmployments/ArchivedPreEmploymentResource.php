<?php

namespace App\Filament\Resources\ArchivedPreEmployments;

use App\Filament\Resources\ArchivedPreEmployments\Pages\ListArchivedPreEmployments;
use App\Filament\Resources\ArchivedPreEmployments\Tables\ArchivedPreEmploymentsTable;
use App\Models\PreEmployment;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchivedPreEmploymentResource extends Resource
{
    protected static ?string $model = PreEmployment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Pre-Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function (Builder $query) {
                $query
                    ->where('is_archived', true)
                    ->orWhere('is_declined', true)
                    ->orWhereNotNull('declined_at')
                    ->orWhereNotNull('converted_to_employment_at');
            });
    }

    public static function table(Table $table): Table
    {
        return ArchivedPreEmploymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArchivedPreEmployments::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}