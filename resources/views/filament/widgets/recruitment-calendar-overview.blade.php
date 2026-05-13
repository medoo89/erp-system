<div
    x-data="{
        events: @js($calendarEvents),
        selectedDate: '{{ now()->toDateString() }}',
        selectedLabel: '{{ now()->format('D, d M Y') }}',
        selectedItems: [],
        showDayModal: false,
        calendar: null,

        init() {
            this.setSelectedDate(this.selectedDate)
            this.initCalendar()
        },

        initCalendar() {
            this.$nextTick(() => {
                setTimeout(() => {
                    if (!this.$refs.calendar || typeof FullCalendar === 'undefined') return

                    if (this.calendar) {
                        this.calendar.destroy()
                        this.calendar = null
                    }

                    this.calendar = new FullCalendar.Calendar(this.$refs.calendar, {
                        initialView: 'dayGridMonth',
                        height: 'auto',
                        contentHeight: 'auto',
                        aspectRatio: 1.55,
                        fixedWeekCount: false,
                        showNonCurrentDates: true,
                        expandRows: true,
                        headerToolbar: {
                            left: '',
                            center: 'prev title next today',
                            right: '',
                        },
                        events: this.events,
                        dayMaxEvents: true,
                        navLinks: false,
                        editable: false,
                        selectable: false,
                        eventDisplay: 'block',
                        dateClick: (info) => {
                            this.setSelectedDate(info.dateStr)
                            this.openDayModal()
                        },
                        eventClick: (info) => {
                            info.jsEvent.preventDefault()
                            this.setSelectedDate(info.event.startStr)
                            this.openDayModal()
                        },
                    })

                    this.calendar.render()

                    setTimeout(() => {
                        if (this.calendar) this.calendar.updateSize()
                    }, 250)
                }, 200)
            })
        },

        setSelectedDate(dateString) {
            this.selectedDate = dateString

            const date = new Date(dateString + 'T00:00:00')
            this.selectedLabel = date.toLocaleDateString('en-GB', {
                weekday: 'short',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
            })

            this.selectedItems = this.events.filter(event => event.start === dateString)
        },

        openTodayTasks() {
            const today = '{{ now()->toDateString() }}'
            this.setSelectedDate(today)

            if (this.calendar) {
                this.calendar.today()
                this.calendar.updateSize()
            }

            this.openDayModal()
        },

        openDayModal() {
            this.showDayModal = true
            document.body.classList.add('overflow-hidden')
        },

        closeDayModal() {
            this.showDayModal = false
            document.body.classList.remove('overflow-hidden')

            this.$nextTick(() => {
                setTimeout(() => {
                    if (this.calendar) this.calendar.updateSize()
                }, 200)
            })
        },
    }"
    x-init="init()"
    class="sf-md3-calendar"
