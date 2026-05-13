<?php

namespace App\Filament\Resources\Employments;

use App\Filament\Resources\Employments\Pages\CreateEmployment;
use App\Filament\Resources\Employments\Pages\EditEmployment;
use App\Filament\Resources\Employments\Pages\ListEmployments;
use App\Filament\Resources\Employments\Pages\ViewEmployment;
use App\Filament\Resources\Employments\Schemas\EmploymentForm;
use App\Filament\Resources\Employments\Tables\EmploymentsTable;
use App\Models\Employment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmploymentResource extends Resource
{
    protected static ?string $model = Employment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Employment';

    protected static ?string $modelLabel = 'Employment';

    protected static ?string $pluralModelLabel = 'Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 30;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Schema $schema): Schema
    {
        return EmploymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmploymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployments::route('/'),
            'create' => CreateEmployment::route('/create'),
            'view' => ViewEmployment::route('/{record}'),
            'edit' => EditEmployment::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'create') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'edit') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'view') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'delete') ?? false);
    }

}
