<?php

namespace App\Filament\Resources\PreEmployments\RelationManagers;

use App\Models\CandidateFinanceProfile;
use Carbon\Carbon;
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
                    ->formatStateUsing(
                        fn ($state, $record) => filled($state)
                            ? number_format((float) $state, 2) . ' ' . ($record->payout_currency ?: '')
                            : '-'
                    ),

                Tables\Columns\TextColumn::make('monthly_salary')
                    ->label('Monthly Salary')
                    ->formatStateUsing(
                        fn ($state, $record) => filled($state)
                            ? number_format((float) $state, 2) . ' ' . ($record->payout_currency ?: '')
                            : '-'
                    ),

                Tables\Columns\TextColumn::make('client_billing_rate')
                    ->label('Client Billing Rate')
                    ->formatStateUsing(
                        fn ($state, $record) => filled($state)
                            ? number_format((float) $state, 2) . ' ' . ($record->client_billing_currency ?: '')
                            : '-'
                    )
                    ->weight('bold'),

                Tables\Columns\IconColumn::make('is_current')
                    ->label('Current')
                    ->boolean(),

                Tables\Columns\TextColumn::make('effective_from')
                    ->label('Effective From')
                    ->formatStateUsing(fn ($state) => filled($state) ? Carbon::parse($state)->format('Y-m-d') : '-'),

                Tables\Columns\TextColumn::make('effective_to')
                    ->label('Effective To')
                    ->formatStateUsing(fn ($state) => filled($state) ? Carbon::parse($state)->format('Y-m-d') : '-'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'finance_profile'))
                    ->visible(fn () => ! filled($this->ownerRecord?->converted_to_employment_at))
                    ->mutateDataUsing(function (array $data): array {
                        $data['pre_employment_id'] = $this->ownerRecord->id;
                        $data['project_id'] = $this->ownerRecord->job?->project?->id;
                        $data['client_id'] = $this->ownerRecord->job?->project?->client?->id;
                        $data['job_id'] = $this->ownerRecord->job_id;
                        $data['job_application_id'] = $this->ownerRecord->job_application_id;
                        $data['source_type'] = 'pre_employment';

                        if (($data['is_current'] ?? false) === true) {
                            CandidateFinanceProfile::query()
                                ->where('pre_employment_id', $this->ownerRecord->id)
                                ->update(['is_current' => false]);
                        }

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'finance_profile'))
                    ->visible(fn () => ! filled($this->ownerRecord?->converted_to_employment_at))
                    ->mutateDataUsing(function (array $data, CandidateFinanceProfile $record): array {
                        if (($data['is_current'] ?? false) === true) {
                            CandidateFinanceProfile::query()
                                ->where('pre_employment_id', $this->ownerRecord->id)
                                ->where('id', '!=', $record->id)
                                ->update(['is_current' => false]);
                        }

                        return $data;
                    }),

                DeleteAction::make()
                    ->visible(fn () => (bool) auth()->user()?->canErp('pre_employments', 'finance_profile'))
                    ->visible(fn () => ! filled($this->ownerRecord?->converted_to_employment_at)),
            ])
            ->bulkActions([]);
    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('pre_employments', 'finance_profile') ?? false);
    }
}
