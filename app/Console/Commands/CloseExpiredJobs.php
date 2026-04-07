<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class CloseExpiredJobs extends Command
{
    protected $signature = 'jobs:close-expired';

    protected $description = 'Automatically close expired job openings';

    public function handle(): int
    {
        $closedCount = Job::query()
            ->where('is_active', true)
            ->where('is_archived', false)
            ->whereNotNull('closing_date')
            ->whereDate('closing_date', '<', today())
            ->update([
                'is_active' => false,
            ]);

        $this->info("Closed {$closedCount} expired job opening(s).");

        return self::SUCCESS;
    }
}