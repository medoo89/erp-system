<?php

namespace App\Http\Controllers;

use App\Models\ClientInvoice;
use App\Models\ClientInvoiceDocument;
use Illuminate\Http\Request;

class ClientInvoiceDocumentPrintController extends Controller
{
    public function show(Request $request, ClientInvoice $clientInvoice, string $documentType)
    {
        abort_unless(in_array($documentType, [
            ClientInvoiceDocument::TYPE_FOREIGN,
            ClientInvoiceDocument::TYPE_LOCAL,
        ], true), 404);

        if (method_exists($clientInvoice, 'generateTimesheetFromSalarySlips') && $clientInvoice->workDays()->count() === 0) {
            $clientInvoice->generateTimesheetFromSalarySlips(true);
            $clientInvoice->refresh();
        }

        if (method_exists($clientInvoice, 'syncSplitDocuments')) {
            $clientInvoice->syncSplitDocuments();
            $clientInvoice->refresh();
        }

        $document = $clientInvoice->documents()
            ->where('document_type', $documentType)
            ->first();

        abort_unless($document, 404);

        $clientInvoice->load([
            'client',
            'project',
            'projectContract',
            'lines.salarySlip.days',
            'lines.salarySlip.employment',
            'workDays.employment',
            'documents',
        ]);

        return view('Print.client-invoice-split-document', [
            'invoice' => $clientInvoice,
            'document' => $document,
            'documentType' => $documentType,
        ]);
    }
}
