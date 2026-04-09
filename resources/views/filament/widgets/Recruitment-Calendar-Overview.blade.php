<x-filament-widgets::widget>
    <style>
        .recruitment-calendar-shell {
            margin-top: 4px;
        }

        .recruitment-calendar-header {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 18px;
        }

        .recruitment-calendar-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 16px 28px;
            border-radius: 22px;
            font-size: 2.35rem;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.03em;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            box-shadow:
                0 10px 30px rgba(15, 23, 42, 0.10),
                inset 0 1px 0 rgba(255, 255, 255, 0.10);
            transition: all 0.2s ease;
        }

        html.dark .recruitment-calendar-title {
            color: #f8fafc;
            background: rgba(15, 23, 42, 0.78);
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        html:not(.dark) .recruitment-calendar-title {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.22);
        }

        .recruitment-calendar-wrap {
            border-radius: 28px;
            overflow: hidden;
            padding: 20px;
            min-height: 820px;
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        html.dark .recruitment-calendar-wrap {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            border: 1px solid rgba(148, 163, 184, 0.10);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
        }

        html:not(.dark) .recruitment-calendar-wrap {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow:
                0 10px 30px rgba(15, 23, 42, 0.06),
                inset 0 1px 0 rgba(255,255,255,0.80);
        }

        .recruitment-calendar-wrap .fc {
            font-family: 'Gilroy', Inter, system-ui, sans-serif;
        }

        html.dark .recruitment-calendar-wrap .fc {
            color: #e5e7eb;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc {
            color: #0f172a;
        }

        html.dark .recruitment-calendar-wrap .fc-theme-standard td,
        html.dark .recruitment-calendar-wrap .fc-theme-standard th,
        html.dark .recruitment-calendar-wrap .fc-scrollgrid,
        html.dark .recruitment-calendar-wrap .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.16) !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-theme-standard td,
        html:not(.dark) .recruitment-calendar-wrap .fc-theme-standard th,
        html:not(.dark) .recruitment-calendar-wrap .fc-scrollgrid,
        html:not(.dark) .recruitment-calendar-wrap .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.22) !important;
        }

        .recruitment-calendar-wrap .fc-toolbar-title {
            font-size: 2.4rem !important;
            line-height: 1.1 !important;
            font-weight: 800 !important;
        }

        html.dark .recruitment-calendar-wrap .fc-toolbar-title {
            color: #f8fafc !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-toolbar-title {
            color: #0f172a !important;
        }

        .recruitment-calendar-wrap .fc-col-header-cell-cushion {
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 12px 0 !important;
        }

        html.dark .recruitment-calendar-wrap .fc-col-header-cell-cushion {
            color: #cbd5e1 !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-col-header-cell-cushion {
            color: #475569 !important;
        }

        .recruitment-calendar-wrap .fc-daygrid-day-number {
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 10px !important;
        }

        html.dark .recruitment-calendar-wrap .fc-daygrid-day-number {
            color: #f8fafc !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-daygrid-day-number {
            color: #0f172a !important;
        }

        html.dark .recruitment-calendar-wrap .fc-day-other .fc-daygrid-day-number {
            color: #64748b !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-day-other .fc-daygrid-day-number {
            color: #94a3b8 !important;
        }

        .recruitment-calendar-wrap .fc-button {
            border: none !important;
            border-radius: 999px !important;
            padding: 0.65rem 1rem !important;
            box-shadow: none !important;
            transition: background 0.2s ease, color 0.2s ease;
        }

        html.dark .recruitment-calendar-wrap .fc-button {
            background: rgba(255,255,255,0.08) !important;
            color: #ffffff !important;
        }

        html.dark .recruitment-calendar-wrap .fc-button:hover {
            background: rgba(255,255,255,0.14) !important;
        }

        html.dark .recruitment-calendar-wrap .fc-button-primary:not(:disabled).fc-button-active,
        html.dark .recruitment-calendar-wrap .fc-button-primary:not(:disabled):active {
            background: rgba(255,255,255,0.18) !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-button {
            background: rgba(15, 23, 42, 0.08) !important;
            color: #0f172a !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-button:hover {
            background: rgba(15, 23, 42, 0.14) !important;
        }

        html:not(.dark) .recruitment-calendar-wrap .fc-button-primary:not(:disabled).fc-button-active,
        html:not(.dark) .recruitment-calendar-wrap .fc-button-primary:not(:disabled):active {
            background: rgba(15, 23, 42, 0.18) !important;
        }

        .recruitment-calendar-wrap .fc-event {
            border-radius: 8px !important;
            padding: 2px 6px !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            border-width: 0 !important;
        }

        .recruitment-calendar-wrap .fc-daygrid-event-dot {
            display: none !important;
        }
    </style>

    <div class="recruitment-calendar-shell">
        <div class="recruitment-calendar-header">
            <div class="recruitment-calendar-title">
                Recruitment Calendar
            </div>
        </div>

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
            <div class="recruitment-calendar-wrap">
                <div x-ref="calendar"></div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>