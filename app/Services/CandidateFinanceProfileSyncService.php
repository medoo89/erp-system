<?php

namespace App\Services;

use App\Models\CandidateFinanceProfile;
use App\Models\CandidateRequest;
use App\Models\Employment;
use App\Models\SalaryTermsHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CandidateFinanceProfileSyncService
{
    public function syncFromAcceptedNegotiation(CandidateRequest $candidateRequest, ?int $createdBy = null): ?CandidateFinanceProfile
    {
        $jobApplication = $candidateRequest->jobApplication()
            ->with('job.project.client', 'preEmployments')
            ->first();

        if (! $jobApplication) {
            return null;
        }

        $rateAmount = $candidateRequest->accepted_salary ?: $candidateRequest->proposed_salary;
        $rateCurrency = $candidateRequest->accepted_currency ?: $candidateRequest->currency;

        if (blank($rateAmount) || blank($rateCurrency)) {
            return null;
        }

        $job = $jobApplication->job;
        $project = $job?->project;
        $client = $project?->client;

        $preEmployment = $jobApplication->preEmployments()
            ->latest('id')
            ->first();

        $employment = null;

        if ($preEmployment) {
            $employment = Employment::query()
                ->where('pre_employment_id', $preEmployment->id)
                ->latest('id')
                ->first();
        }

        return DB::transaction(function () use (
            $candidateRequest,
            $jobApplication,
            $preEmployment,
            $employment,
            $job,
            $project,
            $client,
            $rateAmount,
            $rateCurrency,
            $createdBy
        ) {
            CandidateFinanceProfile::query()
                ->where('job_application_id', $jobApplication->id)
                ->where('is_current', true)
                ->update([
                    'is_current' => false,
                    'effective_to' => now()->toDateString(),
                    'updated_at' => now(),
                ]);

            $financeProfile = CandidateFinanceProfile::create([
                'job_application_id' => $jobApplication->id,
                'pre_employment_id' => $preEmployment?->id,
                'employment_id' => $employment?->id,
                'job_id' => $job?->id,
                'client_id' => $client?->id,
                'project_id' => $project?->id,
                'source_candidate_request_id' => $candidateRequest->id,
                'finance_status' => 'active',
                'salary_basis' => 'daily_rate',
                'agreed_salary_amount' => $rateAmount,
                'agreed_salary_currency' => $rateCurrency,
                'daily_rate' => $rateAmount,
                'monthly_salary' => null,
                'payout_currency' => $rateCurrency,
                'source_type' => 'salary_negotiation',
                'effective_from' => now()->toDateString(),
                'effective_to' => null,
                'is_current' => true,
                'is_hidden_from_non_finance' => true,
                'finance_notes' => 'Created automatically from accepted salary negotiation as daily rate.',
            ]);

            SalaryTermsHistory::create([
                'candidate_finance_profile_id' => $financeProfile->id,
                'job_application_id' => $jobApplication->id,
                'pre_employment_id' => $preEmployment?->id,
                'employment_id' => $employment?->id,
                'job_id' => $job?->id,
                'client_id' => $client?->id,
                'project_id' => $project?->id,
                'source_candidate_request_id' => $candidateRequest->id,
                'created_by' => $createdBy ?? Auth::id(),
                'source_type' => 'salary_negotiation',
                'change_reason' => 'accepted_offer',
                'salary_basis' => 'daily_rate',
                'amount' => $rateAmount,
                'currency' => $rateCurrency,
                'daily_rate' => $rateAmount,
                'monthly_salary' => null,
                'effective_from' => now()->toDateString(),
                'effective_to' => null,
                'notes' => 'Salary terms captured automatically from accepted negotiation as daily rate.',
            ]);

            return $financeProfile;
        });
    }
}