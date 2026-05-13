<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BankProfiles\BankProfileResource;
use App\Filament\Resources\TreasuryAccounts\TreasuryAccountResource;
use App\Filament\Resources\TreasuryOperations\TreasuryOperationResource;
use App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource;
use App\Models\BankProfile;
use App\Models\ClientInvoicePayment;
use App\Models\TreasuryAccount;
use App\Models\TreasuryOperation;
use App\Models\TreasuryTransaction;
use BackedEnum;
use Filament\Pages\Page;

class TreasuryPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = 'Treasury';

    protected static ?string $title = 'Treasury';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 50;

    protected string $view = 'filament.pages.treasury-page';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public function getViewData(): array
    {
        $accounts = TreasuryAccount::query()
            ->where('is_active', true)
            ->orderBy('account_type')
            ->orderBy('institution_name')
            ->orderBy('account_name')
            ->get();

        $currencyTotals = $accounts
            ->groupBy(fn (TreasuryAccount $account) => strtoupper((string) ($account->currency ?: '-')))
            ->map(fn ($group) => round((float) $group->sum('current_balance'), 2))
            ->sortKeys();

        $bankProfiles = BankProfile::query()
            ->with(['accounts.treasuryAccount'])
            ->where('is_active', true)
            ->orderBy('profile_name')
            ->get()
            ->map(function (BankProfile $profile) {
                $rows = $profile->accounts
                    ->where('is_active', true)
                    ->map(function ($account) {
                        return [
                            'currency' => strtoupper((string) ($account->currency ?: '-')),
                            'balance' => (float) ($account->treasuryAccount?->current_balance ?? 0),
                            'treasury_account_id' => $account->treasury_account_id,
                            'account_name' => $account->treasuryAccount?->account_name,
                        ];
                    })
                    ->values();

                return [
                    'id' => $profile->id,
                    'profile_name' => $profile->profile_name,
                    'bank_name' => $profile->bank_name,
                    'beneficiary_name' => $profile->beneficiary_name,
                    'accounts_count' => $rows->count(),
                    'currencies' => $rows,
                    'resource_url' => BankProfileResource::getUrl('edit', ['record' => $profile]),
                ];
            });

        $cashAccounts = $accounts
            ->where('account_type', TreasuryAccount::TYPE_CASH)
            ->map(fn (TreasuryAccount $account) => [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'currency' => strtoupper((string) ($account->currency ?: '-')),
                'balance' => (float) ($account->current_balance ?? 0),
                'url' => TreasuryAccountResource::getUrl('view', ['record' => $account]),
            ])
            ->values();

        $clearingAccounts = $accounts
            ->where('account_type', TreasuryAccount::TYPE_CLEARING)
            ->map(fn (TreasuryAccount $account) => [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'currency' => strtoupper((string) ($account->currency ?: '-')),
                'balance' => (float) ($account->current_balance ?? 0),
                'url' => TreasuryAccountResource::getUrl('view', ['record' => $account]),
            ])
            ->values();

        $accountsCount = (int) $accounts->count();

        $transactionsCount = (int) TreasuryTransaction::query()->count();
        $incomingTransactionsCount = (int) TreasuryTransaction::query()
            ->where('direction', TreasuryTransaction::DIRECTION_IN)
            ->count();
        $outgoingTransactionsCount = (int) TreasuryTransaction::query()
            ->where('direction', TreasuryTransaction::DIRECTION_OUT)
            ->count();

        $operationsCount = (int) TreasuryOperation::query()->count();
        $pendingOperationsCount = (int) TreasuryOperation::query()
            ->where('settlement_status', 'pending')
            ->count();
        $clearedOperationsCount = (int) TreasuryOperation::query()
            ->where('settlement_status', 'cleared')
            ->count();

        $pendingClearingCount = class_exists(ClientInvoicePayment::class)
            ? (int) ClientInvoicePayment::query()->where('settlement_status', 'pending')->count()
            : 0;

        $linkedBankCurrencyAccounts = (int) $bankProfiles->sum('accounts_count');

        return [
            'currencyTotals' => $currencyTotals,
            'bankProfiles' => $bankProfiles,
            'cashAccounts' => $cashAccounts,
            'clearingAccounts' => $clearingAccounts,

            'accountsCount' => $accountsCount,
            'transactionsCount' => $transactionsCount,
            'incomingTransactionsCount' => $incomingTransactionsCount,
            'outgoingTransactionsCount' => $outgoingTransactionsCount,
            'operationsCount' => $operationsCount,
            'pendingOperationsCount' => $pendingOperationsCount,
            'clearedOperationsCount' => $clearedOperationsCount,
            'pendingClearingCount' => $pendingClearingCount,
            'bankProfilesCount' => (int) $bankProfiles->count(),
            'linkedBankCurrencyAccounts' => $linkedBankCurrencyAccounts,

            'treasuryAccountsUrl' => TreasuryAccountResource::getUrl('index'),
            'treasuryTransactionsUrl' => TreasuryTransactionResource::getUrl('index'),
            'treasuryOperationsUrl' => TreasuryOperationResource::getUrl('index'),
            'bankProfilesUrl' => BankProfileResource::getUrl('index'),
            'clearingMonitorUrl' => url('/admin/clearing-monitor-page'),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

}
