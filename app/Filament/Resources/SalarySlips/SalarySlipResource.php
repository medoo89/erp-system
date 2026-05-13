<?php

namespace App\Filament\Resources\SalarySlips;

use App\Filament\Resources\SalarySlips\Pages\CreateSalarySlip;
use App\Filament\Resources\SalarySlips\Pages\EditSalarySlip;
use App\Filament\Resources\SalarySlips\Pages\ListSalarySlips;
use App\Filament\Resources\SalarySlips\Pages\ViewSalarySlip;
use App\Filament\Resources\SalarySlips\Schemas\SalarySlipForm;
use App\Filament\Resources\SalarySlips\Tables\SalarySlipsTable;
use App\Models\SalarySlip;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SalarySlipResource extends Resource
{
    protected static ?string $model = SalarySlip::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Salary Slip';

    protected static ?string $modelLabel = 'Salary Slip';

    protected static ?string $pluralModelLabel = 'Salary Slips';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return SalarySlipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalarySlipsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalarySlips::route('/'),
            'create' => CreateSalarySlip::route('/create'),
            'view' => ViewSalarySlip::route('/{record}'),
            'edit' => EditSalarySlip::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (
            auth()->user()?->canErp('salary_slips', 'create')
            || auth()->user()?->canErp('employments', 'generate_salary_slip')
        );
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'delete') ?? false);
    }

}
