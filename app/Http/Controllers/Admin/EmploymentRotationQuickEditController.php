<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Services\PortalNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmploymentRotationQuickEditController extends Controller
{
    public function edit(Employment $employment, int $rotation)
    {
        $employment->loadMissing('rotations');

        $rotationRecord = $employment->rotations()
            ->whereKey($rotation)
            ->firstOrFail();

        return view('admin.employments.rotations.quick-edit', [
            'employment' => $employment,
            'rotation' => $rotationRecord,
        ]);
    }

    public function update(Request $request, Employment $employment, int $rotation)
    {
        $employment->loadMissing('rotations');

        $rotationRecord = $employment->rotations()
            ->whereKey($rotation)
            ->firstOrFail();

        $validated = $request->validate([
            'rotation_label' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'rotation_pattern' => ['nullable', 'string', 'max:255'],
            'travel_status' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'mobilization_date' => ['nullable', 'date'],
            'demobilization_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'travel_request_file' => ['nullable', 'file', 'max:20480'],
            'ticket_file' => ['nullable', 'file', 'max:20480'],
        ]);

        unset($validated['travel_request_file'], $validated['ticket_file']);

        if ($request->hasFile('travel_request_file')) {
            $validated['travel_request_file_path'] = $request->file('travel_request_file')
                ->store('employment-rotations/' . $employment->id . '/travel-requests', 'public');
        }

        if ($request->hasFile('ticket_file')) {
            $validated['ticket_file_path'] = $request->file('ticket_file')
                ->store('employment-rotations/' . $employment->id . '/tickets', 'public');
        }

        $rotationRecord->forceFill($validated)->save();

        try {
            $label = $rotationRecord->rotation_label ?: ('Rotation #' . $rotationRecord->id);

            app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                employment: $employment,
                category: 'rotation',
                title: 'Rotation Updated',
                message: 'Your rotation has been updated: ' . $label,
                portalPath: '/portal/timeline',
                related: $rotationRecord,
                sendEmail: true,
            );

            if (filled($rotationRecord->travel_request_file_path)) {
                app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                    employment: $employment,
                    category: 'travel',
                    title: 'Travel Request Updated',
                    message: 'A travel request file is available for: ' . $label,
                    portalPath: '/portal/travel-tickets',
                    related: $rotationRecord,
                    sendEmail: true,
                );
            }

            if (filled($rotationRecord->ticket_file_path)) {
                app(PortalNotificationService::class)->notifyGenericEmploymentUpdate(
                    employment: $employment,
                    category: 'ticket',
                    title: 'Ticket Updated',
                    message: 'A ticket file is available for: ' . $label,
                    portalPath: '/portal/travel-tickets',
                    related: $rotationRecord,
                    sendEmail: true,
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->to('/admin/employments/' . $employment->id)
            ->with('success', 'Rotation updated successfully.');
    }

    public function openFile(Employment $employment, int $rotation, string $type)
    {
        $rotationRecord = $employment->rotations()
            ->whereKey($rotation)
            ->firstOrFail();

        $path = match ($type) {
            'ticket' => $rotationRecord->ticket_file_path,
            'travel-request' => $rotationRecord->travel_request_file_path,
            default => null,
        };

        abort_if(blank($path), 404);

        $path = ltrim((string) $path, '/');

        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path));
    }
}
