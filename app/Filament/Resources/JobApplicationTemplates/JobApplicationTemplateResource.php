<?php

namespace App\Filament\Resources\JobApplicationTemplates;

use App\Filament\Resources\JobApplicationTemplates\Pages\CreateJobApplicationTemplate;
use App\Filament\Resources\JobApplicationTemplates\Pages\EditJobApplicationTemplate;
use App\Filament\Resources\JobApplicationTemplates\Pages\ListJobApplicationTemplates;
use App\Filament\Resources\JobApplicationTemplates\Schemas\JobApplicationTemplateForm;
use App\Filament\Resources\JobApplicationTemplates\Tables\JobApplicationTemplatesTable;
use App\Models\JobApplicationTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JobApplicationTemplateResource extends Resource
{
    protected static ?string $model = JobApplicationTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Templates';

    protected static ?string $modelLabel = 'Template';

    protected static ?string $pluralModelLabel = 'Templates';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return JobApplicationTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationTemplatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplicationTemplates::route('/'),
            'create' => CreateJobApplicationTemplate::route('/create'),
            'edit' => EditJobApplicationTemplate::route('/{record}/edit'),
        ];
    }
}