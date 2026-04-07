<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecruitmentStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?int $navigationSort = 1;

    public function getHeaderWidgets(): array
    {
        return [
            RecruitmentStatsOverview::class,
        ];
    }
}