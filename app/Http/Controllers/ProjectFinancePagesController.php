<?php

namespace App\Http\Controllers;

use App\Models\ClientInvoice;

use App\Models\FinanceExpense;

use App\Models\Project;

use App\Models\SalarySlip;

use Illuminate\View\View;

class ProjectFinancePagesController extends Controller

{

    public function invoices(Project $project): View

    {

        $items = class_exists(ClientInvoice::class)

            ? ClientInvoice::query()

                ->where('project_id', $project->id)

                ->latest('id')

                ->get()

            : collect();

        return view('admin.projects.finance-list', [

            'project' => $project->load('client'),

            'type' => 'invoices',

            'title' => 'Project Client Invoices',

            'items' => $items,

        ]);

    }

    public function salarySlips(Project $project): View

    {

        $items = class_exists(SalarySlip::class)

            ? SalarySlip::query()

                ->where('project_id', $project->id)

                ->latest('id')

                ->get()

            : collect();

        return view('admin.projects.finance-list', [

            'project' => $project->load('client'),

            'type' => 'salary_slips',

            'title' => 'Project Salary Slips',

            'items' => $items,

        ]);

    }

    public function expenses(Project $project): View

    {

        $items = class_exists(FinanceExpense::class)

            ? FinanceExpense::query()

                ->where('project_id', $project->id)

                ->latest('id')

                ->get()

            : collect();

        return view('admin.projects.finance-list', [

            'project' => $project->load('client'),

            'type' => 'expenses',

            'title' => 'Project Finance Expenses',

            'items' => $items,

        ]);

    }

}

