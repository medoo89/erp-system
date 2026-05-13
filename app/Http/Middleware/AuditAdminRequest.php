<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditAdminRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        /*
         |--------------------------------------------------------------------------
         | Audit note
         |--------------------------------------------------------------------------
         | We intentionally do NOT log normal page views.
         | Audit Logs should track real actions only:
         | create, update, delete, enable, disable, send, print, approve, upload,
         | request, login/logout, and sensitive workflow changes.
         */

        return $next($request);
    }
}
