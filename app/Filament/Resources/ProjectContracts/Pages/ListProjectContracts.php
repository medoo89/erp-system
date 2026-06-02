<?php

namespace App\Filament\Resources\ProjectContracts\Pages;

use App\Filament\Resources\ProjectContracts\ProjectContractResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectContracts extends ListRecords
{
    protected static string $resource = ProjectContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Contract')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),
        ];
    }
}
