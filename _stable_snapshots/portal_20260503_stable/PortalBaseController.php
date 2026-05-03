<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use Illuminate\Http\Request;

abstract class PortalBaseController extends Controller
{
    protected function sharedPortalData(Request $request): array
    {
        $portalAccount = $request->attributes->get('portalAccount');

        $portalPreviewReadonly = (bool) $request->session()->get('portal_preview_readonly', false);
        $previewEmploymentId = $request->session()->get('portal_preview_employment_id');

        $portalPreviewBackUrl = $previewEmploymentId
            ? url('/admin/employments/' . $previewEmploymentId . '/portal-preview/exit')
            : url('/admin');

        $currentIdentity = $portalAccount?->currentIdentity;

        /*
         * Admin read-only preview fallback:
         * If the current identity relation is missing or not resolved,
         * use the employment id stored by EmploymentPortalPreviewController.
         */
        $employmentId = $currentIdentity?->employment_id;

        if (! $employmentId && $portalPreviewReadonly && $previewEmploymentId) {
            $employmentId = $previewEmploymentId;
        }

        $employment = null;

        if ($employmentId) {
            $employment = Employment::query()
                ->with([
                    'preEmployment',
                    'currentRotation',
                    'files',
                    'documents',
                    'currentFinanceProfile',
                    'preEmployment.currentFinanceProfile',
                    'preEmployment.files',
                    'preEmployment.uploads',
                    'preEmployment.jobApplication',
                ])
                ->find($employmentId);
        }

        $latestNotifications = $portalAccount?->notifications()
            ->latest()
            ->limit(6)
            ->get() ?? collect();

        $unreadNotificationsCount = $portalAccount?->unreadNotifications()->count() ?? 0;

        return [
            'portalAccount' => $portalAccount,
            'currentIdentity' => $currentIdentity,
            'portalEmployment' => $employment,
            'portalHeaderNotifications' => $latestNotifications,
            'portalUnreadNotificationsCount' => $unreadNotificationsCount,
            'portalPreviewReadonly' => $portalPreviewReadonly,
            'portalPreviewBackUrl' => $portalPreviewBackUrl,
        ];
    }
}
