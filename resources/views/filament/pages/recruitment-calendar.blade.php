<x-filament-panels::page>
    <style>
        .recruitment-calendar-page {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .recruitment-calendar-title-wrap {
            display: flex;
            justify-content: center;
        }

        .recruitment-calendar-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: min(100%, 720px);
            padding: 18px 36px;
            border-radius: 24px;
            font-size: 2.5rem;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #0f172a;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow:
                0 12px 30px rgba(15, 23, 42, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.80);
        }

        .dark .recruitment-calendar-title {
            color: #f8fafc;
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow:
                0 12px 30px rgba(0, 0, 0, 0.20),
                inset 0 1px 0 rgba(255, 255, 255, 0.06);
        }

        .recruitment-calendar-layout {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(360px, 0.9fr);
            gap: 1.5rem;
            align-items: start;
        }

        .recruitment-calendar-card,
        .recruitment-sidebar-card {
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow:
                0 12px 30px rgba(15, 23, 42, 0.06),
                inset 0 1px 0 rgba(255,255,255,0.80);
        }

        .dark .recruitment-calendar-card,
        .dark .recruitment-sidebar-card {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            border: 1px solid rgba(148, 163, 184, 0.10);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.02),
                0 12px 30px rgba(0, 0, 0, 0.18);
        }

        .recruitment-calendar-card {
            padding: 24px;
            min-height: 820px;
        }

        .recruitment-sidebar-card {
            padding: 22px;
        }

        .recruitment-sidebar-title {
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0 0 0.3rem 0;
            color: #0f172a;
        }

        .dark .recruitment-sidebar-title {
            color: #f8fafc;
        }

        .recruitment-sidebar-subtitle {
            margin: 0 0 1rem 0;
            font-size: 0.95rem;
            color: #64748b;
        }

        .dark .recruitment-sidebar-subtitle {
            color: #94a3b8;
        }

        .sidebar-section {
            margin-bottom: 1.25rem;
        }

        .sidebar-section:last-child {
            margin-bottom: 0;
        }

        .selected-date-label {
            font-size: 1.08rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            color: #0f172a;
        }

        .dark .selected-date-label {
            color: #f8fafc;
        }

        .event-list,
        .upcoming-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .event-item,
        .upcoming-item {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            background: rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .dark .event-item,
        .dark .upcoming-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(148, 163, 184, 0.10);
        }

        .event-dot,
        .upcoming-dot {
            width: 12px;
            height: 12px;
            min-width: 12px;
            border-radius: 999px;
            margin-top: 0.35rem;
        }

        .event-content,
        .upcoming-content {
            display: flex;
            flex-direction: column;
            gap: 0.24rem;
        }

        .event-title,
        .upcoming-title {
            font-size: 0.98rem;
            font-weight: 800;
            line-height: 1.4;
            color: #0f172a;
        }

        .dark .event-title,
        .dark .upcoming-title {
            color: #f8fafc;
        }

        .event-meta,
        .upcoming-meta {
            font-size: 0.86rem;
            font-weight: 600;
            color: #475569;
        }

        .dark .event-meta,
        .dark .upcoming-meta {
            color: #cbd5e1;
        }

        .event-notes,
        .upcoming-notes {
            font-size: 0.84rem;
            line-height: 1.45;
            color: #64748b;
        }

        .dark .event-notes,
        .dark .upcoming-notes {
            color: #94a3b8;
        }

        .empty-state {
            padding: 1rem;
            border-radius: 18px;
            text-align: center;
            font-size: 0.95rem;
            background: rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(148, 163, 184, 0.12);
            color: #64748b;
        }

        .dark .empty-state {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(148, 163, 184, 0.10);
            color: #94a3b8;
        }

        .event-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.9rem;
        }

        .event-form-grid .full-span {
            grid-column: 1 / -1;
        }

        .color-picker-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 0.25rem;
        }

        .color-picker-button {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: transform .15s ease, border-color .15s ease;
        }

        .color-picker-button.active {
            border-color: #111827;
            transform: scale(1.08);
        }

        .dark .color-picker-button.active {
            border-color: #f8fafc;
        }

        .calendar-action-row {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .recruitment-calendar-card .fc {
            font-family: 'Gilroy', Inter, system-ui, sans-serif;
            color: #0f172a;
        }

        .dark .recruitment-calendar-card .fc {
            color: #e5e7eb;
        }

        .recruitment-calendar-card .fc-theme-standard td,
        .recruitment-calendar-card .fc-theme-standard th,
        .recruitment-calendar-card .fc-scrollgrid,
        .recruitment-calendar-card .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.22) !important;
        }

        .dark .recruitment-calendar-card .fc-theme-standard td,
        .dark .recruitment-calendar-card .fc-theme-standard th,
        .dark .recruitment-calendar-card .fc-scrollgrid,
        .dark .recruitment-calendar-card .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.16) !important;
        }

        .recruitment-calendar-card .fc-toolbar-title {
            font-size: 2.8rem !important;
            line-height: 1.1 !important;
            font-weight: 800 !important;
            color: #0f172a !important;
        }

        .dark .recruitment-calendar-card .fc-toolbar-title {
            color: #f8fafc !important;
        }

        .recruitment-calendar-card .fc-col-header-cell-cushion {
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 12px 0 !important;
            color: #475569 !important;
        }

        .dark .recruitment-calendar-card .fc-col-header-cell-cushion {
            color: #cbd5e1 !important;
        }

        .recruitment-calendar-card .fc-daygrid-day-number {
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 10px !important;
            color: #0f172a !important;
        }

        .dark .recruitment-calendar-card .fc-daygrid-day-number {
            color: #f8fafc !important;
        }

        .recruitment-calendar-card .fc-day-other .fc-daygrid-day-number {
            color: #94a3b8 !important;
        }

        .dark .recruitment-calendar-card .fc-day-other .fc-daygrid-day-number {
            color: #64748b !important;
        }

        .recruitment-calendar-card .fc-button {
            border: none !important;
            border-radius: 999px !important;
            padding: 0.7rem 1rem !important;
            box-shadow: none !important;
            background: rgba(15, 23, 42, 0.08) !important;
            color: #0f172a !important;
        }

        .dark .recruitment-calendar-card .fc-button {
            background: rgba(255,255,255,0.08) !important;
            color: #ffffff !important;
        }

        .recruitment-calendar-card .fc-button:hover {
            background: rgba(15, 23, 42, 0.14) !important;
        }

        .dark .recruitment-calendar-card .fc-button:hover {
            background: rgba(255,255,255,0.14) !important;
        }

        .recruitment-calendar-card .fc-event {
            border-radius: 9px !important;
            padding: 3px 8px !important;
            font-size: 0.8rem !important;
            font-weight: 800 !important;
            border-width: 0 !important;
        }

        .recruitment-calendar-card .fc-daygrid-event-dot {
            display: none !important;
        }

        .recruitment-calendar-card .fc-daygrid-day.selected-calendar-day {
            background: rgba(59, 130, 246, 0.10) !important;
            box-shadow: inset 0 0 0 2px rgba(59, 130, 246, 0.45);
            border-radius: 12px;
        }

        .dark .recruitment-calendar-card .fc-daygrid-day.selected-calendar-day {
            background: rgba(59, 130, 246, 0.16) !important;
            box-shadow: inset 0 0 0 2px rgba(96, 165, 250, 0.55);
        }

        @media (max-width: 1280px) {
            .recruitment-calendar-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .recruitment-calendar-title {
                width: 100%;
                min-width: auto;
                font-size: 1.75rem;
                padding: 14px 18px;
            }

            .event-form-grid {
                grid-template-columns: 1fr;
            }

            .recruitment-calendar-card,
            .recruitment-sidebar-card {
                padding: 16px;
                border-radius: 24px;
            }

            .recruitment-calendar-card .fc-toolbar-title {
                font-size: 1.8rem !important;
            }
        }
    </style>

    <div
        class="recruitment-calendar-page"
        x-data="{
            calendar: null,
            events: @js($calendarEvents),
            selectedDate: @entangle('selectedDate'),
            updateSelectedDateHighlight() {
                if (!this.$refs.calendar) return;

                this.$refs.calendar.querySelectorAll('.fc-daygrid-day').forEach((cell) => {
                    cell.classList.remove('selected-calendar-day');
                });

                if (!this.selectedDate) return;

                const cell = this.$refs.calendar.querySelector(`[data-date='${this.selectedDate}']`);
                if (cell) {
                    cell.classList.add('selected-calendar-day');
                }
            },
            initCalendar() {
                const boot = () => {
                    if (typeof FullCalendar === 'undefined') {
                        setTimeout(boot, 200);
                        return;
                    }

                    if (!this.$refs.calendar) {
                        setTimeout(boot, 200);
                        return;
                    }

                    if (this.calendar) {
                        this.calendar.destroy();
                    }

                    this.calendar = new FullCalendar.Calendar(this.$refs.calendar, {
                        initialView: 'dayGridMonth',
                        height: 760,
                        headerToolbar: {
                            left: 'title',
                            center: '',
                            right: 'prev today next'
                        },
                        buttonText: {
                            today: 'Today',
                        },
                        fixedWeekCount: false,
                        showNonCurrentDates: true,
                        dayMaxEventRows: 3,
                        events: this.events,
                        dateClick: (info) => {
                            this.selectedDate = info.dateStr;
                            $wire.onCalendarDateClick(info.dateStr);
                            setTimeout(() => this.updateSelectedDateHighlight(), 50);
                        },
                    });

                    this.calendar.render();
                    setTimeout(() => this.updateSelectedDateHighlight(), 50);
                };

                boot();

                window.addEventListener('calendar-events-updated', (event) => {
                    const incoming = event.detail?.events ?? [];
                    this.events = incoming;

                    if (this.calendar) {
                        this.calendar.removeAllEvents();
                        this.calendar.addEventSource(incoming);
                        setTimeout(() => this.updateSelectedDateHighlight(), 50);
                    }
                });

                this.$watch('selectedDate', () => {
                    setTimeout(() => this.updateSelectedDateHighlight(), 30);
                });

                document.addEventListener('livewire:navigated', () => {
                    setTimeout(() => boot(), 150);
                });
            }
        }"
        x-init="initCalendar()"
    >
        <div class="recruitment-calendar-title-wrap">
            <div class="recruitment-calendar-title">
                Recruitment Calendar
            </div>
        </div>

        <div class="recruitment-calendar-layout">
            <div class="recruitment-calendar-card" wire:ignore>
                <div x-ref="calendar"></div>
            </div>

            <div class="recruitment-sidebar-card">
                <div class="sidebar-section">
                    <h3 class="recruitment-sidebar-title">Selected Date</h3>
                    <p class="recruitment-sidebar-subtitle">Click any day on the calendar to view its events.</p>

                    <div class="selected-date-label">
                        {{ $selectedDateLabel ?: 'No date selected' }}
                    </div>

                    @if (count($selectedDateEvents))
                        <div class="event-list">
                            @foreach ($selectedDateEvents as $event)
                                <div class="event-item">
                                    <span
                                        class="event-dot"
                                        style="background-color: {{ $event['backgroundColor'] ?? '#94a3b8' }};"
                                    ></span>

                                    <div class="event-content">
                                        <div class="event-title">{{ $event['title'] }}</div>

                                        @if (! empty($event['job_title']))
                                            <div class="event-meta">
                                                Linked to job: {{ $event['job_title'] }}
                                            </div>
                                        @else
                                            <div class="event-meta">
                                                {{ ($event['linked_type'] ?? 'general') === 'general' ? 'General Event' : ucfirst(str_replace('_', ' ', (string) ($event['linked_type'] ?? 'general'))) }}
                                            </div>
                                        @endif

                                        @if (! empty($event['notes']))
                                            <div class="event-notes">{{ $event['notes'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            No events on this selected date.
                        </div>
                    @endif
                </div>

                <div class="sidebar-section">
                    <div class="calendar-action-row">
                        <x-filament::button type="button" wire:click="toggleAddEventForm">
                            {{ $showAddEventForm ? 'Close Add Event' : 'Add Event' }}
                        </x-filament::button>
                    </div>
                </div>

                @if ($showAddEventForm)
                    <div class="sidebar-section">
                        <h3 class="recruitment-sidebar-title">Add Event</h3>
                        <p class="recruitment-sidebar-subtitle">Create a new calendar event linked to a job or keep it general.</p>

                        <div class="event-form-grid">
                            <div class="full-span">
                                {{ \Filament\Support\Facades\FilamentView::renderHook('recruitment-calendar.form.start') }}
                            </div>

                            <x-filament::input.wrapper>
                                <x-filament::input
                                    wire:model.defer="eventForm.title"
                                    type="text"
                                    placeholder="Event title"
                                />
                            </x-filament::input.wrapper>

                            <x-filament::input.wrapper>
                                <select wire:model.defer="eventForm.event_type" class="fi-select-input block w-full rounded-xl border-0 bg-transparent text-sm">
                                    <option value="task">Task</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="expiry">Expiry</option>
                                    <option value="visa">Visa</option>
                                    <option value="medical">Medical</option>
                                    <option value="ticket">Ticket</option>
                                    <option value="rotation">Rotation</option>
                                    <option value="certificate">Certificate</option>
                                    <option value="other">Other</option>
                                </select>
                            </x-filament::input.wrapper>

                            <x-filament::input.wrapper>
                                <x-filament::input
                                    wire:model.defer="eventForm.event_date"
                                    type="date"
                                />
                            </x-filament::input.wrapper>

                            <x-filament::input.wrapper>
                                <select wire:model.defer="eventForm.job_id" class="fi-select-input block w-full rounded-xl border-0 bg-transparent text-sm">
                                    <option value="">General Event</option>
                                    @foreach ($jobOptions as $jobId => $jobTitle)
                                        <option value="{{ $jobId }}">{{ $jobTitle }}</option>
                                    @endforeach
                                </select>
                            </x-filament::input.wrapper>

                            <div class="full-span">
                                <label class="text-sm font-bold">Color</label>
                                <div class="color-picker-row">
                                    @foreach ($colorOptions as $color)
                                        <button
                                            type="button"
                                            wire:click="setEventColor('{{ $color }}')"
                                            class="color-picker-button {{ ($eventForm['color'] ?? null) === $color ? 'active' : '' }}"
                                            style="background-color: {{ $color }};"
                                        ></button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="full-span">
                                <x-filament::input.wrapper>
                                    <textarea
                                        wire:model.defer="eventForm.notes"
                                        rows="4"
                                        class="fi-textarea block w-full rounded-xl border-0 bg-transparent text-sm"
                                        placeholder="Notes"
                                    ></textarea>
                                </x-filament::input.wrapper>
                            </div>

                            <div class="full-span">
                                <x-filament::button type="button" wire:click="saveEvent">
                                    Save Event
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="sidebar-section">
                    <h3 class="recruitment-sidebar-title">Upcoming Tasks</h3>
                    <p class="recruitment-sidebar-subtitle">From today to the next 30 days.</p>

                    @if (count($upcomingTaskGroups))
                        <div class="upcoming-list">
                            @foreach ($upcomingTaskGroups as $group)
                                <div class="upcoming-item">
                                    <div class="upcoming-content" style="width: 100%;">
                                        <div class="selected-date-label" style="margin-bottom: 0.45rem;">
                                            {{ $group['label'] }}
                                        </div>

                                        @foreach ($group['items'] as $item)
                                            <div style="display:flex; gap:0.7rem; align-items:flex-start; margin-bottom:0.7rem;">
                                                <span
                                                    class="upcoming-dot"
                                                    style="background-color: {{ $item['backgroundColor'] ?? '#94a3b8' }};"
                                                ></span>

                                                <div class="upcoming-content">
                                                    <div class="upcoming-title">{{ $item['title'] }}</div>

                                                    @if (! empty($item['job_title']))
                                                        <div class="upcoming-meta">
                                                            Linked to job: {{ $item['job_title'] }}
                                                        </div>
                                                    @else
                                                        <div class="upcoming-meta">
                                                            {{ ($item['linked_type'] ?? 'general') === 'general' ? 'General Event' : ucfirst(str_replace('_', ' ', (string) ($item['linked_type'] ?? 'general'))) }}
                                                        </div>
                                                    @endif

                                                    @if (! empty($item['notes']))
                                                        <div class="upcoming-notes">{{ $item['notes'] }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            No upcoming tasks in the next 30 days.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>