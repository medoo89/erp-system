<?php

namespace App\Filament\Resources\ClientInvoices\Pages;

use Filament\Notifications\Notification;

use Filament\Forms\Components\ToggleButtons;

use Filament\Forms\Components\Toggle;

use Filament\Forms\Components\TextInput;

use Filament\Forms\Components\Textarea;

use Filament\Forms\Components\Repeater;

use Filament\Forms\Components\DatePicker;

use Filament\Actions\Action;

use App\Models\ClientInvoiceWorkDay;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditClientInvoice extends EditRecord
{
    protected string $view = 'filament.resources.client-invoices.pages.edit-client-invoice-premium';

    protected static string $resource = ClientInvoiceResource::class;





    public function getSubheading(): string|Htmlable|null
    {
        $client = $this->record->client?->name ?: 'Unknown Client';
        $project = $this->record->project?->name ?: 'No Project';

        return "Client: {$client} · Project: {$project}";
    }



    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('client_invoices', 'edit') ?? false);
    }


    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToInvoice')
                ->label('Back')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->record])),

            Action::make('viewInvoice')
                ->label('View Invoice')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn (): string => static::getResource()::getUrl('view', ['record' => $this->record])),

            Action::make('manageTimesheet')
                ->label('Manage Timesheet')
                ->icon('heroicon-o-calendar-days')
                ->color('warning')
                ->modalWidth('7xl')
                ->modalHeading('Manage Invoice Timesheet')
                ->modalSubmitActionLabel('Save Timesheet')
                ->fillForm(function (): array {
                    return [
                        'work_days' => $this->record->workDays()
                            ->orderBy('work_date')
                            ->get()
                            ->map(fn ($day): array => [
                                'work_date' => optional($day->work_date)->format('Y-m-d'),
                                'day_status' => $day->day_status ?: ClientInvoiceWorkDay::STATUS_PAID,
                                'is_billable' => (bool) $day->is_billable,
                                'billable_units' => (float) ($day->billable_units ?? 0),
                                'notes' => $day->notes,
                            ])
                            ->values()
                            ->toArray(),
                    ];
                })
                ->form([
                    Repeater::make('work_days')
                        ->label('Work Days')
                        ->schema([
                            DatePicker::make('work_date')
                                ->label('Work Date')
                                ->native(false)
                                ->required(),

                            ToggleButtons::make('day_status')
                                ->label('Day Status')
                                ->options(ClientInvoiceWorkDay::statusOptions())
                                ->inline()
                                ->default(ClientInvoiceWorkDay::STATUS_PAID)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, $set): void {
                                    if ($state === ClientInvoiceWorkDay::STATUS_PAID) {
                                        $set('is_billable', true);
                                        $set('billable_units', 1);

                                        return;
                                    }

                                    $set('is_billable', false);
                                    $set('billable_units', 0);
                                }),

                            Toggle::make('is_billable')
                                ->label('Billable')
                                ->default(true),

                            TextInput::make('billable_units')
                                ->label('Units')
                                ->numeric()
                                ->default(1),

                            Textarea::make('notes')
                                ->label('Notes')
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $this->record->workDays()->delete();

                    foreach (($data['work_days'] ?? []) as $row) {
                        if (blank($row['work_date'] ?? null)) {
                            continue;
                        }

                        $status = $row['day_status'] ?? ClientInvoiceWorkDay::STATUS_PAID;

                        $this->record->workDays()->create([
                            'work_date' => $row['work_date'],
                            'day_status' => $status,
                            'is_billable' => (bool) ($row['is_billable'] ?? $status === ClientInvoiceWorkDay::STATUS_PAID),
                            'billable_units' => (float) ($row['billable_units'] ?? ($status === ClientInvoiceWorkDay::STATUS_PAID ? 1 : 0)),
                            'notes' => $row['notes'] ?? null,
                        ]);
                    }

                    if (method_exists($this->record, 'syncInvoiceTotalsFromLines')) {
                        $this->record->syncInvoiceTotalsFromLines();
                    }

                    if (method_exists($this->record, 'recalculateContractTaxAllocationQuietly')) {
                        $this->record->recalculateContractTaxAllocationQuietly();
                    }

                    if (method_exists($this->record, 'syncSplitDocuments')) {
                        $this->record->syncSplitDocuments();
                    }

                    Notification::make()
                        ->title('Timesheet updated successfully.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record]));
                }),

            Action::make('generateTimesheetFromSalarySlips')
                ->label('Generate From Salary Slips')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate timesheet from salary slips?')
                ->modalDescription('This will replace current invoice work days with days generated from salary slips linked to invoice lines.')
                ->modalSubmitActionLabel('Generate Timesheet')
                ->action(function (): void {
                    $result = method_exists($this->record, 'generateTimesheetFromSalarySlips')
                        ? $this->record->generateTimesheetFromSalarySlips(true)
                        : ['message' => 'Generate method is not available.'];

                    Notification::make()
                        ->title($result['message'] ?? 'Timesheet generated.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('edit', ['record' => $this->record]));
                }),

            DeleteAction::make(),
        ];
    }

}
