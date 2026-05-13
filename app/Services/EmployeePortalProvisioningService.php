<?php

namespace App\Services;

use App\Models\Employment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\PortalIdentity;
use App\Models\PortalAccount;
use App\Mail\PortalAccountAccessMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class EmployeePortalProvisioningService
{
    public function syncForEmployment(Employment $employment, bool $sendPasswordSetupEmail = false): ?User
    {
        if (blank($employment->employee_email)) {
            return null;
        }

        $email = strtolower(trim((string) $employment->employee_email));

        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! $user) {
            $user = User::query()->create([
                'employment_id' => $employment->id,
                'name' => $employment->employee_name ?: $employment->employee_code ?: 'Employee',
                'email' => $email,
                'password' => Hash::make(Str::random(48)),
                'user_type' => User::TYPE_EMPLOYEE_PORTAL,
                'is_admin' => false,
                'portal_status' => User::PORTAL_PENDING_PASSWORD_SETUP,
                'portal_access_enabled' => true,
            ]);
        } else {
            $user->forceFill([
                'employment_id' => $employment->id,
                'name' => $employment->employee_name ?: $user->name,
                'user_type' => $user->is_admin ? User::TYPE_ADMIN : User::TYPE_EMPLOYEE_PORTAL,
                'portal_status' => $user->portal_status ?: User::PORTAL_PENDING_PASSWORD_SETUP,
                'portal_access_enabled' => true,
            ])->save();
        }

        if ($employment->shouldBlockPortalAccess()) {
            return $this->disableForEmployment($employment, $employment->resolvedPortalDisabledReason());
        }

        $user->forceFill([
            'portal_access_enabled' => true,
            'portal_status' => $user->portal_status === User::PORTAL_ACTIVE
                ? User::PORTAL_ACTIVE
                : User::PORTAL_PENDING_PASSWORD_SETUP,
            'portal_disabled_reason' => null,
            'portal_disabled_at' => null,
        ])->save();

        if ($sendPasswordSetupEmail && ! $user->is_admin) {
            $this->sendPasswordSetupEmail($user);
        }

        return $user;
    }

    public function sendPasswordSetupEmail(User $user): bool
    {
        if ($user->is_admin) {
            return false;
        }

        $status = Password::sendResetLink([
            'email' => $user->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            $user->forceFill([
                'password_setup_sent_at' => now(),
                'portal_status' => $user->portal_status ?: User::PORTAL_PENDING_PASSWORD_SETUP,
            ])->save();

            return true;
        }

        return false;
    }

    public function disableForEmployment(Employment $employment, ?string $reason = null): ?User
    {
        $user = $employment->portalUser;

        if (! $user && filled($employment->employee_email)) {
            $user = User::query()
                ->where('email', strtolower(trim((string) $employment->employee_email)))
                ->first();
        }

        if (! $user || $user->is_admin) {
            return $user;
        }

        $blockedReasons = [
            'military_zone',
            'restricted_area',
            'blocked',
        ];

        $status = in_array($reason, $blockedReasons, true)
            ? User::PORTAL_BLOCKED
            : User::PORTAL_DISABLED;

        if ($reason === 'archived') {
            $status = User::PORTAL_ARCHIVED;
        }

        $user->forceFill([
            'portal_access_enabled' => false,
            'portal_status' => $status,
            'portal_disabled_reason' => $reason ?: 'employment_inactive',
            'portal_disabled_at' => now(),
        ])->save();

        return $user;
    }

    public function resetPasswordForEmployment(Employment $employment): bool
    {
        $user = $employment->portalUser;

        if (! $user) {
            $user = $this->syncForEmployment($employment, false);
        }

        if (! $user || $user->is_admin) {
            return false;
        }

        return $this->sendPasswordSetupEmail($user);
    }

    public function createOrUpdatePortalAccountForEmployment(Employment $employment, ?string $plainPassword = null): ?PortalAccount
    {
        if (blank($employment->employee_email)) {
            return null;
        }

        $email = strtolower(trim((string) $employment->employee_email));

        $portalAccount = PortalAccount::query()->firstOrNew([
            'email' => $email,
        ]);

        $portalAccount->full_name = $employment->employee_name
            ?: $portalAccount->full_name
            ?: $employment->employee_code
            ?: 'Employee';

        if ($plainPassword !== null) {
            $portalAccount->password = Hash::make($plainPassword);
        } elseif (blank($portalAccount->password)) {
            $portalAccount->password = Hash::make(Str::random(40));
        }

        if (Schema::hasColumn('portal_accounts', 'phone')) {
            $portalAccount->phone = $employment->employee_phone ?: $portalAccount->phone;
        }

        if (Schema::hasColumn('portal_accounts', 'is_active')) {
            $portalAccount->is_active = true;
        }

        if (Schema::hasColumn('portal_accounts', 'preferred_language') && blank($portalAccount->preferred_language)) {
            $portalAccount->preferred_language = 'en';
        }

        $portalAccount->save();

        $this->syncPortalIdentityForEmployment($portalAccount, $employment);

        return $portalAccount;
    }

    public function syncPortalIdentityForEmployment(PortalAccount $portalAccount, Employment $employment): void
    {
        if (! Schema::hasTable('portal_identities')) {
            return;
        }

        PortalIdentity::query()
            ->where('portal_account_id', $portalAccount->id)
            ->update([
                'is_current' => false,
                'updated_at' => now(),
            ]);

        $identity = PortalIdentity::query()
            ->where('portal_account_id', $portalAccount->id)
            ->where('employment_id', $employment->id)
            ->latest('id')
            ->first();

        if (! $identity) {
            $identity = new PortalIdentity();
            $identity->portal_account_id = $portalAccount->id;
            $identity->employment_id = $employment->id;
        }

        $identity->pre_employment_id = $employment->pre_employment_id;
        $identity->current_stage = defined(PortalIdentity::class . '::STAGE_EMPLOYMENT')
            ? PortalIdentity::STAGE_EMPLOYMENT
            : 'employment';
        $identity->is_current = true;

        if (Schema::hasColumn('portal_identities', 'linked_at') && blank($identity->linked_at)) {
            $identity->linked_at = now();
        }

        if (Schema::hasColumn('portal_identities', 'unlinked_at')) {
            $identity->unlinked_at = null;
        }

        $identity->save();

        PortalIdentity::query()
            ->where('portal_account_id', $portalAccount->id)
            ->where('employment_id', $employment->id)
            ->where('id', '!=', $identity->id)
            ->delete();
    }

    public function sendPortalAccountAccessEmail(Employment $employment, string $mailType = 'setup'): bool
    {
        if (blank($employment->employee_email)) {
            return false;
        }

        $portalAccount = $this->createOrUpdatePortalAccountForEmployment($employment, null);

        if (! $portalAccount) {
            return false;
        }

        $email = strtolower(trim((string) $portalAccount->email));
        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $setupUrl = route('portal.password.setup', [
            'token' => $token,
            'email' => $email,
        ]);

        Mail::to($portalAccount->email)->send(
            new PortalAccountAccessMail($portalAccount, $employment, $setupUrl, $mailType)
        );

        return true;
    }

    public function enablePortalAccountForEmployment(Employment $employment): ?PortalAccount
    {
        $portalAccount = $this->createOrUpdatePortalAccountForEmployment($employment, null);

        if ($portalAccount && Schema::hasColumn('portal_accounts', 'is_active')) {
            $portalAccount->forceFill(['is_active' => true])->save();
        }

        return $portalAccount;
    }

    public function disablePortalAccountForEmployment(Employment $employment): ?PortalAccount
    {
        if (blank($employment->employee_email)) {
            return null;
        }

        $portalAccount = PortalAccount::query()
            ->where('email', strtolower(trim((string) $employment->employee_email)))
            ->first();

        if ($portalAccount && Schema::hasColumn('portal_accounts', 'is_active')) {
            $portalAccount->forceFill(['is_active' => false])->save();
        }

        return $portalAccount;
    }

}
