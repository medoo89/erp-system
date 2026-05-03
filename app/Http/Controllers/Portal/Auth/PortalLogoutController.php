<?php

namespace App\Http\Controllers\Portal\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortalLogoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->session()->forget('portal_account_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
