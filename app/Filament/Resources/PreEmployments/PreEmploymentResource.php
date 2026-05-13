<?php

namespace App\Filament\Resources\PreEmployments;

use App\Filament\Resources\PreEmployments\Pages\CreatePreEmployment;
use App\Filament\Resources\PreEmployments\Pages\EditPreEmployment;
use App\Filament\Resources\PreEmployments\Pages\ListPreEmployments;
use App\Filament\Resources\PreEmployments\Pages\ViewPreEmployment;
use App\Filament\Resources\PreEmployments\Schemas\PreEmploymentForm;
use App\Filament\Resources\PreEmployments\Tables\PreEmploymentsTable;
use App\Models\PreEmployment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PreEmploymentResource extends Resource
{
    protected static ?string $model = PreEmployment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Pre-Employment';

    protected static ?string $modelLabel = 'Pre-Employment';

    protected static ?string $pluralModelLabel = 'Pre-Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'job.project.client',
            'jobApplication',
            'assignedHrUser',
            'files',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return PreEmploymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreEmploymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPreEmployments::route('/'),
            'create' => CreatePreEmployment::route('/create'),
            'view' => ViewPreEmployment::route('/{record}'),
            'edit' => EditPreEmployment::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'delete') ?? false);
    }

}
