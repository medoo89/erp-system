<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class ViewClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    /*
    |--------------------------------------------------------------------------
    | Client View Page
    |--------------------------------------------------------------------------
    | We intentionally extend EditRecord instead of ViewRecord because the current
    | Filament setup was not registering the ViewRecord route.
    |
    | This still renders our custom read-only Blade view, not the edit form.
    */

    protected string $view = 'filament.resources.clients.pages.view-client-premium';

    public function getTitle(): string
    {
        return (string) ($this->record->name ?? 'Client Review');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('editClient')
                ->label('Edit Client')
                ->icon('heroicon-o-pencil-square')
                ->color('warning')
                ->visible(fn (): bool => (bool) auth()->user()?->canErp('clients', 'edit'))
                ->url(fn () => ClientResource::getUrl('edit', ['record' => $this->record])),

            Actions\Action::make('backToClients')
                ->label('Back to Clients')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => ClientResource::getUrl('index')),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
