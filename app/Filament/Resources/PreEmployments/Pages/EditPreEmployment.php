<?php

namespace App\Filament\Resources\PreEmployments\Pages;

use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPreEmployment extends EditRecord
{
    protected static string $resource = PreEmploymentResource::class;

    public function getTitle(): string
    {
        return 'Edit Pre-Employment Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToProfile')
                ->label('Back to Profile')
                ->color('gray')
                ->url(fn () => PreEmploymentResource::getUrl('view', ['record' => $this->record])),

            Action::make('saveChanges')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'edit'))
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
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'delete'))
                ->label('Delete')
                ->color('danger')
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

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'edit') ?? false);
    }

}
