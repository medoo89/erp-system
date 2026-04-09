<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\View\View;

class PublicRecruitmentCalendarController extends Controller
{
    public function index(): View
    {
        $calendarEvents = $this->buildCalendarEvents();
        $upcomingTaskGroups = $this->buildUpcomingTaskGroups($calendarEvents);

        return view('public.recruitment-calendar', [
            'calendarEvents' => $calendarEvents,
            'upcomingTaskGroups' => $upcomingTaskGroups,
        ]);
    }

    protected function buildCalendarEvents(): array
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
                'event_type' => 'job_expiry',
                'sort_date' => $date,
                'job_title' => $job->title,
                'notes' => null,
                'source' => 'job_opening',
            ];
        }

        $manualEvents = CalendarEvent::query()
            ->with('job')
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
                'event_type' => $event->event_type ?: 'task',
                'sort_date' => $date,
                'job_title' => $event->job?->title,
                'notes' => $event->notes,
                'source' => 'manual',
            ];
        }

        usort($events, function (array $a, array $b) {
            $dateCompare = strcmp($a['sort_date'], $b['sort_date']);

            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            return strcmp($a['title'], $b['title']);
        });

        return $events;
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