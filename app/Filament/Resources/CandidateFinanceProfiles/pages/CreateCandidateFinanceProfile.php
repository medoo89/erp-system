<?php

namespace App\Filament\Resources\CandidateFinanceProfiles\Pages;

use App\Filament\Resources\CandidateFinanceProfiles\CandidateFinanceProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCandidateFinanceProfile extends CreateRecord
{
    protected static string $resource = CandidateFinanceProfileResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('candidate_finance_profiles', 'create') ?? false);
    }

}
