<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RecruitmentCalendarOverview;
use App\Filament\Widgets\RecruitmentOperationsStats;
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

    public function getWidgets(): array
    {
        return [
            RecruitmentOperationsStats::class,
            RecruitmentCalendarOverview::class,
        ];
    }

    public function getVisibleWidgets(): array
    {
        return [
            RecruitmentOperationsStats::class,
            RecruitmentCalendarOverview::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return (bool) (
            $user
            && ($user->is_admin ?? false)
        );
    }
}
