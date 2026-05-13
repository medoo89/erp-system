<?php

namespace App\Http\Controllers;

use App\Models\Client;

use App\Models\ClientInvoice;

use App\Models\FinanceExpense;

use App\Models\SalarySlip;

use Illuminate\View\View;

class ClientFinancePagesController extends Controller

{

    public function invoices(Client $client): View

    {

        $items = class_exists(ClientInvoice::class)

            ? ClientInvoice::query()

                ->where('client_id', $client->id)

                ->latest('id')

                ->get()

            : collect();

        return view('admin.clients.finance-list', [

            'client' => $client,

            'type' => 'invoices',

            'title' => 'Client Invoices',

            'items' => $items,

        ]);

    }

    public function salarySlips(Client $client): View

    {

        $items = class_exists(SalarySlip::class)

            ? SalarySlip::query()

                ->where('client_id', $client->id)

                ->latest('id')

                ->get()

            : collect();

        return view('admin.clients.finance-list', [

            'client' => $client,

            'type' => 'salary_slips',

            'title' => 'Client Salary Slips',

            'items' => $items,

        ]);

    }

    public function expenses(Client $client): View

    {

        $items = class_exists(FinanceExpense::class)

            ? FinanceExpense::query()

                ->where('client_id', $client->id)

                ->latest('id')

                ->get()

            : collect();

        return view('admin.clients.finance-list', [

            'client' => $client,

            'type' => 'expenses',

            'title' => 'Client Finance Expenses',

            'items' => $items,

        ]);

    }

}

