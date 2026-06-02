<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ProjectProfilePage extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'legacy-project-profile';

    protected string $view = 'filament.pages.project-profile-page';

    public ?int $project = null;

    public function mount(?int $project = null): void
    {
        $this->project = $project;
    }

    public function getTitle(): string
    {
        return 'Legacy Project Profile';
    }
}
