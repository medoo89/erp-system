<?php

namespace App\Filament\Resources\JobApplicationTemplates\Pages;

use App\Filament\Resources\JobApplicationTemplates\JobApplicationTemplateResource;
use App\Models\JobApplicationField;
use Filament\Resources\Pages\EditRecord;

class EditJobApplicationTemplate extends EditRecord
{
    protected static string $resource = JobApplicationTemplateResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['basic_fields'] = $this->record->fields()
            ->where('field_group', 'basic')
            ->pluck('job_application_fields.id')
            ->toArray();

        $data['additional_fields'] = $this->record->fields()
            ->where('field_group', 'additional')
            ->pluck('job_application_fields.id')
            ->toArray();

        // لو ما عندهاش basic محفوظة، علم default basic
        if (empty($data['basic_fields'])) {
            $data['basic_fields'] = JobApplicationField::query()
                ->where('field_group', 'basic')
                ->where('is_active', true)
                ->whereNotIn('field_key', [
                    'phone',
                    'phone_country_code',
                    'whatsapp_country_code',
                ])
                ->orderBy('sort_order')
                ->pluck('id')
                ->toArray();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $basic = $this->data['basic_fields'] ?? [];
        $additional = $this->data['additional_fields'] ?? [];

        $allFields = array_merge($basic, $additional);

        $this->record->fields()->sync($allFields);
    }
}