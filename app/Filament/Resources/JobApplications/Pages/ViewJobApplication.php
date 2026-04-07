<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('set_screening')
                ->label('Screening')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Move to Screening')
                ->modalDescription('Are you sure you want to move this applicant to Screening?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('screening')),

            Actions\Action::make('set_under_review')
                ->label('Under Review')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Move to Under Review')
                ->modalDescription('Are you sure you want to move this applicant to Under Review?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('under_review')),

            Actions\Action::make('set_client_submitted')
                ->label('Client Submitted')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Move to Client Submitted')
                ->modalDescription('Are you sure you want to mark this applicant as Client Submitted?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('client_submitted')),

            Actions\Action::make('set_qualified')
                ->label('Qualified')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Move to Qualified')
                ->modalDescription('Are you sure you want to move this applicant to Qualified?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('qualified')),

            Actions\Action::make('set_hired')
                ->label('Hired')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Move to Hired')
                ->modalDescription('Are you sure you want to move this applicant to Hired?')
                ->modalSubmitActionLabel('Yes, Move')
                ->action(fn () => $this->updateStatus('hired')),

            Actions\Action::make('set_declined')
                ->label('Declined')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Decline Application')
                ->modalDescription('Are you sure you want to decline this applicant and move the application to archive?')
                ->modalSubmitActionLabel('Yes, Decline')
                ->action(fn () => $this->updateStatus('declined')),

            Actions\EditAction::make(),

            Actions\DeleteAction::make()
                ->label('Delete')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Application')
                ->modalDescription('Are you sure you want to permanently delete this application?')
                ->modalSubmitActionLabel('Yes, Delete'),
        ];
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

        $this->record->refresh();

        Notification::make()
            ->title(match ($status) {
                'screening' => 'Applicant moved to Screening',
                'under_review' => 'Applicant moved to Under Review',
                'client_submitted' => 'Applicant moved to Client Submitted',
                'qualified' => 'Applicant moved to Qualified',
                'hired' => 'Applicant moved to Hired',
                'declined' => 'Applicant declined and archived',
                default => 'Status updated successfully',
            })
            ->success()
            ->send();
    }
}