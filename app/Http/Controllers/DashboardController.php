<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalSalesValue = SalesOrderItem::select(DB::raw('SUM(quantity * price) as total'))->value('total');
        $totalTransactions = SalesOrder::count();
        $lowStockProductsCount = Product::whereColumn('quantity', '<=', 'reorder_point')->count();

        $salesData = SalesOrder::select(
            DB::raw('DATE(order_date) as date'),
            DB::raw('COUNT(id) as total_orders')
        )
            ->where('order_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $salesChartData = [
            'labels' => $salesData->pluck('date'),
            'datasets' => [
                [
                    'label' => 'Sales Orders per Day',
                    'data' => $salesData->pluck('total_orders'),
                    'borderColor' => '#42A5F5',
                    'tension' => 0.1,
                ],
            ],
        ];

        $topProducts = SalesOrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->with('product:id,name,sku')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_point')
            ->orderBy('quantity', 'asc')
            ->get(['id', 'name', 'sku', 'quantity', 'reorder_point']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalSalesValue' => (float) $totalSalesValue,
                'totalTransactions' => $totalTransactions,
                'lowStockProductsCount' => $lowStockProductsCount,
            ],
            'salesChartData' => $salesChartData,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
