<?php

namespace App\Filament\Resources\Employments\RelationManagers;

use App\Models\CandidateFinanceProfile;
use App\Models\SalarySlip;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class FinanceProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'financeProfiles';

    protected static ?string $title = 'Finance Profiles';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
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
                ->numeric(),

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
                ->native(false),

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

            Toggle::make('is_current')
                ->label('Current Profile')
                ->default(true),

            Toggle::make('is_hidden_from_non_finance')
                ->label('Hide From Non-Finance')
                ->default(true),

            Textarea::make('finance_notes')
                ->label('Finance Notes')
                ->rows(4)
                ->columnSpanFull(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('salary_basis')
                    ->label('Salary Basis')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === CandidateFinanceProfile::BASIS_DAILY_RATE ? 'Daily Rate' : 'Monthly')
                    ->color('info'),

                Tables\Columns\TextColumn::make('daily_rate')
                    ->label('Daily Rate')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->payout_currency ?: '') : '-'),

                Tables\Columns\TextColumn::make('monthly_salary')
                    ->label('Monthly Salary')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->payout_currency ?: '') : '-'),

                Tables\Columns\TextColumn::make('client_billing_rate')
                    ->label('Client Billing Rate')
                    ->formatStateUsing(fn ($state, $record) => filled($state) ? number_format((float) $state, 2) . ' ' . ($record->client_billing_currency ?: '') : '-')
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean(),

                Tables\Columns\TextColumn::make('effective_from')
                    ->date('Y-m-d')
                    ->default('-'),

                Tables\Columns\TextColumn::make('effective_to')
                    ->date('Y-m-d')
                    ->default('-'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'finance_profile_edit'))
                    ->visible(fn () => ! $this->financeProfileLocked())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['employment_id'] = $this->ownerRecord->id;
                        $data['project_id'] = $this->ownerRecord->job?->project?->id;
                        $data['client_id'] = $this->ownerRecord->job?->project?->client?->id;
                        $data['job_id'] = $this->ownerRecord->job_id;
                        $data['pre_employment_id'] = $this->ownerRecord->pre_employment_id;
                        $data['source_type'] = 'employment';

                        if (($data['is_current'] ?? false) === true) {
                            CandidateFinanceProfile::query()
                                ->where('employment_id', $this->ownerRecord->id)
                                ->update(['is_current' => false]);
                        }

                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'finance_profile_edit'))
                    ->visible(fn () => ! $this->financeProfileLocked())
                    ->mutateFormDataUsing(function (array $data, CandidateFinanceProfile $record): array {
                        if (($data['is_current'] ?? false) === true) {
                            CandidateFinanceProfile::query()
                                ->where('employment_id', $this->ownerRecord->id)
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }

                        return $data;
                    }),

                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('finance_expenses', 'delete'))
                    ->visible(fn () => ! $this->financeProfileLocked()),
            ])
            ->bulkActions([]);
    }

    protected function financeProfileLocked(): bool
    {
        $hasApprovedSalarySlip = SalarySlip::query()
            ->where('employment_id', $this->ownerRecord->id)
            ->whereIn('status', [
                SalarySlip::STATUS_APPROVED,
                SalarySlip::STATUS_LOCKED,
                SalarySlip::STATUS_PAID,
            ])
            ->exists();

        if ($hasApprovedSalarySlip) {
            return true;
        }

        return $this->ownerRecord->clientInvoiceLines()->exists();
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'finance_profile_view') ?? false);
    }
}
