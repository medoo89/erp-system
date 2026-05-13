<?php

namespace App\Filament\Resources\PreEmployments\Pages;

use App\Filament\Resources\Employments\EmploymentResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;

use App\Filament\Resources\PreEmployments\PreEmploymentResource;

use App\Mail\PreEmploymentPortalRequestMail;

use App\Models\CandidateFinanceProfile;

use App\Models\Employment;
use App\Models\FinanceExpense;

use App\Models\Job;

use App\Services\EmploymentFileImportService;
use App\Services\PortalNotificationService;
use App\Services\AuditLogService;

use Filament\Actions\Action;

use Filament\Actions\DeleteAction;

use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Str;
use App\Models\PreEmploymentPortalField;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Select;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;

use Filament\Resources\Pages\ViewRecord;

use Illuminate\Support\Facades\Mail;

class ViewPreEmployment extends ViewRecord

{

    protected static string $resource = PreEmploymentResource::class;

    

    protected string $view = 'filament.resources.pre-employments.pages.view-pre-employment-premium';
public function getTitle(): string

    {

        return 'Pre-Employment Profile';

    }

    protected function getHeaderActions(): array

    {

        return [

            Action::make('backToJobApplication')
                ->label('Back to Job Applications')
                ->color('warning')
                ->icon('heroicon-o-arrow-uturn-left')
                ->visible(function (): bool {
                    return (bool) auth()->user()?->canErp('pre_employments', 'back_to_job_application')
                        && filled($this->record?->job_application_id)
                        && blank($this->record?->converted_to_employment_at);
                })
                ->disabled(fn (): bool => filled($this->record?->converted_to_employment_at))
                ->modalHeading('Back to Job Applications')
                ->modalDescription('This will reopen the linked Job Application and archive this Pre-Employment record as returned. No files, finance profiles, or portal data will be deleted.')
                ->modalSubmitActionLabel('Return to Job Applications')
                ->form([
                    Select::make('target_status')
                        ->label('Job Application Status')
                        ->options([
                            'qualified' => 'Qualified',
                            'under_review' => 'Under Review',
                            'screening' => 'Screening',
                        ])
                        ->default('qualified')
                        ->required()
                        ->native(false),

                    Textarea::make('return_reason')
                        ->label('Reason / Notes')
                        ->rows(4)
                        ->placeholder('Example: Candidate needs to be reviewed again before continuing pre-employment.'),

                    Toggle::make('confirm_return')
                        ->label('I confirm returning this Pre-Employment record back to Job Applications')
                        ->accepted()
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data): void {
                    if (! (bool) auth()->user()?->canErp('pre_employments', 'back_to_job_application')) {
                        Notification::make()
                            ->title('You do not have permission to perform this action')
                            ->danger()
                            ->send();

                        return;
                    }

                    if (filled($this->record?->converted_to_employment_at)) {
                        Notification::make()
                            ->title('This record is already converted to Employment')
                            ->body('You cannot return a converted Pre-Employment record back to Job Applications.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $jobApplication = $this->record->jobApplication;

                    if (! $jobApplication) {
                        Notification::make()
                            ->title('Linked Job Application not found')
                            ->body('This Pre-Employment record is not linked to a valid Job Application.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $oldPreEmploymentValues = $this->record->only([
                        'status',
                        'is_archived',
                        'archive_reason',
                        'archived_at',
                        'internal_notes',
                    ]);

                    $oldJobApplicationValues = $jobApplication->only([
                        'status',
                        'is_archived',
                        'archive_reason',
                        'archived_at',
                        'notes',
                    ]);

                    $targetStatus = $data['target_status'] ?? 'qualified';
                    $reason = trim((string) ($data['return_reason'] ?? ''));

                    $jobApplication->update([
                        'status' => $targetStatus,
                        'is_archived' => false,
                        'archive_reason' => null,
                        'archived_at' => null,
                        'notes' => trim((string) ($jobApplication->notes ?? '') . "\n\nReturned from Pre-Employment #" . $this->record->id . ($reason ? ": {$reason}" : '') . "\nReturned at: " . now()->toDateTimeString()),
                    ]);

                    $this->record->update([
                        'status' => 'returned_to_job_application',
                        'is_archived' => true,
                        'archive_reason' => 'reopened_to_job_application',
                        'archived_at' => now(),
                        'internal_notes' => trim((string) ($this->record->internal_notes ?? '') . "\n\nReturned to Job Application #" . $jobApplication->id . ($reason ? ": {$reason}" : '') . "\nReturned at: " . now()->toDateTimeString()),
                    ]);

                    try {
                        AuditLogService::updated(
                            'pre_employments',
                            $this->record->fresh(),
                            $oldPreEmploymentValues,
                            $this->record->fresh()->only([
                                'status',
                                'is_archived',
                                'archive_reason',
                                'archived_at',
                                'internal_notes',
                            ]),
                            'Returned Pre-Employment record back to Job Applications',
                            [
                                'job_application_id' => $jobApplication->id,
                                'target_status' => $targetStatus,
                                'reason' => $reason,
                            ]
                        );

                        AuditLogService::updated(
                            'job_applications',
                            $jobApplication->fresh(),
                            $oldJobApplicationValues,
                            $jobApplication->fresh()->only([
                                'status',
                                'is_archived',
                                'archive_reason',
                                'archived_at',
                                'notes',
                            ]),
                            'Job Application reopened from Pre-Employment',
                            [
                                'pre_employment_id' => $this->record->id,
                                'target_status' => $targetStatus,
                                'reason' => $reason,
                            ]
                        );
                    } catch (\Throwable $e) {
                        // Do not block the operational workflow if audit logging fails.
                    }

                    Notification::make()
                        ->title('Returned to Job Applications')
                        ->body('The linked application is now visible again in Job Applications.')
                        ->success()
                        ->send();

                    $this->redirect(JobApplicationResource::getUrl('view', ['record' => $jobApplication->id]));
                }),


            Action::make('editFinalProfile')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'finance_profile'))

                ->label(fn () => $this->record?->currentFinanceProfile ? 'Edit Final Profile' : 'Complete Final Profile')

                ->color(fn () => $this->finalProfileLocked() ? 'gray' : ($this->isFinanceReady() ? 'success' : 'warning'))

                ->icon('heroicon-o-banknotes')

                ->disabled(fn () => $this->finalProfileLocked())

                ->tooltip(fn () => $this->finalProfileLocked() ? $this->finalProfileLockReason() : null)

                ->modalHeading(fn () => $this->record?->currentFinanceProfile ? 'Edit Final Finance Profile' : 'Complete Final Finance Profile')

                ->modalDescription('Daily Rate, Client Billing Rate, and currencies must be completed before conversion to Employment.')

                ->modalSubmitActionLabel('Save Final Profile')

                ->fillForm(function (): array {

                    $profile = $this->record?->currentFinanceProfile;

                    return [

                        'salary_basis' => $profile?->salary_basis ?: CandidateFinanceProfile::BASIS_DAILY_RATE,

                        'daily_rate' => $profile?->daily_rate,

                        'monthly_salary' => $profile?->monthly_salary,

                        'payout_currency' => $profile?->payout_currency ?: 'EUR',

                        'client_billing_basis' => $profile?->client_billing_basis ?: CandidateFinanceProfile::BASIS_DAILY_RATE,

                        'client_billing_rate' => $profile?->client_billing_rate,

                        'client_billing_currency' => $profile?->client_billing_currency ?: 'EUR',

                        'effective_from' => optional($profile?->effective_from)->format('Y-m-d'),

                        'effective_to' => optional($profile?->effective_to)->format('Y-m-d'),

                        'finance_notes' => $profile?->finance_notes,

                    ];

                })

                ->form([

                    Select::make('salary_basis')

                        ->label('Salary Basis')

                        ->options([

                            CandidateFinanceProfile::BASIS_DAILY_RATE => 'Daily Rate',

                            CandidateFinanceProfile::BASIS_MONTHLY => 'Monthly',

                        ])

                        ->default(CandidateFinanceProfile::BASIS_DAILY_RATE)

                        ->required(),

                    TextInput::make('daily_rate')

                        ->label('Daily Rate')

                        ->numeric()

                        ->required(),

                    TextInput::make('monthly_salary')

                        ->label('Monthly Salary')

                        ->numeric(),

                    Select::make('payout_currency')

                        ->label('Payout Currency')

                        ->options([

                            'EUR' => 'EUR',

                            'USD' => 'USD',

                            'GBP' => 'GBP',

                            'LYD' => 'LYD',

                        ])

                        ->native(false)

                        ->required(),

                    Select::make('client_billing_basis')

                        ->label('Client Billing Basis')

                        ->options([

                            CandidateFinanceProfile::BASIS_DAILY_RATE => 'Daily Rate',

                            CandidateFinanceProfile::BASIS_MONTHLY => 'Monthly',

                        ])

                        ->default(CandidateFinanceProfile::BASIS_DAILY_RATE)

                        ->required(),

                    TextInput::make('client_billing_rate')

                        ->label('Client Billing Rate')

                        ->numeric()

                        ->required(),

                    Select::make('client_billing_currency')

                        ->label('Client Billing Currency')

                        ->options([

                            'EUR' => 'EUR',

                            'USD' => 'USD',

                            'GBP' => 'GBP',

                            'LYD' => 'LYD',

                        ])

                        ->native(false)

                        ->required(),

                    DatePicker::make('effective_from')

                        ->label('Effective From'),

                    DatePicker::make('effective_to')

                        ->label('Effective To'),

                    Textarea::make('finance_notes')

                        ->label('Finance Notes')

                        ->rows(4)

                        ->columnSpanFull(),

                ])

                ->action(function (array $data) {

                    if ($this->finalProfileLocked()) {

                        Notification::make()

                            ->title('Final Finance Profile is locked')

                            ->body($this->finalProfileLockReason())

                            ->danger()

                            ->send();

                        return;

                    }

                    CandidateFinanceProfile::query()

                        ->where('pre_employment_id', $this->record->id)

                        ->update(['is_current' => false]);

                    $profile = $this->record->currentFinanceProfile;

                    if ($profile) {

                        $profile->update([

                            'salary_basis' => $data['salary_basis'],

                            'daily_rate' => $data['daily_rate'],

                            'monthly_salary' => $data['monthly_salary'] ?? null,

                            'payout_currency' => $data['payout_currency'],

                            'client_billing_basis' => $data['client_billing_basis'],

                            'client_billing_rate' => $data['client_billing_rate'],

                            'client_billing_currency' => $data['client_billing_currency'],

                            'effective_from' => $data['effective_from'] ?? null,

                            'effective_to' => $data['effective_to'] ?? null,

                            'finance_notes' => $data['finance_notes'] ?? null,

                            'is_current' => true,

                            'is_hidden_from_non_finance' => true,

                        ]);

                    } else {

                        CandidateFinanceProfile::create([

                            'job_application_id' => $this->record->job_application_id,

                            'pre_employment_id' => $this->record->id,

                            'job_id' => $this->record->job_id,

                            'client_id' => $this->record->job?->project?->client?->id,

                            'project_id' => $this->record->job?->project?->id,

                            'finance_status' => 'active',

                            'salary_basis' => $data['salary_basis'],

                            'daily_rate' => $data['daily_rate'],

                            'monthly_salary' => $data['monthly_salary'] ?? null,

                            'payout_currency' => $data['payout_currency'],

                            'client_billing_basis' => $data['client_billing_basis'],

                            'client_billing_rate' => $data['client_billing_rate'],

                            'client_billing_currency' => $data['client_billing_currency'],

                            'source_type' => 'pre_employment',

                            'effective_from' => $data['effective_from'] ?? now()->toDateString(),

                            'effective_to' => $data['effective_to'] ?? null,

                            'is_current' => true,

                            'is_hidden_from_non_finance' => true,

                            'finance_notes' => $data['finance_notes'] ?? null,

                        ]);

                    }

                    $this->record->refresh();

                    Notification::make()

                        ->title('Final Finance Profile saved successfully')

                        ->success()

                        ->send();

                }),

            Action::make('openPortalLink')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'open_public_link'))

