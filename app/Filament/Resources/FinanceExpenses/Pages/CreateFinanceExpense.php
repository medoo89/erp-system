<?php

namespace App\Filament\Resources\FinanceExpenses\Pages;

use App\Filament\Resources\FinanceExpenses\FinanceExpenseResource;
use App\Models\FinanceExpense;
use Filament\Resources\Pages\CreateRecord;

class CreateFinanceExpense extends CreateRecord
{
    protected static string $resource = FinanceExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? FinanceExpense::STATUS_DRAFT;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record) {
            $this->record->refresh();
            $this->record->syncTreasuryPosting();
            $this->record->refresh();
        }
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'create') ?? false);
    }

}
