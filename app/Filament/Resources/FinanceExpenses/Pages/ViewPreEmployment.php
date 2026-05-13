<?php

namespace App\Filament\Resources\FinanceExpenses\Pages;

use App\Filament\Resources\FinanceExpenses\FinanceExpenseResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPreEmployment extends ViewRecord
{
    
    protected string $view = 'filament.resources.pre-employments.pages.view-pre-employment-premium';
protected static string $resource = PreEmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('pre_employments', 'edit'))
                ->label('Edit')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->url(fn () => static::getResource()::getUrl('edit', ['record' => $this->record])),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.pre-employments.pages.view-pre-employment-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('finance_expenses', 'view') ?? false);
    }

}
