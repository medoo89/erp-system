<?php

namespace App\Filament\Resources\SalarySlips\Pages;

use App\Filament\Resources\SalarySlips\SalarySlipResource;
use App\Models\SalarySlip;
use Filament\Resources\Pages\CreateRecord;

class CreateSalarySlip extends CreateRecord
{
    protected static string $resource = SalarySlipResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = $data['status'] ?? SalarySlip::STATUS_DRAFT;

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record) {
            $this->record->refresh();
            $this->record->syncTreasuryPosting();
            $this->record->refresh();
        }
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) ((auth()->user()?->canErp('salary_slips', 'create') || auth()->user()?->canErp('employments', 'generate_salary_slip')) ?? false);
    }

}
