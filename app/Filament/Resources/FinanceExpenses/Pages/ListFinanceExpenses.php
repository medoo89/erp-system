<?php

namespace App\Filament\Resources\FinanceExpenses\Pages;

use App\Filament\Resources\FinanceExpenses\FinanceExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFinanceExpenses extends ListRecords
{
    
    protected string $view = 'filament.resources.finance-expenses.pages.list-finance-expenses-premium';
protected static string $resource = FinanceExpenseResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'create'))
                ->label('Add Expense'),
        ];
    }

    public function getTitle(): string
    {
        return 'Finance Expenses';
    }

    public function getSubheading(): ?string
    {
        return 'Track and manage finance expenses across pre-employment, employment, rotation, and ad hoc operations.';
    }

    public function getView(): string
    {
        return 'filament.resources.finance-expenses.pages.list-finance-expenses-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }

}
