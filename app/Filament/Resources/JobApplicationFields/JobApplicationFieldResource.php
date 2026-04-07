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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JobApplicationFieldResource extends Resource
{
    protected static ?string $model = JobApplicationField::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $recordTitleAttribute = 'label';

    protected static ?string $navigationLabel = 'Application Fields';

    protected static ?string $modelLabel = 'Application Field';

    protected static ?string $pluralModelLabel = 'Application Fields';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return JobApplicationFieldForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationFieldsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\RelationManagers\JobApplicationFieldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplicationFields::route('/'),
            'create' => CreateJobApplicationField::route('/create'),
            'edit' => EditJobApplicationField::route('/{record}/edit'),
        ];
    }
}