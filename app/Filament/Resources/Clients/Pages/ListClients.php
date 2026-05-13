<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    
    protected string $view = 'filament.resources.clients.pages.list-clients-boxes';
protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => (bool) auth()->user()?->canErp('clients', 'create'))
                ->label('New Client'),
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.clients.pages.list-clients-boxes';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'view') ?? false);
    }

}
