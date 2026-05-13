<?php

namespace App\Filament\Resources\Employments\Pages;

use Illuminate\Support\Str;
use App\Models\PreEmploymentPortalField;
use App\Services\PortalNotificationService;
use App\Filament\Resources\Employments\EmploymentResource;
use App\Filament\Resources\Employments\Widgets\EmploymentFinanceSummary;
use App\Models\CandidateFinanceProfile;
use App\Models\SalarySlip;
use App\Models\User;
use App\Services\SalarySlipGenerationService;
use App\Services\EmployeePortalProvisioningService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Schema as DbSchema;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class ViewEmployment extends ViewRecord
{
    protected static string $resource = EmploymentResource::class;

    protected string $view = 'filament.resources.employments.pages.view-employment';

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        $name = $this->record?->employee_name ?: 'Employee';

        return "Employment Profile — {$name}";
    }

    protected function getHeaderActions(): array
    {
        return [

            Action::make('createErpUserLogin')
                ->label(fn () => User::query()->where('email', $this->record->employee_email)->exists()
                    ? 'Open ERP User'
                    : 'Create ERP User Login')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->visible(fn () => (bool) auth()->user()?->canErp('access_control', 'create') || (bool) auth()->user()?->isSuperAdmin())
                ->modalHeading('Create ERP user login')
                ->modalWidth('2xl')
                ->form([
                    TextInput::make('name')
                        ->label('User Name')
                        ->default(fn () => $this->record->employee_name)
                        ->required(),

                    TextInput::make('email')
                        ->label('Login Email')
                        ->email()
                        ->default(fn () => $this->record->employee_email)
                        ->required(),

                    Select::make('erp_role')
                        ->label('ERP Role')
                        ->options(User::erpRoleOptions())
                        ->default(fn () => ($this->record->employee_category ?? 'operational') === 'office' ? 'viewer' : 'viewer')
                        ->native(false)
                        ->required(),

                    TextInput::make('erp_department')
                        ->label('ERP Department')
                        ->default(fn () => $this->record->office_department ?: 'operations')
                        ->placeholder('finance / hr / recruitment / operations'),

                    TextInput::make('password')
                        ->label('Temporary Password')
                        ->default('password123')
                        ->password()
                        ->revealable()
                        ->required()
                        ->minLength(8),
                ])
                ->action(function (array $data): void {
                    $employment = $this->record;

                    if (blank($data['email'] ?? null)) {
                        Notification::make()
                            ->title('Employee email is required')
                            ->danger()
                            ->send();

                        return;
                    }

                    $existing = User::query()
                        ->where('email', strtolower(trim($data['email'])))
                        ->first();

                    if ($existing) {
                        $existing->forceFill([
                            'employment_id' => $existing->employment_id ?: $employment->id,
                            'name' => $data['name'] ?: $existing->name,
                            'erp_role' => $data['erp_role'] ?: $existing->erp_role,
                            'erp_department' => $data['erp_department'] ?? $existing->erp_department,
                            'is_admin' => true,
                            'user_type' => User::TYPE_ADMIN,
                        ])->save();

                        Notification::make()
                            ->title('ERP user already exists')
                            ->body('The existing user was linked/updated. You can manage permissions from Access Control.')
                            ->success()
                            ->send();

                        return;
                    }

                    $permissions = User::defaultErpPermissionsForRole($data['erp_role'] ?? 'viewer');

                    User::query()->create([
                        'employment_id' => $employment->id,
                        'name' => $data['name'],
                        'email' => strtolower(trim($data['email'])),
                        'password' => Hash::make($data['password']),
                        'is_admin' => true,
                        'user_type' => User::TYPE_ADMIN,
                        'erp_role' => $data['erp_role'],
                        'erp_department' => $data['erp_department'] ?? null,
                        'erp_permissions' => json_encode($permissions, JSON_UNESCAPED_UNICODE),
                    ]);

                    Notification::make()
                        ->title('ERP user login created')
                        ->body('The user can now log in. Review detailed permissions from Access Control.')
                        ->success()
                        ->send();
                }),


            Action::make('enableErpUserLogin')
                ->label('Enable ERP User')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(function () {
                    if (! ((bool) auth()->user()?->isSuperAdmin() || (bool) auth()->user()?->canErp('access_control', 'edit'))) {
                        return false;
                    }

                    if (blank($this->record->employee_email)) {
                        return false;
                    }

                    $user = User::query()
                        ->where('email', strtolower(trim($this->record->employee_email)))
                        ->first();

                    return (bool) $user && ! (bool) $user->is_admin;
                })
                ->requiresConfirmation()
                ->modalHeading('Enable ERP access')
                ->modalDescription('This will allow this office employee user to access the ERP admin panel according to assigned page rules.')
                ->modalSubmitActionLabel('Yes, Enable')
                ->action(function (): void {
                    $user = User::query()
                        ->where('email', strtolower(trim($this->record->employee_email)))
                        ->first();

                    if (! $user) {
                        Notification::make()
                            ->title('ERP user not found')
                            ->body('Create ERP User Login first.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $user->forceFill([
                        'is_admin' => true,
                        'user_type' => User::TYPE_ADMIN,
                    ])->save();

                    Notification::make()
                        ->title('ERP user enabled')
                        ->success()
                        ->send();
                }),

            Action::make('disableErpUserLogin')
                ->label('Disable ERP User')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(function () {
                    if (! ((bool) auth()->user()?->isSuperAdmin() || (bool) auth()->user()?->canErp('access_control', 'edit'))) {
                        return false;
                    }

                    if (blank($this->record->employee_email)) {
                        return false;
                    }

                    $user = User::query()
                        ->where('email', strtolower(trim($this->record->employee_email)))
                        ->first();

                    return (bool) $user && (bool) $user->is_admin;
                })
                ->requiresConfirmation()
                ->modalHeading('Disable ERP access')
                ->modalDescription('This will stop this employee from accessing the ERP admin panel. The user record and permissions will remain saved.')
                ->modalSubmitActionLabel('Yes, Disable')
                ->action(function (): void {
                    $user = User::query()
                        ->where('email', strtolower(trim($this->record->employee_email)))
                        ->first();

                    if (! $user) {
                        Notification::make()
                            ->title('ERP user not found')
                            ->danger()
                            ->send();

                        return;
                    }

                    $user->forceFill([
                        'is_admin' => false,
                    ])->save();

                    Notification::make()
                        ->title('ERP user disabled')
                        ->success()
                        ->send();
                }),

            ActionGroup::make([
                Action::make('sendPortalPasswordSetup')
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'portal_send_password'))
                    ->label('Send Password Setup Email')
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Send employee portal password setup email?')
                    ->modalDescription('This will create or activate the employee portal user and send a secure password setup email to the employee email address.')
                    ->action(function (): void {
                        $employment = $this->record;

                        if (blank($employment->employee_email)) {
                            Notification::make()
                                ->title('Employee email is missing')
                                ->body('Please add an employee email before sending the portal password setup email.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $ok = app(EmployeePortalProvisioningService::class)
                            ->sendPortalAccountAccessEmail($employment, 'setup');

                        $employment->refresh();

                        Notification::make()
                            ->title($ok ? 'Portal access email sent' : 'Portal access email was not sent')
                            ->body($ok ? 'The employee received the portal login URL and temporary password.' : 'Please check employee email and mail configuration.')
                            ->{$ok ? 'success' : 'danger'}()
                            ->send();
                    }),

                Action::make('resetPortalPassword')
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'portal_reset_password'))
                    ->label('Reset Portal Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Send password reset email?')
                    ->modalDescription('This will send a new secure password reset link to the employee email.')
                    ->action(function (): void {
                        $employment = $this->record;

                        if (blank($employment->employee_email)) {
                            Notification::make()
                                ->title('Employee email is missing')
                                ->danger()
                                ->send();

                            return;
                        }

                        $ok = app(EmployeePortalProvisioningService::class)
                            ->sendPortalAccountAccessEmail($employment, 'reset');

                        $employment->refresh();

                        Notification::make()
                            ->title($ok ? 'Portal password reset email sent' : 'Portal password reset email was not sent')
                            ->body($ok ? 'A new temporary portal password has been sent to the employee.' : 'Please check employee email and mail configuration.')
                            ->{$ok ? 'success' : 'danger'}()
                            ->send();
                    }),

                Action::make('enablePortal')
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'portal_enable'))
                    ->label('Enable Portal Access')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Enable employee portal access?')
                    ->action(function (): void {
                        $employment = $this->record;

                        if (blank($employment->employee_email)) {
                            Notification::make()
                                ->title('Employee email is missing')
                                ->danger()
                                ->send();

                            return;
                        }

                        $portalAccount = app(EmployeePortalProvisioningService::class)
                            ->enablePortalAccountForEmployment($employment);

                        $employment->refresh();

                        Notification::make()
                            ->title('Portal access enabled')
                            ->body($portalAccount ? 'Portal account: ' . $portalAccount->email : 'Portal account was not created.')
                            ->success()
                            ->send();
                    }),

                Action::make('disablePortal')
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'portal_disable'))
                    ->label('Disable Portal Access')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Disable employee portal access?')
                    ->modalDescription('The employee will not be able to access the portal until access is enabled again.')
                    ->action(function (): void {
                        $employment = $this->record;

                        $portalAccount = app(EmployeePortalProvisioningService::class)
                            ->disablePortalAccountForEmployment($employment);

                        $employment->refresh();

                        Notification::make()
                            ->title('Portal access disabled')
                            ->body($portalAccount ? 'Portal account: ' . $portalAccount->email : 'No portal account was found.')
                            ->success()
                            ->send();
                    }),

                Action::make('openPortalPreview')
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'portal_preview'))
                    ->label('Open Portal Preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn () => url('/admin/employments/' . $this->record->id . '/portal-preview'))
                    ->openUrlInNewTab(),
            ])
                ->label('Employee Portal')
                ->icon('heroicon-o-computer-desktop')
                ->color('info')
                ->button(),
            Action::make('viewCurrentFinanceProfile')
                ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'finance_profile_view'))
                ->label('View Finance Profile')
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->modalHeading('Current Finance Profile')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(function () {
                    $profile = $this->record->currentFinanceProfile;

                    if (! $profile) {
                        return new HtmlString('<div style="padding:18px;border-radius:16px;background:#fff7ed;color:#9a3412;font-weight:800;">No current finance profile is linked to this employment yet.</div>');
                    }

                    $rows = [
                        'Salary Basis' => $profile->salary_basis ?: '-',
                        'Daily Rate' => number_format((float) ($profile->daily_rate ?? 0), 2) . ' ' . ($profile->payout_currency ?: ''),
                        'Monthly Salary' => number_format((float) ($profile->monthly_salary ?? 0), 2) . ' ' . ($profile->payout_currency ?: ''),
                        'Client Billing Basis' => $profile->client_billing_basis ?: '-',
                        'Client Billing Rate' => number_format((float) ($profile->client_billing_rate ?? 0), 2) . ' ' . ($profile->client_billing_currency ?: ''),
                        'Effective From' => optional($profile->effective_from)->format('d M Y') ?: '-',
                        'Effective To' => optional($profile->effective_to)->format('d M Y') ?: '-',
                    ];

                    $html = '<div style="display:grid;gap:10px;">';

                    foreach ($rows as $label => $value) {
                        $html .= '<div style="display:flex;justify-content:space-between;gap:16px;padding:12px 14px;border:1px solid #e2e8f0;border-radius:14px;background:#f8fafc;">'
                            . '<strong style="color:#334155;">' . e($label) . '</strong>'
                            . '<span style="font-weight:800;color:#0f172a;">' . e((string) $value) . '</span>'
                            . '</div>';
                    }

                    if (filled($profile->finance_notes)) {
                        $html .= '<div style="padding:12px 14px;border:1px solid #e2e8f0;border-radius:14px;background:#f8fafc;">'
                            . '<strong style="display:block;color:#334155;margin-bottom:6px;">Finance Notes</strong>'
                            . '<div style="color:#0f172a;font-weight:650;">' . nl2br(e($profile->finance_notes)) . '</div>'
                            . '</div>';
                    }

                    $html .= '</div>';

                    return new HtmlString($html);
                }),

            Action::make('addRotation')
                ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'rotation_add'))
                ->label('Add Rotation')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->color('info')
                ->modalHeading('Add Rotation')
                ->modalWidth('3xl')
                ->modalSubmitActionLabel('Add Rotation')
                ->form([
                    TextInput::make('rotation_label')
                        ->label('Rotation Label')
                        ->placeholder('Example: Rotation 01 / April Offshore')
                        ->maxLength(255),

                    Select::make('status')
                        ->label('Rotation Status')
                        ->options([
                            'scheduled' => 'Scheduled',
                            'active' => 'Active',
                            'completed' => 'Completed',
                            'paused' => 'Paused',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('scheduled')
                        ->native(false),

                    TextInput::make('rotation_pattern')
                        ->label('Rotation Pattern')
                        ->placeholder('28/28, 35/35 ...')
                        ->maxLength(255),

                    Select::make('travel_status')
                        ->label('Travel Status')
                        ->options([
                            'pending_request' => 'Pending Request',
                            'request_received' => 'Request Received',
                            'ticket_booked' => 'Ticket Booked',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('pending_request')
                        ->native(false),

                    DatePicker::make('from_date')
                        ->label('Work Start Date'),

                    DatePicker::make('to_date')
                        ->label('Work End Date'),

                    DatePicker::make('mobilization_date')
                        ->label('Mobilization / Travel Date'),

                    DatePicker::make('demobilization_date')
                        ->label('Demobilization Date'),

                    FileUpload::make('travel_request_file_path')
                        ->label('Travel Request File')
                        ->disk('public')
                        ->directory(fn () => 'employment-rotations/' . ($this->record?->id ?? 'draft') . '/travel-requests')
                        ->downloadable()
                        ->openable(),

                    FileUpload::make('ticket_file_path')
                        ->label('Ticket File')
                        ->disk('public')
                        ->directory(fn () => 'employment-rotations/' . ($this->record?->id ?? 'draft') . '/tickets')
                        ->downloadable()
                        ->openable(),

                    Toggle::make('is_current')
                        ->label('Mark as Current Rotation')
                        ->default(true),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    if (($data['is_current'] ?? false) && method_exists($this->record, 'rotations')) {
                        $this->record->rotations()->update(['is_current' => false]);
                    }

                    $rotation = $this->record->rotations()->create($data);

                    try {
                        $label = $rotation->rotation_label ?: ('Rotation #' . $rotation->id);

                        app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                            employment: $this->record,
                            category: 'rotation',
                            title: 'Rotation Updated',
                            message: 'A new rotation has been added to your portal: ' . $label,
                            portalPath: '/portal/timeline',
                            related: $rotation,
                            sendEmail: true,
                        );

                        if (filled($rotation->travel_request_file_path)) {
                            app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                                employment: $this->record,
                                category: 'travel',
                                title: 'Travel Request Added',
                                message: 'A travel request file has been added to your portal for: ' . $label,
                                portalPath: '/portal/files',
                                related: $rotation,
                                sendEmail: true,
                            );
                        }

                        if (filled($rotation->ticket_file_path)) {
                            app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                                employment: $this->record,
                                category: 'ticket',
                                title: 'Ticket Added',
                                message: 'A ticket file has been added to your portal for: ' . $label,
                                portalPath: '/portal/files',
                                related: $rotation,
                                sendEmail: true,
                            );
                        }
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    $this->record->refresh();

                    Notification::make()
                        ->title('Rotation added successfully')
                        ->success()
                        ->send();
                }),

            Action::make('uploadEmploymentFile')
                ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'upload_file'))
                ->label('Upload File')
                ->icon('heroicon-o-paper-clip')
                ->color('primary')
                ->modalHeading('Upload Employment File')
                ->modalDescription('Use the portal visibility toggle to decide if this file appears in the Employee Portal or stays admin-only.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Upload File')
                ->form([
                    TextInput::make('title')
                        ->label('File Title')
                        ->required(),

                    Select::make('category')
                        ->label('Category')
                        ->options([
                            'cv' => 'CV',
                            'passport' => 'Passport',
                            'visa' => 'Visa',
                            'medical' => 'Medical',
                            'personal_photo' => 'Personal Photo',
                            'certificate' => 'Certificate',
                            'caf' => 'CAF',
                            'gl' => 'General Letter',
                            'contract' => 'Contract',
                            'rotation_document' => 'Rotation Document',
                            'travel_request' => 'Travel Request',
                            'ticket' => 'Ticket',
                            'internal_document' => 'Internal Document',
                            'other' => 'Other',
                        ])
                        ->default('internal_document')
                        ->native(false)
                        ->searchable()
                        ->live()
                        ->required(),

                    Select::make('file_handling')
                        ->label('File Handling')
                        ->options([
                            'new_document' => 'New Document',
                            'new_version' => 'New Version of Existing Document',
                        ])
                        ->default('new_document')
                        ->native(false)
                        ->live()
                        ->helperText('Use New Document for separate files. Use New Version only when this upload replaces a selected old file.'),

                    Select::make('version_parent_file_id')
                        ->label('Replace / Version Existing File')
                        ->options(function () {
                            return $this->record->files()
                                ->where('is_active', true)
                                ->latest('updated_at')
                                ->latest('id')
                                ->get()
                                ->mapWithKeys(function ($file) {
                                    $label = trim(
                                        ($file->title ?: ('File #' . $file->id))
                                        . ' — '
                                        . strtoupper(str_replace('_', ' ', (string) $file->category))
                                        . ' — V'
                                        . ($file->version_no ?: 1)
                                        . ((bool) ($file->is_current ?? true) ? ' — Current' : ' — Old')
                                    );

                                    return [$file->id => $label];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->visible(fn ($get) => $get('file_handling') === 'new_version')
                        ->required(fn ($get) => $get('file_handling') === 'new_version')
                        ->helperText('Only the selected file will become old version. Other files in the same category will stay untouched.'),

                    DatePicker::make('document_date')
                        ->label('Document Date'),

                    DatePicker::make('expiry_date')
                        ->label('Expiry Date'),

                    FileUpload::make('file_path')
                        ->label('File')
                        ->disk('public')
                        ->directory(fn () => 'employment-files/' . ($this->record?->id ?? 'draft'))
                        ->downloadable()
                        ->openable()
                        ->required(),

                    Toggle::make('show_in_employee_portal')
                        ->label('Show in Employee Portal')
                        ->helperText('OFF = admin-only file. ON = employee can see/download it in the portal and receives notification/email.')
                        ->default(false)
                        ->inline(false)
                        ->dehydrated(true),

                    Select::make('uploaded_by_type')
                        ->label('Submitted By')
                        ->options([
                            'candidate' => 'Candidate',
                            'admin' => 'Admin',
                        ])
                        ->default('admin')
                        ->native(false)
                        ->required(),

                    Toggle::make('is_current')
                        ->label('Mark as Current')
                        ->helperText('For New Document this only marks this file as active/current. It will not close other files.')
                        ->default(true),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $fileHandling = $data['file_handling'] ?? 'new_document';
                    $versionParentFileId = $data['version_parent_file_id'] ?? null;

                    /*
                     * IMPORTANT:
                     * This is a form-only field. It is intentionally NOT the same
                     * name as the DB column, to prevent stale/default values.
                     */
                    $showInEmployeePortal = filter_var(
                        $data['show_in_employee_portal'] ?? false,
                        FILTER_VALIDATE_BOOLEAN
                    );

                    unset($data['file_handling'], $data['version_parent_file_id'], $data['show_in_employee_portal']);

                    $categoryText = strtolower(trim(($data['category'] ?? '') . ' ' . ($data['title'] ?? '') . ' ' . ($data['file_path'] ?? '')));

                    if (str_contains($categoryText, 'cv') || str_contains($categoryText, 'resume')) {
                        $data['category'] = 'cv';
                    }

                    if (($data['category'] ?? null) === 'cv') {
                        $data['title'] = trim((string) ($this->record->employee_name ?: 'Candidate')) . ' CV';
                    }

                    $parentFile = null;

                    if ($fileHandling === 'new_version' && filled($versionParentFileId)) {
                        $parentFile = $this->record->files()
                            ->whereKey($versionParentFileId)
                            ->first();

                        if (! $parentFile) {
                            Notification::make()
                                ->title('Selected old file was not found')
                                ->body('Please select the file you want to version again.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $data['category'] = $data['category'] ?: $parentFile->category;
                        $data['title'] = $data['title'] ?: $parentFile->title;
                        $data['version_no'] = ((int) ($parentFile->version_no ?: 1)) + 1;
                        $data['is_current'] = true;
                    } else {
                        $data['version_no'] = 1;
                        $data['is_current'] = (bool) ($data['is_current'] ?? true);
                    }

                    $data['is_active'] = true;
                    $data['uploaded_by_user_id'] = auth()->id();

                    if (DbSchema::hasColumn('employment_files', 'is_visible_to_employee_portal')) {
                        $data['is_visible_to_employee_portal'] = $showInEmployeePortal;
                    }

                    $uploadedFile = $this->record->files()->create($data);

                    /*
                     * Final hard-save for portal visibility.
                     * This protects us even if model fillable/cast/cache changes.
                     */
                    if (DbSchema::hasColumn('employment_files', 'is_visible_to_employee_portal')) {
                        $uploadedFile->forceFill([
                            'is_visible_to_employee_portal' => (bool) $showInEmployeePortal,
                        ])->saveQuietly();

                        $uploadedFile->refresh();
                    }

                    if ($parentFile) {
                        $parentFile->forceFill([
                            'is_current' => false,
                        ])->save();
                    }

                    if ($showInEmployeePortal) {
                        try {
                            app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                                employment: $this->record,
                                category: 'file',
                                title: 'New File Added',
                                message: 'A new employee-visible file has been added to your portal: ' . ($uploadedFile->title ?? $uploadedFile->file_name ?? 'File'),
                                portalPath: '/portal/files',
                                related: $uploadedFile,
                                sendEmail: true,
                            );
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    }

                    $this->record->refresh();

                    Notification::make()
                        ->title('File uploaded successfully')
                        ->body($showInEmployeePortal
                            ? 'The file is visible in the Employee Portal and the employee has been notified.'
                            : 'The file is saved on the Employment Profile only and hidden from the Employee Portal.')
                        ->success()
                        ->send();
                }),

            Action::make('requestCandidateFile')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('employments', 'request_file'))
                ->label('Request File')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('warning')
                ->modalHeading('Request File From Employee')
                ->modalDescription('Create a file request for the employee portal. You can request upload only, or send a document that must be downloaded, signed, and re-uploaded.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Create Request')
                ->form([
                    TextInput::make('label')
                        ->label('Requested File Label')
                        ->placeholder('Example: Updated Visa / New Medical / Signed Contract')
                        ->required()
                        ->maxLength(255),

                    Select::make('document_category')
                        ->label('Document Category')
                        ->options([
                            'cv' => 'CV',
                            'passport' => 'Passport',
                            'visa' => 'Visa',
                            'medical' => 'Medical',
                            'personal_photo' => 'Personal Photo',
                            'certificate' => 'Certificate',
                            'caf' => 'CAF',
                            'gl' => 'General Letter',
                            'contract' => 'Contract',
                            'rotation_document' => 'Rotation Document',
                            'travel_request' => 'Travel Request',
                            'ticket' => 'Ticket',
                            'candidate_upload' => 'Employee Upload',
                            'other' => 'Other',
                        ])
                        ->default('candidate_upload')
                        ->native(false)
                        ->searchable()
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

                    FileUpload::make('document_to_sign_path')
                        ->label('Document to Sign / Return')
                        ->disk('public')
                        ->directory(fn () => 'employee-portal-signing-requests/' . ($this->record?->id ?? 'new'))
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->visible(fn ($get) => $get('request_type') === 'download_sign_upload')
                        ->required(fn ($get) => $get('request_type') === 'download_sign_upload'),

                    Toggle::make('is_required')
                        ->label('Required')
                        ->default(true),

                    Textarea::make('help_text')
                        ->label('Instructions For Employee')
                        ->rows(3)
                        ->placeholder('Please upload the latest valid document. If signing is required, download the attached document, sign it, then upload it back.'),
                ])
                ->action(function (array $data): void {
                    $preEmployment = $this->record->preEmployment;

                    if (! $preEmployment) {
                        Notification::make()
                            ->title('No linked Pre-Employment record')
                            ->body('This employee is not linked to a pre-employment record, so the portal file request cannot be created.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $requestType = $data['request_type'] ?? 'upload_only';
                    $documentToSignPath = $data['document_to_sign_path'] ?? null;

                    $model = new PreEmploymentPortalField();
                    $table = $model->getTable();
                    $columns = DbSchema::hasTable($table) ? DbSchema::getColumnListing($table) : [];

                    $payload = [];

                    foreach ([
                        'pre_employment_id' => $preEmployment->id,
                        'label' => $data['label'] ?? 'Requested File',
                        'field_key' => 'employee_requested_file_' . now()->format('YmdHis'),
                        'field_type' => 'file',
                        'document_category' => $data['document_category'] ?? 'candidate_upload',
                        'help_text' => $data['help_text'] ?? null,
                        'instructions' => $data['help_text'] ?? null,
                        'request_type' => $requestType,
                        'document_to_sign_path' => $documentToSignPath,
                        'document_to_sign_original_name' => $documentToSignPath ? basename((string) $documentToSignPath) : null,
                        'signed_file_required' => $requestType === 'download_sign_upload',
                        'signature_status' => $requestType === 'download_sign_upload' ? 'pending_signature' : null,
                        'is_required' => (bool) ($data['is_required'] ?? true),
                        'visible_to_candidate' => true,
                        'is_active' => true,
                        'sort_order' => 9999,
                    ] as $column => $value) {
                        if (in_array($column, $columns, true)) {
                            $payload[$column] = $value;
                        }
                    }

                    $field = PreEmploymentPortalField::query()->create($payload);

                    try {
                        app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                            employment: $this->record,
                            category: 'file_request',
                            title: 'New File Request',
                            message: 'A new file request has been added to your portal: ' . ($field->label ?: 'Requested File'),
                            portalPath: '/portal/files',
                            related: $field,
                            sendEmail: true,
                        );
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    Notification::make()
                        ->title('Employee file request created')
                        ->body('The request now appears in the employee portal dashboard and files page.')
                        ->success()
                        ->send();
                }),

            Action::make('addExpense')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('employments', 'add_expense') || auth()->user()?->canErp('finance_expenses', 'create')))
                ->label('Add Expense')
                ->icon('heroicon-o-banknotes')
                ->color('danger')
                ->modalHeading('Add Employee Expense')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Add Expense')
                ->form([
                    TextInput::make('title')
                        ->label('Expense Title')
                        ->required(),

                    Select::make('employment_rotation_id')
                        ->label('Linked Rotation')
                        ->options(fn () => $this->record->rotations()
                            ->orderByDesc('from_date')
                            ->orderByDesc('id')
                            ->get()
                            ->mapWithKeys(fn ($rotation) => [
                                $rotation->id => trim(($rotation->rotation_label ?: ('Rotation #' . $rotation->id)) . ' — ' . optional($rotation->from_date)->format('d M Y') . ' to ' . optional($rotation->to_date)->format('d M Y')),
                            ])
                            ->toArray())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->helperText('Link this expense to a specific rotation for accurate rotation cost.'),

                    Select::make('expense_type')
                        ->label('Expense Type')
                        ->options([
                            'ticket' => 'Ticket',
                            'visa' => 'Visa',
                            'medical' => 'Medical',
                            'training' => 'Training',
                            'transport' => 'Transport',
                            'accommodation' => 'Accommodation',
                            'other' => 'Other',
                        ])
                        ->default('other')
                        ->native(false),

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
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->default(fn () => $this->record->salary_currency ?: 'EUR')
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
                        ->default(now()),

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
                        ->visible(fn ($get) => $get('paid_by') === 'candidate'),

                    TextInput::make('reimbursement_amount')
                        ->label('Reimbursement Amount')
                        ->numeric()
                        ->visible(fn ($get) => $get('paid_by') === 'candidate'),

                    Select::make('reimbursement_currency')
                        ->label('Reimbursement Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'GBP' => 'GBP',
                            'LYD' => 'LYD',
                        ])
                        ->default(fn () => $this->record->salary_currency ?: 'EUR')
                        ->native(false)
                        ->visible(fn ($get) => $get('paid_by') === 'candidate'),

                    Textarea::make('reimbursement_notes')
                        ->label('Reimbursement Notes')
                        ->rows(3)
                        ->visible(fn ($get) => $get('paid_by') === 'candidate')
                        ->columnSpanFull(),

                    Select::make('status')
                        ->label('Expense Status')
                        ->options([
                            'draft' => 'Draft',
                            'approved' => 'Approved',
                            'paid' => 'Paid',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('draft')
                        ->native(false)
                        ->required(),

                    FileUpload::make('attachment_path')
                        ->label('Attachment / Receipt')
                        ->disk('public')
                        ->directory(fn () => 'finance-expenses/employment-' . ($this->record?->id ?? 'draft'))
                        ->downloadable()
                        ->openable(),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    if (! class_exists(\App\Models\FinanceExpense::class)) {
                        Notification::make()
                            ->title('FinanceExpense model not found')
                            ->danger()
                            ->send();

                        return;
                    }

                    $expense = new \App\Models\FinanceExpense();
                    $table = $expense->getTable();
                    $columns = DbSchema::hasTable($table) ? DbSchema::getColumnListing($table) : [];

                    $payload = [];

                    $preEmployment = $this->record->preEmployment;
                    $job = $this->record->job;
                    $project = $job?->project;
                    $client = $project?->client;

                    $paidBy = $data['paid_by'] ?? \App\Models\FinanceExpense::PAID_BY_COMPANY;
                    $isCandidatePaid = $paidBy === \App\Models\FinanceExpense::PAID_BY_CANDIDATE;
                    $amount = $data['amount'] ?? 0;
                    $currency = $data['currency'] ?? ($this->record->salary_currency ?: 'EUR');
                    $hasRotation = filled($data['employment_rotation_id'] ?? null);

                    foreach ([
                        'title' => $data['title'] ?? 'Employee Expense',
                        'description' => $data['title'] ?? 'Employee Expense',
                        'expense_type' => $data['expense_type'] ?? 'other',
                        'category' => $data['expense_type'] ?? 'other',
                        'expense_category' => $data['expense_type'] ?? 'other',
                        'amount' => $amount,
                        'total_amount' => $amount,
                        'currency' => $currency,
                        'expense_date' => $data['expense_date'] ?? now(),
                        'attachment_path' => $data['attachment_path'] ?? null,
                        'file_path' => $data['attachment_path'] ?? null,
                        'has_attachment' => filled($data['attachment_path'] ?? null),
                        'notes' => $data['notes'] ?? null,
                        'expense_scope' => $hasRotation ? \App\Models\FinanceExpense::SCOPE_ROTATION : \App\Models\FinanceExpense::SCOPE_EMPLOYMENT,
                        'employment_id' => $this->record->id,
                        'employment_rotation_id' => $data['employment_rotation_id'] ?? null,
                        'rotation_id' => $data['employment_rotation_id'] ?? null,
                        'pre_employment_id' => $this->record->pre_employment_id,
                        'job_application_id' => $preEmployment?->job_application_id,
                        'job_id' => $this->record->job_id,
                        'project_id' => $project?->id,
                        'client_id' => $client?->id,
                        'candidate_finance_profile_id' => $this->record->currentFinanceProfile?->id,
                        'created_by' => auth()->id(),
                        'paid_by' => $paidBy,
                        'reimbursement_required' => $isCandidatePaid,
                        'reimbursement_status' => $isCandidatePaid
                            ? ($data['reimbursement_status'] ?? \App\Models\FinanceExpense::REIMBURSEMENT_PENDING)
                            : \App\Models\FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE,
                        'reimbursement_amount' => $isCandidatePaid ? ($data['reimbursement_amount'] ?? $amount) : null,
                        'reimbursement_currency' => $isCandidatePaid ? ($data['reimbursement_currency'] ?? $currency) : $currency,
                        'reimbursement_notes' => $isCandidatePaid ? ($data['reimbursement_notes'] ?? null) : null,
                        'is_company_expense' => $paidBy === \App\Models\FinanceExpense::PAID_BY_COMPANY,
                        'is_manual_expense' => true,
                        'candidate_submitted' => false,
                        'status' => $data['status'] ?? \App\Models\FinanceExpense::STATUS_DRAFT,
                    ] as $column => $value) {
                        if (in_array($column, $columns, true)) {
                            $payload[$column] = $value;
                        }
                    }

                    \App\Models\FinanceExpense::query()->create($payload);

                    Notification::make()
                        ->title('Expense added successfully')
                        ->success()
                        ->send();
                }),

            Action::make('editCurrentFinanceProfile')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('employments', 'finance_profile'))
                ->label(fn () => $this->record?->currentFinanceProfile ? 'Edit Current Finance Profile' : 'Create Current Finance Profile')
                ->color(fn () => $this->financeProfileLocked() ? 'gray' : ($this->isFinanceReady() ? 'success' : 'warning'))
                ->icon('heroicon-o-banknotes')
                ->disabled(fn () => $this->financeProfileLocked())
                ->tooltip(fn () => $this->financeProfileLocked() ? $this->financeProfileLockReason() : null)
                ->modalHeading(fn () => $this->record?->currentFinanceProfile ? 'Edit Current Finance Profile' : 'Create Current Finance Profile')
                ->modalDescription('This profile controls employee cost, client billing rate, and the financial values used in salary slips and invoices.')
                ->modalSubmitActionLabel('Save Finance Profile')
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
                    if ($this->financeProfileLocked()) {
                        Notification::make()
                            ->title('Finance Profile is locked')
                            ->body($this->financeProfileLockReason())
                            ->danger()
                            ->send();

                        return;
                    }

                    CandidateFinanceProfile::query()
                        ->where('employment_id', $this->record->id)
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
                            'job_application_id' => $this->record->preEmployment?->job_application_id,
                            'pre_employment_id' => $this->record->pre_employment_id,
                            'employment_id' => $this->record->id,
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
                            'source_type' => 'employment',
                            'effective_from' => $data['effective_from'] ?? now()->toDateString(),
                            'effective_to' => $data['effective_to'] ?? null,
                            'is_current' => true,
                            'is_hidden_from_non_finance' => true,
                            'finance_notes' => $data['finance_notes'] ?? null,
                        ]);
                    }

                    $this->record->refresh();

                    Notification::make()
                        ->title('Current Finance Profile saved successfully')
                        ->success()
                        ->send();
                }),

            Action::make('generateSalarySlip')
                ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'generate_salary_slip'))
                ->label('Generate Salary Slip')
                ->color('warning')
                ->icon('heroicon-o-calendar-days')
                ->disabled(fn () => ! $this->isFinanceReady())
                ->tooltip(fn () => ! $this->isFinanceReady() ? $this->financeBlockReason() : null)
                ->form([
                    Select::make('salary_year')
                        ->label('Year')
                        ->options(function () {
                            $currentYear = (int) now()->year;
                            $years = [];

                            for ($year = $currentYear - 3; $year <= $currentYear + 2; $year++) {
                                $years[$year] = (string) $year;
                            }

                            return $years;
                        })
                        ->default((int) now()->year)
                        ->native(false)
                        ->required(),

                    Select::make('salary_month')
                        ->label('Month')
                        ->options([
                            1 => '01 - January',
                            2 => '02 - February',
                            3 => '03 - March',
                            4 => '04 - April',
                            5 => '05 - May',
                            6 => '06 - June',
                            7 => '07 - July',
                            8 => '08 - August',
                            9 => '09 - September',
                            10 => '10 - October',
                            11 => '11 - November',
                            12 => '12 - December',
                        ])
                        ->default((int) now()->month)
                        ->native(false)
                        ->required(),

                    Select::make('replace_existing')
                        ->label('If already exists')
                        ->options([
                            1 => 'Replace existing slip',
                            0 => 'Keep existing slip',
                        ])
                        ->default(1)
                        ->native(false)
                        ->required(),
                ])
                ->action(function (array $data) {
                    try {
                        if (! $this->isFinanceReady()) {
                            Notification::make()
                                ->title('Cannot generate salary slip')
                                ->body($this->financeBlockReason())
                                ->danger()
                                ->send();

                            return;
                        }

                        $salarySlip = app(SalarySlipGenerationService::class)
                            ->generateForEmploymentMonth(
                                $this->record,
                                (int) $data['salary_year'],
                                (int) $data['salary_month'],
                                (bool) $data['replace_existing'],
                                auth()->id()
                            );

                        if (! $salarySlip) {
                            Notification::make()
                                ->title('No worked days found for this month')
                                ->warning()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Salary slip generated successfully')
                            ->body('Draft salary slip created for this employee.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Could not generate salary slip')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('printProfile')
                ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'print_profile'))
                ->label('Print Profile')
                ->color('gray')
                ->url(fn () => route('employment.print.profile', ['employment' => $this->record]))
                ->openUrlInNewTab(),

            Action::make('printRotationHistory')
                ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'rotation_print'))
                ->label('Print Rotation History')
                ->color('gray')
                ->url(fn () => route('employment.print.rotation-history', ['employment' => $this->record]))
                ->openUrlInNewTab(),

            Action::make('editProfile')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('employments', 'edit'))
                ->label('Edit')
                ->color('primary')
                ->url(fn () => EmploymentResource::getUrl('edit', ['record' => $this->record])),

            DeleteAction::make()
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('employments', 'delete'))
                ->label('Delete')
                ->requiresConfirmation()
                ->modalHeading('Permanent delete')
                ->modalDescription('This record will be permanently deleted and cannot be recovered. Are you sure?')
                ->modalSubmitActionLabel('Yes, Delete Permanently'),
        ];
    }

    protected function isFinanceReady(): bool
    {
        $profile = $this->record?->currentFinanceProfile;

        if (! $profile) {
            return false;
        }

        if (! filled($profile->salary_basis)) {
            return false;
        }

        if (! filled($profile->payout_currency)) {
            return false;
        }

        if ($profile->salary_basis === CandidateFinanceProfile::BASIS_DAILY_RATE) {
            return filled($profile->daily_rate) && (float) $profile->daily_rate > 0;
        }

        if ($profile->salary_basis === CandidateFinanceProfile::BASIS_MONTHLY) {
            return filled($profile->monthly_salary) && (float) $profile->monthly_salary > 0;
        }

        return false;
    }

    protected function financeBlockReason(): string
    {
        $profile = $this->record?->currentFinanceProfile;

        if (! $profile) {
            return 'Current Finance Profile is missing.';
        }

        $missing = [];

        if (! filled($profile->salary_basis)) {
            $missing[] = 'Salary Basis';
        }

        if (! filled($profile->payout_currency)) {
            $missing[] = 'Payout Currency';
        }

        if ($profile->salary_basis === CandidateFinanceProfile::BASIS_DAILY_RATE) {
            if (! filled($profile->daily_rate) || (float) $profile->daily_rate <= 0) {
                $missing[] = 'Daily Rate';
            }
        } elseif ($profile->salary_basis === CandidateFinanceProfile::BASIS_MONTHLY) {
            if (! filled($profile->monthly_salary) || (float) $profile->monthly_salary <= 0) {
                $missing[] = 'Monthly Salary';
            }
        } else {
            $missing[] = 'Valid Salary Basis';
        }

        return ! empty($missing)
            ? 'Missing required finance fields: ' . implode(', ', $missing) . '.'
            : 'Current Finance Profile is incomplete.';
    }

    protected function financeProfileLocked(): bool
    {
        $hasApprovedSalarySlip = SalarySlip::query()
            ->where('employment_id', $this->record->id)
            ->whereIn('status', [
                SalarySlip::STATUS_APPROVED,
                SalarySlip::STATUS_LOCKED,
                SalarySlip::STATUS_PAID,
            ])
            ->exists();

        if ($hasApprovedSalarySlip) {
            return true;
        }

        return $this->record->clientInvoiceLines()->exists();
    }

    protected function financeProfileLockReason(): string
    {
        $hasApprovedSalarySlip = SalarySlip::query()
            ->where('employment_id', $this->record->id)
            ->whereIn('status', [
                SalarySlip::STATUS_APPROVED,
                SalarySlip::STATUS_LOCKED,
                SalarySlip::STATUS_PAID,
            ])
            ->exists();

        if ($hasApprovedSalarySlip) {
            return 'Finance Profile is locked because this employee already has approved, locked, or paid salary slips.';
        }

        if ($this->record->clientInvoiceLines()->exists()) {
            return 'Finance Profile is locked because this employee is already linked to client invoice lines.';
        }

        return 'Finance Profile is locked.';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'view') ?? false);
    }

}
