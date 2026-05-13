<?php

namespace App\Providers;

use App\Observers\AuditModelObserver;
use App\Models\CalendarEvent;
use App\Models\Project;
use App\Models\Client;
use App\Models\BankProfile;
use App\Models\TreasuryOperation;
use App\Models\TreasuryTransaction;
use App\Models\TreasuryAccount;
use App\Models\ClientInvoice;
use App\Models\FinanceExpense;
use App\Models\SalarySlip;
use App\Models\JobApplication;
use App\Models\Job;
use App\Models\PreEmployment;
use App\Models\EmploymentFile;
use App\Models\EmploymentRotation;
use App\Models\Employment;
use App\Models\User;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        JobApplication::created(function (JobApplication $application): void {
            $email = $application->email
                ?? $application->candidate_email
                ?? null;

            if (blank($email)) {
                return;
            }

            try {
                $application->loadMissing('job');

                $hiddenKeys = [
                    'id',
                    'job_id',
                    'created_by',
                    'updated_by',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'cv_file',
                    'cv_path',
                    'file_path',
                    'attachment',
                    'attachment_path',
                    'public_token',
                ];

                $answers = collect($application->getAttributes())
                    ->reject(fn ($value, $key) => in_array($key, $hiddenKeys, true))
                    ->reject(fn ($value) => blank($value))
                    ->mapWithKeys(function ($value, $key) {
                        $label = str($key)->replace('_', ' ')->title()->toString();

                        if (is_string($value)) {
                            $decoded = json_decode($value, true);

                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $value = collect($decoded)
                                    ->map(function ($item) {
                                        if (is_array($item)) {
                                            return implode(' / ', array_filter($item));
                                        }

                                        return $item;
                                    })
                                    ->filter()
                                    ->implode(', ');
                            }
                        }

                        return [$label => $value];
                    })
                    ->toArray();

                Mail::to($email)->send(new JobApplicationReceivedMail($application, $answers));
            } catch (\Throwable $e) {
                Log::error('Job application confirmation email failed', [
                    'job_application_id' => $application->id ?? null,
                    'email' => $email,
                    'message' => $e->getMessage(),
                ]);
            }
        });


        foreach ([
            User::class,
            Employment::class,
            EmploymentRotation::class,
            EmploymentFile::class,
            PreEmployment::class,
            Job::class,
            JobApplication::class,
            SalarySlip::class,
            FinanceExpense::class,
            ClientInvoice::class,
            TreasuryAccount::class,
            TreasuryTransaction::class,
            TreasuryOperation::class,
            BankProfile::class,
            Client::class,
            Project::class,
            CalendarEvent::class,
        ] as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(AuditModelObserver::class);
            }
        }

//
    }
}
