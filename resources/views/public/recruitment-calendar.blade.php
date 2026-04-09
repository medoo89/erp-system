<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment Calendar</title>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, Arial, sans-serif;
            background: #0f172a;
            color: #f8fafc;
        }

        .public-calendar-shell {
            min-height: 100vh;
            padding: 24px;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.18), transparent 28%),
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 22%),
                linear-gradient(180deg, #111827 0%, #0f172a 100%);
        }

        .public-calendar-title-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .public-calendar-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: min(100%, 560px);
            padding: 18px 36px;
            border-radius: 24px;
            font-size: 2.2rem;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #f8fafc;
            background: rgba(15, 23, 42, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow:
                0 14px 34px rgba(15, 23, 42, 0.18),
                inset 0 1px 0 rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .public-calendar-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.9fr) minmax(360px, 0.95fr);
            gap: 1.5rem;
            align-items: start;
        }

        .public-calendar-card,
        .public-tasks-card {
            border-radius: 30px;
            overflow: hidden;
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            border: 1px solid rgba(148, 163, 184, 0.10);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.02),
                0 12px 30px rgba(0, 0, 0, 0.18);
        }

        .public-calendar-card {
            padding: 24px;
            min-height: 820px;
        }

        .public-tasks-card {
            padding: 22px;
            min-height: 820px;
        }

        .public-tasks-title {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0 0 0.3rem 0;
        }

        .public-tasks-subtitle {
            margin: 0 0 1rem 0;
            font-size: 0.95rem;
            color: #94a3b8;
        }

        .public-task-group {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .public-task-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .public-task-date {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: #f8fafc;
        }

        .public-task-list {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .public-task-item {
            display: flex;
            gap: 0.78rem;
            align-items: flex-start;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(148, 163, 184, 0.10);
        }

        .public-task-dot {
            width: 12px;
            height: 12px;
            min-width: 12px;
            border-radius: 999px;
            margin-top: 0.32rem;
            box-shadow: 0 0 0 4px rgba(255,255,255,0.06);
        }

        .public-task-content {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .public-task-item-title {
            font-size: 0.98rem;
            line-height: 1.45;
            font-weight: 700;
            color: #f8fafc;
        }

        .public-task-meta {
            font-size: 0.85rem;
            font-weight: 600;
            color: #cbd5e1;
        }

        .public-task-notes {
            font-size: 0.85rem;
            line-height: 1.45;
            color: #94a3b8;
        }

        .public-empty-state {
            padding: 1rem;
            border-radius: 18px;
            text-align: center;
            font-size: 0.96rem;
            background: rgba(255,255,255,0.05);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.10);
        }

        .fc {
            color: #e5e7eb;
            font-family: Inter, Arial, sans-serif;
        }

        .fc-theme-standard td,
        .fc-theme-standard th,
        .fc-scrollgrid,
        .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.16) !important;
        }

        .fc-toolbar-title {
            font-size: 2.3rem !important;
            line-height: 1.1 !important;
            font-weight: 800 !important;
            color: #f8fafc !important;
        }

        .fc-col-header-cell-cushion {
            color: #cbd5e1 !important;
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 12px 0 !important;
        }

        .fc-daygrid-day-number {
            color: #f8fafc !important;
            text-decoration: none !important;
            font-weight: 700 !important;
            padding: 10px !important;
        }

        .fc-day-other .fc-daygrid-day-number {
            color: #64748b !important;
        }

        .fc-button {
            border: none !important;
            border-radius: 999px !important;
            padding: 0.72rem 1.05rem !important;
            background: rgba(255,255,255,0.08) !important;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .fc-button:hover {
            background: rgba(255,255,255,0.14) !important;
        }

        .fc-button-primary:not(:disabled).fc-button-active,
        .fc-button-primary:not(:disabled):active {
            background: rgba(255,255,255,0.18) !important;
        }

        .fc-event {
            border-radius: 9px !important;
            padding: 3px 8px !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            border-width: 0 !important;
        }

        .fc-daygrid-event-dot {
            display: none !important;
        }

        @media (max-width: 1280px) {
            .public-calendar-grid {
                grid-template-columns: 1fr;
            }

            .public-calendar-card,
            .public-tasks-card {
                min-height: auto;
            }
        }

        @media (max-width: 768px) {
            .public-calendar-shell {
                padding: 16px;
            }

            .public-calendar-title {
                min-width: auto;
                width: 100%;
                font-size: 1.6rem;
                padding: 14px 18px;
            }

            .public-calendar-card,
            .public-tasks-card {
                padding: 16px;
                border-radius: 24px;
            }

            .fc-toolbar-title {
                font-size: 1.7rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="public-calendar-shell">
        <div class="public-calendar-title-wrap">
            <div class="public-calendar-title">
                Recruitment Calendar
            </div>
        </div>

        <div class="public-calendar-grid">
            <div class="public-calendar-card">
                <div id="public-calendar"></div>
            </div>

            <div class="public-tasks-card">
                <h3 class="public-tasks-title">Upcoming Tasks</h3>
                <p class="public-tasks-subtitle">From today to the next 30 days.</p>

                @if (count($upcomingTaskGroups))
                    @foreach ($upcomingTaskGroups as $group)
                        <div class="public-task-group">
                            <div class="public-task-date">{{ $group['label'] }}</div>

                            <div class="public-task-list">
                                @foreach ($group['items'] as $item)
                                    <div class="public-task-item">
                                        <span
                                            class="public-task-dot"
                                            style="background-color: {{ $item['backgroundColor'] }};"
                                        ></span>

                                        <div class="public-task-content">
                                            <div class="public-task-item-title">
                                                {{ $item['title'] }}
                                            </div>

                                            @if (! empty($item['job_title']))
                                                <div class="public-task-meta">
                                                    Linked to job: {{ $item['job_title'] }}
                                                </div>
                                            @else
                                                <div class="public-task-meta">
                                                    General Event
                                                </div>
                                            @endif

                                            @if (! empty($item['notes']))
                                                <div class="public-task-notes">
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
                    <div class="public-empty-state">
                        No upcoming tasks in the next 30 days.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const calendarEvents = @json($calendarEvents);

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('public-calendar');

            if (!calendarEl || typeof FullCalendar === 'undefined') {
                return;
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
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
                events: calendarEvents,
            });

            calendar.render();
        });

        setTimeout(() => {
            window.location.reload();
        }, 60000);
    </script>
</body>
</html>0