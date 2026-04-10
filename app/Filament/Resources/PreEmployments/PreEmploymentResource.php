<?php

namespace App\Filament\Resources\PreEmployments;

use App\Filament\Resources\PreEmployments\Pages;
use App\Filament\Resources\PreEmployments\Schemas\PreEmploymentForm;
use App\Filament\Resources\PreEmployments\Tables\PreEmploymentsTable;
use App\Models\PreEmployment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PreEmploymentResource extends Resource
{
    protected static ?string $model = PreEmployment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?string $recordTitleAttribute = 'candidate_name';

    protected static ?string $navigationLabel = 'Pre-Employment';

    protected static ?string $modelLabel = 'Pre-Employment';

    protected static ?string $pluralModelLabel = 'Pre-Employment';

    protected static string|\UnitEnum|null $navigationGroup = 'HR';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_archived', false)
            ->where('is_declined', false)
            ->whereNull('declined_at')
            ->whereNull('converted_to_employment_at');
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
            'index' => Pages\ListPreEmployments::route('/'),
            'create' => Pages\CreatePreEmployment::route('/create'),
            'edit' => Pages\EditPreEmployment::route('/{record}/edit'),
        ];
    }
}