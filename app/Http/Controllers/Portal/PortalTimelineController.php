<?php

namespace App\Http\Controllers\Portal;

use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PortalTimelineController extends PortalBaseController
{
    public function index(Request $request)
    {
        $shared = $this->sharedPortalData($request);
        $employment = $shared['portalEmployment'];
        $preEmployment = $employment?->preEmployment;

        $statusFilter = trim((string) $request->query('status', ''));
        $monthFilter = trim((string) $request->query('month', ''));
        $yearFilter = trim((string) $request->query('year', ''));

        $recentSalarySlips = collect();
        if ($employment?->id) {
            $recentSalarySlips = SalarySlip::query()
                ->where('employment_id', $employment->id)
                ->latest('salary_year')
                ->latest('salary_month')
                ->latest('id')
                ->limit(50)
                ->get();
        }

        $recentFiles = $this->buildPortalFiles($employment, $preEmployment)->take(50);
        $updates = $this->buildLatestUpdates($employment, $preEmployment, $recentSalarySlips, $recentFiles);

        $statusOptions = $updates->pluck('badge_status')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $yearOptions = $updates->map(function ($item) {
                return $item->event_date ? $item->event_date->format('Y') : null;
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $filteredUpdates = $updates->filter(function ($item) use ($statusFilter, $monthFilter, $yearFilter) {
            if ($statusFilter !== '' && (string) ($item->badge_status ?? '') !== $statusFilter) {
                return false;
            }

            if ($monthFilter !== '') {
                $month = $item->event_date ? $item->event_date->format('m') : null;
                if ($month !== str_pad($monthFilter, 2, '0', STR_PAD_LEFT)) {
                    return false;
                }
            }

            if ($yearFilter !== '') {
                $year = $item->event_date ? $item->event_date->format('Y') : null;
                if ($year !== $yearFilter) {
                    return false;
                }
            }

            return true;
        })->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentPageItems = $filteredUpdates->slice(($page - 1) * $perPage, $perPage)->values();

        $events = new LengthAwarePaginator(
            $currentPageItems,
            $filteredUpdates->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('portal.timeline.index', array_merge($shared, [
            'events' => $events,
            'statusFilter' => $statusFilter,
            'monthFilter' => $monthFilter,
            'yearFilter' => $yearFilter,
            'statusOptions' => $statusOptions,
            'yearOptions' => $yearOptions,
            'monthOptions' => [
                '01' => '01 - January',
                '02' => '02 - February',
                '03' => '03 - March',
                '04' => '04 - April',
                '05' => '05 - May',
                '06' => '06 - June',
                '07' => '07 - July',
                '08' => '08 - August',
                '09' => '09 - September',
                '10' => '10 - October',
                '11' => '11 - November',
                '12' => '12 - December',
            ],
        ]));
    }

    protected function buildPortalFiles($employment, $preEmployment): Collection
    {
        $items = collect();

        foreach (($employment?->files ?? collect()) as $file) {
            $items->push([
                'type' => 'file',
                'title' => $file->title ?? $file->file_name ?? ('Employment File #' . $file->id),
                'description' => $file->category ?? 'Employment File',
                'date' => $file->created_at,
                'badge_status' => 'file',
            ]);
        }

        foreach (($employment?->documents ?? collect()) as $doc) {
            $items->push([
                'type' => 'file',
                'title' => $doc->document_name ?? $doc->title ?? ('Employment Document #' . $doc->id),
                'description' => $doc->document_type ?? 'Employment Document',
                'date' => $doc->created_at,
                'badge_status' => 'document',
            ]);
        }

        foreach (($preEmployment?->files ?? collect()) as $file) {
            $items->push([
                'type' => 'file',
                'title' => $file->title ?? $file->file_name ?? ('Pre-Employment File #' . $file->id),
                'description' => $file->category ?? 'Pre-Employment File',
                'date' => $file->created_at,
                'badge_status' => 'file',
            ]);
        }

        return $items
            ->sortByDesc(fn ($item) => optional($item['date'])->timestamp ?? 0)
            ->values();
    }

    protected function buildLatestUpdates($employment, $preEmployment, Collection $recentSalarySlips, Collection $recentFiles): Collection
    {
        $updates = collect();

        foreach ($recentSalarySlips as $slip) {
            $updates->push((object) [
                'title' => 'Salary Slip ' . sprintf('%02d/%04d', (int) ($slip->salary_month ?? 0), (int) ($slip->salary_year ?? 0)),
                'description' => 'Net Amount: ' . number_format((float) ($slip->net_amount ?? 0), 2) . ' ' . ($slip->currency ?: ''),
                'event_date' => $slip->updated_at ?: $slip->created_at,
                'badge_status' => (string) $slip->status,
            ]);
        }

        if ($employment?->currentRotation) {
            $rotation = $employment->currentRotation;
            $updates->push((object) [
                'title' => 'Current Rotation Updated',
                'description' => trim(implode(' · ', array_filter([
                    $rotation->from_date ? 'From: ' . $rotation->from_date->format('Y-m-d') : null,
                    $rotation->to_date ? 'To: ' . $rotation->to_date->format('Y-m-d') : null,
                    $employment->rotation_status ? 'Status: ' . $employment->rotation_status : null,
                ]))),
                'event_date' => $rotation->updated_at ?: $rotation->created_at,
                'badge_status' => 'rotation',
            ]);
        }

        if ($employment?->travel_status || $employment?->mobilization_date || $employment?->demobilization_date) {
            $updates->push((object) [
                'title' => 'Travel / Mobilization Update',
                'description' => trim(implode(' · ', array_filter([
                    $employment->travel_status ? 'Travel: ' . $employment->travel_status : null,
                    $employment->mobilization_date ? 'Mobilization: ' . $employment->mobilization_date->format('Y-m-d') : null,
                    $employment->demobilization_date ? 'Demobilization: ' . $employment->demobilization_date->format('Y-m-d') : null,
                ]))),
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'travel',
            ]);
        }

        if ($employment?->contract_status) {
            $updates->push((object) [
                'title' => 'Contract Status Updated',
                'description' => 'Contract Status: ' . $employment->contract_status,
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'contract',
            ]);
        }

        if ($employment?->visa_status) {
            $updates->push((object) [
                'title' => 'Visa Status Updated',
                'description' => trim(implode(' · ', array_filter([
                    'Visa Status: ' . $employment->visa_status,
                    $employment->visa_expiry_date ? 'Expiry: ' . $employment->visa_expiry_date->format('Y-m-d') : null,
                ]))),
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'visa',
            ]);
        }

        if ($employment?->medical_status) {
            $updates->push((object) [
                'title' => 'Medical Status Updated',
                'description' => trim(implode(' · ', array_filter([
                    'Medical Status: ' . $employment->medical_status,
                    $employment->medical_expiry_date ? 'Expiry: ' . $employment->medical_expiry_date->format('Y-m-d') : null,
                ]))),
                'event_date' => $employment->updated_at ?: $employment->created_at,
                'badge_status' => 'medical',
            ]);
        }

        foreach ($recentFiles as $file) {
            $updates->push((object) [
                'title' => $file['title'],
                'description' => $file['description'] ?: 'File added',
                'event_date' => $file['date'],
                'badge_status' => $file['badge_status'] ?? 'file',
            ]);
        }

        return $updates
            ->sortByDesc(fn ($item) => optional($item->event_date)->timestamp ?? 0)
            ->values();
    }
}
