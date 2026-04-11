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
        return 'Employment Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
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