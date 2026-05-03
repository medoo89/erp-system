<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortalTravelTicketController extends PortalBaseController
{
    public function index(Request $request)
    {
        $shared = $this->sharedPortalData($request);

        if (blank($shared['portalAccount'] ?? null)) {
            return redirect()->route('portal.login');
        }

        if (blank($shared['portalEmployment'] ?? null)) {
            abort(403, 'No employment is linked to this portal account.');
        }

        $employment = $shared['portalEmployment'];

        $employment->loadMissing([
            'rotations',
        ]);

        $items = collect($employment->rotations ?? [])
            ->sortByDesc(fn ($rotation) => optional($rotation->from_date)->timestamp ?? optional($rotation->created_at)->timestamp ?? 0)
            ->map(function ($rotation) {
                return [
                    'id' => $rotation->id,
                    'label' => $rotation->rotation_label ?: ('Rotation #' . $rotation->id),
                    'status' => $rotation->status,
                    'travel_status' => $rotation->travel_status,
                    'rotation_pattern' => $rotation->rotation_pattern,
                    'from_date' => $rotation->from_date,
                    'to_date' => $rotation->to_date,
                    'mobilization_date' => $rotation->mobilization_date,
                    'demobilization_date' => $rotation->demobilization_date,
                    'travel_request_file_path' => $rotation->travel_request_file_path,
                    'ticket_file_path' => $rotation->ticket_file_path,
                    'travel_request_url' => filled($rotation->travel_request_file_path)
                        ? route('portal.travel-tickets.open', ['rotation' => $rotation->id, 'type' => 'travel-request'])
                        : null,
                    'ticket_url' => filled($rotation->ticket_file_path)
                        ? route('portal.travel-tickets.open', ['rotation' => $rotation->id, 'type' => 'ticket'])
                        : null,
                    'notes' => $rotation->notes,
                    'created_at' => $rotation->created_at,
                    'updated_at' => $rotation->updated_at,
                ];
            })
            ->values();

        return view('portal.travel-tickets.index', array_merge($shared, [
            'travelTickets' => $items,
        ]));
    }

    public function open(Request $request, int $rotation, string $type)
    {
        $shared = $this->sharedPortalData($request);

        if (blank($shared['portalAccount'] ?? null)) {
            return redirect()->route('portal.login');
        }

        if (blank($shared['portalEmployment'] ?? null)) {
            abort(403, 'No employment is linked to this portal account.');
        }

        $employment = $shared['portalEmployment'];
        $employment->loadMissing('rotations');

        $record = collect($employment->rotations ?? [])->firstWhere('id', $rotation);

        abort_if(! $record, 404);

        $path = match ($type) {
            'travel-request' => $record->travel_request_file_path,
            'ticket' => $record->ticket_file_path,
            default => null,
        };

        abort_if(blank($path), 404);

        $absolute = $this->resolveStoragePath($path);

        abort_if(! $absolute, 404, 'File not found.');

        return response()->file($absolute);
    }

    public function download(Request $request, int $rotation, string $type)
    {
        $shared = $this->sharedPortalData($request);

        if (blank($shared['portalAccount'] ?? null)) {
            abort(403, 'Portal login required.');
        }

        if (blank($shared['portalEmployment'] ?? null)) {
            abort(403, 'No employment is linked to this portal account.');
        }

        $employment = $shared['portalEmployment'];
        $employment->loadMissing('rotations');

        $record = collect($employment->rotations ?? [])->firstWhere('id', $rotation);

        abort_if(! $record, 404);

        $path = match ($type) {
            'travel-request' => $record->travel_request_file_path,
            'ticket' => $record->ticket_file_path,
            default => null,
        };

        abort_if(blank($path), 404);

        $absolute = $this->resolveStoragePath($path);

        abort_if(! $absolute, 404, 'File not found.');

        $name = $type === 'ticket'
            ? 'ticket-rotation-' . $record->id . '.' . pathinfo($absolute, PATHINFO_EXTENSION)
            : 'travel-request-rotation-' . $record->id . '.' . pathinfo($absolute, PATHINFO_EXTENSION);

        return response()->download($absolute, $name);
    }

    protected function resolveStoragePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = ltrim((string) $path, '/');

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        if (is_file(storage_path('app/public/' . $path))) {
            return storage_path('app/public/' . $path);
        }

        if (is_file(public_path('storage/' . $path))) {
            return public_path('storage/' . $path);
        }

        if (is_file(public_path($path))) {
            return public_path($path);
        }

        return null;
    }
}
