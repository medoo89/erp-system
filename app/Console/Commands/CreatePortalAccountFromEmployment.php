<?php

namespace App\Console\Commands;

use App\Models\Employment;
use App\Models\PortalAccount;
use App\Models\PortalIdentity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreatePortalAccountFromEmployment extends Command
{
    protected $signature = 'portal:create-from-employment {employment_id} {password}';
    protected $description = 'Create or update a portal account from an employment record';

    public function handle(): int
    {
        $employmentId = (int) $this->argument('employment_id');
        $password = (string) $this->argument('password');

        $employment = Employment::query()->find($employmentId);

        if (! $employment) {
            $this->error('Employment not found.');
            return self::FAILURE;
        }

        $email = $employment->email
            ?? $employment->employee_email
            ?? $employment->personal_email
            ?? $employment->work_email
            ?? null;

        if (! $email) {
            $this->error('Employment does not have an email in: email / employee_email / personal_email / work_email');
            return self::FAILURE;
        }

        $fullName = $employment->employee_name
            ?? $employment->full_name
            ?? $employment->name
            ?? ('Employment #' . $employment->id);

        $phone = $employment->phone_number
            ?? $employment->employee_phone
            ?? $employment->phone
            ?? $employment->mobile
            ?? null;

        $portalAccount = PortalAccount::query()->firstOrNew([
            'email' => strtolower(trim($email)),
        ]);

        $portalAccount->full_name = $fullName;
        $portalAccount->password = Hash::make($password);
        $portalAccount->phone = $phone ?: $portalAccount->phone;
        $portalAccount->is_active = true;
        $portalAccount->save();

        PortalIdentity::query()
            ->where('portal_account_id', $portalAccount->id)
            ->where('is_current', true)
            ->update([
                'is_current' => false,
                'unlinked_at' => now(),
            ]);

        PortalIdentity::query()->create([
            'portal_account_id' => $portalAccount->id,
            'employment_id' => $employment->id,
            'current_stage' => PortalIdentity::STAGE_EMPLOYMENT,
            'is_current' => true,
            'linked_at' => now(),
        ]);

        $this->info('Portal account ready.');
        $this->line('Employment ID: ' . $employment->id);
        $this->line('Full Name: ' . $portalAccount->full_name);
        $this->line('Email: ' . $portalAccount->email);
        $this->line('Portal URL: ' . url('/portal/login'));

        return self::SUCCESS;
    }
}
