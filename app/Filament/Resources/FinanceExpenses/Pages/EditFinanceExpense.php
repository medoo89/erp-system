<?php

namespace App\Filament\Resources\FinanceExpenses\Pages;

use App\Filament\Resources\FinanceExpenses\FinanceExpenseResource;
use App\Models\FinanceExpense;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFinanceExpense extends EditRecord
{
    protected static string $resource = FinanceExpenseResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = $data['status'] ?? FinanceExpense::STATUS_DRAFT;

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record) {
            $this->record->refresh();
            $this->record->syncTreasuryPosting();
            $this->record->refresh();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'delete')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'edit') ?? false);
    }

}
