<?php

namespace App\Filament\Resources\Employments\RelationManagers;

use App\Filament\Resources\FinanceExpenses\Schemas\FinanceExpenseForm;
use App\Filament\Resources\FinanceExpenses\Tables\FinanceExpensesTable;
use App\Models\FinanceExpense;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FinanceExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'financeExpenses';

    protected static ?string $title = 'Employee Expenses';

    protected static ?string $modelLabel = 'Finance Expense';

    protected static ?string $pluralModelLabel = 'Employee Expenses';

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
            ->modifyQueryUsing(fn ($query) => $query->where('employment_id', $this->ownerRecord->id))
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => (bool) (auth()->user()?->canErp('employments', 'add_expense') || auth()->user()?->canErp('finance_expenses', 'create')))
                    ->label('Add Expense')
                    ->mutateDataUsing(function (array $data): array {
                        $employment = $this->ownerRecord;
                        $preEmployment = $employment->preEmployment;
                        $job = $employment->job;
                        $project = $job?->project;
                        $client = $project?->client;
                        $financeProfile = $employment->currentFinanceProfile;

                        $data['expense_scope'] = FinanceExpense::SCOPE_EMPLOYMENT;
                        $data['employment_id'] = $employment->id;
                        $data['pre_employment_id'] = $employment->pre_employment_id;
                        $data['job_application_id'] = $preEmployment?->job_application_id;
                        $data['job_id'] = $employment->job_id;
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
