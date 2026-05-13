<?php

namespace App\Filament\Resources\FinanceExpenses\Pages;

use App\Filament\Resources\FinanceExpenses\FinanceExpenseResource;
use App\Models\FinanceExpense;
use App\Models\SalarySlip;
use App\Models\TreasuryAccount;
use App\Models\User;
use App\Services\PortalNotificationService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ViewFinanceExpense extends ViewRecord
{
    protected static string $resource = FinanceExpenseResource::class;

    protected string $view = 'filament.resources.finance-expenses.pages.view-finance-expense-premium';

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return $this->record->title ?: ('Finance Expense #' . $this->record->id);
    }

    public function getSubheading(): string|Htmlable|null
    {
        $owner = $this->record->ownerName() ?: 'Unknown Owner';
        $scope = FinanceExpense::scopeLabels()[$this->record->expense_scope] ?? ($this->record->expense_scope ?: '-');
        $status = FinanceExpense::statusLabels()[$this->record->status] ?? ($this->record->status ?: '-');

        return "Owner: {$owner} · Scope: {$scope} · Status: {$status}";
    }

    protected function getHeaderActions(): array
    {
        $expense = $this->record->fresh();
        $status = (string) $expense->status;

        $actions = [
            EditAction::make()
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('finance_expenses', 'edit'))
                ->extraAttributes($this->buttonAttrs('edit')),
        ];

        if ($status === FinanceExpense::STATUS_DRAFT) {
            $actions[] = Action::make('approve')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('finance_expenses', 'approve'))
                ->label('Approve')
                ->icon('heroicon-o-check-badge')
                ->color('info')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('approve'))
                ->action(function (): void {
                    $this->record->status = FinanceExpense::STATUS_APPROVED;
                    $this->record->approved_by = auth()->id();
                    $this->record->save();

                    Notification::make()
                        ->title('Expense approved successfully.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });

            $actions[] = Action::make('cancel')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('finance_expenses', 'cancel') || auth()->user()?->canErp('finance_expenses', 'approve')))
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('danger'))
                ->action(function (): void {
                    $this->record->status = FinanceExpense::STATUS_CANCELLED;
                    $this->record->save();

                    Notification::make()
                        ->title('Expense cancelled.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }

        if ($status === FinanceExpense::STATUS_APPROVED) {
            $actions[] = Action::make('markPaid')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('finance_expenses', 'mark_paid') || auth()->user()?->canErp('finance_expenses', 'process_payment')))
                ->label('Mark as Paid')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->modalHeading('Mark Expense as Paid')
                ->modalDescription('Choose payment route first, then select the institution/source and final treasury account.')
                ->modalSubmitActionLabel('Confirm Payment')
                ->extraAttributes($this->buttonAttrs('paid'))
                ->form([
                    ToggleButtons::make('payment_channel')
                        ->label('Payment Route')
                        ->options([
                            'cash' => 'Cash',
                            'bank' => 'Bank',
                        ])
                        ->default('bank')
                        ->inline()
                        ->grouped()
                        ->live()
                        ->required(),

                    Select::make('cash_source')
                        ->label('Cash Source')
                        ->options(fn () => $this->cashSourceOptions())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->visible(fn (callable $get): bool => $get('payment_channel') === 'cash')
                        ->required(fn (callable $get): bool => $get('payment_channel') === 'cash')
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('treasury_account_id', null)),

                    Select::make('bank_name')
                        ->label('Bank')
                        ->options(fn () => $this->bankNameOptions())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->visible(fn (callable $get): bool => $get('payment_channel') === 'bank')
                        ->required(fn (callable $get): bool => $get('payment_channel') === 'bank')
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('treasury_account_id', null)),

                    Select::make('treasury_account_id')
                        ->label('Treasury Account')
                        ->options(function (callable $get) {
                            $channel = $get('payment_channel');

                            if ($channel === 'cash') {
                                return $this->cashAccountOptions((string) $get('cash_source'));
                            }

                            if ($channel === 'bank') {
                                return $this->bankAccountOptions((string) $get('bank_name'));
                            }

                            return [];
                        })
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->helperText('Only accounts matching the selected cash source or bank will appear.'),

                    DatePicker::make('expense_date')
                        ->label('Payment Date')
                        ->default($this->record->expense_date?->toDateString() ?: now()->toDateString())
                        ->required()
                        ->native(false),

                    Textarea::make('notes')
                        ->label('Payment Notes')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $treasuryAccountId = (int) ($data['treasury_account_id'] ?? 0);

                    $account = TreasuryAccount::query()->find($treasuryAccountId);

                    if (! $account) {
                        Notification::make()
                            ->title('Please choose a valid treasury account.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $channel = (string) ($data['payment_channel'] ?? 'bank');

                    if ($channel === 'cash' && (string) $account->account_type !== (string) TreasuryAccount::TYPE_CASH) {
                        Notification::make()
                            ->title('Selected account is not a cash account.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($channel === 'bank' && (string) $account->account_type !== (string) TreasuryAccount::TYPE_BANK) {
                        Notification::make()
                            ->title('Selected account is not a bank account.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $selectedBank = trim((string) ($data['bank_name'] ?? ''));
                    $selectedCashSource = trim((string) ($data['cash_source'] ?? ''));

                    if ($channel === 'bank' && $selectedBank !== '') {
                        $accountInstitution = trim((string) ($account->institution_name ?? ''));

                        if ($accountInstitution !== $selectedBank) {
                            Notification::make()
                                ->title('Selected account does not belong to the chosen bank.')
                                ->danger()
                                ->send();

                            return;
                        }
                    }

                    if ($channel === 'cash' && $selectedCashSource !== '') {
                        $accountInstitution = trim((string) ($account->institution_name ?? 'Main Cash'));

                        if ($accountInstitution !== $selectedCashSource) {
                            Notification::make()
                                ->title('Selected account does not belong to the chosen cash source.')
                                ->danger()
                                ->send();

                            return;
                        }
                    }

                    $channelLabel = $channel === 'cash' ? 'Cash' : 'Bank';

                    $this->record->treasury_account_id = $account->id;
                    $this->record->expense_date = $data['expense_date'] ?? $this->record->expense_date;
                    $this->record->notes = $this->mergeNotes(
                        $this->record->notes,
                        $this->buildPaymentAuditNote(
                            $channelLabel,
                            $channel === 'cash' ? $selectedCashSource : $selectedBank,
                            $account->account_name,
                            $data['notes'] ?? null,
                        ),
                    );
                    $this->record->approved_by = $this->record->approved_by ?: auth()->id();
                    $this->record->status = FinanceExpense::STATUS_PAID;
                    $this->record->save();

                    Notification::make()
                        ->title('Expense marked as paid and treasury posting synced.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });

            $actions[] = Action::make('backToDraft')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('finance_expenses', 'back_to_draft') || auth()->user()?->canErp('finance_expenses', 'approve')))
                ->label('Back to Draft')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->record->status = FinanceExpense::STATUS_DRAFT;
                    $this->record->approved_by = null;
                    $this->record->save();

                    Notification::make()
                        ->title('Expense moved back to draft.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });

            $actions[] = Action::make('cancelFromApproved')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('finance_expenses', 'cancel') || auth()->user()?->canErp('finance_expenses', 'approve')))
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('danger'))
                ->action(function (): void {
                    $this->record->status = FinanceExpense::STATUS_CANCELLED;
                    $this->record->save();

                    Notification::make()
                        ->title('Expense cancelled.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }

        if (
            $expense->paid_by === FinanceExpense::PAID_BY_CANDIDATE &&
            $expense->reimbursement_status === FinanceExpense::REIMBURSEMENT_PENDING &&
            in_array($status, [FinanceExpense::STATUS_DRAFT, FinanceExpense::STATUS_APPROVED], true)
        ) {
            $actions[] = Action::make('approveReimbursement')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('finance_expenses', 'approve'))
                ->label('Approve Reimbursement')
                ->icon('heroicon-o-check')
                ->color('warning')
                ->extraAttributes($this->buttonAttrs('partial'))
                ->action(function (): void {
                    $this->record = $this->record->fresh();

                    $payload = [
                        'reimbursement_status' => FinanceExpense::REIMBURSEMENT_APPROVED,
                    ];

                    if (Schema::hasColumn('finance_expenses', 'reimbursement_decision_by')) {
                        $payload['reimbursement_decision_by'] = auth()->id();
                    }

                    if (Schema::hasColumn('finance_expenses', 'reimbursement_decision_at')) {
                        $payload['reimbursement_decision_at'] = now();
                    }

                    $this->record->forceFill($payload)->save();

                    $this->notifyPortalAboutReimbursement(
                        'approved',
                        'Reimbursement Approved',
                        'Your reimbursement claim "' . ($this->record->title ?: 'Claim') . '" has been approved by Finance.'
                    );

                    Notification::make()
                        ->title('Reimbursement approved.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record->id,
                        'refresh' => now()->timestamp,
                    ]));
                });

            $actions[] = Action::make('rejectReimbursement')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('finance_expenses', 'approve'))
                ->label('Reject Reimbursement')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->extraAttributes($this->buttonAttrs('danger'))
                ->action(function (): void {
                    $this->record = $this->record->fresh();

                    $payload = [
                        'reimbursement_status' => FinanceExpense::REIMBURSEMENT_REJECTED,
                    ];

                    if (Schema::hasColumn('finance_expenses', 'reimbursement_decision_by')) {
                        $payload['reimbursement_decision_by'] = auth()->id();
                    }

                    if (Schema::hasColumn('finance_expenses', 'reimbursement_decision_at')) {
                        $payload['reimbursement_decision_at'] = now();
                    }

                    $this->record->forceFill($payload)->save();

                    $this->notifyPortalAboutReimbursement(
                        'rejected',
                        'Reimbursement Rejected',
                        'Your reimbursement claim "' . ($this->record->title ?: 'Claim') . '" has been rejected by Finance.'
                    );

                    Notification::make()
                        ->title('Reimbursement rejected.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record->id,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }

        if (
            $expense->paid_by === FinanceExpense::PAID_BY_CANDIDATE &&
            $expense->reimbursement_status === FinanceExpense::REIMBURSEMENT_APPROVED &&
            blank($expense->reimbursed_salary_slip_id)
        ) {
            $actions[] = Action::make('addToSalarySlip')
                ->hidden(fn () => true)
                ->hidden(fn () => ! (bool) (
                    auth()->user()?->canErp('salary_slips', 'edit')
                    || auth()->user()?->canErp('finance_expenses', 'approve')
                    || auth()->user()?->canErp('finance_expenses', 'edit')
                ))
                ->label('Add to Salary Slip')
                ->icon('heroicon-o-document-plus')
                ->color('warning')
                ->modalHeading('Add Reimbursement to Salary Slip')
                ->modalDescription('Select an unpaid salary slip. Paid, sent-to-bank, and locked salary slips cannot be selected.')
                ->modalSubmitActionLabel('Attach to Salary Slip')
                ->extraAttributes($this->buttonAttrs('partial'))
                ->form([
                    Select::make('salary_slip_id')
                        ->label('Unpaid Salary Slip')
                        ->options(fn () => $this->strictAvailableSalarySlipOptionsForReimbursement())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->helperText('Only draft, approved, or bank-rejected salary slips are available. Paid and sent-to-bank salary slips are blocked.'),
                    Textarea::make('notes')
                        ->label('Reimbursement Notes')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $salarySlipId = (int) ($data['salary_slip_id'] ?? 0);

                    DB::transaction(function () use ($salarySlipId, $data): void {
                        $this->record = $this->record->fresh();

                        $slip = SalarySlip::query()
                            ->whereKey($salarySlipId)
                            ->whereNotIn('status', [
                                SalarySlip::STATUS_SENT_TO_BANK,
                                SalarySlip::STATUS_PAID,
                                SalarySlip::STATUS_LOCKED,
                            ])
                            ->lockForUpdate()
                            ->first();

                        if (! $slip) {
                            throw ValidationException::withMessages([
                                'salary_slip_id' => 'Please choose an unpaid salary slip. Paid or sent-to-bank salary slips cannot be used.',
                            ]);
                        }

                        if (! $this->salarySlipBelongsToExpenseOwner($slip, $this->record->fresh(['preEmployment']))) {
                            throw ValidationException::withMessages([
                                'salary_slip_id' => 'Wrong salary slip. You can only attach reimbursement to the same employee/candidate salary slip.',
                            ]);
                        }

                        if (filled($this->record->reimbursed_salary_slip_id)) {
                            throw ValidationException::withMessages([
                                'salary_slip_id' => 'This reimbursement is already attached to a salary slip.',
                            ]);
                        }

                        if ($this->record->reimbursement_status !== FinanceExpense::REIMBURSEMENT_APPROVED) {
                            throw ValidationException::withMessages([
                                'salary_slip_id' => 'Only approved reimbursements can be attached to a salary slip.',
                            ]);
                        }

                        $amount = (float) ($this->record->reimbursement_amount ?: $this->record->amount ?: 0);
                        $currency = $this->record->reimbursement_currency ?: $this->record->currency ?: $slip->currency;

                        if (strtoupper((string) $currency) !== strtoupper((string) $slip->currency)) {
                            throw ValidationException::withMessages([
                                'salary_slip_id' => 'Salary slip currency must match the reimbursement currency.',
                            ]);
                        }

                        $auditNote = 'Reimbursement added from Finance Expense #' . $this->record->id
                            . ' | Amount: ' . number_format($amount, 2) . ' ' . strtoupper((string) $currency);

                        if (filled($data['notes'] ?? null)) {
                            $auditNote .= ' | Notes: ' . trim((string) $data['notes']);
                        }

                        $slip->adjustments_amount = (float) ($slip->adjustments_amount ?? 0) + $amount;
                        $slip->net_amount = (float) ($slip->net_amount ?? 0) + $amount;
                        $slip->notes = $this->mergeNotes($slip->notes, $auditNote);
                        $slip->save();

                        $this->record->reimbursed_salary_slip_id = $slip->id;
                        $this->record->reimbursement_payment_method = 'salary_slip';
                        $this->record->reimbursement_notes = $this->mergeNotes(
                            $this->record->reimbursement_notes,
                            'Attached to Salary Slip #' . $slip->id . ' by ' . (auth()->user()?->name ?: 'System') . ' on ' . now()->format('Y-m-d H:i')
                        );
                        $this->record->save();
                    });

                    $this->notifyPortalAboutReimbursement(
                        'linked_salary_slip',
                        'Reimbursement Added to Salary Slip',
                        'Your reimbursement claim "' . ($this->record->fresh()->title ?: 'Claim') . '" has been added to your salary slip.'
                    );

                    Notification::make()
                        ->title('Reimbursement added to salary slip.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }


        if (
            $expense->paid_by === FinanceExpense::PAID_BY_CANDIDATE &&
            in_array($expense->reimbursement_status, [
                FinanceExpense::REIMBURSEMENT_APPROVED,
                FinanceExpense::REIMBURSEMENT_REJECTED,
                FinanceExpense::REIMBURSEMENT_PAID,
            ], true)
        ) {
            $actions[] = Action::make('backToPayment')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('finance_expenses', 'approve'))
                ->label('Back to Payment')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Back to Payment')
                ->modalDescription('This will return the reimbursement to Pending so it can be reviewed, attached to a salary slip, or processed again.')
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->record = $this->record->fresh();

                    $this->record->reimbursement_status = FinanceExpense::REIMBURSEMENT_PENDING;

                    if (\Illuminate\Support\Facades\Schema::hasColumn('finance_expenses', 'reimbursement_decision_by')) {
                        $this->record->reimbursement_decision_by = null;
                    }

                    if (\Illuminate\Support\Facades\Schema::hasColumn('finance_expenses', 'reimbursement_decision_at')) {
                        $this->record->reimbursement_decision_at = null;
                    }

                    if (\Illuminate\Support\Facades\Schema::hasColumn('finance_expenses', 'reimbursed_salary_slip_id')) {
                        $this->record->reimbursed_salary_slip_id = null;
                    }

                    if (\Illuminate\Support\Facades\Schema::hasColumn('finance_expenses', 'reimbursed_at')) {
                        $this->record->reimbursed_at = null;
                    }

                    $this->record->save();

                    Notification::make()
                        ->title('Reimbursement moved back to payment.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }


        if (
            $expense->paid_by === FinanceExpense::PAID_BY_CANDIDATE &&
            in_array($expense->reimbursement_status, [
                FinanceExpense::REIMBURSEMENT_APPROVED,
                FinanceExpense::REIMBURSEMENT_REJECTED,
            ], true) &&
            in_array($status, [FinanceExpense::STATUS_DRAFT, FinanceExpense::STATUS_APPROVED], true)
        ) {
            $actions[] = Action::make('backReimbursementToPending')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('finance_expenses', 'approve'))
                ->label('Back to Pending')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->modalHeading('Back Reimbursement to Pending')
                ->modalDescription('This will reopen the reimbursement decision and allow you to approve or reject it again.')
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->record = $this->record->fresh();

                    $payload = [
                        'reimbursement_status' => FinanceExpense::REIMBURSEMENT_PENDING,
                    ];

                    if (Schema::hasColumn('finance_expenses', 'reimbursement_decision_by')) {
                        $payload['reimbursement_decision_by'] = null;
                    }

                    if (Schema::hasColumn('finance_expenses', 'reimbursement_decision_at')) {
                        $payload['reimbursement_decision_at'] = null;
                    }

                    if (Schema::hasColumn('finance_expenses', 'reimbursement_notes')) {
                        $payload['reimbursement_notes'] = $this->mergeNotes(
                            $this->record->reimbursement_notes,
                            'Reimbursement moved back to Pending by ' . (auth()->user()?->name ?: 'ERP User') . ' at ' . now()->format('Y-m-d H:i')
                        );
                    }

                    $this->record->forceFill($payload)->save();

                    Notification::make()
                        ->title('Reimbursement moved back to pending.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }


        if (
            $expense->paid_by === FinanceExpense::PAID_BY_CANDIDATE &&
            $expense->reimbursement_status === FinanceExpense::REIMBURSEMENT_APPROVED &&
            blank($expense->reimbursed_salary_slip_id)
        ) {
            $actions[] = Action::make('linkReimbursementToSalarySlip')
                ->hidden(fn () => ! (bool) (
                    auth()->user()?->canErp('salary_slips', 'edit')
                    || auth()->user()?->canErp('salary_slips', 'create')
                    || auth()->user()?->canErp('finance_expenses', 'approve')
                ))
                ->label('Link to Salary Slip')
                ->icon('heroicon-o-document-currency-dollar')
                ->color('info')
                ->modalHeading('Link Reimbursement to Salary Slip')
                ->modalDescription('Select a not-paid salary slip. Treasury payment will happen later when the salary slip is paid.')
                ->modalSubmitActionLabel('Link to Salary Slip')
                ->extraAttributes($this->buttonAttrs('bank'))
                ->form([
                    Select::make('salary_slip_id')
                        ->label('Salary Slip')
                        ->options(fn () => $this->strictAvailableSalarySlipOptionsForReimbursement())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->required()
                        ->helperText('Only salary slips that are not paid are available. Paid slips cannot be selected.'),
                ])
                ->action(function (array $data): void {
                    $salarySlipId = (int) ($data['salary_slip_id'] ?? 0);

                    $salarySlip = SalarySlip::query()
                        ->whereKey($salarySlipId)
                        ->where('status', '!=', SalarySlip::STATUS_PAID)
                        ->first();

                    if (! $salarySlip) {
                        Notification::make()
                            ->title('Please select a valid not-paid salary slip.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $expense = $this->record->fresh(['preEmployment']);

                    if (! $this->salarySlipBelongsToExpenseOwner($salarySlip, $expense)) {
                        Notification::make()
                            ->title('Wrong salary slip. This salary slip belongs to another employee/candidate.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if (
                        (string) $expense->paid_by !== FinanceExpense::PAID_BY_CANDIDATE ||
                        (string) $expense->reimbursement_status !== FinanceExpense::REIMBURSEMENT_APPROVED
                    ) {
                        Notification::make()
                            ->title('Only approved candidate reimbursements can be linked to salary slips.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $expense->forceFill([
                        'reimbursed_salary_slip_id' => $salarySlip->id,
                        'reimbursement_payment_method' => 'salary_slip',
                    ])->saveQuietly();

                    $this->record = $expense->fresh();

                    $this->notifyPortalAboutReimbursement(
                        'linked_salary_slip',
                        'Reimbursement Linked to Salary Slip',
                        'Your reimbursement claim "' . ($this->record->title ?: 'Claim') . '" has been linked to your salary slip.'
                    );

                    Notification::make()
                        ->title('Reimbursement linked to salary slip.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }


        if ($status === FinanceExpense::STATUS_PAID) {
            if ($expense->treasury_transaction_id) {
                $actions[] = Action::make('viewTreasuryPosting')
                    ->hidden(fn () => ! (bool) (auth()->user()?->canErp('finance_expenses', 'view_treasury_posting') || auth()->user()?->canErp('treasury', 'view')))
                    ->label('Open Treasury Transaction')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('info')
                    ->url(fn (): string => \App\Filament\Resources\TreasuryTransactions\TreasuryTransactionResource::getUrl('view', [
                        'record' => $expense->treasury_transaction_id,
                    ]))
                    ->extraAttributes($this->buttonAttrs('bank'));
            }

            $actions[] = Action::make('reopenToApproved')
                ->hidden(fn () => ! (bool) (
                    auth()->user()?->canErp('finance_expenses', 'reopen')
                    || auth()->user()?->canErp('finance_expenses', 'approve')
                    || auth()->user()?->canErp('finance_expenses', 'mark_paid')
                    || auth()->user()?->canErp('finance_expenses', 'process_payment')
                ))
                ->label('Back to Approved')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Back to Approved')
                ->modalDescription('This will remove the treasury posting linked to this expense and move it back to Approved.')
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->record = $this->record->fresh();

                    // Defensive cleanup first while old IDs are still available.
                    $this->record->deleteTreasuryPosting();

                    $payload = [
                        'status' => FinanceExpense::STATUS_APPROVED,
                        'treasury_account_id' => null,
                    ];

                    if (Schema::hasColumn('finance_expenses', 'treasury_transaction_id')) {
                        $payload['treasury_transaction_id'] = null;
                    }

                    if (Schema::hasColumn('finance_expenses', 'treasury_operation_id')) {
                        $payload['treasury_operation_id'] = null;
                    }

                    $this->record->forceFill($payload)->saveQuietly();

                    $this->record = $this->record->fresh();

                    Notification::make()
                        ->title('Expense moved back to approved and treasury posting removed.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }

        if ($status === FinanceExpense::STATUS_CANCELLED) {
            $actions[] = Action::make('reopenCancelledToDraft')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('finance_expenses', 'reopen'))
                ->label('Reopen to Draft')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->record->status = FinanceExpense::STATUS_DRAFT;
                    $this->record->save();

                    Notification::make()
                        ->title('Expense reopened to draft.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }

        return $actions;
    }


    protected function strictAvailableSalarySlipOptionsForReimbursement(): array
    {
        $expense = $this->record->fresh(['employment', 'jobApplication', 'preEmployment']);

        $query = SalarySlip::query()
            ->with(['employment', 'jobApplication'])
            ->whereNotIn('status', [
                SalarySlip::STATUS_SENT_TO_BANK,
                SalarySlip::STATUS_PAID,
                SalarySlip::STATUS_LOCKED,
            ]);

        /*
         * FINAL HARD RULE:
         * A reimbursement can only be linked to salary slips belonging to the same person.
         * If the expense is for Wesam, Yamin salary slips must never appear.
         */
        if ($expense->employment_id) {
            $query->where('employment_id', $expense->employment_id);
        } elseif ($expense->job_application_id) {
            $query->where('job_application_id', $expense->job_application_id);
        } elseif ($expense->preEmployment?->job_application_id) {
            $query->where('job_application_id', $expense->preEmployment->job_application_id);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query
            ->latest('id')
            ->limit(80)
            ->get()
            ->mapWithKeys(function (SalarySlip $slip) {
                $person = $slip->employment?->employee_name
                    ?: $slip->jobApplication?->full_name
                    ?: $slip->jobApplication?->applicant_name
                    ?: 'Unknown';

                $status = SalarySlip::statusLabels()[$slip->status]
                    ?? ucfirst(str_replace('_', ' ', (string) $slip->status));

                $amount = number_format((float) ($slip->net_amount ?? 0), 2) . ' ' . ($slip->currency ?: '');

                $periodStart = $slip->period_start ? $slip->period_start->format('Y-m-d') : '-';
                $periodEnd = $slip->period_end ? $slip->period_end->format('Y-m-d') : '-';

                return [
                    $slip->id => 'Salary Slip #' . $slip->id
                        . ' — ' . $person
                        . ' — ' . $status
                        . ' — ' . $amount
                        . ' — ' . $periodStart . ' → ' . $periodEnd,
                ];
            })
            ->toArray();
    }

    protected function salarySlipBelongsToExpenseOwner(SalarySlip $salarySlip, FinanceExpense $expense): bool
    {
        if ($expense->employment_id && (int) $salarySlip->employment_id === (int) $expense->employment_id) {
            return true;
        }

        if ($expense->job_application_id && (int) $salarySlip->job_application_id === (int) $expense->job_application_id) {
            return true;
        }

        if ($expense->preEmployment?->job_application_id && (int) $salarySlip->job_application_id === (int) $expense->preEmployment->job_application_id) {
            return true;
        }

        return false;
    }


    protected function unpaidSalarySlipOptions(): array
    {
        $expense = $this->record->fresh();

        return SalarySlip::query()
            ->whereNotIn('status', [
                SalarySlip::STATUS_SENT_TO_BANK,
                SalarySlip::STATUS_PAID,
                SalarySlip::STATUS_LOCKED,
            ])
            ->when($expense->employment_id, fn ($query) => $query->where('employment_id', $expense->employment_id))
            ->when(! $expense->employment_id && $expense->job_application_id, fn ($query) => $query->where('job_application_id', $expense->job_application_id))
            ->orderByDesc('period_start')
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(function (SalarySlip $slip) {
                $period = trim(($slip->period_start?->format('Y-m-d') ?: '-') . ' → ' . ($slip->period_end?->format('Y-m-d') ?: '-'));
                $status = SalarySlip::statusLabels()[$slip->status] ?? ucfirst(str_replace('_', ' ', (string) $slip->status));
                $amount = number_format((float) ($slip->net_amount ?? 0), 2) . ' ' . ($slip->currency ?: 'USD');

                return [
                    $slip->id => 'Salary Slip #' . $slip->id . ' — ' . $period . ' — ' . $status . ' — Net: ' . $amount,
                ];
            })
            ->toArray();
    }



    public function backExpenseToApprovedDirect(): void
    {
        $expense = $this->record->fresh();

        if (! (
            auth()->user()?->canErp('finance_expenses', 'approve')
            || auth()->user()?->canErp('finance_expenses', 'reopen')
            || auth()->user()?->canErp('finance_expenses', 'mark_paid')
            || auth()->user()?->canErp('finance_expenses', 'process_payment')
            || auth()->user()?->canErp('finance_expenses', 'back_to_draft')
        )) {
            Notification::make()
                ->title('You do not have permission to move this expense back.')
                ->danger()
                ->send();

            return;
        }

        if ((string) $expense->status !== FinanceExpense::STATUS_PAID) {
            Notification::make()
                ->title('This expense is not paid.')
                ->warning()
                ->send();

            return;
        }

        /*
         * Move paid expense back to Approved.
         * Any treasury posting created by Mark as Paid is removed first.
         */
        $expense->deleteTreasuryPosting();

        $payload = [
            'status' => FinanceExpense::STATUS_APPROVED,
            'treasury_account_id' => null,
        ];

        if (Schema::hasColumn('finance_expenses', 'treasury_operation_id')) {
            $payload['treasury_operation_id'] = null;
        }

        if (Schema::hasColumn('finance_expenses', 'treasury_transaction_id')) {
            $payload['treasury_transaction_id'] = null;
        }

        $expense->forceFill($payload)->saveQuietly();

        $this->record = $expense->fresh();

        Notification::make()
            ->title('Expense moved back to Approved. Treasury posting removed.')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('view', [
            'record' => $this->record,
            'refresh' => now()->timestamp,
        ]));
    }


    public function approveReimbursementDirect(): void
    {
        $expense = $this->record->fresh();

        if (! (bool) auth()->user()?->canErp('finance_expenses', 'approve')) {
            Notification::make()
                ->title('You do not have permission to approve this reimbursement.')
                ->danger()
                ->send();

            return;
        }

        if ((string) $expense->paid_by !== FinanceExpense::PAID_BY_CANDIDATE) {
            Notification::make()
                ->title('Only candidate / employee reimbursements can use this workflow.')
                ->danger()
                ->send();

            return;
        }

        $expense->forceFill([
            'reimbursement_status' => FinanceExpense::REIMBURSEMENT_APPROVED,
            'reimbursement_decision_by' => auth()->id(),
            'reimbursement_decision_at' => now(),
            'reimbursement_required' => true,
        ])->saveQuietly();

        $this->record = $expense->fresh();

        $this->notifyPortalAboutReimbursement(
            'approved',
            'Reimbursement Approved',
            'Your reimbursement claim "' . ($this->record->title ?: 'Claim') . '" has been approved by Finance.'
        );

        Notification::make()
            ->title('Reimbursement approved. Link it to a salary slip next.')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('view', [
            'record' => $this->record,
            'refresh' => now()->timestamp,
        ]));
    }

    public function rejectReimbursementDirect(): void
    {
        $expense = $this->record->fresh();

        if (! (bool) auth()->user()?->canErp('finance_expenses', 'approve')) {
            Notification::make()
                ->title('You do not have permission to decline this reimbursement.')
                ->danger()
                ->send();

            return;
        }

        $expense->forceFill([
            'reimbursement_status' => FinanceExpense::REIMBURSEMENT_REJECTED,
            'reimbursement_decision_by' => auth()->id(),
            'reimbursement_decision_at' => now(),
            'reimbursed_salary_slip_id' => null,
            'reimbursed_at' => null,
            'reimbursement_payment_method' => null,
        ])->saveQuietly();

        $this->record = $expense->fresh();

        $this->notifyPortalAboutReimbursement(
            'rejected',
            'Reimbursement Rejected',
            'Your reimbursement claim "' . ($this->record->title ?: 'Claim') . '" has been rejected by Finance.'
        );

        Notification::make()
            ->title('Reimbursement declined.')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('view', [
            'record' => $this->record,
            'refresh' => now()->timestamp,
        ]));
    }

    public function backReimbursementToPendingDirect(): void
    {
        $expense = $this->record->fresh();

        if (! (bool) auth()->user()?->canErp('finance_expenses', 'approve')) {
            Notification::make()
                ->title('You do not have permission to move this reimbursement back.')
                ->danger()
                ->send();

            return;
        }

        $expense->forceFill([
            'reimbursement_status' => FinanceExpense::REIMBURSEMENT_PENDING,
            'reimbursement_decision_by' => null,
            'reimbursement_decision_at' => null,
            'reimbursed_salary_slip_id' => null,
            'reimbursed_at' => null,
            'reimbursement_payment_method' => null,
        ])->saveQuietly();

        $this->record = $expense->fresh();

        Notification::make()
            ->title('Reimbursement moved back to pending.')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('view', [
            'record' => $this->record,
            'refresh' => now()->timestamp,
        ]));
    }

    protected function cashSourceOptions(): array
    {
        $sources = TreasuryAccount::query()
            ->where('account_type', TreasuryAccount::TYPE_CASH)
            ->where('is_active', true)
            ->orderBy('institution_name')
            ->get()
            ->map(function (TreasuryAccount $account) {
                return filled($account->institution_name) ? $account->institution_name : 'Main Cash';
            })
            ->unique()
            ->values();

        return $sources
            ->mapWithKeys(fn ($value) => [$value => $value])
            ->toArray();
    }

    protected function bankAccountOptions(string $bankName = ''): array
    {
        return TreasuryAccount::query()
            ->where('account_type', TreasuryAccount::TYPE_BANK)
            ->where('is_active', true)
            ->when($bankName !== '', fn ($query) => $query->where('institution_name', $bankName))
            ->orderBy('account_name')
            ->get()
            ->mapWithKeys(function (TreasuryAccount $item) {
                $label = $item->account_name ?: 'Treasury Account';

                if ($item->currency) {
                    $label .= ' — ' . $item->currency;
                }

                return [$item->id => $label];
            })
            ->toArray();
    }

    protected function cashAccountOptions(string $cashSource = ''): array
    {
        return TreasuryAccount::query()
            ->where('account_type', TreasuryAccount::TYPE_CASH)
            ->where('is_active', true)
            ->when($cashSource !== '', function ($query) use ($cashSource) {
                if ($cashSource === 'Main Cash') {
                    $query->where(function ($sub) {
                        $sub->whereNull('institution_name')
                            ->orWhere('institution_name', '')
                            ->orWhere('institution_name', 'Main Cash');
                    });

                    return;
                }

                $query->where('institution_name', $cashSource);
            })
            ->orderBy('account_name')
            ->get()
            ->mapWithKeys(function (TreasuryAccount $item) {
                $label = $item->account_name ?: 'Cash Account';

                if ($item->currency) {
                    $label .= ' — ' . $item->currency;
                }

                return [$item->id => $label];
            })
            ->toArray();
    }

    protected function buildPaymentAuditNote(
        string $channel,
        ?string $sourceOrBank,
        ?string $accountName,
        ?string $userNotes,
    ): ?string {
        $parts = [
            'Payment Route: ' . $channel,
        ];

        if (filled($sourceOrBank)) {
            $parts[] = ($channel === 'Cash' ? 'Cash Source: ' : 'Bank: ') . $sourceOrBank;
        }

        if (filled($accountName)) {
            $parts[] = 'Account: ' . $accountName;
        }

        if (filled($userNotes)) {
            $parts[] = 'Notes: ' . trim((string) $userNotes);
        }

        return implode(' | ', $parts);
    }

    protected function mergeNotes(?string $existing, ?string $new): ?string
    {
        $existing = trim((string) $existing);
        $new = trim((string) $new);

        if ($new === '') {
            return $existing !== '' ? $existing : null;
        }

        if ($existing === '') {
            return $new;
        }

        return $existing . "\n\n" . $new;
    }

    protected function buttonAttrs(string $variant): array
    {
        return [
            'class' => match ($variant) {
                'edit' => 'sf-fe-btn sf-fe-btn--edit',
                'approve' => 'sf-fe-btn sf-fe-btn--approve',
                'paid' => 'sf-fe-btn sf-fe-btn--paid',
                'bank' => 'sf-fe-btn sf-fe-btn--bank',
                'partial' => 'sf-fe-btn sf-fe-btn--partial',
                'danger' => 'sf-fe-btn sf-fe-btn--danger',
                'reject' => 'sf-fe-btn sf-fe-btn--danger sf-fe-btn--reject',
                'back' => 'sf-fe-btn sf-fe-btn--back',
                default => 'sf-fe-btn',
            },
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }


    public function getExpenseHeaderData(): array
    {
        $expense = $this->record->fresh();

        $amount = number_format((float) ($expense->amount ?? 0), 2) . ' ' . ($expense->currency ?: 'USD');
        $claimAmount = number_format((float) ($expense->reimbursement_amount ?? $expense->amount ?? 0), 2) . ' ' . ($expense->reimbursement_currency ?: $expense->currency ?: 'USD');

        return [
            'title' => $expense->title ?: ('Finance Expense #' . $expense->id),
            'subtitle' => trim(($expense->ownerName() ?: 'Unknown Owner') . ' · ' . (FinanceExpense::scopeLabels()[$expense->expense_scope] ?? $expense->expense_scope ?: '-')),
            'amount' => $amount,
            'claimAmount' => $claimAmount,
            'status' => FinanceExpense::statusLabels()[$expense->status] ?? ($expense->status ?: '-'),
            'reimbursementStatus' => FinanceExpense::reimbursementLabels()[$expense->reimbursement_status] ?? ($expense->reimbursement_status ?: '-'),
            'paidBy' => FinanceExpense::paidByLabels()[$expense->paid_by] ?? ($expense->paid_by ?: '-'),
            'category' => FinanceExpense::categoryLabels()[$expense->category] ?? ($expense->category ?: '-'),
            'date' => $expense->expense_date?->format('Y-m-d') ?: '-',
            'owner' => $expense->ownerName() ?: '-',
            'isClaim' => (bool) ($expense->reimbursement_required ?? false),
            'portalSubmitted' => (bool) ($expense->candidate_submitted ?? false),
        ];
    }

    protected function sendReimbursementPortalNotification(string $event, ?string $note = null): void
    {
        try {
            if (! Schema::hasTable('notifications')) {
                return;
            }

            $expense = $this->record->fresh();

            $portalUser = $this->resolvePortalUserForExpense($expense);

            if (! $portalUser) {
                return;
            }

            $statusLabel = match ($event) {
                'approved' => 'approved',
                'rejected' => 'rejected',
                'paid' => 'marked as paid',
                default => 'updated',
            };

            $title = match ($event) {
                'approved' => 'Reimbursement claim approved',
                'rejected' => 'Reimbursement claim rejected',
                'paid' => 'Reimbursement claim paid',
                default => 'Reimbursement claim updated',
            };

            $message = 'Your reimbursement claim for ' . ($expense->title ?: 'expense #' . $expense->id) . ' has been ' . $statusLabel . '.';

            $amount = number_format((float) ($expense->reimbursement_amount ?? $expense->amount ?? 0), 2) . ' ' . ($expense->reimbursement_currency ?: $expense->currency ?: 'USD');

            $data = [
                'title' => $title,
                'message' => $message,
                'body' => $message,
                'amount' => $amount,
                'finance_expense_id' => $expense->id,
                'reimbursement_status' => $expense->reimbursement_status,
                'url' => '/portal/reimbursements',
                'action_url' => '/portal/reimbursements',
                'icon' => 'heroicon-o-banknotes',
                'color' => match ($event) {
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'paid' => 'success',
                    default => 'info',
                },
            ];

            if (filled($note)) {
                $data['note'] = $note;
            }

            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\PortalDatabaseNotification',
                'notifiable_type' => get_class($portalUser),
                'notifiable_id' => $portalUser->id,
                'data' => json_encode($data),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function resolvePortalUserForExpense(FinanceExpense $expense): ?User
    {
        $emails = [];

        if ($expense->relationLoaded('employment') || $expense->employment_id) {
            $email = $expense->employment?->employee_email;
            if (filled($email)) {
                $emails[] = strtolower(trim($email));
            }
        }

        if ($expense->relationLoaded('preEmployment') || $expense->pre_employment_id) {
            $email = $expense->preEmployment?->candidate_email;
            if (filled($email)) {
                $emails[] = strtolower(trim($email));
            }
        }

        if ($expense->relationLoaded('jobApplication') || $expense->job_application_id) {
            $email = $expense->jobApplication?->email ?? $expense->jobApplication?->candidate_email ?? null;
            if (filled($email)) {
                $emails[] = strtolower(trim($email));
            }
        }

        $emails = array_values(array_unique(array_filter($emails)));

        if (empty($emails)) {
            return null;
        }

        return User::query()
            ->whereIn('email', $emails)
            ->first();
    }



    protected function notifyPortalAboutReimbursement(
        string $event,
        ?string $title = null,
        ?string $message = null,
        bool $sendEmail = true
    ): void {
        try {
            $expense = $this->record->fresh(['employment', 'preEmployment']);

            app(PortalNotificationService::class)->notifyReimbursement(
                expense: $expense,
                event: $event,
                customTitle: $title,
                customMessage: $message,
                sendEmail: $sendEmail,
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }


}
