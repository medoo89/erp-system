<?php

namespace App\Filament\Resources\ClientInvoices\Pages;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use App\Models\ClientInvoice;
use App\Models\ClientInvoicePayment;
use App\Models\TreasuryAccount;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class ViewClientInvoice extends ViewRecord
{
    protected static string $resource = ClientInvoiceResource::class;

    protected string $view = 'filament.resources.client-invoices.pages.view-client-invoice-premium';

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return $this->record->invoice_number ?: ('Invoice #' . $this->record->id);
    }

    public function getSubheading(): string|Htmlable|null
    {
        $client = $this->record->client?->name ?: 'Unknown Client';
        $project = $this->record->project?->name ?: 'No Project';
        $status = ClientInvoice::statusOptions()[$this->record->status] ?? $this->record->status;

        return "Client: {$client} · Project: {$project} · Status: {$status}";
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        $this->record->refresh();
        $this->record->refreshPaymentStatus();
        $this->record = $this->record->fresh();

        $status = (string) $this->record->status;
        $foreignRemaining = round((float) $this->record->foreignRemainingAmount(), 2);
        $localRemaining = round((float) $this->record->localRemainingAmount(), 2);
        $hasForeignRemaining = $foreignRemaining > 0.01;
        $hasLocalRemaining = $localRemaining > 0.01;
        $isFullyPaid = ! $hasForeignRemaining && ! $hasLocalRemaining;

        $pendingReceiptsCount = $this->record->payments()
            ->where('settlement_status', ClientInvoicePayment::SETTLEMENT_PENDING)
            ->count();

        $actions = [
            Action::make('print')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'print'))
                ->label('Print Invoice')
                ->color('gray')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('client-invoices.print', ['clientInvoice' => $this->record]))
                ->openUrlInNewTab()
                ->extraAttributes($this->buttonAttrs('print')),


            Action::make('printForeignInvoice')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'print'))
                ->label('Print Foreign Invoice')
                ->color('info')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('client-invoices.print-document', [
                    'clientInvoice' => $this->record,
                    'documentType' => 'foreign',
                ]))
                ->openUrlInNewTab()
                ->extraAttributes($this->buttonAttrs('print')),

            Action::make('printLocalInvoice')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'print'))
                ->label('Print Local Invoice')
                ->color('success')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('client-invoices.print-document', [
                    'clientInvoice' => $this->record,
                    'documentType' => 'local',
                ]))
                ->openUrlInNewTab()
                ->extraAttributes($this->buttonAttrs('paid')),


            Action::make('generateTimesheetFromSalarySlips')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'edit'))
                ->label('Generate Timesheet')
                ->color('success')
                ->icon('heroicon-o-calendar-days')
                ->requiresConfirmation()
                ->modalHeading('Generate invoice timesheet from salary slips?')
                ->modalDescription('This will replace current invoice work days with days generated from salary slips linked to invoice lines.')
                ->modalSubmitActionLabel('Generate Timesheet')
                ->extraAttributes($this->buttonAttrs('paid'))
                ->action(function (): void {
                    $result = $this->record->generateTimesheetFromSalarySlips(true);

                    Notification::make()
                        ->title($result['message'] ?? 'Timesheet generated.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                }),

            Action::make('printTimesheet')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'print'))
                ->label('Print Timesheet')
                ->color('gray')
                ->icon('heroicon-o-document-chart-bar')
                ->url(fn () => route('client-invoices.print-timesheet', ['clientInvoice' => $this->record]))
                ->openUrlInNewTab()
                ->extraAttributes($this->buttonAttrs('print')),

            EditAction::make()
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'edit'))
                ->extraAttributes($this->buttonAttrs('edit')),
        ];

        if ($pendingReceiptsCount > 0) {
            $actions[] = Action::make('settlePendingReceipts')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'settle_receipts') || auth()->user()?->canErp('client_invoices', 'record_payment') || auth()->user()?->canErp('treasury', 'receive')))
                ->label('Settle Pending Receipts')
                ->color('success')
                ->icon('heroicon-o-building-library')
                ->modalHeading('Settle Pending Receipts To Invoice Bank')
                ->modalSubmitActionLabel('Confirm Settlement')
                ->extraAttributes($this->buttonAttrs('paid'))
                ->form([
                    Placeholder::make('pending_receipts_count')
                        ->label('Pending Receipts')
                        ->content(fn (): string => (string) $pendingReceiptsCount),

                    Placeholder::make('bank_profile_preview')
                        ->label('Invoice Bank Profile')
                        ->content(function (): string {
                            $profile = $this->record->bankProfile;

                            if (! $profile) {
                                return 'No bank profile linked to this invoice.';
                            }

                            return ($profile->profile_name ?: 'Bank Profile')
                                . ' — '
                                . ($profile->bank_name ?: 'Bank')
                                . ' — '
                                . ($profile->currency ?: '-');
                        }),

                    Placeholder::make('destination_account_preview')
                        ->label('Destination Treasury Account')
                        ->content(function (): string {
                            $profile = $this->record->bankProfile;
                            $account = $profile?->treasuryAccountForCurrency($this->record->foreign_currency ?: $this->record->display_currency);

                            if (! $account) {
                                return 'No treasury account linked to the invoice bank profile.';
                            }

                            return ($account->account_name ?: 'Treasury Account')
                                . ' — '
                                . ($account->currency ?: '-')
                                . ' — '
                                . ucfirst((string) ($account->account_type ?: ''));
                        }),

                    Textarea::make('notes')
                        ->label('Settlement Notes')
                        ->rows(3)
                        ->maxLength(1000),
                ])
                ->action(function (array $data): void {
                    $profile = $this->record->bankProfile;

                    if (! $profile) {
                        Notification::make()
                            ->title('This invoice does not have a bank profile.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $payments = $this->record->payments()
                        ->where('settlement_status', ClientInvoicePayment::SETTLEMENT_PENDING)
                        ->get();

                    foreach ($payments as $payment) {
                        $destinationAccount = $profile->treasuryAccountForCurrency($payment->currency)
                            ?: TreasuryAccount::query()
                                ->where('bank_profile_id', $profile->id)
                                ->where('is_active', true)
                                ->orderByDesc('is_default')
                                ->first();

                        if (! $destinationAccount) {
                            Notification::make()
                                ->title('No treasury account linked to this invoice bank profile.')
                                ->body('Please link a Treasury Account to Bank Profile: ' . ($profile->profile_name ?: '#' . $profile->id))
                                ->danger()
                                ->send();

                            return;
                        }

                        $payment->settleFromClearingToAccount(
                            (int) $destinationAccount->id,
                            now()->toDateString(),
                            $data['notes'] ?? null,
                        );
                    }

                    $this->record->refresh();
                    $this->record->refreshPaymentStatus();
                    $this->record = $this->record->fresh();

                    Notification::make()
                        ->title('Pending receipts settled successfully to invoice bank account.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }

        if ($status === ClientInvoice::STATUS_DRAFT) {
            $actions[] = Action::make('approve')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'approve'))
                ->label('Approve')
                ->color('info')
                ->icon('heroicon-o-check-badge')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('approve'))
                ->action(function (): void {
                    $this->updateStatus(ClientInvoice::STATUS_APPROVED, 'Invoice approved successfully.');
                });

            $actions[] = Action::make('cancel')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'cancel') || auth()->user()?->canErp('client_invoices', 'approve')))
                ->label('Cancel')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('danger'))
                ->action(function (): void {
                    $this->updateStatus(ClientInvoice::STATUS_CANCELLED, 'Invoice cancelled.');
                });
        }

        if ($status === ClientInvoice::STATUS_APPROVED) {
            $actions[] = Action::make('sendToClient')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('client_invoices', 'send_to_client'))
                ->label('Send to Client')
                ->color('warning')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('bank'))
                ->action(function (): void {
                    $this->updateStatus(ClientInvoice::STATUS_SENT_TO_CLIENT, 'Invoice marked as sent to client.');
                });

            $actions[] = Action::make('cancelFromApproved')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'cancel') || auth()->user()?->canErp('client_invoices', 'approve')))
                ->label('Cancel')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->extraAttributes($this->buttonAttrs('danger'))
                ->action(function (): void {
                    $this->updateStatus(ClientInvoice::STATUS_CANCELLED, 'Invoice cancelled.');
                });
        }

        if (in_array($status, [ClientInvoice::STATUS_APPROVED, ClientInvoice::STATUS_SENT_TO_CLIENT, ClientInvoice::STATUS_PARTIALLY_PAID], true) && ! $isFullyPaid) {
            $actions[] = Action::make('receivePartialPayment')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'record_payment') || auth()->user()?->canErp('treasury', 'receive')))
                ->label('Receive Partial Payment')
                ->color('warning')
                ->icon('heroicon-o-banknotes')
                ->modalHeading('Receive Partial Payment')
                ->modalSubmitActionLabel('Post Partial Receipt')
                ->extraAttributes($this->buttonAttrs('partial'))
                ->form([
                    Radio::make('receipt_flow')
                        ->label('Receipt Route')
                        ->options([
                            'clearing' => 'Clearing',
                            'bank' => 'Bank',
                            'cash' => 'Cash',
                        ])
                        ->default('bank')
                        ->inline()
                        ->live()
                        ->required(),

                    Select::make('applies_to')
                        ->label('Apply To')
                        ->options(ClientInvoicePayment::appliesToOptions())
                        ->default(
                            $hasForeignRemaining
                                ? ClientInvoicePayment::APPLIES_TO_FOREIGN
                                : ClientInvoicePayment::APPLIES_TO_LOCAL
                        )
                        ->required()
                        ->native(false)
                        ->live(),

                    Placeholder::make('portion_due_preview')
                        ->label('Selected Portion Due')
                        ->content(function (callable $get): string {
                            $appliesTo = (string) ($get('applies_to') ?? ClientInvoicePayment::APPLIES_TO_FOREIGN);

                            if ($appliesTo === ClientInvoicePayment::APPLIES_TO_LOCAL) {
                                return $this->formatMoney(
                                    (float) $this->record->localRemainingAmount(),
                                    (string) ($this->record->local_currency ?: '-')
                                );
                            }

                            return $this->formatMoney(
                                (float) $this->record->foreignRemainingAmount(),
                                (string) ($this->record->foreign_currency ?: '-')
                            );
                        }),

                    TextInput::make('amount')
                        ->label('Received Amount')
                        ->numeric()
                        ->required(),

                    Select::make('currency')
                        ->label('Receipt Currency')
                        ->options(ClientInvoicePayment::currencyOptions())
                        ->default(function (callable $get) {
                            $appliesTo = (string) ($get('applies_to') ?? ClientInvoicePayment::APPLIES_TO_FOREIGN);

                            return $appliesTo === ClientInvoicePayment::APPLIES_TO_LOCAL
                                ? ($this->record->local_currency ?: 'LYD')
                                : ($this->record->foreign_currency ?: 'USD');
                        })
                        ->required()
                        ->native(false)
                        ->live(),

                    TextInput::make('exchange_rate')
                        ->label('Exchange Rate')
                        ->numeric(),

                    Select::make('treasury_account_id')
                        ->label('Treasury Account')
                        ->options(function (callable $get) {
                            $route = (string) ($get('receipt_flow') ?? 'bank');
                            $currency = (string) ($get('currency') ?? ($this->record->foreign_currency ?: 'USD'));

                            return $this->treasuryAccountOptionsForRouteAndCurrency($route, $currency);
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),

                    DatePicker::make('payment_date')
                        ->label('Payment Date')
                        ->default(now()->toDateString())
                        ->required(),

                    TextInput::make('reference_no')
                        ->label('Reference No')
                        ->maxLength(255),

                    Textarea::make('notes')
                        ->label('Payment Notes')
                        ->rows(4),
                ])
                ->action(function (array $data): void {
                    $receiptFlow = (string) ($data['receipt_flow'] ?? 'bank');
                    $settlementStatus = $receiptFlow === 'clearing'
                        ? ClientInvoicePayment::SETTLEMENT_PENDING
                        : ClientInvoicePayment::SETTLEMENT_CLEARED;

                    $treasuryAccountId = $data['treasury_account_id'] ?? null;

                    if (! $treasuryAccountId) {
                        Notification::make()
                            ->title('Please choose a treasury account.')
                            ->danger()
                            ->send();

                        return;
                    }

                    ClientInvoicePayment::create([
                        'client_invoice_id' => $this->record->id,
                        'treasury_account_id' => $treasuryAccountId,
                        'treasury_operation_id' => null,
                        'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                        'amount' => $data['amount'] ?? 0,
                        'currency' => $data['currency'] ?? ($this->record->foreign_currency ?: 'USD'),
                        'applies_to' => $data['applies_to'] ?? ClientInvoicePayment::APPLIES_TO_FOREIGN,
                        'exchange_rate' => $data['exchange_rate'] ?? null,
                        'reference_no' => $data['reference_no'] ?? null,
                        'settlement_status' => $settlementStatus,
                        'notes' => trim(implode("\n", array_filter([
                            match ($receiptFlow) {
                                'clearing' => 'Created from partial payment action (received to clearing).',
                                'bank' => 'Created from partial payment action (received direct to bank).',
                                'cash' => 'Created from partial payment action (received direct to cash).',
                                default => 'Created from partial payment action.',
                            },
                            $data['notes'] ?? null,
                        ]))),
                    ]);

                    $this->record->refresh();
                    $this->record->refreshPaymentStatus();
                    $this->record = $this->record->fresh();

                    Notification::make()
                        ->title('Partial receipt posted successfully.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });

            $actions[] = Action::make('receiveFullPayment')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'record_payment') || auth()->user()?->canErp('treasury', 'receive')))
                ->label('Receive Full Payment')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->modalHeading('Receive Full Payment')
                ->modalSubmitActionLabel('Post Full Receipt')
                ->extraAttributes($this->buttonAttrs('paid'))
                ->form([
                    Radio::make('receipt_flow')
                        ->label('Receipt Route')
                        ->options([
                            'clearing' => 'Clearing',
                            'bank' => 'Bank',
                            'cash' => 'Cash',
                        ])
                        ->default('bank')
                        ->inline()
                        ->live()
                        ->required(),

                    Placeholder::make('foreign_due_preview')
                        ->label('Foreign Due')
                        ->content(fn () => $this->formatMoney(
                            (float) $this->record->foreignRemainingAmount(),
                            (string) ($this->record->foreign_currency ?: '-')
                        ))
                        ->visible(fn (): bool => (float) $this->record->foreignRemainingAmount() > 0.01),

                    Select::make('foreign_treasury_account_id')
                        ->label('Foreign Treasury Account')
                        ->options(function (callable $get) {
                            return $this->treasuryAccountOptionsForRouteAndCurrency(
                                (string) ($get('receipt_flow') ?? 'bank'),
                                (string) ($this->record->foreign_currency ?: 'USD')
                            );
                        })
                        ->searchable()
                        ->preload()
                        ->required(fn (): bool => (float) $this->record->foreignRemainingAmount() > 0.01)
                        ->visible(fn (): bool => (float) $this->record->foreignRemainingAmount() > 0.01)
                        ->native(false),

                    Placeholder::make('local_due_preview')
                        ->label('Local Due')
                        ->content(fn () => $this->formatMoney(
                            (float) $this->record->localRemainingAmount(),
                            (string) ($this->record->local_currency ?: '-')
                        ))
                        ->visible(fn (): bool => (float) $this->record->localRemainingAmount() > 0.01),

                    Select::make('local_treasury_account_id')
                        ->label('Local Treasury Account')
                        ->options(function (callable $get) {
                            return $this->treasuryAccountOptionsForRouteAndCurrency(
                                (string) ($get('receipt_flow') ?? 'bank'),
                                (string) ($this->record->local_currency ?: 'LYD')
                            );
                        })
                        ->searchable()
                        ->preload()
                        ->required(fn (): bool => (float) $this->record->localRemainingAmount() > 0.01)
                        ->visible(fn (): bool => (float) $this->record->localRemainingAmount() > 0.01)
                        ->native(false),

                    DatePicker::make('payment_date')
                        ->label('Payment Date')
                        ->default(now()->toDateString())
                        ->required(),

                    TextInput::make('reference_no')
                        ->label('Reference No')
                        ->maxLength(255),

                    Textarea::make('notes')
                        ->label('Payment Notes')
                        ->rows(4),
                ])
                ->action(function (array $data): void {
                    $receiptFlow = (string) ($data['receipt_flow'] ?? 'bank');
                    $settlementStatus = $receiptFlow === 'clearing'
                        ? ClientInvoicePayment::SETTLEMENT_PENDING
                        : ClientInvoicePayment::SETTLEMENT_CLEARED;

                    $foreignRemainingNow = round((float) $this->record->fresh()->foreignRemainingAmount(), 2);
                    $localRemainingNow = round((float) $this->record->fresh()->localRemainingAmount(), 2);

                    if ($foreignRemainingNow > 0.01 && empty($data['foreign_treasury_account_id'])) {
                        Notification::make()
                            ->title('Foreign treasury account is required.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($localRemainingNow > 0.01 && empty($data['local_treasury_account_id'])) {
                        Notification::make()
                            ->title('Local treasury account is required.')
                            ->danger()
                            ->send();

                        return;
                    }

                    if ($foreignRemainingNow <= 0.01 && $localRemainingNow <= 0.01) {
                        Notification::make()
                            ->title('This invoice is already fully settled.')
                            ->warning()
                            ->send();

                        return;
                    }

                    if ($foreignRemainingNow > 0.01) {
                        ClientInvoicePayment::create([
                            'client_invoice_id' => $this->record->id,
                            'treasury_account_id' => $data['foreign_treasury_account_id'],
                            'treasury_operation_id' => null,
                            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                            'amount' => $foreignRemainingNow,
                            'currency' => $this->record->foreign_currency ?: 'USD',
                            'applies_to' => ClientInvoicePayment::APPLIES_TO_FOREIGN,
                            'exchange_rate' => null,
                            'reference_no' => $data['reference_no'] ?? null,
                            'settlement_status' => $settlementStatus,
                            'notes' => trim(implode("\n", array_filter([
                                match ($receiptFlow) {
                                    'clearing' => 'Created from full payment action (foreign portion to clearing).',
                                    'bank' => 'Created from full payment action (foreign portion direct to bank).',
                                    'cash' => 'Created from full payment action (foreign portion direct to cash).',
                                    default => 'Created from full payment action (foreign portion).',
                                },
                                $data['notes'] ?? null,
                            ]))),
                        ]);
                    }

                    if ($localRemainingNow > 0.01) {
                        ClientInvoicePayment::create([
                            'client_invoice_id' => $this->record->id,
                            'treasury_account_id' => $data['local_treasury_account_id'],
                            'treasury_operation_id' => null,
                            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                            'amount' => $localRemainingNow,
                            'currency' => $this->record->local_currency ?: 'LYD',
                            'applies_to' => ClientInvoicePayment::APPLIES_TO_LOCAL,
                            'exchange_rate' => null,
                            'reference_no' => $data['reference_no'] ?? null,
                            'settlement_status' => $settlementStatus,
                            'notes' => trim(implode("\n", array_filter([
                                match ($receiptFlow) {
                                    'clearing' => 'Created from full payment action (local portion to clearing).',
                                    'bank' => 'Created from full payment action (local portion direct to bank).',
                                    'cash' => 'Created from full payment action (local portion direct to cash).',
                                    default => 'Created from full payment action (local portion).',
                                },
                                $data['notes'] ?? null,
                            ]))),
                        ]);
                    }

                    $this->record->refresh();
                    $this->record->refreshPaymentStatus();
                    $this->record = $this->record->fresh();

                    Notification::make()
                        ->title('Full receipt posted successfully.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('view', [
                        'record' => $this->record,
                        'refresh' => now()->timestamp,
                    ]));
                });
        }

        if ($status === ClientInvoice::STATUS_SENT_TO_CLIENT) {
            $actions[] = Action::make('backToApprovedFromSent')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'back_to_approved') || auth()->user()?->canErp('client_invoices', 'approve')))
                ->label('Back to Approved')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Back to Approved')
                ->modalDescription('This will move the invoice back to Approved.')
                ->modalSubmitActionLabel('Confirm Back to Approved')
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->moveInvoiceBackAndClearReceipts(ClientInvoice::STATUS_APPROVED, false);
                });
        }

        if (in_array($status, [ClientInvoice::STATUS_PARTIALLY_PAID, ClientInvoice::STATUS_PAID], true)) {
            $actions[] = Action::make('backToSentAndClearReceipts')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'back_to_approved') || auth()->user()?->canErp('client_invoices', 'approve')))
                ->label('Back to Sent')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Back to Sent to Client')
                ->modalDescription('This will DELETE all receipt/payment records, treasury receipt transactions, and settlement operations. Global Finance Totals will no longer count this invoice as collected.')
                ->modalSubmitActionLabel('Confirm Back to Sent')
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->moveInvoiceBackAndClearReceipts(ClientInvoice::STATUS_SENT_TO_CLIENT, true);
                });

            $actions[] = Action::make('backToApprovedAndClearReceipts')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'back_to_approved') || auth()->user()?->canErp('client_invoices', 'approve')))
                ->label('Back to Approved')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Back to Approved')
                ->modalDescription('This will DELETE all receipt/payment records, treasury receipt transactions, and settlement operations. Global Finance Totals will no longer count this invoice as collected.')
                ->modalSubmitActionLabel('Confirm Back to Approved')
                ->extraAttributes($this->buttonAttrs('back'))
                ->action(function (): void {
                    $this->moveInvoiceBackAndClearReceipts(ClientInvoice::STATUS_APPROVED, true);
                });
        }


        if (! in_array($status, [ClientInvoice::STATUS_DRAFT, ClientInvoice::STATUS_CANCELLED], true)) {
            $actions[] = Action::make('backToDraft')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('client_invoices', 'edit') || auth()->user()?->canErp('client_invoices', 'approve')))
                ->label('Back to Draft')
                ->color('gray')
                ->icon('heroicon-o-arrow-uturn-left')
                ->requiresConfirmation()
                ->modalHeading('Return invoice to Draft?')
                ->modalDescription('This will only return the invoice workflow status to Draft. Existing receipt/payment records will stay visible for audit.')
                ->modalSubmitActionLabel('Back to Draft')
                ->extraAttributes($this->buttonAttrs('print'))
                ->action(function (): void {
                    $this->record->update([
                        'status' => ClientInvoice::STATUS_DRAFT,
                    ]);

                    Notification::make()
                        ->title('Invoice returned to Draft.')
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

    protected function moveInvoiceBackAndClearReceipts(string $targetStatus, bool $deleteReceipts = true): void
    {
        DB::transaction(function () use ($targetStatus, $deleteReceipts): void {
            $invoice = ClientInvoice::query()
                ->with('payments')
                ->lockForUpdate()
                ->findOrFail($this->record->id);

            $paymentIds = $invoice->payments()->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

            if ($deleteReceipts && count($paymentIds) > 0) {
                foreach ($invoice->payments()->get() as $payment) {
                    $payment->delete();
                }

                // Hard cleanup for any orphan records left by previous broken versions.
                \App\Models\TreasuryTransaction::query()
                    ->where(function ($query) use ($paymentIds) {
                        $query->where(function ($sub) use ($paymentIds) {
                            $sub->where('reference_type', 'invoice_payment')
                                ->whereIn('reference_id', $paymentIds);
                        })->orWhere(function ($sub) use ($paymentIds) {
                            $sub->where('reference_type', 'client_invoice_payment_settlement')
                                ->whereIn('reference_id', $paymentIds);
                        });
                    })
                    ->delete();

                \App\Models\TreasuryOperation::query()
                    ->where('reference_type', 'client_invoice_payment_settlement')
                    ->whereIn('reference_id', $paymentIds)
                    ->delete();
            }

            $invoice->updateQuietly([
                'status' => $targetStatus,
            ]);

            $invoice->refresh();
            $this->record = $invoice;
        });

        Notification::make()
            ->title($deleteReceipts ? 'Invoice moved back and all receipt records were deleted.' : 'Invoice moved back successfully.')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('view', [
            'record' => $this->record,
            'refresh' => now()->timestamp,
        ]));
    }

    protected function updateStatus(string $status, string $message): void
    {
        $this->record->update([
            'status' => $status,
        ]);

        $this->record->refresh();
        $this->record->refreshPaymentStatus();
        $this->record = $this->record->fresh();

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('view', [
            'record' => $this->record,
            'refresh' => now()->timestamp,
        ]));
    }

    protected function cashTreasuryAccountOptions(): array
    {
        return $this->treasuryAccountOptionsByTypeAndCurrency(TreasuryAccount::TYPE_CASH, null);
    }

    protected function bankTreasuryAccountOptions(?string $currency = null): array
    {
        return $this->treasuryAccountOptionsByTypeAndCurrency(TreasuryAccount::TYPE_BANK, $currency);
    }

    protected function clearingTreasuryAccountOptions(?string $currency = null): array
    {
        return $this->treasuryAccountOptionsByTypeAndCurrency(TreasuryAccount::TYPE_CLEARING, $currency);
    }

    protected function treasuryAccountOptionsForRouteAndCurrency(?string $route, ?string $currency = null): array
    {
        return match ((string) $route) {
            'cash' => $this->treasuryAccountOptionsByTypeAndCurrency(TreasuryAccount::TYPE_CASH, $currency),
            'clearing' => $this->treasuryAccountOptionsByTypeAndCurrency(TreasuryAccount::TYPE_CLEARING, $currency),
            'bank' => $this->treasuryAccountOptionsByTypeAndCurrency(TreasuryAccount::TYPE_BANK, $currency),
            default => [],
        };
    }

    protected function treasuryAccountOptionsByTypeAndCurrency(?string $type = null, ?string $currency = null): array
    {
        $query = TreasuryAccount::query()
            ->where('is_active', true)
            ->orderBy('account_name');

        if ($type) {
            $query->where('account_type', $type);
        }

        if (filled($currency)) {
            $query->where('currency', strtoupper((string) $currency));
        }

        return $query->get()
            ->mapWithKeys(function ($item) {
                $label = $item->account_name ?: 'Treasury Account';

                if ($item->institution_name) {
                    $label .= ' — ' . $item->institution_name;
                }

                if ($item->currency) {
                    $label .= ' — ' . strtoupper((string) $item->currency);
                }

                return [$item->id => $label];
            })
            ->toArray();
    }

    protected function formatMoney(float $amount, string $currency): string
    {
        return number_format($amount, 2) . ' ' . strtoupper($currency);
    }






    public function backToDraftInvoice(): void
    {
        DB::transaction(function (): void {
            $invoice = $this->record->fresh();

            /*
            |--------------------------------------------------------------------------
            | Back to Draft reset
            |--------------------------------------------------------------------------
            | Returning an invoice to Draft means finance wants to re-process it.
            | Existing receipts/payments must be removed from this invoice so the
            | invoice becomes unpaid again and the payment workflow buttons appear
            | after approval. Treasury rows created by those receipts are also removed
            | when their IDs are stored on the payment rows.
            */
            $payments = $invoice->payments()->get();

            $treasuryOperationIds = [];
            $treasuryTransactionIds = [];

            foreach ($payments as $payment) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('client_invoice_payments', 'treasury_operation_id') && filled($payment->treasury_operation_id ?? null)) {
                    $treasuryOperationIds[] = (int) $payment->treasury_operation_id;
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('client_invoice_payments', 'treasury_transaction_id') && filled($payment->treasury_transaction_id ?? null)) {
                    $treasuryTransactionIds[] = (int) $payment->treasury_transaction_id;
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('client_invoice_payments', 'settlement_treasury_operation_id') && filled($payment->settlement_treasury_operation_id ?? null)) {
                    $treasuryOperationIds[] = (int) $payment->settlement_treasury_operation_id;
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('client_invoice_payments', 'settlement_treasury_transaction_id') && filled($payment->settlement_treasury_transaction_id ?? null)) {
                    $treasuryTransactionIds[] = (int) $payment->settlement_treasury_transaction_id;
                }
            }

            $invoice->payments()->delete();

            $treasuryOperationIds = array_values(array_unique(array_filter($treasuryOperationIds)));
            $treasuryTransactionIds = array_values(array_unique(array_filter($treasuryTransactionIds)));

            if (! empty($treasuryTransactionIds) && \Illuminate\Support\Facades\Schema::hasTable('treasury_transactions')) {
                \App\Models\TreasuryTransaction::query()
                    ->whereIn('id', $treasuryTransactionIds)
                    ->delete();
            }

            if (! empty($treasuryOperationIds) && \Illuminate\Support\Facades\Schema::hasTable('treasury_operations')) {
                \App\Models\TreasuryOperation::query()
                    ->whereIn('id', $treasuryOperationIds)
                    ->delete();
            }

            $update = [
                'status' => ClientInvoice::STATUS_DRAFT,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('client_invoices', 'treasury_operation_id')) {
                $update['treasury_operation_id'] = null;
            }

            $invoice->update($update);

            $this->record = $invoice->fresh();
        });

        Notification::make()
            ->title('Invoice returned to Draft.')
            ->body('Receipt/payment records were cleared so the invoice can be processed again.')
            ->success()
            ->send();

        $this->redirect(static::getResource()::getUrl('view', [
            'record' => $this->record,
            'refresh' => now()->timestamp,
        ]));
    }


    public function approveInvoiceQuick(): void
    {
        $this->updateStatus(ClientInvoice::STATUS_APPROVED, 'Invoice approved successfully.');
    }

    public function cancelInvoiceQuick(): void
    {
        $this->updateStatus(ClientInvoice::STATUS_CANCELLED, 'Invoice cancelled.');
    }

    public function sendInvoiceToClientQuick(): void
    {
        $this->updateStatus(ClientInvoice::STATUS_SENT_TO_CLIENT, 'Invoice marked as sent to client.');
    }


    protected function buttonAttrs(string $type): array
    {
        $map = [
            'print' => ['#475569', '#64748b', '#ffffff'],
            'edit' => ['#00b9b0', '#1cd3c6', '#ffffff'],
            'approve' => ['#2563eb', '#4f8cff', '#ffffff'],
            'bank' => ['#f59e0b', '#facc15', '#1f2937'],
            'cash' => ['#10b981', '#65d46e', '#ffffff'],
            'paid' => ['#059669', '#34d399', '#ffffff'],
            'partial' => ['#a855f7', '#d946ef', '#ffffff'],
            'danger' => ['#b91c1c', '#ef4444', '#ffffff'],
            'back' => ['#64748b', '#94a3b8', '#ffffff'],
        ];

        [$a, $b, $text] = $map[$type] ?? ['#475569', '#64748b', '#ffffff'];

        return [
            'style' => implode(' ', [
                "background: linear-gradient(90deg, {$a}, {$b});",
                "color: {$text} !important;",
                'border: 0;',
                'border-radius: 999px;',
                'padding: 14px 22px;',
                'min-height: 52px;',
                'font-weight: 800;',
                'font-size: 15px;',
                'box-shadow: 0 10px 22px rgba(15,23,42,.10);',
                'transition: all .22s ease;',
            ]),
            'onmouseover' => "this.style.transform='translateY(-2px) scale(1.02)'; this.style.boxShadow='0 14px 28px rgba(15,23,42,.16)'",
            'onmouseout' => "this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 10px 22px rgba(15,23,42,.10)'",
            'onmousedown' => "this.style.transform='scale(.98)'",
            'onmouseup' => "this.style.transform='translateY(-2px) scale(1.02)'",
        ];
    }


    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'view') ?? false);
    }

}
