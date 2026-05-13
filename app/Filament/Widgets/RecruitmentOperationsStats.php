<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Employments\EmploymentResource;
use App\Filament\Resources\Jobs\JobResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\PreEmployments\PreEmploymentResource;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RecruitmentOperationsStats extends Widget
{
    protected string $view = 'filament.widgets.recruitment-operations-stats';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    public function getViewData(): array
    {
        $today = Carbon::today();
        $next30 = Carbon::today()->addDays(30);
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $stats = [
            [
                'label' => 'Total Applications',
                'value' => $this->countRows('job_applications'),
                'caption' => 'All received candidates',
                'icon' => 'assignment_ind',
                'tone' => 'blue',
                'url' => $this->safeResourceUrl(JobApplicationResource::class, 'job_applications', 'view'),
            ],
            [
                'label' => 'Applications This Month',
                'value' => $this->countRowsBetween('job_applications', 'created_at', $monthStart, $monthEnd),
                'caption' => 'New applications this month',
                'icon' => 'monitoring',
                'tone' => 'cyan',
                'url' => $this->safeResourceUrl(JobApplicationResource::class, 'job_applications', 'view'),
            ],
            [
                'label' => 'Active Published Jobs',
                'value' => $this->openJobsCount(),
                'caption' => 'Jobs currently visible on the public career portal',
                'icon' => 'campaign',
                'tone' => 'teal',
                'url' => $this->safeResourceUrl(JobResource::class, 'jobs', 'view'),
            ],
            [
                'label' => 'Expiring Jobs',
                'value' => $this->expiringJobsCount($today, $next30),
                'caption' => 'Job openings expiring in 30 days',
                'icon' => 'event_busy',
                'tone' => 'amber',
                'url' => $this->safeResourceUrl(JobResource::class, 'jobs', 'view'),
            ],
            [
                'label' => 'Pre-Employment In Progress',
                'value' => $this->preEmploymentInProgressCount(),
                'caption' => 'Candidates under mobilization preparation',
                'icon' => 'fact_check',
                'tone' => 'violet',
                'url' => $this->safeResourceUrl(PreEmploymentResource::class, 'pre_employments', 'view'),
            ],
            [
                'label' => 'Active Employees',
                'value' => $this->activeEmployeesCount(),
                'caption' => 'Current active employments',
                'icon' => 'engineering',
                'tone' => 'green',
                'url' => $this->safeResourceUrl(EmploymentResource::class, 'employments', 'view'),
            ],
            [
                'label' => 'On Rotation Now',
                'value' => $this->onRotationNowCount($today),
                'caption' => 'Employees currently inside work period',
                'icon' => 'sync_alt',
                'tone' => 'indigo',
                'url' => $this->safeResourceUrl(EmploymentResource::class, 'employments', 'view'),
            ],
            [
                'label' => 'Upcoming Mobilization',
                'value' => $this->upcomingMobilizationCount($today, $next30),
                'caption' => 'Mobilizations in the next 30 days',
                'icon' => 'flight_takeoff',
                'tone' => 'orange',
                'url' => $this->safeResourceUrl(EmploymentResource::class, 'employments', 'view'),
            ],
        ];

        return [
            'stats' => $stats,
            'todayLabel' => now()->format('l, d M Y'),
        ];
    }

    protected function safeResourceUrl(string $resourceClass, string $module, string $action): ?string
    {
        $user = auth()->user();

        if (! ($user?->isSuperAdmin() || $user?->canErp($module, $action))) {
            return null;
        }

        try {
            return $resourceClass::getUrl('index');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function countRows(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    protected function countRowsBetween(string $table, string $column, Carbon $from, Carbon $to): int
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return 0;
        }

        return (int) DB::table($table)
            ->whereBetween($column, [$from->toDateTimeString(), $to->toDateTimeString()])
            ->count();
    }

    protected function openJobsCount(): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        $query = DB::table('jobs');

        if (Schema::hasColumn('jobs', 'is_archived')) {
            $query->where('is_archived', false);
        }

        if (Schema::hasColumn('jobs', 'is_published')) {
            $query->where('is_published', true);
        } elseif (Schema::hasColumn('jobs', 'published')) {
            $query->where('published', true);
        }

        if (Schema::hasColumn('jobs', 'status')) {
            $query->whereNotIn('status', ['closed', 'cancelled', 'archived']);
        }

        return (int) $query->count();
    }

    protected function expiringJobsCount(Carbon $from, Carbon $to): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        $dateColumn = null;

        foreach (['expiry_date', 'closing_date', 'end_date'] as $column) {
            if (Schema::hasColumn('jobs', $column)) {
                $dateColumn = $column;
                break;
            }
        }

        if (! $dateColumn) {
            return 0;
        }

        $query = DB::table('jobs')
            ->whereBetween($dateColumn, [$from->toDateString(), $to->toDateString()]);

        if (Schema::hasColumn('jobs', 'is_archived')) {
            $query->where('is_archived', false);
        }

        return (int) $query->count();
    }

    protected function preEmploymentInProgressCount(): int
    {
        if (! Schema::hasTable('pre_employments')) {
            return 0;
        }

        $query = DB::table('pre_employments');

        if (Schema::hasColumn('pre_employments', 'status')) {
            $query->whereNotIn('status', [
                'converted',
                'converted_to_employment',
                'completed',
                'declined',
                'cancelled',
                'archived',
            ]);
        }

        return (int) $query->count();
    }

    protected function activeEmployeesCount(): int
    {
        if (! Schema::hasTable('employments')) {
            return 0;
        }

        $query = DB::table('employments');

        if (Schema::hasColumn('employments', 'status')) {
            $query->where('status', 'active');
        }

        return (int) $query->count();
    }

    protected function onRotationNowCount(Carbon $today): int
    {
        if (! Schema::hasTable('employment_rotations')) {
            return 0;
        }

        $query = DB::table('employment_rotations');

        if (Schema::hasColumn('employment_rotations', 'status')) {
            $query->whereIn('status', ['active', 'scheduled']);
        }

        if (Schema::hasColumn('employment_rotations', 'from_date') && Schema::hasColumn('employment_rotations', 'to_date')) {
            $query
                ->whereDate('from_date', '<=', $today->toDateString())
                ->whereDate('to_date', '>=', $today->toDateString());
        } elseif (Schema::hasColumn('employment_rotations', 'is_current')) {
            $query->where('is_current', true);
        }

        return (int) $query->count();
    }

    protected function upcomingMobilizationCount(Carbon $from, Carbon $to): int
    {
        if (! Schema::hasTable('employment_rotations') || ! Schema::hasColumn('employment_rotations', 'mobilization_date')) {
            return 0;
        }

        $query = DB::table('employment_rotations')
            ->whereBetween('mobilization_date', [$from->toDateString(), $to->toDateString()]);

        if (Schema::hasColumn('employment_rotations', 'status')) {
            $query->whereNotIn('status', ['completed', 'cancelled', 'paused']);
        }

        return (int) $query->count();
    }
}
