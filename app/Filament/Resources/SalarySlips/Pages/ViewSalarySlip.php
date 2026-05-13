<?php

namespace App\Filament\Resources\SalarySlips\Pages;

use App\Services\PortalNotificationService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use App\Models\TreasuryAccount;
use Illuminate\Support\Facades\Schema as DbSchema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use App\Models\SalarySlipAttachment;
use App\Filament\Resources\SalarySlips\SalarySlipResource;
use App\Mail\PortalSalarySlipActionRequiredMail;
use Illuminate\Support\Facades\Mail;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class ViewSalarySlip extends ViewRecord
{
    protected static string $resource = SalarySlipResource::class;

    protected string $view = 'filament.resources.salary-slips.pages.view-salary-slip-premium';

    public array $attendanceRows = [];


    public function canEditAttendanceSchedule(): bool
    {
        try {
            $this->record->refresh();

            return (string) $this->record->status === \App\Models\SalarySlip::STATUS_DRAFT
                && (bool) auth()->user()?->canErp('salary_slips', 'edit');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getTitle(): string
    {
        return 'Salary Slip';
    }

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->loadAttendanceRows();
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('salary_slips', 'print'))
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'print'))
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),

            Actions\EditAction::make()
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                ->label('Edit')
                ->icon('heroicon-o-pencil-square'),

            Actions\Action::make('update_attendance_days')
                ->label('Attendance Schedule / Report')
                ->hidden(fn () => (string) $this->record->fresh()->status !== \App\Models\SalarySlip::STATUS_DRAFT || ! (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                ->visible(fn () => (string) $this->record->fresh()->status === \App\Models\SalarySlip::STATUS_DRAFT && (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->modalHeading('Update Attendance Days')
                ->modalDescription('Manage normal attendance days and add extra travel days with full-day or half-day payment rates.')
                ->modalWidth('5xl')
                ->modalSubmitActionLabel('Save Attendance')
                ->extraModalWindowAttributes([
                    'class' => 'sf-md3-attendance-modal',
                ])
                ->fillForm(fn (): array => [
                    'attendance_rows' => $this->buildAttendanceRowsForPopup(),
                    'additional_travel_days' => $this->buildAdditionalTravelDaysForPopup(),
                ])
                ->form([
                    Repeater::make('attendance_rows')
                        ->label('Normal Attendance Days')
                        ->schema([
                            Hidden::make('id'),

                            TextInput::make('date')
                                ->label('Date')
                                ->disabled()
                                ->dehydrated(true)
                                ->extraInputAttributes(['class' => 'sf-md3-input']),

                            TextInput::make('day_name')
                                ->label('Day')
                                ->disabled()
                                ->dehydrated(true)
                                ->extraInputAttributes(['class' => 'sf-md3-input']),

                            Toggle::make('is_paid')
                                ->label('Paid')
                                ->inline(false)
                                ->extraAttributes(['class' => 'sf-md3-toggle']),

                            Select::make('attendance_status')
                                ->label('Status')
                                ->options([
                                    'present' => 'Present',
                                    'absent' => 'Absent',
                                    'sick' => 'Sick',
                                    'leave' => 'Leave',
                                    'unpaid_leave' => 'Unpaid Leave',
                                    'holiday' => 'Holiday',
                                    'travel' => 'Travel',
                                    'other' => 'Other',
                                ])
                                ->native(false)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set): void {
                                    if (in_array($state, ['absent', 'unpaid_leave'], true)) {
                                        $set('is_paid', false);
                                    }

                                    if (in_array($state, ['present', 'holiday', 'travel'], true)) {
                                        $set('is_paid', true);
                                    }
                                }),

                            TextInput::make('notes')
                                ->label('Notes')
                                ->placeholder('Optional notes...')
                                ->extraInputAttributes(['class' => 'sf-md3-input']),
                        ])
                        ->columns([
                            'default' => 1,
                            'md' => 5,
                        ])
                        ->reorderable(false)
                        ->addable(false)
                        ->deletable(false)
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'sf-md3-attendance-repeater sf-md3-attendance-repeater-scroll']),

                    Repeater::make('additional_travel_days')
                        ->label('Additional Travel Days')
                        ->schema([
                            Hidden::make('id'),

                            DatePicker::make('date')
                                ->label('Travel Date')
                                ->native(false)
                                ->required(),

                            Select::make('day_type')
                                ->label('Type')
                                ->options([
                                    'travel_day' => 'Travel Day',
                                ])
                                ->default('travel_day')
                                ->native(false)
                                ->required(),

                            TextInput::make('pay_multiplier')
                                ->label('Pay Rate')
                                ->numeric()
                                ->minValue(0)
                                ->step('0.5')
                                ->default(1)
                                ->helperText('Use 1 for full day, 0.5 for half day.'),

                            TextInput::make('notes')
                                ->label('Notes')
                                ->placeholder('Optional notes...'),
                        ])
                        ->columns([
                            'default' => 1,
                            'md' => 4,
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Add Travel Day')
                        ->reorderable(false)
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'sf-md3-travel-days-repeater']),
                ])
                ->action(function (array $data): void {
                    $this->saveAttendanceRowsFromPopup(
                        $data['attendance_rows'] ?? [],
                        $data['additional_travel_days'] ?? [],
                    );

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Attendance saved')
                        ->body('Salary slip has been recalculated successfully.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('upload_attachment')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('salary_slips', 'upload_attachment'))
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'upload_attachment'))
                ->label('Upload Attachment')
                ->icon('heroicon-o-paper-clip')
                ->color('info')
                ->modalHeading('Upload Salary Slip Attachment')
                ->modalWidth('lg')
                ->modalSubmitActionLabel('Upload Attachment')
                ->form([
                    Select::make('type')
                        ->label('Attachment Type')
                        ->options([
                            'timesheet' => 'Timesheet',
                            'attendance_sheet' => 'Attendance Sheet',
                            'day_schedule' => 'Day Schedule',
                            'supporting_file' => 'Supporting File',
                        ])
                        ->default('timesheet')
                        ->required(),

                    FileUpload::make('file_path')
                        ->label('File')
                        ->disk('public')
                        ->directory('salary-slip-attachments')
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->required(),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->saveSlipAttachment($data);

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Attachment uploaded')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('salary_adjustment')
                ->label('Addition / Deduction')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->modalHeading('Salary Addition / Deduction')
                ->modalDescription('Add salary additions and deductions with separated notes.')
                ->modalSubmitActionLabel('Save Addition / Deduction')
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                ->fillForm(fn () => [
                    'addition_amount' => (float) ($this->record->adjustments_amount ?? 0),
                    'addition_note' => (string) ($this->record->addition_note ?? ''),
                    'deduction_amount' => (float) ($this->record->deductions_amount ?? 0),
                    'deduction_note' => (string) ($this->record->deduction_note ?? ''),
                ])
                ->form([
                    TextInput::make('addition_amount')
                        ->label('Addition Amount')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->prefix(fn () => $this->record->currency ?: null)
                        ->helperText('Bonus, allowance, correction, or approved adjustment.'),

                    Textarea::make('addition_note')
                        ->label('Addition Note / Reason')
                        ->rows(3)
                        ->placeholder('Optional. Example: Bonus, approved allowance, half-day correction...')
                        ->columnSpanFull(),

                    TextInput::make('deduction_amount')
                        ->label('Deduction Amount')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->prefix(fn () => $this->record->currency ?: null)
                        ->helperText('Penalty, unpaid correction, absence deduction, or finance correction.'),

                    Textarea::make('deduction_note')
                        ->label('Deduction Note / Reason')
                        ->rows(3)
                        ->placeholder('Optional. Example: Delay, absence deduction, advance deduction...')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $addition = round((float) ($data['addition_amount'] ?? 0), 2);
                    $deduction = round((float) ($data['deduction_amount'] ?? 0), 2);
                    $base = round((float) ($this->record->base_amount ?? 0), 2);
                    $net = round($base + $addition - $deduction, 2);

                    $convertedReimbursement = round((float) ($this->record->reimbursement_converted_total ?? 0), 2);
                    $finalPayable = round($net + $convertedReimbursement, 2);

                    $this->record->forceFill([
                        'adjustments_amount' => $addition,
                        'deductions_amount' => $deduction,
                        'addition_note' => trim((string) ($data['addition_note'] ?? '')) ?: null,
                        'deduction_note' => trim((string) ($data['deduction_note'] ?? '')) ?: null,
                        'net_amount' => $net,
                        'payment_total_amount' => $finalPayable,
                    ])->save();

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Addition / deduction saved')
                        ->body('Salary slip amounts and separated notes were updated.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\Action::make('approve')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('salary_slips', 'approve'))
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'approve'))
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['draft', 'pending', null], true))
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->forceFill([
                        'status' => 'approved',
                        'approved_at' => now(),
                    ])->save();

                    $this->resetEmployeePaymentConfirmation();

                    $this->sendSalarySlipPortalEmail('approved');

                    $this->notifyPortalAboutSalarySlip(
                        'approved',
                        'Salary Slip Approved',
                        'Your salary slip has been approved and is now available in the employee portal.'
                    );

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Salary slip approved')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('process_payment')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('salary_slips', 'process_payment') || auth()->user()?->canErp('salary_slips', 'send_to_bank') || auth()->user()?->canErp('salary_slips', 'mark_paid')))
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'process_payment'))
                ->label('Process Payment')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['approved', 'bank_rejected'], true))
                ->modalHeading('Process Salary Slip Payment')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Process Payment')
                ->form([
                    Select::make('payment_route')
                        ->label('Payment Route')
                        ->options([
                            'bank' => 'Bank Transfer',
                            'cash' => 'Cash Payment',
                        ])
                        ->default($this->record->payment_route ?? $this->record->payment_method ?? 'bank')
                        ->live()
                        ->required(),

                    Select::make('currency')
                        ->label('Currency')
                        ->options([
                            'EUR' => 'EUR',
                            'USD' => 'USD',
                            'LYD' => 'LYD',
                            'GBP' => 'GBP',
                        ])
                        ->default($this->record->currency ?? 'EUR')
                        ->live()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set): void {
                            $set('reimbursement_exchange_rates', $this->buildReimbursementExchangeRateRows($state ?: ($this->record->currency ?? 'EUR')));
                        }),

                    Select::make('treasury_account_id')
                        ->label('Treasury Account')
                        ->options(function (callable $get): array {
                            return $this->getTreasuryAccountOptions(
                                $get('currency') ?: ($this->record->currency ?? 'EUR'),
                                $get('payment_route') ?: 'bank'
                            );
                        })
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('payment_reference')
                        ->label('Payment Reference')
                        ->placeholder('Bank reference / cash voucher / transfer ref')
                        ->default($this->record->payment_reference ?? $this->record->bank_reference ?? null),

                    DatePicker::make('payment_date')
                        ->label('Payment Date')
                        ->default(now())
                        ->required(),

                    Repeater::make('reimbursement_exchange_rates')
                        ->label('Reimbursement Exchange Rates')
                        ->live()
                        ->helperText('For reimbursements in currencies different from the payment currency, enter the exchange rate to convert them into the selected payment currency.')
                        ->schema([
                            Hidden::make('currency'),

                            TextInput::make('currency_label')
                                ->label('Currency')
                                ->disabled()
                                ->dehydrated(true),

                            TextInput::make('original_amount')
                                ->label('Amount')
                                ->disabled()
                                ->dehydrated(true),

                            TextInput::make('exchange_rate')
                                ->label('Rate')
                                ->numeric()
                                ->required()
                                ->numeric()
                                ->live(onBlur: false)
                                ->afterStateUpdated(function ($state, $set, $get): void {
                                    $set('converted_preview', $this->convertedPreviewAmount($get('original_amount'), $state));
                                })
                                ->required(),

                            TextInput::make('converted_preview')
                                ->label('Preview')
                                ->disabled()
                                ->dehydrated(),
                        ])
                        ->default(fn (callable $get): array => $this->buildReimbursementExchangeRateRows($get('currency') ?: ($this->record->currency ?? 'EUR')))
                        ->columns(4)
                        ->columns(5)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $this->processSalarySlipPayment($data);

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Salary slip sent to payment processing')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('bank_rejected')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('salary_slips', 'process_payment'))
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'process_payment'))
                ->label('Bank Rejected')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === 'sent_to_bank' && ($this->record->payment_method ?? null) === 'bank')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->forceFill([
                        'status' => 'bank_rejected',
                    ])->save();

                    $this->resetEmployeePaymentConfirmation();

                    $this->notifyPortalAboutSalarySlip(
                        'bank_rejected',
                        'Salary Payment Rejected',
                        'Your salary payment was marked as bank rejected. The company will review and process the correction.'
                    );

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Salary slip marked as bank rejected')
                        ->danger()
                        ->send();
                }),

            Actions\Action::make('back_to_draft')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('salary_slips', 'back_to_draft'))
                ->visible(fn () => (bool) auth()->user()?->canErp('salary_slips', 'edit'))
                ->label('Back To Draft')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->visible(fn () => ! in_array($this->record->status, ['draft', null], true))
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->forceFill([
                        'status' => 'draft',
                            'payment_total_amount' => null,
                            'reimbursement_same_currency_total' => null,
                            'reimbursement_converted_total' => null,
                            'reimbursement_exchange_rates' => null,
                            'reimbursement_breakdown' => null,
                    ])->save();

                    $this->resetEmployeePaymentConfirmation();

                    $this->refreshRecord();

                    Notification::make()
                        ->title('Salary slip moved back to draft')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function refreshRecord(): void
    {
        $relations = [];

        foreach (['employment', 'employee', 'days', 'attachments', 'treasuryAccount', 'treasury_account'] as $relation) {
            if (method_exists($this->record, $relation)) {
                $relations[] = $relation;
            }
        }

        $this->record = $this->record->fresh($relations);
    }


    protected function getTreasuryAccountOptions(?string $currency = null, ?string $paymentRoute = null): array
    {
        $currency = strtoupper((string) ($currency ?: $this->record->currency ?: 'EUR'));
        $paymentRoute = strtolower((string) ($paymentRoute ?: $this->record->payment_method ?: 'bank'));

        $query = \App\Models\TreasuryAccount::query();

        if (\Illuminate\Support\Facades\Schema::hasColumn('treasury_accounts', 'currency')) {
            $query->where('currency', $currency);
        }

        if ($paymentRoute === 'cash') {
            $query->where(function ($q) {
                foreach (['account_type', 'type', 'category', 'account_category'] as $column) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('treasury_accounts', $column)) {
                        $q->orWhere($column, 'cash')
                          ->orWhere($column, 'main_cash')
                          ->orWhere($column, 'Cash')
                          ->orWhere($column, 'Main Cash');
                    }
                }

                foreach (['account_name', 'name', 'title'] as $column) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('treasury_accounts', $column)) {
                        $q->orWhere($column, 'like', '%cash%')
                          ->orWhere($column, 'like', '%Cash%');
                    }
                }
            });
        }

        if ($paymentRoute === 'bank') {
            $query->where(function ($q) {
                $hasTypedColumn = false;

                foreach (['account_type', 'type', 'category', 'account_category'] as $column) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('treasury_accounts', $column)) {
                        $hasTypedColumn = true;
                        $q->orWhere($column, 'bank')
                          ->orWhere($column, 'bank_account')
                          ->orWhere($column, 'Bank')
                          ->orWhere($column, 'Bank Account');
                    }
                }

                foreach (['account_name', 'name', 'title'] as $column) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('treasury_accounts', $column)) {
                        $q->orWhere($column, 'like', '%bank%')
                          ->orWhere($column, 'like', '%Bank%');
                    }
                }

                if (! $hasTypedColumn) {
                    foreach (['account_name', 'name', 'title'] as $column) {
                        if (\Illuminate\Support\Facades\Schema::hasColumn('treasury_accounts', $column)) {
                            $q->orWhere($column, 'not like', '%cash%')
                              ->orWhere($column, 'not like', '%Cash%');
                        }
                    }
                }
            });
        }

        $labelColumn = 'account_name';

        foreach (['account_name', 'name', 'title'] as $column) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('treasury_accounts', $column)) {
                $labelColumn = $column;
                break;
            }
        }

        return $query
            ->orderBy($labelColumn)
            ->get()
            ->mapWithKeys(function ($account) use ($labelColumn) {
                $currency = $account->currency ?? '';
                $label = trim((string) ($account->{$labelColumn} ?? ('Account #' . $account->id)));

                return [
                    $account->id => $currency ? "{$label} - {$currency}" : $label,
                ];
            })
            ->toArray();
    }

