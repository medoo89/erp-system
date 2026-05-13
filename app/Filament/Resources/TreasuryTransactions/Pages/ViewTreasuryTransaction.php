<?php

namespace App\Filament\Resources\TreasuryTransactions\Pages;

use App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTreasuryTransaction extends ViewRecord
{
    protected static string $resource = TreasuryTransactionResource::class;

    protected string $view = 'filament.resources.treasury-transactions.pages.view-treasury-transaction-premium';


    public function getHeading(): string
    {
        return '';
    }


    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('treasury', 'edit')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

}
