<?php

namespace App\Http\Controllers;

use App\Support\RecruitmentCalendarEvents;
use Carbon\Carbon;
use Illuminate\View\View;

class PublicRecruitmentCalendarController extends Controller
{
    public function index(): View
    {
        $calendarEvents = RecruitmentCalendarEvents::make();
        $upcomingTaskGroups = $this->buildUpcomingTaskGroups($calendarEvents);

        return view('public.recruitment-calendar', [
            'calendarEvents' => $calendarEvents,
            'upcomingTaskGroups' => $upcomingTaskGroups,
            'todayLabel' => now()->format('l, d M Y'),
        ]);
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
                'source' => $event['source'] ?? null,
            ];
        }

        return array_values($groups);
    }
}