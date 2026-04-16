<?php

namespace App\Filament\Widgets;

use App\Models\Employment;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\PreEmployment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecruitmentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalJobs = Job::count();

        $activeJobs = Job::query()
            ->where('is_archived', false)
            ->where('is_active', true)
            ->count();

        $totalApplications = JobApplication::count();

        $activeEmployees = class_exists(Employment::class)
            ? Employment::query()->where('status', 'active')->count()
            : 0;

        $onRotation = class_exists(Employment::class)
            ? Employment::query()->where('rotation_status', 'on_rotation')->count()
            : 0;

        $upcomingMobilizations = class_exists(Employment::class)
            ? Employment::query()
                ->whereDate('mobilization_date', '>=', now()->toDateString())
                ->whereDate('mobilization_date', '<=', now()->addDays(14)->toDateString())
                ->count()
            : 0;

        $preEmploymentInProgress = class_exists(PreEmployment::class)
            ? PreEmployment::query()
                ->whereIn('status', ['approved', 'in_progress', 'processing', 'initiated'])
                ->count()
            : 0;

        return [
            Stat::make('All Job Openings', number_format($totalJobs))
                ->description('All job opening records')
                ->icon('heroicon-o-briefcase')
                ->color('primary')
                ->url('/admin/job-openings'),

            Stat::make('Active Job Openings', number_format($activeJobs))
                ->description('Currently open and active')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->url('/admin/job-openings'),

            Stat::make('Total Applications', number_format($totalApplications))
                ->description('All submitted applications')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url('/admin/job-applications'),

            Stat::make('Active Employees', number_format($activeEmployees))
                ->description('Currently active employments')
                ->icon('heroicon-o-users')
                ->color('success')
                ->url('/admin/employments'),

            Stat::make('On Rotation', number_format($onRotation))
                ->description('Employees currently on rotation')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->url('/admin/employments'),

            Stat::make('Upcoming Mobilizations', number_format($upcomingMobilizations))
                ->description('Scheduled within next 14 days')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->url('/admin/employments'),

            Stat::make('Pre-Employment In Progress', number_format($preEmploymentInProgress))
                ->description('Approved and still under processing')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('info')
                ->url('/admin/pre-employments'),
        ];
    }
}