>
    <style>
        .sf-md3-calendar {
            margin-top: 18px;
        }

        .sf-md3-calendar-shell {
            border-radius: 34px;
            padding: 22px;
            background:
                radial-gradient(circle at top right, rgba(20, 184, 166, .10), transparent 34%),
                rgba(255,255,255,.96);
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 18px 48px rgba(15,23,42,.07);
        }

        .dark .sf-md3-calendar-shell {
            background:
                radial-gradient(circle at top right, rgba(20,184,166,.13), transparent 34%),
                rgba(15,23,42,.72);
            border-color: rgba(148,163,184,.18);
            box-shadow: 0 18px 48px rgba(0,0,0,.18);
        }

        .sf-md3-calendar-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .sf-md3-kicker {
            width: fit-content;
            border-radius: 999px;
            padding: 7px 11px;
            background: #eef6ff;
            color: #234b74;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .dark .sf-md3-kicker {
            background: rgba(59,130,246,.14);
            color: #bfdbfe;
        }

        .sf-md3-title {
            margin: 0;
            color: #0f172a;
            font-size: clamp(30px, 3vw, 46px);
            line-height: .95;
            font-weight: 950;
            letter-spacing: -.06em;
        }

        .dark .sf-md3-title {
            color: #ffffff;
        }

        .sf-md3-subtitle {
            margin: 10px 0 0;
            color: #64748b;
            font-size: 14px;
            font-weight: 650;
            line-height: 1.55;
        }

        .dark .sf-md3-subtitle {
            color: #94a3b8;
        }

        .sf-md3-today {
            border: 0;
            border-radius: 999px;
            padding: 12px 16px;
            cursor: pointer;
            background: #0f172a;
            color: #fff;
            font-weight: 900;
            box-shadow: 0 14px 28px rgba(15,23,42,.14);
        }

        .dark .sf-md3-today {
            background: #2563eb;
        }

        .sf-md3-calendar-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(340px, .65fr);
            gap: 18px;
            align-items: stretch;
        }

        .sf-md3-calendar-main,
        .sf-md3-calendar-side {
            min-width: 0;
            border-radius: 30px;
            background: rgba(248,250,252,.78);
            border: 1px solid rgba(15,23,42,.08);
            padding: 16px;
        }

        .dark .sf-md3-calendar-main,
        .dark .sf-md3-calendar-side {
            background: rgba(15,23,42,.42);
            border-color: rgba(148,163,184,.16);
        }

        .sf-md3-calendar-main {
            min-height: 690px;
        }

        .sf-md3-calendar-side {
            height: 690px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sf-md3-scroll-events {
            flex: 1 1 auto;
            overflow-y: auto;
            padding-right: 6px;
            display: grid;
            gap: 12px;
            align-content: start;
        }

        .sf-md3-scroll-events::-webkit-scrollbar {
            width: 8px;
        }

        .sf-md3-scroll-events::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: rgba(148,163,184,.55);
        }

        .sf-md3-event-date {
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #64748b;
            margin: 6px 0;
        }

        .dark .sf-md3-event-date {
            color: #94a3b8;
        }

        .sf-md3-event-card {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            border-radius: 22px;
            padding: 14px;
            background: color-mix(in srgb, var(--event-color, #2563eb) 11%, #ffffff);
            border: 1px solid color-mix(in srgb, var(--event-color, #2563eb) 26%, transparent);
        }

        .dark .sf-md3-event-card {
            background: color-mix(in srgb, var(--event-color, #2563eb) 19%, rgba(15,23,42,.70));
            border-color: color-mix(in srgb, var(--event-color, #2563eb) 32%, transparent);
        }

        .sf-md3-event-dot {
            width: 12px;
            height: 12px;
            margin-top: 5px;
            border-radius: 999px;
            background: var(--event-color, #2563eb);
            box-shadow: 0 0 0 5px color-mix(in srgb, var(--event-color, #2563eb) 16%, transparent);
            flex: 0 0 auto;
        }

        .sf-md3-event-title {
            color: #0f172a;
            font-size: 13px;
            font-weight: 950;
            line-height: 1.35;
        }

        .dark .sf-md3-event-title {
            color: #fff;
        }

        .sf-md3-event-meta {
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.45;
        }

        .dark .sf-md3-event-meta {
            color: #94a3b8;
        }

        .sf-md3-calendar .fc {
            min-height: 640px;
        }

        .sf-md3-calendar .fc .fc-toolbar {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 10px !important;
            margin-bottom: 18px !important;
            flex-wrap: wrap !important;
        }

        .sf-md3-calendar .fc .fc-toolbar-chunk {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .sf-md3-calendar .fc .fc-toolbar-title {
            color: #0f172a;
            text-align: center;
            font-weight: 950;
            letter-spacing: -.04em;
        }

        .dark .sf-md3-calendar .fc .fc-toolbar-title {
            color: #fff;
        }

        .sf-md3-calendar .fc .fc-button {
            border: 0 !important;
            border-radius: 999px !important;
            background: #eef6ff !important;
            color: #234b74 !important;
            font-weight: 900 !important;
            box-shadow: none !important;
        }

        .dark .sf-md3-calendar .fc .fc-button {
            background: rgba(255,255,255,.08) !important;
            color: #e2e8f0 !important;
        }

        .sf-md3-calendar .fc-event {
            border: 0 !important;
            border-radius: 999px !important;
            padding: 2px 7px !important;
            font-weight: 850 !important;
            font-size: 11px !important;
        }

        .sf-md3-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15,23,42,.58);
            display: grid;
            place-items: center;
            padding: 20px;
        }

        .sf-md3-modal {
            width: min(620px, 100%);
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(15,23,42,.08);
            box-shadow: 0 30px 80px rgba(0,0,0,.24);
            overflow: hidden;
        }

        .dark .sf-md3-modal {
            background: #0f172a;
            border-color: rgba(148,163,184,.18);
        }

        .sf-md3-modal-head {
            padding: 18px 20px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            border-bottom: 1px solid rgba(15,23,42,.08);
        }

        .dark .sf-md3-modal-head {
            border-color: rgba(148,163,184,.16);
        }

        .sf-md3-modal-title {
            color: #0f172a;
            font-weight: 950;
            font-size: 20px;
            letter-spacing: -.04em;
        }

        .dark .sf-md3-modal-title {
            color: #fff;
        }

        .sf-md3-modal-close {
            border: 0;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            cursor: pointer;
            background: #eef6ff;
            color: #234b74;
            font-weight: 950;
        }

        .sf-md3-modal-body {
            padding: 18px;
            display: grid;
            gap: 10px;
        }

        .sf-md3-empty {
            border-radius: 18px;
            padding: 16px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 800;
        }

        .dark .sf-md3-empty {
            background: rgba(255,255,255,.06);
            color: #94a3b8;
        }

        @media (max-width: 1100px) {
            .sf-md3-calendar-grid {
                grid-template-columns: 1fr;
            }

            .sf-md3-calendar-main {
                min-height: auto;
            }

            .sf-md3-calendar-side {
                height: auto;
                max-height: 520px;
            }

            .sf-md3-scroll-events {
                max-height: 420px;
            }
        }
    </style>

    <section class="sf-md3-calendar-shell">
        <div class="sf-md3-calendar-head">
            <div>
                <div class="sf-md3-kicker">SADA FEZZAN ERP</div>
                <h2 class="sf-md3-title">Calendar Overview</h2>
                <p class="sf-md3-subtitle">Recruitment, employment, rotation, travel, mobilization, and expiring documents in one operational view.</p>
            </div>

            <button type="button" class="sf-md3-today" @click="openTodayTasks()">
                Today · {{ $todayLabel }}
            </button>
        </div>

        <div class="sf-md3-calendar-grid">
            <div class="sf-md3-calendar-main" wire:ignore>
                <div x-ref="calendar"></div>
            </div>

            <aside class="sf-md3-calendar-side">
                <div class="sf-md3-kicker">Next Events</div>

                <div class="sf-md3-scroll-events">
                    @forelse ($upcomingTaskGroups as $group)
                        <div>
                            <div class="sf-md3-event-date">{{ $group['label'] }}</div>

                            @foreach ($group['items'] as $item)
                                <div class="sf-md3-event-card" style="--event-color: {{ $item['backgroundColor'] ?? '#2563eb' }};">
                                    <div class="sf-md3-event-dot"></div>
                                    <div>
                                        <div class="sf-md3-event-title">{{ $item['title'] }}</div>

                                        @if (! empty($item['job_title']))
                                            <div class="sf-md3-event-meta">Linked to job: {{ $item['job_title'] }}</div>
                                        @endif

                                        @if (! empty($item['notes']))
                                            <div class="sf-md3-event-meta">{{ $item['notes'] }}</div>
                                        @endif

                                        @if (! empty($item['type']))
                                            <div class="sf-md3-event-meta">{{ ucfirst(str_replace('_', ' ', $item['type'])) }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="sf-md3-empty">No upcoming tasks in the next 45 days.</div>
                    @endforelse
                </div>
            </aside>
        </div>
    </section>

    <template x-teleport="body">
        <div
            x-show="showDayModal"
            x-transition.opacity
            class="sf-md3-modal-backdrop"
            style="display: none;"
            @click.self="closeDayModal()"
        >
            <div class="sf-md3-modal">
                <div class="sf-md3-modal-head">
                    <div>
                        <div class="sf-md3-kicker">Day Overview</div>
                        <div class="sf-md3-modal-title" x-text="selectedLabel"></div>
                    </div>

                    <button type="button" class="sf-md3-modal-close" @click="closeDayModal()">×</button>
                </div>

                <div class="sf-md3-modal-body">
                    <template x-if="selectedItems.length === 0">
                        <div class="sf-md3-empty">No events on this selected date.</div>
                    </template>

                    <template x-for="(item, index) in selectedItems" :key="index">
                        <div class="sf-md3-event-card" :style="`--event-color:${item.backgroundColor || '#2563eb'}`">
                            <div class="sf-md3-event-dot"></div>
                            <div>
                                <div class="sf-md3-event-title" x-text="item.title"></div>

                                <template x-if="item.job_title">
                                    <div class="sf-md3-event-meta" x-text="'Linked to job: ' + item.job_title"></div>
                                </template>

                                <template x-if="item.notes">
                                    <div class="sf-md3-event-meta" x-text="item.notes"></div>
                                </template>

                                <template x-if="item.type">
                                    <div class="sf-md3-event-meta" x-text="String(item.type).replaceAll('_', ' ')"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>
