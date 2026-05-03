<?php

namespace App\Http\Controllers\Portal;

use App\Models\SalarySlipAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortalSalarySlipAttachmentController extends PortalBaseController
{
    public function open(Request $request, SalarySlipAttachment $attachment)
    {
        $shared = $this->sharedPortalData($request);

        $portalAccount = $shared['portalAccount'] ?? null;
        $identity = $portalAccount?->currentIdentity;

        if (! $portalAccount || ! $identity) {
            return redirect()->route('portal.login');
        }

        $attachment->loadMissing('salarySlip');

        if ((int) $identity->employment_id !== (int) $attachment->salarySlip?->employment_id) {
            abort(403);
        }

        $path = (string) $attachment->file_path;

        if (Storage::disk('public')->exists($path)) {
            return response()->file(Storage::disk('public')->path($path));
        }

        abort(404, 'Attachment file not found.');
    }

    public function download(Request $request, SalarySlipAttachment $attachment)
    {
        $shared = $this->sharedPortalData($request);

        $portalAccount = $shared['portalAccount'] ?? null;
        $identity = $portalAccount?->currentIdentity;

        if (! $portalAccount || ! $identity) {
            return redirect()->route('portal.login');
        }

        $attachment->loadMissing('salarySlip');

        if ((int) $identity->employment_id !== (int) $attachment->salarySlip?->employment_id) {
            abort(403);
        }

        $path = (string) $attachment->file_path;

        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'Attachment file not found.');
        }

        return Storage::disk('public')->download(
            $path,
            $attachment->original_name ?: $attachment->title ?: basename($path)
        );
    }
}
