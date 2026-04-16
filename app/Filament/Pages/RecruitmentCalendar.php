<?php

namespace App\Filament\Pages;

use App\Models\CalendarEvent;
use App\Models\Job;
use App\Support\RecruitmentCalendarEvents;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class RecruitmentCalendar extends Page
{
    protected string $view = 'filament.pages.recruitment-calendar';

    public array $calendarEvents = [];
    public array $upcomingTaskGroups = [];
    public array $jobOptions = [];
    public array $eventTypeOptions = [];

    public array $eventForm = [
        'title' => '',
        'event_type' => 'task',
        'event_date' => '',
        'job_id' => '',
        'notes' => '',
        'color' => '#2563eb',
    ];

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

    public function mount(): void
    {
        $this->jobOptions = Job::query()
            ->where('is_archived', false)
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();

        $this->eventTypeOptions = [
            'task' => 'Task',
            'meeting' => 'Meeting',
            'expiry' => 'Expiry',
            'visa' => 'Visa',
            'medical' => 'Medical',
            'ticket' => 'Ticket',
            'rotation' => 'Rotation',
            'certificate' => 'Certificate',
            'other' => 'Other',
        ];

        $this->eventForm['event_date'] = Carbon::today()->toDateString();

        $this->refreshCalendarData();
    }

    public function saveEvent(): void
    {
        $data = validator($this->eventForm, [
            'title' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'in:' . implode(',', array_keys($this->eventTypeOptions))],
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

        $this->eventForm = [
            'title' => '',
            'event_type' => 'task',
            'event_date' => $savedDate,
            'job_id' => '',
            'notes' => '',
            'color' => '#2563eb',
        ];

        $this->refreshCalendarData();

        $this->dispatch('calendar-events-updated', events: $this->calendarEvents);

        Notification::make()
            ->title('Event added successfully')
            ->success()
            ->send();
    }

    protected function refreshCalendarData(): void
    {
        $events = RecruitmentCalendarEvents::make();

        $this->calendarEvents = $events;
        $this->upcomingTaskGroups = $this->buildUpcomingTaskGroups($events);
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