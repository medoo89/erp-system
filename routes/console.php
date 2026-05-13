<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');


use Illuminate\Support\Facades\Schedule;

Schedule::command('rotations:sync-statuses')
    ->dailyAt('00:10')
    ->withoutOverlapping();
