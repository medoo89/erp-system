<?php

namespace App\Filament\Resources\FinanceExpenses\Tables;

use App\Models\FinanceExpense;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FinanceExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('expense_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('expense_scope')
                    ->label('Scope')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        FinanceExpense::SCOPE_PRE_HIRE => 'Pre-Hire',
                        FinanceExpense::SCOPE_EMPLOYMENT => 'Employment',
                        FinanceExpense::SCOPE_ROTATION => 'Rotation',
                        FinanceExpense::SCOPE_AD_HOC => 'Ad Hoc',
                        default => filled($state) ? ucfirst(str_replace('_', ' ', $state)) : '-',
                    })
                    ->color(fn (?string $state) => match ($state) {
                        FinanceExpense::SCOPE_PRE_HIRE => 'warning',
                        FinanceExpense::SCOPE_EMPLOYMENT => 'success',
                        FinanceExpense::SCOPE_ROTATION => 'info',
                        FinanceExpense::SCOPE_AD_HOC => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => FinanceExpense::categoryLabels()[$state] ?? '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('owner_name')
                    ->label('Owner')
                    ->state(function ($record) {
                        if ($record->employmentRotation?->employment?->employee_name) {
                            return $record->employmentRotation->employment->employee_name;
                        }

                        if ($record->employment?->employee_name) {
                            return $record->employment->employee_name;
                        }

                        if ($record->preEmployment?->candidate_name) {
                            return $record->preEmployment->candidate_name;
                        }

                        if ($record->jobApplication?->full_name) {
                            return $record->jobApplication->full_name;
                        }

                        return '-';
                    })
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($subQuery) use ($search) {
                            $subQuery->whereHas('employment', fn ($q) => $q->where('employee_name', 'like', "%{$search}%"))
                                ->orWhereHas('preEmployment', fn ($q) => $q->where('candidate_name', 'like', "%{$search}%"))
                                ->orWhereHas('jobApplication', fn ($q) => $q->where('full_name', 'like', "%{$search}%"))
                                ->orWhereHas('employmentRotation.employment', fn ($q) => $q->where('employee_name', 'like', "%{$search}%"));
                        });
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('employment_rotation_id')
                    ->label('Rotation')
                    ->state(function ($record) {
                        if (! $record->employment_rotation_id) {
                            return '-';
                        }

                        $label = 'Rotation #' . $record->employment_rotation_id;

                        if ($record->employmentRotation?->rotation_label) {
                            $label = $record->employmentRotation->rotation_label;
                        }

                        return $label;
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('job.project.client.name')
                    ->label('Client')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('job.project.name')
                    ->label('Project')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '-'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money(fn ($record) => $record->currency ?: 'USD', divideBy: 1)
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_by')
                    ->label('Paid By')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => FinanceExpense::paidByLabels()[$state] ?? '-')
                    ->color(fn (?string $state) => match ($state) {
                        FinanceExpense::PAID_BY_COMPANY => 'success',
                        FinanceExpense::PAID_BY_CANDIDATE => 'warning',
                        FinanceExpense::PAID_BY_CLIENT => 'info',
                        FinanceExpense::PAID_BY_THIRD_PARTY => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reimbursement_status')
                    ->label('Reimbursement')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => FinanceExpense::reimbursementLabels()[$state] ?? '-')
                    ->color(fn (?string $state) => match ($state) {
                        FinanceExpense::REIMBURSEMENT_PENDING => 'warning',
                        FinanceExpense::REIMBURSEMENT_APPROVED => 'info',
                        FinanceExpense::REIMBURSEMENT_PAID => 'success',
                        FinanceExpense::REIMBURSEMENT_REJECTED => 'danger',
                        FinanceExpense::REIMBURSEMENT_NOT_APPLICABLE => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('reimbursement_amount')
                    ->label('Claim Amount')
                    ->state(function ($record) {
                        if (! (bool) ($record->reimbursement_required ?? false)) {
                            return '-';
                        }

                        $amount = $record->reimbursement_amount ?? $record->amount ?? 0;
                        $currency = $record->reimbursement_currency ?: $record->currency ?: 'USD';

                        return number_format((float) $amount, 2) . ' ' . $currency;
                    })
                    ->badge()
                    ->color(fn ($record) => (bool) ($record->reimbursement_required ?? false) ? 'warning' : 'gray')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('candidate_submitted')
                    ->label('Portal Claim')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('has_attachment')
                    ->label('Attachment')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => FinanceExpense::statusLabels()[$state] ?? '-')
                    ->color(fn (?string $state) => match ($state) {
                        FinanceExpense::STATUS_DRAFT => 'gray',
                        FinanceExpense::STATUS_APPROVED => 'info',
                        FinanceExpense::STATUS_PAID => 'success',
                        FinanceExpense::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('expense_date')
                    ->label('Expense Date')
                    ->date('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Approved By')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('expense_scope')
                    ->label('Scope')
                    ->options([
                        FinanceExpense::SCOPE_PRE_HIRE => 'Pre-Hire',
                        FinanceExpense::SCOPE_EMPLOYMENT => 'Employment',
                        FinanceExpense::SCOPE_ROTATION => 'Rotation',
                        FinanceExpense::SCOPE_AD_HOC => 'Ad Hoc',
                    ]),

                SelectFilter::make('category')
                    ->label('Category')
                    ->options(FinanceExpense::categoryLabels()),

                SelectFilter::make('currency')
                    ->label('Currency')
                    ->options([
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'GBP' => 'GBP',
                        'LYD' => 'LYD',
                    ]),

                SelectFilter::make('paid_by')
                    ->label('Paid By')
                    ->options(FinanceExpense::paidByLabels()),

                SelectFilter::make('reimbursement_status')
                    ->label('Reimbursement')
                    ->options(FinanceExpense::reimbursementLabels()),

                SelectFilter::make('reimbursement_required')
                    ->label('Reimbursement Required')
                    ->options([
                        1 => 'Yes - Candidate/Employee Claim',
                        0 => 'No - Company Cost',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(FinanceExpense::statusLabels()),

                SelectFilter::make('employment_id')
                    ->label('Employee')
                    ->relationship('employment', 'employee_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('pre_employment_id')
                    ->label('Pre-Employment')
                    ->relationship('preEmployment', 'candidate_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('employment_rotation_id')
                    ->label('Rotation')
                    ->relationship('employmentRotation', 'rotation_label')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->filtersFormWidth('7xl')
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                            ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'delete')),
                ]),
            ]);
    }
}
