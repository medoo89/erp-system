<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected string $view = 'filament.resources.clients.pages.view-client-premium';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editClient')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('clients', 'edit'))
                ->label('Edit Client')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->url(fn () => static::getResource()::getUrl('edit', [
                    'record' => $this->record,
                ])),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'view') ?? false);
    }

}
