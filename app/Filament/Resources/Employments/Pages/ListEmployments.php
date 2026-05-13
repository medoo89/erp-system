<?php

namespace App\Filament\Resources\Employments\Pages;

use App\Filament\Resources\Employments\EmploymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEmployments extends ListRecords
{
    
    protected string $view = 'filament.resources.employments.pages.list-employments-premium';
protected static string $resource = EmploymentResource::class;


    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        $scope = request()->query('employee_scope');

        return match ($scope) {
            'office' => $query->where('employee_category', 'office'),

            'operational' => $query->where(function (Builder $q): void {
                $q->where('employee_category', 'operational')
                    ->orWhereNull('employee_category');
            }),

            'active' => $query->where('status', 'active'),

            'on_rotation' => $query->where('current_work_status', 'on_rotation'),

            'upcoming_mobilization' => $query
                ->whereNotNull('mobilization_date')
                ->whereDate('mobilization_date', '>=', now()->toDateString())
                ->whereDate('mobilization_date', '<=', now()->addDays(30)->toDateString()),

            default => $query,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('employments', 'create')),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.employments.pages.list-employments-premium';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('employments', 'view') ?? false);
    }

}
