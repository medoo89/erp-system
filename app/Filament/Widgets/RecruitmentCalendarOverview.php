<?php

namespace App\Filament\Widgets;

use App\Support\RecruitmentCalendarEvents;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class RecruitmentCalendarOverview extends Widget
{
    protected string $view = 'filament.widgets.recruitment-calendar-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function getViewData(): array
    {
        $events = RecruitmentCalendarEvents::make();

        return [
            'calendarEvents' => $events,
            'todayLabel' => now()->format('l, d M Y'),
            'upcomingTaskGroups' => $this->buildUpcomingTaskGroups($events),
        ];
    }

    protected function buildUpcomingTaskGroups(array $events): array
    {
        $today = Carbon::today();
        $until = Carbon::today()->addDays(30);

        $filtered = array_filter($events, function (array $event) use ($today, $until) {
            if (empty($event['start'])) {
                return false;
            }

            $date = Carbon::parse($event['start']);

            return $date->betweenIncluded($today, $until);
        });

        $groups = [];

        foreach ($filtered as $event) {
            $dateKey = Carbon::parse($event['start'])->toDateString();

            if (! isset($groups[$dateKey])) {
                $groups[$dateKey] = [
                    'date' => $dateKey,
                    'label' => Carbon::parse($dateKey)->format('D, d M Y'),
                    'items' => [],
                ];
            }

            $groups[$dateKey]['items'][] = [
                'title' => $event['title'],
                'backgroundColor' => $event['backgroundColor'] ?? '#94a3b8',
                'job_title' => $event['job_title'] ?? null,
                'notes' => $event['notes'] ?? null,
            ];
        }

        return array_values($groups);
    }
}