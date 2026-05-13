<?php

namespace App\Filament\Resources\BankProfiles\Pages;

use App\Filament\Resources\BankProfiles\BankProfileResource;
use Filament\Resources\Pages\ViewRecord;

class ViewBankProfile extends ViewRecord
{
    protected static string $resource = BankProfileResource::class;

    protected string $view = 'filament.resources.bank-profiles.pages.view-bank-profile-premium';

    public function getTitle(): string
    {
        return (string) ($this->record->bank_name ?: 'Bank Profile');
    }

    public function getViewData(): array
    {
        $profile = $this->record->loadMissing(['accounts.treasuryAccount']);

        return [
            'profile' => $profile,
            'accounts' => $profile->accounts,
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'view') ?? false);
    }

}
