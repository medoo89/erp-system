<div
    x-data="{
        events: @js($calendarEvents),
        selectedDate: '{{ now()->toDateString() }}',
        selectedLabel: '{{ now()->format('D, d M Y') }}',
        selectedItems: [],
        showManageModal: false,
        calendar: null,

        initCalendar() {
            this.setSelectedDate(this.selectedDate);

            this.calendar = new FullCalendar.Calendar(this.$refs.calendar, {
                initialView: 'dayGridMonth',
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.9,
                fixedWeekCount: false,
                showNonCurrentDates: true,
                headerToolbar: {
                    left: 'title',
                    center: '',
                    right: 'prev today next'
                },
                events: this.events,
                dayMaxEvents: true,
                navLinks: false,
                editable: false,
                selectable: false,
                eventDisplay: 'block',
                windowResizeDelay: 100,
                dateClick: (info) => {
                    this.setSelectedDate(info.dateStr);
                    $wire.set('eventForm.event_date', info.dateStr);
                    this.showManageModal = true;
                },
                eventClick: (info) => {
                    info.jsEvent.preventDefault();
                    this.setSelectedDate(info.event.startStr);
                    $wire.set('eventForm.event_date', info.event.startStr);
                    this.showManageModal = true;
                }
            });

            this.calendar.render();

            this.$nextTick(() => {
                setTimeout(() => {
                    this.calendar.updateSize();
                }, 150);
            });

            Livewire.on('calendar-events-updated', (payload) => {
                this.events = payload[0]?.events ?? payload.events ?? [];
                this.calendar.removeAllEvents();
                this.calendar.addEventSource(this.events);
                this.setSelectedDate(this.selectedDate);

                this.$nextTick(() => {
                    setTimeout(() => {
                        this.calendar.updateSize();
                    }, 100);
                });
            });
        },

        setSelectedDate(dateString) {
            this.selectedDate = dateString;

            const date = new Date(dateString + 'T00:00:00');
            this.selectedLabel = date.toLocaleDateString('en-GB', {
                weekday: 'short',
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });

            this.selectedItems = this.events.filter(event => event.start === dateString);
        },

        openTodayTasks() {
            const today = '{{ now()->toDateString() }}';
            this.setSelectedDate(today);
            $wire.set('eventForm.event_date', today);

            if (this.calendar) {
                this.calendar.today();
            }

            this.showManageModal = true;
        },

        openAddEventModal() {
            const current = this.selectedDate || '{{ now()->toDateString() }}';
            this.setSelectedDate(current);
            $wire.set('eventForm.event_date', current);
            this.showManageModal = true;
        }
    }"
    x-init="initCalendar()"
    class="sada-calendar-page"
>
    <div class="sada-calendar-page-header sada-calendar-hero">
        <div class="sada-calendar-badge">SADA FEZZAN ERP</div>

        <h1 class="sada-calendar-title">Recruitment Calendar</h1>

        <p class="sada-calendar-subtitle">
            Track job expiries, operational tasks, upcoming activities, and recruitment deadlines from one central control page.
        </p>

        <div class="sada-calendar-hero-actions">
            <button type="button" class="sada-calendar-pill-btn" @click="openTodayTasks()">
                <span class="sada-calendar-pill-label">Today</span>
                <span class="sada-calendar-pill-value">{{ now()->format('l, d M Y') }}</span>
            </button>

            <button type="button" class="fi-btn fi-btn-color-primary" @click="openAddEventModal()">
                Add Event
            </button>

            <a
                href="{{ route('recruitment-calendar.public') }}"
                target="_blank"
                class="fi-btn sada-public-calendar-btn"
            >
                Open Public Calendar
            </a>
        </div>
    </div>

    <div class="sada-calendar-schedule-row">
        <div class="sada-calendar-schedule-main">
            <div class="sada-calendar-card">
                <div x-ref="calendar"></div>
            </div>
        </div>

        <div class="sada-calendar-schedule-side">
            <div class="sada-calendar-side-card">
                <div class="sada-calendar-side-top">
                    <div>
                        <div class="sada-calendar-side-badge">Selected Date</div>
                        <h3 class="sada-calendar-side-title" x-text="selectedLabel"></h3>
                    </div>

                    <button type="button" class="fi-btn fi-btn-color-primary" @click="showManageModal = true">
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
                                    <div class="sada-side-item-notes" x-text="item.notes"></div>
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
                                            <div class="sada-upcoming-notes">{{ $item['notes'] }}</div>
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
        </div>
    </div>

    <div x-show="showManageModal" x-transition.opacity class="sada-calendar-modal-backdrop" style="display:none;">
        <div class="sada-calendar-modal sada-calendar-manage-modal" @click.outside="showManageModal = false">
            <div class="sada-calendar-modal-top">
                <div>
                    <div class="sada-calendar-side-badge">Day Management</div>
                    <h3 class="sada-calendar-side-title" x-text="selectedLabel"></h3>
                </div>

                <button type="button" class="sada-calendar-modal-close" @click="showManageModal = false">✕</button>
            </div>

            <div class="sada-manage-layout">
                <div class="sada-manage-column">
                    <div class="sada-manage-section-title">Tasks in This Day</div>

                    <div class="sada-calendar-modal-list">
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
                </div>

                <div class="sada-manage-column sada-manage-form-column">
                    <div class="sada-manage-section-title">Add Event</div>

                    <div class="sada-add-form sada-add-form-inline">
                        <div class="sada-form-grid">
                            <div>
                                <label class="sada-form-label">Title</label>
                                <input type="text" wire:model.defer="eventForm.title" class="fi-input w-full" placeholder="Event title">
                            </div>

                            <div>
                                <label class="sada-form-label">Type</label>
                                <select wire:model.defer="eventForm.event_type" class="fi-select-input w-full">
                                    @foreach ($eventTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="sada-form-label">Date</label>
                                <input type="date" wire:model.defer="eventForm.event_date" class="fi-input w-full">
                            </div>

                            <div>
                                <label class="sada-form-label">Linked Job</label>
                                <select wire:model.defer="eventForm.job_id" class="fi-select-input w-full">
                                    <option value="">General Event</option>
                                    @foreach ($jobOptions as $jobId => $jobTitle)
                                        <option value="{{ $jobId }}">{{ $jobTitle }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sada-form-full">
                                <label class="sada-form-label">Color</label>
                                <div class="sada-color-options">
                                    @foreach ($colorOptions as $color)
                                        <button
                                            type="button"
                                            wire:click="$set('eventForm.color', '{{ $color }}')"
                                            class="sada-color-button"
                                            style="background: {{ $color }}"
                                        ></button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="sada-form-full">
                                <label class="sada-form-label">Notes</label>
                                <textarea wire:model.defer="eventForm.notes" class="fi-textarea w-full" rows="5" placeholder="Notes"></textarea>
                            </div>
                        </div>

                        <div class="sada-form-actions">
                            <button type="button" class="fi-btn fi-btn-color-gray" @click="showManageModal = false">
                                Cancel
                            </button>

                            <button
                                type="button"
                                class="fi-btn fi-btn-color-primary"
                                wire:click="saveEvent"
                            >
                                Save Event
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>