<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Client;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ClientProfile extends Page
{
    protected static string $resource = ClientResource::class;

    protected string $view = 'filament.resources.clients.pages.client-profile';

    public Client $record;

    public function mount(Client $record): void
    {
        $this->record = $record->load([
            'projects' => fn ($query) => $query->latest('id')->withCount('jobs'),
        ]);
    }

    public function getTitle(): string
    {
        return 'Client Profile — ' . ($this->record->name ?? 'Client');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editClient')
                ->hidden(fn () => ! (bool) auth()->user()?->canErp('clients', 'edit'))
                ->label('Edit Client')
                ->url(fn () => ClientResource::getUrl('edit', ['record' => $this->record])),

            Action::make('createProject')
                ->hidden(fn () => ! (bool) (auth()->user()?->canErp('clients', 'create_project') || auth()->user()?->canErp('projects', 'create')))
                ->label('Add Project')
                ->url(fn () => ProjectResource::getUrl('create', ['client_id' => $this->record->id])),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'client' => $this->record,
            'projects' => $this->record->projects,
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (auth()->user()?->canErp('clients', 'view') ?? false);
    }

}
