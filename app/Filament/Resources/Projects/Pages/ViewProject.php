<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\ClientInvoices\ClientInvoiceResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ProjectContract;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected string $view = 'filament.resources.projects.pages.view-project';

    protected function getHeaderActions(): array
    {
        // Actions are rendered inside the premium Blade hero to avoid duplicate / floating buttons.
        return [];
    }

    public function getTitle(): string
    {
        return (string) ($this->record->project_name ?? $this->record->name ?? 'Project');
    }
}
