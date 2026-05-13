<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employment;

class EmploymentPrintController extends Controller
{
    public function profile(Employment $employment)
    {
        abort_unless(auth()->user()?->canErp('employments', 'print_profile') || auth()->user()?->isSuperAdmin(), 403);

        $employment->loadMissing([
            'job.project.client',
            'currentFinanceProfile',
            'files',
            'rotations',
            'salarySlips',
            'financeExpenses',
            'portalUser',
        ]);

        return view('Print.employment-profile', [
            'employment' => $employment,
        ]);
    }

    public function rotationHistory(Employment $employment)
    {
        abort_unless(auth()->user()?->canErp('employments', 'rotation_print') || auth()->user()?->isSuperAdmin(), 403);

        $employment->loadMissing([
            'rotations.financeExpenses',
            'job.project.client',
        ]);

        return view('Print.employment-rotation-history', [
            'employment' => $employment,
            'rotations' => $employment->rotations()->latest('from_date')->get(),
        ]);
    }
}
