<?php

namespace App\Filament\Resources\CandidateFinanceProfiles\Pages;

use App\Filament\Resources\CandidateFinanceProfiles\CandidateFinanceProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCandidateFinanceProfiles extends ListRecords
{
    protected static string $resource = CandidateFinanceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('candidate_finance_profiles', 'create')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('candidate_finance_profiles', 'view') ?? false);
    }

}
