<?php

namespace Database\Seeders;

use App\Models\JobApplicationTemplate;
use Illuminate\Database\Seeder;

class JobApplicationTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Senior Instrument Maintenance Technician',
                'description' => null,
                'is_active' => true,
            ],
            [
                'name' => 'CMMMS',
                'description' => 'Maximo Required',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            JobApplicationTemplate::updateOrCreate(
                ['name' => $template['name']],
                $template,
            );
        }
    }
}