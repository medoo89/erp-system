<?php

namespace App\Services;

use App\Models\Employment;

class EmploymentSalarySyncService
{
    public function syncEmploymentFromSource(Employment $employment): bool
    {
        $employment->loadMissing([
            'preEmployment.jobApplication.candidateRequests',
        ]);

        $jobApplication = $employment->preEmployment?->jobApplication;

        if (! $jobApplication) {
            return false;
        }

        $acceptedNegotiation = $jobApplication->candidateRequests()
            ->where('type', 'salary_negotiation')
            ->whereIn('request_status', ['accepted', 'approved'])
            ->where(function ($query) {
                $query->whereNotNull('accepted_salary')
                    ->orWhereNotNull('proposed_salary');
            })
            ->latest('responded_at')
            ->latest('id')
            ->first();

        if (! $acceptedNegotiation) {
            return false;
        }

        $rateAmount = $acceptedNegotiation->accepted_salary ?: $acceptedNegotiation->proposed_salary;
        $rateCurrency = $acceptedNegotiation->accepted_currency ?: $acceptedNegotiation->currency;

        if (blank($rateAmount) || blank($rateCurrency)) {
            return false;
        }

        $employment->update([
            'salary_basis' => 'daily_rate',
            'daily_rate' => $rateAmount,
            'monthly_salary' => null,
            'salary_currency' => $rateCurrency,
            'source_candidate_request_id' => $acceptedNegotiation->id,
            'salary_notes' => 'Backfilled automatically from accepted salary negotiation as daily rate.',
        ]);

        return true;
    }

    public function syncAllMissingEmployments(): array
    {
        $updated = 0;
        $skipped = 0;

        Employment::query()
            ->with(['preEmployment.jobApplication.candidateRequests'])
            ->chunkById(100, function ($employments) use (&$updated, &$skipped) {
                foreach ($employments as $employment) {
                    if ($employment->hasSalaryConfigured()) {
                        $skipped++;
                        continue;
                    }

                    $synced = $this->syncEmploymentFromSource($employment);

                    if ($synced) {
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }
            });

        return [
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }
}