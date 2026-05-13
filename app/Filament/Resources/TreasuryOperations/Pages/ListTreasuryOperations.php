<?php

namespace App\Filament\Resources\TreasuryOperations\Pages;

use App\Filament\Resources\TreasuryOperations\TreasuryOperationResource;
use App\Models\TreasuryOperation;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTreasuryOperations extends ListRecords
{
    protected static string $resource = TreasuryOperationResource::class;

    protected string $view = 'filament.resources.treasury-operations.pages.list-treasury-operations-premium';

    public function getHeading(): string
    {
        return '';
    }


    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getViewData(): array
    {
        return [
            'operationsCount' => TreasuryOperation::query()->count(),
            'postedCount' => TreasuryOperation::query()->where('is_posted', true)->count(),
            'draftCount' => TreasuryOperation::query()->where('is_posted', false)->count(),
        ];
    }

    public function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record): string => TreasuryOperationResource::getUrl('view', ['record' => $record]);
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('treasury', 'view') ?? false);
    }

}
