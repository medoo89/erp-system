<?php

namespace App\Filament\Resources\ProjectContractDocuments\Pages;

use App\Filament\Resources\ProjectContractDocuments\ProjectContractDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectContractDocuments extends ListRecords
{
    protected static string $resource = ProjectContractDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New Document')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),
        ];
    }
}