public function getSlipNumber(): string
    {
        foreach ([
            $this->record->slip_number ?? null,
            $this->record->reference_no ?? null,
            $this->record->reference ?? null,
            $this->record->code ?? null,
            'SLIP-' . str_pad((string) $this->record->getKey(), 5, '0', STR_PAD_LEFT),
        ] as $value) {
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        return '—';
    }

    public function getPaymentRouteLabel(): string
    {
        $value = $this->record->payment_route ?? $this->record->payment_method ?? null;

        return match ((string) $value) {
            'bank' => 'Bank Transfer',
            'cash' => 'Cash Payment',
            'bank_transfer' => 'Bank Transfer',
            'cash_payment' => 'Cash Payment',
            default => $value ? ucfirst(str_replace('_', ' ', (string) $value)) : '—',
        };
    }

    public function getTreasuryAccountLabel(): string
    {
        $loaded = [
            data_get($this->record, 'treasuryAccount.name'),
            data_get($this->record, 'treasury_account.name'),
            data_get($this->record, 'treasuryAccount.account_name'),
            data_get($this->record, 'treasury_account.account_name'),
            $this->record->treasury_account_name ?? null,
        ];

        foreach ($loaded as $value) {
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        $id = $this->record->treasury_account_id ?? $this->record->treasury_id ?? null;

        if ($id && class_exists(TreasuryAccount::class)) {
            try {
                $account = TreasuryAccount::query()->find($id);

                if ($account) {
                    return $account->name
                        ?? $account->account_name
                        ?? $account->bank_name
                        ?? ('Treasury Account #' . $account->getKey());
                }
            } catch (\Throwable $e) {
                return '—';
            }
        }

        return '—';
    }

    public function getPaymentReference(): string
    {
        foreach ([
            $this->record->payment_reference ?? null,
            $this->record->bank_reference ?? null,
            $this->record->reference_number ?? null,
            $this->record->transaction_reference ?? null,
        ] as $value) {
            $value = trim((string) $value);

            if ($value !== '') {
                return $value;
            }
        }

        return '—';
    }

    public function saveSlipAttachment(array $data): void
    {
        $attachment = new SalarySlipAttachment();
        $table = $attachment->getTable();
        $columns = DbSchema::hasTable($table) ? DbSchema::getColumnListing($table) : [];

        $filePath = $data['file_path'] ?? null;

        if (is_array($filePath)) {
            $filePath = reset($filePath);
        }

        $payload = [];

        foreach ([
            'salary_slip_id' => $this->record->getKey(),
            'type' => $data['type'] ?? 'supporting_file',
            'attachment_type' => $data['type'] ?? 'supporting_file',
            'file_path' => $filePath,
            'path' => $filePath,
            'attachment' => $filePath,
            'file_name' => $filePath ? basename((string) $filePath) : null,
            'name' => $filePath ? basename((string) $filePath) : null,
            'notes' => $data['notes'] ?? null,
        ] as $column => $value) {
            if (in_array($column, $columns, true)) {
                $payload[$column] = $value;
            }
        }

        if (method_exists($this->record, 'attachments')) {
            $this->record->attachments()->create($payload);
            return;
        }

        SalarySlipAttachment::query()->create($payload);
    }

    public function getEmployeeDisplayName(): string
    {
        $candidates = [
            data_get($this->record, 'employment.employee_name'),
            data_get($this->record, 'employment.full_name'),
            data_get($this->record, 'employment.name'),
            data_get($this->record, 'employment.candidate_name'),
            data_get($this->record, 'employment.applicant_name'),
            data_get($this->record, 'employment.candidate.full_name'),
            data_get($this->record, 'employment.candidate.name'),
            data_get($this->record, 'employment.jobApplication.full_name'),
            data_get($this->record, 'employment.jobApplication.name'),
            data_get($this->record, 'employee.full_name'),
            data_get($this->record, 'employee.name'),
            $this->record->employee_name ?? null,
            $this->record->full_name ?? null,
            $this->record->name ?? null,
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '' && strtolower($candidate) !== 'employee') {
                return $candidate;
            }
        }

        return 'Employee';
    }

    public function getEmployeeJobTitle(): string
    {
        $candidates = [
            data_get($this->record, 'employment.job_title'),
            data_get($this->record, 'employment.position'),
            data_get($this->record, 'employment.position_title'),
            data_get($this->record, 'employment.jobOpening.title'),
            data_get($this->record, 'employment.jobApplication.position'),
            data_get($this->record, 'employee.job_title'),
            data_get($this->record, 'employee.position'),
            $this->record->job_title ?? null,
            $this->record->position ?? null,
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return '—';
    }

    public function getEmployeeCode(): string
    {
        $candidates = [
            data_get($this->record, 'employment.employee_code'),
            data_get($this->record, 'employment.job_number'),
            data_get($this->record, 'employment.code'),
            data_get($this->record, 'employee.employee_code'),
            data_get($this->record, 'employee.job_number'),
            $this->record->employee_code ?? null,
            $this->record->job_number ?? null,
            $this->record->code ?? null,
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);

            if ($candidate !== '') {
                return $candidate;
            }
        }

        return '—';
    }

    public function getEditUrl(): string
    {
        try {
            return static::getResource()::getUrl('edit', ['record' => $this->record]);
        } catch (\Throwable $e) {
            return '#';
        }
    }

    public function getPrintUrl(): string
    {
        $id = $this->record->getKey();

        $routeNames = [
            'salary-slips.print',
            'salary_slips.print',
            'filament.admin.resources.salary-slips.print',
        ];

        foreach ($routeNames as $routeName) {
            if (Route::has($routeName)) {
                try {
                    return route($routeName, $this->record);
                } catch (\Throwable $e) {
                    try {
                        return route($routeName, ['salarySlip' => $id]);
                    } catch (\Throwable $e) {
                        try {
                            return route($routeName, ['record' => $id]);
                        } catch (\Throwable $e) {
                            continue;
                        }
                    }
                }
            }
        }

        return url('/salary-slips/' . $id . '/print');
    }

    public function getStatusLabel(): string
    {
        return match ((string) $this->record->status) {
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'sent_to_bank' => 'Sent To Bank',
            'paid' => 'Paid',
            'bank_rejected' => 'Bank Rejected',
            'cancelled' => 'Cancelled',
            default => ucfirst((string) ($this->record->status ?: 'Draft')),
        };
    }

    public function getStatusTheme(): array
    {
        return match ((string) $this->record->status) {
            'approved' => [
                'wrap' => 'from-blue-600 via-indigo-700 to-sky-800',
                'badge' => 'bg-blue-100 text-blue-800 ring-blue-200',
                'accent' => 'text-blue-700 dark:text-blue-300',
            ],
            'sent_to_bank' => [
                'wrap' => 'from-amber-500 via-orange-600 to-yellow-700',
                'badge' => 'bg-amber-100 text-amber-800 ring-amber-200',
                'accent' => 'text-amber-700 dark:text-amber-300',
            ],
            'paid' => [
                'wrap' => 'from-emerald-600 via-emerald-700 to-teal-800',
                'badge' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                'accent' => 'text-emerald-700 dark:text-emerald-300',
            ],
            'bank_rejected', 'cancelled' => [
                'wrap' => 'from-rose-600 via-red-700 to-red-900',
                'badge' => 'bg-rose-100 text-rose-800 ring-rose-200',
                'accent' => 'text-rose-700 dark:text-rose-300',
            ],
            default => [
                'wrap' => 'from-slate-700 via-slate-800 to-zinc-900',
                'badge' => 'bg-slate-100 text-slate-800 ring-slate-200',
                'accent' => 'text-slate-700 dark:text-slate-300',
            ],
        };
    }

public function getSalarySlipDays(): Collection
    {
        try {
            if (method_exists($this->record, 'days')) {
                return $this->record->days()->orderBy('date')->get();
            }
        } catch (\Throwable $e) {
            try {
                return $this->record->days()->get();
            } catch (\Throwable $e) {
                return collect();
            }
        }

        return collect($this->record->days ?? []);
    }

    public function getAttendanceSummary(): array
    {
        $days = $this->getSalarySlipDays();

        $normalize = fn ($value) => strtolower(trim((string) $value));

        $statusCount = function (string $status) use ($days, $normalize): int {
            return $days->filter(function ($day) use ($status, $normalize) {
                return $normalize($day->attendance_status ?? $day->status ?? '') === $status;
            })->count();
        };

        $paidCount = $days->filter(function ($day) {
            return (bool) ($day->is_paid_day ?? $day->paid ?? false);
        })->count();

        $workedDaysTotal = $this->record->worked_days_total
            ?? $this->record->worked_days
            ?? $this->record->days_worked
            ?? $paidCount;

        $absent = $statusCount('absent');
        $unpaidLeave = $statusCount('unpaid_leave');

        return [
            'worked_days_total' => (float) $workedDaysTotal,
            'not_worked_unpaid' => $absent + $unpaidLeave,
            'present' => $statusCount('present'),
            'absent' => $absent,
            'sick' => $statusCount('sick'),
            'leave' => $statusCount('leave'),
            'unpaid_leave' => $unpaidLeave,
            'holiday' => $statusCount('holiday'),
            'travel' => $statusCount('travel'),
            'other' => $statusCount('other'),
        ];
    }

    public function getSlipAttachments()
    {
        try {
            if ($this->record->relationLoaded('attachments')) {
                return collect($this->record->attachments);
            }

            if (method_exists($this->record, 'attachments')) {
                return $this->record->attachments()->latest()->get();
            }
        } catch (\Throwable $e) {
            return collect();
        }

        return collect();
    }

    public function formatMoney($amount, ?string $currency = null): string
    {
        $currency = $currency ?: ($this->record->currency ?? 'EUR');

        if ($amount === null || $amount === '') {
            $amount = 0;
        }

        return number_format((float) $amount, 2) . ' ' . $currency;
    }

    public function safeDate($value): string
    {
        if (! $value) {
            return '—';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }

protected function resetEmployeePaymentConfirmation(): void
    {
        $this->record->forceFill([
            'employee_confirmation_status' => null,
            'employee_confirmed_at' => null,
            'employee_confirmation_notes' => null,
            'employee_confirmation_ip' => null,
            'employee_confirmation_user_agent' => null,
        ])->save();

        $this->record->refresh();
    }



    protected function notifyPortalAboutSalarySlip(string $event, ?string $title = null, ?string $message = null, bool $sendEmail = true): void
    {
        try {
            $this->record->loadMissing(['employment']);

            app(PortalNotificationService::class)->notifySalarySlip(
                salarySlip: $this->record,
                event: $event,
                customTitle: $title,
                customMessage: $message,
                sendEmail: $sendEmail,
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }


    protected function getLinkedPayableReimbursements(): \Illuminate\Support\Collection
    {
        if (! class_exists(\App\Models\FinanceExpense::class)) {
            return collect();
        }

        return \App\Models\FinanceExpense::query()
            ->where('reimbursed_salary_slip_id', $this->record->id)
            ->where('paid_by', \App\Models\FinanceExpense::PAID_BY_CANDIDATE)
            ->whereIn('reimbursement_status', [
                \App\Models\FinanceExpense::REIMBURSEMENT_APPROVED,
                \App\Models\FinanceExpense::REIMBURSEMENT_PAID,
                \App\Models\FinanceExpense::REIMBURSEMENT_PENDING,
            ])
            ->get();
    }

    protected function buildReimbursementExchangeRateRows(?string $targetCurrency = null): array
    {
        $targetCurrency = strtoupper((string) ($targetCurrency ?: $this->record->currency ?: 'EUR'));

        $existingRates = collect((array) ($this->record->reimbursement_exchange_rates ?? []))
            ->mapWithKeys(function ($row) {
                $currency = strtoupper((string) ($row['currency'] ?? $row['currency_label'] ?? ''));

                return $currency !== ''
                    ? [$currency => $row]
                    : [];
            });

        return $this->getLinkedPayableReimbursements()
            ->groupBy(fn ($expense) => strtoupper((string) ($expense->reimbursement_currency ?: $expense->currency ?: $targetCurrency)))
            ->reject(fn ($items, $currency) => strtoupper((string) $currency) === $targetCurrency)
            ->map(function ($items, $currency) use ($existingRates) {
                $amount = (float) $items->sum(fn ($expense) => (float) ($expense->reimbursement_amount ?: $expense->amount ?: 0));
                $existing = $existingRates->get(strtoupper((string) $currency), []);
                $rate = (float) ($existing['exchange_rate'] ?? 0);
                $converted = $rate > 0 ? round($amount * $rate, 2) : null;

                return [
                    'currency' => strtoupper((string) $currency),
                    'currency_label' => strtoupper((string) $currency),
                    'original_amount' => number_format($amount, 2),
                    'exchange_rate' => $rate > 0 ? $rate : null,
                    'converted_preview' => $converted !== null ? number_format($converted, 2) : 'Enter rate',
                ];
            })
            ->values()
            ->toArray();
    }

    protected function calculateReimbursementPaymentTotals(string $paymentCurrency, array $rateRows = []): array
    {
        $paymentCurrency = strtoupper((string) ($paymentCurrency ?: $this->record->currency ?: 'EUR'));

        $salaryNet = (float) ($this->record->net_amount ?? 0);

        $linked = $this->getLinkedPayableReimbursements();

        $sameCurrencyTotal = 0.0;
        $convertedTotal = 0.0;
        $breakdown = [];
        $exchangeRates = [];

        $ratesByCurrency = collect($rateRows)
            ->mapWithKeys(function ($row) {
                $currency = strtoupper((string) ($row['currency'] ?? $row['currency_label'] ?? ''));

                return $currency !== ''
                    ? [$currency => $this->normalizeNumberInput($row['exchange_rate'] ?? 0)]
                    : [];
            });

        foreach ($linked->groupBy(fn ($expense) => strtoupper((string) ($expense->reimbursement_currency ?: $expense->currency ?: $paymentCurrency))) as $currency => $items) {
            $currency = strtoupper((string) $currency);
            $originalAmount = (float) $items->sum(fn ($expense) => (float) ($expense->reimbursement_amount ?: $expense->amount ?: 0));

            if ($currency === $paymentCurrency) {
                $sameCurrencyTotal += $originalAmount;

                $breakdown[] = [
                    'currency' => $currency,
                    'original_amount' => round($originalAmount, 2),
                    'exchange_rate' => 1,
                    'converted_amount' => round($originalAmount, 2),
                    'payment_currency' => $paymentCurrency,
                    'type' => 'same_currency',
                ];

                continue;
            }

            $rate = (float) ($ratesByCurrency[$currency] ?? 0);

            if ($rate <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'reimbursement_exchange_rates' => "Exchange rate is required for {$currency} reimbursements to {$paymentCurrency}.",
                ]);
            }

            $convertedAmount = round($originalAmount * $rate, 2);
            $convertedTotal += $convertedAmount;

            $exchangeRates[] = [
                'currency' => $currency,
                'exchange_rate' => $rate,
                'payment_currency' => $paymentCurrency,
            ];

            $breakdown[] = [
                'currency' => $currency,
                'original_amount' => round($originalAmount, 2),
                'exchange_rate' => $rate,
                'converted_amount' => $convertedAmount,
                'payment_currency' => $paymentCurrency,
                'type' => 'converted',
            ];
        }

        return [
            'salary_net' => round($salaryNet, 2),
            'same_currency_total' => round($sameCurrencyTotal, 2),
            'converted_total' => round($convertedTotal, 2),
            'payment_total_amount' => round($salaryNet + $sameCurrencyTotal + $convertedTotal, 2),
            'exchange_rates' => $exchangeRates,
            'breakdown' => $breakdown,
        ];
    }



    protected function normalizeNumberInput(mixed $value): float
    {
        if (is_null($value)) {
            return 0.0;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return 0.0;
        }

        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^0-9.\-]/', '', $value);

        return is_numeric($value) ? (float) $value : 0.0;
    }

    protected function convertedPreviewAmount(mixed $amount, mixed $rate): string
    {
        $amount = $this->normalizeNumberInput($amount);
        $rate = $this->normalizeNumberInput($rate);

        if ($amount <= 0 || $rate <= 0) {
            return 'Enter rate';
        }

        return number_format(round($amount * $rate, 2), 2);
    }

    protected function refreshReimbursementExchangeRatePreviews(array $rows): array
    {
        foreach ($rows as $key => $row) {
            $rows[$key]['converted_preview'] = $this->convertedPreviewAmount(
                $row['original_amount'] ?? 0,
                $row['exchange_rate'] ?? 0,
            );
        }

        return $rows;
    }

    protected function processSalarySlipPayment(array $data): void
    {
        $paymentRoute = strtolower((string) ($data['payment_route'] ?? $data['payment_method'] ?? 'bank'));
        $paymentDate = $data['payment_date'] ?? now();

        $paymentMethod = $paymentRoute === 'cash'
            ? \App\Models\SalarySlip::PAYMENT_METHOD_CASH
            : \App\Models\SalarySlip::PAYMENT_METHOD_BANK;

        $paymentCurrency = strtoupper((string) ($data['currency'] ?? $this->record->currency ?? 'EUR'));

        $reimbursementFx = $this->calculateReimbursementPaymentTotals(
            $paymentCurrency,
            $data['reimbursement_exchange_rates'] ?? []
        );

        $payload = [
            'payment_method' => $paymentMethod,
            'currency' => $paymentCurrency,
            'treasury_account_id' => $data['treasury_account_id'] ?? null,
            'employee_confirmation_status' => 'pending',
            'employee_confirmation_notes' => null,
            'employee_confirmed_at' => null,
            'employee_confirmation_ip' => null,
            'employee_confirmation_user_agent' => null,
        ];

        foreach ([
            'payment_total_amount' => $reimbursementFx['payment_total_amount'],
            'reimbursement_same_currency_total' => $reimbursementFx['same_currency_total'],
            'reimbursement_converted_total' => $reimbursementFx['converted_total'],
            'reimbursement_exchange_rates' => $reimbursementFx['exchange_rates'],
            'reimbursement_breakdown' => $reimbursementFx['breakdown'],
        ] as $column => $value) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', $column)) {
                $payload[$column] = $value;
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', 'payment_reference')) {
            $payload['payment_reference'] = $data['payment_reference'] ?? null;
        }

        if ($paymentMethod === \App\Models\SalarySlip::PAYMENT_METHOD_CASH) {
            $payload['status'] = \App\Models\SalarySlip::STATUS_PAID;

            if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', 'paid_at')) {
                $payload['paid_at'] = $paymentDate;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', 'bank_sent_at')) {
                $payload['bank_sent_at'] = null;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', 'rejected_at')) {
                $payload['rejected_at'] = null;
            }
        } else {
            $payload['status'] = \App\Models\SalarySlip::STATUS_SENT_TO_BANK;

            if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', 'bank_sent_at')) {
                $payload['bank_sent_at'] = $paymentDate;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', 'paid_at')) {
                $payload['paid_at'] = null;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('salary_slips', 'rejected_at')) {
                $payload['rejected_at'] = null;
            }
        }

        $this->record->forceFill($payload)->save();
        $this->record->refresh();

        $this->createPortalPaymentNotification($paymentMethod);

        if ($paymentMethod === \App\Models\SalarySlip::PAYMENT_METHOD_CASH) {
            $this->notifyPortalAboutSalarySlip(
                'payment_sent',
                'Cash Salary Payment Ready',
                'Your cash salary payment has been processed. Please open the portal and confirm once you receive the cash payment.'
            );
        } else {
            $this->notifyPortalAboutSalarySlip(
                'payment_sent',
                'Salary Payment Sent To Bank',
                'Your salary payment has been sent to bank processing. Please confirm from the portal once the payment is received.'
            );
        }
        $this->sendSalarySlipPortalEmail('payment_confirmation');
    }

