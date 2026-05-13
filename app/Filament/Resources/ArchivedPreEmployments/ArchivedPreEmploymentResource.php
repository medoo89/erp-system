<?php

namespace App\Filament\Resources\ArchivedPreEmployments;

use App\Filament\Resources\ArchivedPreEmployments\Pages\ListArchivedPreEmployments;
use App\Filament\Resources\ArchivedPreEmployments\Schemas\ArchivedPreEmploymentForm;
use App\Filament\Resources\ArchivedPreEmployments\Tables\ArchivedPreEmploymentsTable;
use App\Models\PreEmployment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArchivedPreEmploymentResource extends Resource
{
    protected static ?string $model = PreEmployment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Pre-Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'Archive';

    protected static ?int $navigationSort = 30;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function (Builder $query) {
                $query
                    ->where('is_archived', true)
                    ->orWhere('is_declined', true)
                    ->orWhereNotNull('declined_at');
            });
    }

    public static function form(Schema $schema): Schema
    {
        return ArchivedPreEmploymentForm::configure($schema);
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
