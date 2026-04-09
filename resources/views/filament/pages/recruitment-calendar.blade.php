<x-filament-panels::page>
    <style>
        .fi-page-header {
            display: none !important;
        }

        .rc-layout {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .rc-global-header {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .rc-global-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: min(100%, 560px);
            padding: 18px 36px;
            border-radius: 24px;
            font-size: 2.4rem;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.03em;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow:
                0 14px 34px rgba(15, 23, 42, 0.10),
                inset 0 1px 0 rgba(255, 255, 255, 0.10);
            transition: all 0.2s ease;
        }

        html.dark .rc-global-title {
            color: #f8fafc;
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        html:not(.dark) .rc-global-title {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .rc-page-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.9fr) minmax(360px, 0.95fr);
            gap: 1.5rem;
            align-items: start;
        }

        .rc-main-column,
        .rc-side-column {
            min-width: 0;
        }

        .rc-calendar-wrap,
        .rc-card {
            border-radius: 30px;
            overflow: hidden;
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .rc-calendar-wrap {
            padding: 24px;
            min-height: 820px;
        }

        .rc-card {
            padding: 22px;
        }

        .rc-side-stack {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        html.dark .rc-calendar-wrap,
        html.dark .rc-card {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            border: 1px solid rgba(148, 163, 184, 0.10);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.02),
                0 12px 30px rgba(0, 0, 0, 0.14);
        }

        html:not(.dark) .rc-calendar-wrap,
        html:not(.dark) .rc-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow:
                0 12px 30px rgba(15, 23, 42, 0.06),
                inset 0 1px 0 rgba(255,255,255,0.80);
        }

        .rc-selected-card {
            border-width: 1px;
            border-style: solid;
        }

        html.dark .rc-selected-card {
            border-color: rgba(59, 130, 246, 0.35);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.02),
                0 12px 30px rgba(37, 99, 235, 0.12);
        }

        html:not(.dark) .rc-selected-card {
            border-color: rgba(59, 130, 246, 0.28);
            box-shadow:
                0 12px 30px rgba(37, 99, 235, 0.08),
                inset 0 1px 0 rgba(255,255,255,0.80);
        }

        .rc-calendar-wrap .fc {
            font-family: 'Gilroy', Inter, system-ui, sans-serif;
        }

        html.dark .rc-calendar-wrap .fc {
            color: #e5e7eb;
        }

        html:not(.dark) .rc-calendar-wrap .fc {
            color: #0f172a;
        }

        html.dark .rc-calendar-wrap .fc-theme-standard td,
        html.dark .rc-calendar-wrap .fc-theme-standard th,
        html.dark .rc-calendar-wrap .fc-scrollgrid,
        html.dark .rc-calendar-wrap .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.16) !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-theme-standard td,
        html:not(.dark) .rc-calendar-wrap .fc-theme-standard th,
        html:not(.dark) .rc-calendar-wrap .fc-scrollgrid,
        html:not(.dark) .rc-calendar-wrap .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.20) !important;
        }

        .rc-calendar-wrap .fc-toolbar-title {
            font-size: 2.5rem !important;
            line-height: 1.1 !important;
            font-weight: 800 !important;
        }

        html.dark .rc-calendar-wrap .fc-toolbar-title {
            color: #f8fafc !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-toolbar-title {
            color: #0f172a !important;
        }

        .rc-calendar-wrap .fc-col-header-cell-cushion {
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 12px 0 !important;
        }

        html.dark .rc-calendar-wrap .fc-col-header-cell-cushion {
            color: #cbd5e1 !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-col-header-cell-cushion {
            color: #475569 !important;
        }

        .rc-calendar-wrap .fc-daygrid-day-number {
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 10px !important;
            cursor: pointer;
        }

        html.dark .rc-calendar-wrap .fc-daygrid-day-number {
            color: #f8fafc !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-daygrid-day-number {
            color: #0f172a !important;
        }

        html.dark .rc-calendar-wrap .fc-day-other .fc-daygrid-day-number {
            color: #64748b !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-day-other .fc-daygrid-day-number {
            color: #94a3b8 !important;
        }

        .rc-calendar-wrap .fc-daygrid-day-frame {
            cursor: pointer;
            transition: background 0.18s ease;
        }

        html.dark .rc-calendar-wrap .fc-daygrid-day-frame:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        html:not(.dark) .rc-calendar-wrap .fc-daygrid-day-frame:hover {
            background: rgba(15, 23, 42, 0.03);
        }

        .rc-calendar-wrap .fc-button {
            border: none !important;
            border-radius: 999px !important;
            padding: 0.72rem 1.05rem !important;
            box-shadow: none !important;
            transition: background 0.2s ease, color 0.2s ease;
        }

        html.dark .rc-calendar-wrap .fc-button {
            background: rgba(255,255,255,0.08) !important;
            color: #ffffff !important;
        }

        html.dark .rc-calendar-wrap .fc-button:hover {
            background: rgba(255,255,255,0.14) !important;
        }

        html.dark .rc-calendar-wrap .fc-button-primary:not(:disabled).fc-button-active,
        html.dark .rc-calendar-wrap .fc-button-primary:not(:disabled):active {
            background: rgba(255,255,255,0.18) !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-button {
            background: rgba(15, 23, 42, 0.08) !important;
            color: #0f172a !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-button:hover {
            background: rgba(15, 23, 42, 0.14) !important;
        }

        html:not(.dark) .rc-calendar-wrap .fc-button-primary:not(:disabled).fc-button-active,
        html:not(.dark) .rc-calendar-wrap .fc-button-primary:not(:disabled):active {
            background: rgba(15, 23, 42, 0.18) !important;
        }

        .rc-calendar-wrap .fc-event {
            border-radius: 9px !important;
            padding: 3px 8px !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            border-width: 0 !important;
        }

        .rc-calendar-wrap .fc-daygrid-event-dot {
            display: none !important;
        }

        .rc-card-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0 0 0.35rem 0;
        }

        .rc-card-subtitle {
            font-size: 0.95rem;
            margin: 0;
        }

        .rc-add-event-btn,
        .rc-save-event-btn,
        .rc-cancel-event-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 16px;
            padding: 0.9rem 1rem;
            font-size: 0.98rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .rc-add-event-btn,
        .rc-save-event-btn {
            width: 100%;
            margin-top: 1rem;
            background: #2563eb;
            color: #ffffff;
        }

        .rc-add-event-btn:hover,
        .rc-save-event-btn:hover {
            background: #1d4ed8;
        }

        .rc-cancel-event-btn {
            width: 100%;
            margin-top: 0.75rem;
            background: transparent;
        }

        html.dark .rc-cancel-event-btn {
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        html:not(.dark) .rc-cancel-event-btn {
            color: #334155;
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .rc-form-card {
            margin-top: 1rem;
            padding-top: 1rem;
        }

        html.dark .rc-form-card {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        html:not(.dark) .rc-form-card {
            border-top: 1px solid rgba(148, 163, 184, 0.16);
        }

        .rc-form-grid {
            display: grid;
            gap: 0.9rem;
        }

        .rc-form-field {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .rc-form-label {
            font-size: 0.9rem;
            font-weight: 700;
        }

        html.dark .rc-form-label {
            color: #e2e8f0;
        }

        html:not(.dark) .rc-form-label {
            color: #334155;
        }

        .rc-input,
        .rc-select,
        .rc-textarea {
            width: 100%;
            border-radius: 14px;
            padding: 0.85rem 0.95rem;
            outline: none;
            transition: all 0.18s ease;
        }

        .rc-textarea {
            min-height: 110px;
            resize: vertical;
        }

        html.dark .rc-input,
        html.dark .rc-select,
        html.dark .rc-textarea {
            background: rgba(255,255,255,0.04);
            color: #f8fafc;
            border: 1px solid rgba(148, 163, 184, 0.14);
        }

        html:not(.dark) .rc-input,
        html:not(.dark) .rc-select,
        html:not(.dark) .rc-textarea {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .rc-form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.9rem;
        }

        .rc-color-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .rc-color-swatch {
            height: 46px;
            border-radius: 16px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .rc-color-swatch:hover {
            transform: translateY(-1px);
        }

        .rc-color-swatch.is-active {
            border-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.35);
        }

        html.dark .rc-card-title,
        html.dark .rc-section-title,
        html.dark .rc-task-date,
        html.dark .rc-task-item-title,
        html.dark .rc-selected-date,
        html.dark .rc-event-meta {
            color: #f8fafc;
        }

        html.dark .rc-card-subtitle,
        html.dark .rc-selected-date-label,
        html.dark .rc-event-notes {
            color: #94a3b8;
        }

        html:not(.dark) .rc-card-title,
        html:not(.dark) .rc-section-title,
        html:not(.dark) .rc-task-date,
        html:not(.dark) .rc-task-item-title,
        html:not(.dark) .rc-selected-date,
        html:not(.dark) .rc-event-meta {
            color: #0f172a;
        }

        html:not(.dark) .rc-card-subtitle,
        html:not(.dark) .rc-selected-date-label,
        html:not(.dark) .rc-event-notes {
            color: #64748b;
        }

        .rc-selected-date-label {
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }

        .rc-selected-date {
            font-size: 1rem;
            font-weight: 800;
        }

        .rc-section-title {
            font-size: 1.02rem;
            font-weight: 800;
            margin-bottom: 0.9rem;
        }

        .rc-task-group {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }

        html.dark .rc-task-group {
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        html:not(.dark) .rc-task-group {
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
        }

        .rc-task-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none !important;
        }

        .rc-task-date {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .rc-task-list {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .rc-task-item {
            display: flex;
            gap: 0.78rem;
            align-items: flex-start;
            padding: 0.95rem 1rem;
            border-radius: 18px;
        }

        html.dark .rc-task-item {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(148, 163, 184, 0.10);
        }

        html:not(.dark) .rc-task-item {
            background: rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .rc-task-dot {
            width: 12px;
            height: 12px;
            min-width: 12px;
            border-radius: 999px;
            margin-top: 0.32rem;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.06);
        }

        .rc-task-item-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .rc-task-item-title {
            font-size: 0.98rem;
            line-height: 1.45;
            font-weight: 700;
        }

        .rc-event-meta {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .rc-event-notes {
            font-size: 0.85rem;
            line-height: 1.45;
        }

        .rc-empty-state {
            padding: 1rem;
            border-radius: 18px;
            text-align: center;
            font-size: 0.96rem;
        }

        html.dark .rc-empty-state {
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.10);
        }

        html:not(.dark) .rc-empty-state {
            background: rgba(15, 23, 42, 0.04);
            color: #64748b;
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        @media (max-width: 1280px) {
            .rc-page-shell {
                grid-template-columns: 1fr;
            }

            .rc-calendar-wrap {
                min-height: auto;
            }
        }

        @media (max-width: 768px) {
            .rc-global-title {
                min-width: auto;
                width: 100%;
                font-size: 1.6rem;
                padding: 14px 18px;
            }

            .rc-calendar-wrap,
            .rc-card {
                padding: 16px;
                border-radius: 24px;
            }

            .rc-calendar-wrap .fc-toolbar-title {
                font-size: 1.8rem !important;
            }

            .rc-form-row-2,
            .rc-color-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    <div class="rc-layout">
        <div class="rc-global-header">
            <div class="rc-global-title">
                Recruitment Calendar
            </div>
        </div>

        <div class="rc-page-shell">
            <div class="rc-main-column">
                <div
                    x-data="{
                        calendar: null,
                        events: @js($calendarEvents),
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
                                    height: 820,
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
                                    dayMaxEventRows: 2,
                                    moreLinkClick: (info) => {
                                        if ($wire && typeof $wire.onCalendarDateClick === 'function') {
                                            const dateStr = info.date.toISOString().slice(0, 10);
                                            $wire.onCalendarDateClick(dateStr);
                                        }
                                        return false;
                                    },
                                    dateClick: (info) => {
                                        if ($wire && typeof $wire.onCalendarDateClick === 'function') {
                                            $wire.onCalendarDateClick(info.dateStr);
                                        }
                                    },
                                    events: this.events,
                                });

                                this.calendar.render();
                            };

                            boot();

                            window.addEventListener('calendar-events-updated', (event) => {
                                const incoming = event.detail?.events ?? [];
                                this.events = incoming;

                                if (this.calendar) {
                                    this.calendar.removeAllEvents();
                                    this.calendar.addEventSource(incoming);
                                }
                            });

                            document.addEventListener('livewire:navigated', () => {
                                setTimeout(() => boot(), 150);
                            });
                        }
                    }"
                    x-init="initCalendar()"
                    wire:ignore
                >
                    <div class="rc-calendar-wrap">
                        <div x-ref="calendar"></div>
                    </div>
                </div>
            </div>

            <div class="rc-side-column">
                <div class="rc-side-stack">
                    @if ($selectedDate)
                        <div class="rc-card rc-selected-card">
                            <div class="rc-selected-date-label">Selected date</div>
                            <div class="rc-selected-date">{{ $selectedDateLabel }}</div>

                            <button type="button" class="rc-add-event-btn" wire:click="toggleAddEventForm">
                                Add Event
                            </button>

                            @if ($showAddEventForm)
                                <div class="rc-form-card">
                                    <div class="rc-form-grid">
                                        <div class="rc-form-field">
                                            <label class="rc-form-label">Title</label>
                                            <input type="text" wire:model.defer="eventForm.title" class="rc-input">
                                            @error('eventForm.title') <div class="rc-event-notes">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="rc-form-row-2">
                                            <div class="rc-form-field">
                                                <label class="rc-form-label">Type</label>
                                                <select wire:model.defer="eventForm.event_type" class="rc-select">
                                                    <option value="task">Task</option>
                                                    <option value="meeting">Meeting</option>
                                                    <option value="reminder">Reminder</option>
                                                    <option value="expiry">Expiry</option>
                                                    <option value="ticket">Ticket</option>
                                                    <option value="rotation">Rotation</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>

                                            <div class="rc-form-field">
                                                <label class="rc-form-label">Date</label>
                                                <input type="date" wire:model.defer="eventForm.event_date" class="rc-input">
                                            </div>
                                        </div>

                                        <div class="rc-form-field">
                                            <label class="rc-form-label">Job Opening</label>
                                            <select wire:model.defer="eventForm.job_id" class="rc-select">
                                                <option value="">General Event</option>
                                                @foreach ($jobOptions as $jobId => $jobTitle)
                                                    <option value="{{ $jobId }}">{{ $jobTitle }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="rc-form-field">
                                            <label class="rc-form-label">Choose Color</label>
                                            <div class="rc-color-grid">
                                                @foreach ($colorOptions as $color)
                                                    <button
                                                        type="button"
                                                        wire:click="setEventColor('{{ $color }}')"
                                                        class="rc-color-swatch {{ $eventForm['color'] === $color ? 'is-active' : '' }}"
                                                        style="background: {{ $color }};"
                                                    ></button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="rc-form-field">
                                            <label class="rc-form-label">Notes</label>
                                            <textarea wire:model.defer="eventForm.notes" class="rc-textarea"></textarea>
                                        </div>

                                        <button type="button" class="rc-save-event-btn" wire:click="saveEvent">
                                            Save Event
                                        </button>

                                        <button type="button" class="rc-cancel-event-btn" wire:click="toggleAddEventForm">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div style="margin-top: 1rem;">
                                <div class="rc-section-title">Events on this date</div>

                                @if (count($selectedDateEvents))
                                    <div class="rc-task-list">
                                        @foreach ($selectedDateEvents as $item)
                                            <div class="rc-task-item">
                                                <span
                                                    class="rc-task-dot"
                                                    style="background-color: {{ $item['backgroundColor'] }};"
                                                ></span>

                                                <div class="rc-task-item-content">
                                                    <div class="rc-task-item-title">
                                                        {{ $item['title'] }}
                                                    </div>

                                                    <div class="rc-event-meta">
                                                        @if (! empty($item['job_title']))
                                                            Linked to job: {{ $item['job_title'] }}
                                                        @else
                                                            General Event
                                                        @endif
                                                    </div>

                                                    @if (! empty($item['notes']))
                                                        <div class="rc-event-notes">
                                                            {{ $item['notes'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="rc-empty-state" style="margin-top: 1rem;">
                                        No events on this date.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="rc-card">
                        <h3 class="rc-card-title">Upcoming Tasks</h3>
                        <p class="rc-card-subtitle">From today to the next 30 days.</p>

                        <div style="margin-top: 1rem;">
                            @if (count($upcomingTaskGroups))
                                @foreach ($upcomingTaskGroups as $group)
                                    <div class="rc-task-group">
                                        <div class="rc-task-date">{{ $group['label'] }}</div>

                                        <div class="rc-task-list">
                                            @foreach ($group['items'] as $item)
                                                <div class="rc-task-item">
                                                    <span
                                                        class="rc-task-dot"
                                                        style="background-color: {{ $item['backgroundColor'] }};"
                                                    ></span>

                                                    <div class="rc-task-item-content">
                                                        <div class="rc-task-item-title">
                                                            {{ $item['title'] }}
                                                        </div>

                                                        <div class="rc-event-meta">
                                                            @if (! empty($item['job_title']))
                                                                Linked to job: {{ $item['job_title'] }}
                                                            @else
                                                                General Event
                                                            @endif
                                                        </div>

                                                        @if (! empty($item['notes']))
                                                            <div class="rc-event-notes">
                                                                {{ $item['notes'] }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="rc-empty-state">
                                    No upcoming tasks in the next 30 days.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>