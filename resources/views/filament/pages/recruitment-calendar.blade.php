<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment Calendar</title>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: #ffffff;
            color: #111827;
        }

        .shell {
            min-height: 100vh;
            padding: 24px;
            background: #ffffff;
        }

        .title {
            text-align: center;
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 24px;
            color: #111827;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 0.9fr;
            gap: 24px;
        }

        .card {
            background: #ffffff;
            border: 2px solid #dbeafe;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .calendar-card {
            padding: 24px;
            min-height: 900px;
        }

        .tasks-card {
            padding: 24px;
            min-height: 900px;
        }

        .tasks-title {
            margin: 0 0 6px 0;
            font-size: 1.8rem;
            font-weight: 900;
            color: #111827;
        }

        .tasks-subtitle {
            margin: 0 0 18px 0;
            font-size: 1rem;
            color: #475569;
        }

        .group {
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .group-date {
            font-size: 1.15rem;
            font-weight: 900;
            margin-bottom: 12px;
            color: #111827;
        }

        .item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px 16px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 10px;
        }

        .dot {
            width: 14px;
            height: 14px;
            min-width: 14px;
            border-radius: 999px;
            margin-top: 4px;
        }

        .item-title {
            font-size: 1rem;
            font-weight: 800;
            color: #111827;
            line-height: 1.4;
        }

        .item-meta {
            font-size: 0.9rem;
            color: #475569;
            margin-top: 4px;
            font-weight: 600;
        }

        .item-notes {
            font-size: 0.9rem;
            color: #64748b;
            margin-top: 4px;
        }

        .empty {
            padding: 16px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            text-align: center;
        }

        .fc {
            color: #111827;
            font-family: Inter, Arial, sans-serif;
        }

        .fc-theme-standard td,
        .fc-theme-standard th,
        .fc-scrollgrid,
        .fc-scrollgrid-section > * {
            border-color: #dbeafe !important;
        }

        .fc-toolbar-title {
            font-size: 3rem !important;
            font-weight: 900 !important;
            color: #111827 !important;
        }

        .fc-col-header-cell-cushion {
            color: #334155 !important;
            text-decoration: none !important;
            font-weight: 800 !important;
            font-size: 1rem !important;
            padding: 14px 0 !important;
        }

        .fc-daygrid-day-number {
            color: #111827 !important;
            text-decoration: none !important;
            font-weight: 800 !important;
            font-size: 1rem !important;
            padding: 12px !important;
        }

        .fc-day-other .fc-daygrid-day-number {
            color: #94a3b8 !important;
        }

        .fc-button {
            border: none !important;
            border-radius: 999px !important;
            padding: 0.9rem 1.2rem !important;
            background: #eff6ff !important;
            color: #111827 !important;
            box-shadow: none !important;
            font-weight: 800 !important;
        }

        .fc-button:hover {
            background: #dbeafe !important;
        }

        .fc-event {
            border-radius: 10px !important;
            padding: 4px 8px !important;
            font-size: 0.9rem !important;
            font-weight: 800 !important;
            border-width: 0 !important;
        }

        .fc-daygrid-event-dot {
            display: none !important;
        }

        @media (max-width: 1400px) {
            .grid { grid-template-columns: 1fr; }
            .calendar-card, .tasks-card { min-height: auto; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <div class="title">Recruitment Calendar</div>

        <div class="grid">
            <div class="card calendar-card">
                <div id="public-calendar"></div>
            </div>

            <div class="card tasks-card">
                <h3 class="tasks-title">Upcoming Tasks</h3>
                <p class="tasks-subtitle">From today to the next 30 days.</p>

                @if (count($upcomingTaskGroups))
                    @foreach ($upcomingTaskGroups as $group)
                        <div class="group">
                            <div class="group-date">{{ $group['label'] }}</div>

                            @foreach ($group['items'] as $item)
                                <div class="item">
                                    <span class="dot" style="background-color: {{ $item['backgroundColor'] }};"></span>

                                    <div>
                                        <div class="item-title">{{ $item['title'] }}</div>

                                        @if (! empty($item['job_title']))
                                            <div class="item-meta">Linked to job: {{ $item['job_title'] }}</div>
                                        @else
                                            <div class="item-meta">General Event</div>
                                        @endif

                                        @if (! empty($item['notes']))
                                            <div class="item-notes">{{ $item['notes'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="empty">No upcoming tasks in the next 30 days.</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const calendarEvents = @json($calendarEvents);

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('public-calendar');

            if (!calendarEl || typeof FullCalendar === 'undefined') return;

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 900,
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
                events: calendarEvents,
            });

            calendar.render();
        });

        setTimeout(() => {
            window.location.reload();
        }, 60000);
    </script>
</body>
</html>