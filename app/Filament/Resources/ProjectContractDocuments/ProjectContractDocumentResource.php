<?php

namespace App\Filament\Resources\ProjectContractDocuments;

use App\Filament\Resources\ProjectContractDocuments\Pages\CreateProjectContractDocument;
use App\Filament\Resources\ProjectContractDocuments\Pages\EditProjectContractDocument;
use App\Filament\Resources\ProjectContractDocuments\Pages\ListProjectContractDocuments;
use App\Filament\Resources\ProjectContractDocuments\Schemas\ProjectContractDocumentForm;
use App\Filament\Resources\ProjectContractDocuments\Tables\ProjectContractDocumentsTable;
use App\Models\ProjectContractDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProjectContractDocumentResource extends Resource
{
    protected static ?string $model = ProjectContractDocument::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = 'Contract Documents';

    protected static ?string $modelLabel = 'Contract Document';

    protected static ?string $pluralModelLabel = 'Contract Documents';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 26;

    public static function form(Schema $schema): Schema
    {
        return ProjectContractDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectContractDocumentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectContractDocuments::route('/'),
            'create' => CreateProjectContractDocument::route('/create'),
            'edit' => EditProjectContractDocument::route('/{record}/edit'),
        ];
    }
}
