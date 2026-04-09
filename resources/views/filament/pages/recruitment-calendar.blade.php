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
            background: #f8fafc;
            color: #0f172a;
        }

        .public-calendar-shell {
            min-height: 100vh;
            padding: 28px;
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.08), transparent 28%),
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.06), transparent 22%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .public-calendar-title-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }

        .public-calendar-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: min(100%, 720px);
            padding: 22px 42px;
            border-radius: 28px;
            font-size: 2.8rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.04em;
            color: #0f172a;
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(148, 163, 184, 0.18);
            box-shadow:
                0 16px 36px rgba(15, 23, 42, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .public-calendar-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(400px, 0.95fr);
            gap: 1.5rem;
            align-items: start;
        }

        .public-calendar-card,
        .public-tasks-card {
            border-radius: 32px;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow:
                0 14px 32px rgba(15, 23, 42, 0.06),
                inset 0 1px 0 rgba(255,255,255,0.90);
        }

        .public-calendar-card {
            padding: 28px;
            min-height: 900px;
        }

        .public-tasks-card {
            padding: 24px;
            min-height: 900px;
        }

        .public-tasks-title {
            font-size: 1.7rem;
            font-weight: 900;
            margin: 0 0 0.35rem 0;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .public-tasks-subtitle {
            margin: 0 0 1.2rem 0;
            font-size: 1.02rem;
            color: #64748b;
        }

        .public-task-group {
            margin-bottom: 1.15rem;
            padding-bottom: 1.15rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.16);
        }

        .public-task-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .public-task-date {
            font-size: 1.12rem;
            font-weight: 900;
            margin-bottom: 0.8rem;
            color: #0f172a;
        }

        .public-task-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .public-task-item {
            display: flex;
            gap: 0.85rem;
            align-items: flex-start;
            padding: 1rem 1.05rem;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.04);
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .public-task-dot {
            width: 14px;
            height: 14px;
            min-width: 14px;
            border-radius: 999px;
            margin-top: 0.35rem;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.04);
        }

        .public-task-content {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .public-task-item-title {
            font-size: 1.04rem;
            line-height: 1.45;
            font-weight: 800;
            color: #0f172a;
        }

        .public-task-meta {
            font-size: 0.92rem;
            font-weight: 700;
            color: #475569;
        }

        .public-task-notes {
            font-size: 0.92rem;
            line-height: 1.45;
            color: #64748b;
        }

        .public-empty-state {
            padding: 1.1rem;
            border-radius: 20px;
            text-align: center;
            font-size: 1rem;
            background: rgba(15, 23, 42, 0.04);
            color: #64748b;
            border: 1px solid rgba(148, 163, 184, 0.12);
        }

        .fc {
            color: #0f172a;
            font-family: Inter, Arial, sans-serif;
        }

        .fc-theme-standard td,
        .fc-theme-standard th,
        .fc-scrollgrid,
        .fc-scrollgrid-section > * {
            border-color: rgba(148, 163, 184, 0.22) !important;
        }

        .fc-toolbar-title {
            font-size: 2.8rem !important;
            line-height: 1.1 !important;
            font-weight: 900 !important;
            color: #0f172a !important;
            letter-spacing: -0.03em;
        }

        .fc-col-header-cell-cushion {
            color: #475569 !important;
            text-decoration: none !important;
            font-weight: 800 !important;
            font-size: 1rem !important;
            padding: 14px 0 !important;
        }

        .fc-daygrid-day-number {
            color: #0f172a !important;
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
            background: rgba(15, 23, 42, 0.08) !important;
            color: #0f172a !important;
            box-shadow: none !important;
            font-weight: 800 !important;
            font-size: 0.95rem !important;
        }

        .fc-button:hover {
            background: rgba(15, 23, 42, 0.14) !important;
        }

        .fc-button-primary:not(:disabled).fc-button-active,
        .fc-button-primary:not(:disabled):active {
            background: rgba(15, 23, 42, 0.18) !important;
        }

        .fc-event {
            border-radius: 10px !important;
            padding: 4px 8px !important;
            font-size: 0.86rem !important;
            font-weight: 800 !important;
            border-width: 0 !important;
        }

        .fc-daygrid-event-dot {
            display: none !important;
        }

        @media (max-width: 1400px) {
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
                font-size: 1.8rem;
                padding: 16px 18px;
            }

            .public-calendar-card,
            .public-tasks-card {
                padding: 16px;
                border-radius: 24px;
            }

            .fc-toolbar-title {
                font-size: 1.8rem !important;
            }

            .public-tasks-title {
                font-size: 1.35rem;
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