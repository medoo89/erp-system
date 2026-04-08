<?php

namespace Database\Seeders;

use App\Models\JobApplicationField;
use App\Models\JobApplicationTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobApplicationFieldTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $seniorInstrumentTemplate = JobApplicationTemplate::where('name', 'Senior Instrument Maintenance Technician')->first();
        $cmmmsTemplate = JobApplicationTemplate::where('name', 'CMMMS')->first();

        if ($seniorInstrumentTemplate) {
            $this->syncTemplateFields($seniorInstrumentTemplate->id, [
                'full_name',
                'phone_number',
                'whatsapp_number',
                'email',
                'nationality',
                'current_location',
                'experience',
                'job_title',
                'availability',
                'summary',
                'certificates',
                'cv_file',
                'test',
            ]);
        }

        if ($cmmmsTemplate) {
            $this->syncTemplateFields($cmmmsTemplate->id, [
                'full_name',
                'phone_number',
                'whatsapp_number',
                'email',
                'nationality',
                'current_location',
                'experience',
                'job_title',
                'availability',
                'summary',
                'cv_file',
                'certificates',
                'maximo_cert',
            ]);
        }
    }

    protected function syncTemplateFields(int $templateId, array $fieldKeys): void
    {
        foreach ($fieldKeys as $index => $fieldKey) {
            $field = JobApplicationField::where('field_key', $fieldKey)->first();

            if (! $field) {
                continue;
            }

            DB::table('job_application_field_template')->updateOrInsert(
                [
                    'job_application_template_id' => $templateId,
                    'job_application_field_id' => $field->id,
                ],
                [
                    'sort_order' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}