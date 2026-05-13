<?php

namespace App\Filament\Resources\BankProfiles\Pages;

use App\Filament\Resources\BankProfiles\BankProfileResource;
use App\Models\BankProfile;
use App\Models\TreasuryAccount;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBankProfile extends EditRecord
{
    public function getTitle(): string
    {
        return '';
    }

    public function getHeading(): string
    {
        return '';
    }

    protected static string $resource = BankProfileResource::class;

    protected function afterSave(): void
    {
        $this->syncTreasuryAccountsForProfile($this->record);
    }

    protected function syncTreasuryAccountsForProfile(BankProfile $profile): void
    {
        $profile->load('accounts');

        $activeCurrencyIds = [];

        foreach ($profile->accounts as $accountRow) {
            $currency = strtoupper((string) ($accountRow->currency ?? ''));

            if ($currency === '') {
                continue;
            }

            $treasury = TreasuryAccount::query()->firstOrNew([
                'bank_profile_id' => $profile->id,
                'currency' => $currency,
                'account_type' => TreasuryAccount::TYPE_BANK,
            ]);

            $treasury->account_name = ($profile->profile_name ?: $profile->bank_name ?: 'Bank Account') . ' - ' . $currency;
            $treasury->institution_name = $profile->bank_name;
            $treasury->branch_name = $profile->branch_name;
            $treasury->account_holder_name = $profile->beneficiary_name;
            $treasury->account_number = $accountRow->account_number;
            $treasury->iban = $accountRow->iban;
            $treasury->swift_code = $profile->swift_code;
            $treasury->account_code = null;
            $treasury->is_active = (bool) $accountRow->is_active && (bool) $profile->is_active;

            if (! $treasury->exists) {
                $treasury->opening_balance = 0;
                $treasury->current_balance = 0;
                $treasury->is_default = false;
            }

            $treasury->notes = trim(implode("\n", array_filter([
                $profile->notes,
                $accountRow->notes,
            ])));

            $treasury->save();

            $accountRow->treasury_account_id = $treasury->id;
            $accountRow->saveQuietly();

            $activeCurrencyIds[] = $currency;
        }

        TreasuryAccount::query()
            ->where('bank_profile_id', $profile->id)
            ->where('account_type', TreasuryAccount::TYPE_BANK)
            ->when(
                count($activeCurrencyIds) > 0,
                fn ($q) => $q->whereNotIn('currency', $activeCurrencyIds)
            )
            ->update([
                'is_active' => false,
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('bank_profiles', 'edit') ?? false);
    }

}
