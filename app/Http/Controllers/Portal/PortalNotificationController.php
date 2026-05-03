<?php

namespace App\Http\Controllers\Portal;

use App\Models\PortalNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortalNotificationController extends PortalBaseController
{
    public function index(Request $request)
    {
        $shared = $this->sharedPortalData($request);
        $portalAccount = $shared['portalAccount'];

        $notifications = $portalAccount->notifications()
            ->latest()
            ->paginate(20);

        return view('portal.notifications.index', array_merge($shared, [
            'notifications' => $notifications,
        ]));
    }

    public function open(Request $request, PortalNotification $notification): RedirectResponse
    {
        $shared = $this->sharedPortalData($request);
        $portalAccount = $shared['portalAccount'];

        abort_unless($portalAccount && (int) $notification->portal_account_id === (int) $portalAccount->id, 403);

        if (! $notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        $target = $notification->action_url ?: route('portal.notifications.index');

        return redirect()->to($target);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $shared = $this->sharedPortalData($request);
        $portalAccount = $shared['portalAccount'];

        $portalAccount->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back();
    }

    public function clearAll(Request $request): RedirectResponse
    {
        $shared = $this->sharedPortalData($request);
        $portalAccount = $shared['portalAccount'];

        $portalAccount->notifications()->delete();

        return back();
    }
}
