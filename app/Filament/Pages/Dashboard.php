<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ExecutiveHero;
use App\Filament\Widgets\RecruitmentCalendarOverview;
use App\Filament\Widgets\RecruitmentStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    public function getHeaderWidgets(): array
    {
        return [
            ExecutiveHero::class,
            RecruitmentStatsOverview::class,
            RecruitmentCalendarOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}