<?php

namespace App\Filament\Widgets;

use App\Support\RecruitmentCalendarEvents;
use Filament\Widgets\Widget;

class RecruitmentCalendarOverview extends Widget
{
    protected string $view = 'filament.widgets.recruitment-calendar-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        return [
            'calendarEvents' => RecruitmentCalendarEvents::make(),
        ];
    }
}