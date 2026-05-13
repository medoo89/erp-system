<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditWriteRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if (! Schema::hasTable('audit_logs')) {
                return $response;
            }

            if (! Auth::check()) {
                return $response;
            }

            if ($request->isMethod('GET') || $request->isMethod('HEAD') || $request->isMethod('OPTIONS')) {
                return $response;
            }

            $path = trim($request->path(), '/');

            if (
                str_starts_with($path, '_debugbar')
                || str_starts_with($path, 'sanctum')
                || str_starts_with($path, 'broadcasting')
                || str_contains($path, 'audit-logs')
            ) {
                return $response;
            }

            $module = $this->detectModule($request);
            $action = $this->detectAction($request);

            AuditLogService::log(
                action: $action,
                module: $module,
                subject: null,
                description: 'ERP write/action request: ' . $request->method() . ' /' . $path,
                oldValues: [],
                newValues: [],
                meta: [
                    'source' => 'write_request_middleware',
                    'path' => $path,
                    'request_method' => $request->method(),
                    'livewire_component' => data_get($request->all(), 'components.0.snapshot'),
                    'payload_keys' => array_keys($request->except([
                        'password',
                        'current_password',
                        'new_password',
                        'password_confirmation',
                        'temporary_password',
                        '_token',
                    ])),
                    'status_code' => $response->getStatusCode(),
                ],
                severity: $this->severityFor($action),
                status: $response->getStatusCode() >= 400 ? 'failed' : 'success',
                request: $request,
            );
        } catch (Throwable $e) {
            report($e);
        }

        return $response;
    }

    protected function detectModule(Request $request): string
    {
        $path = strtolower(trim($request->path(), '/'));
        $payload = strtolower(json_encode($request->except(['password', '_token']), JSON_UNESCAPED_UNICODE) ?: '');

        $map = [
            'page-rules' => 'page_rules',
            'erp-access-control' => 'page_rules',
            'employment-rotations' => 'employment_rotations',
            'employments' => 'employments',
            'pre-employments' => 'pre_employments',
            'job-applications' => 'job_applications',
            'job-openings' => 'jobs',
            'salary-slips' => 'salary_slips',
            'finance-expenses' => 'finance_expenses',
            'client-invoices' => 'client_invoices',
            'treasury-transactions' => 'treasury_transactions',
            'treasury-operations' => 'treasury_operations',
            'treasury-accounts' => 'treasury_accounts',
            'treasury' => 'treasury',
            'bank-profiles' => 'bank_profiles',
            'clients' => 'clients',
            'projects' => 'projects',
            'recruitment-calendar' => 'recruitment_calendar',
            'travel' => 'travel_tickets',
            'tickets' => 'travel_tickets',
            'portal' => 'employee_portal',
            'livewire' => 'livewire_action',
        ];

        foreach ($map as $needle => $module) {
            if (str_contains($path, $needle) || str_contains($payload, $needle)) {
                return $module;
            }
        }

        return 'erp_action';
    }

    protected function detectAction(Request $request): string
    {
        $path = strtolower(trim($request->path(), '/'));
        $payload = strtolower(json_encode($request->except(['password', '_token']), JSON_UNESCAPED_UNICODE) ?: '');

        $text = $path . ' ' . $payload;

        return match (true) {
            $request->isMethod('DELETE') || str_contains($text, 'delete') => 'delete',
            str_contains($text, 'disable') => 'disable',
            str_contains($text, 'enable') => 'enable',
            str_contains($text, 'approve') => 'approve',
            str_contains($text, 'reject') => 'reject',
            str_contains($text, 'decline') => 'decline',
            str_contains($text, 'archive') => 'archive',
            str_contains($text, 'send') || str_contains($text, 'email') => 'send',
            str_contains($text, 'print') => 'print',
            str_contains($text, 'upload') || str_contains($text, 'file') => 'upload',
            str_contains($text, 'request') => 'request',
            str_contains($text, 'salary') => 'salary_action',
            str_contains($text, 'invoice') => 'invoice_action',
            str_contains($text, 'rotation') => 'rotation_action',
            str_contains($text, 'ticket') => 'ticket_action',
            str_contains($text, 'mobilization') => 'mobilization_action',
            $request->isMethod('POST') => 'submit',
            $request->isMethod('PUT') || $request->isMethod('PATCH') => 'update',
            default => 'write_action',
        };
    }

    protected function severityFor(string $action): string
    {
        return match ($action) {
            'delete', 'disable', 'reject', 'decline', 'archive' => 'danger',
            'update', 'approve', 'salary_action', 'invoice_action', 'rotation_action', 'ticket_action', 'mobilization_action' => 'warning',
            'submit', 'enable', 'upload', 'request', 'send', 'print' => 'success',
            default => 'info',
        };
    }
}
