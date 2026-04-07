<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobApplicationTemplate;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecruitmentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalJobs = Job::count();

        $publishedJobs = Job::where('status', 'published')->count();

        $expiredJobs = Job::whereNotNull('closing_date')
            ->where('closing_date', '<', now())
            ->count();

        $totalApplications = JobApplication::count();

        $newApplications = JobApplication::where('status', 'new')->count();

        $approvedApplications = JobApplication::where('status', 'approved')->count();

        $templates = JobApplicationTemplate::count();

        return [
            Stat::make('Total Job Openings', $totalJobs)
                ->description('All job records in system')
                ->icon('heroicon-o-briefcase')
                ->color('primary')
                ->url('/admin/jobs'),

            Stat::make('Published Jobs', $publishedJobs)
                ->description('Currently visible jobs')
                ->icon('heroicon-o-megaphone')
                ->color('success')
                ->url('/admin/jobs'),

            Stat::make('Expired Jobs', $expiredJobs)
                ->description('Jobs past closing date')
                ->icon('heroicon-o-clock')
                ->color('danger')
                ->url('/admin/jobs'),

            Stat::make('Total Applications', $totalApplications)
                ->description('All submitted applications')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url('/admin/job-applications'),

            Stat::make('New Applications', $newApplications)
                ->description('Waiting for review')
                ->icon('heroicon-o-inbox')
                ->color('warning')
                ->url('/admin/job-applications?tableFilters[status][value]=new'),

            Stat::make('Approved Applications', $approvedApplications)
                ->description('Applications marked approved')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->url('/admin/job-applications?tableFilters[status][value]=approved'),

            Stat::make('Templates', $templates)
                ->description('Application form templates')
                ->icon('heroicon-o-squares-2x2')
                ->color('gray')
                ->url('/admin/job-application-templates'),
        ];
    }
}