<?php

namespace App\Filament\Pages;

use App\Models\TreasuryTransaction;
use BackedEnum;
use Filament\Pages\Page;

class ClearingMonitorPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Clearing Monitor';

    protected static ?string $title = 'Clearing Monitor';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationParentItem = 'Treasury';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.clearing-monitor-page';

    public ?string $search = null;

    public ?string $currency = null;

    public ?string $accountType = null;

    public ?string $direction = null;

    public function resetFilters(): void
    {
        $this->search = null;
        $this->currency = null;
        $this->accountType = null;
        $this->direction = null;
    }


    public function getViewData(): array
    {
        $query = TreasuryTransaction::query()
            ->with(['treasuryAccount', 'client', 'project', 'employment'])
            ->where('settlement_status', TreasuryTransaction::SETTLEMENT_PENDING);

        if (filled($this->search)) {
            $term = trim($this->search);

            $query->where(function ($q) use ($term) {
                $q->where('transaction_no', 'like', "%{$term}%")
                    ->orWhere('transaction_type', 'like', "%{$term}%")
                    ->orWhere('reference_type', 'like', "%{$term}%")
                    ->orWhere('reference_id', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('treasuryAccount', fn ($accountQuery) => $accountQuery
                        ->where('account_name', 'like', "%{$term}%")
                        ->orWhere('institution_name', 'like', "%{$term}%")
                    )
                    ->orWhereHas('client', fn ($clientQuery) => $clientQuery
                        ->where('name', 'like', "%{$term}%")
                    )
                    ->orWhereHas('project', fn ($projectQuery) => $projectQuery
                        ->where('name', 'like', "%{$term}%")
                    )
                    ->orWhereHas('employment', fn ($employmentQuery) => $employmentQuery
                        ->where('employee_name', 'like', "%{$term}%")
                        ->orWhere('employee_code', 'like', "%{$term}%")
                    );
            });
        }

        if (filled($this->currency)) {
            $query->where('currency', strtoupper($this->currency));
        }

        if (filled($this->direction)) {
            $query->where('direction', $this->direction);
        }

        if (filled($this->accountType)) {
            $query->whereHas('treasuryAccount', fn ($accountQuery) => $accountQuery
                ->where('account_type', $this->accountType)
            );
        }

        $pendingTransactions = $query
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $pendingCount = $pendingTransactions->count();

        $pendingByCurrency = $pendingTransactions
            ->groupBy(fn ($item) => $item->currency ?: '-')
            ->map(fn ($group) => round((float) $group->sum('amount'), 2))
            ->toArray();

        $pendingByAccountType = $pendingTransactions
            ->groupBy(fn ($item) => $item->treasuryAccount?->account_type ?: 'unknown')
            ->map(fn ($group) => round((float) $group->sum('amount'), 2))
            ->toArray();

        return [
            'pendingTransactions' => $pendingTransactions,
            'pendingCount' => $pendingCount,
            'pendingByCurrency' => $pendingByCurrency,
            'pendingByAccountType' => $pendingByAccountType,
            'currencyOptions' => TreasuryTransaction::query()
                ->where('settlement_status', TreasuryTransaction::SETTLEMENT_PENDING)
                ->whereNotNull('currency')
                ->distinct()
                ->orderBy('currency')
                ->pluck('currency')
                ->filter()
                ->values()
                ->toArray(),
            'accountTypeOptions' => ['bank' => 'Bank', 'cash' => 'Cash', 'clearing' => 'Clearing'],
            'directionOptions' => ['in' => 'Incoming', 'out' => 'Outgoing'],
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view_clearing') ?? false);
    }

}
