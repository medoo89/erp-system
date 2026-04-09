<?php

namespace App\Support;

use App\Models\CalendarEvent;
use App\Models\Job;
use Carbon\Carbon;

class RecruitmentCalendarEvents
{
    public static function make(): array
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
                'linked_type' => 'job_opening',
                'linked_id' => $job->id,
                'job_id' => $job->id,
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
                'linked_type' => $event->linked_type ?: 'general',
                'linked_id' => $event->linked_id,
                'job_id' => $event->job_id,
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
}