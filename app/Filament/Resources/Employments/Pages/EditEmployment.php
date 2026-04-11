<?php

namespace App\Filament\Resources\Employments\Pages;

use App\Filament\Resources\Employments\EmploymentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEmployment extends EditRecord
{
    protected static string $resource = EmploymentResource::class;

    public function getTitle(): string
    {
        return 'Edit Employment Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToProfile')
                ->label('Back to Profile')
                ->color('gray')
                ->url(fn () => EmploymentResource::getUrl('view', ['record' => $this->record])),

            Action::make('saveChanges')
                ->label('Save Changes')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Save changes')
                ->modalDescription('Are you sure you want to save these changes?')
                ->modalSubmitActionLabel('Yes, Save')
                ->action(function () {
                    $this->save();

                    Notification::make()
                        ->title('Changes saved successfully')
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->label('Delete')
                ->requiresConfirmation()
                ->modalHeading('Permanent delete')
                ->modalDescription('This record will be permanently deleted and cannot be recovered. Are you sure?')
                ->modalSubmitActionLabel('Yes, Delete Permanently'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}