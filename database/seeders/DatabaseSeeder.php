<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JobApplicationTemplatesSeeder::class,
            JobApplicationFieldsSeeder::class,
            JobApplicationFieldOptionsSeeder::class,
            JobApplicationFieldTemplateSeeder::class,
        ]);
    }
}