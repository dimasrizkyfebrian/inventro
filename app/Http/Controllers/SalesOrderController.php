<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Carbon\Carbon;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('SalesOrders/Index', [
            'salesOrders' => SalesOrder::with('user')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('SalesOrders/Create', [
            'products' => Product::where('quantity', '>', 0)
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'price', 'quantity']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $productId = $request->items[$index]['product_id'];
                    $product = Product::find($productId);
                    if ($value > $product->quantity) {
                        $fail("The quantity for {$product->name} exceeds available stock ({$product->quantity}).");
                    }
                },
            ],
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $salesOrder = SalesOrder::create([
                'user_id' => Auth::id(),
                'order_date' => Carbon::parse($request->order_date)->format('Y-m-d'),
                'status' => 'completed',
                'notes' => $request->notes,
                'so_number' => 'SO-' . date('Ymd') . '-' . mt_rand(1000, 9999),
            ]);

            foreach ($request->items as $item) {
                $salesOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::find($item['product_id']);
                $product->decrement('quantity', $item['quantity']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Failed to create sales order. ' . $e->getMessage());
        }

        return Redirect::route('sales-orders.index')->with('success', 'Sales Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['user', 'items.product']);

        return Inertia::render('SalesOrders/Show', [
            'salesOrder' => $salesOrder
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesOrder $salesOrder)
    {
        try {
            DB::beginTransaction();

            foreach ($salesOrder->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('quantity', $item->quantity);
                }
            }

            $salesOrder->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Failed to delete sales order. ' . $e->getMessage());
        }

        return Redirect::route('sales-orders.index')->with('success', 'Sales Order deleted successfully.');
    }
}
