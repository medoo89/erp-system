<?php

namespace App\Http\Controllers\Portal\Auth;

use App\Http\Controllers\Controller;
use App\Models\PortalAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PortalLoginController extends Controller
{
    public function show()
    {
        if (session()->has('portal_account_id')) {
            return redirect()->route('portal.dashboard');
        }

        return view('portal.auth.login');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $portalAccount = PortalAccount::query()
            ->where('email', strtolower(trim($validated['email'])))
            ->first();

        if (! $portalAccount || ! Hash::check($validated['password'], $portalAccount->password)) {
            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.',
                ])
                ->withInput($request->only('email'));
        }

        if (! $portalAccount->is_active) {
            return back()
                ->withErrors([
                    'email' => 'This portal account is inactive.',
                ])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $request->session()->put('portal_account_id', $portalAccount->id);

        $portalAccount->update([
            'last_login_at' => now(),
        ]);

        return redirect()->route('portal.dashboard');
    }
}
