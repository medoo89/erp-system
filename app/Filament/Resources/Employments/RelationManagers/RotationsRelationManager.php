<?php

namespace App\Filament\Resources\Employments\RelationManagers;

use App\Services\PortalNotificationService;
use App\Models\FinanceExpense;
use App\Services\SalarySlipGenerationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class RotationsRelationManager extends RelationManager
{
    protected static string $relationship = 'rotations';

    protected static ?string $title = 'Rotation History';

    protected static ?string $modelLabel = 'Rotation';

    protected static ?string $pluralModelLabel = 'Rotation History';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('rotation_label')
                    ->label('Rotation Label')
                    ->maxLength(255)
                    ->placeholder('Example: Rotation 01 / April Offshore'),

                Select::make('status')
                    ->label('Rotation Status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'paused' => 'Paused',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                TextInput::make('rotation_pattern')
                    ->label('Rotation Pattern')
                    ->maxLength(255)
                    ->placeholder('28/28, 35/35 ...'),

                Select::make('travel_status')
                    ->label('Travel Status')
                    ->options([
                        'pending_request' => 'Pending Request',
                        'request_received' => 'Request Received',
                        'ticket_booked' => 'Ticket Booked',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->native(false),

                FileUpload::make('travel_request_file_path')
                    ->label('Travel Request File')
                    ->disk('public')
                    ->directory(fn () => 'employment-rotations/' . ($this->ownerRecord?->id ?? 'draft') . '/travel-requests')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/webp',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                    ])
                    ->maxSize(20480)
                    ->helperText('Allowed: PDF, Images, Word, Excel, CSV only.'),

                FileUpload::make('ticket_file_path')
                    ->label('Ticket File')
                    ->disk('public')
                    ->directory(fn () => 'employment-rotations/' . ($this->ownerRecord?->id ?? 'draft') . '/tickets')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/jpeg',
                        'image/jpg',
                        'image/png',
                        'image/webp',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                    ])
                    ->maxSize(20480)
                    ->helperText('Allowed: PDF, Images, Word, Excel, CSV only.'),

                DatePicker::make('from_date')
                    ->label('From Date'),

                DatePicker::make('to_date')
                    ->label('To Date'),

                DatePicker::make('mobilization_date')
                    ->label('Mobilization Date'),

                DatePicker::make('demobilization_date')
                    ->label('Demobilization Date'),

                Toggle::make('is_current')
                    ->label('Mark as Current Rotation')
                    ->default(false),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(4)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('rotation_label')
            ->columns([
                Tables\Columns\TextColumn::make('rotation_label')
                    ->label('Rotation')
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color(fn ($state) => match ($state) {
                        'scheduled' => 'warning',
                        'active' => 'success',
                        'completed' => 'info',
                        'paused' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('rotation_pattern')
                    ->label('Pattern')
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('travel_status')
                    ->label('Travel')
                    ->badge()
                    ->formatStateUsing(fn ($state) => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-')
                    ->color(fn ($state) => match ($state) {
                        'pending_request' => 'warning',
                        'request_received' => 'info',
                        'ticket_booked' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('finance_expenses_count')
                    ->label('Expenses Count')
                    ->counts('financeExpenses')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_salary_cost')
                    ->label('Salary Cost')
                    ->state(fn ($record) => number_format((float) $record->totalSalaryCostByCurrency('EUR'), 2) . ' EUR')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_rotation_expenses')
                    ->label('Rotation Expenses')
                    ->state(fn ($record) => number_format((float) $record->totalExpenseByCurrency('EUR'), 2) . ' EUR')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_rotation_cost')
                    ->label('Total Cost')
                    ->weight('bold')
                    ->state(fn ($record) => number_format((float) $record->totalCostByCurrency('EUR'), 2) . ' EUR')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('travel_request_link')
                    ->label('Travel Request')
                    ->state(fn ($record) => filled($record->travel_request_file_path) ? 'Open File' : '-')
                    ->url(fn ($record) => filled($record->travel_request_file_path) ? Storage::disk('public')->url($record->travel_request_file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('ticket_link')
                    ->label('Ticket')
                    ->state(fn ($record) => filled($record->ticket_file_path) ? 'Open File' : '-')
                    ->url(fn ($record) => filled($record->ticket_file_path) ? Storage::disk('public')->url($record->ticket_file_path) : null)
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('from_date')
                    ->label('From')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('to_date')
                    ->label('To')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('mobilization_date')
                    ->label('Mobilization')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('demobilization_date')
                    ->label('Demobilization')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean(),
            ])
            ->defaultSort('from_date', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'rotation_add'))
                    ->label('Add Rotation')
                    ->requiresConfirmation()
                    ->modalHeading('Add Rotation')
                    ->modalSubmitActionLabel('Add Rotation')
                    ->after(function ($record): void {
                        Notification::make()->title('Rotation added successfully')->success()->send();

                        try {
                            $employment = $this->ownerRecord;
                            $label = $record->rotation_label ?: ('Rotation #' . $record->id);

                            app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                                employment: $employment,
                                category: 'rotation',
                                title: 'Rotation Updated',
                                message: 'A rotation update has been added to your portal: ' . $label,
                                portalPath: '/portal/timeline',
                                related: $record,
                                sendEmail: true,
                            );

                            if (filled($record->travel_request_file_path)) {
                                app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                                    employment: $employment,
                                    category: 'travel',
                                    title: 'Travel Request Added',
                                    message: 'A travel request file has been added to your portal for: ' . $label,
                                    portalPath: '/portal/files',
                                    related: $record,
                                    sendEmail: true,
                                );
                            }

                            if (filled($record->ticket_file_path)) {
                                app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                                    employment: $employment,
                                    category: 'ticket',
                                    title: 'Ticket Added',
                                    message: 'A ticket file has been added to your portal for: ' . $label,
                                    portalPath: '/portal/files',
                                    related: $record,
                                    sendEmail: true,
                                );
                            }
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    }),
            ])
            ->recordActions([
                Action::make('financialSnapshot')
                    ->visible(fn () => (bool) (auth()->user()?->canErp('finance_expenses', 'view') || auth()->user()?->canErp('salary_slips', 'view')))
                    ->label('Financial Snapshot')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->modalHeading('Rotation Financial Snapshot')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function ($record) {
                        $snapshot = $record->financialSnapshot();
                        $rows = '';

                        foreach ($snapshot as $currency => $data) {
                            $rows .= '
                                <tr>
                                    <td style="padding:8px; border:1px solid #e2e8f0; font-weight:700;">' . $currency . '</td>
                                    <td style="padding:8px; border:1px solid #e2e8f0;">' . number_format((float) $data['salary_cost'], 2) . '</td>
                                    <td style="padding:8px; border:1px solid #e2e8f0;">' . number_format((float) $data['rotation_expenses'], 2) . '</td>
                                    <td style="padding:8px; border:1px solid #e2e8f0; font-weight:800;">' . number_format((float) $data['total_cost'], 2) . '</td>
                                    <td style="padding:8px; border:1px solid #e2e8f0;">' . number_format((float) $data['revenue'], 2) . '</td>
                                    <td style="padding:8px; border:1px solid #e2e8f0;">' . number_format((float) $data['net'], 2) . '</td>
                                </tr>
                            ';
                        }

                        return new \Illuminate\Support\HtmlString(
                            '<div style="padding: 4px 0 10px;">
                                <div style="font-size:14px; color:#475569; margin-bottom:12px;">
                                    Rotation-level financial view including salary cost, linked rotation expenses, and placeholders for revenue and net result.
                                </div>
                                <div style="overflow:auto;">
                                    <table style="width:100%; border-collapse:collapse; font-size:13px;">
                                        <thead>
                                            <tr style="background:#f8fafc;">
                                                <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Currency</th>
                                                <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Salary Cost</th>
                                                <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Rotation Expenses</th>
                                                <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Total Cost</th>
                                                <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Revenue</th>
                                                <th style="padding:8px; border:1px solid #e2e8f0; text-align:left;">Net</th>
                                            </tr>
                                        </thead>
                                        <tbody>' . $rows . '</tbody>
                                    </table>
                                </div>
                            </div>'
                        );
                    }),

                Action::make('generateSalarySlips')
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'generate_salary_slip'))
                    ->label('Generate Salary Slips')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Salary Slips')
                    ->modalDescription('This will generate salary slips for all months covered by this rotation.')
                    ->modalSubmitActionLabel('Generate')
                    ->action(function ($record) {
                        try {
                            $slips = app(SalarySlipGenerationService::class)
                                ->generateForEmploymentRotation($record, true, auth()->id());

                            $count = count($slips);

                            if ($count === 0) {
                                Notification::make()
                                    ->title('No salary slips were generated')
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title("{$count} salary slip(s) generated successfully")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Could not generate salary slips')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('addRotationExpense')
                    ->visible(fn () => (bool) (auth()->user()?->canErp('employments', 'add_expense') || auth()->user()?->canErp('finance_expenses', 'create')))
                    ->label('Add Rotation Expense')
                    ->icon('heroicon-o-plus-circle')
                    ->color('warning')
                    ->modalHeading('Add Rotation Expense')
                    ->modalSubmitActionLabel('Save Expense')
                    ->form([
                        Select::make('category')
                            ->label('Category')
                            ->options([
                                FinanceExpense::CATEGORY_VISA => 'Visa',
                                FinanceExpense::CATEGORY_TICKET => 'Ticket',
                                FinanceExpense::CATEGORY_HOTEL => 'Hotel',
                                FinanceExpense::CATEGORY_FOOD => 'Food',
                                FinanceExpense::CATEGORY_TRANSPORT => 'Transport',
                                FinanceExpense::CATEGORY_MEDICAL => 'Medical',
                                FinanceExpense::CATEGORY_TRAINING => 'Training',
                                FinanceExpense::CATEGORY_FIELD_COST => 'Field Cost',
                                FinanceExpense::CATEGORY_ACCOMMODATION => 'Accommodation',
                                FinanceExpense::CATEGORY_DESERT_PASS => 'Desert Pass',
                                FinanceExpense::CATEGORY_OTHER => 'Other',
                            ])
                            ->required()
                            ->native(false),

                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->placeholder('Example: Ticket / Hotel / Transport'),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required(),

                        Select::make('currency')
                            ->label('Currency')
                            ->options([
                                'EUR' => 'EUR',
                                'USD' => 'USD',
                                'GBP' => 'GBP',
                                'LYD' => 'LYD',
                            ])
                            ->default('EUR')
                            ->required()
                            ->native(false),

                        DatePicker::make('expense_date')
                            ->label('Expense Date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('incurred_from')
                            ->label('Incurred From'),

                        DatePicker::make('incurred_to')
                            ->label('Incurred To'),

                        Select::make('paid_by')
                            ->label('Paid By')
                            ->options([
                                FinanceExpense::PAID_BY_COMPANY => 'Company',
                                FinanceExpense::PAID_BY_CANDIDATE => 'Candidate / Employee',
                                FinanceExpense::PAID_BY_CLIENT => 'Client',
                                FinanceExpense::PAID_BY_THIRD_PARTY => 'Third Party',
                            ])
                            ->default(FinanceExpense::PAID_BY_COMPANY)
                            ->required()
                            ->native(false),

                        Select::make('reimbursement_status')
                            ->label('Reimbursement Status')
                            ->options([
                                FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE => 'Not Applicable',
                                FinanceExpense::REIMBURSEMENT_PENDING => 'Pending',
                                FinanceExpense::REIMBURSEMENT_APPROVED => 'Approved',
                                FinanceExpense::REIMBURSEMENT_PAID => 'Paid',
                                FinanceExpense::REIMBURSEMENT_REJECTED => 'Rejected',
                            ])
                            ->default(FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE)
                            ->required()
                            ->native(false),

                        Toggle::make('is_first_mobilization')
                            ->label('First Mobilization Expense')
                            ->default(false),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                FinanceExpense::STATUS_DRAFT => 'Draft',
                                FinanceExpense::STATUS_APPROVED => 'Approved',
                                FinanceExpense::STATUS_POSTED => 'Posted',
                                FinanceExpense::STATUS_CANCELLED => 'Cancelled',
                            ])
                            ->default(FinanceExpense::STATUS_DRAFT)
                            ->required()
                            ->native(false),

                        FileUpload::make('attachment_path')
                            ->label('Attachment')
                            ->disk('public')
                            ->directory(function ($record) {
                                return 'finance-expenses/rotation-' . $record->id;
                            })
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/jpg',
                                'image/png',
                                'image/webp',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'text/csv',
                            ])
                            ->maxSize(20480),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->action(function ($record, array $data) {
                        $employment = $record->employment;
                        $preEmployment = $employment?->preEmployment;
                        $job = $employment?->job;
                        $project = $job?->project;
                        $client = $project?->client;
                        $financeProfile = $employment?->currentFinanceProfile;

                        FinanceExpense::create([
                            'job_application_id' => $preEmployment?->job_application_id,
                            'pre_employment_id' => $employment?->pre_employment_id,
                            'employment_id' => $employment?->id,
                            'employment_rotation_id' => $record->id,
                            'job_id' => $employment?->job_id,
                            'client_id' => $client?->id,
                            'project_id' => $project?->id,
                            'candidate_finance_profile_id' => $financeProfile?->id,
                            'created_by' => auth()->id(),
                            'approved_by' => null,
                            'expense_scope' => FinanceExpense::SCOPE_ROTATION,
                            'category' => $data['category'],
                            'title' => $data['title'] ?? null,
                            'description' => $data['description'] ?? null,
                            'amount' => $data['amount'],
                            'currency' => $data['currency'],
                            'expense_date' => $data['expense_date'] ?? null,
                            'incurred_from' => $data['incurred_from'] ?? null,
                            'incurred_to' => $data['incurred_to'] ?? null,
                            'paid_by' => $data['paid_by'] ?? FinanceExpense::PAID_BY_COMPANY,
                            'reimbursement_status' => $data['reimbursement_status'] ?? FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE,
                            'is_first_mobilization' => (bool) ($data['is_first_mobilization'] ?? false),
                            'has_attachment' => filled($data['attachment_path'] ?? null),
                            'attachment_path' => $data['attachment_path'] ?? null,
                            'status' => $data['status'] ?? FinanceExpense::STATUS_DRAFT,
                            'notes' => $data['notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Rotation expense added successfully')
                            ->success()
                            ->send();
                    }),

                Action::make('viewRotationExpenses')
                    ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'view'))
                    ->label('View Rotation Expenses')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.finance-expenses.index', [
                        'tableFilters[employment_rotation_id][value]' => $record->id,
                    ]))
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'rotation_edit'))
                    ->requiresConfirmation()
                    ->modalHeading('Edit Rotation')
                    ->modalSubmitActionLabel('Save Changes'),

                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'rotation_delete'))
                    ->requiresConfirmation()
                    ->modalHeading('Delete Rotation')
                    ->modalDescription('Are you sure you want to delete this rotation record?')
                    ->modalSubmitActionLabel('Yes, Delete'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'rotation_delete'))
                            ->requiresConfirmation(),
                ]),
            ]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        $user = auth()->user();

        return (bool) (
            $user?->canErp('employments', 'rotation_add')
            || $user?->canErp('employments', 'rotation_edit')
            || $user?->canErp('employments', 'rotation_delete')
            || $user?->canErp('employments', 'rotation_print')
            || $user?->canErp('travel_tickets', 'view')
        );
    }
}
