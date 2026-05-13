<?php

namespace App\Filament\Resources\CandidateFinanceProfiles\Pages;

use App\Filament\Resources\CandidateFinanceProfiles\CandidateFinanceProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCandidateFinanceProfile extends ViewRecord
{
    protected static string $resource = CandidateFinanceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('candidate_finance_profiles', 'edit')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('candidate_finance_profiles', 'view') ?? false);
    }

}
