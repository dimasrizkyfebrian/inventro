<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('StockAdjustments/Index', [
            'adjustments' => StockAdjustment::with(['user', 'product'])->latest()->get(),
            'products' => Product::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:addition,subtraction',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            StockAdjustment::create([
                'product_id' => $validated['product_id'],
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'reason' => $validated['reason'],
            ]);

            $product = Product::find($validated['product_id']);
            if ($validated['type'] === 'addition') {
                $product->increment('quantity', $validated['quantity']);
            } else {
                if ($product->quantity < $validated['quantity']) {
                    DB::rollBack();
                    return Redirect::back()->withErrors(['quantity' => 'Adjustment quantity exceeds available stock.']);
                }
                $product->decrement('quantity', $validated['quantity']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Failed to save stock adjustment.');
        }

        return Redirect::route('stock-adjustments.index')->with('success', 'Stock adjustment saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function destroy(string $id)
    {
        //
    }
}
