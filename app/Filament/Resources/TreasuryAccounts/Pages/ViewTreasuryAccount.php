<?php

namespace App\Filament\Resources\TreasuryAccounts\Pages;

use App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource;
use App\Models\TreasuryAccount;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewTreasuryAccount extends ViewRecord
{
    protected static string $resource = TreasuryAccountResource::class;

    protected string $view = 'filament.resources.treasury-accounts.pages.view-treasury-account-premium';

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return $this->record->account_name ?: ('Treasury Account #' . $this->record->id);
    }

    public function getSubheading(): string|Htmlable|null
    {
        $type = match ((string) $this->record->account_type) {
            TreasuryAccount::TYPE_BANK => 'Bank',
            TreasuryAccount::TYPE_CASH => 'Cash',
            TreasuryAccount::TYPE_CLEARING => 'Clearing',
            default => ucfirst((string) $this->record->account_type),
        };

        $currency = $this->record->currency ?: '-';
        $institution = $this->record->institution_name ?: 'No Institution';

        return "Type: {$type} · Currency: {$currency} · Institution: {$institution}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to Accounts')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(TreasuryAccountResource::getUrl('index')),

            EditAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('treasury', 'edit')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

}
