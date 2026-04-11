<?php

namespace App\Filament\Resources\PreEmployments\Pages;

use App\Filament\Resources\Employments\EmploymentResource;
use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use App\Mail\PreEmploymentPortalRequestMail;
use App\Models\Employment;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewPreEmployment extends ViewRecord
{
    protected static string $resource = PreEmploymentResource::class;

    public function getTitle(): string
    {
        return 'Pre-Employment Profile';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openPortalLink')
                ->label('Open Public Link')
                ->color('gray')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->record?->portal_token ? url('/pre-employment/portal/' . $this->record->portal_token) : null)
                ->openUrlInNewTab()
                ->visible(fn () => filled($this->record?->portal_token)),

            Action::make('sendPortalRequest')
                ->label(fn () => $this->record?->portal_last_sent_at ? 'Resend Public Link' : 'Send Public Link')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(fn () => $this->record?->portal_last_sent_at ? 'Resend public link' : 'Send public link')
                ->modalDescription(function () {
                    if (blank($this->record?->candidate_email)) {
                        return 'This candidate does not have an email address.';
                    }

                    return $this->record?->portal_last_sent_at
                        ? 'Are you sure you want to resend the public link to this candidate?'
                        : 'Are you sure you want to send the public link to this candidate?';
                })
                ->modalSubmitActionLabel('Yes, Send')
                ->disabled(fn () => blank($this->record?->candidate_email) || blank($this->record?->portal_token))
                ->action(function () {
                    $isUpdateRequest = filled($this->record?->portal_last_sent_at);

                    Mail::to($this->record->candidate_email)
                        ->send(new PreEmploymentPortalRequestMail($this->record, $isUpdateRequest));

                    $this->record->update([
                        'portal_last_sent_at' => now(),
                        'status' => $this->record->status === 'initiated'
                            ? 'awaiting_candidate_upload'
                            : $this->record->status,
                    ]);

                    Notification::make()
                        ->title('Public link sent successfully')
                        ->success()
                        ->send();
                }),

            Action::make('convertToEmployment')
                ->label('Convert to Employment')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Convert to Employment')
                ->modalDescription('This will create a new Employment profile from this pre-employment record and copy its files. Are you sure?')
                ->modalSubmitActionLabel('Yes, Convert')
                ->visible(fn () => blank($this->record?->converted_to_employment_at))
                ->disabled(fn () => $this->record?->status !== 'ready_for_employment')
                ->tooltip(fn () => $this->record?->status !== 'ready_for_employment'
                    ? 'Conversion is only allowed when status is Ready for Employment.'
                    : null)
                ->action(function () {
                    $employment = Employment::create([
                        'pre_employment_id' => null,
                        'job_id' => null,

                        'position_title' => $this->record->job?->title,
                        'client_name' => $this->record->job?->project?->client?->name,
                        'project_name' => $this->record->job?->project?->name,

                        'assigned_hr_user_id' => null,
                        'operation_officer_name' => $this->record->assignedHrUser?->name,

                        'employee_name' => $this->record->candidate_name,
                        'employee_email' => $this->record->candidate_email,
                        'employee_phone' => $this->record->candidate_phone,
                        'employee_code' => null,

                        'status' => 'active',
                        'current_work_status' => 'pending_mobilization',
                        'rotation_status' => 'scheduled',
                        'rotation_pattern' => null,

                        'contract_status' => 'active',
                        'contract_start_date' => null,
                        'contract_end_date' => null,

                        'medical_status' => $this->record->medical_status,
                        'medical_date' => null,
                        'medical_expiry_date' => null,

                        'visa_status' => $this->record->visa_status,
                        'visa_issue_date' => null,
                        'visa_expiry_date' => null,

                        'travel_status' => 'pending_request',
                        'travel_request_date' => null,

                        'mobilization_date' => null,
                        'demobilization_date' => null,
                        'work_location' => null,

                        'notes' => $this->record->notes,
                        'internal_notes' => $this->record->internal_notes,
                        'converted_from_pre_employment_at' => now(),
                    ]);

                    foreach ($this->record->files as $file) {
                        $employment->files()->create([
                            'title' => $file->title,
                            'category' => $file->category,
                            'document_date' => $file->document_date,
                            'expiry_date' => $file->expiry_date,
                            'version_no' => $file->version_no,
                            'is_current' => $file->is_current,
                            'file_path' => $file->file_path,
                            'uploaded_by_type' => $file->uploaded_by_type,
                            'uploaded_by_user_id' => null,
                            'notes' => $file->notes,
                            'is_active' => $file->is_active,
                        ]);
                    }

                    $this->record->update([
                        'converted_to_employment_at' => now(),
                        'status' => 'converted_to_employment',
                    ]);

                    Notification::make()
                        ->title('Converted to Employment successfully')
                        ->success()
                        ->send();

                    $this->redirect(EmploymentResource::getUrl('view', ['record' => $employment]));
                }),

            Action::make('editProfile')
                ->label('Edit')
                ->color('primary')
                ->url(fn () => PreEmploymentResource::getUrl('edit', ['record' => $this->record])),

            DeleteAction::make()
                ->label('Delete')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Permanent delete')
                ->modalDescription('This record will be permanently deleted and cannot be recovered. Are you sure?')
                ->modalSubmitActionLabel('Yes, Delete Permanently'),
        ];
    }
}