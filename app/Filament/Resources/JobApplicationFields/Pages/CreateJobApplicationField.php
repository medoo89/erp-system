<?php

namespace App\Filament\Resources\JobApplicationFields\Pages;

use App\Filament\Resources\JobApplicationFields\JobApplicationFieldResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplicationField extends CreateRecord
{
    protected static string $resource = JobApplicationFieldResource::class;
}
