<?php

namespace App\Filament\Resources\JobApplicationTemplates\Pages;

use App\Filament\Resources\JobApplicationTemplates\JobApplicationTemplateResource;
use App\Models\JobApplicationField;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplicationTemplate extends CreateRecord
{
    protected static string $resource = JobApplicationTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // basic fields checked by default لو ما اختارش شيء
        if (! isset($data['basic_fields']) || empty($data['basic_fields'])) {
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

    protected function afterCreate(): void
    {
        $basic = $this->data['basic_fields'] ?? [];
        $additional = $this->data['additional_fields'] ?? [];

        $allFields = array_merge($basic, $additional);

        $this->record->fields()->sync($allFields);
    }
}