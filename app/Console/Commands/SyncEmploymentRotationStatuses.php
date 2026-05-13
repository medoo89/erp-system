<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncEmploymentRotationStatuses extends Command
{
    protected $signature = 'rotations:sync-statuses {--dry-run : Show changes without saving}';
    protected $description = 'Automatically sync employment rotation and travel statuses based on dates.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! Schema::hasTable('employment_rotations')) {
            $this->error('employment_rotations table not found.');
            return self::FAILURE;
        }

        $today = Carbon::today();

        $updatedRotationStatuses = 0;
        $updatedTravelStatuses = 0;

        $rotations = DB::table('employment_rotations')->orderBy('id')->get();

        foreach ($rotations as $rotation) {
            $updates = [];

            $status = strtolower((string) ($rotation->status ?? ''));
            $travelStatus = strtolower((string) ($rotation->travel_status ?? ''));

            $fromDate = filled($rotation->from_date ?? null)
                ? Carbon::parse($rotation->from_date)->startOfDay()
                : null;

            $toDate = filled($rotation->to_date ?? null)
                ? Carbon::parse($rotation->to_date)->startOfDay()
                : null;

            $mobilizationDate = filled($rotation->mobilization_date ?? null)
                ? Carbon::parse($rotation->mobilization_date)->startOfDay()
                : null;

            $demobilizationDate = filled($rotation->demobilization_date ?? null)
                ? Carbon::parse($rotation->demobilization_date)->startOfDay()
                : null;

            /*
             |--------------------------------------------------------------------------
             | Rotation Status Auto Logic
             |--------------------------------------------------------------------------
             | scheduled -> active when real work period starts.
             | active -> completed when real work period ends.
             | paused/cancelled are manual and must not be changed automatically.
             */

            if (! in_array($status, ['paused', 'cancelled', 'completed'], true)) {
                if ($status === 'scheduled' && $fromDate && $today->gte($fromDate)) {
                    $updates['status'] = 'active';
                    $updatedRotationStatuses++;
                }
            }

            $effectiveStatus = $updates['status'] ?? $status;

            if (! in_array($effectiveStatus, ['paused', 'cancelled', 'completed'], true)) {
                if ($effectiveStatus === 'active' && $toDate && $today->gt($toDate)) {
                    $updates['status'] = 'completed';
                    $updatedRotationStatuses++;
                }
            }

            /*
             |--------------------------------------------------------------------------
             | Travel Status Auto Logic
             |--------------------------------------------------------------------------
             | ticket_booked -> completed after demobilization date if available.
             | fallback: ticket_booked -> completed after to_date if demobilization missing.
             | cancelled remains manual and must not change.
             */

            if (! in_array($travelStatus, ['cancelled', 'completed'], true)) {
                if ($travelStatus === 'ticket_booked') {
                    $completionDate = $demobilizationDate ?: $toDate;

                    if ($completionDate && $today->gt($completionDate)) {
                        $updates['travel_status'] = 'completed';
                        $updatedTravelStatuses++;
                    }
                }
            }

            if (! empty($updates)) {
                $updates['updated_at'] = now();

                $this->line(
                    'Rotation #' . $rotation->id .
                    ' | status: ' . ($rotation->status ?? '-') . ' => ' . ($updates['status'] ?? $rotation->status ?? '-') .
                    ' | travel: ' . ($rotation->travel_status ?? '-') . ' => ' . ($updates['travel_status'] ?? $rotation->travel_status ?? '-')
                );

                if (! $dryRun) {
                    DB::table('employment_rotations')
                        ->where('id', $rotation->id)
                        ->update($updates);
                }
            }
        }

        $this->info('Rotation status updates: ' . $updatedRotationStatuses);
        $this->info('Travel status updates: ' . $updatedTravelStatuses);

        if ($dryRun) {
            $this->warn('Dry run only. No database changes were saved.');
        }

        return self::SUCCESS;
    }
}
