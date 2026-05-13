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
            this.buildCalendar();

            Livewire.on('calendar-events-updated', (payload) => {
                this.events = payload[0]?.events ?? payload.events ?? [];
                this.setSelectedDate(this.selectedDate);
                this.rebuildCalendar();
            });
        },

        buildCalendar() {
            this.$nextTick(() => {
                setTimeout(() => {
                    if (!this.$refs.calendar || typeof FullCalendar === 'undefined') {
                        return;
                    }

                    if (this.calendar) {
                        try {
                            this.calendar.destroy();
                        } catch (e) {}
                        this.calendar = null;
                    }

                    this.$refs.calendar.innerHTML = '';

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

                    setTimeout(() => {
                        if (this.calendar) {
                            this.calendar.updateSize();
                        }
                    }, 150);
                }, 50);
            });
        },

        rebuildCalendar() {
            this.$nextTick(() => {
                setTimeout(() => {
                    this.buildCalendar();
                }, 120);
            });
        },

        closeManageModal() {
            this.showManageModal = false;

            this.$nextTick(() => {
                setTimeout(() => {
                    this.rebuildCalendar();
                }, 150);

                setTimeout(() => {
                    this.rebuildCalendar();
                }, 500);
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
        <div class="sada-calendar-modal sada-calendar-manage-modal" @click.outside="closeManageModal()">
            <div class="sada-calendar-modal-top">
                <div>
                    <div class="sada-calendar-side-badge">Day Management</div>
                    <h3 class="sada-calendar-side-title" x-text="selectedLabel"></h3>
                </div>

                <button type="button" class="sada-calendar-modal-close" @click="closeManageModal()">✕</button>
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
                            <button type="button" class="fi-btn fi-btn-color-gray" @click="closeManageModal()">
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

<style id="sf-recruitment-calendar-modal-hard-fix-final">
    /*
     | Recruitment Calendar modal hard fix
     | Prevent layout/footer/sidebar CSS from breaking the day modal.
     */
    .sada-calendar-modal-backdrop {
        position: fixed !important;
        inset: 0 !important;
        z-index: 99999 !important;
        width: 100vw !important;
        height: 100vh !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 28px !important;
        background: rgba(15, 23, 42, .48) !important;
        backdrop-filter: blur(16px) !important;
        overflow-y: auto !important;
    }

    .sada-calendar-modal-backdrop[style*="display:none"],
    .sada-calendar-modal-backdrop[style*="display: none"] {
        display: none !important;
    }

    .sada-calendar-modal {
        position: relative !important;
        width: min(1120px, calc(100vw - 56px)) !important;
        max-width: min(1120px, calc(100vw - 56px)) !important;
        max-height: calc(100vh - 56px) !important;
        overflow: auto !important;
        margin: auto !important;
        border-radius: 30px !important;
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.12), transparent 38%),
            rgba(255,255,255,.98) !important;
        border: 1px solid rgba(148, 163, 184, .28) !important;
        box-shadow: 0 34px 90px rgba(15,23,42,.28) !important;
        color: #0f172a !important;
        transform: none !important;
    }

    .sada-calendar-manage-modal {
        width: min(1120px, calc(100vw - 56px)) !important;
        max-width: min(1120px, calc(100vw - 56px)) !important;
    }

    .sada-calendar-modal-top {
        position: sticky !important;
        top: 0 !important;
        z-index: 5 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
        padding: 20px 22px !important;
        background: rgba(255,255,255,.92) !important;
        border-bottom: 1px solid rgba(148,163,184,.22) !important;
        backdrop-filter: blur(18px) !important;
    }

    .sada-calendar-modal-close {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        border-radius: 999px !important;
        border: 0 !important;
        background: #fee2e2 !important;
        color: #dc2626 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 18px !important;
        font-weight: 950 !important;
        cursor: pointer !important;
        box-shadow: 0 12px 28px rgba(220,38,38,.12) !important;
    }

    .sada-manage-layout {
        display: grid !important;
        grid-template-columns: minmax(280px, .85fr) minmax(420px, 1.15fr) !important;
        gap: 18px !important;
        padding: 20px 22px 22px !important;
        align-items: start !important;
    }

    .sada-manage-column {
        min-width: 0 !important;
        border-radius: 24px !important;
        background: rgba(248,250,252,.86) !important;
        border: 1px solid rgba(148,163,184,.22) !important;
        padding: 16px !important;
    }

    .sada-manage-form-column {
        background: rgba(255,255,255,.90) !important;
    }

    .sada-manage-section-title {
        color: #123a59 !important;
        font-size: 15px !important;
        font-weight: 950 !important;
        margin-bottom: 12px !important;
    }

    .sada-add-form,
    .sada-add-form-inline {
        width: 100% !important;
    }

    .sada-form-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 14px !important;
        width: 100% !important;
    }

    .sada-form-full {
        grid-column: 1 / -1 !important;
    }

    .sada-form-label {
        display: block !important;
        margin-bottom: 7px !important;
        color: #334155 !important;
        font-size: 10px !important;
        font-weight: 950 !important;
        letter-spacing: .10em !important;
        text-transform: uppercase !important;
    }

    .sada-add-form input,
    .sada-add-form select,
    .sada-add-form textarea,
    .sada-add-form .fi-input,
    .sada-add-form .fi-select-input,
    .sada-add-form .fi-textarea {
        width: 100% !important;
        min-height: 44px !important;
        border-radius: 16px !important;
        border: 1px solid rgba(148,163,184,.30) !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 0 14px !important;
        font-size: 13px !important;
        font-weight: 750 !important;
        box-shadow: 0 10px 24px rgba(15,23,42,.04) !important;
    }

    .sada-add-form textarea,
    .sada-add-form .fi-textarea {
        min-height: 110px !important;
        padding-top: 12px !important;
        resize: vertical !important;
    }

    .sada-color-options {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        align-items: center !important;
    }

    .sada-color-button {
        width: 30px !important;
        height: 30px !important;
        border-radius: 999px !important;
        border: 3px solid #ffffff !important;
        box-shadow: 0 0 0 1px rgba(15,23,42,.12), 0 8px 18px rgba(15,23,42,.10) !important;
        cursor: pointer !important;
    }

    .sada-form-actions {
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 10px !important;
        margin-top: 16px !important;
        padding-top: 16px !important;
        border-top: 1px solid rgba(148,163,184,.20) !important;
    }

    .sada-form-actions .fi-btn,
    .sada-calendar-modal .fi-btn {
        min-height: 42px !important;
        border-radius: 999px !important;
        padding: 0 18px !important;
        font-weight: 900 !important;
    }

    .sada-empty-box {
        border-radius: 18px !important;
        padding: 16px !important;
        background: rgba(248,250,252,.90) !important;
        border: 1px dashed rgba(148,163,184,.40) !important;
        color: #64748b !important;
        font-weight: 800 !important;
        text-align: center !important;
    }

    body.overflow-hidden {
        overflow: hidden !important;
    }

    .dark .sada-calendar-modal {
        background:
            radial-gradient(circle at top right, rgba(20,184,166,.14), transparent 38%),
            rgba(15,23,42,.96) !important;
        border-color: rgba(148,163,184,.18) !important;
        color: #f8fafc !important;
    }

    .dark .sada-calendar-modal-top {
        background: rgba(15,23,42,.90) !important;
        border-bottom-color: rgba(148,163,184,.16) !important;
    }

    .dark .sada-manage-column {
        background: rgba(15,23,42,.70) !important;
        border-color: rgba(148,163,184,.16) !important;
    }

    .dark .sada-manage-section-title,
    .dark .sada-form-label {
        color: #f8fafc !important;
    }

    .dark .sada-add-form input,
    .dark .sada-add-form select,
    .dark .sada-add-form textarea,
    .dark .sada-add-form .fi-input,
    .dark .sada-add-form .fi-select-input,
    .dark .sada-add-form .fi-textarea {
        background: rgba(15,23,42,.86) !important;
        border-color: rgba(148,163,184,.20) !important;
        color: #f8fafc !important;
    }

    @media (max-width: 900px) {
        .sada-manage-layout {
            grid-template-columns: 1fr !important;
        }

        .sada-form-grid {
            grid-template-columns: 1fr !important;
        }

        .sada-calendar-modal,
        .sada-calendar-manage-modal {
            width: calc(100vw - 28px) !important;
            max-width: calc(100vw - 28px) !important;
        }

        .sada-calendar-modal-backdrop {
            padding: 14px !important;
        }
    }
