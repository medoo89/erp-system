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
use Filament\Tables\Table;

class JobApplicationTemplateResource extends Resource
{
    protected static ?string $model = JobApplicationTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Templates';

    protected static ?string $modelLabel = 'Template';

    protected static ?string $pluralModelLabel = 'Templates';

    protected static string|\UnitEnum|null $navigationGroup = 'Admin Settings';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return JobApplicationTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JobApplicationTemplatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJobApplicationTemplates::route('/'),
            'create' => CreateJobApplicationTemplate::route('/create'),
            'edit' => EditJobApplicationTemplate::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('application_templates', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('application_templates', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('application_templates', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('application_templates', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('application_templates', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('application_templates', 'delete') ?? false);
    }

}
