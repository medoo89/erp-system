<?php

namespace App\Filament\Resources\Employments\Pages;

use App\Filament\Resources\Employments\EmploymentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployment extends ViewRecord
{
    protected static string $resource = EmploymentResource::class;

    public function getTitle(): string
    {
        $name = $this->record?->employee_name ?: 'Employee';

        return "Employment Profile — {$name}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printProfile')
                ->label('Print Profile')
                ->color('gray')
                ->url(fn () => route('employment.print.profile', ['employment' => $this->record]))
                ->openUrlInNewTab(),

            Action::make('printRotationHistory')
                ->label('Print Rotation History')
                ->color('gray')
                ->url(fn () => route('employment.print.rotation-history', ['employment' => $this->record]))
                ->openUrlInNewTab(),

            Action::make('editProfile')
                ->label('Edit')
                ->color('primary')
                ->url(fn () => EmploymentResource::getUrl('edit', ['record' => $this->record])),

            DeleteAction::make()
                ->label('Delete')
                ->requiresConfirmation()
                ->modalHeading('Permanent delete')
                ->modalDescription('This record will be permanently deleted and cannot be recovered. Are you sure?')
                ->modalSubmitActionLabel('Yes, Delete Permanently'),
        ];
    }
}