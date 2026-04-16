<?php

namespace App\Filament\Resources\JobApplications\Pages;

use App\Filament\Resources\ArchivedJobApplications\ArchivedJobApplicationResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Mail\CandidateRequestMail;
use App\Mail\JobApplicationDeclinedMail;
use App\Mail\JobApplicationStatusUpdatedMail;
use App\Mail\PreEmploymentStartedMail;
use App\Models\PreEmployment;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ViewJobApplication extends ViewRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected string $view = 'filament.resources.job-applications.pages.view-job-application';

    public ?int $activeNegotiationRequestId = null;
    public ?int $activeFinalOfferRequestId = null;
    public ?string $newOfferSalary = null;
    public ?string $newOfferCurrency = 'USD';
    public ?string $newOfferNotes = null;

    public function getCandidateRequestsProperty(): Collection
    {
        return $this->record->candidateRequests()
            ->with('items')
            ->latest()
            ->get();
    }

    public function deleteCandidateRequest(int $requestId): void
    {
        $candidateRequest = $this->record->candidateRequests()
            ->whereKey($requestId)
            ->first();

        if (! $candidateRequest) {
            Notification::make()
                ->title('Request not found')
                ->danger()
                ->send();

            return;
        }

        $candidateRequest->delete();

        $this->record->refresh();

        Notification::make()
            ->title('Candidate request deleted successfully')
            ->success()
            ->send();
    }

    public function resendCandidateRequestEmail(int $requestId): void
    {
        $candidateRequest = $this->record->candidateRequests()
            ->with('items', 'jobApplication.job')
            ->whereKey($requestId)
            ->first();

        if (! $candidateRequest) {
            Notification::make()
                ->title('Request not found')
                ->danger()
                ->send();

            return;
        }

        if (blank($this->record->email)) {
            Notification::make()
                ->title('Candidate email is missing')
                ->warning()
                ->send();

            return;
        }

        try {
            $portalUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $candidateRequest->public_token;

            Mail::to($this->record->email)->send(
                new CandidateRequestMail(
                    $candidateRequest->fresh()->load('items', 'jobApplication.job'),
                    $portalUrl
                )
            );

            Notification::make()
                ->title('Candidate request email resent successfully')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('Candidate request resend email failed', [
                'job_application_id' => $this->record->id,
                'candidate_request_id' => $candidateRequest->id,
                'email' => $this->record->email,
                'message' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Could not resend request email')
                ->danger()
                ->send();
        }
    }

    protected function getCandidateRequestById(int $requestId)
    {
        return $this->record->candidateRequests()
            ->whereKey($requestId)
            ->where('type', 'salary_negotiation')
            ->first();
    }

    protected function decodeCandidateResponse($candidateRequest): array
    {
        $decoded = json_decode((string) $candidateRequest->candidate_response, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function appendThreadEntry($candidateRequest, array $entry): array
    {
        $decoded = $this->decodeCandidateResponse($candidateRequest);

        $thread = is_array($decoded['thread'] ?? null)
            ? $decoded['thread']
            : [];

        if (empty($thread)) {
            $thread[] = [
                'sender' => 'hr',
                'event' => 'request_created',
                'title' => $candidateRequest->title,
                'message' => $candidateRequest->notes,
                'salary' => $candidateRequest->proposed_salary,
                'currency' => $candidateRequest->currency,
                'created_at' => optional($candidateRequest->created_at)?->toDateTimeString(),
            ];
        }

        $entry['created_at'] = $entry['created_at'] ?? now()->toDateTimeString();
        $thread[] = $entry;

        $decoded['thread'] = $thread;

        return $decoded;
    }

    protected function sendCandidateRequestStatusEmail($candidateRequest): void
    {
        if (blank($this->record->email)) {
            return;
        }

        try {
            $portalUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $candidateRequest->public_token;

            Mail::to($this->record->email)->send(
                new CandidateRequestMail(
                    $candidateRequest->fresh()->load('items', 'jobApplication.job'),
                    $portalUrl
                )
            );
        } catch (\Throwable $e) {
            Log::error('Candidate request status email failed', [
                'job_application_id' => $this->record->id,
                'candidate_request_id' => $candidateRequest->id,
                'email' => $this->record->email,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function approveNegotiationRequest(int $requestId): void
    {
        $candidateRequest = $this->getCandidateRequestById($requestId);

        if (! $candidateRequest) {
            Notification::make()
                ->title('Negotiation request not found')
                ->danger()
                ->send();

            return;
        }

        $decoded = $this->appendThreadEntry($candidateRequest, [
            'sender' => 'hr',
            'event' => 'approved',
            'message' => 'HR approved the negotiation.',
            'salary' => $candidateRequest->proposed_salary,
            'currency' => $candidateRequest->currency,
        ]);

        $candidateRequest->update([
            'request_status' => 'accepted',
            'candidate_response' => json_encode($decoded, JSON_UNESCAPED_UNICODE),
            'accepted_salary' => $candidateRequest->proposed_salary,
            'accepted_currency' => $candidateRequest->currency,
            'negotiation_result' => 'accepted',
        ]);

        $this->record->update([
            'candidate_request_status' => 'request_completed',
        ]);

        $this->sendCandidateRequestStatusEmail($candidateRequest);

        $this->record->refresh();

        Notification::make()
            ->title('Negotiation approved successfully')
            ->success()
            ->send();
    }

    public function declineNegotiationRequest(int $requestId): void
    {
        $candidateRequest = $this->getCandidateRequestById($requestId);

        if (! $candidateRequest) {
            Notification::make()
                ->title('Negotiation request not found')
                ->danger()
                ->send();

            return;
        }

        $decoded = $this->appendThreadEntry($candidateRequest, [
            'sender' => 'hr',
            'event' => 'declined',
            'message' => 'HR declined the negotiation due to salary not accepted.',
            'salary' => $candidateRequest->proposed_salary,
            'currency' => $candidateRequest->currency,
        ]);

        $candidateRequest->update([
            'request_status' => 'declined',
            'candidate_response' => json_encode($decoded, JSON_UNESCAPED_UNICODE),
            'negotiation_result' => 'salary_not_accepted',
        ]);

        $this->record->update([
            'candidate_request_status' => 'response_received',
        ]);

        $this->sendCandidateRequestStatusEmail($candidateRequest);

        $this->record->refresh();

        Notification::make()
            ->title('Negotiation declined successfully')
            ->success()
            ->send();
    }

    public function startNewOffer(int $requestId): void
    {
        $candidateRequest = $this->getCandidateRequestById($requestId);

        if (! $candidateRequest) {
            Notification::make()
                ->title('Negotiation request not found')
                ->danger()
                ->send();

            return;
        }

        $this->activeNegotiationRequestId = $candidateRequest->id;
        $this->activeFinalOfferRequestId = null;
        $this->newOfferSalary = $candidateRequest->proposed_salary ? (string) $candidateRequest->proposed_salary : null;
        $this->newOfferCurrency = $candidateRequest->currency ?: 'USD';
        $this->newOfferNotes = null;
    }

    public function startFinalOffer(int $requestId): void
    {
        $candidateRequest = $this->getCandidateRequestById($requestId);

        if (! $candidateRequest) {
            Notification::make()
                ->title('Negotiation request not found')
                ->danger()
                ->send();

            return;
        }

        $this->activeFinalOfferRequestId = $candidateRequest->id;
        $this->activeNegotiationRequestId = null;
        $this->newOfferSalary = $candidateRequest->proposed_salary ? (string) $candidateRequest->proposed_salary : null;
        $this->newOfferCurrency = $candidateRequest->currency ?: 'USD';
        $this->newOfferNotes = null;
    }

    public function cancelNewOffer(): void
    {
        $this->activeNegotiationRequestId = null;
        $this->activeFinalOfferRequestId = null;
        $this->newOfferSalary = null;
        $this->newOfferCurrency = 'USD';
        $this->newOfferNotes = null;
    }

    public function sendNewNegotiationOffer(int $requestId): void
    {
        $candidateRequest = $this->getCandidateRequestById($requestId);

        if (! $candidateRequest) {
            Notification::make()
                ->title('Negotiation request not found')
                ->danger()
                ->send();

            return;
        }

        $this->validate([
            'newOfferSalary' => ['required', 'numeric'],
            'newOfferCurrency' => ['required', 'string', 'max:10'],
            'newOfferNotes' => ['nullable', 'string'],
        ]);

        $decoded = $this->appendThreadEntry($candidateRequest, [
            'sender' => 'hr',
            'event' => 'new_offer',
            'message' => $this->newOfferNotes,
            'salary' => $this->newOfferSalary,
            'currency' => $this->newOfferCurrency,
        ]);

        $candidateRequest->update([
            'proposed_salary' => $this->newOfferSalary,
            'currency' => $this->newOfferCurrency,
            'request_status' => 'pending',
            'responded_at' => null,
            'candidate_response' => json_encode($decoded, JSON_UNESCAPED_UNICODE),
            'is_final_offer' => false,
        ]);

        $this->record->update([
            'candidate_request_status' => 'awaiting_response',
        ]);

        $this->sendCandidateRequestStatusEmail($candidateRequest);

        $this->cancelNewOffer();
        $this->record->refresh();

        Notification::make()
            ->title('New negotiation offer sent successfully')
            ->success()
            ->send();
    }

    public function sendFinalNegotiationOffer(int $requestId): void
    {
        $candidateRequest = $this->getCandidateRequestById($requestId);

        if (! $candidateRequest) {
            Notification::make()
                ->title('Negotiation request not found')
                ->danger()
                ->send();

            return;
        }

        $this->validate([
            'newOfferSalary' => ['required', 'numeric'],
            'newOfferCurrency' => ['required', 'string', 'max:10'],
            'newOfferNotes' => ['nullable', 'string'],
        ]);

        $decoded = $this->appendThreadEntry($candidateRequest, [
            'sender' => 'hr',
            'event' => 'final_offer',
            'message' => $this->newOfferNotes ?: 'This is the final offer from HR.',
            'salary' => $this->newOfferSalary,
            'currency' => $this->newOfferCurrency,
        ]);

        $candidateRequest->update([
            'proposed_salary' => $this->newOfferSalary,
            'currency' => $this->newOfferCurrency,
            'request_status' => 'pending',
            'responded_at' => null,
            'candidate_response' => json_encode($decoded, JSON_UNESCAPED_UNICODE),
            'is_final_offer' => true,
        ]);

        $this->record->update([
            'candidate_request_status' => 'awaiting_response',
        ]);

        $this->sendCandidateRequestStatusEmail($candidateRequest);

        $this->cancelNewOffer();
        $this->record->refresh();

        Notification::make()
            ->title('Final negotiation offer sent successfully')
            ->success()
            ->send();
    }
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

            Actions\Action::make('set_hired')
                ->label('Hired')
                ->color('success')
                ->form([
                    Toggle::make('create_pre_employment')
                        ->label('Create Pre-Employment record automatically')
                        ->default(true),

                    Toggle::make('send_pre_employment_email')
                        ->label('Send Pre-Employment email notification')
                        ->default(false)
                        ->visible(fn (callable $get) => (bool) $get('create_pre_employment')),

                    Placeholder::make('pre_employment_preview')
                        ->label('Pre-Employment Preview')
                        ->content(function () {
                            $jobTitle = optional($this->record->job)->title ?: '-';

                            return "Applicant: {$this->record->full_name}\n"
                                . "Job: {$jobTitle}\n"
                                . "Action: Mark as Hired + create Pre-Employment record";
                        }),
                ])
                ->requiresConfirmation()
                ->modalHeading('Move to Hired')
                ->modalDescription('This can automatically create a Pre-Employment record and notify the candidate.')
                ->modalSubmitActionLabel('Confirm')
                ->action(function (array $data) {
                    $this->updateStatus('hired', $data);
                }),

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

        $requestAction = Actions\Action::make('create_candidate_request')
            ->label('Create Request')
            ->icon('heroicon-o-document-text')
            ->color('primary')
            ->form([
                Select::make('type')
                    ->label('Request Type')
                    ->required()
                    ->options([
                        'document_request' => 'Document Request',
                        'missing_certificates' => 'Missing Certificates',
                        'passport_copy_request' => 'Passport Copy Request',
                        'experience_certificates_request' => 'Experience Certificates Request',
                        'salary_negotiation' => 'Salary Negotiation',
                        'availability_confirmation' => 'Availability Confirmation',
                        'offer_clarification' => 'Offer Clarification',
                        'general_special_request' => 'General Special Request',
                        'other' => 'Other',
                    ])
                    ->live(),

                TextInput::make('title')
                    ->label('Request Title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('notes')
                    ->label('Notes / Instructions')
                    ->rows(5),

                DatePicker::make('due_date')
                    ->label('Due Date'),

                Toggle::make('send_email')
                    ->label('Send email to candidate')
                    ->default(true),

                Placeholder::make('request_items_help')
                    ->label('Request Items Info')
                    ->content('For salary negotiation, request items are optional. You can send salary only, or salary + files/notes in the same request.'),

                Repeater::make('request_items')
                    ->label('Request Items')
                    ->defaultItems(0)
                    ->reorderable(false)
                    ->collapsible()
                    ->addActionLabel('Add Another Request')
                    ->schema([
                        Select::make('item_type')
                            ->label('Item Type')
                            ->required()
                            ->default('file')
                            ->options([
                                'file' => 'File Upload',
                                'note' => 'Information / Note',
                            ])
                            ->live(),

                        TextInput::make('label')
                            ->label('Item Title / Label')
                            ->required()
                            ->placeholder('Example: ATEX Certificate or Salary Expectation'),

                        Select::make('file_format')
                            ->label('File Format')
                            ->options([
                                'pdf' => 'PDF',
                                'image' => 'Image',
                                'pdf_or_image' => 'PDF or Image',
                                'document' => 'Document',
                                'other' => 'Other',
                            ])
                            ->visible(fn (callable $get) => ($get('item_type') ?? 'file') === 'file'),

                        Toggle::make('is_required')
                            ->label('Required')
                            ->default(true),

                        Toggle::make('allow_multiple')
                            ->label('Allow Multiple Uploads')
                            ->default(false)
                            ->visible(fn (callable $get) => ($get('item_type') ?? 'file') === 'file'),

                        Textarea::make('notes')
                            ->label('Item Notes')
                            ->rows(3),
                    ])
                    ->columns(2),

                Placeholder::make('salary_internal_note')
                    ->label('Salary Negotiation')
                    ->content('Use these fields when you want negotiation only, or negotiation together with request items.')
                    ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),

                TextInput::make('proposed_salary')
                    ->label('Proposed Salary')
                    ->numeric()
                    ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),

                Select::make('currency')
                          ->label('Currency')
                          ->options([
                             'USD' => 'US Dollar (USD)',
                             'EUR' => 'Euro (EUR)',
                             'GBP' => 'British Pound (GBP)',
                          'LYD' => 'Libyan Dinar (LYD)',
                 ])
    ->default('USD')
    ->searchable()
    ->required()
    ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),
                Toggle::make('requires_approval')
                    ->label('Requires Candidate Approval')
                    ->default(true)
                    ->visible(fn (callable $get) => in_array($get('type'), ['salary_negotiation'], true)),
            ])
            ->modalHeading('Create Candidate Request')
            ->modalSubmitActionLabel('Create Request')
            ->action(function (array $data): void {
                $requestItems = collect($data['request_items'] ?? [])
                    ->filter(fn ($item) => filled($item['label'] ?? null))
                    ->values()
                    ->all();

                $isSalaryNegotiation = ($data['type'] ?? null) === 'salary_negotiation';
                $hasSalaryValue = filled($data['proposed_salary'] ?? null);
                $hasRequestItems = count($requestItems) > 0;

                if ($isSalaryNegotiation && ! $hasSalaryValue && ! $hasRequestItems) {
                    Notification::make()
                        ->title('For salary negotiation, add a proposed salary or at least one request item.')
                        ->danger()
                        ->send();

                    return;
                }

                if (! $isSalaryNegotiation && ! $hasRequestItems) {
                    Notification::make()
                        ->title('Please add at least one request item for this request type.')
                        ->danger()
                        ->send();

                    return;
                }

                $hasFileItems = collect($requestItems)
                    ->contains(fn ($item) => ($item['item_type'] ?? 'file') === 'file');

                $request = $this->record->candidateRequests()->create([
                    'type' => $data['type'],
                    'title' => $data['title'],
                    'notes' => $data['notes'] ?? null,
                    'request_status' => 'pending',
                    'due_date' => $data['due_date'] ?? null,
                    'requires_upload' => $hasFileItems,
                    'proposed_salary' => $data['proposed_salary'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'requires_approval' => (bool) ($data['requires_approval'] ?? false),
                    'created_by' => Auth::id(),
                    'public_token' => (string) Str::uuid(),
                    'is_final_offer' => false,
                ]);

                foreach ($requestItems as $index => $item) {
                    $request->items()->create([
                        'item_type' => $item['item_type'] ?? 'file',
                        'label' => $item['label'],
                        'file_format' => ($item['item_type'] ?? 'file') === 'file'
                            ? ($item['file_format'] ?? null)
                            : null,
                        'is_required' => (bool) ($item['is_required'] ?? false),
                        'allow_multiple' => (bool) ($item['allow_multiple'] ?? false),
                        'notes' => $item['notes'] ?? null,
                        'sort_order' => $index + 1,
                    ]);
                }

                $decoded = [
                    'thread' => [[
                        'sender' => 'hr',
                        'event' => 'request_created',
                        'title' => $request->title,
                        'message' => $request->notes,
                        'salary' => $request->proposed_salary,
                        'currency' => $request->currency,
                        'created_at' => optional($request->created_at)?->toDateTimeString(),
                    ]],
                    'uploaded_files' => [],
                    'note_responses' => [],
                ];

                $request->update([
                    'candidate_response' => json_encode($decoded, JSON_UNESCAPED_UNICODE),
                ]);

                $this->record->update([
                    'candidate_request_status' => 'awaiting_response',
                ]);

                if ((bool) ($data['send_email'] ?? false) && filled($this->record->email)) {
                    try {
                        $portalUrl = rtrim(config('app.public_app_url') ?: config('app.url'), '/') . '/candidate-request/' . $request->public_token;

                        Mail::to($this->record->email)->send(
                            new CandidateRequestMail($request->fresh()->load('items', 'jobApplication.job'), $portalUrl)
                        );
                    } catch (\Throwable $e) {
                        Log::error('Candidate request email send failed', [
                            'job_application_id' => $this->record->id,
                            'candidate_request_id' => $request->id,
                            'email' => $this->record->email,
                            'message' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Request created, but email could not be sent')
                            ->warning()
                            ->send();
                    }
                }

                $this->record->refresh();

                Notification::make()
                    ->title('Candidate request created successfully')
                    ->success()
                    ->send();
            });

        $deleteCandidateRequestAction = Actions\Action::make('deleteCandidateRequestAction')
            ->label('Delete Candidate Request')
            ->color('danger')
            ->icon('heroicon-o-trash')
            ->visible(false)
            ->form([
                Hidden::make('request_id'),

                Toggle::make('confirm_delete')
                    ->label('I understand and want to delete this request')
                    ->required()
                    ->accepted(),
            ])
            ->mountUsing(function ($form, array $arguments) {
                $form->fill([
                    'request_id' => $arguments['request_id'] ?? null,
                ]);
            })
            ->requiresConfirmation()
            ->modalHeading('Delete Candidate Request')
            ->modalDescription('Do you want to delete this request? This action cannot be undone.')
            ->modalSubmitActionLabel('Yes, Delete')
            ->action(function (array $data): void {
                $requestId = $data['request_id'] ?? null;

                if (! $requestId) {
                    Notification::make()
                        ->title('Request not found')
                        ->danger()
                        ->send();

                    return;
                }

                if (! ($data['confirm_delete'] ?? false)) {
                    return;
                }

                $candidateRequest = $this->record->candidateRequests()
                    ->whereKey($requestId)
                    ->first();

                if (! $candidateRequest) {
                    Notification::make()
                        ->title('Request not found')
                        ->danger()
                        ->send();

                    return;
                }

                $candidateRequest->delete();

                $this->record->refresh();

                Notification::make()
                    ->title('Candidate request deleted successfully')
                    ->success()
                    ->send();
            });

        $moreActions = ActionGroup::make([
            $requestAction,
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->label('Delete')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Delete Application')
                ->modalDescription('Are you sure you want to permanently delete this application?')
                ->modalSubmitActionLabel('Yes, Delete'),
        ])
            ->label('More')
            ->icon('heroicon-o-ellipsis-horizontal')
            ->button()
            ->color('gray');

        $baseActions = [
            $deleteCandidateRequestAction,
            $moreActions,
        ];

        if ($isArchived) {
            return $baseActions;
        }

        return array_merge($statusActions, $baseActions);
    }

    protected function makeStatusAction(string $name, string $label, string $color, string $status): Actions\Action
    {
        return Actions\Action::make($name)
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

        if ($status === 'hired' && (bool) ($extraData['create_pre_employment'] ?? false)) {
            $preEmployment = $this->createOrGetPreEmployment();

            if ((bool) ($extraData['send_pre_employment_email'] ?? false) && filled($this->record->email)) {
                try {
                    Mail::to($this->record->email)->queue(
                        new PreEmploymentStartedMail($preEmployment)
                    );
                } catch (\Throwable $e) {
                    Log::error('Pre-employment email queue failed', [
                        'job_application_id' => $this->record->id,
                        'pre_employment_id' => $preEmployment->id ?? null,
                        'email' => $this->record->email,
                        'message' => $e->getMessage(),
                    ]);

                    Notification::make()
                        ->title('Pre-Employment created, but email could not be queued')
                        ->warning()
                        ->send();
                }
            }

            $this->record->update([
                'is_archived' => true,
                'archive_reason' => 'converted_to_pre_employment',
                'archived_at' => now(),
            ]);

            $this->record->refresh();

            Notification::make()
                ->title('Applicant moved to Hired and converted to Pre-Employment')
                ->success()
                ->send();

                return;
        }

        $sendEmail = (bool) ($extraData['send_email'] ?? false);
        $this->sendStatusEmailIfNeeded($status, $oldStatus, $sendEmail);

        if ($status === 'declined') {
            Notification::make()
                ->title('Applicant declined and archived')
                ->success()
                ->send();

            $this->redirect(ArchivedJobApplicationResource::getUrl('index'));

            return;
        }

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

    protected function createOrGetPreEmployment(): PreEmployment
    {
        $existing = PreEmployment::query()
            ->where('job_application_id', $this->record->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        return PreEmployment::create([
            'job_application_id' => $this->record->id,
            'job_id' => $this->record->job_id,
            'candidate_name' => $this->record->full_name,
            'candidate_email' => $this->record->email,
            'candidate_phone' => $this->record->phone ?: $this->record->whatsapp_number,
            'status' => 'initiated',
            'notes' => $this->record->notes,
        ]);
    }

    protected function sendStatusEmailIfNeeded(string $newStatus, ?string $oldStatus = null, bool $sendEmail = false): void
    {
        if ($newStatus === 'screening') {
            return;
        }

        if (! $sendEmail || $oldStatus === $newStatus || blank($this->record->email)) {
            return;
        }

        try {
            if ($newStatus === 'declined') {
                Mail::to($this->record->email)->queue(
                    new JobApplicationDeclinedMail(
                        $this->record,
                        $this->getDeclineReasonLabel($this->record->decline_reason),
                        $this->record->decline_notes,
                    )
                );

                return;
            }

            Mail::to($this->record->email)->queue(
                new JobApplicationStatusUpdatedMail(
                    $this->record,
                    $this->getStatusLabel($newStatus),
                    $this->getStatusEmailSubject($newStatus),
                    $this->getStatusEmailMessage($newStatus),
                )
            );
        } catch (\Throwable $e) {
            Log::error('Status email queue failed', [
                'job_application_id' => $this->record->id,
                'status' => $newStatus,
                'email' => $this->record->email,
                'message' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Status updated, but email could not be queued')
                ->warning()
                ->send();
        }
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