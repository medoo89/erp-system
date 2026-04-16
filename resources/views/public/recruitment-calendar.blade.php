<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment Calendar</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <style>
        body{
            margin:0;
            font-family: Inter, system-ui, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(60,159,163,.07), transparent 22%),
                radial-gradient(circle at top right, rgba(44,83,119,.06), transparent 24%),
                linear-gradient(180deg, #f8fbfc 0%, #eef5f7 55%, #f7fafc 100%);
            color:#1f2937;
        }

        .page{
            max-width:1420px;
            margin:0 auto;
            padding:32px 20px 44px;
        }

        .hero{
            border:1px solid #d9e3e6;
            border-radius:30px;
            background:
                radial-gradient(circle at top right, rgba(60,159,163,.12), transparent 24%),
                linear-gradient(135deg,#ffffff 0%,#f3fbfb 100%);
            padding:38px 28px;
            text-align:center;
            box-shadow:0 12px 30px rgba(15,23,42,.06);
        }

        .badge{
            display:inline-flex;
            padding:.55rem 1rem;
            border-radius:999px;
            background:rgba(60,159,163,.10);
            color:#3C9FA3;
            font-size:.82rem;
            font-weight:800;
            letter-spacing:.1em;
            text-transform:uppercase;
            margin-bottom:1rem;
        }

        .title{
            margin:0;
            font-size:3rem;
            line-height:1.04;
            font-weight:800;
            color:#2C5377;
        }

        .subtitle{
            margin:1rem auto 0;
            max-width:900px;
            font-size:1.05rem;
            line-height:1.9;
            color:#6b7e99;
        }

        .today-pill{
            margin:1.2rem auto 0;
            display:inline-flex;
            gap:.7rem;
            align-items:center;
            padding:.82rem 1.05rem;
            border-radius:999px;
            background:rgba(255,255,255,.82);
            border:1px solid rgba(217,227,230,.9);
            cursor:pointer;
        }

        .today-label{
            font-size:.76rem;
            font-weight:800;
            letter-spacing:.1em;
            text-transform:uppercase;
            color:#94a3b8;
        }

        .today-value{
            font-size:.96rem;
            font-weight:800;
            color:#2C5377;
        }

        .layout{
            display:grid;
            grid-template-columns:minmax(0,1fr) 360px;
            gap:24px;
            margin-top:24px;
            align-items:start;
        }

        .card{
            border:1px solid rgba(217,227,230,.95);
            border-radius:24px;
            background:rgba(255,255,255,.95);
            box-shadow:0 12px 30px rgba(15,23,42,.06);
        }

        .calendar-card{
            padding:18px;
        }

        .side-card{
            padding:22px;
        }

        .side-badge{
            display:inline-flex;
            padding:.4rem .8rem;
            border-radius:999px;
            background:rgba(60,159,163,.10);
            color:#3C9FA3;
            font-size:.72rem;
            font-weight:800;
            letter-spacing:.08em;
            text-transform:uppercase;
            margin-bottom:.8rem;
        }

        .side-title{
            margin:0;
            font-size:1.35rem;
            font-weight:800;
            color:#2C5377;
            line-height:1.3;
        }

        .side-text{
            margin:.45rem 0 1rem;
            color:#6b7e99;
            line-height:1.7;
        }

        .empty-box{
            padding:14px 16px;
            border:1px solid #e5edf0;
            border-radius:18px;
            background:#f8fbfc;
            color:#7386a0;
            text-align:center;
        }

        .item{
            display:flex;
            gap:10px;
            align-items:flex-start;
            border:1px solid #e5edf0;
            border-radius:18px;
            background:#fafcfd;
            padding:14px 15px;
        }

        .item + .item{
            margin-top:10px;
        }

        .dot{
            width:11px;
            height:11px;
            border-radius:999px;
            margin-top:8px;
            flex-shrink:0;
        }

        .item-title{
            font-size:1rem;
            font-weight:800;
            color:#1f2937;
        }

        .item-meta,
        .item-notes{
            margin-top:4px;
            font-size:.9rem;
            color:#6b7e99;
            line-height:1.6;
        }

        .upcoming-group{
            padding:16px;
            border:1px solid #e5edf0;
            border-radius:20px;
            background:#fafcfd;
        }

        .upcoming-group + .upcoming-group{
            margin-top:12px;
        }

        .upcoming-date{
            font-size:1rem;
            font-weight:800;
            color:#1f2937;
            margin-bottom:12px;
        }

        .modal-backdrop{
            position:fixed;
            inset:0;
            background:rgba(2,6,23,.45);
            backdrop-filter:blur(6px);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
            z-index:50;
        }

        .modal{
            width:100%;
            max-width:760px;
            border-radius:28px;
            background:rgba(255,255,255,.98);
            border:1px solid rgba(217,227,230,.9);
            box-shadow:0 24px 60px rgba(15,23,42,.18);
            overflow:hidden;
        }

        .modal-top{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:1rem;
            padding:22px 24px 16px;
            border-bottom:1px solid rgba(217,227,230,.85);
        }

        .modal-title{
            margin:0;
            font-size:1.45rem;
            font-weight:800;
            color:#2C5377;
        }

        .modal-close{
            border:none;
            background:rgba(239,68,68,.10);
            color:#dc2626;
            width:42px;
            height:42px;
            border-radius:999px;
            font-size:1rem;
            font-weight:800;
            cursor:pointer;
        }

        .modal-list{
            padding:18px 24px 24px;
            display:grid;
            gap:14px;
            max-height:65vh;
            overflow:auto;
        }

        .footer{
            margin-top:28px;
            padding:18px 22px;
            border:1px solid rgba(217,227,230,.9);
            border-radius:22px;
            background:rgba(255,255,255,.78);
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:16px;
            flex-wrap:wrap;
        }

        .footer-title{
            font-size:1rem;
            font-weight:800;
            color:#2C5377;
        }

        .footer-text{
            font-size:.95rem;
            color:#6b7e99;
        }

        @media (max-width: 980px){
            .layout{
                grid-template-columns:1fr;
            }

            .title{
                font-size:2.2rem;
            }
        }
    </style>
</head>
<body>
<div
    class="page"
    x-data="{
        events: @js($calendarEvents),
        selectedDate: '{{ now()->toDateString() }}',
        selectedLabel: '{{ now()->format('D, d M Y') }}',
        selectedItems: [],
        showDayModal: false,
        calendar: null,

        initCalendar() {
            this.setSelectedDate(this.selectedDate);

            this.calendar = new FullCalendar.Calendar(this.$refs.calendar, {
                initialView: 'dayGridMonth',
                height: 'auto',
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
                dateClick: (info) => {
                    this.setSelectedDate(info.dateStr);
                    this.showDayModal = true;
                },
                eventClick: (info) => {
                    info.jsEvent.preventDefault();
                    this.setSelectedDate(info.event.startStr);
                    this.showDayModal = true;
                }
            });

            this.calendar.render();
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

            if (this.calendar) {
                this.calendar.today();
            }

            this.showDayModal = true;
        }
    }"
    x-init="initCalendar()"
