<?php

namespace App\Http\Controllers;

use App\Models\ClientInvoice;

use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class ClientInvoicePdfController extends Controller

{

    public function stream(Request $request, ClientInvoice $clientInvoice)

    {

        $clientInvoice->load([

            'client',

            'project',

            'createdBy',

            'invoiceProfile',

            'lines',

            'lines.employment',

        ]);

        $pdf = Pdf::loadView('pdf.client-invoice', [

            'invoice' => $clientInvoice,

        ])->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . ($clientInvoice->invoice_number ?: $clientInvoice->id) . '.pdf');

    }

    public function download(Request $request, ClientInvoice $clientInvoice)

    {

        $clientInvoice->load([

            'client',

            'project',

            'createdBy',

            'invoiceProfile',

            'lines',

            'lines.employment',

        ]);

        $pdf = Pdf::loadView('pdf.client-invoice', [

            'invoice' => $clientInvoice,

        ])->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . ($clientInvoice->invoice_number ?: $clientInvoice->id) . '.pdf');

    }

}