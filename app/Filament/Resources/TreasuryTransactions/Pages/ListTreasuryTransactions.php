<?php

namespace App\Filament\Resources\TreasuryTransactions\Pages;

use App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource;
use App\Models\TreasuryTransaction;
use Filament\Resources\Pages\ListRecords;

class ListTreasuryTransactions extends ListRecords
{
    protected static string $resource = TreasuryTransactionResource::class;

    protected string $view = 'filament.resources.treasury-transactions.pages.list-treasury-transactions-premium';

    public function getHeading(): string
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getViewData(): array
    {
        return [
            'transactionsCount' => TreasuryTransaction::query()->count(),
            'incomingCount' => TreasuryTransaction::query()->where('direction', 'in')->count(),
            'outgoingCount' => TreasuryTransaction::query()->where('direction', 'out')->count(),
            'postedCount' => TreasuryTransaction::query()->where('is_posted', true)->count(),
        ];
    }

    public function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record): string => TreasuryTransactionResource::getUrl('view', ['record' => $record]);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }
}