</style>

<script id="sf-recruitment-calendar-modal-safety-final">
    document.addEventListener('DOMContentLoaded', function () {
        /*
         | Safety net:
         | If the modal closes, always restore body scroll and calendar size.
         */
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                document.body.classList.remove('overflow-hidden');
            }
        });

        document.addEventListener('click', function (event) {
            if (
                event.target.matches('.sada-calendar-modal-backdrop') ||
                event.target.closest('.sada-calendar-modal-close') ||
                event.target.closest('.sada-form-actions .fi-btn-color-gray')
            ) {
                setTimeout(function () {
                    document.body.classList.remove('overflow-hidden');
                    window.dispatchEvent(new Event('resize'));
                }, 180);
            }
        });
    });
</script>

<style id="sf-admin-calendar-final">
    .sada-calendar-page {
        max-width: 1480px !important;
        margin-inline: auto !important;
    }

    .sada-calendar-hero {
        border-radius: 30px !important;
        border: 1px solid rgba(203,213,225,.86) !important;
        background: linear-gradient(135deg, rgba(255,255,255,.94), rgba(236,253,245,.58)) !important;
        box-shadow: 0 18px 45px rgba(15,23,42,.06) !important;
        padding: 28px !important;
        text-align: center !important;
    }

    .sada-calendar-badge {
        width: fit-content !important;
        margin: 0 auto 12px !important;
        border-radius: 999px !important;
        background: #e0f2fe !important;
        color: #0f766e !important;
        padding: 8px 14px !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .12em !important;
    }

    .sada-calendar-title {
        color: #234b74 !important;
        font-size: clamp(38px, 4vw, 68px) !important;
        line-height: .94 !important;
        font-weight: 950 !important;
        letter-spacing: -.06em !important;
        margin: 0 !important;
    }

    .sada-calendar-subtitle {
        max-width: 720px !important;
        margin: 14px auto 0 !important;
        color: #64748b !important;
        font-size: 15px !important;
        font-weight: 650 !important;
    }

    .sada-calendar-hero-actions {
        margin-top: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
    }

    .sada-calendar-pill-btn,
    .sada-public-calendar-btn,
    .sada-calendar-hero-actions .fi-btn {
        min-height: 42px !important;
        border-radius: 999px !important;
        padding: 0 16px !important;
        border: 1px solid rgba(203,213,225,.86) !important;
        background: #ffffff !important;
        color: #234b74 !important;
        font-weight: 900 !important;
        box-shadow: 0 10px 24px rgba(15,23,42,.06) !important;
    }

    .sada-calendar-card {
        border-radius: 30px !important;
        border: 1px solid rgba(203,213,225,.86) !important;
        background: rgba(255,255,255,.94) !important;
        box-shadow: 0 18px 45px rgba(15,23,42,.06) !important;
    }

    .sada-calendar-schedule-row,
    .sada-calendar-layout {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 360px !important;
        gap: 22px !important;
        align-items: start !important;
    }

    .sada-calendar-main {
        min-width: 0 !important;
    }

    .sada-calendar-sidebar,
    .sada-calendar-schedule-side {
        display: flex !important;
        flex-direction: column !important;
        gap: 18px !important;
    }

    .sada-calendar-side-card {
        border-radius: 26px !important;
        border: 1px solid rgba(203,213,225,.86) !important;
        background: rgba(255,255,255,.92) !important;
        box-shadow: 0 14px 34px rgba(15,23,42,.05) !important;
        padding: 18px !important;
    }

    .sada-calendar-side-badge {
        display: inline-flex !important;
        width: fit-content !important;
        border-radius: 999px !important;
        background: #e0f2fe !important;
        color: #0f766e !important;
        padding: 7px 12px !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .12em !important;
        text-transform: uppercase !important;
    }

    .sada-calendar-side-title {
        color: #234b74 !important;
        font-size: 22px !important;
        font-weight: 950 !important;
        margin: 10px 0 0 !important;
        letter-spacing: -.03em !important;
    }

    .sada-calendar-side-text {
        color: #64748b !important;
        font-weight: 700 !important;
        margin: 8px 0 14px !important;
    }

    .sada-calendar-side-list,
    .sada-upcoming-groups {
        max-height: 430px !important;
        overflow-y: auto !important;
        padding-right: 5px !important;
    }

    .sada-side-item,
    .sada-upcoming-item,
    .sada-calendar-modal-item {
        border-radius: 18px !important;
        border: 1px solid rgba(203,213,225,.82) !important;
        background: #ffffff !important;
        box-shadow: 0 8px 22px rgba(15,23,42,.04) !important;
    }

    .fc .fc-toolbar {
        display: grid !important;
        grid-template-columns: 1fr auto 1fr !important;
        align-items: center !important;
        gap: 10px !important;
        margin-bottom: 16px !important;
    }

    .fc .fc-toolbar-chunk:nth-child(2) {
        justify-self: center !important;
    }

    .fc .fc-toolbar-chunk:last-child {
        justify-self: end !important;
    }

    .fc .fc-toolbar-title {
        color: #234b74 !important;
        font-size: clamp(30px, 3vw, 48px) !important;
        font-weight: 950 !important;
        letter-spacing: -.05em !important;
    }

    .fc .fc-button {
        border: 0 !important;
        border-radius: 14px !important;
        background: #e8f0fe !important;
        color: #0b57d0 !important;
        font-weight: 950 !important;
        box-shadow: 0 8px 22px rgba(11,87,208,.10) !important;
    }

    .fc .fc-button:hover {
        background: #dbeafe !important;
    }

    .fc .fc-scrollgrid {
        border-radius: 24px !important;
        overflow: hidden !important;
        border-color: #dbe7ee !important;
    }

    .fc .fc-col-header-cell {
        background: #f1f7fb !important;
    }

    .fc .fc-col-header-cell-cushion {
        color: #234b74 !important;
        font-weight: 950 !important;
        text-decoration: none !important;
    }

    .fc .fc-daygrid-day-number {
        color: #334155 !important;
        font-weight: 900 !important;
        text-decoration: none !important;
    }

    .fc .fc-day-today {
        background: #eff6ff !important;
    }

    .fc .fc-event {
        border: 0 !important;
        border-radius: 999px !important;
        padding: 2px 6px !important;
        font-size: 11px !important;
        font-weight: 900 !important;
        box-shadow: 0 8px 18px rgba(15,23,42,.10) !important;
    }

    .sada-calendar-modal {
        border-radius: 30px !important;
        max-width: 1180px !important;
        width: min(1180px, calc(100vw - 36px)) !important;
        border: 1px solid rgba(203,213,225,.88) !important;
        box-shadow: 0 30px 90px rgba(15,23,42,.25) !important;
    }

    .sada-calendar-modal-top {
        border-bottom: 1px solid rgba(203,213,225,.80) !important;
        padding: 22px !important;
    }

    .sada-calendar-modal-close {
        width: 44px !important;
        height: 44px !important;
        border-radius: 999px !important;
        background: #fef2f2 !important;
        color: #dc2626 !important;
        font-weight: 950 !important;
    }

    @media (max-width: 1100px) {
        .sada-calendar-schedule-row,
        .sada-calendar-layout {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<style id="sf-calendar-admin-md3-final">
    .sada-calendar-page {
        max-width: 1500px !important;
        margin: 0 auto !important;
    }

    .sada-calendar-page-header,
    .sada-calendar-hero {
        max-width: 100% !important;
        margin-bottom: 24px !important;
    }

    .sada-calendar-hero {
        border-radius: 34px !important;
        border: 1px solid rgba(203, 213, 225, .9) !important;
        background:
            radial-gradient(circle at 85% 0%, rgba(20, 184, 166, .14), transparent 34%),
            linear-gradient(135deg, #ffffff 0%, #f8fbfd 62%, #edfdfb 100%) !important;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .08) !important;
        padding: 34px 36px !important;
        text-align: center !important;
    }

    .sada-calendar-title {
        color: #234b74 !important;
        font-size: clamp(42px, 4vw, 72px) !important;
        line-height: .95 !important;
        font-weight: 950 !important;
        letter-spacing: -.06em !important;
    }

    .sada-calendar-subtitle {
        margin: 14px auto 0 !important;
        max-width: 780px !important;
        color: #64748b !important;
        font-size: 15px !important;
        font-weight: 650 !important;
    }

    .sada-calendar-hero-actions {
        margin-top: 22px !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 10px !important;
        flex-wrap: wrap !important;
    }

    .sada-calendar-card {
        border-radius: 34px !important;
        border: 1px solid rgba(203, 213, 225, .9) !important;
        background: rgba(255,255,255,.96) !important;
        box-shadow: 0 22px 65px rgba(15, 23, 42, .07) !important;
        padding: 24px !important;
        overflow: visible !important;
    }

    .sada-calendar-layout,
    .sada-calendar-schedule-row,
    .sada-calendar-page .sada-calendar-card > .sada-calendar-layout {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 380px !important;
        gap: 28px !important;
        align-items: start !important;
    }

    .sada-calendar-main {
        min-width: 0 !important;
        overflow: visible !important;
    }

    .sada-calendar-sidebar,
    .sada-calendar-schedule-side {
        display: flex !important;
        flex-direction: column !important;
        gap: 20px !important;
        min-width: 0 !important;
    }

    .sada-calendar-side-card {
        border-radius: 28px !important;
        border: 1px solid rgba(203, 213, 225, .86) !important;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbfd 100%) !important;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .06) !important;
        padding: 20px !important;
        overflow: hidden !important;
    }

    .sada-calendar-side-list,
    .sada-upcoming-groups {
        max-height: 450px !important;
        overflow-y: auto !important;
        padding-right: 6px !important;
    }

    .sada-calendar-side-badge {
        display: inline-flex !important;
        width: fit-content !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 999px !important;
        background: #e0f2fe !important;
        color: #0f766e !important;
        padding: 8px 13px !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .13em !important;
        text-transform: uppercase !important;
    }

    .sada-calendar-side-title {
        color: #234b74 !important;
        font-size: 23px !important;
        font-weight: 950 !important;
        letter-spacing: -.035em !important;
        margin-top: 10px !important;
    }

    .fc {
        overflow: visible !important;
    }

    .fc .fc-toolbar {
        display: grid !important;
        grid-template-columns: 1fr auto 1fr !important;
        align-items: center !important;
        margin-bottom: 18px !important;
        gap: 12px !important;
        position: relative !important;
        z-index: 1 !important;
    }

    .fc .fc-toolbar-chunk:first-child {
        justify-self: start !important;
    }

    .fc .fc-toolbar-chunk:nth-child(2) {
        justify-self: center !important;
    }

    .fc .fc-toolbar-chunk:last-child {
        justify-self: end !important;
        display: flex !important;
        gap: 10px !important;
        align-items: center !important;
    }

    .fc .fc-toolbar-title {
        color: #234b74 !important;
        font-size: clamp(36px, 3.4vw, 56px) !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: -.055em !important;
        text-align: center !important;
        white-space: nowrap !important;
    }

    .fc .fc-button {
        height: 46px !important;
        min-width: 46px !important;
        border: 0 !important;
        border-radius: 18px !important;
        background: #e8f0fe !important;
        color: #0b57d0 !important;
        font-weight: 950 !important;
        box-shadow: 0 10px 24px rgba(11, 87, 208, .12) !important;
        text-transform: lowercase !important;
        opacity: 1 !important;
    }

    .fc .fc-button:hover {
        background: #dbeafe !important;
        color: #0842a0 !important;
    }

    .fc .fc-button:disabled {
        opacity: .7 !important;
        color: #64748b !important;
    }

    .fc .fc-scrollgrid {
        border-radius: 26px !important;
        overflow: hidden !important;
        border-color: #dbe7ee !important;
        box-shadow: inset 0 0 0 1px rgba(219,231,238,.55) !important;
    }

    .fc .fc-col-header-cell {
        background: #f1f7fb !important;
        height: 48px !important;
        vertical-align: middle !important;
    }

    .fc .fc-col-header-cell-cushion {
        color: #234b74 !important;
        font-weight: 950 !important;
        text-decoration: none !important;
    }

    .fc .fc-daygrid-day-number {
        color: #334155 !important;
        font-weight: 900 !important;
        text-decoration: none !important;
        padding: 8px !important;
    }

    .fc .fc-day-today {
        background: #eff6ff !important;
    }

    .fc .fc-event {
        border: 0 !important;
        border-radius: 999px !important;
        padding: 3px 7px !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .10) !important;
    }

    .sada-side-item,
    .sada-upcoming-item,
    .sada-calendar-modal-item {
        border-radius: 18px !important;
        border: 1px solid rgba(203, 213, 225, .82) !important;
        background: #ffffff !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .045) !important;
    }

    @media (max-width: 1200px) {
        .sada-calendar-layout,
        .sada-calendar-schedule-row,
        .sada-calendar-page .sada-calendar-card > .sada-calendar-layout {
            grid-template-columns: 1fr !important;
        }

        .fc .fc-toolbar {
            grid-template-columns: 1fr !important;
            justify-items: center !important;
        }

        .fc .fc-toolbar-chunk {
            justify-self: center !important;
        }
    }
