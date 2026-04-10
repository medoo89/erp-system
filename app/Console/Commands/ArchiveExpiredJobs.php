<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class ArchiveExpiredJobs extends Command
{
    protected $signature = 'jobs:archive-expired';

    protected $description = 'Archive expired job openings automatically';

    public function handle(): int
    {
        $jobs = Job::query()
            ->where('is_archived', false)
            ->whereNotNull('closing_date')
            ->whereDate('closing_date', '<', today())
            ->get();

        $count = 0;

        foreach ($jobs as $job) {
            $job->update([
                'is_active' => false,
                'is_archived' => true,
                'archive_reason' => 'expired',
                'archived_at' => now(),
            ]);

            $count++;
        }

        $this->info("Archived {$count} expired job opening(s).");

        return self::SUCCESS;
    }
}