>
    <div class="hero">
        <div class="badge">SADA FEZZAN ERP</div>
        <h1 class="title">Recruitment Calendar</h1>
        <p class="subtitle">
            Monitor job expiries and operational events in one central calendar view.
        </p>

        <button type="button" class="today-pill" @click="openTodayTasks()">
            <span class="today-label">Today</span>
            <span class="today-value">{{ $todayLabel }}</span>
        </button>
    </div>

    <div class="layout">
        <div class="card calendar-card">
            <div x-ref="calendar"></div>
        </div>

        <aside style="display:grid;gap:24px;">
            <div class="card side-card">
                <div class="side-badge">Selected Date</div>
                <h3 class="side-title" x-text="selectedLabel"></h3>

                <div style="margin-top:14px;">
                    <template x-if="selectedItems.length === 0">
                        <div class="empty-box">No events on this selected date.</div>
                    </template>

                    <template x-for="(item, index) in selectedItems" :key="index">
                        <div class="item">
                            <div class="dot" :style="`background:${item.backgroundColor}`"></div>
                            <div>
                                <div class="item-title" x-text="item.title"></div>

                                <template x-if="item.job_title">
                                    <div class="item-meta" x-text="'Linked to job: ' + item.job_title"></div>
                                </template>

                                <template x-if="item.notes">
                                    <div class="item-notes" x-text="item.notes"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card side-card">
                <div class="side-badge">Next Events</div>
                <h3 class="side-title">Upcoming Tasks</h3>
                <p class="side-text">From today to the next 30 days.</p>

                @forelse ($upcomingTaskGroups as $group)
                    <div class="upcoming-group">
                        <div class="upcoming-date">{{ $group['label'] }}</div>

                        @foreach ($group['items'] as $item)
                            <div class="item" style="margin-top:10px;">
                                <div class="dot" style="background: {{ $item['backgroundColor'] }}"></div>
                                <div>
                                    <div class="item-title">{{ $item['title'] }}</div>

                                    @if (! empty($item['job_title']))
                                        <div class="item-meta">Linked to job: {{ $item['job_title'] }}</div>
                                    @endif

                                    @if (! empty($item['notes']))
                                        <div class="item-notes">{{ $item['notes'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="empty-box">No upcoming tasks in the next 30 days.</div>
                @endforelse
            </div>
        </aside>
    </div>

    <div x-show="showDayModal" x-transition.opacity class="modal-backdrop" style="display:none;">
        <div class="modal" @click.outside="showDayModal = false">
            <div class="modal-top">
                <div>
                    <div class="side-badge">Day Overview</div>
                    <h3 class="modal-title" x-text="selectedLabel"></h3>
                </div>

                <button type="button" class="modal-close" @click="showDayModal = false">✕</button>
            </div>

            <div class="modal-list">
                <template x-if="selectedItems.length === 0">
                    <div class="empty-box">No events on this selected date.</div>
                </template>

                <template x-for="(item, index) in selectedItems" :key="index">
                    <div class="item">
                        <div class="dot" :style="`background:${item.backgroundColor}`"></div>
                        <div>
                            <div class="item-title" x-text="item.title"></div>

                            <template x-if="item.job_title">
                                <div class="item-notes" x-text="'Linked to job: ' + item.job_title"></div>
                            </template>

                            <template x-if="item.notes">
                                <div class="item-notes" x-text="item.notes"></div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>
            <div class="footer-title">Public Recruitment Calendar</div>
            <div class="footer-text">Share this page with candidates or external viewers for read-only calendar visibility.</div>
        </div>

        <div class="footer-text">
            Sada Fezzan ERP • Recruitment Calendar
        </div>
    </div>
</div>
</body>
</html>