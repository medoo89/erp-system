<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->url(JobApplicationResource::getUrl('view', ['record' => $this->record])),

            Action::make('save')
                ->label('Save Changes')
                ->color('primary')
                ->action(fn () => $this->save()),

            Action::make('set_screening')
                ->label('Screening')
                ->color('warning')
                ->action(fn () => $this->updateStatus('screening')),

            Action::make('set_under_review')
                ->label('Under Review')
                ->color('info')
                ->action(fn () => $this->updateStatus('under_review')),

            Action::make('set_client_submitted')
                ->label('Client Submitted')
                ->color('primary')
                ->action(fn () => $this->updateStatus('client_submitted')),

            Action::make('set_qualified')
                ->label('Qualified')
                ->color('gray')
                ->action(fn () => $this->updateStatus('qualified')),

            Action::make('set_hired')
                ->label('Hired')
                ->color('success')
                ->action(fn () => $this->updateStatus('hired')),

            Action::make('set_declined')
                ->label('Declined')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Decline Application')
                ->modalDescription('Are you sure you want to mark this application as declined and move it to archive?')
                ->modalSubmitActionLabel('Yes, Decline')
                ->action(fn () => $this->updateStatus('declined')),

            DeleteAction::make()
                ->label('Delete')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Application')
                ->modalDescription('Are you sure you want to permanently delete this application?')
                ->modalSubmitActionLabel('Yes, Delete'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function updateStatus(string $status): void
    {
        $data = [
            'status' => $status,
        ];

        if ($status === 'declined') {
            $data['is_archived'] = true;
            $data['archive_reason'] = 'declined';
            $data['archived_at'] = now();
        }

        $this->record->update($data);

        $this->fillForm();

        Notification::make()
            ->title(match ($status) {
                'screening' => 'Moved to Screening',
                'under_review' => 'Moved to Under Review',
                'client_submitted' => 'Moved to Client Submitted',
                'qualified' => 'Moved to Qualified',
                'hired' => 'Moved to Hired',
                'declined' => 'Moved to Declined and Archived',
                default => 'Status updated successfully',
            })
            ->success()
            ->send();
    }
}