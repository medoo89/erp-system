<?php

namespace App\Http\Controllers;

use App\Models\ClientInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClientInvoiceTimesheetPrintController extends Controller
{
    public function show(Request $request, ClientInvoice $clientInvoice)
    {
        $clientInvoice->load([
            'client',
            'project',
            'lines.salarySlip.employment',
            'workDays.employment',
        ]);

        $periodStart = $clientInvoice->period_start
            ? Carbon::parse($clientInvoice->period_start)
            : ($clientInvoice->workDays->min('work_date') ? Carbon::parse($clientInvoice->workDays->min('work_date')) : now()->startOfMonth());

        $periodEnd = $clientInvoice->period_end
            ? Carbon::parse($clientInvoice->period_end)
            : ($clientInvoice->workDays->max('work_date') ? Carbon::parse($clientInvoice->workDays->max('work_date')) : now()->endOfMonth());

        $days = collect();
        $cursor = $periodStart->copy();

        while ($cursor->lte($periodEnd)) {
            $days->push($cursor->copy());
            $cursor->addDay();
        }

        $lines = $clientInvoice->lines;

        return view('Print.client-invoice-timesheet', [
            'invoice' => $clientInvoice,
            'days' => $days,
            'lines' => $lines,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
        ]);
    }
}
