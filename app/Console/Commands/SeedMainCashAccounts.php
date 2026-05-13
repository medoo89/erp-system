<?php

namespace App\Console\Commands;

use App\Models\TreasuryAccount;
use Illuminate\Console\Command;

class SeedMainCashAccounts extends Command
{
    protected $signature = 'treasury:seed-main-cash';

    protected $description = 'Create or update main cash and clearing treasury accounts by currency';

    public function handle(): int
    {
        $currencies = ['LYD', 'USD', 'EUR', 'GBP'];

        foreach ($currencies as $currency) {
            $this->upsertCashAccount($currency);
            $this->upsertClearingAccount($currency);
        }

        $this->newLine();
        $this->info('Main cash and clearing accounts seeded successfully.');

        return self::SUCCESS;
    }

    protected function upsertCashAccount(string $currency): void
    {
        $account = TreasuryAccount::query()->firstOrNew([
            'account_type' => TreasuryAccount::TYPE_CASH,
            'currency' => $currency,
            'account_name' => 'Main Cash - ' . $currency,
        ]);

        $isNew = ! $account->exists;

        $account->institution_name = 'Sada Fezzan Main Cash';
        $account->branch_name = null;
        $account->account_holder_name = 'Sada Fezzan';
        $account->account_number = null;
        $account->iban = null;
        $account->swift_code = null;
        $account->account_code = 'MAIN-CASH-' . $currency;
        $account->bank_profile_id = null;
        $account->is_active = true;

        if ($isNew) {
            $account->opening_balance = 0;
            $account->current_balance = 0;
            $account->is_default = true;
        }

        $account->notes = 'System main cash account for ' . $currency . '.';
        $account->save();

        $this->info(($isNew ? 'Created: ' : 'Updated: ') . $account->account_name);
    }

    protected function upsertClearingAccount(string $currency): void
    {
        $account = TreasuryAccount::query()->firstOrNew([
            'account_type' => TreasuryAccount::TYPE_CLEARING,
            'currency' => $currency,
            'account_name' => 'Clearing - ' . $currency,
        ]);

        $isNew = ! $account->exists;

        $account->institution_name = 'Sada Fezzan Clearing';
        $account->branch_name = null;
        $account->account_holder_name = 'Sada Fezzan';
        $account->account_number = null;
        $account->iban = null;
        $account->swift_code = null;
        $account->account_code = 'CLEARING-' . $currency;
        $account->bank_profile_id = null;
        $account->is_active = true;

        if ($isNew) {
            $account->opening_balance = 0;
            $account->current_balance = 0;
            $account->is_default = true;
        }

        $account->notes = 'System clearing account for ' . $currency . '.';
        $account->save();

        $this->info(($isNew ? 'Created: ' : 'Updated: ') . $account->account_name);
    }
}
