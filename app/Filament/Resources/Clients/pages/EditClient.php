<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected string $view = 'filament.resources.clients.pages.edit-client-premium';

    public function getTitle(): string
    {
        return 'Edit ' . (string) ($this->record->name ?? 'Client');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('View Client')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => url('/admin/clients/' . $this->record->id . '/view')),

            Actions\Action::make('backToClients')
                ->label('Back to Clients')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => ClientResource::getUrl('index')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return url('/admin/clients/' . $this->record->id . '/view');
    }
}