                ->label('Open Public Link')

                ->color('gray')

                ->icon('heroicon-o-arrow-top-right-on-square')

                ->url(fn () => $this->record?->portal_token ? url('/pre-employment/portal/' . $this->record->portal_token) : null)

                ->openUrlInNewTab()

                ->visible(fn () => filled($this->record?->portal_token)),

            Action::make('sendPortalRequest')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'send_public_link'))

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



            Action::make('addExpense')
                ->label('Add Expense')
                ->icon('heroicon-o-banknotes')
                ->color('danger')
                ->modalHeading('Add Pre-Employment Expense')
                ->modalDescription('This expense will be linked to this Pre-Employment record automatically.')
                ->modalSubmitActionLabel('Save Expense')
                ->form([
                    TextInput::make('title')
                        ->label('Expense Title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Visa, ticket, medical, hotel, transport...'),

                    Select::make('category')
                        ->label('Category')
                        ->options([
                            'visa' => 'Visa',
                            'ticket' => 'Ticket',
                            'hotel' => 'Hotel',
                            'food' => 'Food',
                            'transport' => 'Transport',
                            'medical' => 'Medical',
                            'training' => 'Training',
                            'field_cost' => 'Field Cost',
                            'accommodation' => 'Accommodation',
                            'desert_pass' => 'Desert Pass',
                            'other' => 'Other',
                        ])
                        ->default('other')
                        ->native(false)
                        ->required(),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($get('paid_by') === FinanceExpense::PAID_BY_CANDIDATE && blank($get('reimbursement_amount'))) {
                                $set('reimbursement_amount', $state);
                            }
                        })
                        ->required(),

                    Select::make('currency')
                        ->label('Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'LYD' => 'LYD',
                            'GBP' => 'GBP',
                        ])
                        ->default('EUR')
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($get('paid_by') === FinanceExpense::PAID_BY_CANDIDATE) {
                                $set('reimbursement_currency', $state);
                            }
                        })
                        ->required(),

                    DatePicker::make('expense_date')
                        ->label('Expense Date')
                        ->default(now())
                        ->required(),

                    Select::make('paid_by')
                        ->label('Paid By')
                        ->options([
                            'company' => 'Company',
                            'candidate' => 'Candidate / Employee',
                            'client' => 'Client',
                            'third_party' => 'Third Party',
                        ])
                        ->default('company')
                        ->native(false)
                        ->live()
                        ->required()
                        ->helperText('Candidate / Employee means out-of-pocket claim, not company-paid yet.'),

                    Select::make('reimbursement_status')
                        ->label('Reimbursement Status')
                        ->options([
                            'not_applicable' => 'Not Applicable',
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'paid' => 'Paid',
                            'rejected' => 'Rejected',
                        ])
                        ->default('not_applicable')
                        ->native(false)
                        ->visible(fn ($get) => $get('paid_by') === 'candidate')
                        ->helperText('Use Pending when the candidate paid from pocket and needs reimbursement.'),

                    TextInput::make('reimbursement_amount')
                        ->label('Reimbursement Amount')
                        ->numeric()
                        ->visible(fn ($get) => $get('paid_by') === 'candidate'),

                    Select::make('reimbursement_currency')
                        ->label('Reimbursement Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'LYD' => 'LYD',
                            'GBP' => 'GBP',
                        ])
                        ->default('EUR')
                        ->native(false)
                        ->visible(fn ($get) => $get('paid_by') === 'candidate'),

                    Textarea::make('reimbursement_notes')
                        ->label('Reimbursement Notes')
                        ->rows(3)
                        ->visible(fn ($get) => $get('paid_by') === 'candidate'),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'approved' => 'Approved',
                            'paid' => 'Paid',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft')
                        ->native(false)
                        ->required(),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $record = $this->record;

                    $paidBy = $data['paid_by'] ?? FinanceExpense::PAID_BY_COMPANY;
                    $isCandidatePaid = $paidBy === FinanceExpense::PAID_BY_CANDIDATE;
                    $amount = $data['amount'] ?? 0;
                    $currency = $data['currency'] ?? 'EUR';

                    FinanceExpense::query()->create([
                        'job_application_id' => $record->job_application_id,
                        'pre_employment_id' => $record->id,
                        'employment_id' => null,
                        'job_id' => $record->job_id,
                        'client_id' => $record->job?->project?->client_id,
                        'project_id' => $record->job?->project_id,
                        'candidate_finance_profile_id' => $record->currentFinanceProfile?->id,
                        'created_by' => auth()->id(),
                        'expense_scope' => FinanceExpense::SCOPE_PRE_HIRE,
                        'category' => $data['category'] ?? FinanceExpense::CATEGORY_OTHER,
                        'expense_category' => $data['category'] ?? FinanceExpense::CATEGORY_OTHER,
                        'title' => $data['title'] ?? 'Pre-Employment Expense',
                        'description' => $data['description'] ?? null,
                        'amount' => $amount,
                        'currency' => $currency,
                        'expense_date' => $data['expense_date'] ?? now(),
                        'paid_by' => $paidBy,
                        'reimbursement_required' => $isCandidatePaid,
                        'reimbursement_status' => $isCandidatePaid
                            ? ($data['reimbursement_status'] ?? FinanceExpense::REIMBURSEMENT_PENDING)
                            : FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE,
                        'reimbursement_amount' => $isCandidatePaid ? ($data['reimbursement_amount'] ?? $amount) : null,
                        'reimbursement_currency' => $isCandidatePaid ? ($data['reimbursement_currency'] ?? $currency) : $currency,
                        'reimbursement_notes' => $isCandidatePaid ? ($data['reimbursement_notes'] ?? null) : null,
                        'status' => $data['status'] ?? FinanceExpense::STATUS_DRAFT,
                        'notes' => $data['notes'] ?? null,
                        'is_company_expense' => $paidBy === FinanceExpense::PAID_BY_COMPANY,
                        'is_manual_expense' => true,
                        'candidate_submitted' => false,
                    ]);

                    Notification::make()
                        ->title('Pre-Employment expense added')
                        ->body('The expense was linked to this Pre-Employment profile.')
                        ->success()
                        ->send();
                }),


            Action::make('requestPreEmploymentFile')
                ->label('Request File')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->modalHeading('Request file from candidate')
                ->modalDescription('Create a portal file request. You can request upload only, or attach a document that the candidate must download, sign, and upload back.')
                ->modalWidth('2xl')
                ->form([
                    TextInput::make('label')
                        ->label('Document Label')
                        ->placeholder('Passport / Contract Agreement / Health Certificate / ATEX Certificate')
                        ->required()
                        ->maxLength(255),

                    Select::make('document_category')
                        ->label('Document Category')
                        ->options([
                            'passport' => 'Passport',
                            'contract' => 'Contract',
                            'signed_contract' => 'Signed Contract',
                            'medical_certificate' => 'Medical / Health Certificate',
                            'atex_certificate' => 'ATEX Certificate',
                            'photo' => 'Personal Photo',
                            'visa' => 'Visa',
                            'cv' => 'CV',
                            'other' => 'Other',
                        ])
                        ->default('other')
                        ->native(false)
                        ->required(),

                    Select::make('request_type')
                        ->label('Request Type')
                        ->options([
                            'upload_only' => 'Upload File Only',
                            'download_sign_upload' => 'Download, Sign & Re-upload',
                        ])
                        ->default('upload_only')
                        ->native(false)
                        ->live()
                        ->required(),

                    Textarea::make('instructions')
                        ->label('Instructions')
                        ->rows(4)
                        ->placeholder('Example: Please download the contract, sign every page, then upload the signed copy.'),

                    FileUpload::make('document_to_sign_path')
                        ->label('Document to Sign')
                        ->disk('public')
                        ->directory(fn () => 'pre-employment-signing-requests/' . ($this->record?->id ?? 'new'))
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->visible(fn ($get) => $get('request_type') === 'download_sign_upload'),

                    Toggle::make('is_required')
                        ->label('Required')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $record = $this->record;

                    $label = trim((string) ($data['label'] ?? 'Document'));
                    $category = $data['document_category'] ?? 'other';
                    $requestType = $data['request_type'] ?? 'upload_only';
                    $documentToSignPath = $data['document_to_sign_path'] ?? null;

                    $slugBase = Str::slug(($record->candidate_name ?: 'candidate') . '-' . $label);
                    $fieldKey = $slugBase . '-' . now()->format('YmdHis');

                    PreEmploymentPortalField::query()->create([
                        'pre_employment_id' => $record->id,
                        'label' => $label,
                        'field_key' => $fieldKey,
                        'field_type' => 'file',
                        'request_type' => $requestType,
                        'document_category' => $category,
                        'document_to_sign_path' => $documentToSignPath,
                        'document_to_sign_original_name' => $documentToSignPath ? basename((string) $documentToSignPath) : null,
                        'signed_file_required' => $requestType === 'download_sign_upload',
                        'is_required' => (bool) ($data['is_required'] ?? true),
                        'is_active' => true,
                        'visible_to_candidate' => true,
                        'instructions' => $data['instructions'] ?? null,
                        'sort_order' => (int) (PreEmploymentPortalField::query()
                            ->where('pre_employment_id', $record->id)
                            ->max('sort_order') ?? 0) + 10,
                    ]);

                    $record->update([
                        'portal_status' => 'sent',
                        'portal_last_sent_at' => now(),
                        'status' => in_array($record->status, ['initiated', 'under_preparation'], true)
                            ? 'awaiting_candidate_upload'
                            : $record->status,
                    ]);

                    if (filled($record->candidate_email)) {
                        Mail::to($record->candidate_email)
                            ->send(new PreEmploymentPortalRequestMail($record->fresh(), true));
                    }

                    try {
                        $record->loadMissing('employment');

                        if ($record->employment) {
                            app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                                employment: $record->employment,
                                category: 'file_request',
                                title: 'New File Requested',
                                message: 'A new file has been requested from you: ' . $label . '. Please open your portal and upload it.',
                                portalPath: '/portal/files',
                                related: $record,
                                sendEmail: true,
                            );
                        }
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    Notification::make()
                        ->title('File request created')
                        ->body('The candidate portal has been updated and the request email was sent if an email exists.')
                        ->success()
                        ->send();
                }),

            Action::make('uploadPreEmploymentFile')
                ->label('Upload File')
                ->icon('heroicon-o-paper-clip')
                ->color('info')
                ->visible(fn (): bool => (bool) auth()->user()?->canErp('pre_employments', 'edit'))
                ->modalHeading('Upload Pre-Employment File')
                ->modalDescription('Upload an internal company file directly to this Pre-Employment profile.')
                ->modalSubmitActionLabel('Upload File')
                ->form([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label('File Title')
                        ->placeholder('Passport / Contract / Medical Certificate')
                        ->required()
                        ->maxLength(180),

                    \Filament\Forms\Components\Select::make('category')
                        ->label('Category')
                        ->options([
                            'passport' => 'Passport',
                            'contract' => 'Contract',
                            'medical_certificate' => 'Medical / Health Certificate',
                            'certificate' => 'Certificate',
                            'cv' => 'CV',
                            'visa' => 'Visa',
                            'travel' => 'Travel Document',
                            'other' => 'Other',
                        ])
                        ->default('other')
                        ->native(false)
                        ->required(),

                    \Filament\Forms\Components\DatePicker::make('document_date')
                        ->label('Document Date'),

                    \Filament\Forms\Components\DatePicker::make('expiry_date')
                        ->label('Expiry Date'),

                    \Filament\Forms\Components\FileUpload::make('file_path')
                        ->label('File')
                        ->disk('public')
                        ->directory('pre-employment-files')
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $record = $this->record;

                    if (! $record) {
                        \Filament\Notifications\Notification::make()
                            ->title('Pre-Employment record not found')
                            ->danger()
                            ->send();

                        return;
                    }

                    \App\Models\PreEmploymentFile::query()->create([
                        'pre_employment_id' => $record->id,
                        'title' => $data['title'] ?? 'Uploaded File',
                        'category' => $data['category'] ?? 'other',
                        'document_date' => $data['document_date'] ?? null,
                        'expiry_date' => $data['expiry_date'] ?? null,
                        'file_path' => $data['file_path'] ?? null,
                        'uploaded_by_type' => 'admin',
                        'uploaded_by_user_id' => auth()->id(),
                        'notes' => $data['notes'] ?? null,
                        'is_active' => true,
                        'is_current' => true,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('File uploaded')
                        ->body('The file was added to the Pre-Employment files block.')
                        ->success()
                        ->send();
                }),


            Action::make('changePreEmploymentStatus')
                ->label('Change Status')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('info')
                ->modalHeading('Change Pre-Employment Status')
                ->modalDescription('Update the current pre-employment workflow status. Convert to Employment will only be available when the status is Ready for Employment.')
                ->modalSubmitActionLabel('Update Status')
                ->fillForm(fn (): array => [
                    'status' => $this->record?->status ?: 'initiated',
                ])
                ->form([
                    Select::make('status')
                        ->label('Current Status')
                        ->options([
                            'initiated' => 'Initiated',
                            'under_preparation' => 'Under Preparation',
                            'awaiting_candidate_upload' => 'Awaiting Candidate Upload',
                            'documents_under_review' => 'Documents Under Review',
                            'additional_documents_required' => 'Additional Documents Required',
                            'pending_medical' => 'Pending Medical',
                            'pending_visa' => 'Pending Visa',
                            'pending_travel' => 'Pending Travel',
                            'ready_for_employment' => 'Ready for Employment',
                            'returned_to_job_application' => 'Returned to Job Application',
                            'converted_to_employment' => 'Converted to Employment',
                        ])
                        ->native(false)
                        ->required(),

                    Textarea::make('status_note')
                        ->label('Status Note')
                        ->rows(3)
                        ->placeholder('Optional note about this status change.'),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record?->status;
                    $newStatus = $data['status'] ?? $oldStatus;
                    $note = trim((string) ($data['status_note'] ?? ''));

                    $internalNotes = trim((string) ($this->record->internal_notes ?? ''));

                    $historyLine = "Status changed from " . ($oldStatus ?: '-') . " to " . ($newStatus ?: '-') . " at " . now()->toDateTimeString();

                    if ($note !== '') {
                        $historyLine .= "\nNote: " . $note;
                    }

                    $this->record->update([
                        'status' => $newStatus,
                        'internal_notes' => trim($internalNotes . "\n\n" . $historyLine),
                    ]);

                    Notification::make()
                        ->title('Pre-Employment status updated')
                        ->body($newStatus === 'ready_for_employment'
                            ? 'Candidate is now ready for employment. Convert to Employment is available.'
                            : 'Status was updated successfully.')
                        ->success()
                        ->send();
                }),

            Action::make('convertToEmployment')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'convert_employment'))

                ->label('Convert to Employment')

                ->color('primary')

                ->requiresConfirmation()

                ->modalHeading('Convert to Employment')

                ->modalDescription('This will create a new Employment profile, copy finance data, and copy files from Job Application + Pre-Employment into Employment. Conversion stays locked until Final Finance Profile is complete.')

                ->modalSubmitActionLabel('Yes, Convert')

                ->visible(fn () => blank($this->record?->converted_to_employment_at))

                ->disabled(fn () => ! $this->canConvertToEmployment())

                ->tooltip(fn () => ! $this->canConvertToEmployment() ? $this->conversionBlockReason() : null)

                ->action(function () {

                    $financeProfile = $this->record->currentFinanceProfile;

                    if (! $financeProfile || ! $this->isFinanceReady()) {

                        Notification::make()

                            ->title('Cannot convert to Employment')

                            ->body($this->conversionBlockReason())

                            ->danger()

                            ->send();

                        return;

                    }

                    $job = filled($this->record->job_id)

                        ? Job::with('project.client')->find($this->record->job_id)

                        : null;

                    if (! $job) {

                        Notification::make()

                            ->title('Cannot convert to Employment')

                            ->body('Linked job record is missing or invalid.')

                            ->danger()

                            ->send();

                        return;

                    }

                    $employment = Employment::create([

                        'pre_employment_id' => $this->record->id,

                        'job_id' => $job->id,

                        'position_title' => $job->title,

                        'client_name' => $job->project?->client?->name,

                        'project_name' => $job->project?->name,

                        'assigned_hr_user_id' => null,

                        'operation_officer_name' => null,

                        'employee_name' => $this->record->candidate_name,

                        'employee_email' => $this->record->candidate_email,

                        'employee_phone' => $this->record->candidate_phone,

                        'employee_code' => $this->record->employee_code,

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

                        'visa_status' => $this->normalizeEmploymentVisaStatus($this->record->visa_status),

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

                    app(EmploymentFileImportService::class)->copyFromPreEmployment($employment, $this->record);

                    if ($this->record->jobApplication) {

                        app(EmploymentFileImportService::class)->copyFromJobApplication($employment, $this->record->jobApplication);

                        app(EmploymentFileImportService::class)->copyCandidateRequestFilesFromJobApplication($employment, $this->record->jobApplication);

                    }

                    CandidateFinanceProfile::query()

                        ->where('employment_id', $employment->id)

                        ->update(['is_current' => false]);

                    CandidateFinanceProfile::create([

                        'job_application_id' => $this->record->job_application_id,

                        'pre_employment_id' => $this->record->id,

                        'employment_id' => $employment->id,

                        'job_id' => $job->id,

                        'client_id' => $job->project?->client?->id,

                        'project_id' => $job->project?->id,

                        'finance_status' => 'active',

                        'salary_basis' => $financeProfile->salary_basis,

                        'agreed_salary_amount' => $financeProfile->agreed_salary_amount,

                        'agreed_salary_currency' => $financeProfile->agreed_salary_currency,

                        'daily_rate' => $financeProfile->daily_rate,

                        'monthly_salary' => $financeProfile->monthly_salary,

                        'payout_currency' => $financeProfile->payout_currency,

                        'client_billing_basis' => $financeProfile->client_billing_basis,

                        'client_billing_rate' => $financeProfile->client_billing_rate,

                        'client_billing_currency' => $financeProfile->client_billing_currency,

                        'source_type' => 'employment',

                        'effective_from' => $financeProfile->effective_from ?: now()->toDateString(),

                        'effective_to' => $financeProfile->effective_to,

                        'is_current' => true,

                        'is_hidden_from_non_finance' => $financeProfile->is_hidden_from_non_finance ?? true,

                        'finance_notes' => $financeProfile->finance_notes,

                    ]);

                    /*
                     * Critical conversion rule:
                     * Pre-Employment expenses must travel with the candidate after conversion.
                     * We do NOT duplicate expenses. We keep pre_employment_id for history and attach
                     * employment_id so the same records appear in Employment, Employee Portal,
                     * Finance Expenses, Global Finance Totals, reimbursement, and salary workflows.
                     */
                    FinanceExpense::query()
                        ->where('pre_employment_id', $this->record->id)
                        ->whereNull('employment_id')
                        ->update([
                            'employment_id' => $employment->id,
                            'job_id' => $job->id,
                            'client_id' => $job->project?->client?->id,
                            'project_id' => $job->project?->id,
                            'candidate_finance_profile_id' => $employment->currentFinanceProfile?->id,
                        ]);

                    /*
                     * Carry all Pre-Employment expenses into the new Employment profile.
                     * The expense scope remains pre_hire so finance can still see that the cost
                     * started before employment, but the expense is now also linked to the employee.
                     */
                    FinanceExpense::query()
                        ->where('pre_employment_id', $this->record->id)
                        ->whereNull('employment_id')
                        ->update([
                            'employment_id' => $employment->id,
                        ]);

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
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'edit'))

                ->label('Edit')

                ->color('primary')

                ->url(fn () => PreEmploymentResource::getUrl('edit', ['record' => $this->record])),

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

    protected function normalizeEmploymentVisaStatus(?string $status): ?string

    {

        if (! filled($status)) {

            return null;

        }

        return match ($status) {

            'approved' => 'issued',

            default => $status,

        };

    }

    protected function isFinanceReady(): bool

    {

        $profile = $this->record?->currentFinanceProfile;

        if (! $profile) {

            return false;

        }

        $hasDailyRate = filled($profile->daily_rate) && (float) $profile->daily_rate > 0;

        $hasBillingRate = filled($profile->client_billing_rate) && (float) $profile->client_billing_rate > 0;

        $hasPayoutCurrency = filled($profile->payout_currency);

        $hasBillingCurrency = filled($profile->client_billing_currency);

        $hasBasis = filled($profile->salary_basis);

        return $hasDailyRate && $hasBillingRate && $hasPayoutCurrency && $hasBillingCurrency && $hasBasis;

    }

    protected function canConvertToEmployment(): bool

    {

        if (filled($this->record?->converted_to_employment_at)) {

            return false;

        }

        if ($this->record?->status !== 'ready_for_employment') {

            return false;

        }

        return $this->isFinanceReady();

    }

    protected function conversionBlockReason(): string

    {

        if (filled($this->record?->converted_to_employment_at)) {

            return 'This record has already been converted to Employment.';

        }

        if ($this->record?->status !== 'ready_for_employment') {

            return 'Conversion is only allowed when status is Ready for Employment.';

        }

        $profile = $this->record?->currentFinanceProfile;

        if (! $profile) {

            return 'Final Finance Profile is missing.';

        }

        $missing = [];

        if (! filled($profile->salary_basis)) {

            $missing[] = 'Salary Basis';

        }

        if (! filled($profile->daily_rate) || (float) $profile->daily_rate <= 0) {

            $missing[] = 'Daily Rate';

        }

        if (! filled($profile->payout_currency)) {

            $missing[] = 'Payout Currency';

        }

        if (! filled($profile->client_billing_rate) || (float) $profile->client_billing_rate <= 0) {

            $missing[] = 'Client Billing Rate';

        }

        if (! filled($profile->client_billing_currency)) {

            $missing[] = 'Client Billing Currency';

        }

        return ! empty($missing)

            ? 'Missing required final finance fields: ' . implode(', ', $missing) . '.'

            : 'Conversion is currently blocked.';

    }

    protected function finalProfileLocked(): bool

    {

        return filled($this->record?->converted_to_employment_at);

    }

    protected function finalProfileLockReason(): string

    {

        if (filled($this->record?->converted_to_employment_at)) {

            return 'Final Finance Profile is locked because this record has already been converted to Employment.';

        }

        return 'Final Finance Profile is locked.';

    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'view') ?? false);
    }

}
