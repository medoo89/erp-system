<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ClientViewPage extends Page
{
    protected static ?string $slug = 'clients/{client}/view';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.client-view-page';

    public Client $client;

    public function mount(Client $client): void
    {
        abort_unless((bool) auth()->user()?->canErp('clients', 'view'), 403);

        $this->client = $client;
    }

    public function getTitle(): string | Htmlable
    {
        return $this->client->name ?? 'Client';
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/clients') => 'Clients',
            '#' => 'Review',
        ];
    }
}
