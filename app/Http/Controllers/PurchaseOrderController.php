<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user'])->latest()->get();

        return Inertia::render('PurchaseOrders/Index', [
            'purchaseOrders' => $purchaseOrders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('PurchaseOrders/Create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku', 'cost']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'order_date' => Carbon::parse($request->order_date)->format('Y-m-d'),
                'status' => 'completed',
                'notes' => $request->notes,
                'po_number' => 'PO-' . date('Ymd') . '-' . mt_rand(1000, 9999),
            ]);

            foreach ($request->items as $item) {
                $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'cost' => $item['cost'],
                ]);

                $product = Product::find($item['product_id']);
                $product->increment('quantity', $item['quantity']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Failed to create purchase order. ' . $e->getMessage());
        }

        return Redirect::route('purchase-orders.index')->with('success', 'Purchase Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);

        return Inertia::render('PurchaseOrders/Show', [
            'purchaseOrder' => $purchaseOrder
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.product');

        return Inertia::render('PurchaseOrders/Edit', [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name', 'sku', 'cost']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:purchase_order_items,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'order_date' => Carbon::parse($validated['order_date'])->format('Y-m-d'),
                'notes' => $request->notes,
            ]);

            $incomingItemIds = collect($validated['items'])->pluck('id')->filter();
            $currentItemIds = $purchaseOrder->items->pluck('id');

            $deletedItemIds = $currentItemIds->diff($incomingItemIds);
            foreach ($deletedItemIds as $deletedId) {
                $itemToDelete = $purchaseOrder->items()->find($deletedId);
                Product::find($itemToDelete->product_id)->decrement('quantity', $itemToDelete->quantity);
                $itemToDelete->delete();
            }

            foreach ($validated['items'] as $itemData) {
                $product = Product::find($itemData['product_id']);

                if (isset($itemData['id'])) {
                    $item = $purchaseOrder->items()->find($itemData['id']);
                    $oldQuantity = $item->quantity;

                    $quantityDifference = $itemData['quantity'] - $oldQuantity;
                    $product->increment('quantity', $quantityDifference);

                    $item->update([
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'cost' => $itemData['cost'],
                    ]);
                } else {
                    $purchaseOrder->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity' => $itemData['quantity'],
                        'cost' => $itemData['cost'],
                    ]);
                    $product->increment('quantity', $itemData['quantity']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Failed to update purchase order. ' . $e->getMessage());
        }

        return Redirect::route('purchase-orders.index')->with('success', 'Purchase Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        try {
            DB::beginTransaction();

            foreach ($purchaseOrder->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->decrement('quantity', $item->quantity);
                }
            }
            $purchaseOrder->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Failed to delete purchase order. ' . $e->getMessage());
        }

        return Redirect::route('purchase-orders.index')->with('success', 'Purchase Order deleted successfully.');
    }
}
