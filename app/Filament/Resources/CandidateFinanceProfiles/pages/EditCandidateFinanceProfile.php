<?php

namespace App\Filament\Resources\CandidateFinanceProfiles\Pages;

use App\Filament\Resources\CandidateFinanceProfiles\CandidateFinanceProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCandidateFinanceProfile extends EditRecord
{
    protected static string $resource = CandidateFinanceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('candidate_finance_profiles', 'delete')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('candidate_finance_profiles', 'edit') ?? false);
    }

}
