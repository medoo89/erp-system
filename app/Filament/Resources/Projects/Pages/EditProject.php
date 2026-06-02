<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected string $view = 'filament.resources.projects.pages.edit-project-premium';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewProject')
                ->label('View Project')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => ProjectResource::getUrl('view', ['record' => $this->record])),

            Actions\Action::make('backToClient')
                ->label('Back Client')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(fn () => $this->record->client_id
                    ? url('/admin/clients/' . $this->record->client_id . '/view')
                    : ProjectResource::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit ' . (string) ($this->record->project_name ?? $this->record->name ?? 'Project');
    }

    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('view', ['record' => $this->record]);
    }
}
