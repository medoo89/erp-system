<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Mail\JobApplicationDeclinedMail;
use App\Mail\JobApplicationStatusUpdatedMail;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

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

            $this->makeStatusAction(
                name: 'set_under_review',
                label: 'Under Review',
                color: 'info',
                status: 'under_review',
            ),

            $this->makeStatusAction(
                name: 'set_client_submitted',
                label: 'Client Submitted',
                color: 'primary',
                status: 'client_submitted',
            ),

            $this->makeStatusAction(
                name: 'set_qualified',
                label: 'Qualified',
                color: 'gray',
                status: 'qualified',
            ),

            $this->makeStatusAction(
                name: 'set_hired',
                label: 'Hired',
                color: 'success',
                status: 'hired',
            ),

            Action::make('set_declined')
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
                        ->rows(4),
                    Toggle::make('send_email')
                        ->label('Send email notification')
                        ->default(true),
                    Placeholder::make('email_preview_declined')
                        ->label('Email Preview')
                        ->content(function () {
                            $jobTitle = optional($this->record->job)->title ?: '-';

                            return "To: {$this->record->email}\n"
                                . "Applicant: {$this->record->full_name}\n"
                                . "Job: {$jobTitle}\n"
                                . "Status: Declined\n"
                                . "Subject: Update on Your Job Application";
                        }),
                ])
                ->requiresConfirmation()
                ->modalHeading('Decline Application')
                ->modalDescription('Select the decline reason, review the email option, and confirm moving this application to archive.')
                ->modalSubmitActionLabel('Yes, Decline')
                ->action(function (array $data) {
                    $this->updateStatus('declined', $data);
                }),
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

    protected function makeStatusAction(string $name, string $label, string $color, string $status): Action
    {
        return Action::make($name)
            ->label($label)
            ->color($color)
            ->form([
                Toggle::make('send_email')
                    ->label('Send email notification')
                    ->default(true),

                Placeholder::make('email_preview')
                    ->label('Email Preview')
                    ->content(function () use ($status) {
                        $jobTitle = optional($this->record->job)->title ?: '-';

                        return "To: {$this->record->email}\n"
                            . "Applicant: {$this->record->full_name}\n"
                            . "Job: {$jobTitle}\n"
                            . "Status: {$this->getStatusLabel($status)}\n"
                            . "Subject: {$this->getStatusEmailSubject($status)}\n\n"
                            . $this->getStatusEmailMessage($status);
                    }),
            ])
            ->requiresConfirmation()
            ->modalHeading("Move to {$label}")
            ->modalDescription('Review the action and choose whether to send an email notification.')
            ->modalSubmitActionLabel('Confirm')
            ->action(function (array $data) use ($status) {
                $this->updateStatus($status, $data);
            });
    }

    protected function getFormActions(): array
    {
        return [];
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
        $this->record->refresh();

        $sendEmail = (bool) ($extraData['send_email'] ?? false);

        $this->sendStatusEmailIfNeeded($status, $oldStatus, $sendEmail);

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

    protected function sendStatusEmailIfNeeded(string $newStatus, ?string $oldStatus = null, bool $sendEmail = false): void
    {
        if ($newStatus === 'screening') {
            return;
        }

        if (! $sendEmail) {
            return;
        }

        if ($oldStatus === $newStatus) {
            return;
        }

        if (blank($this->record->email)) {
            return;
        }

        if ($newStatus === 'declined') {
            Mail::to($this->record->email)->send(
                new JobApplicationDeclinedMail(
                    $this->record,
                    $this->getDeclineReasonLabel($this->record->decline_reason),
                    $this->record->decline_notes,
                )
            );

            return;
        }

        Mail::to($this->record->email)->send(
            new JobApplicationStatusUpdatedMail(
                $this->record,
                $this->getStatusLabel($newStatus),
                $this->getStatusEmailSubject($newStatus),
                $this->getStatusEmailMessage($newStatus),
            )
        );
    }

    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'under_review' => 'Under Review',
            'client_submitted' => 'Client Submitted',
            'qualified' => 'Qualified',
            'hired' => 'Hired',
            'declined' => 'Declined',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    protected function getStatusEmailSubject(string $status): string
    {
        return match ($status) {
            'under_review' => 'Your Job Application Is Under Review',
            'client_submitted' => 'Your Job Application Has Been Submitted to Client',
            'qualified' => 'Update on Your Job Application',
            'hired' => 'Congratulations - Job Application Update',
            default => 'Update on Your Job Application',
        };
    }

    protected function getStatusEmailMessage(string $status): string
    {
        return match ($status) {
            'under_review' => 'We would like to inform you that your application is currently under review by our recruitment team.',
            'client_submitted' => 'Your application has been submitted to the client for further consideration.',
            'qualified' => 'Your profile has been marked as qualified and may be considered for this or future opportunities.',
            'hired' => 'Congratulations. Your application has been marked as hired, and our team will contact you regarding the next steps.',
            default => 'Your application status has been updated.',
        };
    }

    protected function getDeclineReasonLabel(?string $reason): string
    {
        return match ($reason) {
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
            default => 'Declined',
        };
    }
}