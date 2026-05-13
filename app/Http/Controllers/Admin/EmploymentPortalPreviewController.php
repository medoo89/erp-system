<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Models\PortalAccount;
use App\Models\PortalIdentity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EmploymentPortalPreviewController extends Controller
{
    public function show(Employment $employment): RedirectResponse
    {
        $employment->loadMissing([
            'preEmployment',
            'currentFinanceProfile',
            'currentRotation',
        ]);

        $email = strtolower(trim((string) $employment->employee_email));

        if ($email === '') {
            return redirect()
                ->back()
                ->with('error', 'Employee email is missing. Please add an employee email before opening portal preview.');
        }

        $portalAccount = PortalAccount::query()->firstOrNew([
            'email' => $email,
        ]);

        $portalAccount->full_name = $employment->employee_name
            ?: $portalAccount->full_name
            ?: $employment->employee_code
            ?: 'Employee';

        if (blank($portalAccount->password)) {
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

        $this->syncCurrentPortalIdentity($portalAccount, $employment);

        session()->put('portal_account_id', $portalAccount->id);
        session()->put('portal_preview_employment_id', $employment->id);
        session()->put('portal_preview_started_at', now()->toDateTimeString());
        session()->put('portal_preview_readonly', true);

        return redirect()->route('portal.dashboard');
    }


    public function exit(Employment $employment): RedirectResponse
    {
        session()->forget([
            'portal_account_id',
            'portal_preview_employment_id',
            'portal_preview_started_at',
            'portal_preview_readonly',
        ]);

        return redirect('/admin/employments/' . $employment->id);
    }

    protected function syncCurrentPortalIdentity(PortalAccount $portalAccount, Employment $employment): void
    {
        if (! Schema::hasTable('portal_identities')) {
            return;
        }

        DB::transaction(function () use ($portalAccount, $employment): void {
            PortalIdentity::query()
                ->where('portal_account_id', $portalAccount->id)
                ->update([
                    'is_current' => false,
                    'updated_at' => now(),
                ]);

            $identity = PortalIdentity::query()
                ->where('portal_account_id', $portalAccount->id)
                ->where('employment_id', $employment->id)
                ->orderByDesc('id')
                ->first();

            if (! $identity) {
                $identity = new PortalIdentity();
                $identity->portal_account_id = $portalAccount->id;
                $identity->employment_id = $employment->id;
            }

            $identity->pre_employment_id = $employment->pre_employment_id;
            $identity->current_stage = 'employment';
            $identity->is_current = true;

            if (Schema::hasColumn('portal_identities', 'linked_at') && ! $identity->linked_at) {
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
        });
    }
}
