<?php

namespace App\Filament\Resources\PreEmployments\RelationManagers;

use App\Filament\Resources\FinanceExpenses\Schemas\FinanceExpenseForm;

use App\Filament\Resources\FinanceExpenses\Tables\FinanceExpensesTable;

use App\Models\FinanceExpense;

use Filament\Actions\CreateAction;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Tables\Table;

class PreEmploymentFinanceExpensesRelationManager extends RelationManager

{

    protected static string $relationship = 'financeExpenses';

    protected static ?string $title = 'Pre-Employment Expenses';

    protected static ?string $modelLabel = 'Finance Expense';

    protected static ?string $pluralModelLabel = 'Pre-Employment Expenses';

    public function isReadOnly(): bool

    {

        return false;

    }

    public function form(Schema $schema): Schema

    {

        return FinanceExpenseForm::configure($schema);

    }

    public function table(Table $table): Table

    {

        return FinanceExpensesTable::configure($table)

            ->modifyQueryUsing(fn ($query) => $query->where('pre_employment_id', $this->ownerRecord->id))

            ->headerActions([

                CreateAction::make()
                    ->visible(fn () => (bool) (auth()->user()?->canErp('pre_employments', 'add_expense') || auth()->user()?->canErp('finance_expenses', 'create')))

                    ->label('Add Expense')

                    ->mutateDataUsing(function (array $data): array {

                        $preEmployment = $this->ownerRecord;

                        $job = $preEmployment->job;

                        $project = $job?->project;

                        $client = $project?->client;

                        $financeProfile = $preEmployment->currentFinanceProfile;

                        $data['expense_scope'] = FinanceExpense::SCOPE_PRE_HIRE;

                        $data['pre_employment_id'] = $preEmployment->id;

                        $data['job_application_id'] = $preEmployment->job_application_id;

                        $data['job_id'] = $preEmployment->job_id;

                        $data['project_id'] = $project?->id;

                        $data['client_id'] = $client?->id;

                        $data['candidate_finance_profile_id'] = $financeProfile?->id;

                        $data['created_by'] = auth()->id();

                        $data['status'] = $data['status'] ?? FinanceExpense::STATUS_DRAFT;

                        return $data;

                    }),

            ]);

    }


    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }
}
