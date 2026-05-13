<?php

namespace App\Http\Middleware;

use App\Models\PortalAccount;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        $portalAccountId = $request->session()->get('portal_account_id');

        if (! $portalAccountId) {
            return redirect()->route('portal.login');
        }

        $portalAccount = PortalAccount::query()
            ->with(['currentIdentity', 'notifications'])
            ->find($portalAccountId);

        if (! $portalAccount || ! $portalAccount->is_active) {
            $request->session()->forget('portal_account_id');

            return redirect()
                ->route('portal.login')
                ->withErrors([
                    'email' => 'Your portal session is invalid or inactive.',
                ]);
        }

        $request->attributes->set('portalAccount', $portalAccount);
        view()->share('portalAccount', $portalAccount);

        return $next($request);
    }
}
