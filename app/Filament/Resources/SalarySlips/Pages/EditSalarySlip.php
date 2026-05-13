<?php

namespace App\Filament\Resources\SalarySlips\Pages;

use App\Filament\Resources\SalarySlips\SalarySlipResource;
use App\Models\SalarySlip;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditSalarySlip extends EditRecord
{
    protected static string $resource = SalarySlipResource::class;

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Edit · Salary Slip #' . $this->record->id;
    }

    public function getSubheading(): string|Htmlable|null
    {
        $employee = $this->record->employment?->employee_name ?: 'Unknown Employee';
        $status = SalarySlip::statusLabels()[$this->record->status] ?? $this->record->status;

        return "Employee: {$employee} · Status: {$status}";
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = $data['status'] ?? SalarySlip::STATUS_DRAFT;

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record) {
            $this->record->refresh();
            $this->record->syncTreasuryPosting();
            $this->record->refresh();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'delete')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'edit') ?? false);
    }

}
