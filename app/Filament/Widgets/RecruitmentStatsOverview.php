<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use App\Models\JobApplication;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecruitmentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $totalJobs = Job::count();

        $activeJobs = Job::query()
            ->where('is_archived', false)
            ->where('is_active', true)
            ->count();

        $archivedJobs = Job::query()
            ->where('is_archived', true)
            ->count();

        $totalApplications = JobApplication::count();

        $activeApplications = JobApplication::query()
            ->where('is_archived', false)
            ->count();

        $archivedApplications = JobApplication::query()
            ->where('is_archived', true)
            ->count();

        $screeningApplications = JobApplication::query()
            ->where('status', 'screening')
            ->where('is_archived', false)
            ->count();

        $underReviewApplications = JobApplication::query()
            ->where('status', 'under_review')
            ->where('is_archived', false)
            ->count();

        $clientSubmittedApplications = JobApplication::query()
            ->where('status', 'client_submitted')
            ->where('is_archived', false)
            ->count();

        $qualifiedApplications = JobApplication::query()
            ->where('status', 'qualified')
            ->where('is_archived', false)
            ->count();

        $hiredApplications = JobApplication::query()
            ->where('status', 'hired')
            ->where('is_archived', false)
            ->count();

        $declinedApplications = JobApplication::query()
            ->where('status', 'declined')
            ->count();

        return [
            Stat::make('Total Job Openings', number_format($totalJobs))
                ->description('All job opening records')
                ->icon('heroicon-o-briefcase')
                ->color('primary')
                ->url('/admin/job-openings'),

            Stat::make('Active Job Openings', number_format($activeJobs))
                ->description('Currently active and open')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->url('/admin/job-openings'),

            Stat::make('Archived Job Openings', number_format($archivedJobs))
                ->description('Moved to archive')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->url('/admin/archived-job-openings'),

            Stat::make('Total Applications', number_format($totalApplications))
                ->description('All submitted applications')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url('/admin/job-applications'),

            Stat::make('Active Applications', number_format($activeApplications))
                ->description('Not archived')
                ->icon('heroicon-o-folder-open')
                ->color('primary')
                ->url('/admin/job-applications'),

            Stat::make('Archived Applications', number_format($archivedApplications))
                ->description('Declined or archived')
                ->icon('heroicon-o-archive-box')
                ->color('gray')
                ->url('/admin/archived-job-applications'),

            Stat::make('Screening', number_format($screeningApplications))
                ->description('Currently in screening')
                ->icon('heroicon-o-funnel')
                ->color('warning')
                ->url('/admin/job-applications?tableFilters[status][value]=screening'),

            Stat::make('Under Review', number_format($underReviewApplications))
                ->description('Being reviewed internally')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url('/admin/job-applications?tableFilters[status][value]=under_review'),

            Stat::make('Client Submitted', number_format($clientSubmittedApplications))
                ->description('Submitted to client')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->url('/admin/job-applications?tableFilters[status][value]=client_submitted'),

            Stat::make('Qualified', number_format($qualifiedApplications))
                ->description('Qualified candidates')
                ->icon('heroicon-o-check-circle')
                ->color('gray')
                ->url('/admin/job-applications?tableFilters[status][value]=qualified'),

            Stat::make('Hired', number_format($hiredApplications))
                ->description('Successfully hired')
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->url('/admin/job-applications?tableFilters[status][value]=hired'),

            Stat::make('Declined', number_format($declinedApplications))
                ->description('Declined applications')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->url('/admin/archived-job-applications'),
        ];
    }
}