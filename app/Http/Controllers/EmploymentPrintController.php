<?php

namespace App\Http\Controllers;

use App\Models\Employment;
use Illuminate\Http\Request;

class EmploymentPrintController extends Controller
{
    public function profile(Employment $employment)
    {
        $employment->load([
            'job.project.client',
            'assignedHrUser',
            'files',
            'rotations',
            'currentRotation',
            'documents',
        ]);

        $documents = $employment->files
            ->sortByDesc('created_at')
            ->map(function ($file) {
                return [
                    'title' => $file->title ?: '-',
                    'category' => $file->category ? ucfirst(str_replace('_', ' ', $file->category)) : '-',
                    'version' => 'V' . ($file->version_no ?: 1),
                    'current' => $file->is_current ? 'Current' : 'Old',
                    'submitted_by' => $file->uploaded_by_type === 'candidate' ? 'Candidate' : 'Admin',
                    'document_date' => $file->document_date?->format('M j, Y') ?: '-',
                    'expiry_date' => $file->expiry_date?->format('M j, Y') ?: '-',
                    'document_status' => $file->document_status ? ucfirst(str_replace('_', ' ', $file->document_status)) : '-',
                ];
            });

        $rotation = $employment->currentRotation
            ?: $employment->rotations->sortByDesc(fn ($item) => optional($item->from_date)?->timestamp ?? 0)->first();

        return view('print.employment-profile', [
            'employment' => $employment,
            'rotation' => $rotation,
            'documents' => $documents,
        ]);
    }

    public function rotationHistory(Employment $employment)
    {
        $employment->load([
            'rotations' => fn ($q) => $q->orderByDesc('from_date'),
        ]);

        return view('print.rotation-history', [
            'employment' => $employment,
            'rotations' => $employment->rotations,
        ]);
    }
}