</style>

<style id="sf-recruitment-calendar-md3-final">
    /*
      Sada Fezzan Recruitment Calendar — Final MD3 Layout
      Goal:
      - Employment-page-inspired premium/minimal look
      - Calendar toolbar centered ONLY inside calendar card
      - Right sidebar separated and never overlapping toolbar
      - Clear Add Event / Open Public Calendar / Today controls
    */

    .fi-main,
    .fi-page,
    .fi-page-content,
    .fi-section,
    .fi-wi {
        overflow: visible !important;
    }

    .sada-calendar-page {
        max-width: 1460px !important;
        margin: 0 auto !important;
        padding: 24px 18px 48px !important;
    }

    /* =========================
       HERO / HEADER
    ========================= */
    .sada-calendar-page-header,
    .sada-calendar-hero {
        position: relative !important;
        overflow: hidden !important;
        border-radius: 34px !important;
        border: 1px solid rgba(30, 64, 175, .16) !important;
        background:
            radial-gradient(circle at 92% 0%, rgba(20,184,166,.24), transparent 36%),
            radial-gradient(circle at 0% 100%, rgba(37,99,235,.10), transparent 34%),
            linear-gradient(135deg, #111827 0%, #172033 48%, #234b74 100%) !important;
        box-shadow: 0 24px 70px rgba(15,23,42,.18) !important;
        padding: 36px 42px !important;
        margin: 0 0 28px !important;
        min-height: 250px !important;
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) auto !important;
        align-items: center !important;
        gap: 28px !important;
        text-align: left !important;
    }

    .sada-calendar-hero::before {
        content: "" !important;
        position: absolute !important;
        inset: -35% auto auto -10% !important;
        width: 420px !important;
        height: 420px !important;
        border-radius: 999px !important;
        background: rgba(20,184,166,.14) !important;
        filter: blur(8px) !important;
        pointer-events: none !important;
    }

    .sada-calendar-badge {
        position: relative !important;
        display: inline-flex !important;
        width: fit-content !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 34px !important;
        padding: 0 16px !important;
        border-radius: 999px !important;
        background: rgba(255,255,255,.10) !important;
        border: 1px solid rgba(255,255,255,.18) !important;
        color: #a7f3d0 !important;
        font-size: 12px !important;
        font-weight: 950 !important;
        letter-spacing: .22em !important;
        text-transform: uppercase !important;
        backdrop-filter: blur(12px) !important;
        margin-bottom: 14px !important;
    }

    .sada-calendar-title {
        position: relative !important;
        color: #ffffff !important;
        font-size: clamp(48px, 5vw, 82px) !important;
        line-height: .92 !important;
        font-weight: 950 !important;
        letter-spacing: -.065em !important;
        margin: 0 !important;
        text-align: left !important;
    }

    .sada-calendar-subtitle {
        position: relative !important;
        max-width: 700px !important;
        color: rgba(226,232,240,.86) !important;
        font-size: 16px !important;
        line-height: 1.7 !important;
        margin: 18px 0 0 !important;
        font-weight: 650 !important;
        text-align: left !important;
    }

    .sada-calendar-hero-actions {
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 12px !important;
        align-items: flex-end !important;
        justify-content: center !important;
        min-width: 260px !important;
    }

    .sada-calendar-pill-btn,
    .sada-public-calendar-btn,
    .fi-btn {
        border-radius: 999px !important;
        min-height: 48px !important;
        padding: 0 20px !important;
        font-weight: 900 !important;
        letter-spacing: -.01em !important;
        box-shadow: 0 14px 30px rgba(15,23,42,.18) !important;
        border: 1px solid rgba(255,255,255,.18) !important;
    }

    .sada-calendar-pill-btn {
        background: rgba(255,255,255,.12) !important;
        color: #ffffff !important;
        backdrop-filter: blur(14px) !important;
        width: 100% !important;
        justify-content: flex-start !important;
    }

    .sada-calendar-pill-label {
        color: rgba(226,232,240,.75) !important;
        font-size: 11px !important;
        letter-spacing: .18em !important;
        text-transform: uppercase !important;
        margin-right: 8px !important;
    }

    .sada-calendar-pill-value {
        color: #ffffff !important;
        font-size: 14px !important;
        font-weight: 950 !important;
    }

    .sada-calendar-hero-actions .fi-btn,
    .sada-calendar-hero-actions a {
        width: 100% !important;
        justify-content: center !important;
    }

    /* =========================
       MAIN GRID
    ========================= */
    .sada-calendar-card {
        border-radius: 34px !important;
        border: 1px solid rgba(203,213,225,.92) !important;
        background:
            radial-gradient(circle at 100% 0%, rgba(20,184,166,.12), transparent 34%),
            rgba(255,255,255,.96) !important;
        box-shadow: 0 24px 70px rgba(15,23,42,.08) !important;
        padding: 26px !important;
        overflow: visible !important;
    }

    .sada-calendar-layout,
    .sada-calendar-schedule-row,
    .sada-calendar-page .sada-calendar-card > .sada-calendar-layout {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) 390px !important;
        gap: 28px !important;
        align-items: start !important;
        overflow: visible !important;
    }

    .sada-calendar-main,
    .sada-calendar-schedule-main {
        min-width: 0 !important;
        overflow: visible !important;
        border-radius: 30px !important;
        background: #ffffff !important;
        border: 1px solid rgba(203,213,225,.85) !important;
        box-shadow: 0 18px 45px rgba(15,23,42,.06) !important;
        padding: 22px !important;
    }

    .sada-calendar-sidebar,
    .sada-calendar-schedule-side {
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 18px !important;
        position: relative !important;
        z-index: 1 !important;
    }

    .sada-calendar-side-card {
        border-radius: 28px !important;
        border: 1px solid rgba(203,213,225,.92) !important;
        background: rgba(255,255,255,.96) !important;
        box-shadow: 0 18px 45px rgba(15,23,42,.06) !important;
        padding: 22px !important;
        overflow: hidden !important;
    }

    .sada-calendar-side-badge {
        display: inline-flex !important;
        align-items: center !important;
        min-height: 32px !important;
        padding: 0 14px !important;
        border-radius: 999px !important;
        background: #e6f4f1 !important;
        color: #0f766e !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        letter-spacing: .20em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
    }

    .sada-calendar-side-title {
        color: #234b74 !important;
        font-size: 26px !important;
        line-height: 1.05 !important;
        font-weight: 950 !important;
        letter-spacing: -.04em !important;
        margin: 14px 0 12px !important;
    }

    .sada-calendar-side-text {
        color: #64748b !important;
        font-size: 14px !important;
        font-weight: 650 !important;
        margin: 0 0 14px !important;
    }

    .sada-calendar-side-top {
        display: grid !important;
        grid-template-columns: minmax(0, 1fr) auto !important;
        align-items: start !important;
        gap: 12px !important;
        margin-bottom: 14px !important;
    }

    .sada-calendar-side-top .fi-btn {
        min-height: 42px !important;
        background: #0f766e !important;
        color: #ffffff !important;
        border: 0 !important;
        box-shadow: 0 12px 28px rgba(15,118,110,.18) !important;
        white-space: nowrap !important;
    }

    .sada-calendar-side-list,
    .sada-upcoming-groups {
        max-height: 420px !important;
        overflow-y: auto !important;
        padding-right: 6px !important;
    }

    .sada-calendar-side-list::-webkit-scrollbar,
    .sada-upcoming-groups::-webkit-scrollbar {
        width: 8px !important;
    }

    .sada-calendar-side-list::-webkit-scrollbar-thumb,
    .sada-upcoming-groups::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 999px !important;
    }

    /* =========================
       FULLCALENDAR TOOLBAR FIX
       Important: centered only inside calendar main card
    ========================= */
    .fc {
        overflow: visible !important;
        font-family: inherit !important;
    }

    .fc .fc-toolbar {
        display: grid !important;
        grid-template-columns: 150px minmax(0, 1fr) 150px !important;
        align-items: center !important;
        gap: 12px !important;
        width: 100% !important;
        margin: 0 0 22px !important;
    }

    .fc .fc-toolbar-chunk:first-child {
        justify-self: start !important;
        width: 150px !important;
        min-width: 150px !important;
        display: flex !important;
        justify-content: flex-start !important;
    }

    .fc .fc-toolbar-chunk:nth-child(2) {
        justify-self: center !important;
        text-align: center !important;
        min-width: 0 !important;
        width: 100% !important;
    }

    .fc .fc-toolbar-chunk:last-child {
        justify-self: end !important;
        width: 150px !important;
        min-width: 150px !important;
        display: flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .fc .fc-toolbar-title {
        width: 100% !important;
        max-width: 100% !important;
        text-align: center !important;
        color: #234b74 !important;
        font-size: clamp(42px, 4.2vw, 64px) !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: -.065em !important;
        white-space: nowrap !important;
        margin: 0 !important;
    }

    .fc .fc-button {
        height: 48px !important;
        min-width: 48px !important;
        border-radius: 18px !important;
        border: 0 !important;
        background: #e8f0fe !important;
        color: #0b57d0 !important;
        font-weight: 950 !important;
        box-shadow: 0 10px 24px rgba(11,87,208,.12) !important;
        text-transform: lowercase !important;
        opacity: 1 !important;
    }

    .fc .fc-button:hover {
        background: #dbeafe !important;
        color: #0842a0 !important;
    }

    .fc .fc-button:disabled {
        opacity: .65 !important;
        background: #eef2f7 !important;
        color: #64748b !important;
    }

    /* =========================
       CALENDAR BOX STYLE
    ========================= */
    .fc .fc-scrollgrid {
        border-radius: 28px !important;
        overflow: hidden !important;
        border: 1px solid #dbe7ee !important;
        background: #ffffff !important;
    }

    .fc .fc-col-header-cell {
        background: #f1f7fb !important;
        height: 54px !important;
        vertical-align: middle !important;
    }

    .fc .fc-col-header-cell-cushion {
        color: #234b74 !important;
        font-weight: 950 !important;
        text-decoration: none !important;
        font-size: 14px !important;
    }

    .fc .fc-daygrid-day {
        background: #ffffff !important;
        transition: .15s ease !important;
    }

    .fc .fc-daygrid-day:hover {
        background: #f8fafc !important;
    }

    .fc .fc-daygrid-day-frame {
        min-height: 118px !important;
        padding: 6px !important;
    }

    .fc .fc-daygrid-day-number {
        color: #334155 !important;
        font-weight: 900 !important;
        text-decoration: none !important;
        padding: 8px !important;
    }

    .fc .fc-day-other .fc-daygrid-day-number {
        color: #b6c0cc !important;
    }

    .fc .fc-day-today {
        background: #eff6ff !important;
    }

    .fc .fc-day-today .fc-daygrid-day-frame {
        box-shadow: inset 0 0 0 2px #3b82f6 !important;
        border-radius: 18px !important;
    }

    .fc .fc-event {
        border: 0 !important;
        border-radius: 999px !important;
        padding: 3px 8px !important;
        font-size: 11px !important;
        font-weight: 950 !important;
        box-shadow: 0 8px 18px rgba(15,23,42,.10) !important;
        overflow: hidden !important;
    }

    /* =========================
       EVENT CARDS
    ========================= */
    .sada-side-item,
    .sada-upcoming-item,
    .sada-calendar-modal-item {
        border-radius: 20px !important;
        padding: 14px !important;
        border: 1px solid rgba(203,213,225,.85) !important;
        background: #ffffff !important;
        box-shadow: 0 10px 24px rgba(15,23,42,.045) !important;
        margin-bottom: 12px !important;
    }

    .sada-side-dot,
    .sada-upcoming-dot,
    .sada-calendar-modal-item-dot {
        width: 12px !important;
        height: 12px !important;
        border-radius: 999px !important;
        flex: 0 0 12px !important;
        box-shadow: 0 0 0 5px rgba(15,23,42,.04) !important;
    }

    .sada-side-item-title,
    .sada-upcoming-title,
    .sada-calendar-modal-item-title {
        color: #0f172a !important;
        font-weight: 950 !important;
        letter-spacing: -.02em !important;
    }

    .sada-side-item-meta,
    .sada-upcoming-meta,
    .sada-calendar-modal-item-notes {
        color: #64748b !important;
        font-weight: 650 !important;
        font-size: 13px !important;
        line-height: 1.55 !important;
    }

    .sada-empty-box {
        border-radius: 18px !important;
        border: 1px dashed #cbd5e1 !important;
        background: #f8fafc !important;
        color: #64748b !important;
        padding: 18px !important;
        text-align: center !important;
        font-weight: 750 !important;
    }

    /* =========================
       MODAL
    ========================= */
    .sada-calendar-modal-backdrop {
        background: rgba(15,23,42,.48) !important;
        backdrop-filter: blur(10px) !important;
    }

    .sada-calendar-modal {
        border-radius: 32px !important;
        border: 1px solid rgba(203,213,225,.90) !important;
        box-shadow: 0 30px 90px rgba(15,23,42,.30) !important;
        overflow: hidden !important;
    }

    .sada-calendar-modal-close {
        width: 42px !important;
        height: 42px !important;
        border-radius: 999px !important;
        background: #fee2e2 !important;
        color: #dc2626 !important;
        font-weight: 950 !important;
    }

    /* =========================
       DARK MODE
    ========================= */
    .dark .sada-calendar-card,
    .dark .sada-calendar-main,
    .dark .sada-calendar-side-card {
        background: rgba(15,23,42,.94) !important;
        border-color: rgba(148,163,184,.24) !important;
        box-shadow: 0 24px 70px rgba(0,0,0,.22) !important;
    }

    .dark .fc .fc-scrollgrid,
    .dark .fc .fc-daygrid-day {
        background: #0f172a !important;
        border-color: rgba(148,163,184,.20) !important;
    }

    .dark .fc .fc-col-header-cell {
        background: #111827 !important;
    }

    .dark .fc .fc-daygrid-day:hover {
        background: #111827 !important;
    }

    .dark .fc .fc-toolbar-title,
    .dark .sada-calendar-side-title,
    .dark .fc .fc-col-header-cell-cushion,
    .dark .fc .fc-daygrid-day-number {
        color: #e5f3ff !important;
    }

    .dark .sada-side-item,
    .dark .sada-upcoming-item,
    .dark .sada-calendar-modal-item,
    .dark .sada-empty-box {
        background: #111827 !important;
        border-color: rgba(148,163,184,.22) !important;
    }

    .dark .sada-side-item-title,
    .dark .sada-upcoming-title {
        color: #f8fafc !important;
    }

    .dark .sada-side-item-meta,
    .dark .sada-upcoming-meta,
    .dark .sada-calendar-side-text {
        color: #94a3b8 !important;
    }

    /* =========================
       RESPONSIVE
    ========================= */
    @media (max-width: 1200px) {
        .sada-calendar-page-header,
        .sada-calendar-hero {
            grid-template-columns: 1fr !important;
            min-height: auto !important;
        }

        .sada-calendar-hero-actions {
            align-items: stretch !important;
            min-width: 0 !important;
        }

        .sada-calendar-layout,
        .sada-calendar-schedule-row,
        .sada-calendar-page .sada-calendar-card > .sada-calendar-layout {
            grid-template-columns: 1fr !important;
        }

        .fc .fc-toolbar {
            grid-template-columns: 1fr !important;
            justify-items: center !important;
        }

        .fc .fc-toolbar-chunk,
        .fc .fc-toolbar-chunk:first-child,
        .fc .fc-toolbar-chunk:nth-child(2),
        .fc .fc-toolbar-chunk:last-child {
            width: auto !important;
            min-width: 0 !important;
            justify-self: center !important;
        }
    }
</style>
<style id="sf-calendar-nav-final-correction">
    /*
     * Final Recruitment Calendar navigation:
     * Month title on left, prev/today/next on right.
     * Keeps the hero Today pill centered and clean.
     */

    .sada-calendar-page .fc-header-toolbar,
    .sada-calendar-page .fc .fc-toolbar.fc-header-toolbar {
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 18px !important;
        padding: 0 8px 18px !important;
        margin: 0 !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-toolbar-chunk:first-child {
        flex: 1 1 auto !important;
        display: flex !important;
        justify-content: flex-start !important;
        align-items: center !important;
        min-width: 0 !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-toolbar-chunk:nth-child(2) {
        display: none !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-toolbar-chunk:last-child {
        flex: 0 0 auto !important;
        display: inline-flex !important;
        justify-content: flex-end !important;
        align-items: center !important;
        gap: 10px !important;
        min-width: max-content !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-toolbar-title {
        margin: 0 !important;
        color: #234b74 !important;
        font-size: clamp(34px, 3.1vw, 48px) !important;
        line-height: 1 !important;
        font-weight: 950 !important;
        letter-spacing: -0.06em !important;
        text-align: left !important;
        white-space: nowrap !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-button-group {
        display: inline-flex !important;
        align-items: center !important;
        gap: 10px !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-button {
        min-width: 48px !important;
        height: 46px !important;
        border: 0 !important;
        border-radius: 18px !important;
        background: #e8f0fe !important;
        color: #0f4fc7 !important;
        font-size: 14px !important;
        font-weight: 950 !important;
        box-shadow: 0 12px 28px rgba(35, 75, 116, .12) !important;
        opacity: 1 !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-button:hover {
        background: #dbeafe !important;
        color: #0b3ea0 !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-today-button {
        min-width: 74px !important;
        padding-inline: 16px !important;
        text-transform: lowercase !important;
        background: #234b74 !important;
        color: #ffffff !important;
    }

    .sada-calendar-page .fc-header-toolbar .fc-button:disabled {
        opacity: .72 !important;
        cursor: default !important;
    }

    /*
     * Hero Today pill centered and visually strong.
     * This targets the top Today / date pill without touching calendar toolbar.
     */
    .sada-calendar-hero-actions,
    .sada-calendar-hero .sada-calendar-hero-actions {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
    }

    .sada-calendar-hero .sada-calendar-pill-btn,
    .sada-calendar-hero-actions .sada-calendar-pill-btn {
        margin-inline: auto !important;
        min-width: 520px !important;
        max-width: 760px !important;
        height: 54px !important;
        justify-content: center !important;
        border-radius: 999px !important;
        background: rgba(255, 255, 255, .12) !important;
        border: 1px solid rgba(255, 255, 255, .24) !important;
        color: #ffffff !important;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.14) !important;
    }

    .sada-calendar-hero .sada-calendar-pill-label,
    .sada-calendar-hero-actions .sada-calendar-pill-label {
        color: rgba(255,255,255,.62) !important;
        letter-spacing: .22em !important;
        text-transform: uppercase !important;
        font-weight: 900 !important;
    }

    .sada-calendar-hero .sada-calendar-pill-value,
    .sada-calendar-hero-actions .sada-calendar-pill-value {
        color: #ffffff !important;
        font-weight: 950 !important;
        letter-spacing: .01em !important;
    }

    .sada-calendar-page .fc,
    .sada-calendar-page .fc-view-harness,
    .sada-calendar-page .fc-scrollgrid,
    .sada-calendar-page .fc-scrollgrid-sync-table {
        visibility: visible !important;
        opacity: 1 !important;
    }

    @media (max-width: 900px) {
        .sada-calendar-page .fc-header-toolbar,
        .sada-calendar-page .fc .fc-toolbar.fc-header-toolbar {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .sada-calendar-page .fc-header-toolbar .fc-toolbar-title {
            font-size: 36px !important;
        }

        .sada-calendar-page .fc-header-toolbar .fc-toolbar-chunk:last-child {
            width: 100% !important;
            justify-content: flex-start !important;
        }

        .sada-calendar-hero .sada-calendar-pill-btn,
        .sada-calendar-hero-actions .sada-calendar-pill-btn {
            min-width: 0 !important;
            width: 100% !important;
        }
    }
</style>

