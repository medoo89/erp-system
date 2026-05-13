<?php

namespace App\Filament\Resources\FinanceExpenses;

use App\Filament\Resources\FinanceExpenses\Pages\CreateFinanceExpense;
use App\Filament\Resources\FinanceExpenses\Pages\EditFinanceExpense;
use App\Filament\Resources\FinanceExpenses\Pages\ListFinanceExpenses;
use App\Filament\Resources\FinanceExpenses\Pages\ViewPreEmployment;
use App\Filament\Resources\FinanceExpenses\Pages\ViewFinanceExpense;
use App\Filament\Resources\FinanceExpenses\RelationManagers\AllocationsRelationManager;
use App\Filament\Resources\FinanceExpenses\RelationManagers\TravelDetailRelationManager;
use App\Filament\Resources\FinanceExpenses\Schemas\FinanceExpenseForm;
use App\Filament\Resources\FinanceExpenses\Tables\FinanceExpensesTable;
use App\Models\FinanceExpense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FinanceExpenseResource extends Resource
{
    protected static ?string $model = FinanceExpense::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Finance Expenses';

    protected static ?string $modelLabel = 'Finance Expense';

    protected static ?string $pluralModelLabel = 'Finance Expenses';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 40;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'jobApplication',
            'preEmployment',
            'employment',
            'employmentRotation.employment',
            'client',
            'project',
            'treasuryAccount',
            'treasuryTransaction',
            'allocations',
            'travelDetail',
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return FinanceExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FinanceExpensesTable::configure($table)
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [
            TravelDetailRelationManager::class,
            AllocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFinanceExpenses::route('/'),
            'create' => CreateFinanceExpense::route('/create'),
            'view' => ViewFinanceExpense::route('/{record}'),
            'edit' => EditFinanceExpense::route('/{record}/edit'),
        ];
    }


    public static function canViewAny(): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'create') ?? false);
    }

    public static function canView($record): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }

    public static function canEdit($record): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'edit') ?? false);
    }

    public static function canDelete($record): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'delete') ?? false);
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'delete') ?? false);
    }

}
