<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    public function getTitle(): string
    {
        return 'Edit Job Application';
    }

    public function getHeading(): string
    {
        return 'Edit Job Application';
    }

    public function getSubheading(): ?string
    {
        return $this->record?->full_name ?: null;
    }

    protected function getHeaderActions(): array
    {
        $isArchived = (bool) ($this->record->is_archived ?? false);

        $statusActions = [
            Action::make('set_screening')
                ->label('Screening')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Move to Screening')
                ->modalDescription('Are you sure you want to move this application to Screening?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('screening')),

            Action::make('set_under_review')
                ->label('Under Review')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Move to Under Review')
                ->modalDescription('Are you sure you want to move this application to Under Review?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('under_review')),

            Action::make('set_client_submitted')
                ->label('Client Submitted')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Move to Client Submitted')
                ->modalDescription('Are you sure you want to move this application to Client Submitted?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('client_submitted')),

            Action::make('set_qualified')
                ->label('Qualified')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Move to Qualified')
                ->modalDescription('Are you sure you want to move this application to Qualified?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('qualified')),

            Action::make('set_hired')
                ->label('Hired')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Move to Hired')
                ->modalDescription('Are you sure you want to move this application to Hired?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('hired')),

            Action::make('set_declined')
                ->label('Declined')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Decline Application')
                ->modalDescription('Are you sure you want to mark this application as Declined and move it to archive?')
                ->modalSubmitActionLabel('Yes, Decline')
                ->action(fn () => $this->updateStatus('declined')),
        ];

        $baseActions = [
            Action::make('cancel')
                ->label('Cancel')
                ->color('gray')
                ->url(JobApplicationResource::getUrl('view', ['record' => $this->record])),

            Action::make('save')
                ->label('Save Changes')
                ->color('primary')
                ->action(fn () => $this->save()),

            DeleteAction::make()
                ->label('Delete')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Application')
                ->modalDescription('Are you sure you want to permanently delete this application?')
                ->modalSubmitActionLabel('Yes, Delete'),
        ];

        if ($isArchived) {
            return $baseActions;
        }

        return array_merge($baseActions, $statusActions);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function updateStatus(string $status): void
    {
        $oldStatus = $this->record->status;

        $data = [
            'status' => $status,
        ];

        if ($status === 'declined') {
            $data['is_archived'] = true;
            $data['archive_reason'] = 'declined';
            $data['archived_at'] = now();
        } else {
            $data['is_archived'] = false;
            $data['archive_reason'] = null;
            $data['archived_at'] = null;
        }

        $this->record->update($data);

        $this->sendStatusEmailIfNeeded($status, $oldStatus);

        if ($status === 'declined') {
            Notification::make()
                ->title('Moved to Declined and Archived')
                ->success()
                ->send();

            $this->redirect(ArchivedJobApplicationResource::getUrl('index'));

            return;
        }

        $this->fillForm();

        Notification::make()
            ->title(match ($status) {
                'screening' => 'Moved to Screening',
                'under_review' => 'Moved to Under Review',
                'client_submitted' => 'Moved to Client Submitted',
                'qualified' => 'Moved to Qualified',
                'hired' => 'Moved to Hired',
                default => 'Status updated successfully',
            })
            ->success()
            ->send();
    }

    protected function sendStatusEmailIfNeeded(string $newStatus, ?string $oldStatus = null): void
    {
        if ($newStatus === 'screening') {
            return;
        }

        if ($oldStatus === $newStatus) {
            return;
        }

        // سنربط هنا الإيميل في الخطوة القادمة
    }
}