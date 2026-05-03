<?php

namespace App\Http\Controllers\Portal\Auth;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PortalPasswordSetupController extends Controller
{

    public function requestForm()
    {
        return view('portal.auth.password-request');
    }

    public function sendRequest(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim((string) $validated['email']));

        $portalAccount = PortalAccount::query()
            ->where('email', $email)
            ->first();

        /*
         * Security note:
         * We intentionally return a generic success message whether the account exists or not.
         */
        if (! $portalAccount) {
            return back()->with('success', 'If this email is linked to a portal account, a password setup link has been sent.');
        }

        $token = \Illuminate\Support\Str::random(64);

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

        $employment = $portalAccount->currentIdentity?->employment
            ?: \App\Models\Employment::query()
                ->where('employee_email', $email)
                ->first();

        if ($employment) {
            \Illuminate\Support\Facades\Mail::to($portalAccount->email)->send(
                new \App\Mail\PortalAccountAccessMail($portalAccount, $employment, $setupUrl, 'reset')
            );
        }

        return back()->with('success', 'If this email is linked to a portal account, a password setup link has been sent.');
    }

    public function show(Request $request, string $token)
    {
        $email = strtolower(trim((string) $request->query('email')));

        if ($email === '') {
            abort(404);
        }

        return view('portal.auth.password-setup', [
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = strtolower(trim((string) $validated['email']));

        $reset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $reset || ! Hash::check($token, $reset->token)) {
            return back()
                ->withErrors([
                    'email' => 'This password setup link is invalid or has expired.',
                ])
                ->withInput($request->only('email'));
        }

        $createdAt = $reset->created_at ? \Carbon\Carbon::parse($reset->created_at) : null;

        if (! $createdAt || $createdAt->lt(now()->subHours(24))) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return back()
                ->withErrors([
                    'email' => 'This password setup link has expired. Please request a new one.',
                ])
                ->withInput($request->only('email'));
        }

        $portalAccount = PortalAccount::query()
            ->where('email', $email)
            ->first();

        if (! $portalAccount) {
            return back()
                ->withErrors([
                    'email' => 'Portal account was not found.',
                ])
                ->withInput($request->only('email'));
        }

        $portalAccount->forceFill([
            'password' => Hash::make($validated['password']),
            'is_active' => true,
            'email_verified_at' => $portalAccount->email_verified_at ?: now(),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $request->session()->regenerate();
        $request->session()->put('portal_account_id', $portalAccount->id);

        $portalAccount->update([
            'last_login_at' => now(),
        ]);

        return redirect()
            ->route('portal.dashboard')
            ->with('success', 'Your portal password has been set successfully.');
    }
}
