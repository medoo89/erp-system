<?php

namespace App\Filament\Pages;

use App\Models\CalendarEvent;
use App\Models\Job;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class RecruitmentCalendar extends Page
{
    protected string $view = 'filament.pages.recruitment-calendar';

    public array $calendarEvents = [];
    public array $upcomingTaskGroups = [];
    public ?string $selectedDate = null;
    public ?string $selectedDateLabel = null;
    public array $selectedDateEvents = [];
    public bool $showAddEventForm = false;

    public array $eventForm = [
        'title' => '',
        'event_type' => 'task',
        'event_date' => '',
        'job_id' => '',
        'notes' => '',
        'color' => '#2563eb',
    ];

    public array $jobOptions = [];

    public array $colorOptions = [
        '#2563eb',
        '#10b981',
        '#f59e0b',
        '#ef4444',
        '#8b5cf6',
        '#06b6d4',
        '#ec4899',
        '#84cc16',
    ];

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationLabel(): string
    {
        return 'Recruitment Calendar';
    }

    public function getTitle(): string
    {
        return '';
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public function mount(): void
    {
        $this->jobOptions = Job::query()
            ->where('is_archived', false)
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();

        $this->refreshCalendarData();

        $today = Carbon::today()->toDateString();
        $this->setSelectedDate($today);
        $this->eventForm['event_date'] = $today;
    }

    public function onCalendarDateClick(string $date): void
    {
        $this->setSelectedDate($date);
        $this->eventForm['event_date'] = $date;
    }

    public function toggleAddEventForm(): void
    {
        $this->showAddEventForm = ! $this->showAddEventForm;

        if ($this->showAddEventForm && blank($this->eventForm['event_date']) && $this->selectedDate) {
            $this->eventForm['event_date'] = $this->selectedDate;
        }
    }

    public function setEventColor(string $color): void
    {
        $this->eventForm['color'] = $color;
    }

    public function saveEvent(): void
    {
        $data = validator($this->eventForm, [
            'title' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'event_date' => ['required', 'date'],
            'job_id' => ['nullable', 'exists:jobs,id'],
            'notes' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
        ])->validate();

        CalendarEvent::create([
            'title' => $data['title'],
            'event_type' => $data['event_type'],
            'notes' => $data['notes'] ?: null,
            'event_date' => $data['event_date'],
            'is_all_day' => true,
            'color' => $data['color'] ?: '#2563eb',
            'linked_type' => filled($data['job_id']) ? 'job_opening' : 'general',
            'linked_id' => filled($data['job_id']) ? (int) $data['job_id'] : null,
            'job_id' => filled($data['job_id']) ? (int) $data['job_id'] : null,
            'is_active' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $savedDate = $data['event_date'];

        $this->resetEventForm();
        $this->showAddEventForm = false;

        $this->refreshCalendarData();
        $this->setSelectedDate($savedDate);

        $this->dispatch('calendar-events-updated', events: $this->calendarEvents);

        Notification::make()
            ->title('Event added successfully')
            ->success()
            ->send();
    }

    protected function refreshCalendarData(): void
    {
        $events = $this->buildCalendarEvents();

        $this->calendarEvents = $events;
        $this->upcomingTaskGroups = $this->buildUpcomingTaskGroups($events);
    }

    protected function resetEventForm(): void
    {
        $this->eventForm = [
            'title' => '',
            'event_type' => 'task',
            'event_date' => $this->selectedDate ?: Carbon::today()->toDateString(),
            'job_id' => '',
            'notes' => '',
            'color' => '#2563eb',
        ];
    }

    protected function setSelectedDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->selectedDateLabel = Carbon::parse($date)->format('D, d M Y');

        $this->selectedDateEvents = array_values(array_filter(
            $this->calendarEvents,
            fn (array $event) => ($event['start'] ?? null) === $date
        ));
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
                'event_type' => $event['event_type'] ?? null,
                'backgroundColor' => $event['backgroundColor'] ?? '#94a3b8',
                'linked_type' => $event['linked_type'] ?? null,
                'linked_id' => $event['linked_id'] ?? null,
                'job_title' => $event['job_title'] ?? null,
                'notes' => $event['notes'] ?? null,
                'source' => $event['source'] ?? null,
            ];
        }

        return array_values($groups);
    }
}