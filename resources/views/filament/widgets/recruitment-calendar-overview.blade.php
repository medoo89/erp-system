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
                    if (!this.$refs.calendar || typeof FullCalendar === 'undefined') {
                        return
                    }

                    if (this.calendar) {
                        this.calendar.destroy()
                        this.calendar = null
                    }

                    this.calendar = new FullCalendar.Calendar(this.$refs.calendar, {
                        initialView: 'dayGridMonth',
                        height: 'auto',
                        contentHeight: 'auto',
                        aspectRatio: 1.85,
                        fixedWeekCount: false,
                        showNonCurrentDates: true,
                        expandRows: true,
                        headerToolbar: {
                            left: 'title',
                            center: '',
                            right: 'prev today next',
                        },
                        events: this.events,
                        dayMaxEvents: true,
                        navLinks: false,
                        editable: false,
                        selectable: false,
                        eventDisplay: 'block',
                        windowResizeDelay: 100,
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
                        if (this.calendar) {
                            this.calendar.updateSize()
                        }
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
                    if (this.calendar) {
                        this.calendar.updateSize()
                    }
                }, 200)
            })
        },
    }"
    x-init="init()"
    class="sada-calendar-page"
>
    <div class="sada-calendar-page-header">
        <div class="sada-calendar-hero">
            <div class="sada-calendar-badge">SADA FEZZAN ERP</div>

            <h2 class="sada-calendar-title">Recruitment Calendar</h2>

            <p class="sada-calendar-subtitle">
                Monitor job expiries and operational events in one central calendar view.
            </p>

            <div class="sada-calendar-hero-actions">
                <button type="button" class="sada-calendar-pill-btn" @click="openTodayTasks()">
                    <span class="sada-calendar-pill-label">Today</span>
                    <span class="sada-calendar-pill-value">{{ $todayLabel }}</span>
                </button>
            </div>
        </div>
    </div>

    <div class="sada-calendar-card" style="padding: 1.5rem;">
        <div class="sada-calendar-layout">
            <div class="sada-calendar-main" wire:ignore>
                <div x-ref="calendar"></div>
            </div>

            <aside class="sada-calendar-sidebar">
                <div class="sada-calendar-side-card">
                    <div class="sada-calendar-side-top">
                        <div>
                            <div class="sada-calendar-side-badge">Selected Date</div>
                            <h3 class="sada-calendar-side-title" x-text="selectedLabel"></h3>
                        </div>

                        <button type="button" class="fi-btn fi-btn-color-primary" @click="openDayModal()">
                            View Day
                        </button>
                    </div>

                    <div class="sada-calendar-side-list">
                        <template x-if="selectedItems.length === 0">
                            <div class="sada-empty-box">No events on this selected date.</div>
                        </template>

                        <template x-for="(item, index) in selectedItems" :key="index">
                            <div class="sada-side-item">
                                <div class="sada-side-dot" :style="`background:${item.backgroundColor}`"></div>

                                <div class="sada-side-content">
                                    <div class="sada-side-item-title" x-text="item.title"></div>

                                    <template x-if="item.job_title">
                                        <div class="sada-side-item-meta" x-text="'Linked to job: ' + item.job_title"></div>
                                    </template>

                                    <template x-if="item.notes">
                                        <div class="sada-side-item-meta" x-text="item.notes"></div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="sada-calendar-side-card">
                    <div class="sada-calendar-side-badge">Next Events</div>
                    <h3 class="sada-calendar-side-title">Upcoming Tasks</h3>
                    <p class="sada-calendar-side-text">From today to the next 30 days.</p>

                    <div class="sada-upcoming-groups">
                        @forelse ($upcomingTaskGroups as $group)
                            <div class="sada-upcoming-group">
                                <div class="sada-upcoming-date">{{ $group['label'] }}</div>

                                @foreach ($group['items'] as $item)
                                    <div class="sada-upcoming-item">
                                        <div class="sada-upcoming-dot" style="background: {{ $item['backgroundColor'] }}"></div>

                                        <div class="sada-upcoming-content">
                                            <div class="sada-upcoming-title">{{ $item['title'] }}</div>

                                            @if (! empty($item['job_title']))
                                                <div class="sada-upcoming-meta">Linked to job: {{ $item['job_title'] }}</div>
                                            @endif

                                            @if (! empty($item['notes']))
                                                <div class="sada-upcoming-meta">{{ $item['notes'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="sada-empty-box">No upcoming tasks in the next 30 days.</div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <template x-teleport="body">
        <div
            x-show="showDayModal"
            x-transition.opacity
            class="sada-calendar-modal-backdrop"
            style="display: none;"
            @click.self="closeDayModal()"
        >
            <div class="sada-calendar-modal">
                <div class="sada-calendar-modal-top">
                    <div>
                        <div class="sada-calendar-side-badge">Day Overview</div>
                        <h3 class="sada-calendar-side-title" x-text="selectedLabel"></h3>
                    </div>

                    <button type="button" class="sada-calendar-modal-close" @click="closeDayModal()">✕</button>
                </div>

                <div class="sada-calendar-modal-list" style="padding: 1.2rem;">
                    <template x-if="selectedItems.length === 0">
                        <div class="sada-empty-box">No events on this selected date.</div>
                    </template>

                    <template x-for="(item, index) in selectedItems" :key="index">
                        <div class="sada-calendar-modal-item">
                            <div class="sada-calendar-modal-item-dot" :style="`background:${item.backgroundColor}`"></div>

                            <div class="sada-calendar-modal-item-content">
                                <div class="sada-calendar-modal-item-title" x-text="item.title"></div>

                                <template x-if="item.job_title">
                                    <div class="sada-calendar-modal-item-notes" x-text="'Linked to job: ' + item.job_title"></div>
                                </template>

                                <template x-if="item.notes">
                                    <div class="sada-calendar-modal-item-notes" x-text="item.notes"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div style="padding: 0 1.2rem 1.2rem; display: flex; justify-content: flex-end;">
                    <button type="button" class="fi-btn fi-btn-color-gray" @click="closeDayModal()">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>