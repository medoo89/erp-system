<?php

namespace App\Filament\Resources\TreasuryAccounts\Pages;

use App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource;
use App\Models\TreasuryAccount;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTreasuryAccounts extends ListRecords
{
    protected static string $resource = TreasuryAccountResource::class;

    protected string $view = 'filament.resources.treasury-accounts.pages.list-treasury-accounts-boxes';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('treasury', 'create')),
        ];
    }

    public function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record): string => TreasuryAccountResource::getUrl('view', ['record' => $record]);
    }

    public function getViewData(): array
    {
        $accounts = TreasuryAccount::query()
            ->where('is_active', true)
            ->orderByRaw("
                CASE account_type
                    WHEN 'bank' THEN 1
                    WHEN 'cash' THEN 2
                    WHEN 'clearing' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('institution_name')
            ->orderBy('currency')
            ->orderBy('account_name')
            ->get()
            ->groupBy('account_type');

        return [
            'bankAccounts' => $accounts->get(TreasuryAccount::TYPE_BANK, collect()),
            'cashAccounts' => $accounts->get(TreasuryAccount::TYPE_CASH, collect()),
            'clearingAccounts' => $accounts->get(TreasuryAccount::TYPE_CLEARING, collect()),
            'allAccountsCount' => $accounts->flatten(1)->count(),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

}
