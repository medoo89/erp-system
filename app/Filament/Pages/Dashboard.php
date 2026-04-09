<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecruitmentCalendarOverview;
use App\Filament\Widgets\RecruitmentStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getHeaderWidgets(): array
    {
        return [
            RecruitmentCalendarOverview::class,
            RecruitmentStatsOverview::class,
        ];
    }
}