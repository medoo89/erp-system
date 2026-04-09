<?php

namespace App\Filament\Widgets;

use App\Models\CalendarEvent;
use App\Models\Job;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class RecruitmentCalendarOverview extends Widget
{
    protected string $view = 'filament.widgets.recruitment-calendar-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected function getViewData(): array
    {
        return [
            'calendarEvents' => $this->getCalendarEvents(),
        ];
    }

    protected function getCalendarEvents(): array
    {
        $events = [];

        $jobs = Job::query()
            ->whereNotNull('closing_date')
            ->where('is_archived', false)
            ->get();

        foreach ($jobs as $job) {
            $date = Carbon::parse($job->closing_date)->toDateString();

            $events[] = [
                'title' => 'Job Expiry: ' . $job->title,
                'start' => $date,
                'allDay' => true,
                'backgroundColor' => '#f59e0b',
                'borderColor' => '#f59e0b',
                'textColor' => '#ffffff',
            ];
        }

        $manualEvents = CalendarEvent::query()
            ->where('is_active', true)
            ->orderBy('event_date')
            ->get();

        foreach ($manualEvents as $event) {
            $date = Carbon::parse($event->event_date)->toDateString();

            $events[] = [
                'title' => $event->title,
                'start' => $date,
                'allDay' => true,
                'backgroundColor' => $event->color ?: '#2563eb',
                'borderColor' => $event->color ?: '#2563eb',
                'textColor' => '#ffffff',
            ];
        }

        usort($events, function (array $a, array $b) {
            return strcmp($a['start'], $b['start']);
        });

        return $events;
    }
}