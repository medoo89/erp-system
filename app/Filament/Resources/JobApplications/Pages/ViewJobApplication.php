<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function getHeaderActions(): array
    {
        $isArchived = (bool) ($this->record->is_archived ?? false);

        $statusActions = [
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
                ->form([
                    Select::make('decline_reason')
                        ->label('Decline Reason')
                        ->required()
                        ->options([
                            'internal_rejected' => 'Internal Rejected',
                            'client_rejected' => 'Rejected by Client',
                            'applicant_withdrew' => 'Applicant Withdrew',
                            'applicant_refused_salary' => 'Applicant Refused Salary',
                            'applicant_refused_offer' => 'Applicant Refused Offer',
                            'applicant_refused_contract' => 'Applicant Refused Contract',
                            'no_response' => 'No Response',
                            'failed_requirements' => 'Failed Requirements',
                            'position_closed' => 'Position Closed',
                            'other' => 'Other',
                        ]),
                    Textarea::make('decline_notes')
                        ->label('Decline Notes')
                        ->rows(4)
                        ->nullable(),
                ])
                ->requiresConfirmation()
                ->modalHeading('Decline Application')
                ->modalDescription('Select the decline reason and confirm moving this application to archive.')
                ->modalSubmitActionLabel('Yes, Decline')
                ->action(function (array $data) {
                    $this->updateStatus('declined', $data);
                }),
        ];

        $baseActions = [
            Actions\EditAction::make(),

            Actions\DeleteAction::make()
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

        return array_merge($statusActions, $baseActions);
    }

    protected function updateStatus(string $status, array $extraData = []): void
    {
        $oldStatus = $this->record->status;

        $data = [
            'status' => $status,
        ];

        if ($status === 'declined') {
            $data['is_archived'] = true;
            $data['archive_reason'] = 'declined';
            $data['archived_at'] = now();
            $data['decline_reason'] = $extraData['decline_reason'] ?? null;
            $data['decline_notes'] = $extraData['decline_notes'] ?? null;
        } else {
            $data['is_archived'] = false;
            $data['archive_reason'] = null;
            $data['archived_at'] = null;
        }

        $this->record->update($data);

        $this->sendStatusEmailIfNeeded($status, $oldStatus);

        if ($status === 'declined') {
            Notification::make()
                ->title('Applicant declined and archived')
                ->success()
                ->send();

            $this->redirect(ArchivedJobApplicationResource::getUrl('index'));

            return;
        }

        $this->record->refresh();

        Notification::make()
            ->title(match ($status) {
                'screening' => 'Applicant moved to Screening',
                'under_review' => 'Applicant moved to Under Review',
                'client_submitted' => 'Applicant moved to Client Submitted',
                'qualified' => 'Applicant moved to Qualified',
                'hired' => 'Applicant moved to Hired',
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