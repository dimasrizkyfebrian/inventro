<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $salesOrders = SalesOrder::with(['items.product', 'user'])
            ->whereBetween('order_date', [$startDate, $endDate])
            ->get();

        $data = [
            'salesOrders' => $salesOrders,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSales' => $salesOrders->flatMap->items->sum(function ($item) {
                return $item->quantity * $item->price;
            }),
        ];

        $pdf = Pdf::loadView('reports.sales', $data);

        return $pdf->stream('sales-report-' . $startDate . '-to-' . $endDate . '.pdf');
    }
}
