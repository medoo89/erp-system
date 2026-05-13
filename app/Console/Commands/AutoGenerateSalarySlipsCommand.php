<?php

namespace App\Console\Commands;

use App\Services\SalarySlipGenerationService;
use Illuminate\Console\Command;

class AutoGenerateSalarySlipsCommand extends Command
{
    protected $signature = 'salary-slips:auto-generate {--replace-existing=0}';
    protected $description = 'Auto generate draft salary slips for the previous month across all projects';

    public function handle(): int
    {
        $results = app(SalarySlipGenerationService::class)
            ->generatePreviousMonthForAllProjects((bool) $this->option('replace-existing'));

        foreach ($results as $result) {
            $message = $result['project'] . ' => ' . $result['status'] . ' (' . $result['count'] . ')';
            if (! empty($result['message'])) {
                $message .= ' | ' . $result['message'];
            }
            $this->line($message);
        }

        $this->info('Auto-generate salary slips process completed.');
        return self::SUCCESS;
    }
}