protected function buildAttendanceRowsForForm(): array
    {
        $this->record->refresh();
        $this->record->load('days');

        return $this->record->days
            ->sortBy(fn ($day) => $day->work_date ?? $day->date ?? $day->id)
            ->map(function ($day): array {
                $status = $day->status ?: 'present';

                $dateValue = $day->work_date ?? $day->date ?? null;
                $dateText = '-';
                $dayName = $day->day_name ?? '-';

                if ($dateValue) {
                    try {
                        $carbon = \Carbon\Carbon::parse($dateValue);
                        $dateText = $carbon->format('Y-m-d');
                        $dayName = $day->day_name ?: $carbon->format('l');
                    } catch (\Throwable $e) {
                        $dateText = (string) $dateValue;
                    }
                }

                if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                    $isPaid = false;
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'is_paid')) {
                    $isPaid = (bool) $day->is_paid;
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'paid')) {
                    $isPaid = (bool) $day->paid;
                } else {
                    $isPaid = true;
                }

                return [
                    'id' => $day->id,
                    'date' => $dateText,
                    'day_name' => $dayName,
                    'is_paid' => $isPaid,
                    'status' => $status,
                    'notes' => $day->notes ?: '',
                ];
            })
            ->values()
            ->toArray();
    }

    protected function saveAttendanceRowsFromForm(array $rows): void
    {
        foreach ($rows as $row) {
            $dayId = $row['id'] ?? null;

            if (! $dayId) {
                continue;
            }

            $day = \App\Models\SalarySlipDay::query()
                ->where('salary_slip_id', $this->record->id)
                ->whereKey($dayId)
                ->first();

            if (! $day) {
                continue;
            }

            $status = $row['status'] ?? 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                $isPaid = false;
            } else {
                $isPaid = (bool) ($row['is_paid'] ?? false);
            }

            $payload = [];

            if (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'status')) {
                $payload['status'] = $status;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'is_paid')) {
                $payload['is_paid'] = $isPaid;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'paid')) {
                $payload['paid'] = $isPaid;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'notes')) {
                $payload['notes'] = $row['notes'] ?? null;
            }

            $day->forceFill($payload)->save();
        }

        $this->recalculateSalarySlipFromAttendance();
    }

    protected function recalculateSalarySlipFromAttendance(): void
    {
        $this->record->refresh();
        $this->record->load('days');

        $paidDays = $this->record->days->filter(function ($day): bool {
            $status = $day->status ?: 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                return false;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'is_paid')) {
                return (bool) $day->is_paid;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($day->getTable(), 'paid')) {
                return (bool) $day->paid;
            }

            return true;
        })->count();

        $dailyRate = (float) ($this->record->daily_rate ?? 0);
        $monthlySalary = (float) ($this->record->monthly_salary ?? 0);
        $adjustments = (float) ($this->record->adjustments_amount ?? 0);
        $deductions = (float) ($this->record->deductions_amount ?? 0);

        if (($this->record->salary_basis ?? null) === \App\Models\SalarySlip::BASIS_MONTHLY && $monthlySalary > 0) {
            $baseAmount = round(($monthlySalary / max(1, $this->record->days->count())) * $paidDays, 2);
        } else {
            $baseAmount = round($dailyRate * $paidDays, 2);
        }

        $netAmount = round($baseAmount + $adjustments - $deductions, 2);

        $this->record->forceFill([
            'days_worked' => $paidDays,
            'base_amount' => $baseAmount,
            'net_amount' => $netAmount,
        ])->save();

        if (method_exists($this->record, 'syncTreasuryPosting')) {
            $this->record->syncTreasuryPosting();
        }

        $this->record->refresh();
    }


    protected function loadAttendanceRows(): void
    {
        /*
         * Compatibility method.
         * Attendance is now loaded through the Filament Action form using buildAttendanceRowsForForm().
         * This keeps old mount() calls safe without relying on custom Livewire table state.
         */
        $this->attendanceRows = $this->buildAttendanceRowsForForm();
    }

    protected function buildAttendanceRowsForPopup(): array
    {
        $query = DB::table('salary_slip_days')
            ->where('salary_slip_id', $this->record->id);

        if (DbSchema::hasColumn('salary_slip_days', 'is_extra_day')) {
            $query->where(function ($q) {
                $q->whereNull('is_extra_day')
                    ->orWhere('is_extra_day', false)
                    ->orWhere('is_extra_day', 0);
            });
        }

        return $query
            ->orderBy('work_date')
            ->orderBy('id')
            ->get()
            ->map(function ($day): array {
                return [
                    'id' => $day->id,
                    'date' => $day->work_date
                        ? \Carbon\Carbon::parse($day->work_date)->format('Y-m-d')
                        : '-',
                    'day_name' => $day->day_name ?: '-',
                    'attendance_status' => $day->attendance_status ?: 'present',
                    'is_paid' => (bool) $day->is_paid_day,
                    'notes' => $day->notes ?: '',
                ];
            })
            ->values()
            ->toArray();
    }

    protected function buildAdditionalTravelDaysForPopup(): array
    {
        if (! DbSchema::hasColumn('salary_slip_days', 'is_extra_day')) {
            return [];
        }

        return DB::table('salary_slip_days')
            ->where('salary_slip_id', $this->record->id)
            ->where('is_extra_day', true)
            ->orderBy('work_date')
            ->orderBy('id')
            ->get()
            ->map(function ($day): array {
                return [
                    'id' => $day->id,
                    'date' => $day->work_date
                        ? \Carbon\Carbon::parse($day->work_date)->format('Y-m-d')
                        : null,
                    'day_type' => $day->day_type ?: 'travel_day',
                    'pay_multiplier' => (float) ($day->pay_multiplier ?? 1),
                    'notes' => $day->notes ?: '',
                ];
            })
            ->values()
            ->toArray();
    }

    protected function saveAttendanceRowsFromPopup(array $rows, array $additionalTravelDays = []): void
    {
        foreach ($rows as $row) {
            $dayId = $row['id'] ?? null;

            if (! $dayId) {
                continue;
            }

            $status = $row['attendance_status'] ?? 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                $isPaid = false;
            } else {
                $isPaid = (bool) ($row['is_paid'] ?? false);
            }

            DB::table('salary_slip_days')
                ->where('id', $dayId)
                ->where('salary_slip_id', $this->record->id)
                ->update([
                    'attendance_status' => $status,
                    'is_paid_day' => $isPaid ? 1 : 0,
                    'notes' => $row['notes'] ?? null,
                    'updated_at' => now(),
                ]);
        }

        $this->saveAdditionalTravelDaysFromPopup($additionalTravelDays);
        $this->recalculateSalarySlipFromAttendancePopup();
    }

    protected function saveAdditionalTravelDaysFromPopup(array $rows): void
    {
        if (! DbSchema::hasColumn('salary_slip_days', 'is_extra_day')) {
            return;
        }

        $keptIds = [];

        foreach ($rows as $row) {
            $date = $row['date'] ?? null;

            if (blank($date)) {
                continue;
            }

            try {
                $workDate = \Carbon\Carbon::parse($date)->toDateString();
            } catch (\Throwable $e) {
                continue;
            }

            $rate = $this->normalizeNumberInput($row['pay_multiplier'] ?? 1);

            if ($rate <= 0) {
                $rate = 1;
            }

            $payload = [
                'salary_slip_id' => $this->record->id,
                'work_date' => $workDate,
                'day_name' => \Carbon\Carbon::parse($workDate)->format('l'),
                'attendance_status' => 'travel',
                'is_paid_day' => true,
                'is_extra_day' => true,
                'day_type' => $row['day_type'] ?? 'travel_day',
                'pay_multiplier' => $rate,
                'notes' => $row['notes'] ?? null,
                'updated_at' => now(),
            ];

            $id = (int) ($row['id'] ?? 0);

            if ($id > 0) {
                DB::table('salary_slip_days')
                    ->where('id', $id)
                    ->where('salary_slip_id', $this->record->id)
                    ->update($payload);

                $keptIds[] = $id;
                continue;
            }

            $payload['created_at'] = now();

            $keptIds[] = DB::table('salary_slip_days')->insertGetId($payload);
        }

        $deleteQuery = DB::table('salary_slip_days')
            ->where('salary_slip_id', $this->record->id)
            ->where('is_extra_day', true);

        if (! empty($keptIds)) {
            $deleteQuery->whereNotIn('id', $keptIds);
        }

        $deleteQuery->delete();
    }

    protected function recalculateSalarySlipFromAttendancePopup(): void
    {
        $days = DB::table('salary_slip_days')
            ->where('salary_slip_id', $this->record->id)
            ->get();

        $paidDays = 0.0;

        foreach ($days as $day) {
            $status = $day->attendance_status ?: 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                continue;
            }

            if (! (bool) $day->is_paid_day) {
                continue;
            }

            $multiplier = 1.0;

            if (property_exists($day, 'pay_multiplier')) {
                $multiplier = (float) ($day->pay_multiplier ?? 1);
            }

            if ($multiplier <= 0) {
                $multiplier = 1.0;
            }

            $paidDays += $multiplier;
        }

        $dailyRate = (float) ($this->record->daily_rate ?? 0);
        $monthlySalary = (float) ($this->record->monthly_salary ?? 0);
        $adjustments = (float) ($this->record->adjustments_amount ?? 0);
        $deductions = (float) ($this->record->deductions_amount ?? 0);

        if (($this->record->salary_basis ?? null) === \App\Models\SalarySlip::BASIS_MONTHLY && $monthlySalary > 0) {
            $baseDays = max(1, $days->filter(fn ($day) => ! (bool) ($day->is_extra_day ?? false))->count());
            $baseAmount = round(($monthlySalary / $baseDays) * $paidDays, 2);
        } else {
            $baseAmount = round($dailyRate * $paidDays, 2);
        }

        $netAmount = round($baseAmount + $adjustments - $deductions, 2);

        DB::table('salary_slips')
            ->where('id', $this->record->id)
            ->update([
                'days_worked' => $paidDays,
                'base_amount' => $baseAmount,
                'net_amount' => $netAmount,
                'updated_at' => now(),
            ]);

        $this->record->refresh();

        if (method_exists($this->record, 'syncTreasuryPosting')) {
            $this->record->syncTreasuryPosting();
        }

        $this->record->refresh();
    }


    protected function createPortalPaymentNotification(string $paymentMethod): void
    {
        try {
            $employment = $this->record->employment;

            if (! $employment) {
                return;
            }

            $title = $paymentMethod === \App\Models\SalarySlip::PAYMENT_METHOD_CASH
                ? 'Cash salary payment requires your receipt confirmation'
                : 'Salary payment requires your receipt confirmation';

            $message = 'Please open your portal dashboard and confirm whether you received salary slip #' . $this->record->id . '.';

            if (class_exists(\App\Models\PortalNotification::class)) {
                $notificationData = [
                    'title' => $title,
                    'message' => $message,
                    'type' => 'salary_payment_confirmation',
                    'is_read' => false,
                ];

                foreach (['employment_id', 'salary_slip_id'] as $column) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('portal_notifications', $column)) {
                        $notificationData[$column] = $column === 'employment_id' ? $employment->id : $this->record->id;
                    }
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('portal_notifications', 'portal_account_id')) {
                    $portalAccountId = $employment->portalUser?->id
                        ?? $employment->portalAccount?->id
                        ?? null;

                    if ($portalAccountId) {
                        $notificationData['portal_account_id'] = $portalAccountId;
                    }
                }

                \App\Models\PortalNotification::query()->create($notificationData);
            }

            if (class_exists(\App\Models\PortalTimelineEvent::class)) {
                $timelineData = [
                    'title' => $title,
                    'description' => $message,
                    'type' => 'salary_payment_confirmation',
                ];

                foreach (['employment_id', 'salary_slip_id'] as $column) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('portal_timeline_events', $column)) {
                        $timelineData[$column] = $column === 'employment_id' ? $employment->id : $this->record->id;
                    }
                }

                \App\Models\PortalTimelineEvent::query()->create($timelineData);
            }
        } catch (\Throwable $e) {
            // Payment must not fail because notification table/schema differs.
        }
    }


    protected function sendSalarySlipPortalEmail(string $mailType = 'payment_confirmation'): void
    {
        try {
            $this->record->loadMissing(['employment', 'client', 'project']);

            $email = trim((string) ($this->record->employment?->employee_email ?? ''));

            if ($email === '') {
                return;
            }

            Mail::to($email)->send(new PortalSalarySlipActionRequiredMail($this->record, $mailType));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('salary_slips', 'view') ?? false);
    }


    protected function calculateSalarySlipReimbursementTotalsFinal(?string $paymentCurrency = null, array $rateRows = []): array
    {
        $paymentCurrency = strtoupper((string) ($paymentCurrency ?: $this->record->currency ?: 'EUR'));
        $salaryCurrency = strtoupper((string) ($this->record->currency ?: $paymentCurrency));
        $salaryNet = (float) ($this->record->net_amount ?? 0);

        $expenses = \App\Models\FinanceExpense::query()
            ->where('reimbursed_salary_slip_id', $this->record->id)
            ->where(function ($query) {
                $query->where('reimbursement_required', true)
                    ->orWhere('paid_by', \App\Models\FinanceExpense::PAID_BY_CANDIDATE);
            })
            ->get();

        $ratesByCurrency = collect($rateRows)
            ->mapWithKeys(function ($row) {
                $currency = strtoupper((string) ($row['currency'] ?? $row['original_currency'] ?? ''));
                return $currency !== '' ? [$currency => (float) ($row['exchange_rate'] ?? 0)] : [];
            })
            ->all();

        $sameCurrencyTotal = 0.0;
        $convertedTotal = 0.0;
        $breakdown = [];
        $exchangeRates = [];

        foreach ($expenses as $expense) {
            $currency = strtoupper((string) ($expense->reimbursement_currency ?: $expense->currency ?: $salaryCurrency));
            $amount = (float) ($expense->reimbursement_amount ?: $expense->amount ?: 0);

            if ($amount <= 0) {
                continue;
            }

            $breakdown[] = [
                'finance_expense_id' => $expense->id,
                'title' => $expense->title,
                'currency' => $currency,
                'original_currency' => $currency,
                'original_amount' => round($amount, 2),
                'amount' => round($amount, 2),
            ];

            if ($currency === $paymentCurrency) {
                $sameCurrencyTotal += $amount;

                $exchangeRates[] = [
                    'finance_expense_id' => $expense->id,
                    'currency' => $currency,
                    'original_currency' => $currency,
                    'original_amount' => round($amount, 2),
                    'exchange_rate' => 1,
                    'converted_amount' => round($amount, 2),
                    'payment_currency' => $paymentCurrency,
                    'type' => 'same_currency',
                ];

                continue;
            }

            $rate = (float) ($ratesByCurrency[$currency] ?? 0);

            if ($rate <= 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'reimbursement_exchange_rates' => "Exchange rate is required for {$currency} reimbursements to {$paymentCurrency}.",
                ]);
            }

            $converted = round($amount * $rate, 2);
            $convertedTotal += $converted;

            $exchangeRates[] = [
                'finance_expense_id' => $expense->id,
                'currency' => $currency,
                'original_currency' => $currency,
                'original_amount' => round($amount, 2),
                'exchange_rate' => $rate,
                'converted_amount' => $converted,
                'payment_currency' => $paymentCurrency,
                'type' => 'converted',
            ];
        }

        return [
            'salary_net' => round($salaryNet, 2),
            'same_currency_total' => round($sameCurrencyTotal, 2),
            'converted_total' => round($convertedTotal, 2),
            'payment_total_amount' => round($salaryNet + $sameCurrencyTotal + $convertedTotal, 2),
            'breakdown' => $breakdown,
            'exchange_rates' => $exchangeRates,
        ];
    }

    
}
