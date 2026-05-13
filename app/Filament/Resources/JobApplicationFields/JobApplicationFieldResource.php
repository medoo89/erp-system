<?php

namespace App\Filament\Resources\JobApplicationFields;

use App\Filament\Resources\JobApplicationFields\Pages\CreateJobApplicationField;
use App\Filament\Resources\JobApplicationFields\Pages\EditJobApplicationField;
use App\Filament\Resources\JobApplicationFields\Pages\ListJobApplicationFields;
use App\Filament\Resources\JobApplicationFields\Schemas\JobApplicationFieldForm;
use App\Filament\Resources\JobApplicationFields\Tables\JobApplicationFieldsTable;
use App\Models\JobApplicationField;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class JobApplicationFieldResource extends Resource
{
    protected static ?string $model = JobApplicationField::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationLabel = 'Application Fields';

    protected static ?string $modelLabel = 'Application Field';

    protected static ?string $pluralModelLabel = 'Application Fields';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin Settings';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return JobApplicationFieldForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationFieldsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplicationFields::route('/'),
            'create' => CreateJobApplicationField::route('/create'),
            'edit' => EditJobApplicationField::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('application_fields', 'delete') ?? false);
    }

}
