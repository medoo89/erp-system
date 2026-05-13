<?php

namespace App\Http\Controllers;

use App\Models\ClientInvoice;

use Illuminate\Http\Request;

class ClientInvoicePrintController extends Controller

{

    public function show(Request $request, ClientInvoice $clientInvoice)

    {

        $clientInvoice->load([

            'client',

            'project',

            'createdBy',

            'invoiceProfile',

            'lines',

            'lines.employment',

        ]);

        return view('print.client-invoice', [

            'invoice' => $clientInvoice,

        ]);

    }



    public function print(ClientInvoice $clientInvoice)
    {
        return $this->show(request(), $clientInvoice);
    }

}